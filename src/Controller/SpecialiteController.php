<?php

namespace App\Controller;

use App\Entity\Specialite;
use App\Form\SpecialiteType;
use App\Repository\SpecialiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class SpecialiteController extends AbstractController
{
    #[Route('/specialite', name: 'app_specialite_index', methods: ['GET'])]
    public function index(SpecialiteRepository $specialiteRepository): Response
    {
        return $this->render('specialite/table-datatable-spec.html.twig', [
            'specialites' => $specialiteRepository->findAll(),
        ]);
    }

    #[Route('/specialite_new', name: 'app_specialite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SpecialiteRepository $specialiteRepository): Response
    {
        $specialite = new Specialite();
        $form = $this->createForm(SpecialiteType::class, $specialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specialiteRepository->save($specialite, true);

            return $this->redirectToRoute('app_specialite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('specialite/new-spec.html.twig', [
            'specialite' => $specialite,
            'form' => $form,
        ]);
    }

   

    #[Route('/specialite_{id}_edit', name: 'app_specialite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Specialite $specialite, SpecialiteRepository $specialiteRepository): Response
    {
        $form = $this->createForm(SpecialiteType::class, $specialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specialiteRepository->save($specialite, true);

            return $this->redirectToRoute('app_specialite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('specialite/edit-spec.html.twig', [
            'specialite' => $specialite,
            'form' => $form,
        ]);
    }

    #[Route('/specialite_{id}_{_token}', name: 'app_specialite_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Specialite $specialite, SpecialiteRepository $specialiteRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$specialite->getId(), $_token)) {
            $specialiteRepository->remove($specialite, true);
        }

        return $this->redirectToRoute('app_specialite_index', [], Response::HTTP_SEE_OTHER);
    }
}
