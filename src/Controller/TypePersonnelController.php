<?php

namespace App\Controller;

use App\Entity\TypePersonnel;
use App\Form\TypePersonnelType;
use App\Repository\TypePersonnelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class TypePersonnelController extends AbstractController
{
    #[Route('/type_personnel', name: 'app_type_personnel_index', methods: ['GET'])]
    public function index(TypePersonnelRepository $typePersonnelRepository): Response
    {
        return $this->render('type_personnel/table-datatable-typeperso.html.twig', [
            'type_personnels' => $typePersonnelRepository->findAll(),
        ]);
    }

    #[Route('/new_type_personnel', name: 'app_type_personnel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TypePersonnelRepository $typePersonnelRepository): Response
    {
        $typePersonnel = new TypePersonnel();
        $form = $this->createForm(TypePersonnelType::class, $typePersonnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typePersonnelRepository->save($typePersonnel, true);

            return $this->redirectToRoute('app_type_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_personnel/new-typeperso.html.twig', [
            'type_personnel' => $typePersonnel,
            'form' => $form,
        ]);
    }

  

    #[Route('/type_personnel_{id}_edit', name: 'app_type_personnel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypePersonnel $typePersonnel, TypePersonnelRepository $typePersonnelRepository): Response
    {
        $form = $this->createForm(TypePersonnelType::class, $typePersonnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typePersonnelRepository->save($typePersonnel, true);

            return $this->redirectToRoute('app_type_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_personnel/edit-typeperso.html.twig', [
            'type_personnel' => $typePersonnel,
            'form' => $form,
        ]);
    }

    #[Route('/type_personnel_{id}_{_token}', name: 'app_type_personnel_delete', methods: ['GET','POST'])]
    public function delete(Request $request, TypePersonnel $typePersonnel, TypePersonnelRepository $typePersonnelRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typePersonnel->getId(), $_token)) {
            $typePersonnelRepository->remove($typePersonnel, true);
        }

        return $this->redirectToRoute('app_type_personnel_index', [], Response::HTTP_SEE_OTHER);
    }
}
