<?php

namespace App\Controller;

use App\Entity\Autorisation;
use App\Form\AutorisationType;
use App\Form\AutorisationEditType;
use App\Entity\Personnel;
use App\Entity\Utilisateurs;
use App\Entity\HistoDemandes;
use App\Repository\AutorisationRepository;
use App\Repository\HistoDemandesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\InternetTest;


class AutorisationController extends AbstractController
{

     /** 
     *
     * @Security("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_RH') or is_granted('ROLE_SG') or is_granted('ROLE_DIR')  or is_granted('ROLE_DIR_ADJ')  ")
     */
    #[Route('/autorisation', name: 'app_autorisation_index', methods: ['GET','POST'])]
    public function index( secure $security ): Response
    {
        //$validateur = $security->getUser()->getPersonnel() ;
        //$codes = $security->getUser()->getCodes();
        // $validateur_roles = $validateur->getIdUser()->getRoles() ;
        // $validateur_codes = $validateur->getIdUser()->getCodes() ;
        $em = $this->getDoctrine()->getManager();
        $validateur_codes = $security->getUser()->getCodes();
        $validateur_roles = $security->getUser()->getRoles() ;
        $autorisations=$em->getRepository(Autorisation::class)->searchDemandesByService($validateur_codes,$validateur_roles);

        if( in_array("ROLE_DIR",$validateur_roles) ){
            $autorisations=$em->getRepository(Autorisation::class)->findby(array('statut' => -1 ,'niveau'=>'ROLE_DIR'));
        }


        return $this->render('autorisation/table-datatable-autorisation.html.twig', [
            'autorisations' => $autorisations,
        ]);
    } 

