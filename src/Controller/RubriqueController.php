<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\Rubrique;
use App\Form\rubriqueType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class RubriqueController extends AbstractController
{




    #[Route('/rubriques_of_article_{id}', name: 'rubriques_of_article', methods: ['GET','POST'])]
    public function rubriques_of_article(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        $rubriques = $em->getRepository(Rubrique::class)->findBy(array("articlePE"=>$id));
      
        return new JsonResponse($rubriques);
   
    }





    /**
     * @Route("/rubriques", name="rubriques")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function rubriquesAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $rubriques = $em->getRepository(Rubrique::class)->findAll();
        
        return $this->render('rubrique/liste.html.twig', array('rubriques' => $rubriques));

        
    }
    /**
     *  @Route("/showRubrique/{id}", name="showRubrique")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Rubrique::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('rubrique/show.html.twig', array('rubrique' => $entity));
    }

    /**
     * @Route("/editRubrique/{id}", name="editRubrique")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Rubrique $entity)
    {
        $form = $this->createForm(rubriqueType::class, $entity);
        return $this->render('rubrique/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateRubrique/{id}", name="updateRubrique")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, Rubrique $rubrique) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(rubriqueType::class, $rubrique);
        $form->handleRequest($request);

        if ($form->isValid()) {

             $em->persist($rubrique);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('editRubrique', array('id' => $rubrique->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('rubrique/edit.html.twig', array('entity' => $rubrique, 'form' => $form->createView()));
    }


    /**
     * @Route("/addRubrique", name="addRubrique")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Rubrique();
        $form = $this->createForm(rubriqueType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
           return $this->render('rubrique/show.html.twig', array('rubrique' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('rubrique/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('rubrique/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_Rubrique/{id}", name="remove_Rubrique")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Rubrique $rubrique)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($rubrique)){

	        $rubrique = $em->getRepository(Rubrique::class)->find($id);
	        $em->remove($rubrique);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('rubriques'));
	    }else{
	    	return new Response('1');
	    }
    }
}