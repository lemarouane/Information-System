<?php

namespace App\Controller;

use App\Entity\Cycle;
use App\Form\CycleType;
use App\Repository\CycleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class CycleController extends AbstractController
{
    
    #[Route('/cycle', name: 'app_cycle_index', methods: ['GET'])]
    public function index(CycleRepository $cycleRepository): Response
    {
        return $this->render('cycle/table-datatable-cycle.html.twig', [
            'cycles' => $cycleRepository->findAll(),
        ]);
    }

    #[Route('/cycle_new', name: 'app_cycle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CycleRepository $cycleRepository): Response
    {
        $cycle = new Cycle();
        $form = $this->createForm(CycleType::class, $cycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cycleRepository->save($cycle, true);

            return $this->redirectToRoute('app_cycle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cycle/new-cycle.html.twig', [
            'cycle' => $cycle,
            'form' => $form,
        ]);
    }

   

    #[Route('/cycle_{id}_edit', name: 'app_cycle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cycle $cycle, CycleRepository $cycleRepository): Response
    {
        $form = $this->createForm(CycleType::class, $cycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cycleRepository->save($cycle, true);

            return $this->redirectToRoute('app_cycle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cycle/edit-cycle.html.twig', [
            'cycle' => $cycle,
            'form' => $form,
        ]);
    }

    #[Route('/cycle_{id}_{_token}', name: 'app_cycle_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Cycle $cycle, CycleRepository $cycleRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cycle->getId(),  $_token)) {
            $cycleRepository->remove($cycle, true);
        }

        return $this->redirectToRoute('app_cycle_index', [], Response::HTTP_SEE_OTHER);
    }
}
