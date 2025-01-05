<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces;

use App\Entity\Etudiant\Etudiants;
use App\Entity\Etudiant\image;
use App\Twig\ConfigExtension;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
// Include JSON Response
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Entity\Etudiant\EtuReleveAttestation;
use App\Entity\Etudiant\EtuDiplomeCarte;
use App\Entity\Etudiant\EtuAttestation;
use App\Entity\Etudiant\Reinscription;
use App\Entity\EtuHistoDemandes;
use App\Entity\Personnel;
use App\Entity\Paiement;
use App\Entity\FiliereFcResponsable;
use App\Repository\EtuHistoDemandesRepository;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Doctrine\DBAL\Connection ;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

use App\Entity\FiliereFc;
use App\Entity\Financeperiode;
use App\Service\InternetTest;


class ScolariteFCController extends AbstractController
{

	/**
     * @Route("/documentsFC/{type}", name="documentsFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC')")
     */
    public function documentsFCAction($type,Request $request)
    {
        $em = $this->getDoctrine()->getManager('etudiant');

        $releves       = $em->getRepository(EtuReleveAttestation::class)->findby(array('decision' => '-1','typeF' => 'FC'));
        $cartes        = $em->getRepository(EtuDiplomeCarte::class)->findby(array('decision' => '-1','type' => 'Carte','typeF' => 'FC'));
		$attestations  = $em->getRepository(EtuAttestation::class)->findby(array('decision' => '-1','typeF' => 'FC'));
		$reinscription = $em->getRepository(Reinscription::class)->findby(array('statut' => '-1'));

		if($type=='attestation'){
			return $this->render('scolariteFC/attestation1.html.twig',['attestations' => $attestations]);
		}elseif($type=='carte'){
			return $this->render('scolariteFC/cartes1.html.twig',['cartes' => $cartes]);
		}elseif($type=='releve'){
			return $this->render('scolariteFC/releve1.html.twig',['releves' => $releves]);
		}elseif($type=='reinscription'){
		return $this->render('scolariteFC/reinscription.html.twig',['reinscription' => $reinscription]);
		}
        
		

    }

	#[Route('/app_counter_scoFC', name: 'app_counter_scoFC', methods: ['POST'])]
    public function app_counter(secure $security ) {
		$em = $this->getDoctrine()->getManager('etudiant');
		$result=[];
        $releves = $em->getRepository(EtuReleveAttestation::class)->findby(array('decision' => '-1','typeF' => 'FC'));
        $cartes = $em->getRepository(EtuDiplomeCarte::class)->findby(array('decision' => '-1','type' => 'Carte','typeF' => 'FC'));
		$attestations = $em->getRepository(EtuAttestation::class)->findby(array('decision' => '-1' ,'typeF' => 'FC'));
		$reinscription = $em->getRepository(Reinscription::class)->findby(array('statut' => '-1'));

		$result['attestationFC']= count($attestations);
		$result['releveFC']= count($releves);
		$result['carteFC']= count($cartes);
		$result['reinscriptionFC']= count($reinscription);
		$result['totaleFC']= count($attestations)+count($releves)+count($cartes)+count($reinscription);

        return new JsonResponse($result);
    }

    /**
     * @Route("/statScolariteFC", name="statScolariteFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') or is_granted('ROLE_MANAGER')")
     */
    public function statScolariteFCAction()
    {
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		$em = $this->getDoctrine()->getManager('etudiant');

		$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivAll($conn);		
		
        
		return $this->render('scolariteFC/statistiques1.html.twig',['anneeUniversitaire' => $anneeUniversitaire]);
    }


	 /**
     * @Route("/getstatScolariteFC/{annee}", name="getstatScolariteFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') or is_granted('ROLE_MANAGER')")
     */
    public function getstatScolariteFC($annee=null)
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

		$initiale = explode(",", $param->app_config('DCA'));

		$nbEtudiants= $em->getRepository(Etudiants::class)->nbEtudiantByGenre($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);
		
		$nbGarçons = $em->getRepository(Etudiants::class)->nbEtudiantByGenre($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn,'M');

		$nbFilles = $em->getRepository(Etudiants::class)->nbEtudiantByGenre($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn,'F');

		$nvIns = $em->getRepository(Etudiants::class)->nbInsNVByAnnee($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);
		 
		$statistiques['nvIns']=$nvIns[0];
		$statistiques['totales']=$nbEtudiants[0];
		$statistiques['nbGarçons']=$nbGarçons[0];
		$statistiques['nbFilles']=$nbFilles[0];

