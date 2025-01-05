<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\ProgrammeEmploi;
use App\Entity\ProgrammeElement;
use App\Entity\Rubrique;
use App\Entity\Personnel;
use App\Entity\Paragraphe;
use App\Form\programmeEmploiType;
use App\Form\programmeEmploiAddType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Security;
use Symfony\Component\Security\Core\Security as secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use App\Entity\FiliereFcResponsable;
use App\Entity\ArticlePE;
use App\Entity\Paiement;
use App\Entity\Financeperiode;
use App\Entity\Etudiant\Etudiants;
use App\Entity\ExecutionPE;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProgrammeEmploiController extends AbstractController
{


    /**
     * @Route("/ProgrammeEmploi", name="ProgrammeEmploi")
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FC_PAI')")
    */
    public function homeAction()
    {

    	$em = $this->getDoctrine()->getManager('default');
		$ProgrammeEmploi=$em->getRepository(ProgrammeEmploi::class)->findAll();
        $periodes=$em->getRepository(Financeperiode::class)->findAll();

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") && !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")  && !$this->get('security.authorization_checker')->isGranted("ROLE_STOCK")){
			return $this->render('programmeEmploi/index.html.twig',['ProgrammeEmploi' => $ProgrammeEmploi , 'periodes'=>$periodes]);
		}else{
			return $this->render('programmeEmploi/index.html.twig',['ProgrammeEmploi' => $ProgrammeEmploi, 'periodes'=>$periodes]);
		}
        

    }


      /**
     * @Route("/ProgrammeEmploi_prof", name="ProgrammeEmploi_prof")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
    */
    public function homeProfAction(secure $security)
    {

    	$em = $this->getDoctrine()->getManager('default');

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")){

            $user =  $security->getUser();
            $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

            $ProgrammeEmploi=$em->getRepository(ProgrammeEmploi::class)->findBy(array('personne'=>$pers));

            $liste_execs_pe = [];

            foreach ($ProgrammeEmploi as $key => $value) {

                $liste_execs_pe[$value->getId()] =  $em->getRepository(ExecutionPE::class)->findBy(array('programme'=>$value->getId()));  

            }

			return $this->render('programmeEmploi/index-prof.html.twig',['ProgrammeEmploi' => $ProgrammeEmploi, 'liste_execs_pe'=>$liste_execs_pe ]);
		}else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirect($this->generateUrl('app_dashboard'));
		}
        

    }
    /**
     *  @Route("/showProgrammeEmploi/{id}", name="showProgrammeEmploi")
     *  @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
     */
    public function showAction($id , secure $security)
    {
        $em = $this->getDoctrine()->getManager('default');
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 
        $entity = $em->getRepository(ProgrammeEmploi::class)->find($id);

        if($entity->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){

            if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

            return $this->render('programmeEmploi/show.html.twig', array('programmeEmploi' => $entity));
        }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
    }

    /**
     * @Route("/editProgrammeEmploi/{id}", name="editProgrammeEmploi")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
     */
    public function editAction(ProgrammeEmploi $entity  , secure $security)
    {
        $em = $this->getDoctrine()->getManager();

        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        if($entity->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){

        if($entity->getParagraphe()!=null)
        {
            $p_id = (string)  $entity->getParagraphe()->getId() ;
            $form = $this->createForm(programmeEmploiType::class, $entity,array('label' => $entity->getArticlePE()->getId() , 'help'=>  $p_id ));
        }else{
            $form = $this->createForm(programmeEmploiType::class, $entity,array('label' => $entity->getArticlePE()->getId()));
        }

     

        if( !$entity->isActiver()){
            $this->get('session')->getFlashBag()->add('danger', "MOD_PE_DESAC");
            if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){

                return $this->redirect($this->generateUrl('ProgrammeEmploi'));
            }else{
                return new RedirectResponse($this->generateUrl('app_dashboard'));
            }
        }else{
            return $this->render('programmeEmploi/programmeEmploi.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit'));
        }
    }else{

        $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }
    
        
    }

    /**
     * @Route("/updateProgrammeEmploi/{id}", name="updateProgrammeEmploi")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
     */
    public function updateAction(Request $request, ProgrammeEmploi $programmeEmploi , secure $security ) {
        $em = $this->getDoctrine()->getManager('default');

        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        if($programmeEmploi->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") ) ){

        
        $form = $this->createForm(programmeEmploiType::class, $programmeEmploi,array('label' => $programmeEmploi->getArticlePE()->getId()));

        $form->handleRequest($request);


        $lien1 = $form->get('lien1')->getData() ;
        $lien2 = $form->get('lien2')->getData() ;


        if( (!$lien1 || !$lien2 ) && ( !$programmeEmploi->getLien1() || !$programmeEmploi->getLien2() )){
            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_FILES");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }

        if($lien1){
            $originalFilename_1 = pathinfo($lien1->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename_1 = sha1(uniqid(mt_rand(), true)).'.'.$lien1->guessExtension();
            try {
              $lien1->move($this->getParameter('webroot_doc'). $programmeEmploi->getPersonne()->GetNom().'_'.$programmeEmploi->getPersonne()->GetPrenom().'/Programme_Emploi/'.$programmeEmploi->getReference().'/', $newFilename_1);
            } catch (FileException $e) {}
            $programmeEmploi->setLien1($newFilename_1);
        }
            
        if($lien2){
            $originalFilename_2 = pathinfo($lien2->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename_2 = sha1(uniqid(mt_rand(), true)).'.'.$lien2->guessExtension();
            try {
            $lien2->move($this->getParameter('webroot_doc'). $programmeEmploi->getPersonne()->GetNom().'_'.$programmeEmploi->getPersonne()->GetPrenom().'/Programme_Emploi/'.$programmeEmploi->getReference().'/', $newFilename_2);
            } catch (FileException $e) {}
            $programmeEmploi->setLien2($newFilename_2);
            }

     

       /*  if(!$lien1 || !$lien2){
            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_FILES");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }

            $originalFilename_1 = pathinfo($lien1->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename_1 = sha1(uniqid(mt_rand(), true)).'.'.$lien1->guessExtension();

            $originalFilename_2 = pathinfo($lien2->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename_2 = sha1(uniqid(mt_rand(), true)).'.'.$lien2->guessExtension();
    
            try {

                $lien1->move($this->getParameter('webroot_doc'). $programmeEmploi->getPersonne()->GetNom().'_'.$programmeEmploi->getPersonne()->GetPrenom().'/Programme_Emploi/'.$programmeEmploi->getReference().'/', $newFilename_1);
                $lien2->move($this->getParameter('webroot_doc'). $programmeEmploi->getPersonne()->GetNom().'_'.$programmeEmploi->getPersonne()->GetPrenom().'/Programme_Emploi/'.$programmeEmploi->getReference().'/', $newFilename_2);

            } catch (FileException $e) {
              
            }

        $programmeEmploi->setLien1($newFilename_1);
        $programmeEmploi->setLien2($newFilename_2); */


        $montant_globale=$programmeEmploi->getMontant();
        $var_20 = $montant_globale * 0.2;
        $var_40 = $montant_globale * 0.4;
        $var_10 = $montant_globale * 0.1 ;
        $reste = $montant_globale ;
        if ($form->isSubmitted()) {

            $somme = 0 ;
            if(!$programmeEmploi->isActiver()){
                $this->get('session')->getFlashBag()->add('danger', "MOD_PE_DESAC");
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){
                    return $this->redirect($this->generateUrl('ProgrammeEmploi'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{


                foreach ($programmeEmploi->getElement() as $element) {

                    if($element->getRubrique()->getLigne()->getNumLigne() == 10 && $element->getRubrique()->getParagraphe()->getNumParagraphe()==10 && $element->getRubrique()->getArticlePE()->getNumArticle() == 911 && $element->getRubrique()->getLigne()->getType()=="Exploitation Personnel"){
                        $somme = $somme + $element->getMontant() ;
                    }
                    $reste=$reste-$element->getMontant();
                }

                if($var_40 < $somme){
                    $this->get('session')->getFlashBag()->add('danger', "MOD_LIGNE_40");
                    return $this->redirect($this->generateUrl('editProgrammeEmploi', array('id' => $programmeEmploi->getId())));

                }elseif( ($reste <  ($var_20 + $var_10) ) && !$this->get('security.authorization_checker')->isGranted("ROLE_DIR") && !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") && !$this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){
                    $this->get('session')->getFlashBag()->add('danger', "MOD_ETAB_30");
                    return $this->redirect($this->generateUrl('editProgrammeEmploi', array('id' => $programmeEmploi->getId())));

                }elseif( ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") ||  $this->get('security.authorization_checker')->isGranted("ROLE_DIR") ) && ($reste < $var_20  )){
                    $this->get('session')->getFlashBag()->add('danger', "MOD_ETAB_20");
                    return $this->redirect($this->generateUrl('editProgrammeEmploi', array('id' => $programmeEmploi->getId())));
                }
               
                $programmeEmploi->setMontantReste($reste);


                

                $em->persist($programmeEmploi);

                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
                
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR")  ){

                    return $this->redirect($this->generateUrl('ProgrammeEmploi'));
                }else{
                    return $this->redirect($this->generateUrl('ProgrammeEmploi_prof'));
                }
            }
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('programmeEmploi/programmeEmploi.html.twig', array('entity' => $programmeEmploi, 'form' => $form->createView(), 'page' => 'edit'));
    }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
    }


    /**
     * @Route("/addProgrammeEmploi", name="addProgrammeEmploi")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
     */

   public function addAction(Request $request , secure $security)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ProgrammeEmploi();
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        $filiere_resp = $em->getRepository(FiliereFcResponsable::class)->findOneBy(array('responsable'=>$pers->getId(),'annee'=>2023)); //

        if(!$filiere_resp && !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")   ){
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirect($this->generateUrl('app_dashboard'));
        }else{
            $article = $em->getRepository(ArticlePE::class)->find(1); 
            $paragraphe = $em->getRepository(Paragraphe::class)->find(1); 
            $entity->setPersonne($pers) ;
            $entity->setArticlePE($article);
            $entity->setParagraphe($paragraphe);
            $entity->setIntitule($filiere_resp->getFiliereFc()->getNomFiliere());

            $form = $this->createForm(programmeEmploiType::class, $entity);
            $form->handleRequest($request);

        }


        if ($form->isSubmitted() ) { // && $form->isValid()
            $entity->setDateCRE(new \DateTime());

            $entity->setValider('NON');
            $entity->setActiver('OUI');

            $montant = $em->getRepository(Paiement::class)->getMontant_by_FC_annee($filiere_resp->getFiliereFc()->getCodeApo() , 2023 , $pers->getId()); 
            $entity->setReference('1');
            $entity->setMontant($montant['montant']);

            $programeelement = new ProgrammeElement();
            $rubrique = $em->getRepository(Rubrique::class)->find(1); 
            $programeelement->setRubrique($rubrique);
            $programeelement->setMontant($montant['montant']*0.4);

            $entity->addElement($programeelement);
/* 
            if($form->get('paragraphes_911')->getData()!='' && $form->get('paragraphes_911')->getData()!=null){
                $parag = $form->get('paragraphes_911')->getData();
                $entity->setParagraphe($parag);
            } */

            $em->persist( $entity );
            $em->flush();
   
           // $this->get('session')->getFlashBag()->add('success', "Le PE a été ajouté avec succès.".$entity->getId());
          // return $this->render('programmeEmploi/show.html.twig', array('programmeEmploi' => $entity));

           return $this->redirect($this->generateUrl('editProgrammeEmploi', array('id' => $entity->getId())));
        }
      /*  if ($form->isSubmitted() && !$form->isValid()) {
      //  $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('programmeEmploi/programmeEmploi.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new'));
        }  */

        return $this->render('programmeEmploi/programmeEmploiAdd.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new'));
    }

    /**
   	 * @Route("/remove_ProgrammeEmploi/{id}", name="remove_ProgrammeEmploi")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,ProgrammeEmploi $programmeEmploi)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($programmeEmploi)){

	        $programmeEmploi = $em->getRepository(ProgrammeEmploi::class)->find($id);
	        $em->remove($programmeEmploi);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('ProgrammeEmploi'));
	    }else{
	    	return new Response('1');
	    }
    }

    /**
     * @Route("/pdfPE/{id}", name="pdfPE")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR') or is_granted('ROLE_FC_PAI') ")
     */
    public function pdfPEAction(Pdf $knpSnappyPdf ,ProgrammeEmploi $programme,$id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ProgrammeEmploi::class)->find($id);
        $numArticle =    $entity->getArticlePE()->getNumArticle() ;
        $rubrique = null;

        if($numArticle == '911'){
            $p_id = $entity->getParagraphe()->getId();
            $paragraphe = $em->getRepository(Paragraphe::class)->find($p_id);
            $rubrique = $em->getRepository(Rubrique::class)->getRubriquesOrdered($entity->getArticlePE()->getId(),$paragraphe->getId());
        }else{
            $rubrique = $em->getRepository(Rubrique::class)->getRubriquesOrderedNoParagraphe($entity->getArticlePE()->getId());
          // dd($rubrique);
        }
      


        //return new JsonResponse($rubrique);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        $footer = $this->renderView('programmeEmploi/footer.html.twig', array(
            //Variables for the template
        ));
        $header = $this->renderView('programmeEmploi/header.html.twig', array(
            //Variables for the template
        ));
        $options = [
        'footer-html' => $footer,
        'header-html' => $header,
        ];
        $html = $this->renderView('programmeEmploi/pdfPE.html.twig', array(
                'programme' => $entity,
                'rubrique' => $rubrique,
                'base_dir' => $this->getParameter('webroot_doc') . '../../'
        ));


    return new PdfResponse(
        $knpSnappyPdf->getOutputFromHtml($html , $options ),
        'programme_emploi_'.$entity->getPersonne()->getNom().'_'.$entity->getPersonne()->getNom().'_'.$entity->getAnnee().'.pdf');
                
    }

  
    /**
     * @Security("is_granted('ROLE_FINANCE')")
     * @Route("/modPEIsActive/{checked}/{id}",name="modPEIsActive")
     */
    public function modPEIsActiveAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp = $em->getRepository(ProgrammeEmploi::class)->find($id);

       // dd($id);
        
        if($checked == 'true'){
            $proEmp->setActiver(true);
        }
        if($checked == 'false'){
            $proEmp->setActiver(false);  
        }
        $em->persist($proEmp);
        $em->flush();
        return new JsonResponse('1');
      //  return $this->redirect($this->generateUrl('paramStock', array('param' => 'PE')));
    }

    /**
     * @Security("is_granted('ROLE_FINANCE')")
     * @Route("/peIsValide/{checked}/{id}",name="peIsValide")
     */
    public function peIsValideAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp1 = $em->getRepository(ProgrammeEmploi::class)->find($id);
        if($checked == 'true'){
            $proEmp1->setValider(true);
        }
        if($checked == 'false'){
            $proEmp1->setValider(false);
        }
        $em->persist($proEmp1);
        $em->flush();
        return new JsonResponse('1');
       // return $this->redirect($this->generateUrl('paramStock', array('param' => 'PE')));
    }
    

     /**
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
     * @Route("/getperiodes/{checked}/{id}",name="getperiodes")
     */
    public function getperiodes(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp1 = $em->getRepository(Financeperiode::class)->find($id);

        $current_date = Date('Y').'-'.$proEmp1->getPeriode()  ;
        $current_date= date('Y-m-d',strtotime($current_date));

        if($checked == 'true' && date('Y-m-d') > $current_date){

         $em = $this->getDoctrine()->getManager();
         $config = new \Doctrine\DBAL\Configuration();
         $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
         $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

         $anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);

         $filiere_resp = $em->getRepository(FiliereFcResponsable::class)->findBy(array('annee'=>$anneeUniversitaire['COD_ANU'])); //
         $article = $em->getRepository(ArticlePE::class)->find(1); 
         $paragraphe = $em->getRepository(Paragraphe::class)->find(1); 
       
         $maxId = $em->getRepository(ProgrammeEmploi::class)->getMaxId(); 

         if($maxId['max_reference']===NULL){
            $i = 0;
         }else{
            $i = $maxId['max_reference'] ;
         }
       
            foreach ($filiere_resp as $resp) {
        
                $paiement_by_resp = $em->getRepository(Paiement::class)->getPaiement_by_resp_FC($resp->getFiliereFc()->getCodeApo() , $resp->getResponsable()->getId());
                $programme = $em->getRepository(ProgrammeEmploi::class)->findBy(array('personne'=> $resp->getResponsable() , 'annee'=>Date('Y') , 'periode'=>$proEmp1->getId()));

                if(!$programme){

                    if($paiement_by_resp){
                        foreach ($paiement_by_resp as $pai) {
                            $i++;

                            $entity = new ProgrammeEmploi();
                            $entity->setPersonne($resp->getResponsable()) ;
                            $entity->setArticlePE($article);
                            $entity->setParagraphe($paragraphe);
                            $entity->setIntitule($resp->getFiliereFc()->getNomFiliere());
                            $entity->setDateCRE(new \DateTime());

                            $entity->setValider(0);
                            $entity->setActiver(1);

                            $entity->setReference($i);

                            $entity->setMontant(floatval($pai['montant']));
                            $entity->setAnnee(Date('Y'));
                            $entity->setAnneeuniv($pai['annee']);
                            $entity->setType(1);
                            $entity->setPeriode($proEmp1->getId());
                            $entity->setCodeapofc($resp->getFiliereFc()->getCodeApo());
                        
                            $em->persist( $entity );

                        }
                    }
                }
            }

            $em->getRepository(Financeperiode::class)->setFermerPeriodes($proEmp1->getId());
            $proEmp1->setActif('O');
           

        }else{


            
                $programme = $em->getRepository(ProgrammeEmploi::class)->findBy(array( 'annee'=>Date('Y') , 'periode'=>$proEmp1->getId()));

                if($programme){
                    foreach ($programme as $prog) {

                        $prog->setActiver(0);
                        $em->persist( $prog );
                    }
        
                }

            $proEmp1->setActif('F');
        }
        $em->persist($proEmp1);
        $em->flush();
        return new JsonResponse('1');

    }
    

    /**
     * @Security("has_role('ROLE_PROF')")
     * @Route("/elaborationPE",name="elaborationPE")
     */
    public function elaborationPEAction(Request $request,secure $security)
    {

        $em = $this->getDoctrine()->getManager();
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('numPpr' => $usr->getNumPpr()));
        $proEmp = $em->getRepository(ProgrammeEmploi::class)->findBy(array('personne' => $personne));
        if($proEmp){
            return $this->render('programmeEmploi/programmeEmploiProf.html.twig', array('ProgrammeEmploi' => $proEmp));
        }else{
            $this->get('session')->getFlashBag()->add('danger', "Vous n'avez aucun programme d'emploi à élaborer ");
            return new RedirectResponse($this->generateUrl('show_personnel'));
        }

    }

    #[Route('/articlePE_paragraphe', name: 'articlePE_paragraphe', methods: ['GET', 'POST'])]
    public function listarticlePE_paragrapheAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $paragrapheRepository = $em->getRepository(Paragraphe::class);
        

        $paragraphes = $paragrapheRepository->createQueryBuilder("p")
            ->where("p.articlePE = :articlePE")
            ->setParameter("articlePE", $request->query->get("article"))
            ->getQuery()
            ->getResult();
        
        
        $responseArray = array();
        foreach($paragraphes as $parag){
            $responseArray[] = array(
                "id" => $parag->getId(),
                "designationFr" =>  $parag->getArticlePE()->getNumArticle()."-". $parag->getNumParagraphe()."-". $parag->getLibelle() 
            );
        }


        return new JsonResponse($responseArray);

        
    }



    /**
     * @Route("/pdfPEALL", name="pdfPEALL")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_DIR')")
     */
    public function pdfPEALLAction(Pdf $knpSnappyPdf )
    {
        $em = $this->getDoctrine()->getManager('default');

        $allProgramme = $em->getRepository(ProgrammeEmploi::class)->getAllRubriqueByProgramme(); 
        
        $article = $em->getRepository(ArticlePE::class)->find(1); 
        $paragraphe = $em->getRepository(Paragraphe::class)->find(1); 
        $responsable = $em->getRepository(Personnel::class)->findOneBy(array('numPPR' => '1974027')); 
        $date1='2023-10-31';
        $date2='2024-03-31';
        $montant_g = $em->getRepository(Paiement::class)->getMontantGlobaleFC($date1,$date2) ;
        $entity = new ProgrammeEmploi();
        $entity->setPersonne($responsable) ;
        $entity->setArticlePE($article);
        $entity->setParagraphe($paragraphe);
        $entity->setIntitule('Formation Continue Ventilation');
        $entity->setDateCRE(new \DateTime());
        $entity->setValider(0);
        $entity->setActiver(1);
        $entity->setReference(40);
        $entity->setMontant(floatval($montant_g[0]['montant_g']));
        $entity->setAnnee(Date('Y'));
        $entity->setAnneeuniv('2024');
        $entity->setType(1);
        $entity->setPeriode(1);
        $entity->setCodeapofc('ICGSI');
        
        foreach ($allProgramme as $rubrique) {
            $rub = $em->getRepository(Rubrique::class)->find($rubrique['rubrique_id']);
            $element = new ProgrammeElement();
            $element->setRubrique($rub);
            $element->setMontant($rubrique['montant']);
            $element->setProgramme($entity);
            $entity->addElement($element);
        }

        $numArticle =    $entity->getArticlePE()->getNumArticle() ;
        $rubrique = null;

        if($numArticle == '911'){
            $p_id = $entity->getParagraphe()->getId();
            $paragraphe = $em->getRepository(Paragraphe::class)->find($p_id);
            $rubrique = $em->getRepository(Rubrique::class)->getRubriquesOrdered($entity->getArticlePE()->getId(),$paragraphe->getId());
        }else{
            $rubrique = $em->getRepository(Rubrique::class)->getRubriquesOrderedNoParagraphe($entity->getArticlePE()->getId());
          // dd($rubrique);
        }
      
        //return new JsonResponse($rubrique);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        $footer = $this->renderView('programmeEmploi/footer.html.twig', array(
            //Variables for the template
        ));
        $header = $this->renderView('programmeEmploi/header.html.twig', array(
            //Variables for the template
        ));
        $options = [
        'footer-html' => $footer,
        'header-html' => $header,
        ];
        $html = $this->renderView('programmeEmploi/pdfPE_g.html.twig', array(
                'programme' => $entity,
                'rubrique' => $rubrique,
                'base_dir' => $this->getParameter('webroot_doc') . '../../'
        ));


    return new PdfResponse(
        $knpSnappyPdf->getOutputFromHtml($html , $options ),
        'programme_emploi_'.$entity->getPersonne()->getNom().'_'.$entity->getPersonne()->getNom().'_'.$entity->getAnnee().'.pdf');
                
    }

}