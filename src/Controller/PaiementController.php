<?php

namespace App\Controller;

use App\Repository\PaiementRepository;
use App\Entity\Config;
use App\Entity\Personnel;
use App\Entity\Etudiant\Etudiants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Form\paiementType;
use App\Form\paiementEtuNonExistType;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Security as secure;
use App\Entity\Paiement;
use App\Entity\FiliereFcResponsable;

use App\Twig\ConfigExtension;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use NumberToWords\NumberToWords;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PaiementController extends AbstractController
{

    /**
     * @Route("/paiement", name="paiement")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_DIR_ADJ') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function homeAction(Request $request)
    {

        $em1= $this->getDoctrine()->getManager();
        $pai=$em1->getRepository(Paiement::class)->ldBY();
        return $this->render('paiement/index.html.twig',['paiement' => $pai]);

    }

    /**
     *  @Route("/showPaiement/{id}", name="showPaiement")
     *  @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_PROF') or is_granted('ROLE_FC_PAI') ")
     */
    public function showAction($id)
    {


        $em = $this->getDoctrine()->getManager();
        $paiement = $em->getRepository(Paiement::class)->findBY(array('demandeur' => $id));
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $anneeUniversitaire= "";
        $etudiant = "";
        $ins_Peda_E = "" ;	
		$groupe   = ""; 
		$gr='';
        $ins_Adm_E = "" ;

    if( strpos($id,"X")===false){
        $anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $etudiant = $em->getRepository(Etudiants::class)->etudiantByInd($id,$conn);
        $ins_Peda_E = $em->getRepository(Etudiants::class)->insPedLastByInd($etudiant["COD_IND"],$conn,$anneeUniversitaire['COD_ANU']);		
		$groupe   = $em->getRepository(Etudiants::class)->getGroupeByInd($etudiant["COD_IND"],$anneeUniversitaire['COD_ANU'],$conn);
		$gr='';


        $etudiant = $conn->fetchAssociative("SELECT * FROM individu i,adresse a WHERE i.COD_ETU='".$id."' and i.COD_IND = a.COD_IND ");
        $ins_Adm_E=$conn->fetchAllAssociative("SELECT * FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP= e.COD_ETP and ie.cod_ind='".$etudiant['COD_IND']."' order by ie.COD_ANU desc");


        return $this->render('paiement/show.html.twig', array('groupe' => $gr,'ins_Peda_E' => $ins_Peda_E,'image' => 'anonymous.png','paiement' => $paiement,'etudiant' => $etudiant,'ins_Adm_E' => $ins_Adm_E , 'id' => $id));

    }else{

        return $this->render('paiement/show_nonexist.html.twig', array('groupe' => $gr,'ins_Peda_E' => $ins_Peda_E,'image' => 'anonymous.png','paiement' => $paiement,'etudiant' => $etudiant,'ins_Adm_E' => $ins_Adm_E , 'id' => $id));

    }
       

    }

    /**
     * @Route("/editPaiement/{id}", name="editPaiement")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Paiement::class)->find($id);
        $form = $this->createForm(paiementType::class, $entity);
        $etudiant_nom_prenom = $entity->getNom(). " ".$entity->getPrenom() ;
        $formation =  $entity->getFormation();

        return $this->render('paiement/edit.html.twig', array('entity' => $entity,'etudiant'=>$etudiant_nom_prenom,'formation'=>$formation, 'form' => $form->createView()));
    }


    /**
     * @Route("/editPaiementNonExist/{id}", name="editPaiementNonExist")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE')")
     */
    public function editActionNonExist($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Paiement::class)->find($id);
        $form = $this->createForm(paiementEtuNonExistType::class, $entity);
        $etudiant_nom_prenom = $entity->getNom(). " ".$entity->getPrenom() ;
        $formation =  $entity->getFormation();

        return $this->render('paiement/editNonExist.html.twig', array('entity' => $entity,'etudiant'=>$etudiant_nom_prenom,'formation'=>$formation, 'form' => $form->createView()));
    }



      /**
     * @Route("/listPaiementForProf", name="listPaiementForProf")
     *  @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN')")
     */
    public function listPaiementForProf(secure $security)
    {
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $em1 = $this->getDoctrine()->getManager('etudiant');
        $anneeUniversitaire= $em1->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);

        $em = $this->getDoctrine()->getManager();
        $user_id =  $security->getUser()->getId();
        $personnel =  $em->getRepository(Personnel::class)->findBy(array('idUser'=>$user_id));
        $perso_id = $personnel[0]->getId();
  
        $formation  = $em->getRepository(FiliereFcResponsable::class)->findBy(array('responsable'=>$perso_id, 'annee'=>strval($anneeUniversitaire['COD_ANU']) ));

        if($formation!=null){
            $formation = $formation[0]->getFiliereFc()->getCodeApo();
            $pai=$em->getRepository(Paiement::class)->ldBY_prof_histo($formation, $anneeUniversitaire['COD_ANU']);
        }else{
            $pai = null ;
        }
      

       // $filiere_resp = $em->getRepository(FiliereFcResponsable::class)->ldBY_prof(,);
       // $entity = $em->getRepository(Paiement::class)->findBy(array('responsable'=>$perso->getId()))
      
       // dd($pai);

        return $this->render('paiement/index_prof.html.twig',['paiement' => $pai , 'filiere_resp'=> $formation]);

    }

       /**
     * @Route("/listPaiementForProfAnneeEncours
     * ", name="listPaiementForProfAnneeEncours")
     *  @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN')")
     */
    public function listPaiementForProfAnneeEncours(secure $security)
    {
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $em1 = $this->getDoctrine()->getManager('etudiant');
        $anneeUniversitaire= $em1->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);


        $em = $this->getDoctrine()->getManager();
        $user_id =  $security->getUser()->getId();
        $personnel =  $em->getRepository(Personnel::class)->findBy(array('idUser'=>$user_id));
        $perso_id = $personnel[0]->getId();
        $pai=$em->getRepository(Paiement::class)->ldBY_prof($perso_id,$anneeUniversitaire['COD_ANU']);
        $filiere_resp = $em->getRepository(FiliereFcResponsable::class)->findBy(array('responsable'=>$perso_id ,'annee'=>strval($anneeUniversitaire['COD_ANU'])));
       // $entity = $em->getRepository(Paiement::class)->findBy(array('responsable'=>$perso->getId()));
        //dd( $filiere_resp );
        if(!$filiere_resp){
            $filiere_resp = null;
            $pai = null;
        }else{
            $filiere_resp = $filiere_resp[0]->getFiliereFc()->getNomFiliere();
        }
       // dd($pai);

        return $this->render('paiement/index_prof_annee_encours.html.twig',['paiement' => $pai,'filiere_resp'=> $filiere_resp,'annee'=> $anneeUniversitaire['COD_ANU']]);

    }

  /**
     * @Route("/updatePaiement/{id}", name="updatePaiement")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE')")
     */
    public function updateAction(Pdf $knpSnappyPdf ,Request $request, $id ) {
        $em = $this->getDoctrine()->getManager();
        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $paiement = $em->getRepository(Paiement::class)->find($id);
        $etudiant = $conn->fetchAssociative("SELECT * FROM individu i,adresse a WHERE i.COD_ETU='".$paiement->getDemandeur()."' and i.COD_IND = a.COD_IND ");
        $anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $formation = $conn->fetchAssociative("SELECT d.LIB_DIP , etp.COD_DIP FROM diplome d, ins_adm_etp etp , individu i WHERE i.COD_IND=etp.COD_IND AND etp.COD_DIP=d.COD_DIP AND etp.COD_DIP = '".$paiement->getFormation() ."' AND  etp.ETA_IAE='E'  AND i.COD_IND='".$etudiant['COD_IND']."'");
        $ins_Adm_E=$conn->fetchAllAssociative("SELECT * FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP= e.COD_ETP and ie.cod_ind='".$etudiant['COD_IND']."' order by ie.COD_ANU desc");

        
        $form = $this->createForm(paiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isValid()) {

             $em->persist($paiement);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            $numberToWords = new NumberToWords();
            // build a new number transformer using the RFC 3066 language identifier
            $numberTransformer = $numberToWords->getNumberTransformer('fr');
 
            $montant_whole = floor($paiement->getMontant());
            $montant_fraction =  round( ($paiement->getMontant() - $montant_whole)  * 100 ,2) ;

            $html = $this->renderView('paiement/ordre_recette.html.twig', [
                'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
                'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
                'montant_f_num' =>  $montant_fraction ,
                'montant_num' => number_format($paiement->getMontant(), 2, ',', '.') ,
                'num' => $paiement->getNumRP(),
                'paiement' => $paiement,
                'formation' => $formation,
                'ins_Adm_E' => $ins_Adm_E
            ]);


            $dir = $this->getParameter('webroot_doc').'/Ordre_recette/'.$paiement->GetNom().'_'.$paiement->GetPrenom().'/';
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }

            $filename =  $paiement->getLien();

             if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiement->setLien($filename);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiement::class)->save($paiement, true);
         //   $PaiementRepository->save($entity, true);
           // $em->getRepository(Config::class)->updateBy('ORDRE_MISSION_INDEX', $nb_om); 

      

           /*  return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                $filename ,
            );
   */

          }else{

            unlink($dir.$filename);
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiement->setLien($filename);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiement::class)->save($paiement, true);

          }
         

           /*  return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'ordre_recette.pdf' ,
            )
            
            
            ; */

            return $this->redirect($this->generateUrl('showPaiement',array('id'=>$paiement->getDemandeur())));
            
         //   return $this->redirect($this->generateUrl('editPaiement', array('id' => $paiement->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
        
        return $this->render('paiement/edit.html.twig', array('entity' => $paiement, 'form' => $form->createView()));
    }


      /**
     * @Route("/updatePaiementNonExist/{id}", name="updatePaiementNonExist")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE')")
     */
    public function updateActionNonExist(Pdf $knpSnappyPdf ,Request $request, $id ) {
        $em = $this->getDoctrine()->getManager();
   
        $paiement = $em->getRepository(Paiement::class)->find($id);

        $form = $this->createForm(paiementEtuNonExistType::class, $paiement);
        $form->handleRequest($request);

        $etudiant = $paiement->getNom() ." " .$paiement->getPrenom() ;
        $formation = $paiement->getFormation() ;

        if ($form->isSubmitted() && $form->isValid()) {

            $paiement->setFormation($form['formation']->getData()->getCodeApo());
            $formation = $form['formation']->getData()->getNomFiliere();
            $etudiant = $form['nom']->getData() ." " .$form['prenom']->getData()  ;
            $etape = $form['etape'] ;

             $em->persist($paiement);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            $numberToWords = new NumberToWords();
            // build a new number transformer using the RFC 3066 language identifier
            $numberTransformer = $numberToWords->getNumberTransformer('fr');
 
            $montant_whole = floor($paiement->getMontant());
            $montant_fraction =  round( ($paiement->getMontant() - $montant_whole)  * 100 ,2) ;

            $html = $this->renderView('paiement/ordre_recette_nonexist.html.twig', [
                'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
                'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
                'montant_f_num' =>  $montant_fraction ,
                'montant_num' => number_format($paiement->getMontant(), 2, ',', '.') ,
                'num' => $paiement->getNumRP(),
                'paiement' => $paiement,
                'formation' => $formation,
                'etape' => $etape
            ]);


            $dir = $this->getParameter('webroot_doc').'/Ordre_recette/'.$paiement->GetNom().'_'.$paiement->GetPrenom().'/';
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }

            $filename =  $paiement->getLien();

             if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiement->setLien($filename);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiement::class)->save($paiement, true);
    

          }else{

            unlink($dir.$filename);
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiement->setLien($filename);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiement::class)->save($paiement, true);

          }
         

         

            return $this->redirect($this->generateUrl('showPaiement',array('id'=>$paiement->getDemandeur())));
            
        }

        $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
        
        return $this->render('paiement/editNonExist.html.twig', array('entity' => $paiement,'etudiant'=>$etudiant ,'formation'=>$formation , 'form' => $form->createView()));
    }



    /**
     * @Route("/addPaiement/{id}_{annee_univ}_{cod_dip}", name="addPaiement")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE')")
     */

   public function addAction(Pdf $knpSnappyPdf ,Request $request,$id,$annee_univ,$cod_dip)
    {
        $em = $this->getDoctrine()->getManager();
        $tranche= $em->getRepository(Paiement::class)->getTranche($id);

if($tranche == null){
    $tranche = 1;
}else{
    $tranche++;
}


        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $etudiant = $conn->fetchAssociative("SELECT * FROM individu i,adresse a WHERE i.COD_ETU='".$id."' and i.COD_IND = a.COD_IND ");
        $anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
        $ins_Adm_E=$conn->fetchAllAssociative("SELECT * FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP= e.COD_ETP and ie.cod_ind='".$etudiant['COD_IND']."' order by ie.COD_ANU desc");
       // dd( $ins_Adm_E);
        $formation = $conn->fetchAssociative("SELECT d.LIB_DIP , etp.COD_DIP FROM diplome d, ins_adm_etp etp , individu i WHERE i.COD_IND=etp.COD_IND AND etp.COD_DIP=d.COD_DIP AND etp.COD_DIP = '".$cod_dip."' AND  etp.ETA_IAE='E'  AND i.COD_IND='".$etudiant['COD_IND']."'");
        $entity = new Paiement();
        $annee= Date('Y');
        $entity->setAnnee($annee);
        $entity->setDemandeur($id);
        $form = $this->createForm(paiementType::class, $entity);
        $form->handleRequest($request);

       
        
        if ( ($form->isSubmitted() && $form->isValid()) ) {
          
          
            if(  $form['responsable']->getData()==NULL || $form['rubrique']->getData()==NULL || $form['montant']->getData()==NULL  || $form['datePaiement']->getData()==NULL || $form['numRP']->getData()==NULL     ){
 
                $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER_INCOMPLETE");
                return $this->render('paiement/new.html.twig', array('entity' => $entity,'tranche'=>$tranche,'etudiant' => $etudiant,'formation'=>$formation['COD_DIP'],'ins_Adm_E' => $ins_Adm_E, 'form' => $form->createView()));

            }
         
          
            $entity->setType('FC');
            $entity->setNom($etudiant['LIB_NOM_PAT_IND']);
            $entity->setPrenom($etudiant['LIB_PR1_IND']);
            $entity->setCin($etudiant['CIN_IND']);
            $entity->setFormation($formation['COD_DIP']);
            $entity->setAnneeuniv($annee_univ);

           /*  $lastRP = $em->getRepository(Paiement::class)->getLastRPbyAnnee($entity->getAnnee()) ;
           if($lastRP!=null){
            $nb_rp = $lastRP ;
           }else{
            $nb_rp = 0;
           }
            $nb_rp++;
            $entity->setNumRP($nb_rp);
            $entity->setLastrp(1);
            $em->getRepository(Paiement::class)->setLastRPbyAnnee($entity->getAnnee(),$nb_rp); */

            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            // create the number to words "manager" class
            $numberToWords = new NumberToWords();
 
            // build a new number transformer using the RFC 3066 language identifier
            $numberTransformer = $numberToWords->getNumberTransformer('fr');

            $montant_whole = floor($entity->getMontant());
            $montant_fraction =  round( ($entity->getMontant() - $montant_whole) *100  , 2);

            $html = $this->renderView('paiement/ordre_recette.html.twig', [
                'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
                'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
                'montant_f_num' =>  $montant_fraction ,
                'montant_num' => number_format($entity->getMontant(), 2, ',', '.') ,
                'num' => $entity->getNumRP(),
                'paiement' => $entity,
                'formation' => $formation,
                'ins_Adm_E' => $ins_Adm_E
            ]);
 
            $filename = 'default.pdf'; 

            $dir = $this->getParameter('webroot_doc').'/Ordre_recette/'.$entity->GetNom().'_'.$entity->GetPrenom().'/';
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }

             if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $entity->setLien($filename);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiement::class)->save($entity, true);
         //   $PaiementRepository->save($entity, true);

           /*  return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                $filename ,
            );
   */

          }


             /*  return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                $filename ,
            );  */

            return $this->redirect($this->generateUrl('showPaiement',array('id'=>$id)));
        }else{
     //  dd($form);
     return $this->render('paiement/new.html.twig', array('annee_univ' => $annee_univ, 'entity' => $entity,'tranche'=>$tranche,'etudiant' => $etudiant,'formation'=>$formation['COD_DIP'],'ins_Adm_E' => $ins_Adm_E, 'form' => $form->createView()));
        }



       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
        return $this->render('paiement/new.html.twig', array('annee_univ' => $annee_univ, 'entity' => $entity,'tranche'=>$tranche,'etudiant' => $etudiant,'formation'=>$formation['COD_DIP'],'ins_Adm_E' => $ins_Adm_E, 'form' => $form->createView()));
        }

        
        return $this->render('paiement/new.html.twig', array('annee_univ' => $annee_univ, 'entity' => $entity,'tranche'=>$tranche,'etudiant' => $etudiant,'formation'=>$formation['COD_DIP'],'ins_Adm_E' => $ins_Adm_E, 'form' => $form->createView()));
    }