		return new JsonResponse($statistiques);
		


    }

	/**
     * @Route("/getCapaciteAsEtapeFC/{annee}", name="getCapaciteAsEtapeFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG')")
     */
    public function getCapaciteAsEtapeFC($annee=null)
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
		$initiale = explode(",", $param->app_config('DCA'));
		$nbAsEtp = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);        
        return new JsonResponse($nbAsEtp);
	    
    }

	/**
     * @Route("/getCapaciteAsEtapeNVFC/{annee}", name="getCapaciteAsEtapeNVFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')")
     */
    public function getCapaciteAsEtapeNVFC($annee=null)
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
		$initiale = explode(",", $param->app_config('DCA'));
		$nbAsEtpN = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);
        
        return new JsonResponse($nbAsEtpN);
	    
    }

	/**
     * @Route("/getCapaciteAsDiplomeFC/{annee}", name="getCapaciteAsDiplomeFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG')")
     */
    public function getCapaciteAsDiplomeFC($annee=null)
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
		$initiale = explode(",", $param->app_config('DCA'));
		$nbAsDip = $em->getRepository(Etudiants::class)->nbInsAsDiplome($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);
        
        return new JsonResponse($nbAsDip);
	    
    }

	/**
     * @Route("/getevolutionEffectifFC/{annee}", name="getevolutionEffectifFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG')")
     */
    public function getevolutionEffectifFC($annee=null)
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
		$initiale = explode(",", $param->app_config('DCA'));
		$effectifs = $em->getRepository(Etudiants::class)->evolutioneffectif($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);
        return new JsonResponse($effectifs);
	    
    }

    /**
     * @droitAcces("is_granted('ROLE_SCOLARITEFC')")
     * @Route("/decisionDocFC/{type}/{id}",name="decisionDocFC")
     */
    public function decisionDocFCAction(MailerInterface $mailer  , InternetTest $int,Request $request,$type,$id,secure $security)
    {

	
        $searchParam = $request->get('searchParam');
        $em = $this->getDoctrine()->getManager('etudiant');
		$em1 = $this->getDoctrine()->getManager();

        extract($searchParam);
        $Motifs='';
        $typeDoc='';
        $typeRetour=''; 
        if($type=="attestation"){
        	$typeDoc= 'Attestation';
        	$document = $em->getRepository(EtuAttestation::class)->find($id);
        }elseif($type=="releve"){
        	$document = $em->getRepository(EtuReleveAttestation::class)->find($id);
        	$typeDoc= $document->getType();

        }elseif($type=="carte"){
        	$document = $em->getRepository(EtuDiplomeCarte::class)->find($id);
        	$typeDoc= $document->getType();
        }elseif($type=="reinscription"){
        	$document = $em->getRepository(Reinscription::class)->find($id);
        	$typeDoc= 'Reinscription';
        }
        $email='';

        if($decision==1){
        	if($type=="reinscription"){
        		if($document->getStatut()=='-1'){
        			$document->setStatut("1");
            		$document->setDateValidation(new \DateTime('now'));
            		$Motifs ='Votre ré-inscription à été validé  ';

				}
        	}else{
				if($type=="carte"){
        			$usr = $em->getRepository(Etudiants::class)->findOneBy(array('code' => $document->getCodeEtudiant()->getCode()));
        			$usr->setCarte('OUI');
        			$em->persist($usr);
        		}
        		$document->setDecision("1");
            	$document->setDatevalidation(new \DateTime('now'));
            	$Motifs ='Votre demande à été accepté ';
        	}
            
        }elseif($decision==0){
        	if($type=="reinscription"){
        		$document->setStatut("0");
            	$Motifs ='Votre demande à été accepté ';
        	}else{
        		$document->setDecision("0");
            	$document->setDatevalidation(new \DateTime('now'));
            	$Motifs ='à été refusé :'.$motifs;
            	$document->setMotif("votre demande ".$Motifs);
        	}

            
        }
        
		$HistoDemandes = new EtuHistoDemandes();
        $HistoDemandes->setTypeDemande($typeDoc) ; 
        $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
        $HistoDemandes->setNiveau("Scolarité") ;
        $HistoDemandes->setDateValidation(new \DateTime('now')) ;
        $HistoDemandes->setDateEnvoie($document->getDateDemande()) ;
        $HistoDemandes->setIdDemande($document->getId()) ;
    
            
		if($type=="reinscription"){
			$html=$this->renderView('scolarite/emailinformation.html.twig',array('etudiant' => $document->getIdUser() ,'decision' => $document->getStatut(),'motif' => $Motifs));
			$email=$document->getIdUser()->getEmail();
			$HistoDemandes->setStatut($document->getStatut()) ;
			$HistoDemandes->setAnneeUniv($document->getAnnNouv()) ;
		}else{
			$email=$document->getCodeEtudiant()->getEmail();
			$HistoDemandes->setStatut($document->getDecision()) ;
			$HistoDemandes->setAnneeUniv($document->getAnneeUniv()) ;
			$html=$this->renderView('scolarite/emailinformation.html.twig',array('etudiant' => $document->getCodeEtudiant() ,'decision' => $document->getDecision(),'motif' => $Motifs));
		}

		$em1->persist($HistoDemandes);
		$em1->flush();

		if($email==''){
            $email='gcvre@uae.ac.ma';
        }
		$message = (new TemplatedEmail())
			->from(new Address('gcvre@uae.ac.ma', 'Document'))
			->to($email.'')
			->subject('Document : '.$typeDoc)
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

         return new RedirectResponse($this->generateUrl('documentsFC', array('type'=>$type)));


    }

    /**
     * @Route("/listUsersScolariteFC", name="listUsersScolariteFC")
     * @droitAcces("is_granted('ROLE_FONC') or is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_PROF')")
     */
    public function listUsersScolariteFCAction(secure $security,Request $request)
    {
    	
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();
		$em = $this->getDoctrine()->getManager('etudiant');
		$param= new ConfigExtension($em1);

		$initiale = explode(",", $param->app_config('DCA'));
		$etudiantsFC = $em->getRepository(Etudiants::class)->getInscritsFC($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);

		return $this->render('scolariteFC/usersListe1.html.twig', array('users' => $etudiantsFC));

	    
    }


  /**
     * @Route("/list_etu_paiement", name="list_etu_paiement")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_FC_PAI')")
     */
    public function listUsersScolariteFCFinanceAction(secure $security,Request $request)
    {
    	
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();
		$em = $this->getDoctrine()->getManager('etudiant');
		$param= new ConfigExtension($em1);

		$initiale = explode(",", $param->app_config('DCA'));
		$etudiantsFC = $em->getRepository(Etudiants::class)->getInscritsFC($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);


		return $this->render('scolariteFC/usersListe_finance.html.twig', array('users' => $etudiantsFC));

	    
    }






    /**
     * @Route("/paiement_by_date", name="paiement_by_date")
     * @droitAcces("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function paiement_by_date(Request $request , secure $security)
    {
        $array_mm_dd = ['10-31' , '03-31'] ;
        $em= $this->getDoctrine()->getManager();

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);

        $personnel_id =  $em->getRepository(Personnel::class)->findOneBy(array('idUser'=> $security->getUser()->getId()));

        if($personnel_id){

            $filiereFC =  $em->getRepository(FiliereFcResponsable::class)->findOneBy(array('responsable'=> $personnel_id ,'annee'=>$anneeUniversitaire['COD_ANU']));

           if( $filiereFC ) { $filiereFC = $filiereFC->getFiliereFc()->getCodeApo() ;}  

        }else{
            $filiereFC = '%%';
        }

        if(!$filiereFC){
            $filiereFC = '%%'; 
        }

     
		$resultat =$em->getRepository(Paiement::class)->paiement_by_date($array_mm_dd , $anneeUniversitaire['COD_ANU'] , $filiereFC , $personnel_id->getId());
       

        return  $resultat ;

    }






 /**
     * @Route("/list_etu_paiement_by_filiere", name="list_etu_paiement_by_filiere")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_PROF')")
     */
    public function listUsersScolariteFCFinanceFiliereAction(secure $security,Request $request)
    {
    	
	
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();
		$em = $this->getDoctrine()->getManager('etudiant');
		$param= new ConfigExtension($em1);
		$anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
	

		$user_id =  $security->getUser()->getId();
        $personnel =  $em1->getRepository(Personnel::class)->findBy(array('idUser'=>$user_id));
		if($personnel){

			$perso_id = $personnel[0]->getId();
			$filiere_resp = $em1->getRepository(FiliereFcResponsable::class)->findBy(array('responsable'=>$perso_id ,'annee'=>$anneeUniversitaire['COD_ANU']));
	
			if(!$filiere_resp){
				$filiere_resp = null;
			}else{
				$filiere_resp = $filiere_resp[0]->getFiliereFc()->getCodeApo();
			}
	
			$pai=$em1->getRepository(Paiement::class)->ldBY_demandeur(); //ldBY_demandeur_filiere($filiere_resp ,$anneeUniversitaire['COD_ANU'] );
		
			$demandeur_code_array = [] ;
			$demandeur_somme = [] ;
			$somme_paiements = 0;
	
		  foreach ($pai as $p) 
		  {
			foreach ($p as $key => $value) 
			{
				if($key==0){
					array_push($demandeur_code_array,$p[0]);
				}else{
					array_push($demandeur_somme,$p[1]);
				}
				
			}  
		  }
		//  $somme_paiements =  array_sum($demandeur_somme);
		  $somme_paiements_by_periode = $this->paiement_by_date($request,$security); 
		
			$initiale = explode(",", $param->app_config('DCA'));

			$etudiantsFC = $em->getRepository(Etudiants::class)->getInscritsFC_COD_DIP($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn,$filiere_resp,$anneeUniversitaire['COD_ANU']);
			// E , 
			//dd($anneeUniversitaire['COD_ANU']);
			return $this->render('scolariteFC/usersListe_finance_filiere.html.twig', array('somme_paiements'=>$somme_paiements_by_periode,'users' => $etudiantsFC,'annee'=>$anneeUniversitaire['COD_ANU'],'filiere_resp'=>$filiere_resp , 'd_somme' =>$demandeur_somme , 'd_code'=> $demandeur_code_array ));
	

		}else{

			return $this->render('scolariteFC/usersListe_finance_filiere.html.twig', array('filiere_resp'=>null));


		}
       
	    
    }














    /**
     * @droitAcces("is_granted('ROLE_FONC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_PROF')")
     * @Route("/detailUserFC/{id}",name="detailUserFC")
     */
    public function detailUserFCAction($id)
    {
    	
	    	$em = $this->getDoctrine()->getManager('etudiant');
			$em1 = $this->getDoctrine()->getManager('default');
			$param= new ConfigExtension($em1);

			
			$config = new \Doctrine\DBAL\Configuration();
			$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
			$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

			$anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
			$etudiant = $em->getRepository(Etudiants::class)->etudiantByInd($id,$conn);
			$ins_Adm_E =  $em->getRepository(Etudiants::class)->insAdmLastByInd($etudiant["COD_IND"],$conn,$param->app_config('COD_CMP'),$param->app_config('ETA_IAE'));
			$ins_Peda_E = $em->getRepository(Etudiants::class)->insPedLastByInd($etudiant["COD_IND"],$conn,$anneeUniversitaire['COD_ANU']);		
			$groupe   = $em->getRepository(Etudiants::class)->getGroupeByInd($etudiant["COD_IND"],$anneeUniversitaire['COD_ANU'],$conn);
			$gr='';
			if($groupe){
				$gr=$groupe['COD_EXT_GPE'];
			}else{
				$gr=$ins_Adm_E[0]['COD_ETP'];
			}
			$res_etudiant= $em->getRepository(Etudiants::class)->insAdmValidInd($etudiant["COD_IND"],$conn,$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$param->app_config('typeResultat'));

			$initiale = explode(",", $param->app_config('DCA'));


			$resultats_elp = $em->getRepository(Etudiants::class)->resultat_elp($conn,$initiale,$param->app_config('DCESS'),$etudiant["COD_IND"],$anneeUniversitaire["COD_ANU"]);

            $resultats_vet = $em->getRepository(Etudiants::class)->resultat_vet($conn,$etudiant["COD_IND"],$param->app_config('typeResultat'),$anneeUniversitaire["COD_ANU"]);


            $details = $this->unique_multidim_array($resultats_elp,'COD_ELP','NOT_ELP','COD_ANU');
            $details1 = $this->unique_multidim_array($resultats_vet,'COD_ETP','NOT_VET','COD_ANU');

			
			
			return $this->render('scolariteFC/showUser1.html.twig', ['image' => 'anonymous.png','details' => $details , 'details1' => $details1,'etudiant' => $etudiant,'ins_Adm_E' => $ins_Adm_E, 'ins_Peda_E' => $ins_Peda_E,'res_etudiant' => $res_etudiant,'anneeUniversitaire'=> $anneeUniversitaire,'groupe' => $gr,
        		'base_dir' => $this->getParameter('kernel.project_dir') . '/../']);

	    

    }

	#[Route(path: '/resultatFC/{id}', name: 'app_resultatFC')]
    public function resultatFC($id, Request $request)
    {
		$em = $this->getDoctrine()->getManager('etudiant');
		$usr = $em->getRepository(Etudiants::class)->find($id);
        
        $em1 = $this->getDoctrine()->getManager('default');
        $conf          = new ConfigExtension($em1);

		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $etudiant      = $em->getRepository(Etudiants::class)->etudiantByInd($usr->getCode(),$conn);

        $ins_Adm_E  = $em->getRepository(Etudiants::class)->insAdmLastByInd($etudiant["COD_IND"],$conn,$conf->app_config('COD_CMP'),$conf->app_config('ETA_IAE'));
        
		$ins_Peda_E = $em->getRepository(Etudiants::class)->insPedLastByInd($etudiant["COD_IND"],$conn);
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $groupe   = $em->getRepository(Etudiants::class)->getGroupeByInd($etudiant["COD_IND"],$anneeUniversitaire['COD_ANU'],$conn);
		$gr='';
        if($groupe){
            $gr=$groupe['COD_EXT_GPE'];
        }else{
            $gr=$ins_Adm_E[0]['COD_ETP'];
        }

        $initiale = explode(",", $conf->app_config('DCA'));

		$resultats_elp = $em->getRepository(Etudiants::class)->resultat_elp($conn,$initiale,$conf->app_config('DCESS'),$etudiant["COD_IND"]);

		$resultats_vet = $em->getRepository(Etudiants::class)->resultat_vet($conn,$etudiant["COD_IND"],$conf->app_config('typeResultat'));


		$details = $this->unique_multidim_array($resultats_elp,'COD_ELP','NOT_ELP','COD_ANU');
		$details1 = $this->unique_multidim_array($resultats_vet,'COD_ETP','NOT_VET','COD_ANU');

		$found_key = array_search('IIAP1', array_column($details1, 'COD_ETP'));
		//return new JsonResponse($details)	;
		return $this->render('scolariteFC/releve_note.html.twig', ['image' => $usr->getImage()->getPath() ,'groupe' => $gr,'details' => $details,'details1' => $details1,'etudiant' => $etudiant,'ins_Peda_E' => $ins_Peda_E,'ins_Adm_E' => $ins_Adm_E
        ]);	
		
    }

	function unique_multidim_array($array, $key,$note,$annee) {

	    $temp_array = array();

	    $i = 0;

	    $key_array = array();

	    

	    foreach($array as $val) {

	        if (in_array($val[$key], $key_array)) {

	        	$element = array_search($val[$key], $key_array);
	        	if($val[$note]>=$temp_array[$element][$note] || $val[$annee]!=$temp_array[$element][$annee]){
	        		$key_array[$element] = $val[$key];

	            	$temp_array[$element] = $val;
	        	}
	        }else{

	        	$key_array[$i] = $val[$key];

	            $temp_array[$i] = $val;
	        }

	        $i++;

	    }

	    return $temp_array;

	}

    
    /**
     * @droitAcces("is_granted('ROLE_SCOLARITEFC')")
     * @Route("/removedocFC/{type}/{id}",name="removedocFC")
    */
    public function removedocFCAction(secure $security,Request $request,$id,$type,Connection $conn)
    {
    	
	    	$em = $this->getDoctrine()->getManager('etudiant');
			$param= new ConfigExtension($em);
			$usr = $em->getRepository(Etudiants::class)->find($id);
			if($type=="attestation"){
	        	$document = $em->getRepository(EtuAttestation::class)->find($id);
	        }elseif($type=="releve"){
	        	$document = $em->getRepository(EtuReleveAttestation::class)->find($id);
	        }elseif($type=="carte"){
	        	$document = $em->getRepository(EtuDiplomeCarte::class)->find($id);
	        }
	        if($document){
	        	$em->remove($document);
        		$em->flush();
	        }
			$users = $em->getRepository(Etudiants::class)->findAll();

	        return $this->render('scolariteFC/usersListe.html.twig', array('users' => $users));

	    

    }
	/**
     * @Route("/carteQRFC/{id}", name="carteQRFC")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function carteQRFCAction(Pdf $knpSnappyPdf ,$id)
    {
        $em = $this->getDoctrine()->getManager('etudiant');

        $document = $em->getRepository(EtuDiplomeCarte::class)->find($id);

		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $etudiant = $conn->fetchAssociative("SELECT * FROM individu i,adresse a WHERE i.COD_ETU='".$document->getCodeEtudiant()->getCode()."' and i.COD_IND = a.COD_IND ");
		$ins_Adm_E=$conn->fetchAssociative("SELECT a.LIB_ANU FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP=e.COD_ETP and ie.COD_IND='".$etudiant['COD_IND']."' AND COD_CMP='ENT' AND ETA_IAE='E' order by ie.COD_ANU ASC");
        $writer = new PngWriter();
        $usr = $em->getRepository(Etudiants::class)->findOneBy(array('code' => $document->getCodeEtudiant()->getCode()));
		$image = $em->getRepository(image::class)->find($usr->getImage());
        $options = [
        	'orientation'   => 'Landscape',
        	'page-height'   => 85,
            'page-width'    => 55,
            'margin-top'    => 0,
    		'margin-right'  => 0,
    		'margin-bottom' => 0,
    		'margin-left'   => 0,
        ];
        // Create a basic QR code
		$qrCode = new QrCode($document->getCodeEtudiant()->getCode());
		$qrCode->setSize(120);
		$qrCode->setBackgroundColor(new Color(245,245,245));

		//$dataUri = $qrCode->writeDataUri();
		$dataUri = $writer->write($qrCode)->getDataUri();
        $html = $this->renderView('scolariteFC/carteQR.html.twig', array(
        	'dataUri' => $dataUri ,
        	'usere'    => $usr,
			'image'   => $image->getPath(),
        	'ins_Adm_E' => $ins_Adm_E,
            'base_dir' => $this->getParameter('kernel.project_dir'). '/../'
        ));
		return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            'carte_'.$usr->getNom().'_'.$usr->getPrenom().'.pdf' ,
        );
                
    }
	
	
    /**
     * @Route("/carteQRALLFC", name="carteQRALLFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC')")
     */
    public function carteQRALLFCAction(Pdf $knpSnappyPdf)
    {
        $em = $this->getDoctrine()->getManager('etudiant');

        $documents = $em->getRepository(EtuDiplomeCarte::class)->findBy(array('decision' => '-1','type' => 'Carte','typeF' => 'FC'));
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $carteALL = array();
		foreach ($documents as $document) {
	        $etudiant = $conn->fetchAssociative("SELECT * FROM individu i,adresse a WHERE i.COD_ETU='".$document->getCodeEtudiant()->getCode()."' and i.COD_IND = a.COD_IND ");
			$ins_Adm_E=$conn->fetchAssociative("SELECT a.LIB_ANU FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP=e.COD_ETP and ie.COD_IND='".$etudiant['COD_IND']."' AND COD_CMP='ENT' AND ETA_IAE='E' order by ie.COD_ANU ASC");
	        if(!empty($ins_Adm_E)){
				$usr = $em->getRepository(Etudiants::class)->findOneBy(array('code' => $document->getCodeEtudiant()->getCode()));
				$image = $em->getRepository(image::class)->find($usr->getImage());

				$options = [
					'orientation'   => 'Landscape',
					'page-height'   => 85,
					'page-width'    => 55,
					'margin-top'    => 0,
					'margin-right'  => 0,
					'margin-bottom' => 0,
					'margin-left'   => 0,
				];
				// Create a basic QR code
				$qrCode = new QrCode($document->getCodeEtudiant()->getCode());
				$qrCode->setSize(120);
				$qrCode->setBackgroundColor(new Color(245,245,245));
				$writer = new PngWriter();
				$dataUri = $writer->write($qrCode)->getDataUri();
				array_push($carteALL,array($dataUri,$usr,$image->getPath(),$ins_Adm_E));
			}
	        
		}
		
        $html = $this->renderView('scolariteFC/carteQRAll.html.twig', array(
        	'carteALL' => $carteALL ,
            'base_dir' => $this->getParameter('kernel.project_dir'). '/../'
        ));

		return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            'carte_All.pdf' ,
        );
        
        
    }
	/**
     * @droitAcces("is_granted('ROLE_SCOLARITEFC') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')")
     * @Route("/exporterStatScoFC",name="exporterStatScoFC")
    */
    public function exporterStatFCAction(Request $request) {

		
    	$em = $this->getDoctrine()->getManager('etudiant');
		
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();

		$param= new ConfigExtension($em1);
		$initiale = explode(",", $param->app_config('DCA'));
		$searchParam = $request->get('searchParam');
		extract($searchParam);
		if($searchParam['annee']){
			$anneeUniversitaire['COD_ANU']=$searchParam['annee'];
		}else{
			$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	
		}
				

        $spreadsheet = new Spreadsheet();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Statistiques Scolarité");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getColumnDimension('B')->setWidth(20);
        $worksheet->getColumnDimension('C')->setWidth(20);
        $worksheet->getColumnDimension('D')->setWidth(20);
        $worksheet->getColumnDimension('E')->setWidth(20);
        $worksheet->getColumnDimension('F')->setWidth(20);
        $worksheet->getColumnDimension('G')->setWidth(20);
        $worksheet->getColumnDimension('H')->setWidth(20);
        $worksheet->getColumnDimension('I')->setWidth(20);
        $worksheet->getColumnDimension('J')->setWidth(20);
        $worksheet->getColumnDimension('K')->setWidth(20);
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
    
    	$worksheet->mergeCells("B2:K2");
        $worksheet->getCell('B2')->setValue('Ecole Nationale des Sciences Appliquées Tanger '.$anneeUniversitaire['COD_ANU']);
        $worksheet->getStyle('B2')->getAlignment()->setWrapText(true);
        $worksheet->getStyle('B2')->applyFromArray($styleArray);


        $worksheet->getCell('B4')->setValue('Diplôme');
        $worksheet->getCell('C4')->setValue('Etape');
        $worksheet->getCell('D4')->setValue('Nombre/Etape');
        $worksheet->getCell('E4')->setValue('Nouvaux inscrits');
        $worksheet->getCell('F4')->setValue('Nouvaux inscrits Garçons');
        $worksheet->getCell('G4')->setValue('Nouvaux inscrits Filles'); 
        $worksheet->getCell('H4')->setValue('les Réinscrits');
        $worksheet->getCell('I4')->setValue('Garçons');
        $worksheet->getCell('J4')->setValue('Filles');
        $worksheet->getCell('K4')->setValue('Totale');
        $worksheet->getStyle('B4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('C4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('D4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('E4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('F4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('G4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('H4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('I4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('J4')->applyFromArray($styleArrayTitle1);
        $worksheet->getStyle('K4')->applyFromArray($styleArrayTitle1);

        $worksheet->getStyle('B4:K4')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
        
        $i=5;
        $l=5;
        $p=5;
        $m=5;
        $j=5;
        $o=5;
        $q=5;

		$nbAsEtp = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);        
		$nbAsEtpN = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);        
        $nbAsDip = $em->getRepository(Etudiants::class)->nbInsAsDiplome($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn);
		$nbAsEtpNG = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn,'M');        
        $nbAsEtpNF = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn,'F');        
       
		$nbGarçons = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn,'M');        
		$nbFilles = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('DCESS'),$conn,'F');        


        foreach ($nbAsDip as $dip) {
        	if($dip['COD_DIP']== 'IIAP' || $dip['COD_DIP']=='IMBS' || $dip['COD_DIP'] == 'IMSE' || $dip['COD_DIP'] == 'IMCC' || $dip['COD_DIP'] == 'IMMP'){
        		$y=2;
	       	}elseif($dip['COD_DIP'] == 'IMCE') {
	       		$y=2;
	       	}elseif($dip['COD_DIP'] == 'IIGI') {
	       		$y=4;
	       	}else{
	       		$y=3;
	       	}
	       		$worksheet->mergeCells("B".$i.":B".($i+$y-1));
	            $worksheet->getStyle("B".$i)->applyFromArray($styleArrayTitle);
	            $worksheet->getStyle("B".($i+$y-1))->applyFromArray($styleArrayTitle);
	            $worksheet->getCell('B'.$i)->setValue($dip['COD_DIP']);
	            $worksheet->mergeCells("K".$i.":K".($i+$y-1));
	            $worksheet->getStyle("K".$i)->applyFromArray($styleArrayTitle);
	            $worksheet->getStyle("K".($i+$y-1))->applyFromArray($styleArrayTitle);
	            $worksheet->getCell('K'.$i)->setValue($dip['NOMBRE']);
	       	
            for ($k=1; $k <=$y ; $k++){
            	foreach ($nbAsEtp as $etp) {
            		$worksheet->getStyle("C".$i)->applyFromArray($styleArrayTitle);
			        $worksheet->getStyle("D".$i)->applyFromArray($styleArrayTitle);
            		if($dip['COD_DIP'].$k == $etp['COD_ETP'] || ($dip['COD_DIP'].$k=='IIGI3' && $etp['COD_ETP']=='IIGL3') || ($dip['COD_DIP'].$k=='IIGI4' && $etp['COD_ETP']=='IISI3')){
			            $worksheet->getCell('C'.$i)->setValue($etp['COD_ETP']);
			            $worksheet->getCell('D'.$i)->setValue($etp['NOMBRE']);
			            
			            		
			        }
            	}
            	$i++;  
        	}
        	for ($k=1; $k <=$y ; $k++){
        		foreach ($nbAsEtpN as $etpN) {
        			$worksheet->getStyle("E".$l)->applyFromArray($styleArrayTitle);
			        $worksheet->getStyle("H".$l)->applyFromArray($styleArrayTitle);
            		if($dip['COD_DIP'].$k == $etpN['COD_ETP'] || ($dip['COD_DIP'].$k=='IIGI3' && $etpN['COD_ETP']=='IIGL3') || ($dip['COD_DIP'].$k=='IIGI4' && $etpN['COD_ETP']=='IISI3')){
	            		
			            $worksheet->getCell('E'.$l)->setValue($etpN['NOMBRE']);
			             
            		}
            		$worksheet->setCellValue('H'.$l,'='.'D'.$l.' - E'.$l);		
            	} 
            	$l++; 
        	}


        	for ($k=1; $k <=$y ; $k++){
            	foreach ($nbGarçons as $garçon) {
            		$worksheet->getStyle("I".$p)->applyFromArray($styleArrayTitle);
            		if($dip['COD_DIP'].$k == $garçon['COD_ETP'] || ($dip['COD_DIP'].$k=='IIGI3' && $garçon['COD_ETP']=='IIGL3') || ($dip['COD_DIP'].$k=='IIGI4' && $garçon['COD_ETP']=='IISI3')){
			            $worksheet->getCell('I'.$p)->setValue($garçon['NOMBRE']);
			            
			            		
			        }
            	}
            	$p++;  
        	}

        	for ($k=1; $k <=$y ; $k++){
            	foreach ($nbFilles as $fille) {
            		$worksheet->getStyle("J".$m)->applyFromArray($styleArrayTitle);
            		if($dip['COD_DIP'].$k == $fille['COD_ETP'] || ($dip['COD_DIP'].$k=='IIGI3' && $fille['COD_ETP']=='IIGL3') || ($dip['COD_DIP'].$k=='IIGI4' && $fille['COD_ETP']=='IISI3')){
			            $worksheet->getCell('J'.$m)->setValue($fille['NOMBRE']);
			            
			            		
			        }
            	}
            	$m++;  
        	}

        	for ($k=1; $k <=$y ; $k++){
            	foreach ($nbAsEtpNF as $fille) {
            		$worksheet->getStyle("F".$q)->applyFromArray($styleArrayTitle);
            		if($dip['COD_DIP'].$k == $fille['COD_ETP'] || ($dip['COD_DIP'].$k=='IIGI3' && $fille['COD_ETP']=='IIGL3') || ($dip['COD_DIP'].$k=='IIGI4' && $fille['COD_ETP']=='IISI3')){
			            $worksheet->getCell('F'.$q)->setValue($fille['NOMBRE']);
			            
			            		
			        }
            	}
            	$q++;  
        	}

        	for ($k=1; $k <=$y ; $k++){
            	foreach ($nbAsEtpNG as $fille) {
            		$worksheet->getStyle("G".$o)->applyFromArray($styleArrayTitle);
            		if($dip['COD_DIP'].$k == $fille['COD_ETP'] || ($dip['COD_DIP'].$k=='IIGI3' && $fille['COD_ETP']=='IIGL3') || ($dip['COD_DIP'].$k=='IIGI4' && $fille['COD_ETP']=='IISI3')){
			            $worksheet->getCell('G'.$o)->setValue($fille['NOMBRE']);
			            
			            		
			        }
            	}
            	$o++;  
        	}
        }
        $worksheet->mergeCells("B".$i.":C".$i);
	    $worksheet->getStyle("B".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("C".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("D".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("E".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("F".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("G".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("H".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("I".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("J".$i)->applyFromArray($styleArrayTitle);
	    $worksheet->getStyle("K".$i)->applyFromArray($styleArrayTitle);
        $worksheet->getCell('B'.$i)->setValue('TOTALE');
        $worksheet->setCellValue('D'.$i,'=SUM(D5: D'.$i.')');
        $worksheet->setCellValue('E'.$i,'=SUM(E5: E'.$i.')');
        $worksheet->setCellValue('F'.$i,'=SUM(F5: F'.$i.')');
        $worksheet->setCellValue('G'.$i,'=SUM(G5: G'.$i.')');
        $worksheet->setCellValue('H'.$i,'=SUM(H5: H'.$i.')');
        $worksheet->setCellValue('I'.$i,'=SUM(I5: I'.$i.')');
        $worksheet->setCellValue('J'.$i,'=SUM(J5: J'.$i.')');
        $worksheet->setCellValue('K'.$i,'=SUM(K5: K'.$i.')');


        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'statistiquesScolariteFC.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
	
	
		
	/**
     * @Route("/add_list_releveFC", name="add_list_releveFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC')")
     */
    public function add_list_releveFCAction(secure $security,Request $request,MailerInterface $mailer  , InternetTest $int)
    {

        $em = $this->getDoctrine()->getManager('etudiant');
        $listes= explode(",",$request->query->get("liste"));
        
        foreach ($listes as $code) {
        		$document = $em->getRepository(EtuReleveAttestation::class)->findOneBy(array('id'=> $code));
        		if($document){
        		
        			$document->setDecision("1");
            		$document->setDatevalidation(new \DateTime('now'));
            		$Motifs ='Votre demande à été accepté ';
            		if($document->getCodeEtudiant()->getEmail()){
            			$email=$document->getCodeEtudiant()->getEmail();
			        }else{
			            $email='gcvre@uae.ac.ma';
			        }

					$html=$this->renderView('scolariteFC/emailinformation.html.twig',array('etudiant' => $document->getCodeEtudiant() ,'decision' => $document->getDecision(),'motif' => $Motifs));
		
					$message = (new TemplatedEmail())
						->from(new Address('gcvre@uae.ac.ma', 'Document'))
						->to($email.'')
						->subject('Document : '.$document->getType())
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
        		}
  
        }

        return new JsonResponse('1');
           
    }

     /**
     * @Route("/add_list_attestationFC", name="add_list_attestationFC")
     * @droitAcces("is_granted('ROLE_SCOLARITEFC')")
     */
    public function add_list_attestationFCAction(secure $security,Request $request, MailerInterface $mailer  , InternetTest $int)
    {
        $usr = $security->getUser();

        $em = $this->getDoctrine()->getManager('etudiant');
        $listes= explode(",",$request->query->get("liste"));
        
        foreach ($listes as $code) {
        		$document = $em->getRepository(EtuAttestation::class)->findOneBy(array('id'=> $code));
        		if($document){
        		
        			$document->setDecision("1");
            		$document->setDatevalidation(new \DateTime('now'));
            		$Motifs ='Votre demande à été accepté ';
            		if($document->getCodeEtudiant()->getEmail()){
            			$email=$document->getCodeEtudiant()->getEmail();
			        }else{
			            $email='gcvre@uae.ac.ma';
			        }
            		$html = $this->renderView('scolariteFC/emailinformation.html.twig',array('etudiant' => $document->getCodeEtudiant() ,'decision' => $document->getDecision(),'motif' => $Motifs));
					$message = (new TemplatedEmail())
						->from(new Address('gcvre@uae.ac.ma', 'Attestation'))
						->to($email.'')
						->subject('Document : Attestation')
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
        		}
  
        }

        return new JsonResponse('1');
           
    }







     /**
     * @Route("/liste_fc_dashboard", name="liste_fc_dashboard")
     * @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_FC_PAI')")
     */
    public function liste_fc_dashboard(secure $security,Request $request)
    {

		$searchParam = $request->get('searchParam');
		$annee_exerc = null ;
		$annee_periode= null ;

		$usr = $security->getUser();
		$em = $this->getDoctrine()->getManager();

		$array_mm_dd = ['10-31' , '03-31'] ;
		$date1 = "";
        $date2 = "";


		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		$em1 = $this->getDoctrine()->getManager('etudiant');
		$anneeUniversitaireListe=$em1->getRepository(Etudiants::class)->getAnneeUnivAll($conn);	

		$anneeUniversitaire=$em1->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	

		$result_array = [];
		$annee_univ_paieurs_array= [];



		
		if($searchParam){
			$annee_exerc = $searchParam['annee'];
			$annee_periode = $searchParam['periode'];

			if($searchParam['periode']== '10-31' )//  2023 = 2023
			{
				$date1=$searchParam['annee'] ."-".$array_mm_dd[1] ; //2023-03-31
			    $date2=$searchParam['annee']."-".$array_mm_dd[0] ; //2023-10-31
			}else{
				$date1=$searchParam['annee']-1 ."-".$array_mm_dd[0] ; //2023-10-31
			    $date2=$searchParam['annee']."-".$array_mm_dd[1] ; //2024-03-31
			}
			
		}else{
			if($anneeUniversitaire['COD_ANU'] == date("Y")  )//  2023 = 2023
			{
				$date1=$anneeUniversitaire['COD_ANU'] ."-".$array_mm_dd[1] ; //2023-03-31
				$date2=$anneeUniversitaire['COD_ANU'] ."-".$array_mm_dd[0] ; //2023-10-31
			}

	/* 		if($anneeUniversitaire['COD_ANU'] == date("Y")  )//  2023 = 2023
			{
				$date1=$anneeUniversitaire['COD_ANU'] ."-".$array_mm_dd[1] ; //2023-03-31
				$date2=$anneeUniversitaire['COD_ANU'] ."-".$array_mm_dd[0] ; //2023-10-31
			} */

			if($anneeUniversitaire['COD_ANU'] +1 == (date("Y")))// 2023 + 1 = 2023 
			{
				$date1=$anneeUniversitaire['COD_ANU'] ."-".$array_mm_dd[0] ;// 2023-10-31
				$date2=($anneeUniversitaire['COD_ANU']+1)."-".$array_mm_dd[1] ; //2024-03-31
			}
		}

		//$date1="2023-10-31";
		//$date2="2024-03-31";

		$filiere = $em->getRepository(Paiement::class)->getForamtionByResponsable($date1,$date2);


		foreach ($filiere as $value) {

	    $filiere_array = [];
		

		
		$filiere_montant_globale = $em->getRepository(Paiement::class)->get_FC_montant_globale($value['formation'],  $date1 , $date2,$value['id']);
		
		$annee_univ_paieurs_array = $em->getRepository(Paiement::class)->getFC_annee_univ_paieur($value['formation'], $date1 , $date2,$value['id']) ;
	
		array_push($filiere_array , $value['formation'], $filiere_montant_globale[0]['montant_globale'], $annee_univ_paieurs_array , $value['responsable'], $filiere_montant_globale,$value['id']);
		array_push($result_array,$filiere_array);
		unset ( $filiere_array ) ;

			}

			

		$periodes = $em->getRepository(Financeperiode::class)->findAll() ;
		$montant_g = $em->getRepository(Paiement::class)->getMontantGlobaleFC($date1,$date2) ;
	//	dd($anneeUniversitaire['COD_ANU'] ." - ".$date1." - ".$date2." - ".$montant_g[0]['montant_g']);

		return $this->render('scolariteFC/liste_filiere_fc_dashboard.html.twig', array('result' => $result_array , 'anneeUniversitaire' => $anneeUniversitaireListe , 'periodes'=>$periodes , 'annee_exerc'=> $annee_exerc,'date1' => $date1,'date2' => $date2 , 'montant_g'=>$montant_g[0]['montant_g']));
           
    }


    /**
     * @Route("/get_liste_paiement_annee/{debut}/{fin}/{res}/{an}", name="get_liste_paiement_annee")
     * @droitAcces("is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FC_PAI') ")
     */
    public function get_date_emploi(Request $request,$debut,$fin,$res,$an=null)
    {
		

		$em = $this->getDoctrine()->getManager();

		$listePaiement =  $em->getRepository(Paiement::class)->getPayeurByAnnee($debut,$fin,$res,$an);

		$spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Liste des paiements");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        //Rename sheet
        $worksheet->setTitle('Liste des paiements');

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
                'horizontal'=> Alignment::HORIZONTAL_LEFT
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
                'horizontal'=> Alignment::HORIZONTAL_LEFT
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
       
        $worksheet->mergeCells("B2:H2");   
        $worksheet->getCell('B2')->setValue(' Université Abdelmalek Essaâdi Ecole Nationale des Sciences Appliquées Tanger ');
        $worksheet->getStyle('B2')->getAlignment()->setWrapText(true);
        $worksheet->getStyle('B2')->applyFromArray($styleArray);

        $worksheet->getCell('B4')->setValue('Code Apogée');
        $worksheet->getCell('C4')->setValue('nom');
        $worksheet->getCell('D4')->setValue('prenom');
        $worksheet->getCell('E4')->setValue('Date Paiement');
        $worksheet->getCell('F4')->setValue('Date Operation');
        $worksheet->getCell('G4')->setValue('N° RP');
        $worksheet->getCell('H4')->setValue('Montant');


        $worksheet->getStyle('B4:H4')->applyFromArray($styleArrayTitle1);

        $worksheet->getStyle('B4:H4')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
       

        $i=5;

		foreach ($listePaiement as $mat) {
			$worksheet->getStyle("B".$i.":H".$i)->applyFromArray($styleArrayTitle);
			$worksheet->getCell('B'.$i)->setValue($mat['demandeur']);
			$worksheet->getCell('C'.$i)->setValue($mat['nom']);
			$worksheet->getCell('D'.$i)->setValue($mat['prenom']);
			$worksheet->getCell('E'.$i)->setValue($mat['datePaiement']);
			$worksheet->getCell('F'.$i)->setValue($mat['dateOperation']);
			$worksheet->getCell('G'.$i)->setValue($mat['numRP']);
			$worksheet->getCell('H'.$i)->setValue($mat['montant']);

			$i++;
		}
		

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'liste_paiement.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
		// Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }


}
