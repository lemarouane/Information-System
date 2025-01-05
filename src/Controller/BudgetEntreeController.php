<?php

namespace App\Controller;

use App\Entity\BudgetEntree;
use App\Form\BudgetEntreeType1;
use App\Repository\BudgetEntreeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BudgetEntreeController extends AbstractController
{
    #[Route('/budget_entree', name: 'app_budget_entree_index', methods: ['GET', 'POST'])]
    public function index(BudgetEntreeRepository $budgetEntreeRepository): Response
    {
        return $this->render('budget_entree/index.html.twig', [
            'budget_entrees' => $budgetEntreeRepository->findAll(),
        ]);
    }

    #[Route('/budget_entree_new', name: 'app_budget_entree_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BudgetEntreeRepository $budgetEntreeRepository): Response
    {
        $budgetEntree = new BudgetEntree();
        $form = $this->createForm(BudgetEntreeType1::class, $budgetEntree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $budgetEntree->getBudget()->setTotaleEntree(($budgetEntree->getBudget()->getTotaleEntree()+$budgetEntree->getMontant())) ;
            $budgetEntree->getBudget()->setMontant(($budgetEntree->getBudget()->getMontant()+$budgetEntree->getMontant())) ;
                
            $budgetEntreeRepository->save($budgetEntree, true);

            return $this->redirectToRoute('app_budget_entree_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_entree/new.html.twig', [
            'budget_entree' => $budgetEntree,
            'form' => $form,
        ]);
    }

    #[Route('/budget_entree_{id}_show', name: 'app_budget_entree_show', methods: ['GET'])]
    public function show(BudgetEntree $budgetEntree): Response
    {
        return $this->render('budget_entree/show.html.twig', [
            'budget_entree' => $budgetEntree,
        ]);
    }

    #[Route('/budget_entree_{id}_edit', name: 'app_budget_entree_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BudgetEntree $budgetEntree, BudgetEntreeRepository $budgetEntreeRepository): Response
    {
        $sommeEntree = $budgetEntree->getMontant();
        $form = $this->createForm(BudgetEntreeType1::class, $budgetEntree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $budgetEntree->getBudget()->setTotaleEntree(($budgetEntree->getBudget()->getTotaleEntree()+$budgetEntree->getMontant() - $sommeEntree)) ;
            $budgetEntree->getBudget()->setMontant(($budgetEntree->getBudget()->getMontant()+$budgetEntree->getMontant() - $sommeEntree)) ;
            $budgetEntreeRepository->save($budgetEntree, true);

            return $this->redirectToRoute('app_budget_entree_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_entree/edit.html.twig', [
            'budget_entree' => $budgetEntree,
            'form' => $form,
        ]);
    }

    #[Route('/budget_entree_{id}_delete', name: 'app_budget_entree_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, BudgetEntree $budgetEntree, BudgetEntreeRepository $budgetEntreeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$budgetEntree->getId(), $request->get('_token'))) {
            $budgetEntree->getBudget()->setTotaleEntree(($budgetEntree->getBudget()->getTotaleEntree() - $budgetEntree->getMontant())) ;
            $budgetEntree->getBudget()->setMontant(($budgetEntree->getBudget()->getMontant() - $budgetEntree->getMontant())) ;
            $budgetEntreeRepository->remove($budgetEntree, true);
        }

        return $this->redirectToRoute('app_budget_entree_index', [], Response::HTTP_SEE_OTHER);
    }
}
