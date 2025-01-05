<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\ArticlePE;
use App\Entity\Paragraphe;
use App\Form\articlePEType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Service\InternetTest;
class ArticlePEController extends AbstractController
{

     /**
     * @Route("/articlePE", name="articlePE")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function articlePEAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $articles = $em->getRepository(ArticlePE::class)->findAll();
        
        return $this->render('articlePE/liste.html.twig', array('articles' => $articles));

        
    }
    /**
     *  @Route("/showArticlePE/{id}", name="showArticlePE")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ArticlePE::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('articlePE/show.html.twig', array('articlePE' => $entity));
    }

    /**
     * @Route("/editArticlePE/{id}", name="editArticlePE")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(ArticlePE $entity)
    {
        $form = $this->createForm(articlePEType::class, $entity);
        return $this->render('articlePE/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateArticlePE/{id}", name="updateArticlePE")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, ArticlePE $articlePE) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(articlePEType::class, $articlePE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $em->persist($articlePE);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('editArticlePE', array('id' => $articlePE->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('articlePE/edit.html.twig', array('entity' => $articlePE, 'form' => $form->createView()));
    }


    /**
     * @Route("/addArticlePE", name="addArticlePE")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ArticlePE();
        $form = $this->createForm(articlePEType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
           return $this->render('articlePE/show.html.twig', array('articlePE' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('articlePE/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('articlePE/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_ArticlePE/{id}", name="remove_ArticlePE")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,ArticlePE $articlePE)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($articlePE)){

	        $articlePE = $em->getRepository(ArticlePE::class)->find($id);
	        $em->remove($articlePE);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('articlePE'));
	    }else{
	    	return new Response('1');
	    }
    }

    /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * @Route("/get-paragraphe-from-article", name="article_list_paragraphes")
     * @param Request $request
     * @return JsonResponse
     */
    public function listParagrapheOfArticleAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $paragrapheRepository = $em->getRepository(Paragraphe::class);
        

        $paragraphes = $paragrapheRepository->createQueryBuilder("p")
            ->where("p.articlePE = :articleid")
            ->setParameter("articleid", $request->query->get("articleid"))
            ->getQuery()
            ->getResult();
        
        
        $responseArray = array();
        foreach($paragraphes as $paragraphe){
            $responseArray[] = array(
                "id" => $paragraphe->getId(),
                "libelle" => $paragraphe->getLibelle()
            );
        }
        

        return new JsonResponse($responseArray);

        
    }
}