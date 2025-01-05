<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/table-datatable-service.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/service_new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ServiceRepository $serviceRepository): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($service, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/new-service.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

 

    #[Route('/service_{id}_edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($service, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/edit-service.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/service_{id}_{_token}', name: 'app_service_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Service $service, ServiceRepository $serviceRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(),  $_token)) {
            $serviceRepository->remove($service, true);
        }

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }


  
}
