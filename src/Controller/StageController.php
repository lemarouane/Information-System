<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces;
use Doctrine\DBAL\Connection;

use App\Entity\Etudiant\Etudiants;
//use App\Entity\Customer\image;

use App\Entity\Etudiant\image;

use App\Twig\ConfigExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

// Include JSON Response
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use App\Entity\Etudiant\EtuDiplomeCarte;
use App\Entity\Filiere;
use App\Entity\Stageencad;
use App\Entity\Personnel;
use App\Entity\Etudiant\Entreprises;
use App\Entity\Etudiant\EtudiantDD;
use App\Entity\Etudiant\InscritEtudiant;
use App\Entity\Etudiant\Stage;
use App\Entity\Etudiant\Laureats;
use App\Entity\Etudiant\Experience;
use App\Entity\Etudiant\TypeStage;
use App\Entity\EtuHistoDemandes;
use DateTime;
use Knp\Snappy\Pdf;
use Liip\ImagineBundle\Config\Filter\Type\Background;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\InternetTest;

use App\Entity\Etudiant\Cvtheque;
use App\Entity\Etudiant\Formations;
use App\Entity\Etudiant\Clubs;

class StageController extends AbstractController
{

     /**
     * @Route("/conventions_annuler/{id}", name="conventions_annuler")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_ADMIN') ")
     */
    public function conventions_annuler($id): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $stage=$em->getRepository(Stage::class)->find($id);
        $stage->setStatut(4) ;
        $em->persist($stage);
		$em->flush();
        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

