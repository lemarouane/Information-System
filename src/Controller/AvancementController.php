<?php

namespace App\Controller;

use App\Entity\Avancement;
use App\Form\AvancementType;
use App\Repository\AvancementRepository;
use App\Entity\Personnel;
use App\Form\PersonnelType;
use App\Repository\PersonnelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use App\Entity\Grades;
use App\Entity\Echelon;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Service\InternetTest;

     /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class AvancementController extends AbstractController
{
    #[Route('/avancement', name: 'app_avancement_index', methods: ['GET'])]
    public function index(AvancementRepository $avancementRepository): Response
    {
        return $this->render('avancement/index.html.twig', [
            'avancements' => $avancementRepository->findAll(),
        ]);
    }

   
    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') ")
     */

    #[Route('/avancement_{id}_new', name: 'app_avancement_new', methods: ['GET', 'POST'])]
    public function new_avancement(Request $request , Personnel $personnel , AvancementRepository $avancementRepository, $id ,  FileUploader $fileUploader): Response
    {
      
       $avancement = new Avancement();      

       $personnel->setId($id) ;   ////
       $avancement->setPersonnel($personnel) ;//// ;

        $form = $this->createForm(AvancementType::class, $avancement);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            

            $arreteFile = $form->get('arreteFile')->getData();
            if(!empty($arreteFile)){
                $originalFilename = pathinfo($arreteFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = sha1(uniqid(mt_rand(), true)).'.'.$arreteFile->guessExtension();
        
                try {
                    $arreteFile->move($this->getParameter('webroot_doc'). $avancement->getPersonnel()->GetNom().'_'.$avancement->getPersonnel()->GetPrenom().'/Arrete/', $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $avancement->setArrete($newFilename);
            }
            $avancementRepository->save($avancement, true);


            $derniere_situation = $form->get('derniere_situation')->getData();

            if($derniere_situation){

                $em = $this->getDoctrine()->getManager();

                $p = $avancement->getPersonnel();

                $p->setCorpsId($avancement->getCorps()) ;

                $p->setGradeId($avancement->getGrade()) ;
         
                $p->setEchelonId($avancement->getEchelon()) ;

                $em->merge($avancement);
                $em->flush();

            }

            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

            return $this->redirectToRoute('app_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avancement/new-avancement.html.twig', [
            'avancement' => $avancement,
            'form' => $form,
            'avancements' => $avancementRepository->findBy(["personnel"=>$personnel->getId()]),
        ]);
    }
 

  /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') ")
     */

    #[Route('/avancement_{id}_edit', name: 'app_avancement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avancement $avancement, AvancementRepository $avancementRepository ,  FileUploader $fileUploader): Response
    {
        $form = $this->createForm(AvancementType::class, $avancement);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

            $arreteFile = $form->get('arreteFile')->getData();
            if(!empty($arreteFile)){

                $newFilename = sha1(uniqid(mt_rand(), true)).'.'.$arreteFile->guessExtension();
                try {
                    $arreteFile->move($this->getParameter('webroot_doc'). $avancement->getPersonnel()->GetNom().'_'.$avancement->getPersonnel()->GetPrenom().'/Arrete/', $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

              // $arreteName = $fileUploader->upload($arreteFile);
               $avancement->setArrete($newFilename);


            }

            $avancementRepository->save($avancement, true);

            $derniere_situation = $form->get('derniere_situation')->getData();

            if($derniere_situation){

                $em = $this->getDoctrine()->getManager();
                //$p = $em->getRepository(Personnel::class)->findBy(["id"=>$personnel->getId()]);

                $p = $avancement->getPersonnel();

                $p->setCorpsId($avancement->getCorps()) ;

                $p->setGradeId($avancement->getGrade()) ;
         
                $p->setEchelonId($avancement->getEchelon()) ;

                $em->merge($avancement);
                $em->flush();

            }

            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");


            return $this->redirectToRoute('app_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avancement/edit-avancement.html.twig', [
            'avancement' => $avancement,
            'form' => $form,
        ]);
    }

     /**
     *
     * @Security("is_granted('ROLE_RH')")
     */
    #[Route('/avancement_{id}_{_token}', name: 'app_avancement_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Avancement $avancement, AvancementRepository $avancementRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avancement->getId(),  $_token)) {
            $avancementRepository->remove($avancement, true);
        }

        return $this->redirectToRoute('app_avancement_index', [], Response::HTTP_SEE_OTHER);
    }



    /// INTERDEPENDET SELECTS

    #[Route('/corps_grades', name: 'corps_grades', methods: ['GET', 'POST'])]
    public function listGradeOfCorpsAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $gradeRepository = $em->getRepository(Grades::class);
        

        $grade = $gradeRepository->createQueryBuilder("g")
            ->where("g.corpsId = :corpsid")
            ->setParameter("corpsid", $request->query->get("corpsid"))
            ->getQuery()
            ->getResult();
        
        
        $responseArray = array();
        foreach($grade as $grade){
            $responseArray[] = array(
                "id" => $grade->getId(),
                "designationFr" => $grade->getDesignationFR()
            );
        }


        return new JsonResponse($responseArray);

        
    }
    #[Route('/grades_echelon', name: 'grades_echelon', methods: ['GET', 'POST'])]
    public function listEchelonOfGradesAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $echelonRepository = $em->getRepository(Echelon::class);
        

        $echelons = $echelonRepository->createQueryBuilder("ech")
            ->where("ech.grade = :gradeid")
            ->setParameter("gradeid", $request->query->get("gradeid"))
            ->getQuery()
            ->getResult();
        
        
        $responseArray = array();
        foreach($echelons as $echelon){
            $responseArray[] = array(
                "id" => $echelon->getId(),
                "designation" => $echelon->getDesignation()
            );
        }
        
        
        return new JsonResponse($responseArray);

        
    }




}
