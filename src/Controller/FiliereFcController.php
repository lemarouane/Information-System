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
use App\Entity\FiliereFc;
use App\Form\FiliereFcType;
use App\Repository\FiliereFcRepository;



class FiliereFcController extends AbstractController
{
    /**
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    
    #[Route('/filiere_fc', name: 'filiere_fc', methods: ['GET'])]
    public function index(FiliereFcRepository $filiereFcRepository , secure $security): Response
    {
        return $this->render('filiere_fc/table-datatable-filierefc.html.twig', [
            'filiere_fc' => $filiereFcRepository->findAll(),
        ]);
    }

      /**
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    
    #[Route('/ffc_new', name: 'ffc_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FiliereFcRepository $filiereFcRepository): Response
    {
        $filiereFc = new FiliereFc();
        $form = $this->createForm(FiliereFcType::class, $filiereFc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiereFcRepository->save($filiereFc, true);

            return $this->redirectToRoute('filiere_fc', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('filiere_fc/new-filierefc.html.twig', [
            'filiere_fc' => $filiereFc,
            'form' => $form,
        ]);
    }

  
    #[Route('/ffc_{id}_edit', name: 'ffc_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FiliereFc $filiereFc, FiliereFcRepository $filiereFcRepository): Response
    {
        $form = $this->createForm(FiliereFcType::class, $filiereFc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiereFcRepository->save($filiereFc, true);

            return $this->redirectToRoute('filiere_fc', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('filiere_fc/edit-filierefc.html.twig', [
            'filiere_fc' => $filiereFc,
            'form' => $form,
        ]);
    }

    #[Route('/ffc_{id}_delete', name: 'ffc_delete', methods: ['POST'])]
    public function delete(Request $request, FiliereFc $filiereFc, FiliereFcRepository $filiereFcRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filiereFc->getId(), $request->request->get('_token'))) {
            $filiereFcRepository->remove($filiereFc, true);
        }

        return $this->redirectToRoute('filiere_fc', [], Response::HTTP_SEE_OTHER);
    }
}
