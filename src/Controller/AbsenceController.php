<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etudiant\Absence;
use App\Form\absenceType;
use App\Form\absenceImportType;
use App\Form\absenceEtudiantType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Connection;
use App\Entity\Customer\User;
use App\Entity\Etudiant\Etudiants;
use App\Entity\Etudiant\image;
use App\Twig\ConfigExtension;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces;

// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Service\InternetTest;
class AbsenceController extends AbstractController
{
    


  

    /**
     * @Route("/addAbsence", name="addAbsence")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $entity = new Absence();
        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $form = $this->createForm(absenceType::class, $entity,array('label' => $anneeUniversitaire['COD_ANU']));
        $form->handleRequest($request);
        $etudiants=[];
        if ($form->isSubmitted() && $form->isValid()) {

            $debut=$entity->getCodeapgeedebut() ? $entity->getCodeapgeedebut() : 'A';
            $fin=$entity->getCodeapogeefin() ? $entity->getCodeapogeefin() : 'ZZZ';
            $etudiants=$conn->fetchAllAssociative("SELECT DISTINCT(i.COD_ETU),i.LIB_NOM_PAT_IND AS NOM,i.LIB_PR1_IND AS PRENOM,etp.COD_ETP 
                                            FROM ins_adm_etp ins LEFT JOIN individu i ON (ins.COD_IND=i.COD_iND AND ins.ETA_IAE='E') LEFT JOIN ind_contrat_elp etp ON i.COD_IND=etp.COD_IND 
                                            WHERE  
                                                 etp.COD_ANU='".$anneeUniversitaire['COD_ANU']."'
                                                AND etp.COD_ETP='".$entity->getEtape()."'
                                                AND etp.COD_ELP='".$entity->getModule()."'
                                                
                                                AND i.LIB_NOM_PAT_IND >='".strtoupper($debut)."%'
                                                AND i.LIB_NOM_PAT_IND <='".strtoupper($fin)."%'
                                                AND etp.COD_FEX='N'
                                            ORDER BY i.LIB_NOM_PAT_IND ASC");
           return $this->render('absence/new.html.twig', array('entity' => $entity,'etudiants'=>$etudiants, 'form' => $form->createView()));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('absence/new.html.twig', array('entity' => $entity,'etudiants'=>$etudiants, 'form' => $form->createView()));
        }

        return $this->render('absence/new.html.twig', array('entity' => $entity,'etudiants'=>$etudiants, 'form' => $form->createView()));
    }

    /**
     * @Route("/add_list_ab", name="add_list_ab")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     */
    public function add_list_abAction(secure $security,Request $request)
    {
        $usr = $security->getUser();
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $em = $this->getDoctrine()->getManager('etudiant');
        $listes= explode(",",$request->query->get("liste"));
        $etape=$request->query->get("etape");
        $module=$request->query->get("module"); 
        $date=$request->query->get("date");
        $matiere=$request->query->get("matiere");
        $scance=$request->query->get("seance");
        foreach ($listes as $code) {

            $absence = new Absence();
            $etudiant = $em->getRepository(Etudiants::class)->findOneBy(array('code'=> $code));
            if($etudiant){
                $absence->setEtape($etape);
                $absence->setDateabsence(new \DateTime($date));
                $absence->setJustif(false);
                $absence->setModule($module);
                $absence->setMatiere($matiere);
                $absence->setSeance($scance);
                $absence->setIdprof($usr->getId());
                $absence->setIdUser($etudiant);
                $absence->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                $em->persist($absence);
            }
        }
        $em->flush();
        return new JsonResponse(true);
    }
    

     /**
     * @Route("/annul_list_ab", name="annul_list_ab")
     * @droitAcces("is_granted('ROLE_MANAGER') or is_granted('ROLE_DIRECTION')")
     */
    public function annul_list_abAction(secure $security,Request $request)
    {
        $usr = $security->getUser();
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $em = $this->getDoctrine()->getManager('etudiant');
        $listes= explode(",",$request->query->get("liste"));
        $etape=$request->query->get("etape");
        $module=$request->query->get("module");
        $matiere=$request->query->get("matiere");
        $date=$request->query->get("date");
        $scance=$request->query->get("seance");
        foreach ($listes as $code) {

            $etudiant = $em->getRepository(Etudiants::class)->findOneBy(array('code'=> $code));
            
            if($etudiant){
                $absence  = $em->getRepository(Absence::class)->findOneBy(array('idUser'=> $etudiant,'dateabsence' => new \DateTime($date),'etape' => $etape ,'module' => $module,'matiere' => $matiere, 'seance' => $scance,'anneeuniv' => $anneeUniversitaire['COD_ANU'],'idprof' => $usr->getId()));
                if($absence){
                    $em->remove($absence);
                }
                
            }
        }
        $em->flush();
        return new JsonResponse(true);
    }
    


