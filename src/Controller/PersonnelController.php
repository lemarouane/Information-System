<?php

namespace App\Controller;

use App\Entity\Personnel;
use App\Entity\TypePersonnel;
use App\Entity\Conge;
use App\Entity\Autorisation;
use App\Entity\OrdreMission;
use App\Entity\Attestation;
use App\Entity\Ficheheure;
use App\Entity\HistoDemandes;
use App\Entity\NoteFonctionnaire;
use App\Entity\Utilisateurs;
use App\Form\PersonnelType;
use App\Repository\PersonnelRepository;
use App\Entity\Avancement;
use App\Entity\EchelonAv;
use App\Entity\Echelon;
use App\Entity\Etudiant\Etudiants;
use App\Entity\Etudiant\Stage;
use App\Entity\GradeAv;
use App\Repository\AvancementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse ;
use App\Service\FileUploader;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use App\Twig\ConfigExtension;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Knp\Snappy\Pdf;

use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use \Datetime;
use App\Service\InternetTest;



class PersonnelController extends AbstractController
{
    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/personnel', name: 'app_personnel_index', methods: ['POST','GET'])]
    public function index(PersonnelRepository $personnelRepository): Response
    {
        return $this->render('personnel/table-datatable-personnel.html.twig', [
            'personnels' => $personnelRepository->findAll(),
        ]);
    }





    #[Route('/proposition_avanc', name: 'proposition_avanc', methods: ['POST','GET'])]
    public function proposition_avanc(): Response
    {
        $etatAvancement =  array();
        $etatAvancementG =  array();
        $em = $this->getDoctrine()->getManager();
        $personnels =$em->getRepository(Personnel::class)->findAll();

        foreach ($personnels as $persone) {
            $avancement=$persone->getAvancements();
           
			if(!empty($avancement->last())){
				$etatproposer =$em->getRepository(EchelonAv::class)->findOneBy(array('etatActuel' => $avancement->last()->getEchelon()));

				$etatproposerGrade=$em->getRepository(GradeAv::class)->findOneBy(array('etatActuel' => $avancement->last()->getGrade()));

				if($etatproposer){
					$d1=$avancement->last()->getDateDeci()->format('Y-m-d');
					$d2= new \Datetime('now');
					$d2= $d2->format('Y-m-d');
					$diff = abs(strtotime($d2) - strtotime($d1));

					$years = $diff / (365*60*60*24);
					if($etatproposer->getRapide() <= floor($years*12)){
						
						array_push($etatAvancement, $avancement->last());
					}  
				}
				if($etatproposerGrade){
					$echellon = explode("-", $avancement->last()->getEchelon()->getDesignation());

					if($avancement->last()->getDateGrade()){
						$dG1=$avancement->last()->getDateGrade()->format('Y-m-d');
						$dGP1=$avancement->last()->getDateDeci()->format('Y-m-d');

						$dG2= new \Datetime('now');
						$dG2= $dG2->format('Y-m-d');

						$diffG = abs(strtotime($dG2) - strtotime($dG1));
						$diffGP = abs(strtotime($dG2) - strtotime($dGP1));

						$yearsG = $diffG / (365*60*60*24);
						$yearsGP = $diffGP / (365*60*60*24);
						
						if($avancement->last()->getPersonnel()->getTypePersonnelId()->getId()==1 || $avancement->last()->getPersonnel()->getTypePersonnelId()->getId()==3){

							if(($echellon[1]==3) && (floor($yearsGP)>=2) 
								|| ($echellon[1]==4) ){
                                
								array_push($etatAvancementG, $avancement->last());
							}
						}elseif($etatproposerGrade->getRapide() <= ceil($yearsG)){

								array_push($etatAvancementG, $avancement->last());
						}
							
						
					}
					
				}
            }  
        }




        return $this->render('personnel/prop-avanc-personnel.html.twig', [
            'etatAvancement' => $etatAvancement,
            'etatAvancementG' => $etatAvancementG,
        ]);
    }






    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR')  or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/stats_personnel', name: 'app_stats_personnel_index', methods: ['POST','GET'])]
    public function stats_index(PersonnelRepository $personnelRepository): Response
    {
        return $this->render('stats_personnel/dashboard.html.twig', [
            'personnels' => $personnelRepository->findAll(),
        ]);
    }

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/personnel_new', name: 'app_personnel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PersonnelRepository $personnelRepository , FileUploader $fileUploader ,  MailerInterface $mailer  , InternetTest $int ,  UserPasswordEncoderInterface $encoder): Response
    {
        $personnel = new Personnel();
        
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

            $utilisateur = new Utilisateurs();
            $utilisateur->setEmail($form['email']->getData()) ;
            $password =  str_shuffle('abcdef123456');
            $encoded = $encoder->encodePassword($utilisateur, $password);
            $utilisateur->setPassword($encoded);
            $utilisateur->setNomUtilisateur($form['email']->getData());
            $utilisateur->setLocale('fr-FR');
            $utilisateur->setEnable(1);
            $personnel->setImageName("default.png");

            $image = $form->get('imageFile')->getData();

        if(!empty($image)){
            $imageName = $fileUploader->upload($image);
            $personnel->setImageName($imageName);
        }else{
            $personnel->setImageName('default.png'); 
        }

         if( $personnel->getTypePersonnelId()->getId()==2 || $personnel->getTypePersonnelId()->getId()==4 ){
            $utilisateur->setRoles(array("ROLE_FONC")); 
         }else{
            $utilisateur->setRoles(array("ROLE_PROF")); 
         }
        
            $personnel->setIdUser($utilisateur);
            $personnelRepository->save($personnel, true);

            $html=$this->renderView('personnel/creation-personnel-email.html.twig',array(
                'personnel' => $personnel , 'password' =>$password , 'email'=> $utilisateur->getEmail() ));
            
            $message = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-ENSAT'))
                ->to($utilisateur->getEmail())
                ->subject('Création de votre compte E-ENSAT')
                ->html($html)
                ;
            try {
                 if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
              // dd($html);
            } catch (TransportExceptionInterface $e) {
            
            }
 
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS ");
            return $this->redirectToRoute('app_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('personnel/new-personnel.html.twig', [
            'personnel' => $personnel,
            'form' => $form,
        ]);
    }


/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/personnel_{id}_show', name: 'app_personnel_show', methods: ['POST','GET'])]
    public function show(Personnel $personnel, AvancementRepository $avancementRepository ,$id ): Response
    {
        $avancements =  $avancementRepository->findBy(['personnel'=>$id]);

        return $this->render('personnel/show-personnel.html.twig', [
            'personnel' => $personnel,
            'avancements' => $avancements,
        ]);
    } 
