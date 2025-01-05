<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\ProgrammeEmploiBudge;
use App\Entity\ProgrammeElementBudget;
use App\Entity\Rubrique;
use App\Entity\Personnel;
use App\Entity\Paragraphe;
use App\Entity\ProgrammeEmploiBudget;
use App\Entity\ProgrammeEmploiElementBudget;
use App\Form\ProgrammeEmploiBudgetType;
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
use App\Entity\Etudiant\Etudiants;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Budgetperiode;
use App\Entity\BudgetSortie;
use App\Entity\Departement;
use App\Entity\Utilisateurs;
use App\Entity\ExecutionPE;



class ProgrammeEmploiBudgetController extends AbstractController
{


    /**
     * @Route("/ProgrammeEmploiBudget", name="ProgrammeEmploiBudget")
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function homeAction()
    {

    	$em = $this->getDoctrine()->getManager('default');
		$ProgrammeEmploiBudget=$em->getRepository(ProgrammeEmploiBudget::class)->findAll();
        $periodes=$em->getRepository(Budgetperiode::class)->findAll();

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") && !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")  && !$this->get('security.authorization_checker')->isGranted("ROLE_STOCK")){
			return $this->render('programmeEmploiBudget/index.html.twig',['ProgrammeEmploiBudget' => $ProgrammeEmploiBudget , 'periodes'=>$periodes]);
		}else{
			return $this->render('programmeEmploiBudget/index.html.twig',['ProgrammeEmploiBudget' => $ProgrammeEmploiBudget, 'periodes'=>$periodes]);
		}
        

    }

   /**
     * @Route("/ProgrammeEmploiBudget_prof", name="ProgrammeEmploiBudget_prof")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_ADMIN')")
    */
    public function homeProfAction(secure $security)
    {

    	$em = $this->getDoctrine()->getManager('default');

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")){

            $user =  $security->getUser();
            $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

            $ProgrammeEmploiBudget=$em->getRepository(ProgrammeEmploiBudget::class)->findBy(array('personne'=>$pers));


            $liste_execs_pe = [];

            foreach ($ProgrammeEmploiBudget as $key => $value) {

                $liste_execs_pe[$value->getId()] =  $em->getRepository(ExecutionPE::class)->findBy(array('programmeBudget'=>$value->getId()));  
            }
//dd($liste_execs_pe);
			return $this->render('programmeEmploiBudget/index-prof.html.twig',['ProgrammeEmploiBudget' => $ProgrammeEmploiBudget , 'liste_execs_pe'=>$liste_execs_pe ]);
		}else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirect($this->generateUrl('app_dashboard'));
		}
        

    }
    /**
     *  @Route("/showProgrammeEmploiBudget/{id}", name="showProgrammeEmploiBudget")
     *  @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE')")
     */
    public function showAction($id , secure $security)
    {
        $em = $this->getDoctrine()->getManager('default');
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        $entity = $em->getRepository(ProgrammeEmploiBudget::class)->find($id);

        if($entity->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){

            if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

            return $this->render('programmeEmploiBudget/show.html.twig', array('ProgrammeEmploiBudget' => $entity));
        }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
    }
    /**
     * @Route("/editProgrammeEmploiBudget/{id}", name="editProgrammeEmploiBudget")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE')")
     */
    public function editAction(ProgrammeEmploiBudget $entity , secure $security)
    {

        $em = $this->getDoctrine()->getManager();
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        if($entity->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){


      
                $form = $this->createForm(ProgrammeEmploiBudgetType::class, $entity,array('label' => $entity->getArticlePE()->getId()));
            

        

            if( !$entity->isActiver()){
                $this->get('session')->getFlashBag()->add('danger', "l'élaborer de PE est désactivé par l'administrateur");
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){

                    return $this->redirect($this->generateUrl('ProgrammeEmploiBudget'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{
                return $this->render('programmeEmploiBudget/programmeEmploiBudget.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit'));
            }
        }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
        
    }

    /**
     * @Route("/updateProgrammeEmploiBudget/{id}", name="updateProgrammeEmploiBudget")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE')")
     */
    public function updateAction(Request $request, ProgrammeEmploiBudget $ProgrammeEmploiBudget , secure $security ) {
        $em = $this->getDoctrine()->getManager('default');
       
        $user =  $security->getUser();
        $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

       
        
        if($ProgrammeEmploiBudget->getPersonne() == $pers || ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") ) ){
            
       
        $form = $this->createForm(ProgrammeEmploiBudgetType::class, $ProgrammeEmploiBudget,array('label' => $ProgrammeEmploiBudget->getArticlePE()->getId()));

        

        $form->handleRequest($request);

       
        $montant_globale=$ProgrammeEmploiBudget->getMontant();

        if ($form->isSubmitted()) {

            $somme = 0 ;
            if(!$ProgrammeEmploiBudget->isActiver()){
                $this->get('session')->getFlashBag()->add('danger', "MOD_PE_DESAC");
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){
                    return $this->redirect($this->generateUrl('ProgrammeEmploiBudget'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{


                foreach ($ProgrammeEmploiBudget->getElement() as $element) {

                    $somme = $somme + $element->getMontant() ;
                   
                }

                if($montant_globale < $somme){
                    $this->get('session')->getFlashBag()->add('danger', "MOD_BUDGET_LIMIT");
                    return $this->redirect($this->generateUrl('editProgrammeEmploiBudget', array('id' => $ProgrammeEmploiBudget->getId())));

                }
               
                $ProgrammeEmploiBudget->setMontantReste($montant_globale-$somme);


                

                $em->persist($ProgrammeEmploiBudget);

                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR")  ){

                    return $this->redirect($this->generateUrl('ProgrammeEmploiBudget'));
                }else{
                    return $this->redirect($this->generateUrl('ProgrammeEmploiBudget_prof'));
                }
                
               
            }
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('programmeEmploiBudget/programmeEmploiBudget.html.twig', array('entity' => $ProgrammeEmploiBudget, 'form' => $form->createView(), 'page' => 'edit'));

    }else{

        $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }

    }


  

    /**
   	 * @Route("/remove_ProgrammeEmploiBudget/{id}", name="remove_ProgrammeEmploiBudget")
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,ProgrammeEmploiBudget $ProgrammeEmploiBudget)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($ProgrammeEmploiBudget)){

	        $ProgrammeEmploiBudget = $em->getRepository(ProgrammeEmploiBudget::class)->find($id);
	        $em->remove($ProgrammeEmploiBudget);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('ProgrammeEmploiBudget'));
	    }else{
	    	return new Response('1');
	    }
    }
 
    /**
     * @Route("/pdfPE_budget/{id}", name="pdfPE_budget")
     * @Security("is_granted('ROLE_FINANCE')")
     */
    public function pdfPEAction(Pdf $knpSnappyPdf ,ProgrammeEmploiBudget $programme,$id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ProgrammeEmploiBudget::class)->find($id);
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

        $footer = $this->renderView('programmeEmploiBudget/footer.html.twig', array(
            //Variables for the template
        ));
        $header = $this->renderView('programmeEmploiBudget/header.html.twig', array(
            //Variables for the template
        ));
        $options = [
        'footer-html' => $footer,
        'header-html' => $header,
        ];
        $html = $this->renderView('programmeEmploiBudget/pdfPE.html.twig', array(
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
     * @Route("/modPEIsActive_budget/{checked}/{id}",name="modPEIsActive_budget")
     */
    public function modPEIsActiveAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp = $em->getRepository(ProgrammeEmploiBudget::class)->find($id);

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
     * @Route("/peIsValide_budget/{checked}/{id}",name="peIsValide_budget")
     */
    public function peIsValideAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp1 = $em->getRepository(ProgrammeEmploiBudget::class)->find($id);
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
     * @Route("/getperiodes_budget/{checked}/{id}",name="getperiodes_budget")
     */
    public function getperiodes(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp1 = $em->getRepository(Budgetperiode::class)->find($id);

  

        if($checked == 'true' ){

            $em = $this->getDoctrine()->getManager();
            $maxId = $em->getRepository(ProgrammeEmploiBudget::class)->getMaxId(); 

            if($maxId['max_reference']===NULL){
                $i = 0;
            }else{
                $i = $maxId['max_reference'] ;
            }

            if($id == 1){

                $progs = $em->getRepository(BudgetSortie::class)->findBy(array('type_structure'=>$id)); //
                $article = $em->getRepository(ArticlePE::class)->findOneBy(array('numArticle'=>907)); 
                foreach ($progs as $resp) {
            
                    $dep_by_resp = $em->getRepository(Utilisateurs::class)->findByRoleDep($resp->getStructure(),'ROLE_CHEF_DEP');
                    $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$dep_by_resp['id']));
                    $programme = $em->getRepository(ProgrammeEmploiBudget::class)->findBy(array('personne'=> $personne , 'annee'=>Date('Y')));

                    if(!$programme){

                        if($dep_by_resp){

                                $i++;

                                $entity = new ProgrammeEmploiBudget();
                                $entity->setPersonne($personne) ;
                                $entity->setArticlePE($article);
                                $entity->setIntitule($dep_by_resp['departement']);
                                $entity->setDateCRE(new \DateTime());
                                $entity->setActiver(1);
                                $entity->setReference($i);
                                $entity->setMontant($resp->getMontant());
                                $entity->setAnnee(Date('Y'));
                        
                                $em->persist( $entity );


                        }
                    }
                }

                // $em->getRepository(Budgetperiode::class)->setFermerPeriodes($proEmp1->getId());
                $proEmp1->setActif('O');
            
            }else{
                $progs = $em->getRepository(BudgetSortie::class)->findBy(array('type_structure'=>$id)); //
                $article = $em->getRepository(ArticlePE::class)->findOneBy(array('numArticle'=>908)); 
                foreach ($progs as $resp) {
            
                    $lab_by_resp = $em->getRepository(Utilisateurs::class)->findByRoleStruct($resp->getStructure(),'ROLE_CHEF_STRUCT');
                    $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$lab_by_resp['id']));
                    $programme = $em->getRepository(ProgrammeEmploiBudget::class)->findBy(array('personne'=> $personne , 'annee'=>Date('Y')));

                    if(!$programme){

                        if($lab_by_resp){

                                $i++;

                                $entity = new ProgrammeEmploiBudget();
                                $entity->setPersonne($personne) ;
                                $entity->setArticlePE($article);
                                $entity->setIntitule($lab_by_resp['laboratoire']);
                                $entity->setDateCRE(new \DateTime());
                                $entity->setActiver(1);
                                $entity->setReference($i);
                                $entity->setMontant($resp->getMontant());
                                $entity->setAnnee(Date('Y'));
                        
                                $em->persist( $entity );


                        }
                    }
                }

                // $em->getRepository(Budgetperiode::class)->setFermerPeriodes($proEmp1->getId());
                $proEmp1->setActif('O');
            }

        }else{
            $proEmp1->setActif('F');
        }
        $em->persist($proEmp1);
        $em->flush();
        return new JsonResponse('1');

    }
    

    /**
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE')")
     * @Route("/elaborationPE_budget",name="elaborationPE_budget")
     */
    public function elaborationPEAction(Request $request,secure $security)
    {

        $em = $this->getDoctrine()->getManager();
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $usr->getId()));
        $proEmp = $em->getRepository(ProgrammeEmploiBudget::class)->findBy(array('personne' => $personne));
        if($proEmp){
            return $this->render('programmeEmploiBudget/ProgrammeEmploiBudgetProf.html.twig', array('ProgrammeEmploiBudget' => $proEmp));
        }else{
            $this->get('session')->getFlashBag()->add('danger', "Vous n'avez aucun programme d'emploi à élaborer ");
            return new RedirectResponse($this->generateUrl('show_personnel'));
        }

    }

   /*  #[Route('/articlePE_paragraphe', name: 'articlePE_paragraphe', methods: ['GET', 'POST'])]
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

        
    } */

}