    #[Route('/autorisation_bloque_{id}', name: 'app_autorisation_bloque', methods: ['GET','POST'])]
    public function bloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Autorisation::class)->bloque_autorisation($id);
        return new JsonResponse('1');

    }

    #[Route('/autorisation_debloque_{id}', name: 'app_autorisation_debloque', methods: ['GET','POST'])]
    public function debloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Autorisation::class)->debloque_autorisation($id);
        return new JsonResponse('0');
    }

    /**
     *
     * @Security("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_RH') or is_granted('ROLE_SG') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')")
     */
    #[Route('/autorisationVAL_{id}', name: 'autorisationAL', methods: ['GET','POST'])]
    public function validation(Request $request  ,secure $security , HistoDemandesRepository $HistoDemandesRepository , Autorisation $autorisation, AutorisationRepository $autorisationRepository , $id , MailerInterface $mailer , InternetTest $int): Response
    {
        $searchParam = $request->get('searchParam');
        $em = $this->getDoctrine()->getManager();

        if( in_array("ROLE_RH",$security->getUser()->getRoles()) ){

            if($searchParam['statut']=="1"){
                $autorisation->setStatut("1");
            }else{
                $autorisation->setStatut("2");
                $autorisation->setMotifRefu($searchParam['motifRefu']);
            }
           

            $subject = "Demande d'Autorisation";
            $html = $this->renderView('autorisation/email-autorisation.html.twig',[
                'autorisation'  => $autorisation]); 
            $email = (new TemplatedEmail())
            ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
            ->to($autorisation->getPersonnel()->getIdUser()->getEmail())
            ->subject($subject)
            ->html($html)
            ;
            try {
                if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
            } catch (TransportExceptionInterface $e) {
            } 

        }else{
            if($searchParam['statut']=="1"){
                $autorisation->setNiveau("ROLE_RH");
                $autorisation->setStatut("-1");


                $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                $subject = "Traitement d'Autorisation";
                $html = $this->renderView('autorisation/email-autorisation-notif.html.twig',['autorisation'  => $autorisation ,'destinataire' => $destinataire]); 
                $email = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
                ->to($destinataire['email'])
                ->subject($subject)
                ->html($html)
                ;
                try {
                    if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
                } catch (TransportExceptionInterface $e) {
                } 


            }else{
                $autorisation->setStatut("2");
            } 
           
        }

        $HistoDemandes = new HistoDemandes();
        $HistoDemandes->setTypeDemande('autorisation') ; 
        $HistoDemandes->setDateValidation(new \DateTime()) ;
        $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
        $HistoDemandes->setStatut($autorisation->getStatut()) ;
        $HistoDemandes->setNiveau($autorisation->getNiveau()) ;
        $HistoDemandes->setDemandeur($autorisation->getPersonnel()) ;
        $HistoDemandes->setDateEnvoie($autorisation->getDateEnvoie()) ;
        $date_reprise = $autorisation->getDateRentree();
        $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
        $HistoDemandes->setIdDemande($autorisation->getId()) ;

        $HistoDemandesRepository->save($HistoDemandes, true);
        $autorisationRepository->save($autorisation, true);

        return new RedirectResponse($this->generateUrl('app_autorisation_index'));
    }



     /**
     *
     * @Security("is_granted('ROLE_FONC')")
     */
    #[Route('/autorisation_new', name: 'app_autorisation_new', methods: ['GET', 'POST'])]
    public function new(Request $request,secure $security, AutorisationRepository $autorisationRepository ,HistoDemandesRepository $HistoDemandesRepository, MailerInterface $mailer , InternetTest $int): Response
    {
        $em = $this->getDoctrine()->getManager();
        $destinataire = null ;
        $autorisations=$em->getRepository(Autorisation::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
        $autorisation = new Autorisation();
        $form = $this->createForm(AutorisationType::class, $autorisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            
            $demandeur = $security->getUser()->getPersonnel();
            $autorisation->setPersonnel($demandeur);
            $autorisation->setBloque(0);
            $autorisation->setDateEnvoie(new \DateTime());
     
            $autorisation->setStatut("-1");

            if( in_array("ROLE_FONC",$security->getUser()->getRoles()) && !in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles())  ){
                $autorisation->setNiveau("ROLE_CHEF_SERV");

                $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleServ('SER_'.$autorisation->getPersonnel()->getServiceAffectationId()->getId(),'ROLE_CHEF_SERV') ;

                if($destinataire==null || $destinataire=='' )
                {
                    $role_superieur = $security->getUser()->getPersonnel()->getServiceAffectationId()->getRoleSuperieur() ;
                    $autorisation->setNiveau($role_superieur);
            

                if($role_superieur=='ROLE_SG'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_SG') ;
                                      }

                if($role_superieur=='ROLE_DIR_ADJ'){
                     $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_'.$autorisation->getPersonnel()->getServiceAffectationId()->getCodes(),'ROLE_DIR_ADJ') ;
                                     }    
                                     
                if($role_superieur=='ROLE_DIR'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDir() ;
                                                        }    
                }    


                $subject = "Traitement d'Autorisation";
                $html = $this->renderView('autorisation/email-autorisation-notif.html.twig',['autorisation'  => $autorisation ,'destinataire' => $destinataire]); 
                $email = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
                ->to($destinataire['email'])
                ->subject($subject)
                ->html($html)
                ;
                try {
                    if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
                } catch (TransportExceptionInterface $e) {
                } 


            }
            if( in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles()) ){
                $role_superieur = $security->getUser()->getPersonnel()->getServiceAffectationId()->getRoleSuperieur() ;
                $autorisation->setNiveau($role_superieur);

                       if($role_superieur=='ROLE_SG'){
                             $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_SG') ;
                                               }

                       if($role_superieur=='ROLE_DIR_ADJ'){
                              $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_'.$autorisation->getPersonnel()->getServiceAffectationId()->getCodes(),'ROLE_DIR_ADJ') ;
                                              }       
                                              
                            if($role_superieur=='ROLE_DIR'){

                                $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDir() ;
                                     }   

                $subject = "Traitement d'Autorisation";
                $html = $this->renderView('autorisation/email-autorisation-notif.html.twig',['autorisation'  => $autorisation ,'destinataire' => $destinataire]); 
                $email = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
                ->to($destinataire['email'])
                ->subject($subject)
                ->html($html)
                ;
                try {
                    if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
                } catch (TransportExceptionInterface $e) {
                } 



            }
            if( in_array("ROLE_DIR",$security->getUser()->getRoles()) || in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles()) || in_array("ROLE_SG",$security->getUser()->getRoles()) ){
                $autorisation->setNiveau("ROLE_RH");


                $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                $subject = "Traitement d'Autorisation";
                $html = $this->renderView('autorisation/email-autorisation-notif.html.twig',['autorisation'  => $autorisation ,'destinataire' => $destinataire]); 
                $email = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
                ->to($destinataire['email'])
                ->subject($subject)
                ->html($html)
                ;
                try {
                    if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
                } catch (TransportExceptionInterface $e) {
                } 
            }

           
            
            $autorisationRepository->save($autorisation, true);

            $HistoDemandes = new HistoDemandes();
            $HistoDemandes->setTypeDemande('autorisation') ; 

            $HistoDemandes->setStatut($autorisation->getStatut()) ;
            $HistoDemandes->setNiveau($autorisation->getNiveau()) ;
            $HistoDemandes->setDemandeur($autorisation->getPersonnel()) ;
            $HistoDemandes->setDateEnvoie($autorisation->getDateEnvoie()) ;
            $date_reprise = $autorisation->getDateRentree();
            $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
            $HistoDemandes->setIdDemande($autorisation->getId()) ;

            $HistoDemandesRepository->save($HistoDemandes, true);
            
            $autorisations=$em->getRepository(Autorisation::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            return $this->renderForm('autorisation/new-autorisation.html.twig', [
                'autorisation' => $autorisation,
                'autorisations' => $autorisations,
                'form' => $form,
            ]);
        }

        return $this->renderForm('autorisation/new-autorisation.html.twig', [
            'autorisation' => $autorisation,
            'autorisations' => $autorisations,
            'form' => $form,
        ]);
    }

   
    /**
     *
     * @Security("is_granted('ROLE_FONC')")
     */
    #[Route('/autorisation_{id}_edit', name: 'app_autorisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Autorisation $autorisation, AutorisationRepository $autorisationRepository , secure $security): Response
    {

       if($autorisation->getStatut()=="-1" && $autorisation->getPersonnel()->getId() == $security->getUser()->getPersonnel()->getId() ){
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AutorisationEditType::class, $autorisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$autorisation->isBloque()){
                $autorisationRepository->save($autorisation, true);
                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

            }else{
                $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            }
           
        }
        $autorisations=$em->getRepository(Autorisation::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
        return $this->renderForm('autorisation/edit-autorisation.html.twig', [
            'autorisation' => $autorisation,
            'autorisations' => $autorisations,
            'id' => $autorisation->getId(), 
            'form' => $form,
        ]);
       }else{
        return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
       }
       
    }


    /**
     *
     * @Security("is_granted('ROLE_FONC')")
     */
    #[Route('/autorisation_{id}_{_token}', name: 'app_autorisation_delete', methods: ['POST','GET'])]
    public function delete(Request $request, Autorisation $autorisation, AutorisationRepository $autorisationRepository , $_token): Response
    {  
       
        if ($this->isCsrfTokenValid('delete'.$autorisation->getId(), $_token)) {

            $em = $this->getDoctrine()->getManager();
            $histo_auto = $em->getRepository(HistoDemandes::class)->findBy(['id_demande' => strval($autorisation->getId()),"type_demande"=>"autorisation"]);
            foreach ($histo_auto as $auto) {$em->remove($auto);}
            $em->flush();
            $autorisationRepository->remove($autorisation, true);
        }

        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
        return $this->redirectToRoute('app_autorisation_new', [], Response::HTTP_SEE_OTHER);
    }

     /**
     *
     * @Security("is_granted('ROLE_FONC')")
     */
  #[Route('/autorisationPdf_{id}', name: 'autorisationPdf', methods: ['GET', 'POST'])]
    public function autorisation_pdf(Pdf $knpSnappyPdf , Request $request  , Autorisation $autorisation , KernelInterface $appKernel)
    {
        $filename = 'Autorisation N.'.$autorisation->GetId().' '. $autorisation->getPersonnel()->GetNom() ." ".$autorisation->getPersonnel()->GetPrenom().'.pdf';

        $html = $this->renderView('document/autorisation.html.twig', [
            'autorisation' => $autorisation,
        ]);

        $dir = $this->getParameter('webroot_doc'). $autorisation->getPersonnel()->GetNom().'_'.$autorisation->getPersonnel()->GetPrenom().'/Autorisation/' ;
          if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
          }

          if (!file_exists($dir.$filename)) {
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
          }else{
            unlink($dir.$filename);
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
          }
    
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            $filename ,
        );
    }



}