/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/personnel_{id}_edit', name: 'app_personnel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Personnel $personnel, PersonnelRepository $personnelRepository , FileUploader $fileUploader) : Response
    {
        $form = $this->createForm(PersonnelType::class, $personnel);

        $form['email']->setData($personnel->getIdUser()->getEmail()) ; ////

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

            $personnel->getIdUser()->setEmail($form['email']->getData()) ; ////

            $image = $form->get('imageFile')->getData();
            if(!empty($image)){
                $imageName = $fileUploader->upload($image);
                $personnel->setImageName($imageName);
            }
 
            $personnelRepository->save($personnel, true);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

            return $this->redirectToRoute('app_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('personnel/edit-personnel.html.twig', [
            'personnel' => $personnel,
            'form' => $form,
        ]);
    }





    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/personnel_{id}_{_token}', name: 'app_personnel_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Personnel $personnel, PersonnelRepository $personnelRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personnel->getId(), $_token )) {
            $personnelRepository->remove($personnel, true);
        }

        return $this->redirectToRoute('app_personnel_index', [], Response::HTTP_SEE_OTHER);
    }

 /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */

    #[Route('/statistiqueRH', name: 'statistiqueRH', methods: ['GET', 'POST'])]
    public function statistiqueAction()
    {
        $em = $this->getDoctrine()->getManager();
        $femme = $em->getRepository(Personnel::class)->findby(array('genre' => 'F'));
        $homme = $em->getRepository(Personnel::class)->findby(array('genre' => 'M'));
        $prof = $em->getRepository(Personnel::class)->PersonnelsNbProf();
        $admin = $em->getRepository(Personnel::class)->PersonnelsNbAd();
        $effectifParDep =$em->getRepository(Personnel::class)->PersonnelsParDepartement();
        $repCorpsEnseignant =$em->getRepository(Personnel::class)->PersonnelsParCorpsEnseignant();
        $effectifParService =$em->getRepository(Personnel::class)->PersonnelsParService();
        $repCorpsAdmin =$em->getRepository(Personnel::class)->PersonnelsParCorpsAdmin();
        return $this->render('statistique/statistiqueRH.html.twig', array(
            'femme'      => count($femme),
            'homme'        => count($homme),
            'prof'      => $prof[0]['nb'],
            'admin'        => $admin[0]['nb'],
            'effectifParDep' => $effectifParDep,
            'repCorpsEnseignant' => $repCorpsEnseignant,
            'effectifParService' => $effectifParService,
            'repCorpsAdmin' => $repCorpsAdmin,
        ));


    }

  /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/recrutementDatePr', name: 'recrutementDatePr', methods: ['GET', 'POST'])]
    public function getAllrecrueAnneePrAction() {

        $em = $this->getDoctrine()->getManager();
        $nbrecrueAnnee =$em->getRepository(Personnel::class)->PersonnelsNbYearProf();
        return new JsonResponse($nbrecrueAnnee);
    }

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/recrutementDateAd', name: 'recrutementDateAd', methods: ['GET', 'POST'])]
    public function getAllrecrueAnneeAdAction() {

        $em = $this->getDoctrine()->getManager();
        $nbrecrueAnnee =$em->getRepository(Personnel::class)->PersonnelsNbYearAd();
        return new JsonResponse($nbrecrueAnnee);
    }



/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/effectifParDep', name: 'effectifParDep', methods: ['GET', 'POST'])]
    public function getEffectifParDepAction() {

        $em = $this->getDoctrine()->getManager();
        $effectifParDep =$em->getRepository(Personnel::class)->PersonnelsParDepartement();
        return new JsonResponse($effectifParDep);
    }

/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/effectifParType', name: 'effectifParType', methods: ['GET', 'POST'])]
    public function getEffectifParTypeAction() {

        $em = $this->getDoctrine()->getManager();
        $effectifParType =$em->getRepository(Personnel::class)->PersonnelsParType();
        return new JsonResponse($effectifParType);
    }
/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/effectifParGenre', name: 'effectifParGenre', methods: ['GET', 'POST'])]
    public function getEffectifParGenreAction() {

        $em = $this->getDoctrine()->getManager();
        $effectifParGenre =$em->getRepository(Personnel::class)->PersonnelsParGenre();
        return new JsonResponse($effectifParGenre);
    }
