<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces;

use App\Entity\Etudiant\Etudiants;
use App\Entity\Etudiant\image;
use App\Form\Etudiant\EtudiantsType;
use App\Twig\ConfigExtension;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
// Include JSON Response
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Repository\EtudiantsRepository;
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
use App\Entity\Etudiant\Absence;
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
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

use App\Entity\Filiere;
use App\Entity\Edt\Enseignements;
use App\Service\InternetTest;


class ScolariteController extends AbstractController
{

/**
     * @Route("/get_maquette_module", name="get_maquette_module")
     *  @droitAcces("is_granted('ROLE_ADMIN') or is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ')  ")
     */
    public function get_maquette_moduleAction(secure $security,Request $request, MailerInterface $mailer  , InternetTest $int,Pdf $knpSnappyPdf)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
		$em1 = $this->getDoctrine()->getManager();
		$param= new ConfigExtension($em1);

		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config); 

		$anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
		$codes=array();
		$usr = $security->getUser();
		foreach($usr->getCodes() as $code){
			if(strpos($code, 'FIL') !== false){
				$code=explode('_',$code);
				
				array_push($codes,$code[1]);
			}
			
		}
		$module =array();
		$liste_modules = $em->getRepository(Etudiants::class)->get_liste_module($conn,$codes);
		$liste_importer = $em->getRepository(Etudiants::class)->get_liste_module_importer($conn,$anneeUniversitaire['COD_ANU'],$param->app_config('COD_CMP'),$codes);
		$liste_filiere = $em1->getRepository(Filiere::class)->findAll();
		//$i = array_search($liste_modules[15]["COD_ELP"], array_column($liste_importer, 'COD_ELP'));
		
        foreach ($liste_modules as $mod) {
			$i = array_search($mod["COD_ELP"], array_column($liste_importer, 'COD_ELP'));

			$code_filiere="";

			foreach ($liste_filiere as $key => $value) {
			if(strpos($mod['COD_ELP'] , $value->getCodeApo())!==false){
				$code_filiere = $value->getCodeEtab() ;
				break;
			}
			}

            $array_filiere = array('filiere' => $code_filiere);
	

			if($i !== false){
				$mod = $mod + $array_filiere ;
				$array1 = array($mod, "OK");
				array_push($module,$array1 );
			
			}else{
		
				$mod = $mod + $array_filiere ;
				$array1 = array($mod, "-");
				array_push($module,$array1 );
				
			}
	

		}

		$filiere_string_result = "";

		foreach ($module as $key => $value) {
		$filiere_string =  implode(",",$module[$key][0]);
		$filiere_string_result =  $filiere_string_result .",". $filiere_string ;
		}
		$count_string_result = array_count_values(explode(",",$filiere_string_result));
		
