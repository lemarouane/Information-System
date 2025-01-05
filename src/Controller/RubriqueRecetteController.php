<?php

namespace App\Controller;

use App\Entity\RubriqueRecette;
use App\Form\RubriqueRecetteType;
use App\Repository\RubriqueRecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RubriqueRecetteController extends AbstractController
{
    #[Route('/rubriqueRecette', name: 'app_rubrique_recette_index', methods: ['GET','POST'])]
    public function index(RubriqueRecetteRepository $rubriqueRecetteRepository): Response
    {
        return $this->render('rubrique_recette/index.html.twig', [
            'rubrique_recettes' => $rubriqueRecetteRepository->findAll(),
        ]);
    }

    #[Route('/rubriqueRecette/new', name: 'app_rubrique_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RubriqueRecetteRepository $rubriqueRecetteRepository): Response
    {
        $rubriqueRecette = new RubriqueRecette();
        $form = $this->createForm(RubriqueRecetteType::class, $rubriqueRecette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rubriqueRecetteRepository->save($rubriqueRecette, true);

            return $this->redirectToRoute('app_rubrique_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rubrique_recette/new.html.twig', [
            'rubrique_recette' => $rubriqueRecette,
            'form' => $form,
        ]);
    }

    #[Route('/rubriqueRecette/show/{id}', name: 'app_rubrique_recette_show', methods: ['GET'])]
    public function show(RubriqueRecette $rubriqueRecette): Response
    {
        return $this->render('rubrique_recette/show.html.twig', [
            'rubrique_recette' => $rubriqueRecette,
        ]);
    }

    #[Route('/rubriqueRecette/{id}/edit', name: 'app_rubrique_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RubriqueRecette $rubriqueRecette, RubriqueRecetteRepository $rubriqueRecetteRepository): Response
    {
        $form = $this->createForm(RubriqueRecetteType::class, $rubriqueRecette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rubriqueRecetteRepository->save($rubriqueRecette, true);

            return $this->redirectToRoute('app_rubrique_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rubrique_recette/edit.html.twig', [
            'rubrique_recette' => $rubriqueRecette,
            'form' => $form,
        ]);
    }

    #[Route('/rubriqueRecette/delete/{id}', name: 'app_rubrique_recette_delete', methods: ['POST'])]
    public function delete(Request $request, RubriqueRecette $rubriqueRecette, RubriqueRecetteRepository $rubriqueRecetteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rubriqueRecette->getId(), $request->request->get('_token'))) {
            $rubriqueRecetteRepository->remove($rubriqueRecette, true);
        }

        return $this->redirectToRoute('app_rubrique_recette_index', [], Response::HTTP_SEE_OTHER);
    }
}
