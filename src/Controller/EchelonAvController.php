<?php

namespace App\Controller;

use App\Entity\EchelonAv;
use App\Entity\Echelon;
use App\Form\EchelonAvType;
use App\Repository\EchelonAvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class EchelonAvController extends AbstractController
{
    #[Route('/echelonav', name: 'app_echelon_av_index', methods: ['GET'])]
    public function index(EchelonAvRepository $echelonAvRepository): Response
    {
        return $this->render('echelon_av/table-datatable-echelonav.html.twig', [
            'echelon_avs' => $echelonAvRepository->findAll(),
        ]);
    }

    #[Route('/echelonav_new', name: 'app_echelon_av_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EchelonAvRepository $echelonAvRepository): Response
    {
        $echelonAv = new EchelonAv();
        $form = $this->createForm(EchelonAvType::class, $echelonAv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $echelonAvRepository->save($echelonAv, true);

            return $this->redirectToRoute('app_echelon_av_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('echelon_av/new-echelonav.html.twig', [
            'echelon_av' => $echelonAv,
            'form' => $form,
        ]);
    }

  

    #[Route('/echelonav_{id}_edit', name: 'app_echelon_av_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EchelonAv $echelonAv, EchelonAvRepository $echelonAvRepository): Response
    {
        $form = $this->createForm(EchelonAvType::class, $echelonAv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $echelonAvRepository->save($echelonAv, true);

            return $this->redirectToRoute('app_echelon_av_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('echelon_av/edit-echelonav.html.twig', [
            'echelon_av' => $echelonAv,
            'form' => $form,
        ]);
    }

    #[Route('/echelonav_{id}_{_token}', name: 'app_echelon_av_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, EchelonAv $echelonAv, EchelonAvRepository $echelonAvRepository ,  $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echelonAv->getId(),  $_token)) {
            $echelonAvRepository->remove($echelonAv, true);
        }

        return $this->redirectToRoute('app_echelon_av_index', [], Response::HTTP_SEE_OTHER);
    }
}
