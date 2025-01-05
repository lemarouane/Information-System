<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use App\Entity\SousCategorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    #[Route("/categorie/liste",name: "app_categories")]
    public function liste(CategorieRepository $categorieRepository){
        $categories = $categorieRepository->findAll();
        return $this->render('categorie/liste.html.twig',array(
            'categories'=>$categories
        ));
    }

        #[Route('/categorie/ajouter', name: 'app_ajouter_categorie')]
        public function ajouter(Request $request, EntityManagerInterface $em)
        {
            $categorie = new Categorie(); 
            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){ 
                $em ->persist($categorie);
                $em ->flush();
                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
                return $this->redirectToRoute('app_categories');
            }
            return $this->render('categorie/ajouter.html.twig',array(
                'form'=>$form->createView()
            ));
        }
    
        #[Route("/categorie/modifier/{id<\d+>}", name: "app_modifier_categorie")]
        public function modifier(Request $request, Categorie $categorie, EntityManagerInterface $em)
        {
            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em->flush();
                return $this->redirectToRoute('app_categories');
            }
            return $this->render('categorie/modifier.html.twig',array(
                'form'=>$form->createView()
            ));
        }
    
        #[Route("/categorie/supprimer/{id<\d+>}", name: "app_supprimer_categorie")]
        public function supprimer(Categorie $categorie, EntityManagerInterface $em)
        {
            $em ->remove($categorie);
            $em ->flush();
            return $this->redirectToRoute('app_categories'); 
        }

    /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * @Route("/get-souscategorie-from-categorie", name="categorie_list_souscategories")
     * @param Request $request
     * @return JsonResponse
     */
    public function listSousCategorieOfCategorieAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $souscategorieRepository = $em->getRepository(SousCategorie::class);
        

        $souscategories = $souscategorieRepository->createQueryBuilder("sc")
            ->where("sc.categorie = :categorieid")
            ->setParameter("categorieid", $request->query->get("categorieid"))
            ->getQuery()
            ->getResult();
        
        
        $responseArray = array();
        foreach($souscategories as $souscategorie){
            $responseArray[] = array(
                "id" => $souscategorie->getId(),
                "designation" => $souscategorie->getDesignation()
            );
        }

       

        return new JsonResponse($responseArray);
    }
            /**
   	 * @Route("/remove_Categorie/{id}", name="remove_Categorie")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Categorie $categorie)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($categorie)){

	        $categorie = $em->getRepository(Categorie::class)->find($id);
	        $em->remove($categorie);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirectToRoute('app_categories'); 
	    }else{
	    	return new Response('1');
	    }
    }
}