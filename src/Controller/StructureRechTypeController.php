<?php

namespace App\Controller;

use App\Entity\StructureRechType;
use App\Form\StructureRechTypeType;
use App\Repository\StructureRechTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class StructureRechTypeController extends AbstractController
{
    #[Route('/structure_rech_type', name: 'app_structure_rech_type_index', methods: ['GET'])]
    public function index(StructureRechTypeRepository $structureRechTypeRepository): Response
    {
        return $this->render('structure_rech_type/table-datatable-structtype.html.twig', [
            'structure_rech_types' => $structureRechTypeRepository->findAll(),
        ]);
    }

    #[Route('/new_structure_rech_type', name: 'app_structure_rech_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StructureRechTypeRepository $structureRechTypeRepository): Response
    {
        $structureRechType = new StructureRechType();
        $form = $this->createForm(StructureRechTypeType::class, $structureRechType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureRechTypeRepository->save($structureRechType, true);

            return $this->redirectToRoute('app_structure_rech_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('structure_rech_type/new-structtype.html.twig', [
            'structure_rech_type' => $structureRechType,
            'form' => $form,
        ]);
    }

   

    #[Route('/structure_rech_type_{id}_edit', name: 'app_structure_rech_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StructureRechType $structureRechType, StructureRechTypeRepository $structureRechTypeRepository): Response
    {
        $form = $this->createForm(StructureRechTypeType::class, $structureRechType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureRechTypeRepository->save($structureRechType, true);

            return $this->redirectToRoute('app_structure_rech_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('structure_rech_type/edit-structtype.html.twig', [
            'structure_rech_type' => $structureRechType,
            'form' => $form,
        ]);
    }

    #[Route('/structure_rech_type_{id}_{_token}', name: 'app_structure_rech_type_delete', methods: ['GET','POST'])]
    public function delete(Request $request, StructureRechType $structureRechType, StructureRechTypeRepository $structureRechTypeRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$structureRechType->getId(),  $_token)) {
            $structureRechTypeRepository->remove($structureRechType, true);
        }

        return $this->redirectToRoute('app_structure_rech_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
