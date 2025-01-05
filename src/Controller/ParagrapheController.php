<?php

namespace App\Controller;

use App\Entity\Ligne;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\Paragraphe;
use App\Form\paragrapheType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class ParagrapheController extends AbstractController
{

    /**
     * @Route("/paragraphes", name="paragraphes")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $paragraphes = $em->getRepository(Paragraphe::class)->findAll();
        
        return $this->render('paragraphe/liste.html.twig', array('paragraphes' => $paragraphes));

        
    }
    /**
     *  @Route("/showParagraphe/{id}", name="showParagraphe")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Paragraphe::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('paragraphe/show.html.twig', array('paragraphe' => $entity));
    }

    /**
     * @Route("/editParagraphe/{id}", name="editParagraphe")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Paragraphe $entity)
    {
        $form = $this->createForm(paragrapheType::class, $entity);
        return $this->render('paragraphe/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateParagraphe/{id}", name="updateParagraphe")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, Paragraphe $paragraphe) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(paragrapheType::class, $paragraphe);
        $form->handleRequest($request);

        if ($form->isValid()) {

             $em->persist($paragraphe);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('editParagraphe', array('id' => $paragraphe->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('paragraphe/edit.html.twig', array('entity' => $paragraphe, 'form' => $form->createView()));
    }


    /**
     * @Route("/addParagraphe", name="addParagraphe")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Paragraphe();
        $form = $this->createForm(paragrapheType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
           return $this->render('paragraphe/show.html.twig', array('paragraphe' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('paragraphe/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('paragraphe/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_Paragraphe/{id}", name="remove_Paragraphe")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Paragraphe $paragraphe)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($paragraphe)){

	        $paragraphe = $em->getRepository(Paragraphe::class)->find($id);
	        $em->remove($paragraphe);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('paragraphes'));
	    }else{
	    	return new Response('1');
	    }
    }

    /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * @Route("/get-lignes-from-paragraphe", name="paragraphe_list_lignes")
     * @param Request $request
     * @return JsonResponse
     */
    public function listLigneOfParagrapheAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $ligneRepository = $em->getRepository(Ligne::class);
        

        $lignes = $ligneRepository->createQueryBuilder("l")
            ->where("l.paragraphe = :paragrapheid")
            ->setParameter("paragrapheid", $request->query->get("paragrapheid"))
            ->getQuery()
            ->getResult();
        
        
        $responseArray = array();
        foreach($lignes as $ligne){
            $responseArray[] = array(
                "id" => $ligne->getId(),
                "libelle" => $ligne->getLibelle()
            );
        }
        

        return new JsonResponse($responseArray);

        
    }
}