    /**
     * @Route("/absence_list_module", name="absence_list_module")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     */
    public function listModuleOfEtapeAction(Request $request)
    {
        // Get Entity manager and repository
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $modules=$conn->fetchAllAssociative("select distinct(ic.COD_ELP) from ind_contrat_elp ic
                                                        left outer join element_pedagogi ep 
                                                            on ic.COD_ELP=ep.COD_ELP
                                                    where ic.cod_etp='".$request->query->get("etape")."' 
                                                        and (ep.COD_NEL='MOD' or ep.COD_NEL='MO')
                                                    order by ic.COD_ELP asc ");
        
        
        $responseModule = array();
        foreach ($modules as $choice) {
            $responseModule[] = array(
                "id" => $choice['COD_ELP']
                
            );
             
        }
    
        return new JsonResponse($responseModule);      
    }

     /**
     * @Route("/absence_list_matiere", name="absence_list_matiere")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     */
    public function listMatiereOfModuleAction(Request $request)
    {
        // Get Entity manager and repository
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $matieres=$conn->fetchAllAssociative("select  distinct(ep.LIB_ELP)
                                    from resultat_elp r,element_pedagogi ep, elp_regroupe_elp elp
                                    where  r.COD_ELP = ep.COD_ELP
                                        and ep.COD_ELP=elp.COD_ELP_FILS
                                        and elp.COD_ELP_PERE = '".$request->query->get("module")."'");
        
        
        $responseMatiere = array();
        foreach ($matieres as $matiere) {
            $responseMatiere[] = array(
                "id" => $matiere['LIB_ELP']
                
            );
             
        }
    
        return new JsonResponse($responseMatiere);      
    }

    /**
     * @Route("/absenceImportExport", name="absenceImportExport")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
    */
    public function absenceImportExportAction(Request $request,secure $security)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $etapes= $conn->fetchAllAssociative("select distinct(ins_adm_etp.COD_ETP)
                                        from ins_adm_etp
                                        where ins_adm_etp.COD_ANU='".$anneeUniversitaire['COD_ANU']."'
                                            AND ins_adm_etp.ETA_IAE='E'
                                            AND (ins_adm_etp.COD_ETP LIKE 'II%' OR ins_adm_etp.COD_ETP LIKE 'IM%')
                                            AND ins_adm_etp.COD_CMP='ENT'
                                        order by ins_adm_etp.COD_ETP ASC");
        $entity = new Absence();
        $form = $this->createForm(absenceImportType::class, $entity);
        $form->handleRequest($request);
        $newFilename=null;
        if ($form->isSubmitted() && $form->isValid()) { 

            $file = $form->get('fichier')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $originalFilename .'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('absence'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

               // $entity->setFichier($newFilename);
            }
            $user=$em->getRepository(Etudiants::class)->findOneBy(array('code' => $entity->getCodeapgeedebut()));
            $absences = $em->getRepository(Absence::class)->absenceByDate($user->getId(),$entity->getDatedebut()->format("Y-m-d"),$entity->getDatefin()->format("Y-m-d"));

            foreach ($absences as $abs) {
                $abs->setDatedebut($entity->getDatedebut());
                $abs->setDatefin($entity->getDatefin());
                $abs->setJustif(true);
                if($newFilename){
                    $abs->setFichier($newFilename);
                }
                $em->persist($abs);
            }

            $em->flush();   
            $this->get('session')->getFlashBag()->add('success', "Le certificat à été ajouter avec succes'.");
            return $this->render('absence/importExport1.html.twig', array('form' => $form->createView(),'etapes' =>$etapes));

        }
        if ($form->isSubmitted() && !$form->isValid()) {
          $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
          return $this->render('absence/importExport1.html.twig', array('form' => $form->createView(),'etapes' =>$etapes));
        }
        return $this->render('absence/importExport1.html.twig', array('form' => $form->createView(),'etapes' =>$etapes));
    }


    

    /**
     * @Route("/showEtudiantAbsence/{id}", name="showEtudiantAbsence")
     * @droitAcces("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
    */
    public function showEtudiantAbsenceAction(Request $request,secure $security,$id=null)
    {
            $em = $this->getDoctrine()->getManager('etudiant');
			$em1 = $this->getDoctrine()->getManager('default');
			$param= new ConfigExtension($em1);
			$usr = $em->getRepository(Etudiants::class)->find($id);
			$image = $em->getRepository(image::class)->find($usr->getImage());
			$user = $security->getUser();
			$config = new \Doctrine\DBAL\Configuration();
			$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
			$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

			$anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
			$etudiant = $em->getRepository(Etudiants::class)->etudiantByInd($usr->getCode(),$conn);
			$ins_Adm_E =  $em->getRepository(Etudiants::class)->insAdmLastByInd($etudiant["COD_IND"],$conn,$param->app_config('COD_CMP'),$param->app_config('ETA_IAE'));
			$ins_Peda_E = $em->getRepository(Etudiants::class)->insPedLastByInd($etudiant["COD_IND"],$conn,$anneeUniversitaire['COD_ANU']);		
			$groupe   = $em->getRepository(Etudiants::class)->getGroupeByInd($etudiant["COD_IND"],$anneeUniversitaire['COD_ANU'],$conn);
			$gr='';
			if($groupe){
				$gr=$groupe['COD_EXT_GPE'];
			}else{
				$gr=$ins_Adm_E[0]['COD_ETP'];
			}

			$initiale = explode(",", $param->app_config('initiale'));



            if ($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER')){
                $absences = $em->getRepository(Absence::class)->findby(array('idUser' => $usr,'anneeuniv' => $anneeUniversitaire['COD_ANU']));
            }else{
                $absences = $em->getRepository(Absence::class)->findby(array('idUser' => $usr,'anneeuniv' => $anneeUniversitaire['COD_ANU'],'idProf' => $user->getId()));
            }



			
			
			return $this->render('absence/infoEtudiant1.html.twig', ['absences' => $absences,'image' => $image->getPath(),'etudiant' => $etudiant,'ins_Adm_E' => $ins_Adm_E, 'ins_Peda_E' => $ins_Peda_E,'anneeUniversitaire'=> $anneeUniversitaire,'groupe' => $gr,
        		'base_dir' => $this->getParameter('kernel.project_dir') . '/../','user' =>$usr]);

    }


     /**
     * @Route("/absence_gestion", name="absence_gestion")
     * @droitAcces("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function absence_gestionAction(Request $request,secure $security)
    {

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $em = $this->getDoctrine()->getManager('etudiant');
        $user = $security->getUser();
        $absences=array();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER') and !$this->get('security.authorization_checker')->isGranted('ROLE_PROF')){
            $absences = $em->getRepository(Absence::class)->absenceByProf('',$anneeUniversitaire['COD_ANU']);
        }else{
            $absences = $em->getRepository(Absence::class)->absenceByProf($user->getId(),$anneeUniversitaire['COD_ANU']);
        }
        return $this->render('absence/gestionAbsence1.html.twig', array('absences' => $absences));

        
    }


    /**
     * @droitAcces("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     * @Route("/removeAbsence/{id}",name="removeAbsence")
     */

    public function removeAbsenceAction(Request $request,secure $security,$id)
    {
      
        $em = $this->getDoctrine()->getManager('etudiant');
        $user = $security->getUser();
        $entity=array();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER')){
            $entity = $em->getRepository(Absence::class)->find($id);
        }else{
            $entity = $em->getRepository(Absence::class)->findOneBy(array('id' =>$id,'idProf' =>$user->getId()));
        }
        if($entity){
            $file = $em->getRepository(Absence::class)->findBy(array('fichier' =>$entity->getFichier()));

            if(file_exists($this->getParameter('absence').$entity->getFichier()) && $entity->getFichier()!=null){
                unlink($this->getParameter('absence').$entity->getFichier());
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "L'absence a été supprimé avec succès.");
            return $this->redirectToRoute('showEtudiantAbsence', array('id' => $entity->getIdUser()->getId()));
        }else{
            $entity = $em->getRepository(Absence::class)->find($id);
            $this->get('session')->getFlashBag()->add('danger', "Merci de choisir id valide.");
            return $this->redirectToRoute('showEtudiantAbsence', array('id' => $entity->getIdUser()->getId()));
        }
    }
    /**
     * @Route("/edit_absence/{id}", name="edit_absence")
     * @droitAcces("is_granted('ROLE_MANAGER') or is_granted('ROLE_DIRECTION')")
     */
    public function editAction(Request $request,secure $security,$id)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $entity = $em->getRepository(Absence::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Absence entity.');
        }

