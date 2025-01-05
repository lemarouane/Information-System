<?php

namespace App\Controller;

use App\Entity\TypeStructRech;
use App\Form\TypeStructRechType;
use App\Repository\TypeStructRechRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class TypeStructRechController extends AbstractController
{
    #[Route('/type_struct_rech', name: 'app_type_struct_rech_index', methods: ['GET'])]
    public function index(TypeStructRechRepository $typeStructRechRepository): Response
    {
        return $this->render('type_struct_rech/table-datatable-typestruct.html.twig', [
            'type_struct_reches' => $typeStructRechRepository->findAll(),
        ]);
    }

    #[Route('/new_type_struct_rech', name: 'app_type_struct_rech_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TypeStructRechRepository $typeStructRechRepository): Response
    {
        $typeStructRech = new TypeStructRech();
        $form = $this->createForm(TypeStructRechType::class, $typeStructRech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeStructRechRepository->save($typeStructRech, true);

            return $this->redirectToRoute('app_type_struct_rech_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_struct_rech/new-typestruct.html.twig', [
            'type_struct_rech' => $typeStructRech,
            'form' => $form,
        ]);
    }

    #[Route('/type_struct_rech_{id}_edit', name: 'app_type_struct_rech_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeStructRech $typeStructRech, TypeStructRechRepository $typeStructRechRepository): Response
    {
        $form = $this->createForm(TypeStructRechType::class, $typeStructRech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeStructRechRepository->save($typeStructRech, true);

            return $this->redirectToRoute('app_type_struct_rech_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_struct_rech/edit-typestruct.html.twig', [
            'type_struct_rech' => $typeStructRech,
            'form' => $form,
        ]);
    }

    #[Route('/type_struct_rech_{id}_{_token}', name: 'app_type_struct_rech_delete', methods: ['GET','POST'])]
    public function delete(Request $request, TypeStructRech $typeStructRech, TypeStructRechRepository $typeStructRechRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeStructRech->getId(), $_token)) {
            $typeStructRechRepository->remove($typeStructRech, true);
        }

        return $this->redirectToRoute('app_type_struct_rech_index', [], Response::HTTP_SEE_OTHER);
    }
}
