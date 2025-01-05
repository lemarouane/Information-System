<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\Ligne;
use App\Form\ligneType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class LigneController extends AbstractController
{


    /**
     * @Route("/lignes", name="lignes")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $lignes = $em->getRepository(Ligne::class)->findAll();
        
        return $this->render('ligne/liste.html.twig', array('lignes' => $lignes));

        
    }
    /**
     *  @Route("/showLigne/{id}", name="showLigne")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Ligne::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('ligne/show.html.twig', array('ligne' => $entity));
    }

    /**
     * @Route("/editLigne/{id}", name="editLigne")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Ligne $entity)
    {
        $form = $this->createForm(ligneType::class, $entity);
        return $this->render('ligne/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateLigne/{id}", name="updateLigne")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, Ligne $ligne) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(ligneType::class, $ligne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $em->persist($ligne);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('editLigne', array('id' => $ligne->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('ligne/edit.html.twig', array('entity' => $ligne, 'form' => $form->createView()));
    }


    /**
     * @Route("/addLigne", name="addLigne")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Ligne();
        $form = $this->createForm(ligneType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
           return $this->render('ligne/show.html.twig', array('ligne' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('ligne/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('ligne/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_Ligne/{id}", name="remove_Ligne")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Ligne $ligne)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($ligne)){

	        $ligne = $em->getRepository(Ligne::class)->find($id);
	        $em->remove($ligne);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('lignes'));
	    }else{
	    	return new Response('1');
	    }
    }
}