//dd($count_string_result);
		return $this->render('scolarite/import_modules.html.twig',['module' => $module , 'count_string'=>$count_string_result ,'annee_univ'=>$anneeUniversitaire['COD_ANU'] , 'filiere' => $codes ]);
		
    }

	/**
     * @Route("/documents/{type}", name="documents")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR')")
     */
    public function documentsAction($type,Request $request)
    {
        $em = $this->getDoctrine()->getManager('etudiant');

        $releves       = $em->getRepository(EtuReleveAttestation::class)->findby(array('decision' => '-1','typeF' => 'FI'));
        $cartes        = $em->getRepository(EtuDiplomeCarte::class)->findby(array('decision' => '-1','type' => 'Carte','typeF' => 'FI'));
		$attestations  = $em->getRepository(EtuAttestation::class)->findby(array('decision' => '-1','typeF' => 'FI'));
		$reinscription = $em->getRepository(Reinscription::class)->findby(array('statut' => '-1'));

		if($type=='attestation'){
			return $this->render('scolarite/attestation1.html.twig',['attestations' => $attestations]);
		}elseif($type=='carte'){
			return $this->render('scolarite/cartes1.html.twig',['cartes' => $cartes]);
		}elseif($type=='releve'){
			return $this->render('scolarite/releve1.html.twig',['releves' => $releves]);
		}elseif($type=='reinscription'){
		return $this->render('scolarite/reinscription.html.twig',['reinscription' => $reinscription]);
		}
        
		

    }

	#[Route('/app_counter_sco', name: 'app_counter_sco', methods: ['POST'])]
    public function app_counter(secure $security ) {
		$em = $this->getDoctrine()->getManager('etudiant');
		$result=[];
        $releves = $em->getRepository(EtuReleveAttestation::class)->findby(array('decision' => '-1','typeF' => 'FI'));
        $cartes = $em->getRepository(EtuDiplomeCarte::class)->findby(array('decision' => '-1','type' => 'Carte','typeF' => 'FI'));
		$attestations = $em->getRepository(EtuAttestation::class)->findby(array('decision' => '-1' ,'typeF' => 'FI'));
		$reinscription = $em->getRepository(Reinscription::class)->findby(array('statut' => '-1'));

		$result['attestation']= count($attestations);
		$result['releve']= count($releves);
		$result['carte']= count($cartes);
		$result['reinscription']= count($reinscription);
		$result['totale']= count($attestations)+count($releves)+count($cartes)+count($reinscription);

        return new JsonResponse($result);
    }

    /**
     * @Route("/statScolarite", name="statScolarite")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV')")
     */
    public function statScolariteAction()
    {
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		$em = $this->getDoctrine()->getManager('etudiant');

		$anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivAll($conn);		
		
        
		return $this->render('scolarite/statistiques1.html.twig',['anneeUniversitaire' => $anneeUniversitaire]);
    }

	 /**
     * @Route("/statByPays/{annee}", name="statByPays")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV')")
     */
    public function statPays($annee)
    {
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em = $this->getDoctrine()->getManager('etudiant');

		$nb_etu_by_pays =$em->getRepository(Etudiants::class)->getNbetudiantByPays($conn,$annee);  
		
		return new JsonResponse($nb_etu_by_pays) ;
    }

	 /**
     * @Route("/getstatScolarite/{annee}", name="getstatScolarite")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_SG') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV')")
     */
    public function getstatScolarite($annee=null)
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

		$nbEtudiants= $em->getRepository(Etudiants::class)->nbEtudiantByGenre($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
		
		$nbGarçons = $em->getRepository(Etudiants::class)->nbEtudiantByGenre($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'M');

		$nbFilles = $em->getRepository(Etudiants::class)->nbEtudiantByGenre($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'F');

		$nvIns = $em->getRepository(Etudiants::class)->nbInsNVByAnnee($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
		 
		$statistiques['nvIns']=$nvIns[0];
		$statistiques['totales']=$nbEtudiants[0];
		$statistiques['nbGarçons']=$nbGarçons[0];
		$statistiques['nbFilles']=$nbFilles[0];

		return new JsonResponse($statistiques);
		


    }

	/**
     * @Route("/getCapaciteAsEtape/{annee}", name="getCapaciteAsEtape")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV')")
     */
    public function getCapaciteAsEtape($annee=null)
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
		$nbAsEtp = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);        
        return new JsonResponse($nbAsEtp);
	    
    }

	/**
     * @Route("/getCapaciteAsEtapeNV/{annee}", name="getCapaciteAsEtapeNV")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV')")
     */
    public function getCapaciteAsEtapeNV($annee=null)
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
		$nbAsEtpN = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
        
        return new JsonResponse($nbAsEtpN);
	    
    }

	/**
     * @Route("/getCapaciteAsDiplome/{annee}", name="getCapaciteAsDiplome")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV')")
     */
    public function getCapaciteAsDiplome($annee=null)
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
		$nbAsDip = $em->getRepository(Etudiants::class)->nbInsAsDiplome($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
        
        return new JsonResponse($nbAsDip);
	    
    }

	/**
     * @Route("/getevolutionEffectif/{annee}", name="getevolutionEffectif")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV') ")
     */
    public function getevolutionEffectif($annee=null)
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
		$effectifs = $em->getRepository(Etudiants::class)->evolutioneffectif($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
        return new JsonResponse($effectifs);
	    
    }

    /**
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     * @Route("/decisionDoc/{type}/{id}",name="decisionDoc")
     */
    public function decisionDocAction(MailerInterface $mailer  , InternetTest $int,Request $request,$type,$id,secure $security)
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
            	$Motifs = $motifs;
            	$document->setMotif("votre demande à été refusé : ".$Motifs);
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

         return new RedirectResponse($this->generateUrl('documents', array('type'=>$type)));


    }

    /**
     * @Route("/listUsersScolarite", name="listUsersScolarite")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_PROF') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV') ")
     */
    public function listUsersScolariteAction(secure $security,Request $request, Connection $conn)
    {
    	
	    	$em = $this->getDoctrine()->getManager('etudiant');
			$users = $em->getRepository(Etudiants::class)->findBy(array('type' => 'FI'));

	        return $this->render('scolarite/usersListe1.html.twig', array('users' => $users));

	    
    }








 
 /**
     * @Route("/addEtudiantScolarite", name="addEtudiantScolarite")
     * @droitAcces("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_ADMIN')")
     */
    public function addEtudiantScolarite(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer  , InternetTest $int)
    {
    	
		$etudiant = new Etudiants();
        $form = $this->createForm(EtudiantsType::class, $etudiant);
		$em = $this->getDoctrine()->getManager('etudiant');

        $form->handleRequest($request);
           //&& $form->isValid()
       if ($form->isSubmitted() ) {

      /*   $etudiant->setPassword(
            $passwordEncoder->encodePassword(
                $etudiant,
                $form->get('password')->getData()
            )
        ); */
       
            $etudiant->setNom($form['nom']->getData());  
            $etudiant->setPrenom($form['prenom']->getData());
			$etudiant->setEnable(1);
			$etudiant->setRoles(array("ROLE_USER"));
			$etudiant->setCarte('NON');
			$etudiant->setCode($form['code']->getData());
			$etudiant->setNomUtilisateur($form['nomUtilisateur']->getData());
			$etudiant->setEmail($form['email']->getData());
			$etudiant->setLocale($form['locale']->getData());

			$password =  str_shuffle('abcdef123456');
            $encoded = $passwordEncoder->encodePassword($etudiant, $password);
            $etudiant->setPassword($encoded);

			$etudiant->getImage()->manualRemove($etudiant->getImage()->getAbsolutePath());
            $etudiant->getImage()->upload();

			$html=$this->renderView('scolarite/creation-etudiant-email.html.twig',array(
                'etudiant' => $etudiant , 'password' =>$password , 'email'=> $etudiant->getEmail() ));

			$message = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-DOC ENSAT'))
                ->to($form['email']->getData())
                ->subject('Création de votre compte E-DOC')
                ->html($html)
                ;
            try {
                       if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
				
            } catch (TransportExceptionInterface $e) {
            
            }

			$em->persist($etudiant);
			$em->flush();
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            return $this->redirectToRoute('addEtudiantScolarite', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('scolarite/new-etudiants.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,

        ]);


	    
    } 




 /**
     * @Route("/addEtudiantScolariteMass", name="addEtudiantScolariteMass")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function addEtudiantScolariteMass(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer  , InternetTest $int)
    {
    	
       // $form = $this->createForm(EtudiantsType::class);
		$em = $this->getDoctrine()->getManager('etudiant');
		//$form->handleRequest($request);

		//if ($form->isSubmitted() ) {
			
		if (!empty($_FILES['fichier'])) {
			

			//UPLOAD DU FICHIER CSV, vérification et insertion en BASE
			if (isset($_FILES["fichier"]["type"]) != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
				die("Ce n'est pas un fichier de type .xslx");
			} elseif (is_uploaded_file($_FILES['fichier']['tmp_name'])) {

				

				$inputFile = $_FILES['fichier']['tmp_name'];
				$reader = new XlsxReader();
				$objPHPExcel = $reader->load($inputFile);
				// on séléctionne le bonne feuille du document
				$sheet = $objPHPExcel->getSheet(0);
				// on sauvegarde le nombre de lignes du document.
				$highestRow = $sheet->getHighestRow();
				// on sauvegarde le nombre d colonnes du document.
				$highestColumn = 'F';//$sheet->getHighestColumn() ;//'F';
				

				for ($i = 2; $i <= $highestRow; $i++)
				{ 
					set_time_limit(1000); 
					// On range la ligne dans l'ordre 'normal'
					$rowData =  $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,NULL,TRUE,FALSE);
					// rowData est un tableau contenant les données de la ligne
				
		
					$rowData = $rowData[0];

					$username = $rowData[0];
					//$password = $rowData[1];
					$email = $rowData[0];
					$codeapogee = $rowData[1];
					$nom = $rowData[2];
					$prenom = $rowData[3];

					$etudiant = new Etudiants();
			/* 	
					$encoded = $encoder->encodePassword($etudiant, $password );
					$etudiant->setPassword($encoded); */


					$password =  str_shuffle('abcdef123456');
					$encoded = $passwordEncoder->encodePassword($etudiant, $password);
					$etudiant->setPassword($encoded);


					$etudiant->setEmail($email);
					$etudiant->setNomUtilisateur($username);
					$etudiant->setCode($codeapogee);
					$etudiant->setNom($nom);
					$etudiant->setPrenom($prenom);

					if($_POST["type"]=="FI"){
						$etudiant->setType('FI');
					}
					if($_POST["type"]=="FC"){
						$etudiant->setType('FC');
					}

					$etudiant->setLocale('fr-FR');
					$etudiant->setCarte('NON');
					$etudiant->setEnable(1);
					$etudiant->setRoles(array("ROLE_USER"));		
					$etudiant->getImage()->manualRemove($etudiant->getImage()->getAbsolutePath());
					$etudiant->getImage()->upload();


	       $html=$this->renderView('scolarite/creation-etudiant-email.html.twig',array(
                'etudiant' => $etudiant , 'password' =>$password , 'email'=> $etudiant->getEmail() ));

                $em->persist($etudiant);

			$message = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-ENSAT'))
                ->to($etudiant->getEmail())
                ->subject('Création de votre compte E-DOC')
                ->html($html)
                ;
            try {
                       if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
		
            } catch (TransportExceptionInterface $e) {
            
            }



				
				
					
				}
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
			} else {
				$this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
		            }

	}else{
		$this->get('session')->getFlashBag()->add('danger', "le fichier est vide");
		
   } 
        
        return $this->redirectToRoute('addEtudiantScolarite') ; 

	    
    } 









 /**
     * @Route("/addEtudiantScolariteMassMoodle", name="addEtudiantScolariteMassMoodle")
     * @droitAcces("is_granted('ROLE_ADMIN')")
     */
    public function addEtudiantScolariteMassMoodle(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer  , InternetTest $int)
    {
    	
       // $form = $this->createForm(EtudiantsType::class);
		$em = $this->getDoctrine()->getManager('etudiant');
		//$form->handleRequest($request);

		//if ($form->isSubmitted() ) {
			
		if (!empty($_FILES['fichier_moodle'])) {
			

			//UPLOAD DU FICHIER CSV, vérification et insertion en BASE
			if (isset($_FILES["fichier_moodle"]["type"]) != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
				die("Ce n'est pas un fichier de type .xslx");
			} elseif (is_uploaded_file($_FILES['fichier_moodle']['tmp_name'])) {

				

				$inputFile = $_FILES['fichier_moodle']['tmp_name'];
				$reader = new XlsxReader();
				$objPHPExcel = $reader->load($inputFile);
				// on séléctionne le bonne feuille du document
				$sheet = $objPHPExcel->getSheet(0);
				// on sauvegarde le nombre de lignes du document.
				$highestRow = $sheet->getHighestRow();
				// on sauvegarde le nombre d colonnes du document.
				$highestColumn = 'F';//$sheet->getHighestColumn() ;//'F';
				
// 2 -> highestrow
				for ($i = 1554; $i <= 1554; $i++)
				{ 
					set_time_limit(9000); 
					// On range la ligne dans l'ordre 'normal'
					$rowData =  $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,NULL,TRUE,FALSE);
					// rowData est un tableau contenant les données de la ligne
				
		
					$rowData = $rowData[0];

					//$password = $rowData[1];
					$email = $rowData[0];
					$password = $rowData[1];
					$nom = $rowData[2];
					$prenom = $rowData[3];
		

	       $html=$this->renderView('scolarite/creation-etudiant-moodle.html.twig',array(
                'nom' => $nom , 'prenom' => $prenom , 'password' =>$password , 'email'=> $email ));

       //  dd($highestRow);

		 	$message = (new TemplatedEmail())
                ->from(new Address('gcvre@uae.ac.ma', 'E-ENSAT'))
                ->to($email)
                ->subject('Création de votre compte Moodle ENSAT')
                ->html($html)
                ;
            try {
                       if($int->pingGmail() == 'alive'){
                       $mailer->send($message);
                    }
		
            } catch (TransportExceptionInterface $e) {
            
            } 



				
				
					
				}
				//$em->flush();
				$this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
			} else {
				$this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
		            }

	}else{
		$this->get('session')->getFlashBag()->add('danger', "le fichier est vide");
		
   } 
        
        return $this->redirectToRoute('addEtudiantScolarite') ; 

	    
    } 













    /**
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_PROF') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV') ")
     * @Route("/detailUser/{id}",name="detailUser")
     */
    public function detailUserAction($id)
    {
    	
	    	$em = $this->getDoctrine()->getManager('etudiant');
			$em1 = $this->getDoctrine()->getManager('default');
			$param= new ConfigExtension($em1);
			$usr = $em->getRepository(Etudiants::class)->find($id);
			$image = $em->getRepository(image::class)->find($usr->getImage());
			
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
			$res = explode(",", $param->app_config('typeResultat1'));
			$res_etudiant= $em->getRepository(Etudiants::class)->insAdmValidInd_show($etudiant["COD_IND"],$conn,$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$res);

			$initiale = explode(",", $param->app_config('initiale'));
			$releves = $em->getRepository(EtuReleveAttestation::class)->findby(array('codeEtudiant' => $usr));
			$diplomes = $em->getRepository(EtuDiplomeCarte::class)->findby(array('codeEtudiant' => $usr));
			$attestations = $em->getRepository(EtuAttestation::class)->findby(array('codeEtudiant' => $usr));

			$resultats_elp = $em->getRepository(Etudiants::class)->resultat_elp($conn,$initiale,$param->app_config('master'),$etudiant["COD_IND"],$anneeUniversitaire["COD_ANU"]);

            $resultats_vet = $em->getRepository(Etudiants::class)->resultat_vet_global($conn,$etudiant["COD_IND"],$param->app_config('typeResultat'),$anneeUniversitaire["COD_ANU"]);


            if ($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER')){
                $absences = $em->getRepository(Absence::class)->findby(array('idUser' => $usr,'anneeuniv' => $anneeUniversitaire['COD_ANU']));
            }else{
                $absences = $em->getRepository(Absence::class)->findby(array('idUser' => $usr,'anneeuniv' => $anneeUniversitaire['COD_ANU'],'idProf' => $usr->getId()));
            }

            $details = $this->unique_multidim_array($resultats_elp,'COD_ELP','NOT_ELP','COD_ANU');
            $details1 = $this->unique_multidim_array($resultats_vet,'COD_ETP','NOT_VET','COD_ANU');

			
			
			return $this->render('scolarite/showUser1.html.twig', ['absences' => $absences,'details' => $details , 'details1' => $details1,'image' => $image->getPath(),'etudiant' => $etudiant,'releves' => $releves,'diplomes' => $diplomes,'attestations' => $attestations,'ins_Adm_E' => $ins_Adm_E, 'ins_Peda_E' => $ins_Peda_E,'res_etudiant' => $res_etudiant,'anneeUniversitaire'=> $anneeUniversitaire,'groupe' => $gr,
        		'base_dir' => $this->getParameter('kernel.project_dir') . '/../','user' =>$usr]);

	    

    }

	#[Route(path: '/resultat/{id}', name: 'app_resultat')]
    public function resultat($id, Request $request)
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

        $initiale = explode(",", $conf->app_config('initiale'));

		$resultats_elp = $em->getRepository(Etudiants::class)->resultat_elp($conn,$initiale,$conf->app_config('master'),$etudiant["COD_IND"]);

		$resultats_vet = $em->getRepository(Etudiants::class)->resultat_vet($conn,$etudiant["COD_IND"],$conf->app_config('typeResultat'));


		$details = $this->unique_multidim_array($resultats_elp,'COD_ELP','NOT_ELP','COD_ANU');
		$details1 = $this->unique_multidim_array($resultats_vet,'COD_ETP','NOT_VET','COD_ANU');

		$found_key = array_search('IIAP1', array_column($details1, 'COD_ETP'));
		//return new JsonResponse($details)	;
		return $this->render('scolarite/releve_note.html.twig', ['image' => $usr->getImage()->getPath() ,'groupe' => $gr,'details' => $details,'details1' => $details1,'etudiant' => $etudiant,'ins_Peda_E' => $ins_Peda_E,'ins_Adm_E' => $ins_Adm_E
        ]);	
		
    }

	public static function unique_multidim_array($array, $key,$note,$annee) {

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
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     * @Route("/removedoc/{type}/{id}",name="removedoc")
    */
    public function removedocAction(secure $security,Request $request,$id,$type,Connection $conn)
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

	        return $this->render('scolarite/usersListe.html.twig', array('users' => $users));

	    

    }
	/**
     * @Route("/carteQR/{id}", name="carteQR")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function carteQRAction(Pdf $knpSnappyPdf ,$id)
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
        $html = $this->renderView('scolarite/carteQR.html.twig', array(
        	'dataUri' => $dataUri ,
        	'usere'    => $usr,
			'image'   => $image->getPath(),
        	'ins_Adm_E' => $ins_Adm_E,
            'base_dir' => $this->getParameter('kernel.project_dir')
        ));
		return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            'carte_'.$usr->getNom().'_'.$usr->getPrenom().'.pdf' ,
        );
                
    }
	
	
    /**
     * @Route("/carteQRALL", name="carteQRALL")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function carteQRALLAction(Pdf $knpSnappyPdf)
    {
        $em = $this->getDoctrine()->getManager('etudiant');

        $documents = $em->getRepository(EtuDiplomeCarte::class)->findBy(array('decision' => '-1','type' => 'Carte','typeF' => 'FI'));
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
		
        $html = $this->renderView('scolarite/carteQRAll.html.twig', array(
        	'carteALL' => $carteALL ,
            'base_dir' => $this->getParameter('kernel.project_dir'). '/../'
        ));

		return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            'carte_All.pdf' ,
        );
        
        
    }
	/**
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV')")
     * @Route("/exporterStatSco",name="exporterStatSco")
    */
    public function exporterStatAction(Request $request) {

		
    	$em = $this->getDoctrine()->getManager('etudiant');
		
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$em1 = $this->getDoctrine()->getManager();

		$param= new ConfigExtension($em1);
		$initiale = explode(",", $param->app_config('initiale'));
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
        $worksheet->getCell('F4')->setValue('Nouvaux inscrits Filles'); 
        $worksheet->getCell('G4')->setValue('Nouvaux inscrits Garçons');
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

		$nbAsEtp = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);        
		$nbAsEtpN = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);        
		$nbAsDip = $em->getRepository(Etudiants::class)->nbInsAsDiplome($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn);
		$nbAsEtpNG = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'M');        
		$nbAsEtpNF = $em->getRepository(Etudiants::class)->nbInsAsEtapeNV($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'F');        
		
		$nbGarçons = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'M');        
		$nbFilles = $em->getRepository(Etudiants::class)->nbInsAsEtape($anneeUniversitaire['COD_ANU'],$param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$initiale,$param->app_config('master'),$conn,'F');        


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
        $fileName = 'statistiquesScolarite.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
	
	
		
	/**
     * @Route("/add_list_releve", name="add_list_releve")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function add_list_releveAction(secure $security,Request $request,MailerInterface $mailer  , InternetTest $int)
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

					$html=$this->renderView('scolarite/emailinformation.html.twig',array('etudiant' => $document->getCodeEtudiant() ,'decision' => $document->getDecision(),'motif' => $Motifs));
		
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
     * @Route("/add_list_attestation", name="add_list_attestation")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function add_list_attestationAction(secure $security,Request $request, MailerInterface $mailer  , InternetTest $int)
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
            		$html = $this->renderView('scolarite/emailinformation.html.twig',array('etudiant' => $document->getCodeEtudiant() ,'decision' => $document->getDecision(),'motif' => $Motifs));
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
     * @Route("/get_atestation_sco_fc/{id}/{annee}", name="get_atestation_scofc")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function getAtestationScoFCAction(secure $security,Request $request, MailerInterface $mailer,$id,Pdf $knpSnappyPdf,$annee)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
        $param= new ConfigExtension($em1);
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' =>$_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        
        $etudiant = $em->getRepository(Etudiants::class)->attestationScByInd($id,$conn,$annee,$param->app_config('cod_sig'));
        
        $filename = 'Attestation_'.$etudiant["COD_ETU"] .'.pdf';
        $html = $this->renderView('scolarite/attestation_scolarite.html.twig', [
            'etudiant' =>$etudiant,
        ]);
        
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            $filename ,
        );
        
    }




/**
     * @Route("/get_atestation_sco/{id}", name="get_atestation_sco")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function getAtestationScoAction(secure $security,Request $request, MailerInterface $mailer  , InternetTest $int,$id,Pdf $knpSnappyPdf)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $usr = $em->getRepository(Etudiants::class)->find($id);
        $em1 = $this->getDoctrine()->getManager();
        $param= new ConfigExtension($em1);
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' =>$_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        
        $anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);

        $etudiant = $em->getRepository(Etudiants::class)->attestationScByInd($usr->getCode(),$conn,$anneeUniversitaire['COD_ANU'],$param->app_config('cod_sig'));
        //dd($etudiant);
        $filename = 'Attestation_'.$etudiant["COD_ETU"] .'.pdf';
        $html = $this->renderView('scolarite/attestation_scolarite.html.twig', [
            'etudiant' =>$etudiant,
        ]);
        //dd($etudiant);
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            $filename ,
        );
        
    }

    /**
     * @Route("/get_atestation_reussite/{id}", name="get_atestation_reussite")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function getAtestationReussiteAction(secure $security,Request $request, MailerInterface $mailer  , InternetTest $int,$id,Pdf $knpSnappyPdf)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
        $param= new ConfigExtension($em1);

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' =>$_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $document = $em->getRepository(EtuReleveAttestation::class)->find($id);
        $etudiant = $em->getRepository(Etudiants::class)->attestationReussiteByInd($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$document,$conn);
        $mention='';
		if($etudiant["NOT_VET"]>=10 && $etudiant["NOT_VET"]<12){
			$mention='Passable';
		}elseif($etudiant["NOT_VET"]>=12 && $etudiant["NOT_VET"]<14){
			$mention='Assez Bien';
		}elseif($etudiant["NOT_VET"]>=14 && $etudiant["NOT_VET"]<16){
			$mention='Bien';
		}elseif($etudiant["NOT_VET"]>=16 && $etudiant["NOT_VET"]<18){
			$mention='Très Bien';
		}elseif($etudiant["NOT_VET"]>=18 && $etudiant["NOT_VET"]<20){
			$mention='EXCELLENT';
		}
        $filename = 'Attestation_Reussite_'.$etudiant["COD_ETU"] .'.pdf';
        $html = $this->renderView('scolarite/attestation_reussite.html.twig', [
            'etudiant' =>$etudiant,
            'mention' => $mention,
        ]);
        
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            $filename ,
        );
        
    }

    /**
     * @Route("/get_releve_note/{id}", name="get_releve_note")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function getReleveNoteAction(secure $security,Request $request, MailerInterface $mailer  , InternetTest $int,$id,Pdf $knpSnappyPdf)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
        $param= new ConfigExtension($em1);

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' =>$_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $document = $em->getRepository(EtuReleveAttestation::class)->find($id);
        $etudiant = $em->getRepository(Etudiants::class)->attestationReussiteByInd($param->app_config('ETA_IAE'),$param->app_config('COD_CMP'),$document,$conn);
        $initiale = explode(",", $param->app_config('initiale'));
        
        $resultats_elp = $em->getRepository(Etudiants::class)->releve_note_etu($conn,$document->getCodeEtape(),$etudiant["COD_IND"],$document->getAnneeEtape(),$document->getVersion1());
        $rang = $em->getRepository(Etudiants::class)->rang_vet($conn,$document->getCodeEtape(),$document->getAnneeEtape());
        $details = $this->unique_multidim_array($resultats_elp,'COD_ELP','NOT_ELP','COD_ANU');
         $mention='';
		if($details[0]["NOT_VET"]>=10 && $details[0]["NOT_VET"]<12){
			$mention='Passable';
		}elseif($details[0]["NOT_VET"]>=12 && $details[0]["NOT_VET"]<14){
			$mention='Assez Bien';
		}elseif($details[0]["NOT_VET"]>=14 && $details[0]["NOT_VET"]<16){
			$mention='Bien';
		}elseif($details[0]["NOT_VET"]>=16 && $details[0]["NOT_VET"]<18){
			$mention='Très Bien';
		}elseif($details[0]["NOT_VET"]>=18 && $details[0]["NOT_VET"]<20){
			$mention='EXCELLENT';
		}
        $filename = 'Releve_Note'.$etudiant["COD_ETU"] .'.pdf';
        $html = $this->renderView('scolarite/releve_note_pdf.html.twig', [
            'etudiant' =>$etudiant,
            'details' =>$details,
            'rang'  =>$rang,
            'mention' => $mention,
        ]);
        $footer = $this->renderView( 'scolarite/footer.html.twig' ,['details' =>$details]);
        $header = $this->renderView( 'scolarite/header.html.twig' ,['details' =>$details]);
        $options = [
            'margin-top'    =>50,
            'margin-left'   =>3,
            'margin-bottom' =>20,
            'footer-html' =>$footer,
            'header-html' =>$header,
        ];
        
        
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            $filename ,
        );
        
    }

   

    /**
     * @Route("/mod_releve_version/{id}/{version}", name="mod_releve_version")
     * @droitAcces("is_granted('ROLE_SCOLARITE')")
     */
    public function modReleveVersionAction(secure $security,Request $request, MailerInterface $mailer  , InternetTest $int,$id,$version,Pdf $knpSnappyPdf) {
        $em = $this->getDoctrine()->getManager('etudiant');
        
        $document = $em->getRepository(EtuReleveAttestation::class)->find($id);
        
        $document->setVersion1($version);
        $em->persist($document);
        $em->flush();
        return new JsonResponse('1');
    }



