<?php

namespace App\Controller;

use App\Entity\Province;
use App\Form\ProvinceType;
use App\Repository\ProvinceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class ProvinceController extends AbstractController
{
    #[Route('/province', name: 'app_province_index', methods: ['GET'])]
    public function index(ProvinceRepository $provinceRepository): Response
    {
        return $this->render('province/table-datatable-province.html.twig', [
            'provinces' => $provinceRepository->findAll(),
        ]);
    }

    #[Route('/province_new', name: 'app_province_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProvinceRepository $provinceRepository): Response
    {
        $province = new Province();
        $form = $this->createForm(ProvinceType::class, $province);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $provinceRepository->save($province, true);

            return $this->redirectToRoute('app_province_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('province/new-province.html.twig', [
            'province' => $province,
            'form' => $form,
        ]);
    }

  

    #[Route('/province_{id}_edit', name: 'app_province_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Province $province, ProvinceRepository $provinceRepository): Response
    {
        $form = $this->createForm(ProvinceType::class, $province);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $provinceRepository->save($province, true);

            return $this->redirectToRoute('app_province_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('province/edit-province.html.twig', [
            'province' => $province,
            'form' => $form,
        ]);
    }

    #[Route('/province_{id}_{_token}', name: 'app_province_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Province $province, ProvinceRepository $provinceRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$province->getId(), $_token)) {
            $provinceRepository->remove($province, true);
        }

        return $this->redirectToRoute('app_province_index', [], Response::HTTP_SEE_OTHER);
    }
}
