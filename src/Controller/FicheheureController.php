<?php

namespace App\Controller;

use App\Entity\Ficheheure;
use App\Form\FicheheureType;
use App\Entity\Engagementheure;
use App\Form\EngegementheureType;
use App\Repository\FicheheureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\HistoDemandes;
use App\Entity\Utilisateurs;
use App\Repository\HistoDemandesRepository;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\Mime\Address ;
use DateTime;
use App\Service\InternetTest;



class FicheheureController extends AbstractController
{
    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_RH') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/ficheheure', name: 'app_ficheheure_index', methods: ['GET','POST'])]
    public function index(FicheheureRepository $ficheheureRepository , secure $security): Response
    {
        $em = $this->getDoctrine()->getManager();
        $validateur_codes = $security->getUser()->getCodes();
        $validateur_roles = $security->getUser()->getRoles() ;
        $ficheheures = null ;

        if( in_array("ROLE_CHEF_DEP",$validateur_roles) ){
            $ficheheures=$em->getRepository(Ficheheure::class)->searchDemandesByDep($validateur_codes,$validateur_roles);
        }

        if( (in_array("ROLE_DIR_ADJ",$validateur_roles) && in_array("DIR_1",$validateur_codes))|| in_array("ROLE_RH",$validateur_roles ) ){
            $ficheheures=$em->getRepository(Ficheheure::class)->searchDemandesByService($validateur_codes,$validateur_roles);
        }

        return $this->render('ficheheure/table-datatable-ficheheure.html.twig',  [
            'ficheheures' => $ficheheures,
        ]);
    }


    #[Route('/calc_max_heure_fh', name: 'max_heure_fh', methods: ['POST'])]
    public function max_heure_fh(Ficheheure $ficheheure , $heures_max)
    {   $result = [];

        $d1 = $ficheheure->getMoisDebut();
        $d2 = $ficheheure->getMoisFin();

        $dmem_1 = new DateTime($d1->format('Y-m-d'));
        $dmem_2 = new DateTime($d2->format('Y-m-d'));
                               
        $date_debut = $ficheheure->getMoisDebut();
        $date_fin = $ficheheure->getMoisFin();
        $nb_days = $date_debut->diff($date_fin)->days;

        $months_array = [];
        $heures_sup_array = [];

        $last_month = date("m", strtotime($date_debut->format('Y-m-d')));
        array_push($months_array,$last_month);

        $engs = $ficheheure->getengagements();
        foreach ($engs as $e) {

            switch ($e->getJours()) {
                case "الاثنين":
               $e->setJours('Monday');
                  break;
                case "الثلاثاء":
               $e->setJours('Tuesday');
                  break;
                case "الأربعاء":
               $e->setJours('Wednesday');
                  break;
                case "الخميس":
                $e->setJours('Thursday');
                  break;
                case "الجمعة":
               $e->setJours('Friday');
                 break;
                case "السبت":
               $e->setJours('Saturday');
                 break;
              }
        
          }
 
        $nb_minutes_sup = 0;

        $day = $date_debut->modify('-1 day');

        for($i=0; $i<= $nb_days ; $i++){

            $day = $date_debut->modify('+1 day');
            $month = date("m", strtotime($day->format('Y-m-d')));
            $day_name = date("l", strtotime($day->format('Y-m-d')));
            
            foreach ($engs as $e) {
                if($e->getJours()==$day_name){
                    $time1 = $e->getHeureDebut();
                    $time2 = $e->getHeureFin() ;
                    $diff_minutes  = $time1->diff($time2);

                    $nb_minutes_sup =  $nb_minutes_sup + (($diff_minutes->h)*60) + $diff_minutes->i ;
                }
            }
                
            if($month!=$last_month){
                array_push($months_array,$month);
                array_push($heures_sup_array,$nb_minutes_sup);
                $nb_minutes_sup = 0;
                $last_month = $month;
            }

            }
              array_push($heures_sup_array,$nb_minutes_sup);

              for($k=0; $k< count($heures_sup_array) ; $k++) {

                if($heures_sup_array[$k] > $heures_max * 60){
                    array_push($result,$months_array[$k]);    
                }
               
              }


              $ficheheure->setMoisDebut($dmem_1);
              $ficheheure->setMoisFin($dmem_2);

              $engs = $ficheheure->getengagements();
              foreach ($engs as $e) {
      
                  switch ($e->getJours()) {
                      case "Monday":
                     $e->setJours('الاثنين'); 
                        break;
                      case "Tuesday":
                     $e->setJours('الثلاثاء');
                        break;
                      case "Wednesday":
                     $e->setJours('الأربعاء');
                        break;
                      case "Thursday":
                      $e->setJours('الخميس');
                        break;
                      case "Friday":
                     $e->setJours('الجمعة');
                       break;
                      case "Saturday":
                     $e->setJours('السبت');
                       break;
                    }
              
                }

        return  $result ; //new JsonResponse($ficheheure);
      

    }


    #[Route('/calc_heure_par_mois_fh', name: 'calc_heure_par_mois_fh', methods: ['POST'])]
    public function calc_heure_par_mois_fh(Ficheheure $ficheheure )
    {   $result = [];

        $d1 = $ficheheure->getMoisDebut();
        $d2 = $ficheheure->getMoisFin();

        $dmem_1 = new DateTime($d1->format('Y-m-d'));
        $dmem_2 = new DateTime($d2->format('Y-m-d'));
                               
        $date_debut = $ficheheure->getMoisDebut();
        $date_fin = $ficheheure->getMoisFin();
        $nb_days = $date_debut->diff($date_fin)->days;

        $months_array = [];
        $heures_sup_array = [];

        $last_month = date("m", strtotime($date_debut->format('Y-m-d')));
        array_push($months_array,$last_month);

        $engs = $ficheheure->getengagements();
        foreach ($engs as $e) {

            switch ($e->getJours()) {
                case "الاثنين":
               $e->setJours('Monday');
                  break;
                case "الثلاثاء":
               $e->setJours('Tuesday');
                  break;
                case "الأربعاء":
               $e->setJours('Wednesday');
                  break;
                case "الخميس":
                $e->setJours('Thursday');
                  break;
                case "الجمعة":
               $e->setJours('Friday');
                 break;
                case "السبت":
               $e->setJours('Saturday');
                 break;
              }
        
          }
 
        $nb_minutes_sup = 0;

        $day = $date_debut->modify('-1 day');

        for($i=0; $i<= $nb_days ; $i++){

            $day = $date_debut->modify('+1 day');
            $month = date("m", strtotime($day->format('Y-m-d')));
            $day_name = date("l", strtotime($day->format('Y-m-d')));
            
            foreach ($engs as $e) {
                if($e->getJours()==$day_name){
                    $time1 = $e->getHeureDebut();
                    $time2 = $e->getHeureFin() ;
                    $diff_minutes  = $time1->diff($time2);

                    $nb_minutes_sup =  $nb_minutes_sup + (($diff_minutes->h)*60) + $diff_minutes->i ;
                }
            }
                
            if($month!=$last_month){
                array_push($months_array,$month);
                array_push($heures_sup_array,$nb_minutes_sup);
                $nb_minutes_sup = 0;
                $last_month = $month;
            }

            }
              array_push($heures_sup_array,$nb_minutes_sup);

              for($k=0; $k< count($heures_sup_array) ; $k++) {

             /*    if($heures_sup_array[$k] > $heures_max * 60){
                    array_push($result,$months_array[$k]);    
                } */
               
              }


              $ficheheure->setMoisDebut($dmem_1);
              $ficheheure->setMoisFin($dmem_2);

              $engs = $ficheheure->getengagements();
              foreach ($engs as $e) {
      
                  switch ($e->getJours()) {
                      case "Monday":
                     $e->setJours('الاثنين'); 
                        break;
                      case "Tuesday":
                     $e->setJours('الثلاثاء');
                        break;
                      case "Wednesday":
                     $e->setJours('الأربعاء');
                        break;
                      case "Thursday":
                      $e->setJours('الخميس');
                        break;
                      case "Friday":
                     $e->setJours('الجمعة');
                       break;
                      case "Saturday":
                     $e->setJours('السبت');
                       break;
                    }
              
                }


              $result =  array($months_array, $heures_sup_array);
    
        return  $result ; //new JsonResponse($ficheheure);
      

    }


    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_RH') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/ficheheure_new', name: 'app_ficheheure_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FicheheureRepository $ficheheureRepository ,HistoDemandesRepository $HistoDemandesRepository, secure $security , MailerInterface $mailer , InternetTest $int): Response
    {
        $ficheheure = new Ficheheure();
        $ficheheure->addEngagement(new Engagementheure()) ;
        $form = $this->createForm(FicheheureType::class, $ficheheure);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $ficheheures=$em->getRepository(Ficheheure::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
        $ficheheure->setPersonnel($security->getUser()->getPersonnel());

        if ($form->isSubmitted()  && $form->isValid()) { 
          $max_heures = 20;
          $mois_max_heure_depasse =  $this->max_heure_fh($ficheheure,$max_heures) ;

         if(count($mois_max_heure_depasse) == 0 ){

            $ficheheure->setBloque(0);
            $ficheheure->setDateEnvoie(new \DateTime());
            $ficheheure->setStatut("-1");
            $destinataire = null;
            if($this->isGranted('ROLE_PROF')){

                $ficheheure->setNiveau("ROLE_CHEF_DEP");
                $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDep($ficheheure->getPersonnel()->getDepartementId()->getId(),'ROLE_CHEF_DEP') ;
            }

            if( in_array("ROLE_CHEF_DEP",$security->getUser()->getRoles()) ){
                $ficheheure->setNiveau("ROLE_DIR_ADJ");
                $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj("DIR_1",'ROLE_DIR_ADJ') ;
            }


            if( in_array("ROLE_PROF",$security->getUser()->getRoles()) && $ficheheure->getPersonnel()->getDepartementId()==null){
                $this->get('session')->getFlashBag()->add('danger', "MOD_NON_ATACHEE_DEP");
                return $this->redirectToRoute('app_ficheheure_new', [], Response::HTTP_SEE_OTHER);
            }

            $subject = "Demande d'Autorisation H.S.";
            $html = $this->renderView('ficheheure/email-ficheheure-notif.html.twig',['ficheheure'  => $ficheheure , 'destinataire' => $destinataire]); 
            $email = (new TemplatedEmail())
       
            ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
            ->to($destinataire['email'])
            ->subject($subject)
            ->html($html);
            try {
             if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
          
            } catch (TransportExceptionInterface $e) {
            } 
           
            $ficheheureRepository->save($ficheheure, true);
 
           
            $HistoDemandes = new HistoDemandes();
            $HistoDemandes->setTypeDemande('fiche heure') ; 
            $HistoDemandes->setStatut($ficheheure->getStatut()) ;
            $HistoDemandes->setNiveau($ficheheure->getNiveau()) ;
            $HistoDemandes->setDemandeur($ficheheure->getPersonnel()) ;
            $HistoDemandes->setDateEnvoie($ficheheure->getDateEnvoie()) ;
            $date_reprise = $ficheheure->getMoisFin();
            $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
            $HistoDemandes->setIdDemande($ficheheure->getId()) ;

            $HistoDemandesRepository->save($HistoDemandes, true);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            $ficheheures=$em->getRepository(Ficheheure::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);

            return $this->redirectToRoute('app_ficheheure_new', [], Response::HTTP_SEE_OTHER);
        }else{
            
            $msg = "Vous avez dépassé le nombre maximal d'heures (".$max_heures." Heures) pour le mois ";
            foreach($mois_max_heure_depasse as $mois){
            
                $msg =  $msg." , ".$mois." ";
            } 
            $msg = $msg . " , Veuillez réessayer.";
            $this->get('session')->getFlashBag()->add('danger', $msg);
            return $this->redirectToRoute('app_ficheheure_new', [], Response::HTTP_SEE_OTHER);
         }
        }

        return $this->renderForm('ficheheure/new-ficheheure.html.twig', [
            'ficheheure' => $ficheheure,
            'form' => $form,
            'ficheheures'=> $ficheheures,
        ]);
    }

 
    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_RH') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/ficheheure_edit_{id}', name: 'app_ficheheure_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ficheheure $ficheheure, FicheheureRepository $ficheheureRepository , HistoDemandesRepository $HistoDemandesRepository ,  secure $security): Response
    {
        if($ficheheure->getStatut()=="-1" && $ficheheure->getPersonnel()->getId() == $security->getUser()->getPersonnel()->getId()){

            $form = $this->createForm(FicheheureType::class, $ficheheure);
            $form->handleRequest($request);
            $em = $this->getDoctrine()->getManager();
            $ficheheures=$em->getRepository(Ficheheure::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
            if ($form->isSubmitted() && $form->isValid()) {
          
                if(!$ficheheure->isBloque()){

                    $max_heures = 20;
                    $mois_max_heure_depasse =  $this->max_heure_fh($ficheheure,$max_heures) ;

                    if(count($mois_max_heure_depasse) == 0 ){

                        $ficheheureRepository->save($ficheheure, true);
                        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

                    }else{

                        $msg = "Vous avez dépassé le nombre maximal d'heures (".$max_heures." Heures) pour le mois ";
                        foreach($mois_max_heure_depasse as $mois){
                        
                            $msg =  $msg." , ".$mois." ";
                        } 
                        $msg = $msg . " , Veuillez réessayer.";
                        $this->get('session')->getFlashBag()->add('danger', $msg);

                       // return $this->redirectToRoute('app_ficheheure_new', [], Response::HTTP_SEE_OTHER);
                    }

                   
                }else{
                    $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
                }
               
            }
       
    
            return $this->renderForm('ficheheure/edit-ficheheure.html.twig', [
                'ficheheure' => $ficheheure,
                'form' => $form,
                'ficheheures'=> $ficheheures,
                'id' => $ficheheure->getId(), 
            ]);


        }else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
        }
    }

   

    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_RH') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/ficheheureVAL_{id}', name: 'ficheheureVAL', methods: ['GET','POST'])]
    public function validation(Request $request  ,  secure $security , HistoDemandesRepository $HistoDemandesRepository , Ficheheure $ficheheure, FicheheureRepository $ficheheureRepository , $id , MailerInterface $mailer , InternetTest $int): Response
    {
        $searchParam = $request->get('searchParam');
        $destinataire =null;
        $em = $this->getDoctrine()->getManager();

        if( in_array("ROLE_RH",$security->getUser()->getRoles()) ){

            if($searchParam['statut']=="1"){
                $ficheheure->setStatut("1");
            }else{
                $ficheheure->setStatut("2");
                $ficheheure->setMotif($searchParam['motif']);
            }
           

            $subject = "Demande de Fiche-Heure";
            $html = $this->renderView('ficheheure/email-ficheheure.html.twig',['ficheheure'  => $ficheheure]); 
            $email = (new TemplatedEmail())
            ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
            ->to($ficheheure->getPersonnel()->getIdUser()->getEmail())
            ->subject($subject)
            ->html($html);
            try {
               if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
              
            } catch (TransportExceptionInterface $e) {
            } 
            
        }else{
          
            if( in_array("ROLE_CHEF_DEP",$security->getUser()->getRoles()) ){
                if($searchParam['statut']=="1"){
    
                    $ficheheure->setNiveau("ROLE_DIR_ADJ");
                    $ficheheure->setStatut("-1");

                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRoleDirAdj('DIR_1','ROLE_DIR_ADJ') ;
                    $subject = "Traitement de Fiche-Heure";
                    $html = $this->renderView('ficheheure/email-ficheheure-notif.html.twig',['ficheheure'  => $ficheheure , 'destinataire' => $destinataire]); 
                    $email = (new TemplatedEmail())
                    ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
                    ->to($destinataire['email'])
                    ->subject($subject)
                    ->html($html);
                    try {
                       if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
                       
                    } catch (TransportExceptionInterface $e) {
                    } 


                }else{
                    $ficheheure->setStatut("2");
                    $ficheheure->setMotif($searchParam['motif']);
                }

               

              }
    
                if( in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles()) ){
                    if($searchParam['statut']=="1"){
                            $ficheheure->setNiveau("ROLE_RH");
                            $ficheheure->setStatut("-1");  

                            $emploiFile = $request->files->get('fileemploi') ; 
                            if($emploiFile){ 
                                if (file_exists($this->getParameter('webroot_doc'). $ficheheure->getPersonnel()->GetNom().'_'.$ficheheure->getPersonnel()->GetPrenom().'/Fiche_Heure/'.$ficheheure->getEmploi()) && $ficheheure->getEmploi()!="" && $ficheheure->getEmploi()!=NULL ){
                                    unlink($this->getParameter('webroot_doc'). $ficheheure->getPersonnel()->GetNom().'_'.$ficheheure->getPersonnel()->GetPrenom().'/Fiche_Heure/'.$ficheheure->getEmploi());
                                }

                
                                $fileUploader = new FileUploader($this->getParameter('webroot_doc'). $ficheheure->getPersonnel()->GetNom().'_'.$ficheheure->getPersonnel()->GetPrenom().'/Fiche_Heure/');
        
                                $emploiName = $fileUploader->upload($emploiFile);
                                $ficheheure->setEmploi($emploiName);
                            }else{
                                return new JsonResponse("0");                 
                            }

                    $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
                    $subject = "Traitement de Fiche-Heure";
                    $html = $this->renderView('ficheheure/email-ficheheure-notif.html.twig',['ficheheure'  => $ficheheure ,'destinataire' => $destinataire]); 
                    $email = (new TemplatedEmail())
                    ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
                    ->to($destinataire['email'])
                    ->subject($subject)
                    ->html($html);
                    try {
                       if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
                    } catch (TransportExceptionInterface $e) {
                    } 
                 
                            
                    }else{
                        $ficheheure->setStatut("2");
                        $ficheheure->setMotif($searchParam['motif']);
                    }
                       
                    
           
                }
    
            

          

        }

        $HistoDemandes = new HistoDemandes();
        $HistoDemandes->setTypeDemande('fiche heure') ; 
        $HistoDemandes->setDateValidation(new \DateTime()) ;
        $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
        $HistoDemandes->setStatut($ficheheure->getStatut()) ;
        $HistoDemandes->setNiveau($ficheheure->getNiveau()) ;
        $HistoDemandes->setDemandeur($ficheheure->getPersonnel()) ;
        $HistoDemandes->setDateEnvoie($ficheheure->getDateEnvoie()) ;
        $date_reprise = $ficheheure->getMoisFin();
        $HistoDemandes->setDateReprise($date_reprise->modify('+1 day'));
        $HistoDemandes->setIdDemande($ficheheure->getId()) ;

        $HistoDemandesRepository->save($HistoDemandes, true);
        $ficheheureRepository->save($ficheheure, true);

        return new RedirectResponse($this->generateUrl('app_ficheheure_index'));
    }




    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_RH') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/ficheheure_{id}_{_token}', name: 'app_ficheheure_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Ficheheure $ficheheure, FicheheureRepository $ficheheureRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ficheheure->getId(), $_token)) {

            $em = $this->getDoctrine()->getManager();
            $histo_auto = $em->getRepository(HistoDemandes::class)->findBy(['id_demande' => strval($ficheheure->getId()),"type_demande"=>"fiche heure"]);
            foreach ($histo_auto as $auto) {$em->remove($auto);}
            $em->flush();

            $ficheheureRepository->remove($ficheheure, true);
        }
        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
        return $this->redirectToRoute('app_ficheheure_new', [], Response::HTTP_SEE_OTHER);
    }


    

    #[Route('/ficheheures_bloque_{id}', name: 'app_ficheheures_bloque', methods: ['GET','POST'])]
    public function bloque($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Ficheheure::class)->bloque_ficheheure($id);
        return new JsonResponse('1');
    }

    #[Route('/ficheheures_debloque_{id}', name: 'app_ficheheures_debloque', methods: ['GET','POST'])]
    public function debloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Ficheheure::class)->debloque_ficheheure($id);
        return new JsonResponse('0');
    }



    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_RH') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/ficheheurePdf_{id}', name: 'ficheheurePdf', methods: ['GET', 'POST'])]
    public function ficheheure_pdf(Pdf $knpSnappyPdf , Request $request  , Ficheheure $ficheheure , KernelInterface $appKernel)
    { 
        $em = $this->getDoctrine()->getManager();
        $filename = 'fiche heure N.'.$ficheheure->GetId().' '. $ficheheure->getPersonnel()->GetNom() ." ".$ficheheure->getPersonnel()->GetPrenom().'.pdf';

        $mois = null;
        $minutes = null;
        $array_fh_mois =  $this->calc_heure_par_mois_fh($ficheheure) ;

        list($mois,$minutes) = $array_fh_mois;

        foreach ($mois as $key => $value) { 

            switch ($value) {

                case '1':
                    $mois[$key] = 'يناير' ;
                    break;
                case '2':
                    $mois[$key] = 'فبراير' ;
                    break;
                case '3':
                    $mois[$key] = 'مارس' ;
                    break;
                case '4':
                    $mois[$key] = 'أبريل' ;
                    break;
                case '5':
                    $mois[$key] = 'ماي' ;
                    break;
                case '6':
                    $mois[$key] = 'يونيو' ;
                    break;
                case '7':
                    $mois[$key] = 'يوليوز' ;
                    break;
                case '8':
                    $mois[$key] = 'غشت' ;
                    break;
                case '9':
                    $mois[$key] = 'سبتمبر' ;
                    break;
                case '10':
                    $mois[$key] = 'أكتوبر' ;
                    break;
                case '11':
                    $mois[$key] = 'نوفمبر' ;
                    break;
                case '12':
                    $mois[$key] = 'ديسمبر' ;
                    break;
                               
              }

        }
       

        $html = $this->renderView('document/ficheheure.html.twig', [
        'ficheheure' => $ficheheure,
        'mois'=>$mois, 
        'minutes'=>$minutes, 
          ]);
       
       
        $dir = $this->getParameter('webroot_doc'). $ficheheure->getPersonnel()->GetNom().'_'.$ficheheure->getPersonnel()->GetPrenom().'/Fiche_Heure/' ;
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
     * @Route("/fh_loop/{id}", name="app_fh_loop")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_RH')")
     */
    public function fh_loop($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $fh = $em->getRepository(Ficheheure::class)->findOneBy(array("id" => $id));
   
        return $this->renderForm('ficheheure/hist-fh-loop.html.twig',[
            'fh' => $fh,
        ]);

    }

 




    
}

