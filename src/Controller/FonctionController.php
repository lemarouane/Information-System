<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Form\FonctionType;
use App\Repository\FonctionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class FonctionController extends AbstractController
{
    #[Route('/fonction', name: 'app_fonction_index', methods: ['GET'])]
    public function index(FonctionRepository $fonctionRepository): Response
    {
        return $this->render('fonction/table-datatable-fonction.html.twig', [
            'fonctions' => $fonctionRepository->findAll(),
        ]);
    }

    #[Route('/fonction_new', name: 'app_fonction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FonctionRepository $fonctionRepository): Response
    {
        $fonction = new Fonction();
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fonctionRepository->save($fonction, true);

            return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fonction/new-fonction.html.twig', [
            'fonction' => $fonction,
            'form' => $form,
        ]);
    }

  

    #[Route('/fonction_{id}_edit', name: 'app_fonction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fonction $fonction, FonctionRepository $fonctionRepository): Response
    {
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fonctionRepository->save($fonction, true);

            return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fonction/edit-fonction.html.twig', [
            'fonction' => $fonction,
            'form' => $form,
        ]);
    }

    #[Route('/fonction_{id}_{_token}', name: 'app_fonction_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Fonction $fonction, FonctionRepository $fonctionRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fonction->getId(), $_token)) {
            $fonctionRepository->remove($fonction, true);
        }

        return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
    }
}
