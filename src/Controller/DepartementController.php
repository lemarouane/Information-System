<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class DepartementController extends AbstractController
{
    #[Route('/departement', name: 'app_departement_index', methods: ['GET'])]
    public function index(DepartementRepository $departementRepository): Response
    {
        return $this->render('departement/table-datatable-departement.html.twig', [
            'departements' => $departementRepository->findAll(),
        ]);
    }

    #[Route('/departement_new', name: 'app_departement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepartementRepository $departementRepository): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $departementRepository->save($departement, true);

            return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departement/new-departement.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }



    #[Route('/departement_{id}_edit', name: 'app_departement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Departement $departement, DepartementRepository $departementRepository): Response
    {
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $departementRepository->save($departement, true);

            return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departement/edit-departement.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }

    #[Route('/departement_{id}_{_token}', name: 'app_departement_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Departement $departement, DepartementRepository $departementRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departement->getId(), $_token)) {
            $departementRepository->remove($departement, true);
        }

        return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
    }
}
