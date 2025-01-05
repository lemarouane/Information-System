<?php

namespace App\Controller;

use App\Entity\OrdreMission;
use App\Entity\Engagement;

use App\Entity\Personnel;
use App\Entity\Utilisateurs;
use App\Form\OrdreMissionType;
use App\Form\EngagementType;
use App\Twig\ConfigExtension;
use App\Entity\Config;
use App\Repository\OrdreMissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use App\Entity\HistoDemandes;
use App\Repository\HistoDemandesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Service\InternetTest;


class OrdreMissionController extends AbstractController
{
   /**
     *
     * @Security("is_granted('ROLE_CHEF_SERV')  or is_granted('ROLE_CHEF_DEP') or is_granted('ROLE_CHEF_STRUCT') or is_granted('ROLE_RH') or is_granted('ROLE_SG') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')")
     */
    #[Route('/ordre_mission', name: 'app_ordre_mission_index', methods: ['GET','POST'])]
    public function index(OrdreMissionRepository $ordreMissionRepository , secure $security ): Response
    {
        $em = $this->getDoctrine()->getManager();
        $validateur_codes = $security->getUser()->getCodes();
        $validateur_roles = $security->getUser()->getRoles() ;
        
        $ordre_missions1=null;
        $ordre_missions2=null;
        $ordre_missions3=null;
        $ordre_missions4=null;
        $ordre_missions5=null;
        if( in_array("ROLE_DIR_ADJ",$validateur_roles) || in_array("ROLE_CHEF_SERV",$validateur_roles) || in_array("ROLE_SG",$validateur_roles)  || in_array("ROLE_RH",$validateur_roles ) ){
            $ordre_missions1=$em->getRepository(OrdreMission::class)->searchDemandesByService($validateur_codes,$validateur_roles);
        }
       
        if( in_array("ROLE_CHEF_DEP",$validateur_roles) ){
            $ordre_missions2=$em->getRepository(OrdreMission::class)->searchDemandesByDep($validateur_codes,$validateur_roles);
        }
       
        if( in_array("ROLE_CHEF_STRUCT",$validateur_roles) ){
            $ordre_missions3=$em->getRepository(OrdreMission::class)->searchDemandesByLab($validateur_codes,$validateur_roles);
        }
        if( in_array("ROLE_DIR",$validateur_roles) ){
            $ordre_missions4=$em->getRepository(OrdreMission::class)->findby(array('statut' => -1 ,'niveau'=>'ROLE_DIR'));
        }
        if( in_array("ROLE_ADMIN",$validateur_roles) ){
            $ordre_missions5=$em->getRepository(OrdreMission::class)->findby(array('statut' => -1));
        }
      
        $ordre_missions = (array) array_merge((array) $ordre_missions1,(array) $ordre_missions2,(array) $ordre_missions3,(array) $ordre_missions4,(array) $ordre_missions5);
        

        return $this->render('ordre_mission/table-datatable-ordremission.html.twig',  [
            'ordre_missions' => $ordre_missions,
            'lien'=>$this->getParameter('webroot_doc'),
        ]);
    }
 


 /**
     *
     * @Security("is_granted('ROLE_RH')")
     */
    #[Route('/histo_om', name: 'histo_om', methods: ['GET','POST'])]
    public function histo_om(OrdreMissionRepository $ordreMissionRepository , secure $security ): Response
    {
       
        $ordre_missions = $ordreMissionRepository->findBy(array('statut' => array(1, 2)));
    
        return $this->render('histo_demandes/table-datatable-histo_om.html.twig',  [
            'oms' => $ordre_missions,
        ]);
    }
 