        $editForm = $this->createForm(absenceEtudiantType::class, $entity);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "L'absence a été modifié avec succès.");
            return $this->render('absence/edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

            ));

        }

        if ($editForm->isSubmitted() && !$editForm->isValid()) {
          $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
            return $this->redirectToRoute('showEtudiantAbsence', array('id' => $entity->getIdUser()->getId())); 
        }
        return $this->render('absence/edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

            ));

    }

    /**
     * @Route("/statAbsence", name="statAbsence")
     * @droitAcces("is_granted('ROLE_MANAGER') or is_granted('ROLE_DIRECTION')")
     */
    public function statAbsenceAction(secure $security, Request $request)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $user = $security->getUser();
        
        if ($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER') or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $absenceModule = $em->getRepository(Absence::class)->absenceByProf('',$anneeUniversitaire['COD_ANU']);
            $absenceEtape = $em->getRepository(Absence::class)->absenceByEtape('',$anneeUniversitaire['COD_ANU']);
			$absencesJ = $em->getRepository(Absence::class)->findBy(array('justif' => 1));
			$absencesN = $em->getRepository(Absence::class)->findBy(array('justif' => 0));
        }else{
            $absenceEtape = $em->getRepository(Absence::class)->absenceByEtape($user->getId(),$anneeUniversitaire['COD_ANU']);

            $absenceModule = $em->getRepository(Absence::class)->absenceByProf($user->getId(),$anneeUniversitaire['COD_ANU']);
			$absencesJ = $em->getRepository(Absence::class)->findBy(array('idprof' =>$user->getId(),'justif' => 1));
			$absencesN = $em->getRepository(Absence::class)->findBy(array('idprof' =>$user->getId(),'justif' => 0));
        }

        


        return $this->render('absence/statistiques.html.twig',['totatesN' => count($absencesN),'totatesJ' => count($absencesJ),'etapes'=>$absenceEtape,'modules'=>$absenceModule]);
        


    }


    /**
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     * @Route("/telechargerAbsence",name="telechargerAbsence")
     */
    public function telechargerAbsenceAction(secure $security,Request $request)
    {
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $em = $this->getDoctrine()->getManager('etudiant');
        $user = $security->getUser();

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
        $sheet->setTitle('listes des Etudiants');
        $j=3;
        $sheet->setCellValue('B2','Code Apogee');
        $sheet->setCellValue('C2','NOM');
        $sheet->setCellValue('D2','PRENOM');
        $sheet->setCellValue('E2','E-mail');
        $sheet->setCellValue('F2','N° Téléphone');
        $sheet->setCellValue('G2','Etape');
        $sheet->setCellValue('H2','Module');
        $sheet->setCellValue('I2','Nombre d\'absence');

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

        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        if (($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER') and !$this->get('security.authorization_checker')->isGranted('ROLE_PROF')) or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ){
            $absenceModule = $em->getRepository(Absence::class)->absenceByProfAll('',$anneeUniversitaire['COD_ANU']);
        }else {
            $absenceModule = $em->getRepository(Absence::class)->absenceByProfAll($user->getId(),$anneeUniversitaire['COD_ANU']);
        }
            foreach( $absenceModule as $entity  ){

                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getColumnDimension('G')->setWidth(20);
                    $sheet->getColumnDimension('H')->setWidth(20);
                    $sheet->getColumnDimension('I')->setWidth(20);


                    $sheet->getStyle('B'.$j.':I'.$j)->applyFromArray($styleBordure1);
                    
                    $sheet->setCellValue('B'.$j,$entity[0]->getIdUser()->getCode());
                    $sheet->setCellValue('C'.$j,$entity[0]->getIdUser()->getNom());
                    $sheet->setCellValue('D'.$j,$entity[0]->getIdUser()->getPrenom());
                    $sheet->setCellValue('E'.$j,$entity[0]->getIdUser()->getEmail());
                    $sheet->setCellValue('F'.$j,$entity[0]->getIdUser()->getPhone());
                    $sheet->setCellValue('G'.$j,$entity[0]->getEtape());
                    $sheet->setCellValue('H'.$j,$entity[0]->getModule());
                    $sheet->setCellValue('I'.$j,$entity[1]);
                    $j++;

        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($objPHPExcel);
        
        // Create a Temporary file in the system
        $fileName = 'listes_absence.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }

    /**
     * @Route("/exporterListeEtudiant", name="exporterListeEtudiant")
     * @droitAcces("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
    */
    public function exporterListeEtudiantAction(Request $request,secure $security)
    {
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $searchParam = $request->get('importFiche');
        extract($searchParam);
        $etapes= $conn->fetchAllAssociative("select distinct(ins_adm_etp.COD_ETP)
                                        from ins_adm_etp
                                        where ins_adm_etp.COD_ANU='".$anneeUniversitaire['COD_ANU']."'
                                            AND ins_adm_etp.ETA_IAE='E'
                                            AND ins_adm_etp.COD_CMP='ENT'
											AND (ins_adm_etp.COD_ETP like 'II%' OR  ins_adm_etp.COD_ETP like 'IM%')
                                        order by ins_adm_etp.COD_ETP ASC");

        $modules = $conn->fetchAllAssociative("select distinct(ic.COD_ELP) from ind_contrat_elp ic
                                                        left outer join element_pedagogi ep 
                                                            on ic.COD_ELP=ep.COD_ELP
                                                    where ic.cod_etp='".$codeEtape."' 
                                                        and (ep.COD_NEL='MOD' or ep.COD_NEL='MO' )
                                                    order by ic.COD_ELP asc ");
        $objPHPExcel = new Spreadsheet();
        

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
        if(!empty($codeEtape)){
            $objPHPExcel = new Spreadsheet();
            // Get the active sheet.
            $objPHPExcel->getProperties()
                ->setCreator("Abdessamad")
                ->setLastModifiedBy("Abdessamad")
                ->setTitle("listes des Etudiants")
                ->setSubject("listes des Etudiants")
                ->setDescription("description du fichier")
                ->setKeywords("creation  fichier excel phpexcel tutoriel");

            $p=0;  
            $premfeuille = true;
            if($codeEtape=='Tous'){
                foreach ($etapes as $etape) {

                    if ($premfeuille == true)
                    {

                      $objPHPExcel->setActiveSheetIndex($p);   
                      $sheet = $objPHPExcel->getActiveSheet();             
                      $sheet->setTitle($etape['COD_ETP']);
                      $p++;
                      $premfeuille = false;
                    }
                    else
                    {
                      $sheet = $objPHPExcel->createSheet($p);
                      $objPHPExcel->setActiveSheetIndex($p);   
                      $sheet = $objPHPExcel->getActiveSheet();
                      $sheet ->setTitle($etape['COD_ETP']);
                      $p++;
                    }
     
                    $j=3;
                    $sheet->setCellValue('B2','Code Apogee');
                    $sheet->setCellValue('C2','NOM');
                    $sheet->setCellValue('D2','PRENOM');
                    $objPHPExcel->getActiveSheet()
                        ->getStyle('B2'.':D2')
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
                    $objPHPExcel->getActiveSheet()->getStyle('B2'.':D2')->applyFromArray($styleA1);
                    $etudiants=$conn->fetchAllAssociative("select i.COD_ETU,i.LIB_NOM_PAT_IND as NOM,i.LIB_PR1_IND as PRENOM,etp.COD_ETP 
                                                from individu i,ins_adm_etp etp  
                                                where i.COD_IND=etp.COD_IND
													AND etp.ETA_IAE='E'
													AND etp.COD_CMP='ENT'
                                                    AND etp.COD_ANU='".$anneeUniversitaire['COD_ANU']."'
                                                    AND etp.COD_ETP='".$etape['COD_ETP']."'
                                                order By i.LIB_NOM_PAT_IND asc");
                    foreach( $etudiants as $entity  ){

                        $sheet->getColumnDimension('B')->setWidth(20);
                        $sheet->getColumnDimension('C')->setWidth(20);
                        $sheet->getColumnDimension('D')->setWidth(20);


                        $sheet->getStyle('B'.$j.':D'.$j)->applyFromArray($styleBordure1);
                        
                        $sheet->setCellValue('B'.$j,$entity['COD_ETU']);
                        $sheet->setCellValue('C'.$j,$entity['NOM']);
                        $sheet->setCellValue('D'.$j,$entity['PRENOM']);
                        $j++;

                    }
                    

                }
            }else{
                foreach ($modules as $module) {

                    if ($premfeuille == true)
                    {

                      $objPHPExcel->setActiveSheetIndex($p);   
                      $sheet = $objPHPExcel->getActiveSheet();             
                      $sheet->setTitle($module['COD_ELP']);
                      $p++;
                      $premfeuille = false;
                    }
                    else
                    {
                      $sheet = $objPHPExcel->createSheet($p);
                      $objPHPExcel->setActiveSheetIndex($p);   
                      $sheet = $objPHPExcel->getActiveSheet();
                      $sheet ->setTitle($module['COD_ELP']);
                      $p++;
                    }
     
                    $j=3;
                    $sheet->setCellValue('B2','Code Apogee');
                    $sheet->setCellValue('C2','NOM');
                    $sheet->setCellValue('D2','PRENOM');
                    $objPHPExcel->getActiveSheet()
                        ->getStyle('B2'.':D2')
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
                    $objPHPExcel->getActiveSheet()->getStyle('B2'.':D2')->applyFromArray($styleA1);
                    $etudiants=$conn->fetchAllAssociative("select i.COD_ETU,i.LIB_NOM_PAT_IND as NOM,i.LIB_PR1_IND as PRENOM,etp.COD_ETP 
                                                from individu i,ind_contrat_elp etp  
                                                where i.COD_IND=etp.COD_IND
                                                    AND etp.COD_ANU='".$anneeUniversitaire['COD_ANU']."'
                                                    AND etp.COD_ETP='".$codeEtape."'
                                                    AND etp.COD_ELP='".$module['COD_ELP']."'
                                                    AND etp.COD_FEX='N'
                                                order By i.LIB_NOM_PAT_IND asc");
                    foreach( $etudiants as $entity  ){

                        $sheet->getColumnDimension('B')->setWidth(20);
                        $sheet->getColumnDimension('C')->setWidth(20);
                        $sheet->getColumnDimension('D')->setWidth(20);


                        $sheet->getStyle('B'.$j.':D'.$j)->applyFromArray($styleBordure1);
                        
                        $sheet->setCellValue('B'.$j,$entity['COD_ETU']);
                        $sheet->setCellValue('C'.$j,$entity['NOM']);
                        $sheet->setCellValue('D'.$j,$entity['PRENOM']);
                        $j++;

                    }
                    

                }

            }
            // Create your Office 2007 Excel (XLSX Format)
            $writer = new Xlsx($objPHPExcel);
            
            // Create a Temporary file in the system
            $fileName = 'listes_etiduants.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            
            // Create the excel file in the tmp directory of the system
            $writer->save($temp_file);
            
            // Return the excel file as an attachment
            return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

            }else{
                
                $this->get('session')->getFlashBag()->add('danger', "Merci de saisir code apogée valide  !");
                return $this->render('absence/importExport1.html.twig', array('form' => $form->createView(),'etapes' =>$etapes));
            } 
    }

     /**
     * @droitAcces("is_granted('ROLE_MANAGER') or is_granted('ROLE_DIRECTION')")
     * @Route("/absmois",name="absmois")
     */
    public function getAllAbsenceMoisAction(Request $request,secure $security) {

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$conn->fetchAssociative("SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'");
        $etapes= $conn->fetchAllAssociative("select distinct(ins_adm_etp.COD_ETP)
                                        from ins_adm_etp
                                        where ins_adm_etp.COD_ANU='".$anneeUniversitaire['COD_ANU']."'
                                            AND ins_adm_etp.ETA_IAE='E'
                                            AND ins_adm_etp.COD_CMP='ENT'
                                        order by ins_adm_etp.COD_ETP ASC");
        $em = $this->getDoctrine()->getManager('etudiant');
        $nbAbParFiliere =$em->getRepository(Absence::class)->nbAbsenceFiliereParMois($anneeUniversitaire['COD_ANU']);



        return new JsonResponse(array('absences' =>$nbAbParFiliere, 'etapes' =>$etapes));
    }

    

}
