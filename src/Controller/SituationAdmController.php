<?php

namespace App\Controller;

use App\Entity\SituationAdm;
use App\Form\SituationAdmType;
use App\Repository\SituationAdmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/situation/adm')]
class SituationAdmController extends AbstractController
{
    #[Route('/', name: 'app_situation_adm_index', methods: ['GET'])]
    public function index(SituationAdmRepository $situationAdmRepository): Response
    {
        return $this->render('situation_adm/index.html.twig', [
            'situation_adms' => $situationAdmRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_situation_adm_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SituationAdmRepository $situationAdmRepository): Response
    {
        $situationAdm = new SituationAdm();
        $form = $this->createForm(SituationAdmType::class, $situationAdm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $situationAdmRepository->save($situationAdm, true);

            return $this->redirectToRoute('app_situation_adm_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('situation_adm/new.html.twig', [
            'situation_adm' => $situationAdm,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_situation_adm_show', methods: ['GET'])]
    public function show(SituationAdm $situationAdm): Response
    {
        return $this->render('situation_adm/show.html.twig', [
            'situation_adm' => $situationAdm,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_situation_adm_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SituationAdm $situationAdm, SituationAdmRepository $situationAdmRepository): Response
    {
        $form = $this->createForm(SituationAdmType::class, $situationAdm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $situationAdmRepository->save($situationAdm, true);

            return $this->redirectToRoute('app_situation_adm_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('situation_adm/edit.html.twig', [
            'situation_adm' => $situationAdm,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_situation_adm_delete', methods: ['POST'])]
    public function delete(Request $request, SituationAdm $situationAdm, SituationAdmRepository $situationAdmRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$situationAdm->getId(), $request->request->get('_token'))) {
            $situationAdmRepository->remove($situationAdm, true);
        }

        return $this->redirectToRoute('app_situation_adm_index', [], Response::HTTP_SEE_OTHER);
    }
}