/**
     * @Route("/addPaiementNonExist", name="addPaiementNonExist")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE')")
     */

     public function addActionNonExist(Pdf $knpSnappyPdf ,Request $request)
     {
         $em = $this->getDoctrine()->getManager();
      
         $tranche = 1;
      
        
         $entity = new Paiement();
         $annee= Date('Y');
         $entity->setAnnee($annee);

         $form = $this->createForm(paiementEtuNonExistType::class, $entity);
         $form->handleRequest($request);
 
         
         if ( ($form->isSubmitted() && $form->isValid()) ) {
           
           
             if( $form['etape']->getData()==NULL || $form['formation']->getData()==NULL || $form['responsable']->getData()==NULL || $form['rubrique']->getData()==NULL || $form['montant']->getData()==NULL  || $form['datePaiement']->getData()==NULL || $form['numRP']->getData()==NULL     ){
  
                 $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER_INCOMPLETE");
                 return $this->render('paiement/new_nonexist.html.twig', array('entity' => $entity,'tranche'=>$tranche , 'form' => $form->createView()));
 
             }
          
           
             $entity->setType('FC');
             $max_X= $em->getRepository(Paiement::class)->MaxEtuX();

             if($max_X[0]==null){

              $entity->setDemandeur('X1');

             }else{
                $max_X = substr($max_X[0]['max_x'], 1);
                $max_X++;
                $entity->setDemandeur('X'.$max_X);
             }

             $entity->setFormation($form['formation']->getData()->getCodeApo());
             $formation = $form['formation']->getData()->getNomFiliere();
             $etape = $form['etape'] ;

             $em->persist( $entity );
             $em->flush();
             $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
             // create the number to words "manager" class
             $numberToWords = new NumberToWords();
  
             // build a new number transformer using the RFC 3066 language identifier
             $numberTransformer = $numberToWords->getNumberTransformer('fr');
 
             $montant_whole = floor($entity->getMontant());
             $montant_fraction =  round( ($entity->getMontant() - $montant_whole) *100  , 2);
 
             $html = $this->renderView('paiement/ordre_recette_nonexist.html.twig', [
                 'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
                 'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
                 'montant_f_num' =>  $montant_fraction ,
                 'montant_num' => number_format($entity->getMontant(), 2, ',', '.') ,
                 'num' => $entity->getNumRP(),
                 'paiement' => $entity,
                 'formation' => $formation,
                 'etape' => $etape
             ]);
  
             $filename = 'default.pdf'; 
 
             $dir = $this->getParameter('webroot_doc').'/Ordre_recette/'.$entity->GetNom().'_'.$entity->GetPrenom().'/';
             if (!file_exists($dir)) {
               mkdir($dir, 0777, true);
             }
 
              if (!file_exists($dir.$filename)) {
             $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
             $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
             $entity->setLien($filename);
 
             $em = $this->getDoctrine()->getManager();
             $em->getRepository(Paiement::class)->save($entity, true);
          //   $PaiementRepository->save($entity, true);
 
            /*  return new PdfResponse(
                 $knpSnappyPdf->getOutputFromHtml($html),
                 $filename ,
             );
    */
 
           }
 
 
              /*  return new PdfResponse(
                 $knpSnappyPdf->getOutputFromHtml($html),
                 $filename ,
             );  */
 
             return $this->redirect($this->generateUrl('showPaiement',array('id'=>$entity->getDemandeur() )));
                          
         }else{

      return $this->render('paiement/new_nonexist.html.twig', array( 'entity' => $entity,'tranche'=>$tranche, 'form' => $form->createView()));
         }
 
 
 
        if ($form->isSubmitted() && !$form->isValid()) {
         $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
         return $this->render('paiement/new_nonexist.html.twig', array( 'entity' => $entity,'tranche'=>$tranche, 'form' => $form->createView()));
        }
 
         
        return $this->render('paiement/new_nonexist.html.twig', array( 'entity' => $entity,'tranche'=>$tranche, 'form' => $form->createView()));
    }




    /**
   	 * @Route("/remove_Paiement/{id}", name="remove_Paiement")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token)
    {

        $em = $this->getDoctrine()->getManager();
        $paiement = $em->getRepository(Paiement::class)->find($id);
        if(!empty($paiement)){

            $param= new ConfigExtension($em);
         //   $nb_rp = $param->app_config('numRP');
         //   $nb_rp--;
         //   $em->getRepository(Config::class)->updateBy('numRP',$nb_rp);

         $dir = $this->getParameter('webroot_doc').'/Ordre_recette/'.$paiement->GetNom().'_'.$paiement->GetPrenom().'/';

         if (file_exists($dir.$paiement->getLien())) {
     
            unlink($dir.$paiement->getLien());

          }

	        $em->remove($paiement);
	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('paiement'));
	    
        }else{
	    	return new Response('1');
	    }
    }




  /**
   	 * @Route("/print_all", name="print_all")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN')")
     */
    public function print_all( Pdf $knpSnappyPdf )
    {
        $em = $this->getDoctrine()->getManager();
 
        $paiement_array = $em->getRepository(Paiement::class)->findBy(array() , array('annee'=>'ASC' ,'numRP' => 'ASC'));


        $em1 = $this->getDoctrine()->getManager();

       
        $montant_w_array = [];
        $montant_f_array  = [];
        $montant_f_num_array =  [];
        $montant_num_array =  [];
        $num_array = [];

        $formation_array = [];
        $cod_anu_array = [];
        $cod_dip_array = [];
        $cod_etp_array = [];

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        
        foreach ($paiement_array as $p) {


           

            $etudiant = $conn->fetchAssociative("SELECT * FROM individu i,adresse a WHERE i.COD_ETU='".$p->getDemandeur()."' and i.COD_IND = a.COD_IND ");
          //  $anneeUniversitaire= $em1->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);
            $ins_Adm_E=$conn->fetchAllAssociative("SELECT * FROM ins_adm_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP= e.COD_ETP and ie.cod_ind='".$etudiant['COD_IND']."' order by ie.COD_ANU desc");
            $formation = $conn->fetchAssociative("SELECT d.LIB_DIP , etp.COD_DIP FROM diplome d, ins_adm_etp etp , individu i WHERE i.COD_IND=etp.COD_IND AND etp.COD_DIP=d.COD_DIP AND etp.COD_DIP = '".$p->getFormation()."' AND  etp.ETA_IAE='E'  AND i.COD_IND='".$etudiant['COD_IND']."'");

        
            
            $numberToWords = new NumberToWords();
            // build a new number transformer using the RFC 3066 language identifier
            $numberTransformer = $numberToWords->getNumberTransformer('fr');
            $montant_whole = floor($p->getMontant());
            $montant_fraction =  round( ($p->getMontant() - $montant_whole) *100  , 2);

            $montant_w = $numberTransformer->toWords(intval($montant_whole));
            $montant_f = $numberTransformer->toWords(intval($montant_fraction));
            $montant_f_num =  $montant_fraction ;
            $montant_num = number_format($p->getMontant(), 2, ',', '.');
            $num = $p->getNumRP();

            array_push($montant_w_array,$montant_w);
            array_push($montant_f_array,$montant_f);
            array_push($montant_f_num_array,$montant_f_num);
            array_push($montant_num_array,$montant_num);
            array_push($num_array,$num);
            array_push($formation_array,$formation['LIB_DIP']); 
            array_push($cod_dip_array,$formation['COD_DIP']); 
            array_push($cod_anu_array,$ins_Adm_E[0]['COD_ANU']); 
            array_push($cod_etp_array,$ins_Adm_E[0]['COD_ETP']); 

        }
       
        $html = $this->renderView('paiement/ordre_recette_all.html.twig', [
            'montant_w' => $montant_w_array,
            'montant_f' => $montant_f_array,
            'montant_f_num' => $montant_f_num_array,
            'montant_num' => $montant_num_array,
            'num' => $num_array,
            'formation'=>$formation_array,
            'cod_anu'=>$cod_anu_array,
            'cod_dip'=>$cod_dip_array,
            'cod_etp'=>$cod_etp_array,
            'paiement' => $paiement_array,

        ]);

        $filename = 'ordre_recettes_tous.pdf'; 

        

       /*  $dir = $this->getParameter('webroot_doc').'/Ordre_recette/'.'/';
        if (!file_exists($dir)) {
          mkdir($dir, 0777, true);
        } */

      //  $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
        
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            $filename ,
        );


    }  


    /**
     * @Route("/export_paiment", name="export_paiment")
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FC_PAI') ")
    */
    public function export_paiment()
    {
		
		$annee_exerc = $years = range(date('Y'), 2000);

        return $this->render('paiement/importExport.html.twig',['annee_exerc' => $annee_exerc]);
    }


 /**
     * @Route("/export_plist_mois",name="export_plist_mois")
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FC_PAI') ")
     */
    public function export_plist_mois(Request $request , secure $security)
    {
        $searchParam = $request->get('importFile');

		$em = $this->getDoctrine()->getManager();

        $security->getUser()->getRoles();

    
if($searchParam["mois"]=='TOUS'){
    $list_paiements=$em->getRepository(Paiement::class)->get_FC_paiement_par_date($searchParam["annee"]."-01-01",$searchParam["annee"]."-12-31",$searchParam["annee"]);

}else{
    $list_paiements=$em->getRepository(Paiement::class)->get_FC_paiement_par_date($searchParam["annee"]."-".$searchParam["mois"]."-01",$searchParam["annee"]."-".$searchParam["mois"]."-31",$searchParam["annee"]);

}

      // dd($list_paiements);

        $objPHPExcel = new Spreadsheet();

        // Get the active sheet.
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getProperties()
            ->setCreator("Abdessamad")
            ->setLastModifiedBy("Abdessamad")
            ->setTitle("listes des Paiement")
            ->setSubject("listes des Paiement")
            ->setDescription("description du fichier")
            ->setKeywords("");
        $sheet = $objPHPExcel->getActiveSheet();
       
        $sheet->setCellValue('A1','RUBRIQUE');
        $sheet->setCellValue('B1','BENEFICIAIRE');
        $sheet->setCellValue('C1','DATE OPERATION');
        $sheet->setCellValue('D1','MONTANT');
        $sheet->setCellValue('E1','RP');
        $sheet->setCellValue('F1','IMPUTATION BUDGETAIRE');
        $sheet->setCellValue('G1','VERSION DIPLOME');
        $sheet->setCellValue('H1','PAIEMENT PAR UN TIERS');
        $sheet->setCellValue('I1','MODE PAIEMENT');

        $objPHPExcel->getActiveSheet()
            ->getStyle('A1'.':I1')
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
        $objPHPExcel->getActiveSheet()->getStyle('A1'.':I1')->applyFromArray($styleA1);

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

        $j=2;
            $entities = $list_paiements;
         //   dd($entities);
            foreach( $entities as $e  ){

                    $sheet->getColumnDimension('A')->setWidth(15);
                    $sheet->getColumnDimension('B')->setWidth(25);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(15);
                    $sheet->getColumnDimension('E')->setWidth(5);
                    $sheet->getColumnDimension('F')->setWidth(80);
                    $sheet->getColumnDimension('G')->setWidth(25);
                    $sheet->getColumnDimension('H')->setWidth(30);
                    $sheet->getColumnDimension('I')->setWidth(20);

                    $sheet->getStyle('A'.$j.':I'.$j)->applyFromArray($styleBordure1);
                    
                    $sheet->setCellValue('A'.$j,"71211");
                    $sheet->setCellValue('B'.$j,$e['etu_nom'] ." ".$e['etu_prenom']);
                    $sheet->setCellValue('C'.$j,$e['dateOperation']);
                    $sheet->setCellValue('D'.$j,$e['montant']);
                    $sheet->setCellValue('E'.$j,$e['numRP']);

                    $annee_univ = $e['anneeuniv'];
                    if( str_starts_with( $e['codeApo'], "IC") ) 
                    {
                        $annee_univ = $e['anneeuniv'] ." / " . ($e['anneeuniv']+1) ;
                    }

                    if( str_starts_with( $e['codeApo'], "ID") ) 
                    {
                        $annee_univ = $e['anneeuniv'] ." / " . ($e['anneeuniv']+2) ;
                    }


                    $imp_str = $e['codeApo']. " TR " .$e['tranche'] ." PROF ". $e['nom'] ." " .$e['prenom'] . " PROMOTION ". $annee_univ ;

                
                    $sheet->setCellValue('F'.$j,$imp_str);
                   
      
                    $sheet->setCellValue('G'.$j,$e['code_version'] );
                    $sheet->setCellValue('H'.$j,$e['tiers'] );
                    $sheet->setCellValue('I'.$j,$e['modePaiement'] );
                    $j++;

        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($objPHPExcel);
        
        // Create a Temporary file in the system
        $fileName = 'Liste_Paiements_FC_Mois_'.$searchParam["mois"]."_Annee_".$searchParam["annee"].'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }






}