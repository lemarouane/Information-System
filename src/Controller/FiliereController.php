<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class FiliereController extends AbstractController
{
    #[Route('/filiere', name: 'app_filiere_index', methods: ['GET'])]
    public function index(FiliereRepository $filiereRepository): Response
    {
        return $this->render('filiere/table-datatable-filiere.html.twig', [
            'filieres' => $filiereRepository->findAll(),
        ]);
    }

    #[Route('/filiere_new', name: 'app_filiere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FiliereRepository $filiereRepository): Response
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiereRepository->save($filiere, true);

            return $this->redirectToRoute('app_filiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('filiere/new-filiere.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }

  

    #[Route('/filiere_{id}_edit', name: 'app_filiere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filiere $filiere, FiliereRepository $filiereRepository): Response
    {
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiereRepository->save($filiere, true);

            return $this->redirectToRoute('app_filiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('filiere/edit-filiere.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }

    #[Route('/filiere_{id}_{_token}', name: 'app_filiere_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Filiere $filiere, FiliereRepository $filiereRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filiere->getId(), $_token)) {
            $filiereRepository->remove($filiere, true);
        }

        return $this->redirectToRoute('app_filiere_index', [], Response::HTTP_SEE_OTHER);
    }
}