/**
     * @Route("/liste_etudiant", name="liste_etudiant")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_SERVICEEXT') ")
     */
    public function liste_etudiant()
    {
		$em = $this->getDoctrine()->getManager('etudiant');
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		$annees = $em->getRepository(Etudiants::class)->getAnneeUnivAll($conn);
        return $this->render('scolarite/get_liste_etudiant.html.twig', [
            'annees' => $annees,
        ]);
    }

	/**
     * @Route("/get_liste_etudiant", name="get_liste_etudiant")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_SERVICEEXT') ")
     */
    public function get_liste_etudiant(Request $request)
    {
		
		$importFile = $request->get('importFile');
		extract($importFile);
		$em1 = $this->getDoctrine()->getManager();
		$param= new ConfigExtension($em1);
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$cn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		$spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Liste des étudiants");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        //Rename sheet
        $worksheet->setTitle('Etudiants');

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
       
        $worksheet->mergeCells("B2:P2");   
        $worksheet->getCell('B2')->setValue(' Université Abdelmalek Essaâdi Ecole Nationale des Sciences Appliquées Tanger ');
        $worksheet->getStyle('B2')->getAlignment()->setWrapText(true);
        $worksheet->getStyle('B2')->applyFromArray($styleArray);

        $worksheet->getCell('B4')->setValue('Code Apogée');
        $worksheet->getCell('C4')->setValue('Nom');
        $worksheet->getCell('D4')->setValue('Prénom');
        $worksheet->getCell('E4')->setValue('N° CNE');
        $worksheet->getCell('F4')->setValue('N° CIN');
        $worksheet->getCell('G4')->setValue('Date de Naissance');
        $worksheet->getCell('H4')->setValue('Lieu de Naissance');
        $worksheet->getCell('I4')->setValue('Sexe');
		$worksheet->getCell('J4')->setValue('الإسم العائلي  ');
        $worksheet->getCell('K4')->setValue('الإسم الشخصي  ');
        $worksheet->getCell('L4')->setValue('مكان الإزدياد');
        $worksheet->getCell('M4')->setValue('Pays');
        $worksheet->getCell('N4')->setValue('Code ');
        $worksheet->getCell('O4')->setValue('Code Version' );
        $worksheet->getCell('P4')->setValue('1ère Inscription' );


        $worksheet->getStyle('B4:P4')->applyFromArray($styleArrayTitle1);

        $worksheet->getStyle('B4:P4')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
       

        $i=5;
        $etudiants_inscrit =$em->getRepository(Etudiants::class)->get_liste_etudiant_inscrits($cn,$annee,$param->app_config('COD_CMP'),$param->app_config('ETA_IAE'),$typeDocument);
        $etudiants_laureat =$em->getRepository(Etudiants::class)->get_liste_etudiant_laureat($cn,$annee,$typeDocument);
		if($listeDoc=='LI'){
			foreach ($etudiants_inscrit as $etudiant) {
				$worksheet->getStyle("B".$i.":P".$i)->applyFromArray($styleArrayTitle);
				$worksheet->getCell('B'.$i)->setValue($etudiant['COD_ETU']);
				$worksheet->getCell('C'.$i)->setValue($etudiant['LIB_NOM_PAT_IND']);
				$worksheet->getCell('D'.$i)->setValue($etudiant['LIB_PR1_IND']);
				$worksheet->getCell('E'.$i)->setValue($etudiant['COD_NNE_IND']);
				$worksheet->getCell('F'.$i)->setValue($etudiant['CIN_IND']);
				$worksheet->getCell('G'.$i)->setValue($etudiant['DATE_NAI_IND']);
				$worksheet->getCell('H'.$i)->setValue($etudiant['LIB_VIL_NAI_ETU']);
				$worksheet->getCell('I'.$i)->setValue($etudiant['COD_SEX_ETU'] );
				$worksheet->getCell('J'.$i)->setValue($etudiant['LIB_NOM_IND_ARB']);
				$worksheet->getCell('K'.$i)->setValue($etudiant['LIB_PRN_IND_ARB']);
				$worksheet->getCell('L'.$i)->setValue($etudiant['LIB_VIL_NAI_ETU_ARB']);
				$worksheet->getCell('M'.$i)->setValue($etudiant['LIB_PAY']);
				$worksheet->getCell('N'.$i)->setValue($etudiant['COD_ETP']);
				$worksheet->getCell('O'.$i)->setValue($etudiant['COD_VRS_VET']);
				$ins_Adm_E=$cn->fetchAssociative("SELECT a.LIB_ANU FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP=e.COD_ETP and ie.COD_IND='".$etudiant['COD_IND']."' AND COD_CMP='ENT' AND ETA_IAE='E' order by ie.COD_ANU ASC");
				if($ins_Adm_E){
					$worksheet->getCell('P'.$i)->setValue($ins_Adm_E['LIB_ANU']);
				}else{
					$worksheet->getCell('P'.$i)->setValue("-");
				}
				
				$i++;
			}
		}elseif($listeDoc=='LL'){
			foreach ($etudiants_laureat as $etudiant) {
				$worksheet->getStyle("B".$i.":P".$i)->applyFromArray($styleArrayTitle);
				$worksheet->getCell('B'.$i)->setValue($etudiant['COD_ETU']);
				$worksheet->getCell('C'.$i)->setValue($etudiant['LIB_NOM_PAT_IND']);
				$worksheet->getCell('D'.$i)->setValue($etudiant['LIB_PR1_IND']);
				$worksheet->getCell('E'.$i)->setValue($etudiant['COD_NNE_IND']);
				$worksheet->getCell('F'.$i)->setValue($etudiant['CIN_IND']);
				$worksheet->getCell('G'.$i)->setValue($etudiant['DATE_NAI_IND']);
				$worksheet->getCell('H'.$i)->setValue($etudiant['LIB_VIL_NAI_ETU']);
				$worksheet->getCell('I'.$i)->setValue($etudiant['COD_SEX_ETU'] );
				$worksheet->getCell('J'.$i)->setValue($etudiant['LIB_NOM_IND_ARB']);
				$worksheet->getCell('K'.$i)->setValue($etudiant['LIB_PRN_IND_ARB']);
				$worksheet->getCell('L'.$i)->setValue($etudiant['LIB_VIL_NAI_ETU_ARB']);
				$worksheet->getCell('M'.$i)->setValue($etudiant['LIB_PAY']);
				$worksheet->getCell('N'.$i)->setValue($etudiant['COD_DIP']);
				$worksheet->getCell('O'.$i)->setValue($etudiant['COD_VRS_VDI']);

				$ins_Adm_E=$cn->fetchAssociative("SELECT a.LIB_ANU FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP=e.COD_ETP and ie.COD_IND='".$etudiant['COD_IND']."' AND COD_CMP='ENT' AND ETA_IAE='E' order by ie.COD_ANU ASC");
				if($ins_Adm_E){
					$worksheet->getCell('P'.$i)->setValue($ins_Adm_E['LIB_ANU']);
				}else{
					$worksheet->getCell('P'.$i)->setValue("-");
				}
			
				$i++;
			}
		}
        
       


        

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'liste_etudiants.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
		// Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
        
    }


	/**
     * @Route("/get_date_emploi", name="get_date_emploi")
     * @droitAcces("is_granted('ROLE_SCOLARITE') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_MANAGER') or is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_SERVICEEXT') ")
     */
    public function get_date_emploi(Request $request)
    {
		$importFile = $request->get('emploi');
		extract($importFile);

		$em = $this->getDoctrine()->getManager('edt');
		$planning =$em->getRepository(Enseignements::class)->planning_emploi($datedebut,$datefin);
		$spreadsheet = new Spreadsheet();
        $em = $this->getDoctrine()->getManager();
        //Set metadata.
        $spreadsheet->getProperties()->setTitle("Planning");

        // Get the active sheet.
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        //Rename sheet
        $worksheet->setTitle('Planning');

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

        $worksheet->getCell('B4')->setValue('Libelle');
        $worksheet->getCell('C4')->setValue('Groupe');
        $worksheet->getCell('D4')->setValue('Enseignant');
        $worksheet->getCell('E4')->setValue('Date');
        $worksheet->getCell('F4')->setValue('Heure');
        $worksheet->getCell('G4')->setValue('Salle');
        $worksheet->getCell('H4')->setValue('Type');


        $worksheet->getStyle('B4:H4')->applyFromArray($styleArrayTitle1);

        $worksheet->getStyle('B4:H4')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('09594C');
       

        $i=5;

		foreach ($planning as $mat) {
			$worksheet->getStyle("B".$i.":H".$i)->applyFromArray($styleArrayTitle);
			$worksheet->getCell('B'.$i)->setValue($mat['Exam']);
			$worksheet->getCell('C'.$i)->setValue($mat['Groupe']);
			$worksheet->getCell('D'.$i)->setValue($mat['Enseignant']);
			$worksheet->getCell('E'.$i)->setValue($mat['Date']);
			$worksheet->getCell('F'.$i)->setValue($mat['Heure']);
			$worksheet->getCell('G'.$i)->setValue($mat['nom']);
			$worksheet->getCell('H'.$i)->setValue($mat['alias']);

			$i++;
		}
		

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'Planning.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
		// Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }











}
