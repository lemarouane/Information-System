<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class ArticleController extends AbstractController
{


    /**
     * @Route("/articles", name="articles")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $articles = $em->getRepository(Article::class)->findAll();
        
        return $this->render('article/liste.html.twig', array('articles' => $articles));

        
    }
    /**
     *  @Route("/showArticle/{id}", name="showArticle")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Article::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('article/show.html.twig', array('article' => $entity));
    }

    /**
     * @Route("/editArticle/{id}", name="editArticle")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Article $entity)
    {
        $form = $this->createForm(ArticleType::class, $entity);
        return $this->render('article/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateArticle/{id}", name="updateArticle")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, Article $article) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $em->persist($article);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('editArticle', array('id' => $article->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('article/edit.html.twig', array('entity' => $article, 'form' => $form->createView()));
    }


    /**
     * @Route("/addArticle", name="addArticle")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Article();
        $form = $this->createForm(ArticleType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCode('ART'.str_pad($em->getRepository(Article::class)->getCode()['nextval'], 6, '0', STR_PAD_LEFT));
        $em->getRepository(Article::class)->NextCode();
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
           return $this->render('article/show.html.twig', array('article' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('article/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('article/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_Article/{id}", name="remove_Article")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Article $article)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($article)){

	        $article = $em->getRepository(Article::class)->find($id);
	        $em->remove($article);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('articles'));
	    }else{
	    	return new Response('1');
	    }
    }
}