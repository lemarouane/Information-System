<?php

namespace App\Controller;

use App\Entity\Conge; 
use App\Entity\SoldeConge; 
use App\Entity\Utilisateurs;
use App\Form\CongeType;
use App\Form\CongeEditType;
use App\Repository\CongeRepository;
use App\Repository\SoldeCongeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Personnel;
use App\Entity\HistoDemandes;
use App\Repository\HistoDemandesRepository;
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



class CongeController extends AbstractController
{
    /**
     *
     * @Security("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_RH') or is_granted('ROLE_SG') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')")
     */
    #[Route('/conge', name: 'app_conge_index', methods: ['GET','POST'])]
    public function index(secure $security ): Response
    {

        $em = $this->getDoctrine()->getManager();
        $validateur_codes = $security->getUser()->getCodes();
        $validateur_roles = $security->getUser()->getRoles() ;
        $conges=$em->getRepository(Conge::class)->searchDemandesByService($validateur_codes,$validateur_roles);

        if( in_array("ROLE_DIR",$validateur_roles) ){
            $conges=$em->getRepository(Conge::class)->findby(array('statut' => -1 ,'niveau'=>'ROLE_DIR'));
        }


        return $this->render('conge/table-datatable-conge.html.twig', [
            'conges' => $conges,
        ]);
    }


