<?php

namespace App\Controller;

use App\Repository\FournisseurRepository;
use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FournisseurController extends AbstractController
{
    #[Route('/fournisseur', name: 'app_fournisseur')]
    public function index(): Response
    {
        return $this->render('fournisseur/index.html.twig', [
            'controller_name' => 'FournisseurController',
        ]);
    }

    /*Fonction de recupération de tous les fournisseurs*/
    #[Route("/fournisseur/liste",name: "app_fournisseurs")]
    public function fournisseurs(FournisseurRepository $fournisseurRepository){
        $fournisseurs = $fournisseurRepository->findAll();
        return $this->render('fournisseur/liste.html.twig',array(
            'fournisseurs'=>$fournisseurs
        ));
    }

        /*Fonction d'ajout d'un fournisseur*/
        #[Route('/fournisseur/ajouter', name: 'app_ajouter_fournisseur')]
        public function ajouterFournisseur(Request $request, EntityManagerInterface $em)
        {
            $fournisseur = new Fournisseur(); 
            
        
            $form = $this->createForm(FournisseurType::class, $fournisseur);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){ 
                $fournisseur->setCode('FRS'.str_pad($em->getRepository(Fournisseur::class)->getCode()['nextval'], 6, '0', STR_PAD_LEFT));
                $em->getRepository(Fournisseur::class)->NextCode();
                $em ->persist($fournisseur);
                $em ->flush();
                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
                return $this->redirectToRoute('app_fournisseurs');
            }
            return $this->render('fournisseur/ajouterFournisseur.html.twig',array(
                'form'=>$form->createView()
            ));
        }
    
        /*Fonction de modification d'un fournisseur*/
        #[Route("/fournisseur/modifier/{id<\d+>}", name: "app_modifier_fournisseur")]
        public function modifierFournisseur(Request $request, Fournisseur $fournisseur, EntityManagerInterface $em)
        {
            $form = $this->createForm(FournisseurType::class, $fournisseur);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em->flush();
                return $this->redirectToRoute('app_fournisseurs');
            }
            return $this->render('fournisseur/modifierfournisseur.html.twig',array(
                'form'=>$form->createView()
            ));
        }
    
        /*Fonction de suppression d'un fournisseur*/
        #[Route("/fournisseur/supprimer/{id<\d+>}", name: "app_supprimer_fournisseur")]
        public function supprimerFournisseur(Fournisseur $fournisseur, EntityManagerInterface $em)
        {
            $em ->remove($fournisseur);
            $em ->flush();
            return $this->redirectToRoute('app_fournisseurs'); 
        }

            /**
   	 * @Route("/remove_Fournisseur/{id}", name="remove_Fournisseur")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Fournisseur $fournisseur)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($fournisseur)){

	        $fournisseur = $em->getRepository(Fournisseur::class)->find($id);
	        $em->remove($fournisseur);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirectToRoute('app_fournisseurs'); 
	    }else{
	    	return new Response('1');
	    }
    }
}
