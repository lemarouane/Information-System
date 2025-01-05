<?php

namespace App\Controller;

use App\Entity\Engagement;
use App\Form\EngagementType;
use App\Repository\EngagementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/engagement')]
class EngagementController extends AbstractController
{
    #[Route('/', name: 'app_engagement_index', methods: ['GET'])]
    public function index(EngagementRepository $engagementRepository): Response
    {
        return $this->render('engagement/index.html.twig', [
            'engagements' => $engagementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_engagement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EngagementRepository $engagementRepository): Response
    {
        $engagement = new Engagement();
        $form = $this->createForm(EngagementType::class, $engagement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $engagementRepository->save($engagement, true);

            return $this->redirectToRoute('app_engagement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('engagement/new.html.twig', [
            'engagement' => $engagement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_engagement_show', methods: ['GET'])]
    public function show(Engagement $engagement): Response
    {
        return $this->render('engagement/show.html.twig', [
            'engagement' => $engagement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_engagement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Engagement $engagement, EngagementRepository $engagementRepository): Response
    {
        $form = $this->createForm(EngagementType::class, $engagement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $engagementRepository->save($engagement, true);

            return $this->redirectToRoute('app_engagement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('engagement/edit.html.twig', [
            'engagement' => $engagement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_engagement_delete', methods: ['POST'])]
    public function delete(Request $request, Engagement $engagement, EngagementRepository $engagementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$engagement->getId(), $request->request->get('_token'))) {
            $engagementRepository->remove($engagement, true);
        }

        return $this->redirectToRoute('app_engagement_index', [], Response::HTTP_SEE_OTHER);
    }
}