    #[Route('/conge_bloque_{id}', name: 'app_conge_bloque', methods: ['GET','POST'])]
    public function bloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Conge::class)->bloque_conge($id);
        return new JsonResponse('1');
      
    }

    #[Route('/conge_debloque_{id}', name: 'app_conge_debloque', methods: ['GET','POST'])]
    public function debloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Conge::class)->debloque_conge($id);
        return new JsonResponse('0');
    }


    /**
     *
     * @Security("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_RH') or is_granted('ROLE_SG') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')")
     */
    #[Route('/congeVAL_{id}', name: 'congeAL', methods: ['GET','POST'])]
    public function validation(Request $request  ,secure $security , HistoDemandesRepository $HistoDemandesRepository , Conge $conge, CongeRepository $congeRepository , $id , MailerInterface $mailer , InternetTest $int): Response
    {
        $searchParam = $request->get('searchParam');
        $em = $this->getDoctrine()->getManager();
        $destinataire = null;

        if( in_array("ROLE_RH",$security->getUser()->getRoles()) ){

            if($searchParam['statut']=="1"){
                $conge->setStatut("1");    
                  
            }else{
                $conge->setStatut("2");
                $conge->setMotifRefu($searchParam['motifRefu']);

                $soldeid =  $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$conge->getPersonnel()->getId(),"annee"=>$conge->getAnnee()])[0]->getId();
                if($conge->getTypeConge()=="N" ){
                    $sc_normale = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeConge() ;
                    $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeConge( $sc_normale + $conge->getNbJour() ); 
                }
                if($conge->getTypeConge()=="E" ){
                    $sc_ex = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeCongeEx() ;
                    $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeCongeEx($sc_ex + $conge->getNbJour() ); 
                }  

            }
           
            $subject = "Demande de Conge";
            $html = $this->renderView('conge/email-conge.html.twig',['conge'  => $conge]); 
            $email = (new TemplatedEmail())
            ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
            ->to($conge->getPersonnel()->getIdUser()->getEmail())
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
                $conge->setNiveau("ROLE_RH");
                $conge->setStatut("-1");

               
                $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                $subject = "Traitement de Congé";
                $html = $this->renderView('conge/email-conge-notif.html.twig',['conge'  => $conge ,'destinataire' => $destinataire]); 
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
                $conge->setStatut("2");
                $conge->setMotifRefu($searchParam['motifRefu']);

                $soldeid =  $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$conge->getPersonnel()->getId(),"annee"=>$conge->getAnnee()])[0]->getId();
                if($conge->getTypeConge()=="N" ){
                    $sc_normale = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeConge() ;
                    $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeConge( $sc_normale + $conge->getNbJour() ); 
                }
                if($conge->getTypeConge()=="E" ){
                    $sc_ex = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeCongeEx() ;
                    $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeCongeEx($sc_ex + $conge->getNbJour() ); 
                }  
            } 

           

        }

        $HistoDemandes = new HistoDemandes();
        $HistoDemandes->setTypeDemande('conge') ; 
        $HistoDemandes->setDateValidation(new \DateTime()) ;
        $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
        $HistoDemandes->setStatut($conge->getStatut()) ;
        $HistoDemandes->setNiveau($conge->getNiveau()) ;
        $HistoDemandes->setDemandeur($conge->getPersonnel()) ;
        $HistoDemandes->setDateEnvoie($conge->getDateEnvoie()) ;
        $date_reprise = $conge->getDateReprise();
        $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
        $HistoDemandes->setIdDemande($conge->getId()) ;

        $HistoDemandesRepository->save($HistoDemandes, true);
        $congeRepository->save($conge, true);

        return new RedirectResponse($this->generateUrl('app_conge_index'));
    }

 
    /**
     *
     * @Security("is_granted('ROLE_FONC')")
     */
    #[Route('/conge_new', name: 'app_conge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CongeRepository $congeRepository ,HistoDemandesRepository $HistoDemandesRepository, secure $security, MailerInterface $mailer , InternetTest $int): Response
    {
        $em = $this->getDoctrine()->getManager();
        $conges=$em->getRepository(Conge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()],['id'=>'ASC']);

        $solde_en_cours = $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId() , "annee"=>intval(Date("Y"))]);
        $solde_prec = $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId() , "annee"=> intval(Date("Y")-1)]); 

        $en_cours_normale = 0;
        $en_cours_ex= 0;
        $prec_normale= 0;
   

        if($solde_en_cours!=null){
            $en_cours_normale =  $solde_en_cours[0]->getSoldeConge();
            $en_cours_ex =  $solde_en_cours[0]->getSoldeCongeEx();
        }
        if($solde_prec!=null){
            $prec_normale =  $solde_prec[0]->getSoldeConge();
        }

   

        $conge = new Conge();
        $form = $this->createForm(CongeType::class, $conge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                 
            $solde_en_cours = $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId() , "annee"=>intval(Date("Y"))]);
            $solde_prec = $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId() , "annee"=> intval(Date("Y")-1)]); //intval(Date("Y")-1)
    
            $en_cours_normale = 0;
            $en_cours_ex= 0;
            $prec_normale= 0;

    
            if($solde_en_cours!=null){
                $en_cours_normale =  $solde_en_cours[0]->getSoldeConge();
                $en_cours_ex =  $solde_en_cours[0]->getSoldeCongeEx();
            }
            if($solde_prec!=null){
                $prec_normale =  $solde_prec[0]->getSoldeConge();
            }
    

          


            $destinataire = null;
            $demandeur = $security->getUser()->getPersonnel();
            $conge->setPersonnel($demandeur);
            $conge->setBloque(0);
            $soldeid =  $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId(),"annee"=>$conge->getAnnee()])[0]->getId();

            $conge->setSoldeconge($em->getRepository(SoldeConge::class)->find($soldeid)); 

       
            $conge->setDateEnvoie(new \DateTime());
        
            $conge->setStatut("-1");
            if( in_array("ROLE_FONC",$security->getUser()->getRoles())  && !in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles()) ){
                $conge->setNiveau("ROLE_CHEF_SERV");

             
                $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleServ('SER_'.$conge->getPersonnel()->getServiceAffectationId()->getId(),'ROLE_CHEF_SERV') ;


                if($destinataire==null || $destinataire=='' )
                {
                    $role_superieur = $security->getUser()->getPersonnel()->getServiceAffectationId()->getRoleSuperieur() ;
                    $conge->setNiveau($role_superieur);
            

                if($role_superieur=='ROLE_SG'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_SG') ;
                                      }

                if($role_superieur=='ROLE_DIR_ADJ'){
                     $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_'.$conge->getPersonnel()->getServiceAffectationId()->getCodes(),'ROLE_DIR_ADJ') ;
                                     }    
                                     
                if($role_superieur=='ROLE_DIR'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDir() ;
                                                        }    
                }    

                $subject = "Traitement de Congé";
                $html = $this->renderView('conge/email-conge-notif.html.twig',['conge'  => $conge ,'destinataire' => $destinataire]); 
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
                $conge->setNiveau($role_superieur);

                if($role_superieur=='ROLE_SG'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_SG') ;
                                      }

              if($role_superieur=='ROLE_DIR_ADJ'){
                     $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_'.$conge->getPersonnel()->getServiceAffectationId()->getCodes(),'ROLE_DIR_ADJ') ;
                                     }            
                                     
             if($role_superieur=='ROLE_DIR'){
                                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDir() ;
                                   }   

       $subject = "Traitement de Conge";
       $html = $this->renderView('conge/email-conge-notif.html.twig',['conge'  => $conge ,'destinataire' => $destinataire]); 
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
                $conge->setNiveau("ROLE_RH");

                $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                $subject = "Traitement de Conge";
                $html = $this->renderView('conge/email-conge-notif.html.twig',['conge'  => $conge ,'destinataire' => $destinataire]); 
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

            
            $soldeid =  $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId(),"annee"=>$conge->getAnnee()])[0]->getId();
            if($conge->getTypeConge()=="N" ){
                $sc_normale = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeConge() ;
                $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeConge( $sc_normale - $conge->getNbJour() ); 
            }
            if($conge->getTypeConge()=="E" ){
                $sc_ex = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeCongeEx() ;
                $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeCongeEx($sc_ex - $conge->getNbJour() ); 
            }           
           
            $solde_en_cours = $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId() , "annee"=>intval(Date("Y"))]);
            $solde_prec = $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId() , "annee"=> intval(Date("Y")-1)]); //intval(Date("Y")-1)
    
            $en_cours_normale = 0;
            $en_cours_ex= 0;
            $prec_normale= 0;
    
            if($solde_en_cours!=null){
                $en_cours_normale =  $solde_en_cours[0]->getSoldeConge();
                $en_cours_ex =  $solde_en_cours[0]->getSoldeCongeEx();
            }
            if($solde_prec!=null){
                $prec_normale =  $solde_prec[0]->getSoldeConge();
            }
    
            $overlapped = false ;
            $overlaped_conge = null ;
            $conges_demandes_intervals =  $em->getRepository(Conge::class)->findBy(array('personnel'=>$security->getUser()->getPersonnel()->getId(),'statut'=>(array(-1,1)))); // OVERLAPPING
            foreach ($conges_demandes_intervals as $value){   

               $d_debut = $value->getDateDebut();
               $d_reprise =  $value->getDateReprise();
               
               if( $d_debut <= $conge->getDateReprise() && $conge->getDateDebut() <= $d_reprise ){
               $overlapped = true;
               $overlaped_conge = "Vous avez un autre congés dans le méme interval choisie , [ Congé Numéro : ".
               $value->getId().", De ". $value->getDateDebut()->format('Y-m-d'). " à ".$value->getDateReprise()->format('Y-m-d') ." ] Veuillez choisir un autre interval !" ;
                break ;
               }

            }
            $conges=$em->getRepository(Conge::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()],['id'=>'ASC']);

            if($overlapped==false){
                $congeRepository->save($conge, true);

                $HistoDemandes = new HistoDemandes();
                $HistoDemandes->setTypeDemande('conge') ; 
                $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
                $HistoDemandes->setStatut($conge->getStatut()) ;
                $HistoDemandes->setNiveau($conge->getNiveau()) ;
                $date_reprise = $conge->getDateReprise();
                $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
                $HistoDemandes->setDemandeur($conge->getPersonnel()) ;
                $HistoDemandes->setDateEnvoie($conge->getDateEnvoie()) ;
                $HistoDemandes->setIdDemande($conge->getId()) ;
    
                $HistoDemandesRepository->save($HistoDemandes, true);
          
                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            }else{
                $this->get('session')->getFlashBag()->add('danger',$overlaped_conge );
              //  $this->get('session')->getFlashBag()->add("conge_info_overlap","koko");
                
            }
            return $this->redirectToRoute('app_conge_new');
            
               
       
        }

        return $this->renderForm('conge/new-conge.html.twig', [
            'conge' => $conge,
            'conges' => $conges,
            'form' => $form,
            'en_cours_normale' => $en_cours_normale,
            'en_cours_ex'=> $en_cours_ex,
            'prec_normale'=> $prec_normale,
        ]);
    }

  


    /**
     *
     * @Security("is_granted('ROLE_FONC')")
     */
    #[Route('/conge_{id}_{_token}', name: 'app_conge_delete', methods: ['POST','GET'])]
    public function delete(Request $request, Conge $conge, CongeRepository $congeRepository , $_token): Response
    {  
       
        if ($this->isCsrfTokenValid('delete'.$conge->getId(), $_token)) {

            $em = $this->getDoctrine()->getManager();


            $soldeid =  $em->getRepository(SoldeConge::class)->findBy(["personnel"=>$conge->getPersonnel()->getId(),"annee"=>$conge->getAnnee()])[0]->getId();
            if($conge->getTypeConge()=="N" ){
                $sc_normale = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeConge() ;
                $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeConge( $sc_normale + $conge->getNbJour() ); 
            }
            if($conge->getTypeConge()=="E" ){
                $sc_ex = $em->getRepository(SoldeConge::class)->find($soldeid)->getSoldeCongeEx() ;
                $em->getRepository(SoldeConge::class)->find($soldeid)->setSoldeCongeEx($sc_ex + $conge->getNbJour() ); 
            }  







            $histo_conge= $em->getRepository(HistoDemandes::class)->findBy(['id_demande' => strval($conge->getId()),"type_demande"=>"conge"]);
            foreach ($histo_conge as $cn) {$em->remove($cn);}
            $em->flush();
            $congeRepository->remove($conge, true);
        }

        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
        return $this->redirectToRoute('app_conge_new', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/congePdf_{id}', name: 'congePdf', methods: ['GET', 'POST'])]
    public function conge_pdf(Pdf $knpSnappyPdf , Request $request  , Conge $conge , KernelInterface $appKernel)
    {
        $filename = 'Conge N.'.$conge->GetId().' '. $conge->getPersonnel()->GetNom() ." ".$conge->getPersonnel()->GetPrenom().'.pdf';

        $html = $this->renderView('document/conge.html.twig', [
            'conge' => $conge,
        ]);

        $dir = $this->getParameter('webroot_doc'). $conge->getPersonnel()->GetNom().'_'.$conge->getPersonnel()->GetPrenom().'/Conge/' ;
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



  
    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/add_solde_conge', name: 'add_solde_conge', methods: ['POST','GET'])]
    public function congeAnneeAction()
    {
         $em = $this->getDoctrine()->getManager();
        $personnels = $em->getRepository(Personnel::class)->findAll();
        $year = date('Y');
        foreach ($personnels as $personne){
          if($personne->getTypePersonnelId()->getId() == 2 ){
            $soldeConge = $em->getRepository(SoldeConge::class)->findOneBy(array('personnel'=>$personne, 'annee'=>$year));
            if(empty($soldeConge)){
              $entity = new SoldeConge();
              $entity->setSoldeConge(22);
              $entity->setSoldeCongeEx(10);
              $entity->setAnnee($year);
              $entity->setPersonnel($personne);
              $em->persist($entity);
            }
            
            $em->flush();
          }
          
        }

        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
        return $this->redirectToRoute('app_import_export_rh_index', [], Response::HTTP_SEE_OTHER);
       // return $this->render('Personnel/parametreExport.html.twig');

    }


}
 
