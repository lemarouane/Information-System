<?php

namespace App\Controller;

use App\Entity\BudgetSortie;
use App\Entity\Budget;
use App\Form\BudgetSortieType1;
use App\Repository\BudgetSortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BudgetSortieController extends AbstractController
{
    #[Route('/budget_sortie', name: 'app_budget_sortie_index', methods: ['GET', 'POST'])]
    public function index(BudgetSortieRepository $budgetSortieRepository): Response
    {
        return $this->render('budget_sortie/index.html.twig', [
            'budget_sorties' => $budgetSortieRepository->findAll(),
        ]);
    }

    #[Route('/budget_sortie_new', name: 'app_budget_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BudgetSortieRepository $budgetSortieRepository): Response
    {
        $budgetSortie = new BudgetSortie();
        $form = $this->createForm(BudgetSortieType1::class, $budgetSortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetSortie->getBudget()->setTotaleSortie(($budgetSortie->getBudget()->getTotaleSortie() + $budgetSortie->getMontant())) ;
            $budgetSortie->getBudget()->setMontant(($budgetSortie->getBudget()->getMontant() - $budgetSortie->getMontant())) ;
            $budgetSortie->setAnnee($budgetSortie->getBudget()->getAnnee());
            $budgetSortieRepository->save($budgetSortie, true);

            return $this->redirectToRoute('app_budget_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_sortie/new.html.twig', [
            'budget_sortie' => $budgetSortie,
            'form' => $form,
        ]);
    }

    #[Route('/budget_sortie_{id}_show', name: 'app_budget_sortie_show', methods: ['GET'])]
    public function show(BudgetSortie $budgetSortie): Response
    {
        return $this->render('budget_sortie/show.html.twig', [
            'budget_sortie' => $budgetSortie,
        ]);
    }

    #[Route('/budget_sortie_{id}_edit', name: 'app_budget_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BudgetSortie $budgetSortie, BudgetSortieRepository $budgetSortieRepository): Response
    {
        $sommeEntree = $budgetSortie->getMontant();
        $form = $this->createForm(BudgetSortieType1::class, $budgetSortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetSortie->getBudget()->setTotaleSortie(($budgetSortie->getBudget()->getTotaleSortie() + $budgetSortie->getMontant() - $sommeEntree)) ;
            $budgetSortie->getBudget()->setMontant(($budgetSortie->getBudget()->getMontant() - $budgetSortie->getMontant() + $sommeEntree)) ;
            $budgetSortieRepository->save($budgetSortie, true);

            return $this->redirectToRoute('app_budget_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_sortie/edit.html.twig', [
            'budget_sortie' => $budgetSortie,
            'form' => $form,
        ]);
    }

    #[Route('/budget_sortie_{id}_delete', name: 'app_budget_sortie_delete', methods: ['GET','POST'])]
    public function delete(Request $request, BudgetSortie $budgetSortie, BudgetSortieRepository $budgetSortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$budgetSortie->getId(), $request->get('_token'))) {
            $budgetSortie->getBudget()->setTotaleSortie(($budgetSortie->getBudget()->getTotaleSortie() - $budgetSortie->getMontant())) ;
            $budgetSortie->getBudget()->setMontant(($budgetSortie->getBudget()->getMontant() + $budgetSortie->getMontant())) ;
            $budgetSortieRepository->remove($budgetSortie, true);
        }

        return $this->redirectToRoute('app_budget_sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}
