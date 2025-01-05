<?php

namespace App\Controller;

use App\Entity\StructRech;
use App\Form\StructRechType;
use App\Repository\StructRechRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class StructRechController extends AbstractController
{
    #[Route('/struct_rech', name: 'app_struct_rech_index', methods: ['GET'])]
    public function index(StructRechRepository $structRechRepository): Response
    {
        return $this->render('struct_rech/table-datatable-struct.html.twig', [
            'struct_reches' => $structRechRepository->findAll(),
        ]);
    }

    #[Route('/new_struct_rech', name: 'app_struct_rech_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StructRechRepository $structRechRepository): Response
    {
        $structRech = new StructRech();
        $form = $this->createForm(StructRechType::class, $structRech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structRechRepository->save($structRech, true);

            return $this->redirectToRoute('app_struct_rech_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('struct_rech/new-struct.html.twig', [
            'struct_rech' => $structRech,
            'form' => $form,
        ]);
    }

  

    #[Route('/struct_rech_{id}_edit', name: 'app_struct_rech_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StructRech $structRech, StructRechRepository $structRechRepository): Response
    {
        $form = $this->createForm(StructRechType::class, $structRech);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structRechRepository->save($structRech, true);

            return $this->redirectToRoute('app_struct_rech_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('struct_rech/edit-struct.html.twig', [
            'struct_rech' => $structRech,
            'form' => $form,
        ]);
    }

    #[Route('/struct_rech_{id}_{_token}', name: 'app_struct_rech_delete', methods: ['GET','POST'])]
    public function delete(Request $request, StructRech $structRech, StructRechRepository $structRechRepository, $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$structRech->getId(), $_token)) {
            $structRechRepository->remove($structRech, true);
        }

        return $this->redirectToRoute('app_struct_rech_index', [], Response::HTTP_SEE_OTHER);
    }
}
