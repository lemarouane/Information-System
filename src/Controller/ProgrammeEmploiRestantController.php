<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\ProgrammeEmploiRestant;
use App\Entity\ProgrammeElementRestant;
use App\Entity\Rubrique;
use App\Entity\Personnel;
use App\Entity\Paragraphe;
use App\Entity\ArticlePE;

use App\Form\ProgrammeEmploiRestantType;
use App\Form\ProgrammeElementRestantType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Security;
use Symfony\Component\Security\Core\Security as secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use App\Entity\FiliereFcResponsable;
use App\Entity\PaiementProjet;
use App\Entity\Financeprojetperiode;
use App\Entity\Etudiant\Etudiants;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProgrammeEmploiRestantController extends AbstractController
{



    /**
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
     * @Route("/ProgrammeEmploiRestant", name="ProgrammeEmploiRestant")
    */
    public function homeAction()
    {

    	$em = $this->getDoctrine()->getManager('default');
		$ProgrammeEmploiRestant=$em->getRepository(ProgrammeEmploiRestant::class)->findAll();
       // dd($ProgrammeEmploiRestant);
     //   $periodes=$em->getRepository(Financeprojetperiode::class)->findAll();

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") && !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")  && !$this->get('security.authorization_checker')->isGranted("ROLE_STOCK")){
			return $this->render('programme_emploi_restant/index.html.twig',['ProgrammeEmploi' => $ProgrammeEmploiRestant ]);
		}else{
			return $this->render('programme_emploi_restant/index.html.twig',['ProgrammeEmploi' => $ProgrammeEmploiRestant ]);
		}
        

    }


      /**
     * @Route("/ProgrammeEmploiRestant_prof", name="ProgrammeEmploiRestant_prof")
     * @Security("is_granted('ROLE_PROF')")
    */
   /*  public function homeProfAction(secure $security)
    {

    	$em = $this->getDoctrine()->getManager('default');

		if($this->get('security.authorization_checker')->isGranted("ROLE_PROF") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")){

            $user =  $security->getUser();
            $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

            $ProgrammeEmploiRestant=$em->getRepository(ProgrammeEmploiRestant::class)->findBy(array('personne'=>$pers));

			return $this->render('programme_emploi_restant/index-prof.html.twig',['ProgrammeEmploi' => $ProgrammeEmploiRestant ]);
		}else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirect($this->generateUrl('app_dashboard'));
		}
        

    } */

    /**
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
     * @Route("/showProgrammeEmploiRestant/{id}", name="showProgrammeEmploiRestant")
     */
    public function showAction($id , secure $security)
    {
        $em = $this->getDoctrine()->getManager('default');
        $user =  $security->getUser();
      //  $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        $entity = $em->getRepository(ProgrammeEmploiRestant::class)->find($id);

        if( ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){

            if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

            return $this->render('programme_emploi_restant/show.html.twig', array('programmeEmploi' => $entity));
        }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
    }
    /**
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
     * @Route("/editProgrammeEmploiRestant/{id}", name="editProgrammeEmploiRestant")
     */
    public function editAction(ProgrammeEmploiRestant $entity , secure $security)
    {

        $em = $this->getDoctrine()->getManager();
        $user =  $security->getUser();
      //  $pers = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $user)); 

        if(($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) ){


        /*     if($entity->getParagraphe()!=null)
            {
                $p_id = (string)  $entity->getParagraphe()->getId() ;
                $form = $this->createForm(ProgrammeEmploiRestantType::class, $entity,array('label' => $entity->getArticlePE()->getId() , 'help'=>  $p_id ));
            }else{
                $form = $this->createForm(ProgrammeEmploiRestantType::class, $entity,array('label' => $entity->getArticlePE()->getId()));
            }
 */
            $form = $this->createForm(ProgrammeEmploiRestantType::class, $entity , array('label' => $entity->getArticlePE()->getId())) ; 
            return $this->render('programme_emploi_restant/ProgrammeEmploi.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit'));

           /*  if( !$entity->isActiver()){
                $this->get('session')->getFlashBag()->add('danger', "l'élaborer de PE est désactivé par l'administrateur");
                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")){

                    return $this->redirect($this->generateUrl('ProgrammeEmploiRestant'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{
                return $this->render('programme_emploi_restant/ProgrammeEmploi.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit'));
            } */
        }else{

            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
            return new RedirectResponse($this->generateUrl('app_dashboard'));
        }
        
    }

    /**
     *  @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
     * @Route("/updateProgrammeEmploiRestant/{id}", name="updateProgrammeEmploiRestant")
     */
    public function updateAction(Request $request, secure $security , ProgrammeEmploiRestant $ProgrammeEmploiRestant , $id ) {

        
        $user =  $security->getUser();
     

        
        
        if( ($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") ) ){
          
        $form = $this->createForm(ProgrammeEmploiRestantType::class, $ProgrammeEmploiRestant,array('label' => $ProgrammeEmploiRestant->getArticlePE()->getId()));

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
    
        $ProgrammeEmploiRestant_BD =  $em->getRepository(ProgrammeEmploiRestant::class)->find_by_id($id);

        $montant_globale = $ProgrammeEmploiRestant_BD['montant'];

     
        $reste = $montant_globale ;
         //    $ProgrammeEmploiRestant_BD =  $em->getRepository(ProgrammeEmploiRestant::class)->find($ProgrammeEmploiRestant->getId());
         //dd($ProgrammeEmploiRestant_BD );

        if ($form->isSubmitted()) {

               $somme = 0 ;
           
                foreach ($ProgrammeEmploiRestant->getProgrammeElementRestants() as $element) {

                    $somme = $somme + $element->getMontant() ;
                    
                    $reste=$reste-$element->getMontant();
                } 

                if($somme > $ProgrammeEmploiRestant_BD['montant'] ){

                    $this->get('session')->getFlashBag()->add('danger', "MOD_MT_DEPASSE");
                    return $this->redirect($this->generateUrl('editProgrammeEmploiRestant',array('id'=>$id)));

                  } 
               
                $ProgrammeEmploiRestant->setMontantReste($reste);
                $ProgrammeEmploiRestant->setArticlePE(  $em->getRepository(ArticlePE::class)->find($ProgrammeEmploiRestant_BD['article_pe_id'])   ) ;
                $ProgrammeEmploiRestant->setAnnee($ProgrammeEmploiRestant_BD['annee']) ;
                $ProgrammeEmploiRestant->setIntitule($ProgrammeEmploiRestant_BD['intitule']) ;
                $ProgrammeEmploiRestant->setMontant($ProgrammeEmploiRestant_BD['montant']) ;
                $ProgrammeEmploiRestant->setReference($ProgrammeEmploiRestant_BD['reference']) ;
         
                $em->persist($ProgrammeEmploiRestant);

                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

                if($this->get('security.authorization_checker')->isGranted("ROLE_FINANCE") || $this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") || $this->get('security.authorization_checker')->isGranted("ROLE_DIR")  ){

                    return $this->redirect($this->generateUrl('ProgrammeEmploiRestant'));
                }
                              
            
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('programme_emploi_restant/ProgrammeEmploi.html.twig', array('entity' => $ProgrammeEmploiRestant, 'form' => $form->createView(), 'page' => 'edit'));

    }else{

        $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
        return new RedirectResponse($this->generateUrl('app_dashboard'));
    }

    }


    /**
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
     * @Route("/addProgrammeEmploiRestant", name="addProgrammeEmploiRestant")
     */

   public function addAction(Request $request , secure $security)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ProgrammeEmploiRestant();
        $user =  $security->getUser();


        if( !$this->get('security.authorization_checker')->isGranted("ROLE_ADMIN") && !$this->get('security.authorization_checker')->isGranted("ROLE_FINANCE")     ){
            $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
            return $this->redirect($this->generateUrl('app_dashboard'));
        }else{
           // $article = $em->getRepository(ArticlePE::class)->find(1); 
          //  $paragraphe = $em->getRepository(Paragraphe::class)->find(1); 

          //  $entity->setArticlePE($article);
            $entity->setAnnee(Date('Y'));

            $form = $this->createForm(ProgrammeEmploiRestantType::class, $entity);
            $form->handleRequest($request);

        }


        if ($form->isSubmitted() ) { // && $form->isValid()


          //  $montant = $em->getRepository(PaiementProjet::class)->getMontant_by_FC_annee($filiere_resp->getFiliereFc()->getCodeApo() , 2023 , $pers->getId()); 
            $entity->setReference('1');
       
           // $entity->setMontant($montant['montant']);

          //  $programeelement = new ProgrammeElementRestant();
         //   $rubrique = $em->getRepository(Rubrique::class)->find(1); 
          //  $programeelement->setRubrique($rubrique);
          //  $programeelement->setMontant($montant['montant']*0.4);

         //   $entity->addElement($programeelement);
/* 
            if($form->get('paragraphes_911')->getData()!='' && $form->get('paragraphes_911')->getData()!=null){
                $parag = $form->get('paragraphes_911')->getData();
                $entity->setParagraphe($parag);
            } */

            $em->persist( $entity );
            $em->flush();
   


           return $this->redirect($this->generateUrl('editProgrammeEmploiRestant', array('id' => $entity->getId())));
        }
 
        return $this->render('programme_emploi_restant/ProgrammeEmploiAdd.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new'));
    }

    /**
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
   	 * @Route("/remove_ProgrammeEmploiRestant/{id}", name="remove_ProgrammeEmploiRestant")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,ProgrammeEmploiRestant $ProgrammeEmploiRestant)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($ProgrammeEmploiRestant)){

	        $ProgrammeEmploiRestant = $em->getRepository(ProgrammeEmploiRestant::class)->find($id);
	        $em->remove($ProgrammeEmploiRestant);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('ProgrammeEmploiRestant'));
	    }else{
	    	return new Response('1');
	    }
    }

    /**
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
     * @Route("/pdfPERestant/{id}", name="pdfPERestant")
     */
    public function pdfPEAction(Pdf $knpSnappyPdf ,ProgrammeEmploiRestant $programme,$id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ProgrammeEmploiRestant::class)->find($id);
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

        $footer = $this->renderView('programme_emploi_restant/footer.html.twig', array(
            //Variables for the template
        ));
        $header = $this->renderView('programme_emploi_restant/header.html.twig', array(
            //Variables for the template
        ));
        $options = [
        'footer-html' => $footer,
        'header-html' => $header,
        ];
        $html = $this->renderView('programme_emploi_restant/pdfPE.html.twig', array(
                'programme' => $entity,
                'rubrique' => $rubrique,
                'base_dir' => $this->getParameter('webroot_doc') . '../../'
        ));


    return new PdfResponse(
        $knpSnappyPdf->getOutputFromHtml($html , $options ),
        'programme_emploi_restant'.$entity->getAnnee().'.pdf');
                
    }

  
    /**
     * @Security("is_granted('ROLE_FINANCE')")
     * @Route("/modPEIsActiveRestant/{checked}/{id}",name="modPEIsActiveRestant")
     */
    public function modPEIsActiveAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp = $em->getRepository(ProgrammeEmploiRestant::class)->find($id);

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
     * @Route("/peIsValideRestant/{checked}/{id}",name="peIsValideRestant")
     */
    public function peIsValideAction(Request $request,$checked,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $proEmp1 = $em->getRepository(ProgrammeEmploiRestant::class)->find($id);
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
     * @Route("/getperiodesRestant/{checked}/{id}",name="getperiodesRestant")
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

         $prog_resp = $em->getRepository(PaiementProjet::class)->findBy(array('annee'=>Date('Y'))); //
         $article = $em->getRepository(ArticlePE::class)->find(1); 
         $paragraphe = $em->getRepository(Paragraphe::class)->find(2); 
       
         $maxId = $em->getRepository(ProgrammeEmploiRestant::class)->getMaxId(); 

         if($maxId['max_reference']===NULL){
            $i = 0;
         }else{
            $i = $maxId['max_reference'] ;
         }
       
      
            foreach ($prog_resp as $resp) {
        
                $paiement_by_resp = $em->getRepository(PaiementProjet::class)->getPaiement_by_resp($resp->getResponsable()->getId());
                $programme = $em->getRepository(ProgrammeEmploiRestant::class)->findBy(array('personne'=> $resp->getResponsable() , 'annee'=>Date('Y') , 'periode'=>$proEmp1->getId()));

                if(!$programme){

                    if($paiement_by_resp){
                        foreach ($paiement_by_resp as $pai) {
                            $i++;

                            $entity = new ProgrammeEmploiRestant();
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
     * @Security("is_granted('ROLE_PROF')")
     * @Route("/elaborationPERestant",name="elaborationPERestant")
     */
    public function elaborationPEAction(Request $request,secure $security)
    {

        $em = $this->getDoctrine()->getManager();
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' => $usr->getId()));
        $proEmp = $em->getRepository(ProgrammeEmploiRestant::class)->findBy(array('personne' => $personne));
        if($proEmp){
            return $this->render('programme_emploi_restant/ProgrammeEmploiProf.html.twig', array('ProgrammeEmploi' => $proEmp));
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