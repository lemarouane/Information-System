<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces;
use Doctrine\DBAL\Connection;
use App\Entity\Etudiant\Etat;
use App\Entity\Etudiant\ChoixAffecter;
use App\Entity\Etudiant\Reclamation;
use App\Entity\Etudiant\Etudiants;

use App\Entity\Etudiant\ChoixOrientation;
use App\Entity\Config;
use App\Twig\ConfigExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
// Include JSON Response

use Symfony\Component\Mime\Address;
// Include PhpSpreadsheet required namespaces

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use App\Entity\Etudiant\AnneeUniversitaire ;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\InternetTest;

class OrientationController extends AbstractController
{

	/**
     * @Route("/Orientation", name="Orientation")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function parametreAction()
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        
        return $this->render('orientation/orientation.html.twig');

    }

    /**
     * @Route("/importOrientation", name="importOrientation")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function importAction()
    {

        return $this->render('orientation/importExport.html.twig');


    }

    /**
     * @droitAcces("is_granted('ROLE_ADMIN')")
     * @Route("/platformeIsActive",name="platformeIsActive")
     */
    public function platformeIsActiveAction(Request $request)
    {
 
        $checked = $request->get('doc');
        $em = $this->getDoctrine()->getManager();
        $this->get('session')->getFlashBag()->add('success', "MOD_PLATEFORME_OUVERTE");

        $em->getRepository(Config::class)->updateBy('plateforme_ouvert',$checked);
        return $this->redirectToRoute('Orientation');
    }

    /**
     * @droitAcces("is_granted('ROLE_ADMIN')")
     * @Route("/capaciteFiliere",name="capaciteFiliere")
     */
    public function capaciteFiliereAction(Request $request)
    {
        $searchParam = $request->get('searchParam');
        $em = $this->getDoctrine()->getManager('default');
        $em->getRepository(Config::class)->updateBy('capacite_g3ei',$searchParam['g3ei']);
        $em->getRepository(Config::class)->updateBy('capacite_gil',$searchParam['gil']);
        $em->getRepository(Config::class)->updateBy('capacite_ginf',$searchParam['ginf']);
        $em->getRepository(Config::class)->updateBy('capacite_gsea',$searchParam['gsea']);
        $em->getRepository(Config::class)->updateBy('capacite_gstr',$searchParam['gstr']);
        $em->getRepository(Config::class)->updateBy('capacite_gcys',$searchParam['gcys']);

        return $this->redirectToRoute('Orientation');
    }