       return new RedirectResponse($this->generateUrl('conventionsTraitement'));

    }
    
	/**
     * @Route("/diplomes", name="diplomes")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_DIR') ")
     */
    public function diplomesAction()
    {
		$em = $this->getDoctrine()->getManager('etudiant');


        $diplomes = $em->getRepository(EtuDiplomeCarte::class)->findby(array('type'=>'Diplome','decision' => '-1'));

		return $this->render('stage/diplomes1.html.twig',['diplomes' => $diplomes]);

    }

    #[Route('/app_counter_cooperation', name: 'app_counter_cooperation', methods: ['POST'])]
    public function app_counter(secure $security ) {
		$em = $this->getDoctrine()->getManager('etudiant');
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $usr = $security->getUser();

        $diplomes = [];
        $non_inscrit = [];
        if($this->get('security.authorization_checker')->isGranted('ROLE_CHEF_FIL') && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $codes=array();
            foreach($usr->getCodes() as $code){
                if(strpos($code, 'FIL') !== false){
                    $code=explode('_',$code);
                    
                    array_push($codes,$code[1]);
                }
                
            }

            $conventions = $em->getRepository(Stage::class)->searchByFiliere($anneeUniversitaire['COD_ANU'],$codes);
        }else{

            $diplomes = $em->getRepository(EtuDiplomeCarte::class)->findby(array('type'=>'Diplome','decision' => '-1'));
            $non_inscrit = $em->getRepository(InscritEtudiant::class)->findUserByAnnee($anneeUniversitaire['COD_ANU']);
            $conventions = $em->getRepository(Stage::class)->findby(array('statut' => '-2','niveau' => '1','anneeuniv' => $anneeUniversitaire['COD_ANU']));
        }


		$result['diplome']= count($diplomes);
		$result['non_inscrit']= count($non_inscrit);
		$result['convention']= count($conventions);
		$result['totale']= count($conventions)+count($diplomes);

        return new JsonResponse($result);
    }




    /**
     * @Route("/conventions_loop/{id}", name="conventions_loop")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')")
     */
    public function loop($id): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $stage=$em->getRepository(Stage::class)->find($id);
        return $this->renderForm('stage/hist-stage-loop.html.twig',[
            'stage' => $stage,
        ]);

    }

    /**
     * @Route("/suivi_loop/{id}", name="suivi_loop")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_CHEF_FIL')")
     */
    public function suivi_loop($id): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        
        $suivi = $em->getRepository(Cvtheque::class)->findOneBy(array("idUser" => $id));

      //  dd($suivi) ;
   
        return $this->renderForm('stage/hist-suivi-loop.html.twig',[
            'suivi' => $suivi,
        ]);

    }

    /**
     * @Route("/stageencad_by_prof", name="stageencad_by_prof")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') ")
     */
    public function stageencad_by_prof(Request $request  ,secure $security  ): Response
    {
      
        $em = $this->getDoctrine()->getManager();

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em = $this->getDoctrine()->getManager('etudiant');

        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
      
        $array_stage = [];

        $em1 = $this->getDoctrine()->getManager();
        $user = $security->getUser();
        $id_prof =  $em1->getRepository(Personnel::class)->findOneBy(['idUser' => $user->getId() ]);

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $prof = 0;
            $resultat =  $em1->getRepository(Stageencad::class)->findAll();
        }else{
            $prof = $id_prof->getId();
            $resultat =  $em1->getRepository(Stageencad::class)->findBy(['encadrant' => $prof]);
        }
          

        foreach ($resultat as $key => $value) {

            $pfe =  $em->getRepository(Stage::class)->findOneBy(['id' => $value->getStage()]);
            array_push($array_stage, $pfe);
        }

        return $this->renderForm('stage/pfe-encadre.html.twig',[
            'annee_univ'=>$anneeUniversitaire['COD_ANU'],
            'array_stage' => $array_stage,
        ]);

    }

     /**
     * @Route("/stageencad_{id}", name="stageencad")
     * @droitAcces("is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SERVICEEXT')")
     */
    public function stageencad(Request $request  ,secure $security  , $id ): Response
    {

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager('etudiant');

        $anneeUniversitaire=$em1->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);



      $stageencad = new Stageencad();
      $em = $this->getDoctrine()->getManager();
      $searchParam = $request->get('searchParam');

      $stageencad->setEncadrant($searchParam['encadrant']) ;
      $stageencad->setStage($searchParam['code_stage']) ;
      $stageencad->setAnneeuniv($anneeUniversitaire['COD_ANU']) ;

      $stageencad_acnien = $em->getRepository(Stageencad::class)->findBy(['stage' => $id]);
     if( count($stageencad_acnien) > 0){

        foreach ($stageencad_acnien as $a) {$em->remove($a);}
        $em->flush();

     }
    
      $em->getRepository(Stageencad::class)->save($stageencad, true);

      return new RedirectResponse($this->generateUrl('pfe'));
     
    }

 
    /**
     * @Route("/pfe", name="pfe")
     * @droitAcces("is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SERVICEEXT')")
     */
    public function pfe(secure $security): Response
    {
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em = $this->getDoctrine()->getManager('etudiant');

        $em1 = $this->getDoctrine()->getManager();
      
        $security->getUser()->getRoles();
        $filiere = "";
        if( in_array("ROLE_CHEF_FIL",$security->getUser()->getRoles()) ){
        $codes = implode(',',  $security->getUser()->getCodes() );  
        $filiere =  substr($codes ,strpos($codes,"FIL_")+4,4);
        }
      
        if( strpos($filiere,"IM")==false ){
            $filiere = $filiere."3" ;
        }

       if( in_array("ROLE_ADMIN",$security->getUser()->getRoles()) || in_array("ROLE_DIR",$security->getUser()->getRoles()) || in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles()) || in_array("ROLE_SERVICEEXT",$security->getUser()->getRoles()) ){
            $filiere =  "%" ;
            } 
      
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $etudiant_inscrits = $em->getRepository(Stage::class)->getEtudiantsInscrits($conn,$anneeUniversitaire['COD_ANU'],$filiere);
        $etudiant_stage_par_filiere = $em->getRepository(Stage::class)->getPFEbyFil($anneeUniversitaire['COD_ANU'],$filiere,1); 
        $personel_list = $em1->getRepository(Personnel::class)->findBy(array('typePersonnelId' => array(1, 3)), array('nom' => 'ASC')); 

         foreach ($etudiant_inscrits as $key => $value) {

          $key_found = array_search($value['cod_etu'] , $etudiant_stage_par_filiere[1] ) ;

           if($key_found!==false){

            $encad = $em1->getRepository(Stageencad::class)->findOneBy(array('stage'=>$etudiant_stage_par_filiere[0][$key_found]));
          

            if($encad){
                $personel = $em1->getRepository(Personnel::class)->findOneBy(array('id'=>$encad->getEncadrant()));
                $etudiant_inscrits[$key]['code_encad'] = $personel->getNom() . " " .$personel->getPrenom()      ;
            }else{
                $etudiant_inscrits[$key]['code_encad'] = '-' ;
            }


            $etudiant_inscrits[$key]['code_stage'] = $etudiant_stage_par_filiere[0][$key_found] ;

           }else{
            $etudiant_inscrits[$key]['code_stage'] = 0 ;
            $etudiant_inscrits[$key]['code_encad'] = '-' ;

           }
        }
     
       /*  if(in_array("ROLE_CHEF_FIL",$security->getUser()->getRoles())){
            dd($etudiant_inscrits);
        } */


        return $this->renderForm('stage/pfe.html.twig',[
            'etudiant_inscrits' => $etudiant_inscrits,
            'personel_list' => $personel_list,
            'filiere'=>$filiere,
            'annee_univ'=>$anneeUniversitaire['COD_ANU']
        ]);

    }


     /**
     * @Route("/export_pfe_list",name="export_pfe_list")
     * @droitAcces("is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SERVICEEXT')")
     */
    public function export_pfe_list(Request $request , secure $security)
    {
        $searchParam = $request->get('importFile');

///////////////////////////////////////////////////////////////////////////////////
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em = $this->getDoctrine()->getManager('etudiant');

        $em1 = $this->getDoctrine()->getManager();
      
        $security->getUser()->getRoles();
        $filiere = "";
        if( in_array("ROLE_CHEF_FIL",$security->getUser()->getRoles()) ){
        $codes = implode(',',  $security->getUser()->getCodes() );  
        $filiere =  substr($codes ,strpos($codes,"FIL_")+4,4);
        }
      
        if( strpos($filiere,"IM")==false ){
            $filiere = $filiere."3" ;
        }

        if( in_array("ROLE_ADMIN",$security->getUser()->getRoles()) || in_array("ROLE_DIR",$security->getUser()->getRoles()) || in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles()) || in_array("ROLE_SERVICEEXT",$security->getUser()->getRoles()) ){
            $filiere =  "%" ;
            }
      
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $etudiant_inscrits = $em->getRepository(Stage::class)->getEtudiantsInscrits($conn,$anneeUniversitaire['COD_ANU'],$filiere);
        $etudiant_stage_par_filiere = $em->getRepository(Stage::class)->getPFEbyFil($anneeUniversitaire['COD_ANU'],$filiere,1); 
        $personel_list = $em1->getRepository(Personnel::class)->findBy(array('typePersonnelId' => array(1, 3)), array('nom' => 'ASC')); 

         foreach ($etudiant_inscrits as $key => $value) {

          $key_found = array_search($value['cod_etu'] , $etudiant_stage_par_filiere[1] ) ;

          $email = $em->getRepository(Etudiants::class)->findOneBy(array('code'=> $value['cod_etu'] ));
          if($email){
            $etudiant_inscrits[$key]['email_inst'] = $email->getEmail() ;
        }else{
            $etudiant_inscrits[$key]['email_inst'] = '-';
        }


           if($key_found!=false){

            $encad = $em1->getRepository(Stageencad::class)->findOneBy(array('stage'=>$etudiant_stage_par_filiere[0][$key_found]));
           

            if($encad){
                $personel = $em1->getRepository(Personnel::class)->findOneBy(array('id'=>$encad->getEncadrant()));
                $etudiant_inscrits[$key]['code_encad'] = $personel->getNom() . " " .$personel->getPrenom()      ;
            }else{
                $etudiant_inscrits[$key]['code_encad'] = '-' ;
            }

            

            $etudiant_inscrits[$key]['code_stage'] = $etudiant_stage_par_filiere[0][$key_found] ;

           }else{
            $etudiant_inscrits[$key]['code_stage'] = 0 ;
            $etudiant_inscrits[$key]['code_encad'] = '-' ;

           }
        }
     

