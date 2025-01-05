<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateurs;
use App\Entity\Avancement;
use App\Repository\AvancementRepository;
use App\Repository\DashboardRepository;

use App\Repository\OrdreMissionRepository;
use App\Repository\CongeRepository;
use App\Repository\AutorisationRepository;
use App\Repository\FicheheureRepository;

use App\Form\ProfileUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security as secure;
use App\Entity\Etudiant\Etudiants;
use App\Entity\Etudiant\InscritEtudiant;
use App\Entity\Personnel;


    /**
     *
     * @Security("is_granted('ROLE_USER') ")
     */
class DashboardController extends AbstractController
{
    #[Route(path: '/dashboard', name: 'app_dashboard')]
    public function index(AvancementRepository $avancementRepository  , OrdreMissionRepository $ordremissionRepository ,AutorisationRepository $autorisationRepository , CongeRepository $congeRepository, FicheheureRepository $ficheheureRepository, secure $security ): Response
    {
        $ordremission = 0 ;
        $autorisation = 0;
        $conge = 0;
        $ficheheures = 0 ;
       
        if(in_array("ROLE_ADMIN",$security->getUser()->getRoles())){
            return new RedirectResponse($this->generateUrl('app_stats_personnel_index'));
        }else{
            if(in_array("ROLE_FONC",$security->getUser()->getRoles())){
                $ordremission = count($ordremissionRepository->searchDemandesByAnnee($security->getUser()->getPersonnel()->getId() , Date("Y")));
                $autorisation = count($autorisationRepository->searchDemandesByAnnee($security->getUser()->getPersonnel()->getId() , Date("Y")));
                $conge =        count($congeRepository->searchDemandesByAnnee($security->getUser()->getPersonnel()->getId() , Date("Y")));
               }
               if(in_array("ROLE_PROF",$security->getUser()->getRoles())){
                $ordremission = count($ordremissionRepository->searchDemandesByAnnee($security->getUser()->getPersonnel()->getId() , Date("Y")));
                $ficheheures =  count($ficheheureRepository->searchDemandesByAnnee($security->getUser()->getPersonnel()->getId() , Date("Y")));
               }

                $avancements =  $avancementRepository->findBy(['personnel'=>$security->getUser()->getPersonnel()->getId()]);

            return $this->render('dashboard/dashboard.html.twig', [
                'avancements' => $avancements,
                'ordremission' => $ordremission,
                'autorisation' => $autorisation,
                'ficheheures' => $ficheheures,
                'conge' => $conge,
            ]); 
        }


       

    }


    #[Route(path: '/', name: 'app_home')]
    public function index_home()
    {
        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }

