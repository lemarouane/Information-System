<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\SousCategorie;
use App\Form\SousCategorieType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class SousCategorieController extends AbstractController
{


    /**
     * @Route("/souscategories", name="souscategories")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $souscategories = $em->getRepository(SousCategorie::class)->findAll();
        
        return $this->render('sous_categorie/liste.html.twig', array('souscategories' => $souscategories));

        
    }
    /**
     *  @Route("/showSousCategorie/{id}", name="showSousCategorie")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(SousCategorie::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('sous_categorie/show.html.twig', array('souscategorie' => $entity));
    }

    /**
     * @Route("/editSousCategorie/{id}", name="editSousCategorie")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(SousCategorie $entity)
    {
        $form = $this->createForm(SousCategorieType::class, $entity);
        return $this->render('sous_categorie/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateSousCategorie/{id}", name="updateSousCategorie")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, SousCategorie $souscategorie) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(SousCategorieType::class, $souscategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $em->persist($souscategorie);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('editSousCategorie', array('id' => $souscategorie->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('sous_categorie/edit.html.twig', array('entity' => $souscategorie, 'form' => $form->createView()));
    }


    /**
     * @Route("/addSousCategorie", name="addSousCategorie")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new SousCategorie();
        $form = $this->createForm(SousCategorieType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
           return $this->render('sous_categorie/show.html.twig', array('souscategorie' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('sous_categorie/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('sous_categorie/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_SousCategorie/{id}", name="remove_SousCategorie")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,SousCategorie $souscategorie)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($souscategorie)){

	        $souscategorie = $em->getRepository(SousCategorie::class)->find($id);
	        $em->remove($souscategorie);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('souscategories'));
	    }else{
	    	return new Response('1');
	    }
    }
}