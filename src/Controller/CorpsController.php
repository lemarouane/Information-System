<?php

namespace App\Controller;

use App\Entity\Corps;
use App\Form\CorpsType;
use App\Repository\CorpsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Twig\ConfigExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class CorpsController extends AbstractController
{
    
    #[Route('/corps', name: 'app_corps_index', methods: ['GET'])]
    public function index(CorpsRepository $corpsRepository): Response
    {
        return $this->render('corps/table-datatable-corps.html.twig', [
            'corps' => $corpsRepository->findAll(),
        ]);
    }

    
    #[Route('/corps_new', name: 'app_corps_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CorpsRepository $corpsRepository): Response
    {
        $corps = new Corps();
        $form = $this->createForm(CorpsType::class, $corps);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $corpsRepository->save($corps, true);

            return $this->redirectToRoute('app_corps_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('corps/new-corps.html.twig', [
            'corps' => $corps,
            'form' => $form,
        ]);
    }



    #[Route('/corps_{id}_edit', name: 'app_corps_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Corps $corps, CorpsRepository $corpsRepository): Response
    {
        $form = $this->createForm(CorpsType::class, $corps);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $corpsRepository->save($corps, true);

            return $this->redirectToRoute('app_corps_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('corps/edit-corps.html.twig', [
            'corps' => $corps,
            'form' => $form,
        ]);
    }

    #[Route('/delete_{id}', name: 'app_corps_delete', methods: ['POST'])]
    public function delete(Request $request, Corps $corps, CorpsRepository $corpsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$corps->getId(), $request->request->get('_token'))) {
            $corpsRepository->remove($corps, true);
        }

        return $this->redirectToRoute('app_corps_index', [], Response::HTTP_SEE_OTHER);
    }
}
