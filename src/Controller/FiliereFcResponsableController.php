<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces;


use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Entity\FiliereFcResponsable;
use App\Form\FiliereFcResponsableType;
use App\Repository\FiliereFcResponsableRepository;




class FiliereFcResponsableController extends AbstractController
{
    #[Route('/ffc_responsable', name: 'ffc_responsable', methods: ['GET'])]
    public function index(FiliereFcResponsableRepository $filiereFcResponsableRepository): Response
    {
        return $this->render('filiere_fc_responsable/table-datatable-filierefc-resp.html.twig', [
            'filiere_fc_responsable' => $filiereFcResponsableRepository->findAll(),
        ]);
    }

    #[Route('/ffcr_new', name: 'ffcr_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FiliereFcResponsableRepository $filiereFcResponsableRepository): Response
    {
        $filiereFcResponsable = new FiliereFcResponsable();
        $form = $this->createForm(FiliereFcResponsableType::class, $filiereFcResponsable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiereFcResponsableRepository->save($filiereFcResponsable, true);

            return $this->redirectToRoute('ffc_responsable', [], Response::HTTP_SEE_OTHER);
        } 

        return $this->renderForm('filiere_fc_responsable/new-filierefc-resp.html.twig', [
            'filiere_fc_responsable' => $filiereFcResponsable,
            'form' => $form,
        ]);
    }

   

    #[Route('/ffcr_{id}_edit', name: 'ffcr_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FiliereFcResponsable $filiereFcResponsable, FiliereFcResponsableRepository $filiereFcResponsableRepository): Response
    {
        $form = $this->createForm(FiliereFcResponsableType::class, $filiereFcResponsable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiereFcResponsableRepository->save($filiereFcResponsable, true);

            return $this->redirectToRoute('ffc_responsable', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('filiere_fc_responsable/edit-filierefc-resp.html.twig', [
            'filiere_fc_responsable' => $filiereFcResponsable,
            'form' => $form,
        ]);
    }

    #[Route('/ffcr_{id}_delete', name: 'ffcr_delete', methods: ['POST'])]
    public function delete(Request $request, FiliereFcResponsable $filiereFcResponsable, FiliereFcResponsableRepository $filiereFcResponsableRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filiereFcResponsable->getId(), $request->request->get('_token'))) {
            $filiereFcResponsableRepository->remove($filiereFcResponsable, true);
        }

        return $this->redirectToRoute('ffc_responsable', [], Response::HTTP_SEE_OTHER);
    }
}
