<?php

namespace App\Controller;

use App\Entity\Echelon;
use App\Form\EchelonType;
use App\Repository\EchelonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class EchelonController extends AbstractController
{
    #[Route('/echelon', name: 'app_echelon_index', methods: ['GET'])]
    public function index(EchelonRepository $echelonRepository): Response
    {
        return $this->render('echelon/table-datatable-echelon.html.twig', [
            'echelons' => $echelonRepository->findAll(),
        ]);
    }

    #[Route('/echelon_new', name: 'app_echelon_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EchelonRepository $echelonRepository): Response
    {
        $echelon = new Echelon();
        $form = $this->createForm(EchelonType::class, $echelon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $echelonRepository->save($echelon, true);

            return $this->redirectToRoute('app_echelon_index', [], Response::HTTP_SEE_OTHER);
        }
 
        return $this->renderForm('echelon/new-echelon.html.twig', [
            'echelon' => $echelon,
            'form' => $form,
        ]);
    }

   

    #[Route('/echelon_{id}_edit', name: 'app_echelon_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Echelon $echelon, EchelonRepository $echelonRepository): Response
    {
        $form = $this->createForm(EchelonType::class, $echelon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $echelonRepository->save($echelon, true);

            return $this->redirectToRoute('app_echelon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('echelon/edit-echelon.html.twig', [
            'echelon' => $echelon,
            'form' => $form,
        ]);
    }

    #[Route('/echelon_{id}_{_token}', name: 'app_echelon_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Echelon $echelon, EchelonRepository $echelonRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echelon->getId(), $_token)) {
            $echelonRepository->remove($echelon, true);
        }

        return $this->redirectToRoute('app_echelon_index', [], Response::HTTP_SEE_OTHER);
    }
}
