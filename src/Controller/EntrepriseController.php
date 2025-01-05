<?php

namespace App\Controller;

use App\Entity\Etudiant\Entreprises;
use App\Form\Etudiant\EntreprisesType;
use App\Repository\EntreprisesRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise_index', methods: ['GET'])]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $entreprises = $em->getRepository(Entreprises::class)->findAll();
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    #[Route('/entreprise_new', name: 'app_entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $entreprise = new Entreprises();
        $form = $this->createForm(EntreprisesType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();
            if(!empty($fichier)){

                
                if ( file_exists($this->getParameter('webroot_ent').$entreprise->getFichier()) 
                    && $entreprise->getFichier()!="" && $entreprise->getFichier()!=NULL){
                    unlink($this->getParameter('webroot_ent').$entreprise->getFichier());
                }
                
                $fileUploader = new FileUploader($this->getParameter('webroot_ent'));
                $invitName = $fileUploader->upload($fichier);
         
                $entreprise->setFichier($invitName);
            }
            $em->persist($entreprise);
            $em->flush();

            return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entreprise/new.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }



    #[Route('/entreprises_edit/{id}', name: 'app_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $entreprise = $em->getRepository(Entreprises::class)->find($id);
        $form = $this->createForm(EntreprisesType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();
            if(!empty($fichier)){

                
                if ( file_exists($this->getParameter('webroot_ent').$entreprise->getFichier()) 
                    && $entreprise->getFichier()!="" && $entreprise->getFichier()!=NULL){
                    unlink($this->getParameter('webroot_ent').$entreprise->getFichier());
                }
                
                $fileUploader = new FileUploader($this->getParameter('webroot_ent'));
                $invitName = $fileUploader->upload($fichier);
         
                $entreprise->setFichier($invitName);
            }
            $em->persist($entreprise);
            $em->flush();

            return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }

    #[Route('/entreprise_delete/{id}/{_token}', name: 'app_entreprise_delete', methods: ['GET','POST'])]
    public function delete(Request $request, $id,$_token): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $entreprise = $em->getRepository(Entreprises::class)->find($id);
        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $_token)) {
            if ( file_exists($this->getParameter('webroot_ent').$entreprise->getFichier()) 
                    && $entreprise->getFichier()!="" && $entreprise->getFichier()!=NULL){
                unlink($this->getParameter('webroot_ent').$entreprise->getFichier());
            }
            $em->remove($entreprise);
            $em->flush();
        }

        return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
    }
}
