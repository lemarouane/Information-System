<?php

namespace App\Controller;

use App\Entity\Grades;
use App\Form\GradesType;
use App\Repository\GradesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class GradesController extends AbstractController
{
    #[Route('/grades', name: 'app_grades_index', methods: ['GET'])]
    public function index(GradesRepository $gradesRepository): Response
    {
        return $this->render('grades/table-datatable-grades.html.twig', [
            'grades' => $gradesRepository->findAll(),
        ]);
    }

    #[Route('/grades_new', name: 'app_grades_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GradesRepository $gradesRepository): Response
    {
        $grade = new Grades();
        $form = $this->createForm(GradesType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gradesRepository->save($grade, true);

            return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('grades/new-grades.html.twig', [
            'grade' => $grade,
            'form' => $form,
        ]);
    }

  

    #[Route('/grades_{id}_edit', name: 'app_grades_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Grades $grade, GradesRepository $gradesRepository): Response
    {
        $form = $this->createForm(GradesType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gradesRepository->save($grade, true);

            return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('grades/edit-grades.html.twig', [
            'grade' => $grade,
            'form' => $form,
        ]);
    }

    #[Route('/grades_{id}_{_token}', name: 'app_grades_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Grades $grade, GradesRepository $gradesRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$grade->getId(),  $_token)) {
            $gradesRepository->remove($grade, true);
        }

        return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
    }
}