/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/effectifParActivite', name: 'effectifParActivite', methods: ['GET', 'POST'])]
    public function getEffectifParActiviteAction() {

        $em = $this->getDoctrine()->getManager();
        $effectifParActivite =$em->getRepository(Personnel::class)->PersonnelsParActivite();
        return new JsonResponse($effectifParActivite);
    }

 /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/effectifParService', name: 'effectifParService', methods: ['GET', 'POST'])]
    public function getEffectifParServiceAction() {

        $em = $this->getDoctrine()->getManager();
        $effectifParService =$em->getRepository(Personnel::class)->PersonnelsParService();
        return new JsonResponse($effectifParService);
    }

   /**
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/repCorpsEnseignant', name: 'repCorpsEnseignant', methods: ['GET', 'POST'])]
    public function repCorpsEnseignantAction() {

        $em = $this->getDoctrine()->getManager();
        $repCorpsEnseignant =$em->getRepository(Personnel::class)->PersonnelsParCorpsEnseignant();
        return new JsonResponse($repCorpsEnseignant);
    }
/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_CHEF_SERV') ")
     */
    #[Route('/repCorpsAdmin', name: 'repCorpsAdmin', methods: ['GET', 'POST'])]
    public function repCorpsAdminAction() {

        $em = $this->getDoctrine()->getManager();
        $repCorpsAdmin =$em->getRepository(Personnel::class)->PersonnelsParCorpsAdmin();
        return new JsonResponse($repCorpsAdmin);
    }

   /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG')  or is_granted('ROLE_CHEF_SERV')")
     */
    #[Route('/effectifevolution', name: 'effectifevolution', methods: ['GET', 'POST'])]
    public function getEffectifevolutionAction() {

        $em = $this->getDoctrine()->getManager();
        $effectifParDep =$em->getRepository(Personnel::class)->PersonnelsEffectifevolution();
        return new JsonResponse($effectifParDep);
    }




 /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/detail_dem_attest_{id1}_{id2}', name: 'detail_dem_attest', methods: ['GET', 'POST'])]
    public function detail_dem_attest($id1 , $id2) {

        $em = $this->getDoctrine()->getManager();
        $attest  =  $em->getRepository(Attestation::class)->find_by_annee_and_persid($id1,$id2);
        $nom_perso = $attest[0]->getPersonnel()->getNom().' '. $attest[0]->getPersonnel()->getPrenom();

        return $this->render('personnel/details-dem-personnel-attest.html.twig', [
             'attests' => $attest ,
             'nom_perso'=> $nom_perso,
             'annee'=>$id1,
        ]); 
    }

      /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/detail_dem_auto_{id1}_{id2}', name: 'detail_dem_auto', methods: ['GET', 'POST'])]
    public function detail_dem_auto($id1 , $id2) {

        $em = $this->getDoctrine()->getManager();
        $autos  =  $em->getRepository(Autorisation::class)->find_by_annee_and_persid($id1,$id2);
        $nom_perso = $autos[0]->getPersonnel()->getNom().' '. $autos[0]->getPersonnel()->getPrenom();

        return $this->render('personnel/details-dem-personnel-auto.html.twig', [
            'autos' => $autos ,
            'nom_perso'=> $nom_perso,
            'annee'=>$id1,
        ]); 
    }



 /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/detail_dem_conge_{id1}_{id2}', name: 'detail_dem_conge', methods: ['GET', 'POST'])]
    public function detail_dem_conge($id1 , $id2) {

        $em = $this->getDoctrine()->getManager();
        $conge  =  $em->getRepository(Conge::class)->find_by_annee_and_persid($id1,$id2);
        $nom_perso = $conge[0]->getPersonnel()->getNom().' '. $conge[0]->getPersonnel()->getPrenom();

        return $this->render('personnel/details-dem-personnel-conge.html.twig', [
             'conges' => $conge ,
             'nom_perso'=> $nom_perso,
             'annee'=>$id1,
        ]); 
    }


 /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN')  ")
     */
    #[Route('/detail_dem_om_{id1}_{id2}', name: 'detail_dem_om', methods: ['GET', 'POST'])]
    public function detail_dem_om($id1 , $id2) {

        $em = $this->getDoctrine()->getManager();
        $oms  =  $em->getRepository(OrdreMission::class)->find_by_annee_and_persid($id1,$id2);
        $nom_perso = $oms[0]->getPersonnel()->getNom().' '. $oms[0]->getPersonnel()->getPrenom();

        return $this->render('personnel/details-dem-personnel-om.html.twig', [
             'oms' => $oms ,
             'nom_perso'=> $nom_perso,
             'annee'=>$id1,
        ]); 
    }

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN')  ")
     */
    #[Route('/detail_dem_fh_{id1}_{id2}', name: 'detail_dem_fh', methods: ['GET', 'POST'])]
    public function detail_dem_fh($id1 , $id2) {

        $em = $this->getDoctrine()->getManager();
        $fh  =  $em->getRepository(Ficheheure::class)->find_by_annee_and_persid($id1,$id2);
       
        $nom_perso = $fh[0]->getPersonnel()->getNom().' '. $fh[0]->getPersonnel()->getPrenom();

        return $this->render('personnel/details-dem-personnel-fh.html.twig', [
             'fhs' => $fh ,
             'nom_perso'=> $nom_perso,
             'annee'=>$id1,
        ]); 
    }



    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/stats_dem_personnel_{id}', name: 'stats_dem_personnel', methods: ['GET', 'POST'])]
    public function stats_dem_personnel($id , PersonnelRepository $personnelRepository) {

        $personnel = $personnelRepository->findOneBy(['id'=>$id]);

        return $this->render('personnel/stats-personnel.html.twig', [
            'personnel' => $personnel,
        ]); 
    }

     /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN')  ")
     */
    #[Route('/annee_service_personnel_{id}', name: 'annee_service_personnel', methods: ['GET', 'POST'])]
    public function annee_service_personnel($id , PersonnelRepository $personnelRepository) {

        $annees_interval = [];

        $count_att = [];
        $count_conge = [];
        $count_auto = [];
        $count_om = [];
        $count_fh = [];
        $result_array = [];

        $personnel = $personnelRepository->findOneBy(['id'=>$id]);
        
        $d1 = $personnel->getDateAffectationENSAT();
        $d2 =  new DateTime();
        $diff = $d2->diff($d1);
        $annees =  $diff->y;
        $start_annee =  ($d2->format('Y') - $annees)-1;
       
        for ($i=-1; $i <= $annees ; $i++) { 
      
            array_push($annees_interval,$start_annee);
            $start_annee++;
        }

        $em = $this->getDoctrine()->getManager();



        if( $personnel->getTypePersonnelId()->getId()==2){

            $count_att  =  $em->getRepository(Attestation::class)->count_by_annee($annees_interval,$personnel->getId());
            $count_conge  =  $em->getRepository(Conge::class)->count_by_annee($annees_interval,$personnel->getId());
            $count_auto  =  $em->getRepository(Autorisation::class)->count_by_annee($annees_interval,$personnel->getId());
            $count_om  =  $em->getRepository(OrdreMission::class)->count_by_annee($annees_interval,$personnel->getId());
    
            array_push($result_array,$annees_interval);
            array_push($result_array,$count_att);
            array_push($result_array,$count_conge);
            array_push($result_array,$count_auto);
            array_push($result_array,$count_om);
        }else{

            $count_att  =  $em->getRepository(Attestation::class)->count_by_annee($annees_interval,$personnel->getId());
            $count_fh  =  $em->getRepository(Ficheheure::class)->count_by_annee($annees_interval,$personnel->getId());
            $count_om  =  $em->getRepository(OrdreMission::class)->count_by_annee($annees_interval,$personnel->getId());

            array_push($result_array,$annees_interval);
            array_push($result_array,$count_att);
            array_push($result_array,$count_fh);
            array_push($result_array,$count_om);

        }

        return new JsonResponse($result_array);
       
    }

    





















