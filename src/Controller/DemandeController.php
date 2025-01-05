<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\Demande;
use App\Form\DemandeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class DemandeController extends AbstractController
{


    /**
     * @Route("/demandes", name="demandes")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $demandes = $em->getRepository(Demande::class)->findAll();
        
        return $this->render('demande/liste.html.twig', array('demandes' => $demandes));

        
    }
    /**
     *  @Route("/showDemande/{id}", name="showDemande")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Demande::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('demande/show.html.twig', array('demande' => $entity));
    }

    /**
     * @Route("/editDemande/{id}", name="editDemande")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Demande $entity)
    {
        $form = $this->createForm(DemandeType::class, $entity);
        return $this->render('demande/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateDemande/{id}", name="updateDemande")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, Demande $demande) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $em->persist($demande);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('editDemande', array('id' => $demande->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('demande/edit.html.twig', array('entity' => $demande, 'form' => $form->createView()));
    }


    /**
     * @Route("/addDemande", name="addDemande")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request, TokenStorageInterface $tokenStorage)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Demande();
        $entity->setNumDemande('DEM'.str_pad($em->getRepository(Demande::class)->getNumDemande()['nextval'], 6, '0', STR_PAD_LEFT));
        $form = $this->createForm(DemandeType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->getRepository(Demande::class)->NextNumDemande();
            $entity->setPersonnel($tokenStorage->getToken()->getUser()->getPersonnel());
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
           return $this->render('demande/show.html.twig', array('demande' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('demande/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('demande/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_Demande/{id}", name="remove_Demande")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Demande $demande)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($demande)){

	        $demande = $em->getRepository(Demande::class)->find($id);
	        $em->remove($demande);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('demandes'));
	    }else{
	    	return new Response('1');
	    }
    }
}