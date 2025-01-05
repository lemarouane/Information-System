<?php

namespace App\Controller;

use App\Entity\GradeAv;
use App\Entity\Grades;
use App\Form\GradeAvType;
use App\Repository\GradeAvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class GradeAvController extends AbstractController
{
    #[Route('/grade_av', name: 'app_grade_av_index', methods: ['GET'])]
    public function index(GradeAvRepository $gradeAvRepository): Response
    {
        return $this->render('grade_av/table-datatable-gradeav.html.twig', [
            'grade_avs' => $gradeAvRepository->findAll(),
        ]);
    }

    #[Route('/grade_av_new', name: 'app_grade_av_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GradeAvRepository $gradeAvRepository): Response
    {
        $gradeAv = new GradeAv();
        $form = $this->createForm(GradeAvType::class, $gradeAv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gradeAvRepository->save($gradeAv, true);

            return $this->redirectToRoute('app_grade_av_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('grade_av/new-gradeav.html.twig', [
            'grade_av' => $gradeAv,
            'form' => $form,
        ]);
    }



    #[Route('/grade_av_{id}_edit', name: 'app_grade_av_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GradeAv $gradeAv, GradeAvRepository $gradeAvRepository): Response
    {
        $form = $this->createForm(GradeAvType::class, $gradeAv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gradeAvRepository->save($gradeAv, true);

            return $this->redirectToRoute('app_grade_av_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('grade_av/edit-gradeav.html.twig', [
            'grade_av' => $gradeAv,
            'form' => $form,
        ]);
    }

    #[Route('/grade_av_{id}_{_token}', name: 'app_grade_av_delete', methods: ['GET','POST'])]
    public function delete(Request $request, GradeAv $gradeAv, GradeAvRepository $gradeAvRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gradeAv->getId(),$_token)) {
            $gradeAvRepository->remove($gradeAv, true);
        }

        return $this->redirectToRoute('app_grade_av_index', [], Response::HTTP_SEE_OTHER);
    }
}