    /**
     * @Route("/etatOrientation", name="etatOrientation")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function etatAction()
    {
	    
	        $em = $this->getDoctrine()->getManager('etudiant');
            $em1 = $this->getDoctrine()->getManager('default');
            $param= new ConfigExtension($em1);

	        $anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
	        $etats= $em->getRepository(Etat::class)->findBy(array("anneeuniv" => $anneeUniversitaire["COD_ANU"]));

            $config = new \Doctrine\DBAL\Configuration();
		    $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

            $nbEtudiantsAP2=$em->getRepository(Etudiants::class)->nbEtudiantAP2($anneeUniversitaire["COD_ANU"],$param->app_config('orientation_cod_etp'),$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$conn);

                                               
	        return $this->render('orientation/etat.html.twig', array('Totales' =>  $nbEtudiantsAP2['nb'], 'nbetat' => count($etats), 'etats' => $etats));
        

    }

    /**
     * @Route("/choixOrientation", name="choixOrientation")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function choixAction()
    {
	    
	        $em = $this->getDoctrine()->getManager('etudiant');
            $em1 = $this->getDoctrine()->getManager('default');
            $param= new ConfigExtension($em1);
	        $anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
	        $choix= $em->getRepository(ChoixOrientation::class)->findBy(array("anneeuniv" => $anneeUniversitaire["COD_ANU"]));

            $config = new \Doctrine\DBAL\Configuration();
		    $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

	        $nbEtudiantsAP2= $em->getRepository(Etudiants::class)->nbEtudiantAP2($anneeUniversitaire["COD_ANU"],$param->app_config('orientation_cod_etp'),$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$conn);


	        return $this->render('orientation/choix.html.twig', array('Totales' => $nbEtudiantsAP2['nb'], 'nbchoix' => count($choix), 'choix' => $choix));
        

    }

    /**
     * @Route("/statistiquesOrientation/{annee}", name="statistiquesOrientation")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR')  or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ')")
     */
    public function statistiquesOrienAction($annee=null)
    {
    	
	        $em = $this->getDoctrine()->getManager('etudiant');
            $em1 = $this->getDoctrine()->getManager('default');
            $param= new ConfigExtension($em1);
			if($annee){
				$anneeUniversitaire["COD_ANU"]=$annee;
			}else{
				$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
				$anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
			}

	        $etat= $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));
	        $choix= $em->getRepository(ChoixOrientation::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));

            $config = new \Doctrine\DBAL\Configuration();
		    $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

	        $nbEtudiantsAP2= $em->getRepository(Etudiants::class)->nbEtudiantAP2($anneeUniversitaire["COD_ANU"],$param->app_config('orientation_cod_etp'),$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$conn);

			$nbGarçonsAP2 = $em->getRepository(Etudiants::class)->nbEtudiantAP2_Garcon($anneeUniversitaire["COD_ANU"],$param->app_config('orientation_cod_etp'),$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$conn);

			$nbFillesAP2 = $em->getRepository(Etudiants::class)->nbEtudiantAP2_Fille($anneeUniversitaire["COD_ANU"],$param->app_config('orientation_cod_etp'),$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$conn);


			

        	
            

	        return $this->render('orientation/statistiques.html.twig' , array('Totales' => $nbEtudiantsAP2['nb'], 
					        	'garçons' => $nbGarçonsAP2['nb'], 
					        	'filles' => $nbFillesAP2['nb'],
					        	'etat' => count($etat) ,
					        	'choix' => count($choix)));
	    

    }

    /**
     * @Route("/getPremierchoixAffecter", name="getPremierchoixAffecter")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR')  or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ')")
     */
    public function getPremierchoixAffecter($annee=null)
    {     
        $em = $this->getDoctrine()->getManager('etudiant');  
        if($annee){
            $anneeUniversitaire["COD_ANU"]=$annee;
        }else{
            $anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
        } 
        
        $arrayFiliere =  array('G3EI','GIND','GINF','GSEA','GSTR','GCYS');
		$dataChoix1=array();
	    $data_Choix1 = $em->getRepository(ChoixOrientation::class)->getPremierchoixAffecter($anneeUniversitaire['COD_ANU']);
	    for ($p = 0; $p < 6; $p++) {
	        for($m = 0; $m < count($data_Choix1) ; $m++){
	            if(!empty($data_Choix1[$m]['choix']) && $data_Choix1[$m]['choix']== $arrayFiliere[$p]){
	                $dataChoix1[$arrayFiliere[$p]]=$data_Choix1[$m]['c'];
	                break;
	            }else{
	                $dataChoix1[$arrayFiliere[$p]]=0;

	            }
	        }
	            
	    }
        return new JsonResponse($dataChoix1);
    }

    /**
     * @Route("/getCapaciteFiliere", name="getCapaciteFiliere")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR')  or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ')")
     */
    public function getCapaciteFiliere($annee=null)
    {     

        $em1 = $this->getDoctrine()->getManager('default');
        $param= new ConfigExtension($em1);
        $arrayFiliere =  array('G3EI' => $param->app_config('capacite_g3ei'),'GIND' => $param->app_config('capacite_gil'),'GINF' => $param->app_config('capacite_ginf'),'GSEA' => $param->app_config('capacite_gsea'),'GSTR' => $param->app_config('capacite_gstr'),'GCYS' => $param->app_config('capacite_gcys'));

        return new JsonResponse($arrayFiliere);
	    
    }

    /**
     * @Route("/getchoixFiliere_50", name="getchoixFiliere_50")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR')  or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ')")
     */
    public function getchoixFiliere_50($annee=null)
    {     
        $arrayFiliere =  array('G3EI','GIND','GINF','GSEA','GSTR','GCYS');
        $em = $this->getDoctrine()->getManager('etudiant');
        if($annee){
            $anneeUniversitaire["COD_ANU"]=$annee;
        }else{
            $anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
        } 
        $data_50 = $em->getRepository(ChoixOrientation::class)->getchoixFiliere_50($anneeUniversitaire['COD_ANU']);
        $d_50 = array();
        $dataset_50  = array();
            // choix
   
        for ($i = 0; $i < count($data_50); $i++) {
            for ($j = 0; $j < 6; $j++) {
                        
                if($data_50[$i]['choix'] == $arrayFiliere[$j]){
                    $d_50[$arrayFiliere[$j]][$data_50[$i]['num']] = $data_50[$i]['c'];
                                
                }

            }
        }            
        for ($k = 0; $k < 6; $k++) {
            for ($l = 1; $l <= 6; $l++) {  
                if(empty($d_50[$arrayFiliere[$k]][$l])){
                    $dataset_50[$arrayFiliere[$k]][$l] = 0;       
                }else{
                    $dataset_50[$arrayFiliere[$k]][$l] = $d_50[$arrayFiliere[$k]][$l];   
                }
            }

        }
        return new JsonResponse($dataset_50);
	    
    }

    /**
     * @Route("/getchoixFiliere", name="getchoixFiliere")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR')  or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ')")
     */
    public function getchoixFiliere($annee=null)
    {     
        $arrayFiliere =  array('G3EI','GIND','GINF','GSEA','GSTR','GCYS');
        $em = $this->getDoctrine()->getManager('etudiant');
        if($annee){
            $anneeUniversitaire["COD_ANU"]=$annee;
        }else{
            $anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
        } 
        $data = $em->getRepository(ChoixOrientation::class)->getchoixFiliere($anneeUniversitaire['COD_ANU']);
        $d = array();
        $dataset  = array();
            // choix
   
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < 6; $j++) {
                        
                if($data[$i]['choix'] == $arrayFiliere[$j]){
                    $d[$arrayFiliere[$j]][$data[$i]['num']] = $data[$i]['c'];
                                
                }

            }
        }            
        for ($k = 0; $k < 6; $k++) {
            for ($l = 1; $l <= 6; $l++) {  
                if(empty($d[$arrayFiliere[$k]][$l])){
                    $dataset[$arrayFiliere[$k]][$l] = 0;       
                }else{
                    $dataset[$arrayFiliere[$k]][$l] = $d[$arrayFiliere[$k]][$l];   
                }
            }

        }
        return new JsonResponse($dataset);
	    
    }

     /**
     * @Route("/importFileExcelOrientation", name="importFileExcelOrientation")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */

   public function importFileExcelAction(Request $request, Connection $conn)
    {


            $em = $this->getDoctrine()->getManager('etudiant');

            $anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
            if (!empty($_FILES['file'])) {

                //UPLOAD DU FICHIER CSV, vérification et insertion en BASE
                if (isset($_FILES["file"]["type"]) != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                    die("Ce n'est pas un fichier de type .xslx");
                } elseif (is_uploaded_file($_FILES['file']['tmp_name'])) {

                    $inputFile = $_FILES['file']['tmp_name'];
                    $reader = new XlsxReader();
                    $objPHPExcel = $reader->load($inputFile);
                    // on séléctionne le bonne feuille du document
                    $sheet = $objPHPExcel->getSheet(0);
                    
                    // on sauvegarde le nombre de lignes du document.
                    $highestRow = $sheet->getHighestRow();
                    // on sauvegarde le nombre d colonnes du document.
                    $highestColumn = 'P';

                    for ($row = 8; $row <= $highestRow; $row++)
                    {
                        set_time_limit(300);
                        // On range la ligne dans l'ordre 'normal'
                        $rowData = $sheet->rangeToArray('D' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
                        // rowData est un tableau contenant les données de la ligne
                        $rowData = $rowData[0];
                        
                        
                        	$moy_Cal= (0.4 * ($rowData[3] - (0.25 * $rowData[4]) - (0.5 * $rowData[5]) - (1 * $rowData[6]))) + (0.6 * ($rowData[8] - (0.25 * $rowData[9]) - (0.5 * $rowData[10]) - (1 * $rowData[11])));
                        	$etat= $em->getRepository(Etat::class)->findOneBy(array('anneeuniv' => $anneeUniversitaire["COD_ANU"],'codetudiant'=>$rowData[0]));
                            $user = $em->getRepository(Etudiants::class)->findOneBy(array('code' => $rowData[0]));


                        	if(empty($etat)){
	                            $etat1 = new Etat();
	                            $etat1->setCodetudiant($rowData[0]);
	                            $etat1->setMCP1($rowData[3]);
	                            $etat1->setRACHCP1($rowData[4]);
	                            $etat1->setAJRCP1($rowData[5]);
	                            $etat1->setDERCP1($rowData[6]);
	                            $etat1->setMCP2($rowData[8]);
                                $etat1->setUser($user);
	                            $etat1->setRACHCP2($rowData[9]);
	                            $etat1->setAJRCP2($rowData[10]);
	                            $etat1->setDERCP2($rowData[11]);
	                            $etat1->setMcal(number_format($moy_Cal,3));
								$etat1->setCCHOIX(0);
								$etat1->setERESULTAT(0);
	                            $etat1->setAnneeuniv($anneeUniversitaire["COD_ANU"]);
	                            $em->persist($etat1);

	                        }else{
	                            $etat->setMCP1($rowData[3]);
	                            $etat->setRACHCP1($rowData[4]);
	                            $etat->setAJRCP1($rowData[5]);
	                            $etat->setDERCP1($rowData[6]);
	                            $etat->setMCP2($rowData[8]);
                                $etat->setUser($user);
	                            $etat->setRACHCP2($rowData[9]);
	                            $etat->setAJRCP2($rowData[10]);
	                            $etat->setDERCP2($rowData[11]);
								$etat->setCCHOIX(0);
								$etat->setERESULTAT(0);
	                            $etat->setAnneeuniv($anneeUniversitaire["COD_ANU"]);
	                            $etat->setMcal(number_format($moy_Cal,3));
	                            $em->persist($etat);
	                        }

                       
  
                    }
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('success', "MOD_FICHIER_DEPLOYE");
                    return $this->render('orientation/importExport.html.twig');
                } else {
                 	$this->get('session')->getFlashBag()->add('danger', "MOD_FICHIER_NON_DEPLOYE");
                 	return $this->render('orientation/importExport.html.twig');
                }
            }else{
                $this->get('session')->getFlashBag()->add('danger', "MOD_FICHIER_NON_EXIST");
                return $this->render('orientation/importExport.html.twig');
            }

    }

    /**
     * @Route("/listEtudiantChoix",name="listEtudiantChoix")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function listEtudiantChoixAction(Request $request, Connection $conn)
    {
        $em = $this->getDoctrine()->getManager('etudiant');      
		$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
        $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
        $etats= $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));
        $listEtats=array();
        foreach ($etats as $etat) {
            $choix = $em->getRepository(ChoixOrientation::class)->findOneBy(array('codeEtudiant' => $etat->getCodetudiant()));
            $user = $em->getRepository(Etudiants::class)->findOneBy(array('code' => $etat->getCodetudiant()));
            if(empty($choix)){
                if(!empty($user)){
                    
                    array_push($listEtats,$user);
                }  
            }   
        } 
        $response = $this->render('orientation/etudiantChoixNon.csv.twig',array(
                    'entities' => $listEtats,
                    ));

         $response->headers->set('Content-Type', 'text/csv');
         $response->headers->set('Content-Disposition', 'attachment; filename="etudiantChoixNon.csv"');

        return $response;
        
        
    }

    /**
     * @Route("/envoyerLeursChoix",name="envoyerLeursChoix")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function envoyerLeursChoixAction(MailerInterface $mailer , InternetTest $int)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
		$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
        $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
        $etats = $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU'],'cCHOIX' => 0));
        $i=0;
        foreach ($etats as $etat) {
            if($etat->getCCHOIX()==0 && $i<50){
                if($etat->getUser()){
                    if($etat->getUser()->getEmail()){
                        $html=$this->renderView('orientation/emailOrientationEnvoie.html.twig',array(
                            'etat' => $etat));
                        
                        $message = (new TemplatedEmail())
                            ->from(new Address('gcvre@uae.ac.ma', 'Orientation'))
                            ->to($etat->getUser()->getEmail()."")
                            ->subject('Orientation : Verification et Envoie de Choix')
                            ->html($html)
                            ;
                        try {
                             if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
                        } catch (TransportExceptionInterface $e) {
                        
                        }
                    	$etat->setCCHOIX(1);
                    	$em->persist($etat);
                        $i++;
                	}
                }  
            }   
        } 
        $em->flush();
       	return new RedirectResponse($this->generateUrl('Orientation'));   
    }

    /**
     * @Route("/rappelEnvoyeChoix",name="rappelEnvoyeChoix")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function rappelEnvoyeChoixAction(MailerInterface $mailer , InternetTest $int)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
		$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
        $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
        $etats = $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));
        $i=0;
        foreach ($etats as $etat) {
            $choix = $em->getRepository(ChoixOrientation::class)->findOneBy(array('codeEtudiant' => $etat->getCodetudiant()));
            if(empty($choix)){
                if($etat->getUser()){
                    if($etat->getUser()->getEmail()){

                        $html=$this->renderView('orientation/emailOrientationEnvoie.html.twig',array(
                            'etat' => $etat));
                        
                        $message = (new TemplatedEmail())
                            ->from(new Address('gcvre@uae.ac.ma', 'Orientation'))
                            ->to($etat->getUser()->getEmail()."")
                            ->subject('Orientation : Rappel d\'envoie de Choix')
                            ->html($html)
                            ;
                        try {
                             if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
                        } catch (TransportExceptionInterface $e) {
                        
                        }
                    	
                        $i++;
                        
                    }
                }  
            }   
        } 
        $em->flush();
        return new RedirectResponse($this->generateUrl('Orientation'));
    }

    /**
     * @Route("/envoyerChoix",name="envoyerChoix")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function envoyerChoixAction(MailerInterface $mailer , InternetTest $int)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
		$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
      $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
        $choixs = $em->getRepository(ChoixOrientation::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU'],'cCHOIX' => 0),array('id' => 'ASC'));
        $j=0;
        foreach ($choixs as $choix) {
            if($choix->getCCHOIX()==0){
                if($j<50){
                    if($choix->getUser()->getEmail()){

                        $html=$this->renderView('orientation/emailconfirmationChoix.html.twig',array(
                            'choix' => $choix));
                        
                        $message = (new TemplatedEmail())
                            ->from(new Address('gcvre@uae.ac.ma', 'Orientation'))
                            ->to($choix->getUser()->getEmail()."")
                            ->subject('Orientation : Verification de Choix')
                            ->html($html)
                            ;
                        try {
                             if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
                        } catch (TransportExceptionInterface $e) {
                        
                        }
 
                        $choix->setCCHOIX(1);
                        $em->persist($choix);
                
                    } 
                    $j++; 
                }else{
                    break;
                }
            }  
        } 
        $em->flush();
        return new RedirectResponse($this->generateUrl('Orientation')); 
    }

    
    /**
     * @Route("/orientationEtat", name="orientationEtat")
     * @droitAcces("is_granted('ROLE_ADMIN')")
    */
    public function orientation()
    {
    	
			$em = $this->getDoctrine()->getManager('etudiant');
            $em1 = $this->getDoctrine()->getManager('default');
            $param= new ConfigExtension($em1);

            $config = new \Doctrine\DBAL\Configuration();
		    $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

			$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();

			$iiap2Liste = $em->getRepository(Etudiants::class)->iiap2Liste_valide($anneeUniversitaire["COD_ANU"],$param->app_config('orientation_cod_etp'),$conn);

            if(!empty($iiap2Liste)){                                 
                foreach ($iiap2Liste as $iiap2) {
                    $etat =new Etat();
                    $resEtudiant=$em->getRepository(Etudiants::class)->resEtudiant($iiap2['COD_IND'],$conn);

                    $user = $em->getRepository(Etudiants::class)->findOneBy(array('code' => $iiap2['COD_ETU']));
                    if(!empty($user)){
                        if(!empty($resEtudiant)){
                            $etudiant = $em->getRepository(Etat::class)->findOneBy(array('codetudiant' => $resEtudiant[0]['COD_ETU']));
                            
                            if($etudiant){
                                foreach ($resEtudiant as $res) {

                                    if($res['COD_ETP']=='IIAP2'){
                                        if($res['COD_TRE']=='ADMR' || $res['COD_TRE']=='ADM'){
                                            if($res['COD_TRE']=='ADM'){
                                                $etudiant->setMCP2(floatval(str_replace(',','.',$res['NOT_VET'])));
                                            }else{
                                                $etudiant->setMCP2(floatval(str_replace(',','.',$res['NOT_VET'])));
                                                $etudiant->setRACHCP2(1);
                                            }

                                        }
                                        if($res['COD_TRE']=='ROR' || $res['COD_TRE']=='AJ'){

                                            if($res['COD_TRE']=='ROR'){
                                                $etudiant->setDERCP2(1);
                                                $etudiant->setAJRCP2(1);
                                            }else{
                                                $etudiant->setAJRCP2(1);
                                            }
                                        }

                                    }elseif($res['COD_ETP']=='IIAP1'){
                                        if($res['COD_TRE']=='ADMR' || $res['COD_TRE']=='ADM'){
                                            if($res['COD_TRE']=='ADM'){
                                                $etudiant->setMCP1(floatval(str_replace(',','.',$res['NOT_VET'])));
                                            }else{
                                                $etudiant->setMCP1(floatval(str_replace(',','.',$res['NOT_VET'])));
                                                $etudiant->setRACHCP1(1);
                                            }

                                        }
                                        if($res['COD_TRE']=='ROR' || $res['COD_TRE']=='AJ'){

                                            if($res['COD_TRE']=='ROR'){
                                                $etudiant->setDERCP1(1);
                                                $etudiant->setAJRCP1(1);
                                            }else{
                                                $etudiant->setAJRCP1(1);
                                            }
                                        }
                                    
                                    }
                                    $noteCalculer= (0.4 * ($etudiant->getMCP1() - (0.25 * $etudiant->getRACHCP1()) - (0.5 * $etudiant->getAJRCP1()) - (1 * $etudiant->getDERCP1()))) + (0.6 * ($etudiant->getMCP2() - (0.25 * $etudiant->getRACHCP2()) - (0.5 * $etudiant->getAJRCP2()) - (1 * $etudiant->getDERCP2())));
                                    $etudiant->setMcal(number_format($noteCalculer,3));
                                    $etudiant->setUser($user);
                                    $em->persist($etudiant);
                                }
                            }else{
                                $etat->setCodetudiant($resEtudiant[0]['COD_ETU']);
                                $etat->setRACHCP2(0);
                                $etat->setDERCP2(0);
                                $etat->setAJRCP2(0);
                                $etat->setRACHCP1(0);
                                $etat->setDERCP1(0);
                                $etat->setAJRCP1(0);
                                foreach ($resEtudiant as $res) {

                                    if($res['COD_ETP']=='IIAP2'){
                                        if($res['COD_TRE']=='ADMR' || $res['COD_TRE']=='ADM'){
                                            if($res['COD_TRE']=='ADM'){
                                                $etat->setMCP2(floatval(str_replace(',','.',$res['NOT_VET'])));
                                            }else{
                                                $etat->setMCP2(floatval(str_replace(',','.',$res['NOT_VET'])));
                                                $etat->setRACHCP2(1);
                                            }

                                        }
                                        if($res['COD_TRE']=='ROR' || $res['COD_TRE']=='AJ'){

                                            if($res['COD_TRE']=='ROR'){
                                                $etat->setDERCP2(1);
                                                $etat->setAJRCP2(1);
                                            }else{
                                                $etat->setAJRCP2(1);
                                            }
                                        }

                                    }elseif($res['COD_ETP']=='IIAP1'){
                                        if($res['COD_TRE']=='ADMR' || $res['COD_TRE']=='ADM'){
                                            if($res['COD_TRE']=='ADM'){
                                                $etat->setMCP1(floatval(str_replace(',','.',$res['NOT_VET'])));
                                            }else{
                                                $etat->setMCP1(floatval(str_replace(',','.',$res['NOT_VET'])));
                                                $etat->setRACHCP1(1);
                                            }

                                        }
                                        if($res['COD_TRE']=='ROR' || $res['COD_TRE']=='AJ'){

                                            if($res['COD_TRE']=='ROR'){
                                                $etat->setDERCP1(1);
                                                $etat->setAJRCP1(1);
                                            }else{
                                                $etat->setAJRCP1(1);
                                            }
                                        }
                                    
                                    }
                                    $noteCalculer= (0.4 * ($etat->getMCP1() - (0.25 * $etat->getRACHCP1()) - (0.5 * $etat->getAJRCP1()) - (1 * $etat->getDERCP1()))) + (0.6 * ($etat->getMCP2() - (0.25 * $etat->getRACHCP2()) - (0.5 * $etat->getAJRCP2()) - (1 * $etat->getDERCP2())));
                                    $etat->setMcal(number_format($noteCalculer,3));
                                    $etat->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                                    $etat->setUser($user);
                                    $etat->setCCHOIX(0);
                                    $etat->setERESULTAT(0);
                                    $em->persist($etat);
                                }
                            }
                        }else{
                            $etudiant1 = $em->getRepository(Etat::class)->findOneBy(array('codetudiant' => $iiap2['COD_ETU']));
                            
                            if(empty($etudiant1)){
                                $etat->setCodetudiant($iiap2['COD_ETU']);
                                $etat->setRACHCP2(0);
                                $etat->setDERCP2(0);
                                $etat->setAJRCP2(0);
                                $etat->setRACHCP1(0);
                                $etat->setDERCP1(0);
                                $etat->setAJRCP1(0);
                                $etat->setUser($user);
                                $etat->setMcal(0);
                                $etat->setCCHOIX(0);
                                $etat->setERESULTAT(0);
                                $etat->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                                $em->persist($etat);
                            }else{
                                $etudiant1->setCodetudiant($iiap2['COD_ETU']);
                                $etudiant1->setRACHCP2(0);
                                $etudiant1->setDERCP2(0);
                                $etudiant1->setAJRCP2(0);
                                $etudiant1->setRACHCP1(0);
                                $etudiant1->setDERCP1(0);
                                $etudiant1->setAJRCP1(0);
                                $etudiant1->setUser($user);
                                $etudiant1->setMcal(0);
                                $etudiant1->setCCHOIX(0);
                                $etudiant1->setERESULTAT(0);
                                $etudiant1->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                                $em->persist($etudiant1);
                            }
                            
                        }
                    }
                }
            }
	        $em->flush();
			return new RedirectResponse($this->generateUrl('importOrientation')); 
		
	}




	/**
     * @droitAcces("is_granted('ROLE_ADMIN')")
     * @Route("/affectationAutomatique",name="affectationAutomatique")
    */
    public function affectationAutomatiqueAction(Request $request, Connection $conn)
    {
 
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
		$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
        $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
		$orientations = $em->getRepository(ChoixOrientation::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));
        $etats = $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']), array('mcal' => 'desc'));
/*        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['ETUDIANT_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);*/
		$param= new ConfigExtension($em1);

        
        $arrayFiliereCapacite =  array('G3EI' => $param->app_config('capacite_g3ei'), 'GIND' => $param->app_config('capacite_gil'),'GINF' => $param->app_config('capacite_ginf'),'GSEA' => $param->app_config('capacite_gsea'),'GSTR' => $param->app_config('capacite_gstr'),'GCYS' => $param->app_config('capacite_gcys'));
        
        
        $keyF='';
        $valueF=0;

        foreach($etats as $etat)
        {
            $affectation = $em->getRepository(ChoixAffecter::class)->findOneBy(array('etat' => $etat));
            foreach($orientations as $orientation)
            {set_time_limit(300);
                if(empty($affectation)){
                    $choixAffecter = new ChoixAffecter();
					//$choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                    if($etat->getCodetudiant() == $orientation->getCodeetudiant()){
                        
                        if($arrayFiliereCapacite[$orientation->getChoix1()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix1()]--;
                            $choixAffecter->setAffectation($orientation->getChoix1());
                            $choixAffecter->setEtat($etat);
                            $choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                            $em->persist($choixAffecter);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix2()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix2()]--;
                            $choixAffecter->setAffectation($orientation->getChoix2());
                            $choixAffecter->setEtat($etat);
                            $choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                            $em->persist($choixAffecter);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix3()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix3()]--;
                            $choixAffecter->setAffectation($orientation->getChoix3());
                            $choixAffecter->setEtat($etat);
                            $choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                            $em->persist($choixAffecter);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix4()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix4()]--;
                            $choixAffecter->setAffectation($orientation->getChoix4());
                            $choixAffecter->setEtat($etat);
                            $choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                            $em->persist($choixAffecter);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix5()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix5()]--;
                            $choixAffecter->setAffectation($orientation->getChoix5());
                            $choixAffecter->setEtat($etat);
                            $choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                            $em->persist($choixAffecter);

                            break;

                        }else{

                            $arrayFiliereCapacite[$orientation->getChoix6()]--;
                            $choixAffecter->setAffectation($orientation->getChoix6());
                            $choixAffecter->setEtat($etat);
                            $choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                            $em->persist($choixAffecter);

                            break;

                        }
                    }else{

                        $orient = $em->getRepository(ChoixOrientation::class)->findOneBy(array('codeEtudiant' => $etat->getCodetudiant()));
                        if(empty($orient)){
                            $valueF = max($arrayFiliereCapacite);
                            $keyF   = array_search($valueF , $arrayFiliereCapacite);

                            $arrayFiliereCapacite[$keyF]--;
                            $choixAffecter->setAffectation($keyF);
                            $choixAffecter->setEtat($etat);
                            $choixAffecter->setAnneeuniv($anneeUniversitaire['COD_ANU']);
                            $em->persist($choixAffecter);

                            break;
                        }   
                    }
                }else{
                    if($etat->getCodetudiant() == $orientation->getCodeetudiant()){
                       
                        if($arrayFiliereCapacite[$orientation->getChoix1()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix1()]--;
                            $affectation->setAffectation($orientation->getChoix1());
                            $em->persist($affectation);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix2()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix2()]--;
                            $affectation->setAffectation($orientation->getChoix2());
                            $em->persist($affectation);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix3()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix3()]--;
                            $affectation->setAffectation($orientation->getChoix3());
                            $em->persist($affectation);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix4()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix4()]--;
                            $affectation->setAffectation($orientation->getChoix4());
                            $em->persist($affectation);

                            break;

                        }elseif($arrayFiliereCapacite[$orientation->getChoix5()]>0){

                            $arrayFiliereCapacite[$orientation->getChoix5()]--;
                            $affectation->setAffectation($orientation->getChoix5());
                            $em->persist($affectation);

                            break;

                        }else{

                            $arrayFiliereCapacite[$orientation->getChoix6()]--;
                            $affectation->setAffectation($orientation->getChoix6());
                            $em->merge($affectation);

                            break;

                        }
                    }else{

                        $orient1 = $em->getRepository(ChoixOrientation::class)->findOneBy(array('codeEtudiant' => $etat->getCodetudiant()));
                        if(empty($orient1)){
                            $valueF = max($arrayFiliereCapacite);
                            $keyF   = array_search($valueF , $arrayFiliereCapacite);

                            $arrayFiliereCapacite[$keyF]--;
                            $affectation->setAffectation($keyF);
                            $em->persist($affectation);

                            break;
                        }  
                        
                    }
                }
            }
        }
        $em->flush();

        $entities = $em->getRepository(ChoixAffecter::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));
        $response = $this->render('orientation/list.csv.twig',array(
                    'entities' => $entities,
                    ));

         $response->headers->set('Content-Type', 'text/csv');
         $response->headers->set('Content-Disposition', 'attachment; filename="FiliereAffecter.csv"');

        return $response;

       
    }


    /**
     * @Route("/envoyerResultat",name="envoyerResultat")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function envoyerResultatAction(MailerInterface $mailer , InternetTest $int)
    {
	    
	        $em = $this->getDoctrine()->getManager('etudiant');
			$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
	        $etats = $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU'], 'eRESULTAT' => 0),array('id' => 'ASC'));
	        $i=0;
	        foreach ($etats as $etat) {
	            if($etat->getERESULTAT()==0){
	                if($i<50){
	                    if($etat->getUser()->getEmail()){
                            
                            $html=$this->renderView('orientation/emailResultat.html.twig',array(
                                'etat' => $etat));
                            
                            $message = (new TemplatedEmail())
                                ->from(new Address('gcvre@uae.ac.ma', 'Orientation'))
                                ->to($etat->getUser()->getEmail()."")
                                ->subject('Orientation : Resultat')
                                ->html($html)
                                ;
                            try {
                                 if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
                            } catch (TransportExceptionInterface $e) {
                            
                            }

	                        $etat->setEResultat(1);
	                        $em->persist($etat);
	                        
	                    } 
	                    $i++; 
	                }else{
	                    break;
	                }   
	            }
	        }
	        $i=0; 
	        $em->flush();
	        
	        return new RedirectResponse($this->generateUrl('Orientation')); 
        
    }

    /**
     * @droitAcces("is_granted('ROLE_ADMIN')")
     * @Route("/resultatFinal",name="resultatFinal")
    */
    public function resultatFinalAction()
    {
 		
	 		$em = $this->getDoctrine()->getManager('etudiant');
			$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();

	        $ChoixAffecter = $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));

	        $response = $this->render('orientation/listFinal.csv.twig',array(
	                    'ChoixAffecter' => $ChoixAffecter,
	                    ));

	         $response->headers->set('Content-Type', 'text/csv');
	         $response->headers->set('Content-Disposition', 'attachment; filename="Resultat_Final.csv"');

	        return $response;
        

       
    }

    /**
     * @Route("/reclamationOrientation", name="reclamationOrientation")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function reclamationAction(secure $security, Request $request, Connection $conn)
    {
    	
            $em = $this->getDoctrine()->getManager('default');
            $param= new ConfigExtension($em);
	    	$anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
	        
	        $config = new \Doctrine\DBAL\Configuration();
			$connectionParams = array('url' => $_ENV['DATABASE_ETUDIANT_URL'].'',);
			$conn1 = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
			$reclamations=$conn1->fetchAllAssociative("SELECT count(r.id_user) as nb ,u.nom,u.prenom,u.code_etudiant ,u.id FROM reclamation r , User u where r.id_user = u.id and r.anneeuniv ='".$anneeUniversitaire["COD_ANU"]."' group By r.id_user");
	        $nbEtudiantsAP2= $em->getRepository(Etudiants::class)->nbEtudiantAP2($anneeUniversitaire["COD_ANU"],$param->app_config('orientation_cod_etp'),$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$conn);

	        return $this->render('orientation/reclamation.html.twig', array('Totales' => $nbEtudiantsAP2, 'nbreclamation' => count($reclamations), 'reclamations' => $reclamations));
        

    }

    /**
     * @Route("/afficherReclamation",name="afficherReclamation")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function afficherReclamationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $reclamation = $em->getRepository(Reclamation::class)->findBy(array('idUser' => $request->get('codeApogee')));
        $reclamationString ="<ol>";
        if(!empty($reclamation))
        {
            foreach ($reclamation as $recla) {
                $reclamationString .= '<li>'.$recla->getMessage().'</li>';
            }
        }
        $reclamationString .="</ol>";
       return new Response($reclamationString);
    }
	
	/**
     * @droitAcces("is_granted('ROLE_ADMIN')")
     * @Route("/resultatEtat",name="resultatEtat")
    */
    public function resultatEtatAction(Request $request, Connection $conn)
    {
        
            $em = $this->getDoctrine()->getManager('etudiant');
            $anneeUniver = $em->getRepository(AnneeUniversitaire::class)->findOneBy(array('etat' => 'O'));
            $anneeUniversitaire["COD_ANU"]=$anneeUniver->getAnnee();
            $ChoixAffecter = $em->getRepository(Etat::class)->findBy(array('anneeuniv' => $anneeUniversitaire['COD_ANU']));
            
            $response = $this->render('orientation/listEtat.csv.twig',array(
                        'ChoixAffecter' => $ChoixAffecter,
                        ));

             $response->headers->set('Content-Type', 'text/csv');
             $response->headers->set('Content-Disposition', 'attachment; filename="Resultat_Etat.csv"');

            return $response;
        

       
    }


}
