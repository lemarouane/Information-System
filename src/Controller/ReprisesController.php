<?php

namespace App\Controller;

use App\Entity\Personnel;
use App\Entity\HistoDemandes;

use App\Repository\HistoDemandesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use App\Service\InternetTest;

class ReprisesController extends AbstractController
{
     /** 
     *
     * @Security("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/reprises', name: 'app_reprises_index', methods: ['GET','POST'])]
    public function index(secure $security , HistoDemandesRepository $histoDemandesRepository ): Response
    {
        $result = NULL ;

    //    $result  =  $histoDemandesRepository->Get_Services_Sup("ROLE_SG");
     //   dd($result);

       $em = $this->getDoctrine()->getManager();
       if(in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles())){
        $personel_service_ids =  $histoDemandesRepository->Get_Fonc_By_Service($security->getUser()->getPersonnel()->getServiceAffectationId()->getId());
        $result =  $histoDemandesRepository->Histo_Demandes_Reprises($personel_service_ids , $security->getUser()->getPersonnel()->getId());
       }

       if(in_array("ROLE_SG",$security->getUser()->getRoles())){
        $personel_service_ids =  $histoDemandesRepository->Get_Fonc_By_Services_SG();
        $result =  $histoDemandesRepository->Histo_Demandes_Reprises($personel_service_ids , $security->getUser()->getPersonnel()->getId());
       }

       if(in_array("ROLE_DIR_ADJ",$security->getUser()->getRoles())){
        $personel_service_ids =  $histoDemandesRepository->Get_Fonc_By_Services_DirAdj($security->getUser()->getCodes());
        $result =  $histoDemandesRepository->Histo_Demandes_Reprises($personel_service_ids , $security->getUser()->getPersonnel()->getId());
       }


        return $this->render('reprises/table-datatable-reprises.html.twig', [
            'reprises' => $result,
        ]);
    }


   /** 
     *
     * @Security("is_granted('ROLE_RH') ")
     */
    #[Route('/reprises_rh', name: 'app_reprises_index_rh', methods: ['GET','POST'])]
    public function index_rh( HistoDemandesRepository $histoDemandesRepository ): Response
    {
 
        $result =  $histoDemandesRepository->Histo_Demandes_Reprises_RH();
       
        return $this->render('reprises_rh/table-datatable-reprises.html.twig', [
            'reprises' => $result,
        ]);
    }



  /** 
     *
     * @Security("is_granted('ROLE_CHEF_SERV') or is_granted('ROLE_SG') or is_granted('ROLE_DIR_ADJ') ")
     */
    #[Route('/reprisesVAL_{id}', name: 'reprisesVAL', methods: ['GET','POST'])]
    public function validation(Request $request  ,secure $security  , HistoDemandes $reprises, HistoDemandesRepository $reprisesRepository , $id , MailerInterface $mailer): Response
    {
        $searchParam = $request->get('searchParam');
        $reprises->setReprise($searchParam['rep']);
 
   
        $reprisesRepository->save($reprises, true);

   
        return new RedirectResponse($this->generateUrl('app_reprises_index'));
    }



}