/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') ")
     */
    #[Route('/attestationTravailPdf_{id}', name: 'attestationTravailPdf', methods: ['GET', 'POST'])]
    public function attest_pdf(Pdf $knpSnappyPdf , Personnel $personnel )
    {
      
        $html = $this->renderView('document/attestation.html.twig', [
            'personnel' => $personnel,
        ]);

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'Att-Travail '. $personnel->GetNom() ." ".$personnel->GetPrenom().'.pdf' ,
        );
    }
  
 

  
    
   
    



    #[Route('/app_counter', name: 'app_counter', methods: ['POST'])]
    public function app_counter(secure $security ) {
        $result = [];
        $em = $this->getDoctrine()->getManager();
        $em1 = $this->getDoctrine()->getManager('etudiant');
        $validateur_codes = $security->getUser()->getCodes();
        $validateur_roles = $security->getUser()->getRoles() ;

       
      // $ordremissions=$em->getRepository(OrdreMission::class)->searchDemandesByService($validateur_codes,$validateur_roles);
       $attestations = [];
       $ordremissions = [];
       $ordremissions4 = [];
       $ordremissions1 = [];
       $conges = [];
       $conges1 = [];
       $autorisations = [];
       $autorisations1 = [];
       $reprises = [];
       $reprises_rh = [];
       $ficheheures = [];


        if( in_array("ROLE_CHEF_DEP",$validateur_roles) ){
            $ordremissions=$em->getRepository(OrdreMission::class)->searchDemandesByDep($validateur_codes,$validateur_roles);
            $ficheheures=$em->getRepository(Ficheheure::class)->searchDemandesByDep($validateur_codes,$validateur_roles);
            $result["ordremission"] =count($ordremissions)+count($ordremissions4);
            $result["ficheheures"] = count($ficheheures);


            //array_push($result ,count($ordremissions) ,  count($ficheheures));
        }
   
        if( in_array("ROLE_CHEF_STRUCT",$validateur_roles) ){
            $ordremissions=$em->getRepository(OrdreMission::class)->searchDemandesByLab($validateur_codes,$validateur_roles);
            $result["ordremission"] =count($ordremissions)+count($ordremissions4);
           // array_push($result ,count($ordremissions) );
        }
        if(in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles())){
            $autorisations=$em->getRepository(Autorisation::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $conges=$em->getRepository(Conge::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $ordremissions4=$em->getRepository(OrdreMission::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $personel_service_ids =  $em->getRepository(HistoDemandes::class)->Get_Fonc_By_Service($security->getUser()->getPersonnel()->getServiceAffectationId()->getId());
            $reprises  =  $em->getRepository(HistoDemandes::class)->Histo_Demandes_Reprises($personel_service_ids , $security->getUser()->getPersonnel()->getId());
            $result["ordremission"] = count($ordremissions)+count($ordremissions4);
            $result["autorisations"] =count($autorisations);
            $result["conges"] = count($conges);
            $result["reprises"] = count($reprises);
          //  array_push($result , count($autorisations),count($conges),count($ordremissions) , count($reprises) );
        }
    
        if(in_array("ROLE_SG",$security->getUser()->getRoles())){
            $personel_service_ids =  $em->getRepository(HistoDemandes::class)->Get_Fonc_By_Services_SG();
            $ordremissions4=$em->getRepository(OrdreMission::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $conges=$em->getRepository(Conge::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $autorisations=$em->getRepository(Autorisation::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $reprises  = $em->getRepository(HistoDemandes::class)->Histo_Demandes_Reprises($personel_service_ids , $security->getUser()->getPersonnel()->getId());
            $result["ordremission"] =count($ordremissions)+count($ordremissions4);
            $result["conges"] = count($conges);
            $result["autorisations"] =count($autorisations);
            $result["reprises"] = count($reprises);
           // array_push($result , count($ordremissions) , count($reprises) );
        }


        if(in_array("ROLE_DIR",$security->getUser()->getRoles())){
           // $personel_service_ids =  $em->getRepository(HistoDemandes::class)->Get_Fonc_By_Services_SG();
            $ordremissions4=$em->getRepository(OrdreMission::class)->findby(array('statut' => -1 ,'niveau'=>'ROLE_DIR'));
            $conges=$em->getRepository(Conge::class)->findby(array('statut' => -1 ,'niveau'=>'ROLE_DIR'));;
            $autorisations=$em->getRepository(Autorisation::class)->findby(array('statut' => -1 ,'niveau'=>'ROLE_DIR'));
           // $reprises  = $em->getRepository(HistoDemandes::class)->Histo_Demandes_Reprises($personel_service_ids , $security->getUser()->getPersonnel()->getId());
            $result["ordremission"] =count($ordremissions)+count($ordremissions4);
            $result["conges"] = count($conges);
            $result["autorisations"] =count($autorisations);
           // $result["reprises"] = count($reprises);
           // array_push($result , count($ordremissions) , count($reprises) );
        }
    
        if(in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles())){
            $personel_service_ids =  $em->getRepository(HistoDemandes::class)->Get_Fonc_By_Services_DirAdj($security->getUser()->getCodes());
            $ordremissions4=$em->getRepository(OrdreMission::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $conges=$em->getRepository(Conge::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $autorisations=$em->getRepository(Autorisation::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $reprises  =  $em->getRepository(HistoDemandes::class)->Histo_Demandes_Reprises($personel_service_ids , $security->getUser()->getPersonnel()->getId());
            if(in_array("DIR_1",$security->getUser()->getCodes())){
                $ficheheures=$em->getRepository(Ficheheure::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            }
            $result["ordremission"] =count($ordremissions)+count($ordremissions4);
            $result["conges"] = count($conges);
            $result["autorisations"] =count($autorisations);
            $result["ficheheures"] = count($ficheheures);
            
                if($reprises!=null){
                    $result["reprises"] = count($reprises);
                }else{
                    $result["reprises"] = 0;
                }
                        

           // array_push($result , count($ordremissions) , count($reprises) , count($ficheheures));
         
        }
        if (in_array("ROLE_RH", $validateur_roles)){
            $autorisations=$em->getRepository(Autorisation::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $conges=$em->getRepository(Conge::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $attestations = $em->getRepository(Attestation::class)->findBy(['statut'=>"-1"]);
            $ficheheures=$em->getRepository(Ficheheure::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $ordremissions4=$em->getRepository(OrdreMission::class)->searchDemandesByService($validateur_codes,$validateur_roles);
            $reprises_rh=$em->getRepository(HistoDemandes::class)->Histo_Demandes_Reprises_RH();
            $result["ordremission"] = count($ordremissions)+count($ordremissions4);
            $result["ficheheures"] = count($ficheheures);
            $result["autorisations"] =count($autorisations);
            $result["conges"] = count($conges);
            $result["attestations"] = count($attestations);
            $result["reprises_rh"] = count($reprises_rh);
           // array_push($result , count($autorisations),count($conges),count($attestations),count($ordremissions) ,count($ficheheures));
        }
        if(in_array("ROLE_ADMIN", $validateur_roles)){
            $attestations = $em->getRepository(Attestation::class)->findBy(['statut'=>"-1"]);
            $ficheheures=$em->getRepository(Ficheheure::class)->findBy(['statut'=>"-1"]);
            $ordremissions1=$em->getRepository(OrdreMission::class)->findBy(['statut'=>"-1"]);
            $autorisations1=$em->getRepository(Autorisation::class)->findBy(['statut'=>"-1"]);
            $conges1=$em->getRepository(Conge::class)->findBy(['statut'=>"-1"]);
            $result["ordremission"] =count($ordremissions1);
            $result["ficheheures"] = count($ficheheures);
            $result["autorisations"] =count($autorisations1);
            $result["conges"] = count($conges1);
            $result["reprises"] = count($reprises);
            $result["attestations"] = count($attestations);

           // array_push($result , count($autorisations),count($conges),count($attestations),count($ordremissions) , count($reprises) , count($ficheheures));
        }if(in_array("ROLE_CHEF_FIL", $validateur_roles)){
            $codes=array();
            $usr = $security->getUser();
            foreach($usr->getCodes() as $code){
                if(strpos($code, 'FIL') !== false){
                    $code=explode('_',$code);
                    
                    array_push($codes,$code[1]);
                }
                
            }
            $config = new \Doctrine\DBAL\Configuration();
		    $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
            $anneeUniversitaire=$em1->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        
            
            $convention = $em1->getRepository(Stage::class)->searchByFiliere($anneeUniversitaire['COD_ANU'],$codes);
            $result["convention"] =count($convention);

           // array_push($result , count($convention));
        }
        
       // $counter_attestation = count($autorisations=$em->getRepository(Autorisation::class)->searchDemandesByService($validateur_codes,$validateur_roles,$codes[0]));
       
        return new JsonResponse($result);
    }


    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/export_stats_rh', name: 'export_stats_rh', methods: ['POST','GET'])]
    public function exporterStatAction(Request $request) {

        $spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Statistiques RH");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getColumnDimension('B')->setWidth(20);
        $worksheet->getColumnDimension('I')->setWidth(20);
        $worksheet->getColumnDimension('K')->setWidth(15);
        //Rename sheet
        $worksheet->setTitle('Statistiques');

        /*
        * TITLE
        */
        //Set style Title
        $styleArrayTitle = array(
            'font' => array(
                'color' => array('rgb' => '161617'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );
        //Set style Title
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '161617'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );
        $styleArrayTitle1 = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );
        $styleArrayTitle2 = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => 'd2e0e0')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );
        $worksheet->getColumnDimension('H')->setWidth(45);      
        $worksheet->getCell('H2')->setValue('       Université Abdelmalek Essaâdi          Ecole Nationale des Sciences Appliquées Tanger ');
        $worksheet->getStyle('H2')->getAlignment()->setWrapText(true);
        $worksheet->getStyle('H2')->applyFromArray($styleArray);

        $worksheet->mergeCells("B4:G4");
        $worksheet->getCell('B4')->setValue('Répartition du corps enseignant par Département:');
        $worksheet->mergeCells("I4:N4");
        $worksheet->getCell('I4')->setValue('Répartition du Corps enseignant par grade:');
        $worksheet->getStyle('B4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('I4')->applyFromArray($styleArrayTitle1);

        $worksheet->getStyle('B4:G4')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
        $worksheet->getStyle('I4:N4')
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('09594C');

        $worksheet->getStyle('B7:G7')
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('d2e0e0');
        $worksheet->getStyle('I7:N7')
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('d2e0e0');

        $worksheet->mergeCells("B7:D7");
        $worksheet->getCell('B7')->setValue('Département');
        $worksheet->mergeCells("E7:G7");
        $worksheet->getCell('E7')->setValue('Effectifs');

        $worksheet->mergeCells("I7:K7");
        $worksheet->getCell('I7')->setValue('Grade');
        $worksheet->mergeCells("L7:N7");
        $worksheet->getCell('L7')->setValue('Effectifs');

        $worksheet->getStyle('B7:G7')->applyFromArray($styleArrayTitle2);

        $worksheet->getStyle('I7:N7')->applyFromArray($styleArrayTitle2);
        $i=8;
        $j=8;
        $effectifParDep =$em->getRepository(Personnel::class)->PersonnelsParDepartement();
        $repCorpsEnseignant =$em->getRepository(Personnel::class)->PersonnelsParCorpsEnseignant();

        foreach ($effectifParDep as $dep) {
            $worksheet->mergeCells("B".$i.":D".$i);
            $worksheet->mergeCells("E".$i.":G".$i);
            $worksheet->getStyle("B".$i.":G".$i)->applyFromArray($styleArrayTitle);
            $worksheet->getCell('B'.$i)->setValue($dep['libelle_dep']);
            $worksheet->getCell('E'.$i)->setValue($dep['nb']);
            $i++;
        }
        foreach ($repCorpsEnseignant as $eff) {
            $worksheet->getStyle("I".$j.":N".$j)->applyFromArray($styleArrayTitle);
            $worksheet->mergeCells("I".$j.":K".$j);
            $worksheet->mergeCells("L".$j.":N".$j);
            $worksheet->getCell('I'.$j)->setValue($eff['designation_fr']);
            $worksheet->getCell('L'.$j)->setValue($eff['nb']);
            $j++;
        }
        $y=max($j,$i);
        $y=$y+3;

        $worksheet->mergeCells("B".$y.":G".$y);
        $worksheet->getCell('B'.$y)->setValue('Répartition du corps administratif par Service:');
        $worksheet->mergeCells("I".$y.":N".$y);
        $worksheet->getCell('I'.$y)->setValue('Répartition du Corps administratif par grade:');
        $worksheet->getStyle('B'.$y)->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('I'.$y)->applyFromArray($styleArrayTitle1);

        $worksheet->getStyle('B'.$y.':G'.$y)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
        $worksheet->getStyle('I'.$y.':N'.$y)
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('09594C');
        $y=$y+3;  
        $worksheet->getStyle('B'.$y.':G'.$y)
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('d2e0e0');
        $worksheet->getStyle('I'.$y.':N'.$y)
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('d2e0e0');

        $worksheet->mergeCells("B".$y.":D".$y);
        $worksheet->getCell('B'.$y)->setValue('Département');
        $worksheet->mergeCells("E".$y.":G".$y);
        $worksheet->getCell('E'.$y)->setValue('Effectifs');

        $worksheet->mergeCells("I".$y.":K".$y);
        $worksheet->getCell('I'.$y)->setValue('Grade');
        $worksheet->mergeCells("L".$y.":N".$y);
        $worksheet->getCell('L'.$y)->setValue('Effectifs');

        $worksheet->getStyle('B'.$y.':G'.$y)->applyFromArray($styleArrayTitle2);

        $worksheet->getStyle('I'.$y.':N'.$y)->applyFromArray($styleArrayTitle2);
        $k=$y+1;
        $l=$y+1;
        $effectifParService =$em->getRepository(Personnel::class)->PersonnelsParService();
        $repCorpsAdmin =$em->getRepository(Personnel::class)->PersonnelsParCorpsAdmin();


        foreach ($effectifParService as $ser) {
            $worksheet->mergeCells("B".$k.":D".$k);
            $worksheet->mergeCells("E".$k.":G".$k);
            $worksheet->getStyle("B".$k.":G".$k)->applyFromArray($styleArrayTitle);
            $worksheet->getCell('B'.$k)->setValue($ser['nom_service']);
            $worksheet->getCell('E'.$k)->setValue($ser['nb']);
            $k++;
        }
        foreach ($repCorpsAdmin as $eff) {
            $worksheet->getStyle("I".$l.":N".$l)->applyFromArray($styleArrayTitle);
            $worksheet->mergeCells("I".$l.":K".$l);
            $worksheet->mergeCells("L".$l.":N".$l);
            $worksheet->getCell('I'.$l)->setValue($eff['designation_fr']);
            $worksheet->getCell('L'.$l)->setValue($eff['nb']);
            $l++;
        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'statistiquesRH.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }






    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/export_personnel_rh', name: 'export_personnel_rh', methods: ['POST','GET'])]

    public function exporterListeAction(Request $request) {

        $spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Liste du Personnel");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        //Rename sheet
        $worksheet->setTitle('Personnel');

        /*
        * TITLE
        */
        //Set style Title
        $styleArrayTitle = array(
            'font' => array(
                'color' => array('rgb' => '161617'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );
        //Set style Title
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '161617'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );
        $styleArrayTitle1 = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );
       
        $worksheet->mergeCells("B2:Z2");   
        $worksheet->getCell('B2')->setValue(' Université Abdelmalek Essaâdi Ecole Nationale des Sciences Appliquées Tanger ');
        $worksheet->getStyle('B2')->getAlignment()->setWrapText(true);
        $worksheet->getStyle('B2')->applyFromArray($styleArray);

        $worksheet->getCell('A4')->setValue('Activité');
        $worksheet->getCell('B4')->setValue('N° P.P.R');
        $worksheet->getCell('C4')->setValue('Nom');
        $worksheet->getCell('D4')->setValue('Prénom');
        $worksheet->getCell('E4')->setValue('الإسم العائلي بالعربية ');
        $worksheet->getCell('F4')->setValue('الإسم الشخصي بالعربية ');
        $worksheet->getCell('G4')->setValue('Sexe');
        $worksheet->getCell('H4')->setValue('Poste Budjétaire');
        $worksheet->getCell('I4')->setValue('Date de Naissance');
        $worksheet->getCell('J4')->setValue('Lieu de Naissance');
        $worksheet->getCell('K4')->setValue('E-mail');
        $worksheet->getCell('L4')->setValue('N° Téléphone');
        $worksheet->getCell('M4')->setValue('N° CIN');
        $worksheet->getCell('N4')->setValue('Date de recrutement');
        $worksheet->getCell('O4')->setValue('Date Affectation au MESRSFC' );
        $worksheet->getCell('P4')->setValue('Date Affectation à l\'enseignement');
        $worksheet->getCell('Q4')->setValue('Date Affectation à l\' ENSAT');
        $worksheet->getCell('R4')->setValue('Coprs');
        $worksheet->getCell('S4')->setValue('Grade');
        $worksheet->getCell('T4')->setValue('date de grade');
        $worksheet->getCell('U4')->setValue('Echellon');
        $worksheet->getCell('V4')->setValue('date echellon');
        $worksheet->getCell('W4')->setValue('Service Affectation');
        $worksheet->getCell('X4')->setValue('Type Personnel' );
        $worksheet->getCell('Y4')->setValue('Département');
        $worksheet->getCell('Z4')->setValue('Laboratoire');

        $worksheet->getStyle('A4:Z4')->applyFromArray($styleArrayTitle1);

        $worksheet->getStyle('A4:Z4')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
       

        $i=5;
        $personnels =$em->getRepository(Personnel::class)->findAll();

        foreach ($personnels as $personne) {
            $avancement=$personne->getAvancements()->last();

            $worksheet->getStyle("A".$i.":Z".$i)->applyFromArray($styleArrayTitle);

      
            $activite = "" ;

            switch ($personne->getActivite()) {
                case "A":
                    $activite = "Abondon";
                break;
                case "N":
                    $activite = "Normale";
                break;
                case "M":
                    $activite = "Mutation";
                break;
                case "D":
                    $activite = "Décés";
                break;
                case "R":
                    $activite = "Retraité";
                break;
                
            }
            
  

            $worksheet->getCell('A'.$i)->setValue($activite);



            $worksheet->getCell('B'.$i)->setValue($personne->getNumPPR());
            $worksheet->getCell('C'.$i)->setValue($personne->getNom());
            $worksheet->getCell('D'.$i)->setValue($personne->getPrenom());
            $worksheet->getCell('E'.$i)->setValue($personne->getNomArabe());
            $worksheet->getCell('F'.$i)->setValue($personne->getPrenomArabe());
            $worksheet->getCell('G'.$i)->setValue($personne->getGenre());
            $worksheet->getCell('H'.$i)->setValue($personne->getPosteBudget());
            $worksheet->getCell('I'.$i)->setValue($personne->getDatenaissance() ? $personne->getDatenaissance()->format('Y-m-d') : ' ');
            $worksheet->getCell('J'.$i)->setValue($personne->getProvinceNaissance()->getNomProvince());
            $worksheet->getCell('K'.$i)->setValue($personne->getIdUser()->getEmail());
            $worksheet->getCell('L'.$i)->setValue($personne->getTel());
            $worksheet->getCell('M'.$i)->setValue($personne->getCin());
            $worksheet->getCell('N'.$i)->setValue($personne->getDateRecrutement() ? $personne->getDateRecrutement()->format('Y-m-d') : ' ');
            $worksheet->getCell('O'.$i)->setValue($personne->getDateAffectationMESRSFC() ? $personne->getDateAffectationMESRSFC()->format('Y-m-d') : ' ');
            $worksheet->getCell('P'.$i)->setValue($personne->getDateAffectationEnseignement() ? $personne->getDateAffectationEnseignement()->format('Y-m-d') : ' ');
            $worksheet->getCell('Q'.$i)->setValue($personne->getDateAffectationENSAT() ? $personne->getDateAffectationENSAT()->format('Y-m-d') : ' ');
            $worksheet->getCell('R'.$i)->setValue($personne->getCorpsId()->getDesignationFR());
			if($avancement){
				$worksheet->getCell('S'.$i)->setValue($avancement->getGrade() ? $avancement->getGrade()->getDesignationFR() : ' ');
				$worksheet->getCell('T'.$i)->setValue($avancement->getDateGrade() ? $avancement->getDateGrade()->format('Y-m-d') : ' ');
				$worksheet->getCell('U'.$i)->setValue($avancement->getEchelon()->getDesignation());
				$worksheet->getCell('V'.$i)->setValue($avancement->getDateDeci() ? $avancement->getDateDeci()->format('Y-m-d') : ' ');
			}else{
				$worksheet->getCell('S'.$i)->setValue(' ');
				$worksheet->getCell('T'.$i)->setValue(' ');
				$worksheet->getCell('U'.$i)->setValue(' ');
				$worksheet->getCell('V'.$i)->setValue(' ');
			}
            $worksheet->getCell('W'.$i)->setValue($personne->getServiceAffectationId()->getNomService());
            $worksheet->getCell('X'.$i)->setValue($personne->getTypePersonnelId()->getLibellePersonnel());
            $worksheet->getCell('Y'.$i)->setValue($personne->getDepartementId()->getLibelleDep());
            $worksheet->getCell('Z'.$i)->setValue($personne->getStructureRech() ? $personne->getStructureRech()->getLibelleStructure() : ' ');

            $i++;
        }
       


        

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'Liste_personnel_ENSAT.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }





    /**
     *
     * @Security(" is_granted('ROLE_RH')  ")
     */
    #[Route('/exporterFiche_prop/{id}', name: 'exporterFiche_prop', methods: ['POST','GET'])]
    public function exporterFiche1ActionProp(Request $request, Pdf $knpSnappyPdf , $id ) {

        $em = $this->getDoctrine()->getManager();
        $searchParam = $request->get('importFiche');
        $idAv="";

          if($id!=null){
            $idAv=$id;
        }else{
            $idAv=$searchParam['numpprFiche'];
        }

       // $idAv=$searchParam['numpprFiche'];

        $personneAv =$em->getRepository(Personnel::class)->findOneBy(array('numPPR' => $idAv ));

        $avancement=$personneAv->getAvancements();

        $etatproposer =$em->getRepository(EchelonAv::class)->findOneBy(array('etatActuel' => $avancement->last()->getEchelon()));

        $etatproposerGrade =$em->getRepository(GradeAv::class)->findOneBy(array('etatActuel' => $avancement->last()->getGrade()));


        $etat=null;
        $d1=$avancement->last()->getDateDeci()->format('Y-m-d');
        $d2= new \Datetime('now');
        $d2= $d2->format('Y-m-d');

       


        $diff = abs(strtotime($d2) - strtotime($d1));

        $years = $diff / (365*60*60*24);
        if($etatproposer){
            if($etatproposer->getRapide() <= floor($years*12)){
                $etat = $etatproposer;
            }else{
                $etat=null;
            }

        }
        if($etatproposerGrade){
            $echellon = explode("-", $avancement->last()->getEchelon()->getDesignation());
            if($avancement->last()->getDateGrade()){
                $dG1=$avancement->last()->getDateGrade()->format('Y-m-d');
                $dGP1=$avancement->last()->getDateDeci()->format('Y-m-d');

                $dG2= new \Datetime('now');
                $dG2= $dG2->format('Y-m-d');

                $diffG = abs(strtotime($dG2) - strtotime($dG1));
                $diffGP = abs(strtotime($dG2) - strtotime($dGP1));

                $yearsG = $diffG / (365*60*60*24);
                $yearsGP = $diffGP / (365*60*60*24);
                    
                if($avancement->last()->getPersonnel()->getTypePersonnelId()->getId()==1 || $avancement->last()->getPersonnel()->getTypePersonnelId()->getId()==3){
                    if(($echellon[1]==3) && ($etatproposerGrade->getRapide() == floor($yearsGP) )
                        || ($echellon[1]==4) && ($etatproposerGrade->getExceptionnel() == floor($yearsGP) )
                        || ($echellon[1]==4) && ($etatproposerGrade->getNormale() == floor($yearsGP) )){

                            $etatechlon = $em->getRepository(Echelon::class)->findOneBy(array('grade' => $etatproposerGrade->getEtatPropose()));
                            $etatproposer =new EchelonAv();
                            $etatproposer->setEtatPropose($etatechlon);
                            if($echellon[1]==4){
                                $etatproposer->setRapide($etatproposerGrade->getExceptionnel()*12);
                            }else{
                                $etatproposer->setRapide($etatproposerGrade->getRapide()*12);
                            }
                            
                            $etat=$etatproposer;

                    } 
                }
    
                    
            }
                
        }


        $html = $this->renderView('document/fiche-personnel.html.twig', array(
                'persone'  => $personneAv,
                'etatproposer' => $etat,
                'base_dir' => $this->getParameter('webroot_doc') . '/../'
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html , array('orientation'=>'Landscape')),
            $personneAv->getNom().'_'.$personneAv->getPrenom().'.pdf' ,
        );
      //  return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html,array('orientation'=>'Landscape')), 200, array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename=fiche_'.$personneAv->getNom().'_'.$personneAv->getPrenom().'.pdf'));
        
    }





    /**
     *
     * @Security(" is_granted('ROLE_RH')  ")
     */
    #[Route('/exporterFiche', name: 'exporterFiche', methods: ['POST','GET'])]
    public function exporterFiche1Action(Request $request, Pdf $knpSnappyPdf , $id=null ) {

        $em = $this->getDoctrine()->getManager();
        $searchParam = $request->get('importFiche');
        $idAv="";

          if($id!=null){
            $idAv=$id;
        }else{
            $idAv=$searchParam['numpprFiche'];
        }

       // $idAv=$searchParam['numpprFiche'];

        $personneAv =$em->getRepository(Personnel::class)->findOneBy(array('numPPR' => $idAv ));

        $avancement=$personneAv->getAvancements();

        $etatproposer =$em->getRepository(EchelonAv::class)->findOneBy(array('etatActuel' => $avancement->last()->getEchelon()));

        $etatproposerGrade =$em->getRepository(GradeAv::class)->findOneBy(array('etatActuel' => $avancement->last()->getGrade()));


        $etat=null;
        $d1=$avancement->last()->getDateDeci()->format('Y-m-d');
        $d2= new \Datetime('now');
        $d2= $d2->format('Y-m-d');

       


        $diff = abs(strtotime($d2) - strtotime($d1));

        $years = $diff / (365*60*60*24);
        if($etatproposer){
            if($etatproposer->getRapide() <= floor($years*12)){
                $etat = $etatproposer;
            }else{
                $etat=null;
            }

        }
        if($etatproposerGrade){
            $echellon = explode("-", $avancement->last()->getEchelon()->getDesignation());
            if($avancement->last()->getDateGrade()){
                $dG1=$avancement->last()->getDateGrade()->format('Y-m-d');
                $dGP1=$avancement->last()->getDateDeci()->format('Y-m-d');

                $dG2= new \Datetime('now');
                $dG2= $dG2->format('Y-m-d');

                $diffG = abs(strtotime($dG2) - strtotime($dG1));
                $diffGP = abs(strtotime($dG2) - strtotime($dGP1));

                $yearsG = $diffG / (365*60*60*24);
                $yearsGP = $diffGP / (365*60*60*24);
                    
                if($avancement->last()->getPersonnel()->getTypePersonnelId()->getId()==1 || $avancement->last()->getPersonnel()->getTypePersonnelId()->getId()==3){
                    if(($echellon[1]==3) && ($etatproposerGrade->getRapide() == floor($yearsGP) )
                        || ($echellon[1]==4) && ($etatproposerGrade->getExceptionnel() == floor($yearsGP) )
                        || ($echellon[1]==4) && ($etatproposerGrade->getNormal() == floor($yearsGP) )){

                            $etatechlon = $em->getRepository(Echelon::class)->findOneBy(array('grade' => $etatproposerGrade->getEtatPropose()));
                            $etatproposer =new EchelonAv();
                            $etatproposer->setEtatPropose($etatechlon);
                            if($echellon[1]==4){
                                $etatproposer->setRapide($etatproposerGrade->getExceptionnel()*12);
                            }else{
                                $etatproposer->setRapide($etatproposerGrade->getRapide()*12);
                            }
                            
                            $etat=$etatproposer;

                    }
                }
    
                    
            }
                
        }


        $html = $this->renderView('document/fiche-personnel.html.twig', array(
                'persone'  => $personneAv,
                'etatproposer' => $etat,
                'base_dir' => $this->getParameter('webroot_doc') . '/../'
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html , array('orientation'=>'Landscape')),
            $personneAv->getNom().'_'.$personneAv->getPrenom().'.pdf' ,
        );
      //  return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html,array('orientation'=>'Landscape')), 200, array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename=fiche_'.$personneAv->getNom().'_'.$personneAv->getPrenom().'.pdf'));
        
    }


    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/importFileDoc', name: 'importFileDoc', methods: ['POST','GET'])]
    public function importFileDocAction(Request $request)
    {


        $searchParam = $request->get('importFile');
        $em = $this->getDoctrine()->getManager();
        $entity="";
        $html="";
    
        $persone = $em->getRepository(Personnel::class)->findOneBy(array('numPPR' => $searchParam["numppr"]));
    
        if($searchParam["typeDocument"]=="AS" or $searchParam["typeDocument"]=="AT"){
            $entity=$em->getRepository(Attestation::class)->findOneBy(array('id' => $searchParam["idObjet"],'personnel' => $persone));
            $html='/Attestation/';
        }elseif($searchParam["typeDocument"]=="AU"){
            $entity=$em->getRepository(Autorisation::class)->findOneBy(array('id' => $searchParam["idObjet"],'personnel' => $persone));
            $html='/Autorisation/';
        }elseif($searchParam["typeDocument"]=="CO"){
            $entity=$em->getRepository(Conge::class)->findOneBy(array('id' => $searchParam["idObjet"],'statut' => '-1'));
            $html='/Conge/';
        }elseif($searchParam["typeDocument"]=="OM"){
            $entity=$em->getRepository(OrdreMission::class)->findOneBy(array('id' => $searchParam["idObjet"],'personnel' => $persone));
            $html='/Ordre_mission/';
        }else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_PPR_INVALIDE");
        }
   
        if($entity){
         
            if (isset($_FILES['lien'])) {
     
                $file = $_FILES['lien'];
                $file_name = $file['name'];
                $file_tmp  = $file['tmp_name'];
               // dd($file_tmp );
                $file_size = $file['size'];
                $file_type = $file['type'];
                $file_error = $file['error'];
                $file_ext  = explode('.', $file_name);
                $file_ext  = strtolower(end($file_ext));
                $fileName = md5(uniqid()).'.'.$file_ext;
                move_uploaded_file($file_tmp,$this->getParameter('webroot_doc').$persone->getNom().'_'.$persone->getPrenom().$html . basename($fileName));

                if(file_exists($this->getParameter('webroot_doc').$persone->getNom().'_'.$persone->getPrenom().$html.$entity->getLien()) && $entity->getLien()!="" && $entity->getLien()!=NULL  ){
                  unlink($this->getParameter('webroot_doc').$persone->getNom().'_'.$persone->getPrenom().$html.$entity->getLien());
                    }
             

                $entity->setStatut('1');
                $entity->setLien($fileName);
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', "MOD_DOCUMENT_VALIDE");
            }else{
                $this->get('session')->getFlashBag()->add('danger', "MOD_DOCUMENT_INVALIDE");
            }
        }else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
        }
        return $this->render('import_export_rh/import_export_rh.html.twig');

    }


    #[Route('/import_export_rh_index', name: 'app_import_export_rh_index', methods: ['POST','GET'])]
    public function import_export_rh(PersonnelRepository $personnelRepository): Response
    {
        return $this->render('import_export_rh/import_export_rh.html.twig');
    }










/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/tableau_calendrier_perso', name: 'tableau_calendrier_perso', methods: ['POST','GET'])]

    public function tableau_calendrier_perso(Request $request) {

        $spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Tableau Calendrier du Personnel");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();


        $styleArrayTitle = array(
            'font' => array(
                'color' => array('rgb' => '161617'),
                'size' => 12,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER
                ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
                ),
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN, 'color' => [ 'rgb' => '808080' ] ] ],
        );

        $styleArrayTitle1 = array(
            'font' => array(
                'color' => array('rgb' => '161617'),
                'size' => 10,
                'name' => 'Times New Roman'
                ),
            'alignment'=>array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER ,
                'vertical'=> Alignment::VERTICAL_CENTER
                ),
 

        );

        $worksheet->getColumnDimension('A')->setWidth(10);
        $worksheet->getColumnDimension('B')->setWidth(20);
        $worksheet->getColumnDimension('C')->setWidth(20);
        $worksheet->getColumnDimension('D')->setWidth(20);
        $worksheet->getColumnDimension('E')->setWidth(27);
        $worksheet->getColumnDimension('F')->setWidth(27);
        $worksheet->getColumnDimension('G')->setWidth(27);
        $worksheet->getColumnDimension('H')->setWidth(27);

        //Rename sheet
        $worksheet->setTitle('Personnel');

        // $worksheet->mergeCells("B2:Z2");   
        // $worksheet->getCell('B2')->setValue(' Université Abdelmalek Essaâdi Ecole Nationale des Sciences Appliquées Tanger ');
       //  $worksheet->getStyle('B2')->getAlignment()->setWrapText(true);
       //$worksheet->getStyle('B2')->applyFromArray($styleArray);
     
        $worksheet->getCell('A1')->setValue('N° P.P.R');
        $worksheet->getCell('B1')->setValue('Nom');
        $worksheet->getCell('C1')->setValue('Prénom');
        $worksheet->getCell('D1')->setValue('Type');
        $worksheet->getCell('E1')->setValue('Ordre de Mission');
        $worksheet->getCell('F1')->setValue('Autorisation');
        $worksheet->getCell('G1')->setValue('Congé');
        $worksheet->getCell('H1')->setValue('Congé Maladie');
 

        $worksheet->getStyle('A1:H1')->applyFromArray($styleArrayTitle);
 


     /*    $worksheet->getStyle('A4:Z4')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');  */
       

        $i=2;
        $personnels =$em->getRepository(Personnel::class)->findAll();
        $om = $em->getRepository(Personnel::class)->Tableau_calendrier_perso_om(Date("Y"));
        $a = $em->getRepository(Personnel::class)->Tableau_calendrier_perso_auto(Date("Y"));
        $conge = $em->getRepository(Personnel::class)->Tableau_calendrier_perso_conge(Date("Y"));

   

        foreach ($personnels as $personne) {
       
            $start = $i;
            $start_array = [];
            $not_null = false;

            foreach ($om as $key => $value) {
                if($personne->getId() ==  $value['p_id'] ){
                 $not_null = true;
                 break;
                } 
             }

             foreach ($a as $key => $value) {
                if($personne->getId() ==  $value['p_id'] ){
                $not_null = true;
                break;
                }  
             }

             foreach ($conge as $key => $value) {
                if($personne->getId() ==  $value['p_id'] ){
                $not_null = true;
                break;
                }  
             }

                if($not_null){

                   

                    $worksheet->getCell('A'.$i)->setValue($personne->getNumPPR());
                    $worksheet->getCell('B'.$i)->setValue($personne->getNom());
                    $worksheet->getCell('C'.$i)->setValue($personne->getPrenom());
               
           
                    if( $personne->getTypePersonnelId()->getId() == 2 || $personne->getTypePersonnelId()->getId() == 4  ){
                        $worksheet->getCell('D'.$i)->setValue("Corps Administratif");
                      }else{
                         $worksheet->getCell('D'.$i)->setValue("Corps Enseignant");
                     }
                    


          
                foreach ($om as $key => $value) {
                   if($personne->getId() ==  $value['p_id'] ){
                    $worksheet->getCell('E'.$start)->setValue($value['date_debut']." --> " .$value['date_fin'] ); 
                    $start++;
                   }
                  
                }

                
                array_push($start_array,$start) ;
                $start = $i;
              



                foreach ($a as $key => $value) {
                    if($personne->getId() ==  $value['p_id'] ){
                    $worksheet->getCell('F'.$start)->setValue(  substr($value['date_sortie'], 0, 10) ." --> " . substr($value['date_rentree'] ,0,10) ) ; 
                    $start++;
                    }
                   
                 }
                 array_push($start_array,$start) ;
                $start = $i;
              


                foreach ($conge as $key => $value) {
                    if($personne->getId() ==  $value['p_id'] ){
                    $worksheet->getCell('G'.$start)->setValue($value['date_debut']." --> " .$value['date_reprise'] ); 
                    $start++;
                    }
                   
                 }
                 array_push($start_array,$start) ;
                $start = $i;


               
           $i = max($start_array) ;
           $k = $i-1;
    

           $worksheet->mergeCells("A".$start.":A".$k); 
           $worksheet->getStyle("A".$start.":A".$k)->applyFromArray($styleArrayTitle1);

           $worksheet->mergeCells("B".$start.":B".$k); 
           $worksheet->getStyle("B".$start.":B".$k)->applyFromArray($styleArrayTitle1);

           $worksheet->mergeCells("C".$start.":C".$k); 
           $worksheet->getStyle("C".$start.":C".$k)->applyFromArray($styleArrayTitle1);

           $worksheet->mergeCells("D".$start.":D".$k); 
           $worksheet->getStyle("D".$start.":D".$k)->applyFromArray($styleArrayTitle1);
         //  $i++;
        }

        
          
        }
       

       $worksheet->getStyle('A2:H'.$i)->applyFromArray($styleArrayTitle1);
        

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'Tableau_Calendrier_Personnel_ENSAT_'.Date("Y").'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

















}
