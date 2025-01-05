<?php

namespace App\Controller;

use App\Entity\Diplome;
use App\Form\DiplomeType;
use App\Repository\DiplomeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Service\InternetTest;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class DiplomeController extends AbstractController
{
    #[Route('/diplome', name: 'app_diplome_index', methods: ['GET'])]
    public function index(DiplomeRepository $diplomeRepository): Response
    {
        return $this->render('diplome/table-datatable-diplome.html.twig', [
            'diplomes' => $diplomeRepository->findAll(),
        ]);
    }

    #[Route('/diplome_new', name: 'app_diplome_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DiplomeRepository $diplomeRepository): Response
    {
        $diplome = new Diplome();
        $form = $this->createForm(DiplomeType::class, $diplome);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diplomeRepository->save($diplome, true);

            return $this->redirectToRoute('app_diplome_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diplome/new-diplome.html.twig', [
            'diplome' => $diplome,
            'form' => $form,
        ]);
    }

  

    #[Route('/diplome_{id}_edit', name: 'app_diplome_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Diplome $diplome, DiplomeRepository $diplomeRepository): Response
    {
        $form = $this->createForm(DiplomeType::class, $diplome);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diplomeRepository->save($diplome, true);

            return $this->redirectToRoute('app_diplome_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diplome/edit-diplome.html.twig', [
            'diplome' => $diplome,
            'form' => $form,
        ]);
    }

    #[Route('/diplome_{id}_{_token}', name: 'app_diplome_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Diplome $diplome, DiplomeRepository $diplomeRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$diplome->getId(), $_token)) {
            $diplomeRepository->remove($diplome, true);
        }

        return $this->redirectToRoute('app_diplome_index', [], Response::HTTP_SEE_OTHER);
    }
}