     /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR') ")
     */
    #[Route(path: '/dashboard_dir', name: 'app_dashboard_dir')]
    public function index_dir( secure $security ): Response
    {
        
      

        $em = $this->getDoctrine()->getManager();
        $em1 = $this->getDoctrine()->getManager('etudiant');


        $CHEF_DIR = $em->getRepository(Personnel::class)->get_resps("ROLE_DIR",null);
        $CHEF_FIN = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_SERV","SER_1");
        $CHEF_COOP = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_SERV","SER_2");
        $CHEF_SCO = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_SERV","SER_3");
        $CHEF_INFO = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_SERV","SER_6");
        $CHEF_RH = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_SERV","SER_14");

        $CHEF_DIR_ADJ_1 = $em->getRepository(Personnel::class)->get_resps("ROLE_DIR_ADJ","DIR_1");
        $CHEF_DIR_ADJ_2 = $em->getRepository(Personnel::class)->get_resps("ROLE_DIR_ADJ","DIR_2");
        $CHEF_SG = $em->getRepository(Personnel::class)->get_resps("ROLE_SG",null);

        $CHEF_SIC = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_DEP","DEP_1");
        $CHEF_MI = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_DEP","DEP_2");
        $CHEF_GEI = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_DEP","DEP_3");
        $CHEF_LCM = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_DEP","DEP_5");

        $CHEF_LTI = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_STRUCT","STR_1");
        $CHEF_LABTIC = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_STRUCT","STR_2");
        $CHEF_IDS = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_STRUCT","STR_3");
        $CHEF_ERMIA = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_STRUCT","STR_4");
        $CHEF_MASI = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_STRUCT","STR_6");

        $CHEF_IITR = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IITR");
        $CHEF_IIGD = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IIGD");
        $CHEF_IIEA = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IIEA");
        $CHEF_IIEE = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IIEE");
        $CHEF_IIGI = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IIGI");
        $CHEF_IICS = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IICS");

/*         $CHEF_IIGL = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IIGL");
        $CHEF_IICS = $em->getRepository(Personnel::class)->get_resps("ROLE_CHEF_FIL","FIL_IICS"); */



       
////////////////////////////////// SCOLARITE
        $attest_all = $em1->getRepository(Etudiants::class)->nb_dem_etu_attest('T');  
        $diplome_all = $em1->getRepository(Etudiants::class)->nb_dem_etu_diplome('T');  
        $carte_all = $em1->getRepository(Etudiants::class)->nb_dem_etu_carte('T');  
        $attest_res_all = $em1->getRepository(Etudiants::class)->nb_dem_etu_attreus('T');  
        $rel_all = $em1->getRepository(Etudiants::class)->nb_dem_etu_rel('T'); 

        $attest_tr = $em1->getRepository(Etudiants::class)->nb_dem_etu_attest('TR');  
        $diplome_tr = $em1->getRepository(Etudiants::class)->nb_dem_etu_diplome('TR');  
        $carte_tr = $em1->getRepository(Etudiants::class)->nb_dem_etu_carte('TR');  
        $attest_res_tr = $em1->getRepository(Etudiants::class)->nb_dem_etu_attreus('TR');  
        $rel_tr = $em1->getRepository(Etudiants::class)->nb_dem_etu_rel('TR');  

    
        $auto_all_SCO = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',3,'ROLE_CHEF_SERV',NULL); 

     

        $conge_all_SCO = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',3,'ROLE_CHEF_SERV',NULL); 
        $om_all_SCO = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',3,'ROLE_CHEF_SERV',NULL); 

        $auto_tr_SCO = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',3,'ROLE_CHEF_SERV',NULL); 
        $conge_tr_SCO = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',3,'ROLE_CHEF_SERV',NULL); 
        $om_tr_SCO = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',3,'ROLE_CHEF_SERV',NULL); 

     //////////////////////RH


        $auto_all_RHS = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',14,'ROLE_CHEF_SERV',NULL); 
        $conge_all_RHS = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',14,'ROLE_CHEF_SERV',NULL); 
        $om_all_RHS = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',14,'ROLE_CHEF_SERV',NULL); 

        $auto_tr_RHS = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',14,'ROLE_CHEF_SERV',NULL); 
        $conge_tr_RHS = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',14,'ROLE_CHEF_SERV',NULL); 
        $om_tr_RHS = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',14,'ROLE_CHEF_SERV',NULL); 

        $att_all_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_ATT',null,null,NULL); 
        $auto_all_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',null,'ROLE_RH',NULL); 
        $conge_all_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',null,'ROLE_RH',NULL); 
        $om_all_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',null,'ROLE_RH',NULL);
        $fh_all_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',null,'ROLE_RH',NULL);

        $att_tr_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_ATT',null,null,NULL); 
        $auto_tr_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',null,'ROLE_RH',NULL); 
        $conge_tr_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',null,'ROLE_RH',NULL); 
        $om_tr_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',null,'ROLE_RH',NULL);
        $fh_tr_RH = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',null,'ROLE_RH',NULL);

     

        /////////// COOPERATION
      

        $emp = $this->getDoctrine()->getManager('etudiant');
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$emp->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	


        $stage_tr = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',1,null, $anneeUniversitaire['COD_ANU']);  
        $dip_tr = $em1->getRepository(Etudiants::class)->nb_dem_etu_diplome('TR'); 

        $stage_all = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',1,null , $anneeUniversitaire['COD_ANU']);  
        $dip_all = $em1->getRepository(Etudiants::class)->nb_dem_etu_diplome('T'); 

        $non_inscrit = $emp->getRepository(InscritEtudiant::class)->findUserByAnnee($anneeUniversitaire['COD_ANU']);
        $inscrit_all = $emp->getRepository(InscritEtudiant::class)->findUserByAnnee_all($anneeUniversitaire['COD_ANU']);

        $non_inscrit_dd= count($non_inscrit);
        $inscrit_dd_all = count($inscrit_all);

        $auto_all_COOP = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',2,'ROLE_CHEF_SERV',NULL); 
        $conge_all_COOP = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',2,'ROLE_CHEF_SERV',NULL); 
        $om_all_COOP = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',2,'ROLE_CHEF_SERV',NULL); 

        $auto_tr_COOP = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',2,'ROLE_CHEF_SERV',NULL); 
        $conge_tr_COOP = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',2,'ROLE_CHEF_SERV',NULL); 
        $om_tr_COOP = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',2,'ROLE_CHEF_SERV',NULL); 

        ///////////////// FINANCE

        $auto_all_FIN = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',1,'ROLE_CHEF_SERV',NULL); 
        $conge_all_FIN = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',1,'ROLE_CHEF_SERV',NULL); 
        $om_all_FIN = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',1,'ROLE_CHEF_SERV',NULL); 

        $auto_tr_FIN = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',1,'ROLE_CHEF_SERV',NULL); 
        $conge_tr_FIN = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',1,'ROLE_CHEF_SERV',NULL); 
        $om_tr_FIN = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',1,'ROLE_CHEF_SERV',NULL); 

        /////////////// INFO

        $auto_all_INFO = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',6,'ROLE_CHEF_SERV',NULL); 
        $conge_all_INFO = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',6,'ROLE_CHEF_SERV',NULL); 
        $om_all_INFO= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',6,'ROLE_CHEF_SERV',NULL); 

        $auto_tr_INFO = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',6,'ROLE_CHEF_SERV',NULL); 
        $conge_tr_INFO= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',6,'ROLE_CHEF_SERV',NULL); 
        $om_tr_INFO = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',6,'ROLE_CHEF_SERV',NULL); 

        ////////////// SG
        $auto_all_SG= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',NULL,'ROLE_SG',NULL); 
        $conge_all_SG = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',NULL,'ROLE_SG',NULL); 
        $om_all_SG= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_SG',NULL); 

        $auto_tr_SG = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',NULL,'ROLE_SG',NULL); 
        $conge_tr_SG= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',NULL,'ROLE_SG',NULL); 
        $om_tr_SG = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_SG',NULL); 
        ////////////// DIR ADJ 1

        $auto_all_DIRADJ1= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',NULL,'ROLE_DIR_ADJ',1); 
        $conge_all_DIRADJ1 = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',NULL,'ROLE_DIR_ADJ',1); 
        $om_all_DIRADJ1= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_DIR_ADJ',1); 
        $fh_all_DIRADJ1= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_DIR_ADJ',1); 

        $auto_tr_DIRADJ1 = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',NULL,'ROLE_DIR_ADJ',1); 
        $conge_tr_DIRADJ1= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',NULL,'ROLE_DIR_ADJ',1); 
        $om_tr_DIRADJ1 = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_DIR_ADJ',1); 
        $fh_tr_DIRADJ1= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_DIR_ADJ',1); 

        //////////////////// DIR ADJ 2

        $auto_all_DIRADJ2 = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',NULL,'ROLE_DIR_ADJ',2); 
        $conge_all_DIRADJ2 = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',NULL,'ROLE_DIR_ADJ',2); 
        $om_all_DIRADJ2= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_DIR_ADJ',2); 

        $auto_tr_DIRADJ2 = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',NULL,'ROLE_DIR_ADJ',2); 
        $conge_tr_DIRADJ2= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',NULL,'ROLE_DIR_ADJ',2); 
        $om_tr_DIRADJ2 = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_DIR_ADJ',2); 

    /////////////////////////////// CHEF DEP


    $om_all_DEP_SIC= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_DEP',1);
    $fh_all_DEP_SIC= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_DEP',1); 

    $om_tr_DEP_SIC = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_DEP',1); 
    $fh_tr_DEP_SIC = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_DEP',1); 

    $om_all_DEP_MI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_DEP',2);
    $fh_all_DEP_MI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_DEP',2); 

    $om_tr_DEP_MI= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_DEP',2);
    $fh_tr_DEP_MI= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_DEP',2); 

    $om_all_DEP_GEI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_DEP',3);
    $fh_all_DEP_GEI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_DEP',3); 

    $om_tr_DEP_GEI = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_DEP',3); 
    $fh_tr_DEP_GEI = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_DEP',3); 
 
    $om_all_DEP_LCM= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_DEP',5);
    $fh_all_DEP_LCM= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_DEP',5); 

    $om_tr_DEP_LCM = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_DEP',5); 
    $fh_tr_DEP_LCM = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_DEP',5); 

  

 

    ///////// CHEF STRUCT

    $om_all_STR_LTI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_STRUCT',1);
    $fh_all_STR_LTI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_STRUCT',1); 

    $om_tr_STR_LTI = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_STRUCT',1); 
    $fh_tr_STR_LTI = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_STRUCT',1); 

    $om_all_STR_LABTIC= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_STRUCT',2);
    $fh_all_STR_LABTIC= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_STRUCT',2); 

    $om_tr_STR_LABTIC = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_STRUCT',2); 
    $fh_tr_STR_LABTIC = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_STRUCT',2); 


    $om_all_STR_IDS= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_STRUCT',3);
    $fh_all_STR_IDS= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_STRUCT',3); 

    $om_tr_STR_IDS = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_STRUCT',3); 
    $fh_tr_STR_IDS = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_STRUCT',3); 

    $om_all_STR_ERMIA= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_STRUCT',4);
    $fh_all_STR_ERMIA= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_STRUCT',4); 

    $om_tr_STR_ERMIA = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_STRUCT',4); 
    $fh_tr_STR_ERMIA= $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_STRUCT',4); 

    $om_all_STR_MASI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_CHEF_STRUCT',6);
    $fh_all_STR_MASI= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_CHEF_STRUCT',6); 

    $om_tr_STR_MASI = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_CHEF_STRUCT',6); 
    $fh_tr_STR_MASI = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_CHEF_STRUCT',6); 

     ///////// DIR
     $auto_all_DIR = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_AUTO',null,'ROLE_DIR',NULL); 
     $conge_all_DIR = $em->getRepository(Personnel::class)->get_dem_by_niveau('T_CONGE',null,'ROLE_DIR',NULL); 
     $om_all_DIR= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_OM',NULL,'ROLE_DIR',NULL);
  //   $fh_all_DIR= $em->getRepository(Personnel::class)->get_dem_by_niveau('T_FH',NULL,'ROLE_DIR',NULL); 

     $auto_tr_DIR = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_AUTO',null,'ROLE_DIR',NULL); 
     $conge_tr_DIR = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_CONGE',null,'ROLE_DIR',NULL); 
     $om_tr_DIR = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_OM',NULL,'ROLE_DIR',NULL); 
    // $fh_tr_DIR = $em->getRepository(Personnel::class)->get_dem_by_niveau('TR_FH',NULL,'ROLE_DIR',NULL); 

////////// CHEF_FIL


$stage_tr_FIL_IITR = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IITR', $anneeUniversitaire['COD_ANU']);  
$stage_all_FIL_IITR = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IITR', $anneeUniversitaire['COD_ANU']);

$stage_tr_FIL_IIGD = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IIGD', $anneeUniversitaire['COD_ANU']);  
$stage_all_FIL_IIGD = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IIGD', $anneeUniversitaire['COD_ANU']);

$stage_tr_FIL_IIGI = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IIGI', $anneeUniversitaire['COD_ANU']);  
$stage_all_FIL_IIGI= $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IIGI',  $anneeUniversitaire['COD_ANU']); 

$stage_tr_FIL_IIEA = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IIEA', $anneeUniversitaire['COD_ANU']);  
$stage_all_FIL_IIEA = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IIEA', $anneeUniversitaire['COD_ANU']); 

$stage_tr_FIL_IIEE= $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IIEE',  $anneeUniversitaire['COD_ANU']);  
$stage_all_FIL_IIEE = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IIEE', $anneeUniversitaire['COD_ANU']);

$stage_tr_FIL_IIGI = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IIGI', $anneeUniversitaire['COD_ANU']);  
$stage_all_FIL_IIGI = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IIGI', $anneeUniversitaire['COD_ANU']); 

$stage_tr_FIL_IICS = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IICS', $anneeUniversitaire['COD_ANU']);  
$stage_all_FIL_IICS = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IICS', $anneeUniversitaire['COD_ANU']);

/* $stage_tr_FIL_IIGL = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('TR',0,'IIGL');  
$stage_all_FIL_IIGL = $em1->getRepository(Etudiants::class)->nb_dem_etu_stage('T',0,'IIGL'); 

  */

//dd("koko");

    //    dd($attest_all) ;



       return $this->render('dashboard/dashboard_dir.html.twig', [
        'attest_all'=> $attest_all[0]['n'] ,
        'diplome_all'=> $diplome_all[0]['n'] ,
        'carte_all'=> $carte_all[0]['n'] ,
        'attest_res_all'=> $attest_res_all[0]['n'] ,
        'rel_all'=> $rel_all[0]['n'] ,

        'attest_tr'=> $attest_tr[0]['n'] ,
        'diplome_tr'=> $diplome_tr[0]['n'] ,
        'carte_tr'=> $carte_tr[0]['n'] ,
        'attest_res_tr'=> $attest_res_tr[0]['n'] ,
        'rel_tr'=> $rel_tr[0]['n'] ,

        'auto_all_SCO'=> $auto_all_SCO[0]['n'] ,
        'conge_all_SCO'=> $conge_all_SCO[0]['n'] ,
        'om_all_SCO'=> $om_all_SCO[0]['n'] ,

        'auto_tr_SCO'=> $auto_tr_SCO[0]['n'] ,
        'conge_tr_SCO'=> $conge_tr_SCO[0]['n'] ,
        'om_tr_SCO'=> $om_tr_SCO[0]['n'] ,
/////////////////////////////////////
        'auto_all_RHS'=> $auto_all_RHS[0]['n'] ,
        'conge_all_RHS'=> $conge_all_RHS[0]['n'] ,
        'om_all_RHS'=> $om_all_RHS[0]['n'] ,

        'att_all_RH'=> $att_all_RH[0]['n'] ,
        'auto_all_RH'=> $auto_all_RH[0]['n'] ,
        'conge_all_RH'=> $conge_all_RH[0]['n'] ,
        'om_all_RH'=> $om_all_RH[0]['n'] ,
        'fh_all_RH'=> $fh_all_RH[0]['n'] ,

        'auto_tr_RHS'=> $auto_tr_RHS[0]['n'] ,
        'conge_tr_RHS'=> $conge_tr_RHS[0]['n'] ,
        'om_tr_RHS'=> $om_tr_RHS[0]['n'] ,

        'att_tr_RH'=> $att_tr_RH[0]['n'] ,
        'auto_tr_RH'=> $auto_tr_RH[0]['n'] ,
        'conge_tr_RH'=> $conge_tr_RH[0]['n'] ,
        'om_tr_RH'=> $om_tr_RH[0]['n'] ,
        'fh_tr_RH'=> $fh_tr_RH[0]['n'] ,

 ///
        'auto_all_COOP'=> $auto_all_COOP[0]['n'] ,
        'conge_all_COOP'=> $conge_all_COOP[0]['n'] ,
        'om_all_COOP'=> $om_all_COOP[0]['n'] ,
        
        'auto_tr_COOP'=> $auto_tr_COOP[0]['n'] ,
        'conge_tr_COOP'=> $conge_tr_COOP[0]['n'] ,
        'om_tr_COOP'=> $om_tr_COOP[0]['n'] ,

        'stage_tr'=> $stage_tr[0]['n'] ,
        'dip_tr'=> $dip_tr[0]['n'] ,
        'stage_all'=> $stage_all[0]['n'] ,
        'dip_all'=> $dip_all[0]['n'] ,
//

        'auto_all_INFO'=> $auto_all_INFO[0]['n'] ,
        'conge_all_INFO'=> $conge_all_INFO[0]['n'] ,
        'om_all_INFO'=> $om_all_INFO[0]['n'] ,
        
        'auto_tr_INFO'=> $auto_tr_INFO[0]['n'] ,
        'conge_tr_INFO'=> $conge_tr_INFO[0]['n'] ,
        'om_tr_INFO'=> $om_tr_INFO[0]['n'] ,

     //  'non_inscrit_dd'=> $non_inscrit_dd ,
      //  'inscrit_dd_all'=> $inscrit_all ,
//

        'auto_all_FIN'=> $auto_all_FIN[0]['n'] ,
        'conge_all_FIN'=> $conge_all_FIN[0]['n'] ,
        'om_all_FIN'=> $om_all_FIN[0]['n'] ,
        
        'auto_tr_FIN'=> $auto_tr_FIN[0]['n'] ,
        'conge_tr_FIN'=> $conge_tr_FIN[0]['n'] ,
        'om_tr_FIN'=> $om_tr_FIN[0]['n'] ,

    ///////////

    'auto_all_SG'=> $auto_all_SG[0]['n'] ,
    'conge_all_SG'=> $conge_all_SG[0]['n'] ,
    'om_all_SG'=> $om_all_SG[0]['n'] ,
    
    'auto_tr_SG'=> $auto_tr_SG[0]['n'] ,
    'conge_tr_SG'=> $conge_tr_SG[0]['n'] ,
    'om_tr_SG'=> $om_tr_SG[0]['n'] ,

    ///////
    'auto_all_DIRADJ1'=> $auto_all_DIRADJ1[0]['n'] ,
    'conge_all_DIRADJ1'=> $conge_all_DIRADJ1[0]['n'] ,
    'om_all_DIRADJ1'=> $om_all_DIRADJ1[0]['n'] ,
    'fh_all_DIRADJ1'=> $fh_all_DIRADJ1[0]['n'] ,

    'auto_tr_DIRADJ1'=> $auto_tr_DIRADJ1[0]['n'] ,
    'conge_tr_DIRADJ1'=> $conge_tr_DIRADJ1[0]['n'] ,
    'om_tr_DIRADJ1'=> $om_tr_DIRADJ1['n'] ,
    'fh_tr_DIRADJ1' => $fh_tr_DIRADJ1[0]['n'] ,

    //////

    'auto_all_DIRADJ2'=> $auto_all_DIRADJ2[0]['n'] ,
    'conge_all_DIRADJ2'=> $conge_all_DIRADJ2[0]['n'] ,
    'om_all_DIRADJ2'=> $om_all_DIRADJ2[0]['n'] ,
    
    'auto_tr_DIRADJ2'=> $auto_tr_DIRADJ2[0]['n'] ,
    'conge_tr_DIRADJ2'=> $conge_tr_DIRADJ2[0]['n'] ,
    'om_tr_DIRADJ2'=> $om_tr_DIRADJ2['n'] ,


    ///////

    'om_all_DEP_SIC'=> $om_all_DEP_SIC[0]['n'] ,
    'fh_all_DEP_SIC'=> $fh_all_DEP_SIC[0]['n'] ,

    'om_tr_DEP_SIC'=> $om_tr_DEP_SIC[0]['n'] ,
    'fh_tr_DEP_SIC'=> $fh_tr_DEP_SIC[0]['n'] ,

    'om_all_DEP_MI'=> $om_all_DEP_MI[0]['n'] ,
    'fh_all_DEP_MI'=> $fh_all_DEP_MI[0]['n'] ,

    'om_tr_DEP_MI'=> $om_tr_DEP_MI[0]['n'] ,
    'fh_tr_DEP_MI'=> $fh_tr_DEP_MI[0]['n'] ,

    'om_all_DEP_GEI'=> $om_all_DEP_GEI[0]['n'] ,
    'fh_all_DEP_GEI'=> $fh_all_DEP_GEI[0]['n'] ,

    'om_tr_DEP_GEI'=> $om_tr_DEP_GEI[0]['n'] ,
    'fh_tr_DEP_GEI'=> $fh_tr_DEP_GEI[0]['n'] ,

    'om_all_DEP_LCM'=> $om_all_DEP_LCM[0]['n'] ,
    'fh_all_DEP_LCM'=> $fh_all_DEP_LCM[0]['n'] ,

    'om_tr_DEP_LCM'=> $om_tr_DEP_LCM[0]['n'] ,
    'fh_tr_DEP_LCM'=> $fh_tr_DEP_LCM[0]['n'] ,


//////////
'auto_all_DIR'=> $auto_all_DIR[0]['n'] ,
'conge_all_DIR'=> $conge_all_DIR[0]['n'] ,
'om_all_DIR'=> $om_all_DIR[0]['n'] ,

'auto_tr_DIR'=> $auto_tr_DIR[0]['n'] ,
'conge_tr_DIR'=> $conge_tr_DIR[0]['n'] ,
'om_tr_DIR'=> $om_tr_DIR[0]['n'] ,

//////////////

'om_all_STR_LTI'=> $om_all_STR_LTI[0]['n'] ,
'fh_all_STR_LTI'=> $fh_all_STR_LTI[0]['n'] ,
'om_tr_STR_LTI'=> $om_tr_STR_LTI[0]['n'] ,
'fh_tr_STR_LTI'=> $fh_tr_STR_LTI[0]['n'] ,

'om_all_STR_LABTIC'=> $om_all_STR_LABTIC[0]['n'] ,
'fh_all_STR_LABTIC'=> $fh_all_STR_LABTIC[0]['n'] ,
'om_tr_STR_LABTIC'=> $om_tr_STR_LABTIC[0]['n'] ,
'fh_tr_STR_LABTIC'=> $fh_tr_STR_LABTIC[0]['n'] ,

'om_all_STR_IDS'=> $om_all_STR_IDS[0]['n'] ,
'fh_all_STR_IDS'=> $fh_all_STR_IDS[0]['n'] ,
'om_tr_STR_IDS'=> $om_tr_STR_IDS[0]['n'] ,
'fh_tr_STR_IDS'=> $fh_tr_STR_IDS[0]['n'] ,

'om_all_STR_ERMIA'=> $om_all_STR_ERMIA[0]['n'] ,
'fh_all_STR_ERMIA'=> $fh_all_STR_ERMIA[0]['n'] ,
'om_tr_STR_ERMIA'=> $om_tr_STR_ERMIA[0]['n'] ,
'fh_tr_STR_ERMIA'=> $fh_tr_STR_ERMIA[0]['n'] ,

'om_all_STR_MASI'=> $om_all_STR_MASI[0]['n'] ,
'fh_all_STR_MASI'=> $fh_all_STR_MASI[0]['n'] ,
'om_tr_STR_MASI'=> $om_tr_STR_MASI[0]['n'] ,
'fh_tr_STR_MASI'=> $fh_tr_STR_MASI[0]['n'] ,

///////////

'stage_tr_FIL_IITR'=> $stage_tr_FIL_IITR[0]['n'] ,
'stage_all_FIL_IITR'=> $stage_all_FIL_IITR[0]['n'] ,

'stage_tr_FIL_IIGD'=> $stage_tr_FIL_IIGD[0]['n'] ,
'stage_all_FIL_IIGD'=> $stage_all_FIL_IIGD[0]['n'] ,

'stage_tr_FIL_IIGI'=> $stage_tr_FIL_IIGI[0]['n'] ,
'stage_all_FIL_IIGI'=> $stage_all_FIL_IIGI[0]['n'] ,

'stage_tr_FIL_IIEA'=> $stage_tr_FIL_IIEA[0]['n'] ,
'stage_all_FIL_IIEA'=> $stage_all_FIL_IIEA[0]['n'] ,

'stage_tr_FIL_IIEE'=> $stage_tr_FIL_IIEE[0]['n'] ,
'stage_all_FIL_IIEE'=> $stage_all_FIL_IIEE[0]['n'] ,

'stage_tr_FIL_IIGI'=> $stage_tr_FIL_IIGI[0]['n'] ,
'stage_all_FIL_IIGI'=> $stage_all_FIL_IIGI[0]['n'] ,

'stage_tr_FIL_IICS'=> $stage_tr_FIL_IICS[0]['n'] ,
'stage_all_FIL_IICS'=> $stage_all_FIL_IICS[0]['n'] ,

/* 
'stage_tr_FIL_IIGL'=> $stage_tr_FIL_IIGL[0]['n'] ,
'stage_all_FIL_IIGL'=> $stage_all_FIL_IIGL[0]['n'] ,

 */

////////////////
'CHEF_DIR'  =>   $CHEF_DIR,
'CHEF_FIN'=> $CHEF_FIN ,
'CHEF_COOP' => $CHEF_COOP,
'CHEF_SCO' => $CHEF_SCO,
'CHEF_INFO' => $CHEF_INFO,
'CHEF_RH' => $CHEF_RH,

'CHEF_DIR_ADJ_1' => $CHEF_DIR_ADJ_1,
'CHEF_DIR_ADJ_2' => $CHEF_DIR_ADJ_2,
'CHEF_SG'  => $CHEF_SG ,

'CHEF_SIC' => $CHEF_SIC ,
'CHEF_MI'  => $CHEF_MI,
'CHEF_GEI' => $CHEF_GEI,
'CHEF_LCM' => $CHEF_LCM ,

'CHEF_LTI' => $CHEF_LTI,
'CHEF_LABTIC' => $CHEF_LABTIC ,
'CHEF_IDS' => $CHEF_IDS ,
'CHEF_ERMIA' => $CHEF_ERMIA ,
'CHEF_MASI' => $CHEF_MASI ,

'CHEF_IITR' => $CHEF_IITR,
'CHEF_IIGD' => $CHEF_IIGD ,
'CHEF_IIEA' => $CHEF_IIEA,
'CHEF_IIEE' => $CHEF_IIEE ,
'CHEF_IIGI' => $CHEF_IIGI ,
'CHEF_IICS' => $CHEF_IICS ,
/* 'CHEF_IIGL' => $CHEF_IIGL,
'CHEF_IICS' => $CHEF_IICS */


    ]); 



       

    }

   

   
}