//////////////////////////////////////////////////////////////////////////////////////////////////////////////


        $objPHPExcel = new Spreadsheet();

        // Get the active sheet.
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getProperties()
            ->setCreator("Abdessamad")
            ->setLastModifiedBy("Abdessamad")
            ->setTitle("listes des PFE")
            ->setSubject("listes des PFE")
            ->setDescription("description du fichier")
            ->setKeywords("");
        $sheet = $objPHPExcel->getActiveSheet();
        $j=3;
        $sheet->setCellValue('B2','CODE APOGEE');
        $sheet->setCellValue('C2','NOM');
        $sheet->setCellValue('D2','PRENOM');
        $sheet->setCellValue('E2','FILIERE');
        $sheet->setCellValue('F2','PFE');
        $sheet->setCellValue('G2','E-MAIL INSTITUTIONNEL');
        $sheet->setCellValue('H2','ENCADRANT');

        $objPHPExcel->getActiveSheet()
            ->getStyle('B2'.':H2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('09594C');
        $styleA1=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('B2'.':H2')->applyFromArray($styleA1);

        $styleA2=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
            ),
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
        );
        $styleBordure1=array(
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
            'font'=>array(
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            )
        );

            $entities = $etudiant_inscrits;
            foreach( $entities as $e  ){

                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getColumnDimension('G')->setWidth(20);
                    $sheet->getColumnDimension('H')->setWidth(20);

                    $sheet->getStyle('B'.$j.':H'.$j)->applyFromArray($styleBordure1);
                    
                    $sheet->setCellValue('B'.$j,$e['cod_etu']);
                    $sheet->setCellValue('C'.$j,$e['lib_nom_pat_ind']);
                    $sheet->setCellValue('D'.$j,$e['lib_pr1_ind']);
                    $sheet->setCellValue('E'.$j,$e['cod_etp']);

                    if($e['code_stage']!=0){
                        $sheet->setCellValue('F'.$j,'OUI' );
                    }else{
                        $sheet->setCellValue('F'.$j,'NON' );
                    }
                    $sheet->setCellValue('G'.$j,$e['email_inst'] );
                    $sheet->setCellValue('H'.$j,$e['code_encad'] );
                    $j++;

        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($objPHPExcel);
        
        // Create a Temporary file in the system
        $fileName = 'Liste_PFE_'.$filiere.'_'.$anneeUniversitaire['COD_ANU'].'-'.($anneeUniversitaire['COD_ANU']+1).'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
 
 

    /**
     * @Route("/conventions", name="conventions")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_CHEF_FIL')  or is_granted('ROLE_DIR')")
     */
    public function conventionsFiliereAction(secure $security)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
        
		$param= new ConfigExtension($em1);
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $conventions = array();

		$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $nbStages=$em->getRepository(Stage::class)->nbStageByEtudiant($anneeUniversitaire['COD_ANU']);
        $usr = $security->getUser();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_CHEF_FIL') && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $codes=array();
            foreach($usr->getCodes() as $code){
                if(strpos($code, 'FIL') !== false){
                    $code=explode('_',$code);
                    
                    array_push($codes,$code[1]);
                }
                
            }

            $conventions1 = $em->getRepository(Stage::class)->searchByFiliere($anneeUniversitaire['COD_ANU'],$codes);
            foreach($conventions1 as $convention){
                $stage= new Stage();
                $stage->setId($convention['id']);
                $stage->setSujet($convention['sujet']);
                $date = new DateTime($convention['dateDebut']);
                $date1 = new DateTime($convention['dateEnvoie']);
                $date2 = new DateTime($convention['dateFin']);
                $date3 = new DateTime($convention['dateValidation']);
                $stage->setDateDebut($date);
                $stage->setDateFin($date2);
                $stage->setPhone($convention['phone']);
                $stage->setFichier($convention['fichier']);
                $stage->setFiliere($convention['filiere']);
                $stage->setStatut($convention['statut']);
                $stage->setNiveau($convention['niveau']);
                $stage->setDateValidation($date3);
                $stage->setUser($em->getRepository(Etudiants::class)->find($convention['user_id']));
                $stage->setEntreprise($em->getRepository(Entreprises::class)->find($convention['entreprise_id']));
                $stage->setIntitule($convention['intitule']);
                $stage->setTypeStage($em->getRepository(TypeStage::class)->find($convention['typeStage_id']));
                $stage->setDateEnvoie($date1);
                array_push($conventions,$stage);
            }
        }else if ($this->get('security.authorization_checker')->isGranted('ROLE_SERVICEEXT')){
            $conventions = $em->getRepository(Stage::class)->findby(array('statut' => '-2','niveau' => '1','anneeuniv' => $anneeUniversitaire['COD_ANU']));

        }
        

        return $this->render('stage/convention1.html.twig',['conventions' => $conventions,'nbStages'=>$nbStages]);
    }
	
    

    /**
     * @Route("/conventions_by_fil_{fil}", name="conventions_by_fil")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR')")
     */
    public function conventionsFiliereAction_by_fil(secure $security , $fil)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
        
		$param= new ConfigExtension($em1);
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $conventions = array();

		$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $nbStages=$em->getRepository(Stage::class)->nbStageByEtudiant($anneeUniversitaire['COD_ANU']);
        $usr = $security->getUser();

    
            $codes=array();
         
            array_push($codes,$fil);
            
            $conventions1 = $em->getRepository(Stage::class)->searchByFiliere($anneeUniversitaire['COD_ANU'],$codes);
         //   dd($anneeUniversitaire );
            foreach($conventions1 as $convention){
                $stage= new Stage();
                $stage->setId($convention['id']);
                $stage->setSujet($convention['sujet']);
                $date = new DateTime($convention['dateDebut']);
                $date1 = new DateTime($convention['dateEnvoie']);
                $date2 = new DateTime($convention['dateFin']);
                $date3 = new DateTime($convention['dateValidation']);
                $stage->setDateDebut($date);
                $stage->setDateFin($date2);
                $stage->setPhone($convention['phone']);
                $stage->setFichier($convention['fichier']);
                $stage->setFiliere($convention['filiere']);
                $stage->setStatut($convention['statut']);
                $stage->setNiveau($convention['niveau']);
                $stage->setDateValidation($date3);
                $stage->setUser($em->getRepository(Etudiants::class)->find($convention['user_id']));
                $stage->setEntreprise($em->getRepository(Entreprises::class)->find($convention['entreprise_id']));
                $stage->setIntitule($convention['intitule']);
                $stage->setTypeStage($em->getRepository(TypeStage::class)->find($convention['typeStage_id']));
                $stage->setDateEnvoie($date1);
                array_push($conventions,$stage);
            }
        

        return $this->render('stage/convention1.html.twig',['conventions' => $conventions,'nbStages'=>$nbStages, 'fil' => $fil]);
    }



    /**
     * @Route("/conventionsTraitement", name="conventionsTraitement")
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     */
    public function conventionsTraitementAction()
    {
    	$em = $this->getDoctrine()->getManager('etudiant');

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $conventions = $em->getRepository(Stage::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']) );

        //$conventions = $em->getRepository(Stage::class)->rechercheByStatut($anneeUniversitaire['COD_ANU']);
		return $this->render('stage/gestionStage1.html.twig',['conventions' => $conventions]);
    }

    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     * @Route("/decisiondiplome/{id}",name="decisiondiplome")
     */
    public function decisionDocAction(Request $request,$id,MailerInterface $mailer , InternetTest $int)
    {

        $em = $this->getDoctrine()->getManager('etudiant');
        $searchParam = $request->get('searchParam');

        extract($searchParam);
        $Motifs='';
        $typeDoc='';
        $typeRetour=''; 
        $document = $em->getRepository(EtuDiplomeCarte::class)->find($id);
        $typeDoc = $document->getType();

        $email='';

        if($decision==1){
        	
        	$document->setDecision("1");
            $document->setDatevalidation(new \DateTime('now'));
            $Motifs ='Votre demande à été accepté ';
  
        }elseif($decision==0){

        	$document->setDecision("0");
            $document->setDatevalidation(new \DateTime('now'));
            $Motifs ='à été refusé :'.$motifs;
            $document->setMotif("votre demande ".$Motifs);
        }elseif($decision==2){

        	$document->setDecision("2");
            $document->setDatevalidation(new \DateTime('now'));
            $Motifs ='Votre demande à été accepté '.$motifs;   
        }

        if($document->getCodeEtudiant()->getEmail()){
            $email=$document->getCodeEtudiant()->getEmail();
        }else{
            $email='scolarite.ensat@gmail.com';
        }

        $html=$this->renderView('stage/emailinformation1.html.twig',array('etudiant' => $document->getCodeEtudiant() ,'decision' => $document->getDecision(),'motif' => $Motifs));
        $message = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'Convention'))
                ->to($email.'')
                ->subject($typeDoc)
                ->html($html)
                ;
        try {
               if($int->pingGmail() == 'alive'){
                      $mailer->send($message);
                    }
        } catch (TransportExceptionInterface $e) {
              
        }

	    $em->persist($document);
        $em->flush();

         return new RedirectResponse($this->generateUrl('diplomes'));


    }

    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_CHEF_FIL')")
     * @Route("/decisionstage/{id}",name="decisionstage")
     */
    public function decisionStageAction(Request $request,$id,secure $security,MailerInterface $mailer , InternetTest $int)
    {

	
        $searchParam = $request->get('searchParam');
        $em1 = $this->getDoctrine()->getManager('etudiant');
        $em = $this->getDoctrine()->getManager();
        extract($searchParam);
        $Motifs='';
        $typeDoc='';
        $typeRetour=''; 
        $document = $em1->getRepository(Stage::class)->find($id);
        $typeDoc = 'Convention de stage';

        $email='';

        
        if($decision==1){
        	if($this->get('security.authorization_checker')->isGranted("ROLE_CHEF_FIL")){
                $document->setStatut("-2");
                $document->setNiveau("1");
                $document->setDatevalidation(new \DateTime('now'));
            }else{
                $document->setStatut("1");
                $document->setDatevalidation(new \DateTime('now'));
                $Motifs ='Votre demande à été accepté ';
            }

        }elseif($decision==0){

        	$document->setStatut("0");
            $document->setDatevalidation(new \DateTime('now'));
            $Motifs ='à été refusé :'.$motif;
            $document->setMotif("votre demande ".$Motifs);
        }

        if($document->getUser()->getEmail()){
            $email=$document->getUser()->getEmail();
        }else{
            $email='gcvre@uae.ac.ma';
        }

        $HistoDemandes = new EtuHistoDemandes();
        $HistoDemandes->setTypeDemande('Convention') ; 
        $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
        $HistoDemandes->setNiveau("Service Coopération") ;
        $HistoDemandes->setDateValidation(new \DateTime('now')) ;
        $HistoDemandes->setDateEnvoie($document->getDateEnvoie()) ;
        $HistoDemandes->setIdDemande($document->getId()) ;
        $HistoDemandes->setStatut($document->getStatut()) ;
		$HistoDemandes->setAnneeUniv($document->getAnneeuniv()) ;
        if($this->get('security.authorization_checker')->isGranted("ROLE_SERVICEEXT")){
			
            $html=$this->renderView('stage/emailinformation.html.twig',array('etudiant' => $document->getUser() ,'decision' => $document->getStatut(),'motif' => $Motifs));
            $message = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'Convention'))
                ->to($email.'')
                ->subject('Convention de stage ')
                ->html($html)
                ;
              try {
                     if($int->pingGmail() == 'alive'){
                      $mailer->send($message);
                    }
              } catch (TransportExceptionInterface $e) {
              
              }
        }
        $em->persist($HistoDemandes);
		$em->flush();
	    $em1->persist($document);
        $em1->flush();
        
        return new RedirectResponse($this->generateUrl('conventions'));
        


    }

    /**
     * @Route("/listdiplomes", name="listdiplomes")
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     */
    public function listdiplomesAction(secure $security,Request $request, Connection $conn)
    {

	    	$em = $this->getDoctrine()->getManager('etudiant');

			$diplomes = $em->getRepository(EtuDiplomeCarte::class)->rechercheByDecision();

	        return $this->render('stage/diplomesListe1.html.twig', array('diplomes' => $diplomes));


    }

         /**
     * @Route("/listlaureat", name="listlaureat")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_ADMIN') or is_granted('ROLE_CHEF_SERV')")
     */
    public function listlaureat(secure $security,Request $request, Connection $conn)
    {

	    	$em = $this->getDoctrine()->getManager('etudiant');

			$diplomes = $em->getRepository(EtuDiplomeCarte::class)->findBy(array('type'=>'Diplome' ,'decision'=>'1'));
            $cv_array = [];

            foreach ($diplomes as $key => $value) {

               $cv = $em->getRepository(Cvtheque::class)->findOneBy(array('idUser'=>$value->getCodeEtudiant()->getId()));
               if($cv!=null){
                array_push($cv_array,$value->getCodeEtudiant()->getId());
               }
              
            }
        

	        return $this->render('stage/LaureatListe.html.twig', array('diplomes' => $diplomes , 'cv'=>$cv_array ));


    }
    
    
    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     * @Route("/removedip/{id}",name="removedip")
    */
    public function removedipAction(secure $security,Request $request,$id,Connection $conn)
    {

	    	$em = $this->getDoctrine()->getManager('etudiant');


	        $document = $em->getRepository(EtuDiplomecarte::class)->find($id);
	       
	        if($document){
	        	$em->remove($document);
        		$em->flush();
	        }
			$users = $em->getRepository(Etudiants::class)->findAll();

	        return $this->render('scolarite/usersListe.html.twig', array('users' => $users));



    }
    /**
     * @Route("/afficherListeConvention",name="afficherListeConvention")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_CHEF_FIL')")
     */
    public function afficherListeConventionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
		$param= new ConfigExtension($em1);
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $usr = $em->getRepository(Etudiants::class)->find($request->get('codeApogee'));
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);

        $stage = $em->getRepository(Stage::class)->findBy(array('user' => $usr,'statut' => '1','anneeuniv' => $anneeUniversitaire['COD_ANU']));
        $stageString ="<div class='row table-responsive' style='margin-left:5px; margin-right: 5px;border: 1px solid #EDEBEB;padding: 0px 18px;'>  <table class='table table-striped  table-bordered'><thead style='Background-color:#ff6632;color:white'><tr><th>Code Apogee</th><th> Nom</th><th>Prenom</th><th>E-mail</th><th>N° Téléphone</th><th>Sujet</th><th>Entreprise</th><th>Date Debut</th><th>Date de Fin</th><th>Statut</th></tr></thead><tbody>";
        if(!empty($stage))
        {   
            foreach ($stage as $stage) {
                if($stage->getStatut() == -1){
                    $statut = "en cours de traitement";
                }elseif($stage->getStatut() == -2){
                    $statut = "en cours de traitement";
                }elseif($stage->getStatut() == 0){
                    $statut = "Refusé";
                }else{
                    $statut = "Accordé";
                } 
                $stageString .= '<tr>';
                $stageString .= '<td>'.$stage->getUser()->getCode().'</td>';
                $stageString .= '<td>'.$stage->getUser()->getNom().'</td>';
                $stageString .= '<td>'.$stage->getUser()->getPrenom().'</td>';
                $stageString .= '<td>'.$stage->getUser()->getEmail().'</td>';
                $stageString .= '<td>'.$stage->getUser()->getPhone().'</td>';
                $stageString .= '<td>'.$stage->getSujet().'</td>';
                $stageString .= '<td>'.$stage->getEntreprise()->getIntitule().'</td>';
                $stageString .= '<td>'.$stage->getDateDebut()->format('d/m/Y').'</td>';
                $stageString .= '<td>'.$stage->getDateFin()->format('d/m/Y').'</td>';
                $stageString .= '<td>'. $statut.'</td>';
                $stageString .= '</tr>';
            }
        }
        $stageString .="</tbody></table></div>";
       return new Response($stageString);
    }

    /**
     * @Route("/statStage", name="statStage")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') or is_granted('ROLE_MANAGER')")
     */
    public function statStageAction()
    {
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		$em = $this->getDoctrine()->getManager('etudiant');

		$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivAll($conn);	
        $annee=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	
		
        $non_inscrit = $em->getRepository(InscritEtudiant::class)->findUserByAnnee($annee['COD_ANU']);
        $totale = $em->getRepository(EtudiantDD::class)->searchEtudiantDDAnnee($annee['COD_ANU']);

		return $this->render('stage/statistiques1.html.twig',['annee' => $annee['COD_ANU'],'anneeUniversitaire' => $anneeUniversitaire, 'non_inscrit' => count($non_inscrit),'totaleInscritDD' => count($totale)]);
    }

    /**
     * @Route("/getstatStage/{annee}", name="getstatStage")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') or is_granted('ROLE_MANAGER')")
     */
    public function getstatStage($annee=null)
    {
        
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();
		$em = $this->getDoctrine()->getManager('etudiant');
		$param= new ConfigExtension($em1);

		
        if($annee){
			$anneeUniversitaire['COD_ANU']=$annee;
		}else{
			$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);		
		}

		$initiale = explode(",", $param->app_config('initiale'));

		$nbEtudiants= $em->getRepository(Etudiants::class)->nbEtudiantByGenreStage($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
		
		$nbGarçons = $em->getRepository(Etudiants::class)->nbEtudiantByGenreStage($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'M');

		$nbFilles = $em->getRepository(Etudiants::class)->nbEtudiantByGenreStage($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'F');

        $dipValide=$em->getRepository(EtuDiplomeCarte::class)->findby(array('type'=>'Diplome','decision' => '1'));

        $convValide=$em->getRepository(Stage::class)->findby(array('statut' => '1','niveau' => '1','anneeuniv' => $anneeUniversitaire['COD_ANU']));
        
        $TotalDiplome = ($dipValide== null)  ? 0 : count($dipValide);
        $TotalConvention = ($convValide==null) ? 0 : count($convValide);
	 
		$statistiques['convention']=$TotalConvention;
		$statistiques['diplome']=$TotalDiplome;
		$statistiques['totales']=$nbEtudiants[0];
		$statistiques['nbGarçons']=$nbGarçons[0];
		$statistiques['nbFilles']=$nbFilles[0];

		return new JsonResponse($statistiques);
		


    }

    
	/**
     * @Route("/getevolutionEffectifStage/{annee}", name="getevolutionEffectifStage")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') ")
     */
    public function getevolutionEffectifStage($annee=null)
    {     

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();
		$em = $this->getDoctrine()->getManager('etudiant');
		$param= new ConfigExtension($em1);

		
        if($annee){
			$anneeUniversitaire['COD_ANU']=$annee;
		}else{
			$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	
		}
		$initiale = explode(",", $param->app_config('initiale'));
		$effectifs = $em->getRepository(Etudiants::class)->evolutioneffectifStage($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,$param->app_config('typeResultat'));
        return new JsonResponse($effectifs);
	    
    }

    /**
     * @Route("/getCapaciteAsDiplomeStage/{annee}", name="getCapaciteAsDiplomeStage")
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') ")
     */
    public function getCapaciteAsDiplomeStage($annee=null)
    {     

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();
		$em = $this->getDoctrine()->getManager('etudiant');
		$param= new ConfigExtension($em1);

		
        if($annee){
			$anneeUniversitaire['COD_ANU']=$annee;
		}else{
			$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);			
		}
		$initiale = explode(",", $param->app_config('initiale'));
		$nbAsDip = $em->getRepository(Etudiants::class)->getNbAsDiplomeStage($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
        
        return new JsonResponse($nbAsDip);
	    
    }
    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG')")
     * @Route("/statsPor_ext/{annee}",name="statsPor_ext")
     */
    public function getDocumentPourcentage_extAction($annee) {

        $em = $this->getDoctrine()->getManager('etudiant');
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        if($annee){
			$anneeUniversitaire['COD_ANU']=$annee;
		}else{
			$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);			
		}
        $nbAbParType =$em->getRepository(Stage::class)->nbDocumentParType($annee);

        return new JsonResponse($nbAbParType);
    }
    
    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') ")
     * @Route("/statsPorConventionFiliere/{annee}",name="statsPorConventionFiliere")
     */
    public function getstatsPorConventionFiliereAction($annee) {

        $em = $this->getDoctrine()->getManager('etudiant');
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        if($annee){
			$anneeUniversitaire['COD_ANU']=$annee;
		}else{
			$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);			
		}

        $capacite = $em->getRepository(Stage::class)->ConventionParFiliere($annee);

        return new JsonResponse($capacite);
    }




    /**
     * @Route("/convention_imprimer", name="convention_imprimer")
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     */
    public function conventionImprimerAction(Pdf $knpSnappyPdf)
    {
		$em = $this->getDoctrine()->getManager('etudiant');
		$em1 = $this->getDoctrine()->getManager();
		$param= new ConfigExtension($em1);
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='".$param->app_config('ETA_ANU_IAE')."'");

		$conventions = $em->getRepository(Stage::class)->findby(array('statut' => '-2','niveau' => '1','anneeuniv' => $anneeUniversitaire['COD_ANU']));
        $formeEtudiant = array();
        $annestring='';
        if( $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") || $this->get('security.authorization_checker')->isGranted("ROLE_SERVICEEXT"))
        { 
            foreach ($conventions as $entity) {
            	$niveau = array('1', '2', '3');
            	$dip = str_replace($niveau, "", $entity->getFiliere());
            	$pos = strpos($entity->getFiliere(), '1');
            	if(strpos($entity->getFiliere(), '1') !== false){
            		$annestring='1er Année';
            	}elseif(strpos($entity->getFiliere(), '2')!==false ){
            		$annestring='2ème Année';
            	}else{
            		$annestring='3ème Année';
            	}
            	$filière = $em1->getRepository(Filiere::class)->findOneBy(array('codeApo' => $dip));
            	if($filière){
            		$formeEtudiant[$entity->getId()]=$annestring.'  cycle '.$filière->getCycle()->getNomCycle().', '.$filière->getNomFiliere();
            	}
                

            }

                $html = $this->renderView('stage/conventionpdf.html.twig', array(
                    'stage'  => $conventions,
                    'formeEtudiant'=> $formeEtudiant,
                    'base_dir' => $this->getParameter('kernel.project_dir')
                ));

                return new PdfResponse(
                    $knpSnappyPdf->getOutputFromHtml($html),
                    'conventions.pdf' ,
                );
                
            
        }else{
            return new RedirectResponse($this->generateUrl('statStage'));
        }
    }

    /**
     * @Route("/stageImportExport", name="stageImportExport")
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
    */
    public function stageImportExportAction()
    {
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		$em = $this->getDoctrine()->getManager('etudiant');

		$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivAll($conn);
        return $this->render('stage/importExport1.html.twig',['anneeUniversitaire' => $anneeUniversitaire]);
    }


    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     * @Route("/telechargerFileStage",name="telechargerFileStage")
     */
    public function telechargerFileStageAction(Request $request)
    {
        $searchParam = $request->get('importFile');

        $em = $this->getDoctrine()->getManager('etudiant');

        $objPHPExcel = new Spreadsheet();


        // Get the active sheet.
        $objPHPExcel->setActiveSheetIndex(0);


        $objPHPExcel->getProperties()
            ->setCreator("Abdessamad")
            ->setLastModifiedBy("Abdessamad")
            ->setTitle("listes des stages")
            ->setSubject("listes des stages")
            ->setDescription("description du fichier")
            ->setKeywords("creation  fichier excel phpexcel tutoriel");
        $sheet = $objPHPExcel->getActiveSheet();
        $j=3;
        $sheet->setCellValue('B2','Code Apogee');
        $sheet->setCellValue('C2','NOM');
        $sheet->setCellValue('D2','PRENOM');
        $sheet->setCellValue('E2','E-mail');
        $sheet->setCellValue('F2','N° Téléphone');
        $sheet->setCellValue('G2','Filière');
        $sheet->setCellValue('H2','Entreprise');
        $sheet->setCellValue('I2','Sujet');
        $sheet->setCellValue('J2','Date de début');
        $sheet->setCellValue('K2','Date de fin');
        $sheet->setCellValue('L2','Type');
        $sheet->setCellValue('M2','Statut');
        $sheet->setCellValue('N2','Niveau');
        $sheet->setCellValue('O2','Motif si refusé');

        $objPHPExcel->getActiveSheet()
            ->getStyle('B2'.':O2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('09594C');
        $styleA1=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('B2'.':O2')->applyFromArray($styleA1);

        $styleA2=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
            ),
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
        );
        $styleBordure1=array(
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
            'font'=>array(
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            )
        );



            $entities = $em->getRepository(Stage::class)->searchStageByFiliere($searchParam);

            
            foreach( $entities as $entity  ){
                if($entity->getUser()){
                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getColumnDimension('G')->setWidth(20);
                    $sheet->getColumnDimension('H')->setWidth(20);
                    $sheet->getColumnDimension('I')->setWidth(20);
                    $sheet->getColumnDimension('J')->setWidth(20);
                    $sheet->getColumnDimension('K')->setWidth(20);
                    $sheet->getColumnDimension('L')->setWidth(20);
                    $sheet->getColumnDimension('M')->setWidth(20);
                    $sheet->getColumnDimension('N')->setWidth(20);
                    $sheet->getColumnDimension('O')->setWidth(20);

                    $sheet->getStyle('B'.$j.':O'.$j)->applyFromArray($styleBordure1);
                    
                    $sheet->setCellValue('B'.$j,$entity->getUser()->getCode());
                    $sheet->setCellValue('C'.$j,$entity->getUser()->getNom());
                    $sheet->setCellValue('D'.$j,$entity->getUser()->getPrenom());
                    $sheet->setCellValue('E'.$j,$entity->getUser()->getEmail());
                    $sheet->setCellValue('F'.$j,$entity->getUser()->getPhone());
                    $sheet->setCellValue('G'.$j,$entity->getFiliere());
                    if($entity->getEntreprise()){
                        if($entity->getEntreprise()->getIntitule()=='Autre'){
                            $sheet->setCellValue('H'.$j,$entity->getIntitule());
                        }else{
                            $sheet->setCellValue('H'.$j,$entity->getEntreprise()->getIntitule());
                        }
                        
                    }
                    $sheet->setCellValue('I'.$j,"`".$entity->getSujet()."`");
                    $sheet->setCellValue('J'.$j,$entity->getDateDebut()->format("Y-m-d"));
                    $sheet->setCellValue('K'.$j,$entity->getDateFin()->format("Y-m-d"));
                    $sheet->setCellValue('L'.$j,$entity->getTypeStage()->getLibelle());
                    $sheet->setCellValue('M'.$j,$entity->getStatut());
                    $sheet->setCellValue('N'.$j,$entity->getNiveau());
                    $sheet->setCellValue('O'.$j,$entity->getMotif());
                    $j++;
                }

        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($objPHPExcel);
        
        // Create a Temporary file in the system
        $fileName = 'listes_stages.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }

    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     * @Route("/telechargerEtudiantDiplome",name="telechargerEtudiantDiplome")
     */
    public function telechargerEtudiantDiplomeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager('etudiant');

        $objPHPExcel = new Spreadsheet();


        // Get the active sheet.
        $objPHPExcel->setActiveSheetIndex(0);


        $objPHPExcel->getProperties()
            ->setCreator("Abdessamad")
            ->setLastModifiedBy("Abdessamad")
            ->setTitle("listes des Etudiants")
            ->setSubject("listes des Etudiants")
            ->setDescription("description du fichier")
            ->setKeywords("creation  fichier excel phpexcel tutoriel");
        $sheet = $objPHPExcel->getActiveSheet();
        $j=3;
        $sheet->setCellValue('B2','Code Apogee');
        $sheet->setCellValue('C2','NOM');
        $sheet->setCellValue('D2','PRENOM');
        $sheet->setCellValue('E2','E-mail');
        $sheet->setCellValue('F2','N° Téléphone');
        $sheet->setCellValue('G2','Filière');
        $sheet->setCellValue('H2','Date de demande');
        $sheet->setCellValue('I2','Date de Validation');

        $objPHPExcel->getActiveSheet()
            ->getStyle('B2'.':I2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('09594C');
        $styleA1=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('B2'.':I2')->applyFromArray($styleA1);

        $styleA2=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
            ),
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
        );
        $styleBordure1=array(
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
            'font'=>array(
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_CENTER
            )
        );

            $entities = $em->getRepository(EtuDiplomecarte::class)->findBy(array('type' =>'Diplome','decision' => 'Accepte'));
            foreach( $entities as $entity  ){

                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getColumnDimension('G')->setWidth(20);
                    $sheet->getColumnDimension('H')->setWidth(20);
                    $sheet->getColumnDimension('I')->setWidth(20);


                    $sheet->getStyle('B'.$j.':I'.$j)->applyFromArray($styleBordure1);
                    
                    $sheet->setCellValue('B'.$j,$entity->getCodeEtudiant()->getCode());
                    $sheet->setCellValue('C'.$j,$entity->getCodeEtudiant()->getNom());
                    $sheet->setCellValue('D'.$j,$entity->getCodeEtudiant()->getPrenom());
                    $sheet->setCellValue('E'.$j,$entity->getCodeEtudiant()->getEmail());
                    $sheet->setCellValue('F'.$j,$entity->getCodeEtudiant()->getPhone());
                    $sheet->setCellValue('G'.$j,$entity->getFiliere());
                    $sheet->setCellValue('H'.$j,$entity->getDatedemande()->format("d-m-Y H:i:s"));
                    $sheet->setCellValue('I'.$j,$entity->getDatevalidation()->format("d-m-Y H:i:s"));
                    $j++;

        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($objPHPExcel);
        
        // Create a Temporary file in the system
        $fileName = 'listes_diplome.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }

    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     * @Route("/exporterStatStage",name="exporterStatStage")
    */
    public function exporterStatStageAction(Request $request) {

    	$em = $this->getDoctrine()->getManager('etudiant');
    	$em1 = $this->getDoctrine()->getManager();
		$param= new ConfigExtension($em1);

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $searchParam = $request->get('searchParam');
        if($searchParam){
            extract($searchParam);
            if($searchParam['annee']){
                $anneeUniversitaire['COD_ANU']=$searchParam['annee'];
            }
           
        }else{
			$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	
		}
			
		$initiale = explode(",", $param->app_config('initiale'));


        $nbEtudiants= $em->getRepository(Etudiants::class)->nbEtudiantByGenreStage($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
		
		$nbGarçons = $em->getRepository(Etudiants::class)->nbEtudiantByGenreStage($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'M');

		$nbFilles = $em->getRepository(Etudiants::class)->nbEtudiantByGenreStage($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'F');


    	
		$nbAsDip = $em->getRepository(Etudiants::class)->nbAsDiplomeCooperation($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,$param->app_config('typeResultat'));

        $spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Statistiques");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getColumnDimension('A')->setWidth(40);

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
        $k="A";
        $i="A";
        $v="C";
        $total=0;
        if($nbAsDip){
            
            $worksheet->getStyle('A4:A7')->applyFromArray($styleArrayTitle1);
            $worksheet->getCell('A4')->setValue('Année Universitaire '.count($nbAsDip));
            $worksheet->getCell('A5')->setValue('Code Diplôme');
            $worksheet->getCell('A6')->setValue('effectifs');
            $worksheet->getCell('A7')->setValue('Total');
            $worksheet->getStyle('A4:A7')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('09594C');
        
        
        
            
        	$i=$k;
        	foreach ($nbAsDip as $diplome){
        		if($anneeUniversitaire['COD_ANU'] == $diplome["COD_ANU"]){
                    $k++;
        			$worksheet->getStyle($k."4".":".$k."6")->applyFromArray($styleArrayTitle);
        			$worksheet->getCell($k."5")->setValue($diplome["COD_DIP"]);
            		$worksheet->getCell($k."6")->setValue($diplome["NOMBRE"]);	
            		$total=$total+intval($diplome["NOMBRE"]);
        		}
        		
        	}
            $i++;
        	$v=$k;

        	$worksheet->mergeCells($i."4:".$v."4");
        	$worksheet->getCell($i."4")->setValue($anneeUniversitaire['COD_ANU']);
        	$worksheet->getStyle($i."4:".$v."4")->applyFromArray($styleArrayTitle2);

            $worksheet->mergeCells($i."7:".$v."7");
            $worksheet->getCell($i."7")->setValue($total);
            $worksheet->getStyle($i."7:".$v."7")->applyFromArray($styleArrayTitle2);
        
        }
        $worksheet->getStyle("A9:A12")->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('A9:A12')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
        $worksheet->getCell("A9")->setValue("Année en cours");
        $worksheet->getCell("A10")->setValue("Nombre diplômé");
        $worksheet->getCell("A11")->setValue("Garçons");
        $worksheet->getCell("A12")->setValue("Filles");
        $worksheet->getCell("B9")->setValue($anneeUniversitaire["COD_ANU"]);
        $worksheet->getCell("B10")->setValue($nbEtudiants[0]);
        $worksheet->getCell("B11")->setValue($nbGarçons[0]);
        $worksheet->getCell("B12")->setValue($nbFilles[0]);
        $worksheet->getStyle("B9:B12")->applyFromArray($styleArrayTitle);
        
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'statistiques.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/showEtudiantStage", name="showEtudiantStage")
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
    */
    public function showEtudiantStageAction(Request $request,secure $security,Connection $conn)
    {
        $searchParam = $request->get('importFiche');
        extract($searchParam);
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
		$param= new ConfigExtension($em1);
		
		
        if(!empty($codeApogee)){
			$usr = $em->getRepository(User::class)->findOneBy(array('codeEtudiant' => $codeApogee));
			$image = $em->getRepository(Image::class)->find($usr->getImage());
			$anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='".$param->app_config('ETA_ANU_IAE')."'");
			$diplomes1 = $em->getRepository(DiplomeCarte::class)->findby(array('type'=>'Diplome','decision' => '-1'));
			$conventions1 = $em->getRepository(Stage::class)->findby(array('statut' => '-2','niveau' => '1','anneeuniv' => $anneeUniversitaire['COD_ANU']));
			$relationEx = array('diplomes' => $diplomes1,'conventions' => $conventions1);
            $etudiant = $conn->fetchAssociative("SELECT * FROM individu i,adresse a WHERE i.COD_ETU='".$codeApogee."' and i.COD_IND = a.COD_IND ");
            $res_etudiant= $conn->fetchAllAssociative("select distinct(r.COD_ANU),r.COD_ETP,r.NOT_VET,r.COD_TRE
                               from ins_adm_etp ETP
                            left outer join resultat_vet r 
                                  on ETP.COD_ETP=r.COD_ETP
                            where  ETP.ETA_IAE='".$param->app_config('ETA_IAE')."'
                                and  ETP.COD_CMP='".$param->app_config('COD_CMP')."'
                                and  r.COD_IND='".$etudiant['COD_IND']."'
                                and r.COD_ADM=".$param->app_config('COD_ADM')." and r.COD_SES= ".$param->app_config('COD_SES')." 
                            order by r.COD_ANU desc");
			if($etudiant){
                $ins_Adm_E=$conn->fetchAllAssociative("SELECT * FROM ins_pedagogi_etp ie, annee_uni a, etape e WHERE ie.cod_anu=a.cod_anu and ie.cod_etp= e.cod_etp and ie.cod_ind='".$etudiant['COD_IND']."' order by ie.cod_anu desc");
                return $this->render('stage/infoEtudiant.html.twig', ['res_etudiant' => $res_etudiant,'etudiant' => $etudiant,'ins_Adm_E' => $ins_Adm_E,'image' => $image->getPath(),'relationEx' => $relationEx
                ]);
            }else{
                $this->get('session')->getFlashBag()->add('danger', "MOD_CODE_APOGEE_INCORECTE");
                $anneeUniversitaire=$conn->fetchAllAssociative("SELECT COD_ANU,ETA_ANU_IAE FROM annee_uni ORDER BY COD_ANU DESC");
                return $this->render('stage/importExport.html.twig',['anneeUniversitaire' => $anneeUniversitaire]);
            }
            

        }else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_CODE_APOGEE_INCORECTE");
            $anneeUniversitaire=$conn->fetchAllAssociative("SELECT COD_ANU,ETA_ANU_IAE FROM annee_uni ORDER BY COD_ANU DESC");
            return $this->render('stage/importExport.html.twig',['anneeUniversitaire' => $anneeUniversitaire]);
        } 
    }


    /**
     * @droitAcces("is_granted('ROLE_SERVICEEXT')")
     * @Route("/exporterEtudiantDD",name="exporterEtudiantDD")
     */
    public function exporterEtudiantDDAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager('etudiant');

        $objPHPExcel = new Spreadsheet();


        // Get the active sheet.
        $objPHPExcel->setActiveSheetIndex(0);


        $objPHPExcel->getProperties()
            ->setCreator("Abdessamad")
            ->setLastModifiedBy("Abdessamad")
            ->setTitle("listes des Etudiants")
            ->setSubject("listes des Etudiants")
            ->setDescription("description du fichier")
            ->setKeywords("creation  fichier excel phpexcel tutoriel");
        $sheet = $objPHPExcel->getActiveSheet();
        $j=3;
        $sheet->setCellValue('B2','Code Apogee');
        $sheet->setCellValue('C2','NOM');
        $sheet->setCellValue('D2','PRENOM');
        $sheet->setCellValue('E2','E-mail');
        $sheet->setCellValue('F2','N° Téléphone');
        $sheet->setCellValue('G2','Filière');
        $sheet->setCellValue('H2','Inscription');
        $sheet->setCellValue('I2','Etablissement');

        $objPHPExcel->getActiveSheet()
            ->getStyle('B2'.':I2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('09594C');
        $styleA1=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('B2'.':I2')->applyFromArray($styleA1);

        $styleA2=array(
            'font'=>array(
                'bold'=>true,
                'color'=>array('rgb'=>'FFFFFF'),
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_LEFT
            ),
            'fill' => array(
                'type' => Fill::FILL_SOLID,
                'color' => array('rgb' => '008fb3')
            ),
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
        );
        $styleBordure1=array(
            'borders' => array(
                'allborders'     => array(
                    'style' => Border::BORDER_THIN
                )
            ),
            'font'=>array(
                'name'=>'Times New Roman'
            ),
            'alignment'=>array(
                'horizontal'=>Alignment::HORIZONTAL_CENTER
            )
        );


            $config = new \Doctrine\DBAL\Configuration();
		    $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
            $annee=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	
	
            $entities = $em->getRepository(EtudiantDD::class)->searchEtudiantDDAnnee($annee['COD_ANU']);
            foreach( $entities as $entity  ){

                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getColumnDimension('G')->setWidth(20);
                    $sheet->getColumnDimension('H')->setWidth(20);


                    $sheet->getStyle('B'.$j.':I'.$j)->applyFromArray($styleBordure1);
                    
                    $sheet->setCellValue('B'.$j,$entity->getEtudiants()->getCode());
                    $sheet->setCellValue('C'.$j,$entity->getEtudiants()->getNom());
                    $sheet->setCellValue('D'.$j,$entity->getEtudiants()->getPrenom());
                    $sheet->setCellValue('E'.$j,$entity->getEtudiants()->getEmail());
                    $sheet->setCellValue('F'.$j,$entity->getEtudiants()->getPhone());
                    $sheet->setCellValue('G'.$j,$entity->getFiliere());

                    $inscription = $em->getRepository(InscritEtudiant::class)->findOneBy(array('inscription' => $entity,'annee'=>$annee['COD_ANU']));
                    if($inscription){
                        $sheet->setCellValue('H'.$j,'OUI');
                    }else{
                        $sheet->setCellValue('H'.$j,'NON');
                    }
                    $sheet->setCellValue('I'.$j,$entity->getConvention()->getEtablissement());
                    
                    $j++;

        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($objPHPExcel);
        
        // Create a Temporary file in the system
        $fileName = 'listes_etudiant_DD_'.$annee['COD_ANU'].'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }








    /**
     * @Route("/cvtheque_pdf/{id}", name="cvtheque_pdf")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_CHEF_FIL')")
     */
    public function cvtheque_pdf(Pdf $knpSnappyPdf , $id)
    {
  
        $em = $this->getDoctrine()->getManager('etudiant');
        $cv = $em->getRepository(Cvtheque::class)->findOneBy(array("idUser" => $id));
       

        $usr = $em->getRepository(Etudiants::class)->find($id);
        $image = $em->getRepository(image::class)->find($usr->getImage());

       // dd($image->getPath()) ;

        
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $etudiant = $em->getRepository(Etudiants::class)->etudiantByInd($usr->getCode(),$conn);

      
        $html = $this->renderView('stage/cv.html.twig', array(
            'cv'  => $cv,
            'formeEtudiant'=> null,
            'etudiant'=>$etudiant,
            'image' =>$image->getPath(),
            'base_dir' => $this->getParameter('kernel.project_dir')
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'CV-'.$usr->getNom().'_'.$usr->getPrenom().'.pdf' ,
        );





                
    }













    
}
