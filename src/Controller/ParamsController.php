<?php

namespace App\Controller; 

use App\Entity\Params;
use App\Form\ParamsType;
use App\Repository\ParamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\FileUploader;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class ParamsController extends AbstractController
{
    #[Route('/params', name: 'app_params_index', methods: ['GET'])]
    public function index(ParamsRepository $paramsRepository): Response
    {
        return $this->render('params/table-datatable-params.html.twig', [
            'params' => $paramsRepository->findAll(),
        ]);
    }

    #[Route('/params_new', name: 'app_params_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParamsRepository $paramsRepository): Response
    {
        $param = new Params();
        $form = $this->createForm(ParamsType::class, $param);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paramsRepository->save($param, true);

            return $this->redirectToRoute('app_params_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('params/new-params.html.twig', [
            'param' => $param,
            'form' => $form,
        ]);
    }

   
    #[Route('/params_{id}_edit', name: 'app_params_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Params $param, ParamsRepository $paramsRepository , FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ParamsType::class, $param);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

            $image = $form->get('imageFile')->getData();
            if(!empty($image)){
                $imageName = $fileUploader->upload($image);
                $param->setImageName($imageName);
            }
            $image2 = $form->get('imageFile2')->getData();
            if(!empty($image2)){
                $imageName2 = $fileUploader->upload($image2);
                $param->setImageName2($imageName2);
            }

            $paramsRepository->save($param, true);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

            return $this->redirectToRoute('app_params_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('params/edit-params.html.twig', [
            'param' => $param,
            'form' => $form,
        ]);
    }

    #[Route('/params_{id}_{_token}', name: 'app_params_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Params $param, ParamsRepository $paramsRepository,$_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$param->getId(), $_token)) {
            $paramsRepository->remove($param, true);
        }

        return $this->redirectToRoute('app_params_index', [], Response::HTTP_SEE_OTHER);
    }
}
