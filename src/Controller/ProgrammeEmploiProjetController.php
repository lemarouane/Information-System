<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\ProgrammeEmploiProjet;
use App\Entity\ProgrammeElementProjet;
use App\Entity\Rubrique;
use App\Entity\Personnel;
use App\Entity\Paragraphe;
use App\Form\ProgrammeEmploiProjetType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Security;
use Symfony\Component\Security\Core\Security as secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use App\Entity\FiliereFcResponsable;
use App\Entity\ArticlePE;
use App\Entity\Paiementprojet;
use App\Entity\Financeprojetperiode;
use App\Entity\Etudiant\Etudiants;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class ProgrammeEmploiProjetController extends AbstractController
{



    /**
     * @Route("/ProgrammeEmploiProjet", name="ProgrammeEmploiProjet")
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function homeAction()
    {

    	$em = $this->getDoctrine()->getManager('default');
		$ProgrammeEmploiProjet=$em->getRepository(ProgrammeEmploiProjet::class)->findAll();
       // dd($ProgrammeEmploiProjet);
        $periodes=$em->getRepository(Financeprojetperiode::class)->findAll();

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") && !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")  && !$this->get('security.authorization_checker')->isGranted("ROLE_STOCK")){
			return $this->render('programme_emploi_projet/index.html.twig',['ProgrammeEmploi' => $ProgrammeEmploiProjet , 'periodes'=>$periodes]);
		}else{
			return $this->render('programme_emploi_projet/index.html.twig',['ProgrammeEmploi' => $ProgrammeEmploiProjet, 'periodes'=>$periodes]);
		}
        

    }


      /**
     * @Route("/ProgrammeEmploiProjet_prof", name="ProgrammeEmploiProjet_prof")
     * @Security("is_granted('ROLE_PROF')")
    */
    public function homeProfAction(secure $security)
    {

    	$em = $this->getDoctrine()->getManager('default');

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")){

            $user =  $security->getUser();
            $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

            $ProgrammeEmploiProjet=$em->getRepository(ProgrammeEmploiProjet::class)->findBy(array('personne'=>$pers));

			return $this->render('programme_emploi_projet/index-prof.html.twig',['ProgrammeEmploi' => $ProgrammeEmploiProjet ]);
		}else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirect($this->generateUrl('app_dashboard'));
		}
        

    }
    /**
     *  @Route("/showProgrammeEmploiProjet/{id}", name="showProgrammeEmploiProjet")
     *  @Security("is_granted('ROLE_PROF')")
     */
    public function showAction($id , secure $security)
    {
        $em = $this->getDoctrine()->getManager('default');
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        $entity = $em->getRepository(ProgrammeEmploiProjet::class)->find($id);

        if($entity->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){

            if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

            return $this->render('programme_emploi_projet/show.html.twig', array('programmeEmploi' => $entity));
        }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
    }
    /**
     * @Route("/editProgrammeEmploiProjet/{id}", name="editProgrammeEmploiProjet")
     * @Security("is_granted('ROLE_PROF')")
     */
    public function editAction(ProgrammeEmploiProjet $entity , secure $security)
    {

        $em = $this->getDoctrine()->getManager();
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        if($entity->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){


            if($entity->getParagraphe()!=null)
            {
                $p_id = (string)  $entity->getParagraphe()->getId() ;
                $form = $this->createForm(ProgrammeEmploiProjetType::class, $entity,array('label' => $entity->getArticlePE()->getId() , 'help'=>  $p_id ));
            }else{
                $form = $this->createForm(ProgrammeEmploiProjetType::class, $entity,array('label' => $entity->getArticlePE()->getId()));
            }

        

            if( !$entity->isActiver()){
                $this->get('session')->getFlashBag()->add('danger', "l'élaborer de PE est désactivé par l'administrateur");
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){

                    return $this->redirect($this->generateUrl('ProgrammeEmploiProjet'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{
                return $this->render('programme_emploi_projet/programmeEmploi.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit'));
            }
        }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
        
    }

    /**
     * @Route("/updateProgrammeEmploiProjet/{id}", name="updateProgrammeEmploiProjet")
     * @Security("is_granted('ROLE_PROF')")
     */
    public function updateAction(Request $request, ProgrammeEmploiProjet $ProgrammeEmploiProjet , secure $security , $id ) {
        $em = $this->getDoctrine()->getManager('default');
       
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

       
        
        if($ProgrammeEmploiProjet->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") ) ){
            
       
        $form = $this->createForm(ProgrammeEmploiProjetType::class, $ProgrammeEmploiProjet,array('label' => $ProgrammeEmploiProjet->getArticlePE()->getId()));

        

        $form->handleRequest($request);

        
        
        $montant_globale=$ProgrammeEmploiProjet->getMontant();

  /*       $var_20 = $montant_globale * 0.2;
        $var_40 = $montant_globale * 0.4;
        $var_10 = $montant_globale * 0.1 ; */

        $reste = $montant_globale ;
        if ($form->isSubmitted()) {

            $somme = 0 ;
            if(!$ProgrammeEmploiProjet->isActiver()){
                $this->get('session')->getFlashBag()->add('danger', "MOD_PE_DESAC");
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){
                    return $this->redirect($this->generateUrl('ProgrammeEmploiProjet'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{


                foreach ($ProgrammeEmploiProjet->getProgrammeElementProjets() as $element) {

                //    if($element->getRubrique()->getLigne()->getNumLigne() == 10 && $element->getRubrique()->getParagraphe()->getNumParagraphe()==10 && $element->getRubrique()->getArticlePE()->getNumArticle() == 911 && $element->getRubrique()->getLigne()->getType()=="Exploitation Personnel"){
                        $somme = $somme + $element->getMontant() ;
                  //  }
                    $reste=$reste-$element->getMontant();
                }

             /*    if($var_40 < $somme){
                    $this->get('session')->getFlashBag()->add('danger', "MOD_LIGNE_40");
                    return $this->redirect($this->generateUrl('editProgrammeEmploiProjet', array('id' => $ProgrammeEmploiProjet->getId())));

                }elseif( ($reste <  ($var_20 + $var_10) ) && !$this->get('security.authorization_checker')->isGranted("ROLE_DIR") && !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")){
                    $this->get('session')->getFlashBag()->add('danger', "MOD_ETAB_30");
                    return $this->redirect($this->generateUrl('editProgrammeEmploiProjet', array('id' => $ProgrammeEmploiProjet->getId())));

                }elseif( ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") ||  $this->get('security.authorization_checker')->isGranted("ROLE_DIR") ) && ($reste < $var_20  )){
                    $this->get('session')->getFlashBag()->add('danger', "MOD_ETAB_20");
                    return $this->redirect($this->generateUrl('editProgrammeEmploiProjet', array('id' => $ProgrammeEmploiProjet->getId())));
                } */
               
                  if($somme > $ProgrammeEmploiProjet->getMontant() ){

                    $this->get('session')->getFlashBag()->add('danger', "MOD_MT_DEPASSE");
                    return $this->redirect($this->generateUrl('editProgrammeEmploiProjet',array('id'=>$id)));

               /*  if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR")  ){
                    return $this->redirect($this->generateUrl('editProgrammeEmploiProjet'));
                }else{
                    return $this->redirect($this->generateUrl('ProgrammeEmploiProjet_prof'));
                } */
                  } 

                $ProgrammeEmploiProjet->setMontantReste($reste);


                $em->persist($ProgrammeEmploiProjet);

                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR")  ){
                    return $this->redirect($this->generateUrl('ProgrammeEmploiProjet'));
                }else{
                    return $this->redirect($this->generateUrl('ProgrammeEmploiProjet_prof'));
                }
                
               
            }
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('programme_emploi_projet/ProgrammeEmploi.html.twig', array('entity' => $ProgrammeEmploiProjet, 'form' => $form->createView(), 'page' => 'edit'));

    }else{

        $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }

    }


    /**
     * @Route("/addProgrammeEmploiProjet", name="addProgrammeEmploiProjet")
     * @Security("is_granted('ROLE_PROF')")
     */

   public function addAction(Request $request , secure $security)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ProgrammeEmploiProjet();
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

            $form = $this->createForm(ProgrammeEmploiProjetType::class, $entity);
            $form->handleRequest($request);

        }


        if ($form->isSubmitted() ) { // && $form->isValid()
            $entity->setDateCRE(new \DateTime());

            $entity->setValider('NON');
            $entity->setActiver('OUI');

            $montant = $em->getRepository(Paiementprojet::class)->getMontant_by_FC_annee($filiere_resp->getFiliereFc()->getCodeApo() , 2023 , $pers->getId()); 
            $entity->setReference('1');
            $entity->setMontant($montant['montant']);

            $programeelement = new ProgrammeElementProjet();
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
          // return $this->render('ProgrammeEmploiProjet/show.html.twig', array('ProgrammeEmploiProjet' => $entity));

           return $this->redirect($this->generateUrl('editProgrammeEmploiProjet', array('id' => $entity->getId())));
        }
      /*  if ($form->isSubmitted() && !$form->isValid()) {
      //  $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('ProgrammeEmploiProjet/ProgrammeEmploiProjet.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new'));
        }  */

        return $this->render('programme_emploi_projet/ProgrammeEmploiAdd.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new'));
    }

    /**
   	 * @Route("/remove_ProgrammeEmploiProjet/{id}", name="remove_ProgrammeEmploiProjet")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,ProgrammeEmploiProjet $ProgrammeEmploiProjet)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($ProgrammeEmploiProjet)){

	        $ProgrammeEmploiProjet = $em->getRepository(ProgrammeEmploiProjet::class)->find($id);
	        $em->remove($ProgrammeEmploiProjet);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('ProgrammeEmploiProjet'));
	    }else{
	    	return new Response('1');
	    }
    }

    /**
     * @Route("/pdfPEProjet/{id}", name="pdfPEProjet")
     * @Security("is_granted('ROLE_FINANCE')")
     */
    public function pdfPEAction(Pdf $knpSnappyPdf ,ProgrammeEmploiProjet $programme,$id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ProgrammeEmploiProjet::class)->find($id);
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

        $footer = $this->renderView('programme_emploi_projet/footer.html.twig', array(
            //Variables for the template
        ));
        $header = $this->renderView('programme_emploi_projet/header.html.twig', array(
            //Variables for the template
        ));
        $options = [
        'footer-html' => $footer,
        'header-html' => $header,
        ];
        $html = $this->renderView('programme_emploi_projet/pdfPE.html.twig', array(
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
     * @Route("/modPEIsActiveProjet/{checked}/{id}",name="modPEIsActiveProjet")
     */
    public function modPEIsActiveAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp = $em->getRepository(ProgrammeEmploiProjet::class)->find($id);

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
     * @Route("/peIsValideProjet/{checked}/{id}",name="peIsValideProjet")
     */
    public function peIsValideAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp1 = $em->getRepository(ProgrammeEmploiProjet::class)->find($id);
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
     * @Security("is_granted('ROLE_FINANCE')")
     * @Route("/getperiodesProjet/{checked}/{id}",name="getperiodesProjet")
     */
    public function getperiodes(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp1 = $em->getRepository(Financeprojetperiode::class)->find($id);

        $current_date = Date('Y').'-'.$proEmp1->getPeriode()  ;
        $current_date= date('Y-m-d',strtotime($current_date));

        if($checked == 'true' && date('Y-m-d') > $current_date){

         $em = $this->getDoctrine()->getManager();
         $config = new \Doctrine\DBAL\Configuration();
         $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
         $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

         $anneeUniversitaire= $em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);

         $prog_resp = $em->getRepository(Paiementprojet::class)->findBy(array('annee'=>Date('Y'))); //
         $article = $em->getRepository(ArticlePE::class)->find(1); 
         $paragraphe = $em->getRepository(Paragraphe::class)->find(2); 
       
         $maxId = $em->getRepository(ProgrammeEmploiProjet::class)->getMaxId(); 

         if($maxId['max_reference']===NULL){
            $i = 0;
         }else{
            $i = $maxId['max_reference'] ;
         }
       
      
            foreach ($prog_resp as $resp) {
        
                $paiement_by_resp = $em->getRepository(Paiementprojet::class)->getPaiement_by_resp($resp->getResponsable()->getId());
                $programme = $em->getRepository(ProgrammeEmploiProjet::class)->findBy(array('personne'=> $resp->getResponsable() , 'annee'=>Date('Y') , 'periode'=>$proEmp1->getId()));

                if(!$programme){

                    if($paiement_by_resp){
                        foreach ($paiement_by_resp as $pai) {
                            $i++;

                            $entity = new ProgrammeEmploiProjet();
                            $entity->setPersonne($resp->getResponsable()) ;
                            $entity->setArticlePE($article);
                            $entity->setParagraphe($paragraphe);
                            $entity->setIntitule($resp->getIntitule());
                            $entity->setDateCRE(new \DateTime());

                            $entity->setValider(0);
                            $entity->setActiver(1);

                            $entity->setReference($i);

                            $entity->setMontant(floatval($pai['montant']));
                            $entity->setAnnee(Date('Y'));
                            $entity->setType(1);
                         //   $entity->setPeriode($proEmp1->getId());

                        
                            $em->persist( $entity );

                        }
                    }
                }
            }

            $em->getRepository(Financeprojetperiode::class)->setFermerPeriodes($proEmp1->getId());
            $proEmp1->setActif('O');
           

        }else{
            $proEmp1->setActif('F');
        }
        $em->persist($proEmp1);
        $em->flush();
        return new JsonResponse('1');

    }
    

      /**
     * @Security("is_granted('ROLE_FINANCE')")
     * @Route("/addNewProgProj/{id}",name="addNewProgProj")
     */ 
    public function addProg(Request $request , $id)
    {

        $em = $this->getDoctrine()->getManager();


       //  $prog_resp = $em->getRepository(Paiementprojet::class)->findBy(array('annee'=>Date('Y'))); //
         $article = $em->getRepository(ArticlePE::class)->find(1); 
         $paragraphe = $em->getRepository(Paragraphe::class)->find(2); 
       
         $maxId = $em->getRepository(ProgrammeEmploiProjet::class)->getMaxId(); 

         if($maxId['max_reference']===NULL){
            $i = 0;
         }else{
            $i = $maxId['max_reference'] ;
         }
       
    
                            $i++;
                            $paiment_proj =  $em->getRepository(Paiementprojet::class)->find($id);
                            $entity = new ProgrammeEmploiProjet();

                            $entity->setPersonne($paiment_proj->getResponsable()) ;
                            $entity->setArticlePE($article);
                            $entity->setNumpaiementprojet($id);
                            $entity->setParagraphe($paragraphe);
                            $entity->setIntitule($paiment_proj->getIntitule());
                            $entity->setDateCRE(new \DateTime());

                            $entity->setValider(0);
                            $entity->setActiver(0);

                            $entity->setReference($i);

                            $entity->setMontant(floatval($paiment_proj->getMontant()));
                            $entity->setAnnee($paiment_proj->getAnnee());
                            $entity->setType(1);
                         //   $entity->setPeriode($proEmp1->getId());

                            $em->persist( $entity );       
            
   
        $em->flush();
        return $this->redirectToRoute('paiementprojet', [], Response::HTTP_SEE_OTHER);
       // return new JsonResponse('1');

    }

    /**
     * @Security("is_granted('ROLE_PROF')")
     * @Route("/elaborationPEProjet",name="elaborationPEProjet")
     */
    public function elaborationPEAction(Request $request,secure $security)
    {

        $em = $this->getDoctrine()->getManager();
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $usr->getId()));
        $proEmp = $em->getRepository(ProgrammeEmploiProjet::class)->findBy(array('personne' => $personne));
        if($proEmp){
            return $this->render('programme_emploi_projet/ProgrammeEmploiProf.html.twig', array('ProgrammeEmploi' => $proEmp));
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

}