   /**
     *
     * @Security("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_RH') or is_granted('ROLE_CHEF_DEP') or is_granted('ROLE_CHEF_STRUCT') or is_granted('ROLE_SG') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')") 
     */
    #[Route('/ordre_missionVAL_{id}', name: 'ordre_missionVAL', methods: ['GET','POST'])]
    public function validation(Request $request  ,secure $security , HistoDemandesRepository $HistoDemandesRepository , OrdreMission $ordre_mission, OrdreMissionRepository $ordreMissionRepository , $id , MailerInterface $mailer  , InternetTest $int): Response
    {
        $searchParam = $request->get('searchParam');
        $destinataire = null;
        $em = $this->getDoctrine()->getManager();

        if( in_array("ROLE_RH",$security->getUser()->getRoles()) ){

            if($searchParam['statut']=="1"){
                $ordre_mission->setStatut("1");
            }else{
                $ordre_mission->setStatut("2");
                $ordre_mission->setMotif($searchParam['motifRefu']);
            }
           

            $subject = "Demande d'Ordre de Mission";
            $html = $this->renderView('ordre_mission/email-ordre-mission.html.twig',['om'  => $ordre_mission]); 
            $email = (new TemplatedEmail())
            ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
            ->to($ordre_mission->getPersonnel()->getIdUser()->getEmail())
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
          
                $var= $ordre_mission->getNiveau();
                if( in_array("ROLE_CHEF_DEP",$security->getUser()->getRoles()) ){
                if($searchParam['statut']=="1"){
    
                    if($ordre_mission->getTypeMission()=="R"){
                        $ordre_mission->setNiveau("ROLE_CHEF_STRUCT");
                        $ordre_mission->setStatut("-1");

                        $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleStruct($ordre_mission->getPersonnel()->getStructureRech()->getId(),'ROLE_CHEF_STRUCT') ;

                        $subject = "Traitement d'Ordre de Mission";
                        $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordre_mission ,'destinataire' => $destinataire]); 
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
                        $ordre_mission->setNiveau("ROLE_DIR_ADJ");
                        $ordre_mission->setStatut("-1");


                        $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_1','ROLE_DIR_ADJ') ;
                        $subject = "Traitement d'Ordre de Mission";
                        $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordre_mission ,'destinataire' => $destinataire]); 
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
                }else{
                    $ordre_mission->setStatut("2");
                    $ordre_mission->setMotif($searchParam['motifRefu']);
                }
              }
    
                if( in_array("ROLE_CHEF_STRUCT",$security->getUser()->getRoles()) ){
                    if($searchParam['statut']=="1"){
                            $ordre_mission->setNiveau("ROLE_DIR_ADJ");
                            $ordre_mission->setStatut("-1");   

                            $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_1','ROLE_DIR_ADJ') ;
                            $subject = "Traitement d'Ordre de Mission";
                            $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordre_mission ,'destinataire' => $destinataire]); 
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
                        $ordre_mission->setStatut("2");
                        $ordre_mission->setMotif($searchParam['motifRefu']);
                    }
                       
                }
    
                if( (in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles()) && $var == "ROLE_DIR_ADJ" ) || in_array("ROLE_SG",$security->getUser()->getRoles()) || in_array("ROLE_DIR",$security->getUser()->getRoles()) ){
                    if($searchParam['statut']=="1"){
                            $ordre_mission->setNiveau("ROLE_RH");
                            $ordre_mission->setStatut("-1");   


                            $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                            $subject = "Traitement Ordre de Mission";
                            $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordre_mission ,'destinataire' => $destinataire]); 
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
                        $ordre_mission->setStatut("2");
                        $ordre_mission->setMotif($searchParam['motifRefu']);
                    }
                       
                }

             

                if( in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles()) ){
           
                    if($searchParam['statut']=="1"){
                            $ordre_mission->setNiveau("ROLE_RH");
                            $ordre_mission->setStatut("-1");   

                            $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                            $subject = "Traitement Ordre de Mission";
                            $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordre_mission ,'destinataire' => $destinataire]); 
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
                        $ordre_mission->setStatut("2");
                        $ordre_mission->setMotif($searchParam['motifRefu']);
                    }

                   
                }




        }

        $HistoDemandes = new HistoDemandes();
        $HistoDemandes->setTypeDemande('ordre de mission') ; 
        $HistoDemandes->setDateValidation(new \DateTime()) ;
        $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
        $HistoDemandes->setStatut($ordre_mission->getStatut()) ;
        $HistoDemandes->setNiveau($ordre_mission->getNiveau()) ;
        $HistoDemandes->setDemandeur($ordre_mission->getPersonnel()) ;
        $HistoDemandes->setDateEnvoie($ordre_mission->getDateEnvoie()) ;
        $date_reprise = $ordre_mission->getDateFin();
        $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
        $HistoDemandes->setIdDemande($ordre_mission->getId()) ;

        $HistoDemandesRepository->save($HistoDemandes, true);
        $ordreMissionRepository->save($ordre_mission, true);

        return new RedirectResponse($this->generateUrl('app_ordre_mission_index'));
    }





    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     */
    #[Route('/ordre_mission_new', name: 'app_ordre_mission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrdreMissionRepository $ordreMissionRepository , secure $security  ,HistoDemandesRepository $HistoDemandesRepository, MailerInterface $mailer  , InternetTest $int): Response
    {

        $ordreMission = new OrdreMission();
        $destinataire = null;

        $form = $this->createForm(OrdreMissionType::class, $ordreMission);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $ordreMissions=$em->getRepository(OrdreMission::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
        $ordreMission->setPersonnel($security->getUser()->getPersonnel());


        if ($form->isSubmitted() && $form->isValid())
         {



            $ordreMission->setBloque(0);
            $ordreMission->setDateEnvoie(new \DateTime());
            $ordreMission->setStatut("-1");


            if( in_array("ROLE_PROF",$security->getUser()->getRoles()) && $ordreMission->getPersonnel()->getDepartementId()==null){
                $this->get('session')->getFlashBag()->add('danger', "MOD_NON_ATACHEE_DEP");
                return $this->redirectToRoute('app_ordre_mission_new', [], Response::HTTP_SEE_OTHER);
            }

            if( in_array("ROLE_PROF",$security->getUser()->getRoles()) && $ordreMission->getPersonnel()->getStructureRech()==null &&  $ordreMission->getTypeMission()=="R"){

                $this->get('session')->getFlashBag()->add('danger', "MOD_NON_ATACHEE_STR");
                return $this->redirectToRoute('app_ordre_mission_new', [], Response::HTTP_SEE_OTHER);
               
            }










            if( in_array("ROLE_FONC",$security->getUser()->getRoles()) && !in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles()) ){
                $ordreMission->setNiveau("ROLE_CHEF_SERV");

                $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleServ('SER_'.$ordreMission->getPersonnel()->getServiceAffectationId()->getId(),'ROLE_CHEF_SERV') ;


                if($destinataire==null || $destinataire=='' )
                {
                    $role_superieur = $security->getUser()->getPersonnel()->getServiceAffectationId()->getRoleSuperieur() ;
                    $ordreMission->setNiveau($role_superieur);
            

                if($role_superieur=='ROLE_SG'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_SG') ;
                                      }

                if($role_superieur=='ROLE_DIR_ADJ'){
                     $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_'.$ordreMission->getPersonnel()->getServiceAffectationId()->getCodes(),'ROLE_DIR_ADJ') ;
                                     }    
                                     
                if($role_superieur=='ROLE_DIR'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDir() ;
                                                        }    
                }    





                $subject = "Traitement d'Ordre de Mission";
                $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordreMission ,'destinataire' => $destinataire]); 
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

            if( ( in_array("ROLE_PROF",$security->getUser()->getRoles()) && !in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles()) && !in_array("ROLE_DIR",$security->getUser()->getRoles()) ) || ( in_array("ROLE_CHEF_STRUCT",$security->getUser()->getRoles()) && !in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles()) && !in_array("ROLE_DIR",$security->getUser()->getRoles())) ){
                $ordreMission->setNiveau("ROLE_CHEF_DEP");


                $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDep($ordreMission->getPersonnel()->getDepartementId()->getId(),'ROLE_CHEF_DEP') ;
                $subject = "Traitement d'Ordre de Mission";
                $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordreMission ,'destinataire' => $destinataire]); 
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

            if( in_array("ROLE_CHEF_DEP",$security->getUser()->getRoles())){
                if( $ordreMission->getTypeMission()=="R"){
                    $ordreMission->setNiveau("ROLE_CHEF_STRUCT");


                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleStruct($ordreMission->getPersonnel()->getStructureRech()->getId(),'ROLE_CHEF_STRUCT') ;

               

                    $subject = "Traitement d'Ordre de Mission";
                    $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordreMission ,'destinataire' => $destinataire]); 
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
                    $ordreMission->setNiveau("ROLE_DIR_ADJ");

                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_'.$ordreMission->getPersonnel()->getServiceAffectationId()->getCodes(),'ROLE_DIR_ADJ') ;
                    $subject = "Traitement d'Ordre de Mission";
                    $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordreMission ,'destinataire' => $destinataire]); 
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
            }

            if( in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles()) ){
                $role_superieur = $security->getUser()->getPersonnel()->getServiceAffectationId()->getRoleSuperieur() ;
                $ordreMission->setNiveau($role_superieur);


                if($role_superieur=='ROLE_SG'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_SG') ;
                                      }

              if($role_superieur=='ROLE_DIR_ADJ'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_'.$ordreMission->getPersonnel()->getServiceAffectationId()->getCodes(),'ROLE_DIR_ADJ') ;
                                     }             

              if($role_superieur=='ROLE_DIR'){
                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDir() ;
                    }     

                    $subject = "Traitement Ordre de Mission";
                    $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordreMission ,'destinataire' => $destinataire]); 
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
                $ordreMission->setNiveau("ROLE_RH");


                $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                $subject = "Traitement Ordre de Mission";
                $html = $this->renderView('ordre_mission/email-ordre-mission-notif.html.twig',['om'  => $ordreMission ,'destinataire' => $destinataire]); 
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

          
         

            $invitFile = $form->get('invitFile')->getData();
            if(!empty($invitFile)){

                
                if ( file_exists($this->getParameter('webroot_doc'). $ordreMission->getPersonnel()->GetNom().'_'.$ordreMission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordreMission->getInvitation()) 
                    && $ordreMission->getInvitation()!="" && $ordreMission->getInvitation()!=NULL){
                    unlink($this->getParameter('webroot_doc'). $ordreMission->getPersonnel()->GetNom().'_'.$ordreMission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordreMission->getInvitation());
                }
                
                $fileUploader = new FileUploader($this->getParameter('webroot_doc'). $ordreMission->getPersonnel()->GetNom().'_'.$ordreMission->getPersonnel()->GetPrenom().'/Ordre_mission/');
                $invitName = $fileUploader->upload($invitFile);
         
                $ordreMission->setInvitation($invitName);
            }



            
            $ordreMissionRepository->save($ordreMission, true);

            $HistoDemandes = new HistoDemandes();
            $HistoDemandes->setTypeDemande('ordre de mission') ; 
            $HistoDemandes->setStatut($ordreMission->getStatut()) ;
            $HistoDemandes->setNiveau($ordreMission->getNiveau()) ;
            $HistoDemandes->setDemandeur($ordreMission->getPersonnel()) ;
            $HistoDemandes->setDateEnvoie($ordreMission->getDateEnvoie()) ;
            $date_reprise = $ordreMission->getDateFin();
            $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
            $HistoDemandes->setIdDemande($ordreMission->getId()) ;

            $HistoDemandesRepository->save($HistoDemandes, true);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            $ordreMissions=$em->getRepository(OrdreMission::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);

            return $this->redirectToRoute('app_ordre_mission_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ordre_mission/new-ordre-mission.html.twig', [
            'ordre_mission' => $ordreMission,
            'form' => $form,
            'ordre_missions'=> $ordreMissions,
        ]);
    }




    /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/ordre_mission_{id}_loop', name: 'app_ordre_mission_loop', methods: ['GET', 'POST'])]
    public function loop(Request $request, OrdreMission $ordreMission, OrdreMissionRepository $ordreMissionRepository , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ordreMission=$em->getRepository(OrdreMission::class)->find($id);
        return $this->renderForm('ordre_mission/ordremission-loop.html.twig',[
            'ordreMission' => $ordreMission,
        ]);


    }





    /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/ordre_mission_{id}_edit', name: 'app_ordre_mission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrdreMission $ordreMission, secure $security , OrdreMissionRepository $ordreMissionRepository): Response
    {
        if($ordreMission->getStatut()=="-1" && $ordreMission->getPersonnel()->getId() == $security->getUser()->getPersonnel()->getId()){

            $form = $this->createForm(OrdreMissionType::class, $ordreMission);
            $form->handleRequest($request);
            $em = $this->getDoctrine()->getManager();
            $ordreMissions=$em->getRepository(OrdreMission::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
            if ($form->isSubmitted() && $form->isValid()) {

                if($form['financementMission'][2]->getData()){
                   if($form['valeurProjet']->getData()==NULL) {
                        $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
                        return $this->renderForm('ordre_mission/edit-ordre-mission.html.twig', [
                            'ordre_mission' => $ordreMission,
                            'form' => $form,
                            'ordre_missions'=> $ordreMissions,
                            'id' => $ordreMission->getId(), 
                        ]);
                   }
                }
                if($form['financementMission'][3]->getData()){
                    if($form['valeurAutre']->getData()==NULL) {
                         $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
                         return $this->renderForm('ordre_mission/edit-ordre-mission.html.twig', [
                             'ordre_mission' => $ordreMission,
                             'form' => $form,
                             'ordre_missions'=> $ordreMissions,
                             'id' => $ordreMission->getId(), 
                         ]);
                    }
                 }
              
               
                $invitFile = $form->get('invitFile')->getData();
                if(!empty($invitFile)){
    
                    if ( file_exists($this->getParameter('webroot_doc'). $ordreMission->getPersonnel()->GetNom().'_'.$ordreMission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordreMission->getInvitation()) 
                && $ordreMission->getInvitation()!="" && $ordreMission->getInvitation()!=NULL  ){
                    unlink($this->getParameter('webroot_doc'). $ordreMission->getPersonnel()->GetNom().'_'.$ordreMission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordreMission->getInvitation());
                }
                
                    
                    $fileUploader = new FileUploader($this->getParameter('webroot_doc'). $ordreMission->getPersonnel()->GetNom().'_'.$ordreMission->getPersonnel()->GetPrenom().'/Ordre_mission/');
                    $invitName = $fileUploader->upload($invitFile);
          
                    $ordreMission->setInvitation($invitName);
                }


                if(!$ordreMission->isBloque()){
                    $ordreMissionRepository->save($ordreMission, true);
                    $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
                }else{
                    $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
                }

            }
    
           
    
            return $this->renderForm('ordre_mission/edit-ordre-mission.html.twig', [
                'ordre_mission' => $ordreMission,
                'form' => $form,
                'ordre_missions'=> $ordreMissions,
                'id' => $ordreMission->getId(), 
            ]);


        }else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
        }
     
    }

     /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/ordre_mission_{id}_{_token}', name: 'app_ordre_mission_delete', methods: ['GET','POST'])]
    public function delete(Request $request, OrdreMission $ordreMission, OrdreMissionRepository $ordreMissionRepository,$_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ordreMission->getId(), $_token)) {

            $em = $this->getDoctrine()->getManager();
            $histo_auto = $em->getRepository(HistoDemandes::class)->findBy(['id_demande' => strval($ordreMission->getId()),"type_demande"=>"ordre de mission"]);
            foreach ($histo_auto as $auto) {$em->remove($auto);}
            $em->flush();

            $ordreMissionRepository->remove($ordreMission, true);
        }
        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
        return $this->redirectToRoute('app_ordre_mission_new', [], Response::HTTP_SEE_OTHER);
    }





    #[Route('/ordremissionPdf_date_{id}', name: 'ordremissionPdf_date', methods: ['GET', 'POST'])]
    public function ordremission_date_pdf(Pdf $knpSnappyPdf , Request $request  , Ordremission $ordremission ,OrdreMissionRepository $ordreMissionRepository, KernelInterface $appKernel  , $id )
    { 
        $d_om = $request->get('d_om');

        $c_moy_transp_om = $request->get('c_moy_transp_om');

        if($c_moy_transp_om!='0'){
         $ordremission->setMoyenTransport($c_moy_transp_om) ;
        }
        
   
        $em = $this->getDoctrine()->getManager();
        $param= new ConfigExtension($em);
        $nb_om = $param->app_config('ORDRE_MISSION_INDEX');
        $nb_om++;
      
        if($ordremission->getLien()!=null){
            $filename = $ordremission->getLien();
        }else{
            $filename = 'default.pdf';
        }
  
       
       $dir = $this->getParameter('webroot_doc'). $ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom().'/Ordre_mission/' ;
          if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
          }

          if($ordremission->getPersonnel()->getTypePersonnelId()->getId()==2) {
            $html = $this->renderView('document/ordreMissionAd.html.twig', [
                'ordreMission' => $ordremission,
                'nb_om' => $nb_om, 
                'date_om' => $d_om
            ]);
        }else{
            $html = $this->renderView('document/ordreMissionPr.html.twig', [
                'ordreMission' => $ordremission,
                'nb_om' => $nb_om,
                'date_om' => $d_om
             ]);
       }
       $data['dir'] = "/webroot/docs/".$ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordremission->getLien();
       $data['name'] = "Ordre_mission_".$ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom();

          if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $ordremission->setLien($filename);
            $ordreMissionRepository->save($ordremission, true);
            $em->getRepository(Config::class)->updateBy('ORDRE_MISSION_INDEX', $nb_om);

         /*    return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                $filename ,
            ); */
            $data['dir'] = "/webroot/docs/".$ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordremission->getLien();

            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                $filename ,
            );
            //return new JsonResponse($data);

          }else{

          //  header("Content-type:application/pdf");
          //  readfile($dir.$filename);

          
             //unlink($dir.$filename);
             //$knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            //return new JsonResponse($data);
             
          }
          
    
        
    }










    #[Route('/ordremissionPdf_{id}', name: 'ordremissionPdf', methods: ['GET', 'POST'])]
    public function ordremission_pdf(Pdf $knpSnappyPdf , Request $request  , Ordremission $ordremission ,OrdreMissionRepository $ordreMissionRepository, KernelInterface $appKernel)
    { 
        $em = $this->getDoctrine()->getManager();
        $param= new ConfigExtension($em);
        $nb_om = $param->app_config('ORDRE_MISSION_INDEX');
        $nb_om++;
      
        if($ordremission->getLien()!=null){
            $filename = $ordremission->getLien();
        }else{
            $filename = 'default.pdf';
        }
   
       
       $dir = $this->getParameter('webroot_doc'). $ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom().'/Ordre_mission/' ;
          if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
          }

          if($ordremission->getPersonnel()->getTypePersonnelId()->getId()==2) {
            $html = $this->renderView('document/ordreMissionAd.html.twig', [
                'ordreMission' => $ordremission,
                'nb_om' => $nb_om,
            ]);
        }else{
            $html = $this->renderView('document/ordreMissionPr.html.twig', [
                'ordreMission' => $ordremission,
                'nb_om' => $nb_om,
             ]);
       }
       $data['dir'] = "/webroot/docs/".$ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordremission->getLien();
       $data['name'] = "Ordre_mission_".$ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom();

          if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $ordremission->setLien($filename);
            $ordreMissionRepository->save($ordremission, true);
            $em->getRepository(Config::class)->updateBy('ORDRE_MISSION_INDEX', $nb_om);

         /*    return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                $filename ,
            ); */
            $data['dir'] = "/webroot/docs/".$ordremission->getPersonnel()->GetNom().'_'.$ordremission->getPersonnel()->GetPrenom().'/Ordre_mission/'.$ordremission->getLien();

            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                $filename ,
            );
            //return new JsonResponse($data);

          }else{

            header("Content-type:application/pdf");
            header('Content-Disposition: attachment; filename="'.$data['name'].'"');
            // I used the first PDF I could find on the internet for the demo
            readfile($dir.$filename);
             //unlink($dir.$filename);
             //$knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            //return new JsonResponse($data);
             
          }
          
    
        
    }




    #[Route('/ordremission_bloque_{id}', name: 'app_ordremission_bloque', methods: ['GET','POST'])]
    public function bloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Ordremission::class)->bloque_ordremission($id);
        return new JsonResponse('1');
    }

    #[Route('/ordremission_debloque_{id}', name: 'app_ordremission_debloque', methods: ['GET','POST'])]
    public function debloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Ordremission::class)->debloque_ordremission($id);
        return new JsonResponse('0');
    }
}
