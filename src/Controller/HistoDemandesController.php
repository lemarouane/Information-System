<?php

namespace App\Controller;

use App\Entity\HistoDemandes;
use App\Form\HistoDemandesType;
use App\Repository\HistoDemandesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrdreMissionRepository;
use App\Repository\CongeRepository;
use App\Repository\AutorisationRepository;
use App\Repository\FicheheureRepository;
use App\Entity\Conge;
use App\Entity\Ficheheure;
use App\Entity\Autorisation;
use App\Entity\OrdreMission;
use App\Entity\Service;
use App\Entity\Departement;
use App\Entity\StructRech;
use App\Entity\Filiere;

use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class HistoDemandesController extends AbstractController
{



    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE_HISTODEM') or is_granted('ROLE_RH') or is_granted('ROLE_SG')  ")
     */
    #[Route('/histo_dem_valide', name: 'histo_dem_valide', methods: ['GET','POST'])] 
    public function histo_dem_valide(OrdreMissionRepository $ordreMissionRepository , CongeRepository $CongeRepository , FicheheureRepository $FicheheureRepository, AutorisationRepository $AutorisationRepository ,secure $security ): Response
    {
       
        $ordre_missions = $ordreMissionRepository->findBy(array('statut' => array(1)));
        $conges = $CongeRepository->findBy(array('statut' => array(1)));
        $fhs = $FicheheureRepository->findBy(array('statut' => array(1)));
        $autos = $AutorisationRepository->findBy(array('statut' => array(1)));

    
        return $this->render('histo_demandes/table-datatable-histo-dem_val.html.twig',  [
            'oms' => $ordre_missions,
            'conges' => $conges,
            'fhs' => $fhs,
            'autos' => $autos,
        ]);
    }



    #[Route('/histo_demandes', name: 'app_histo_demandes_index', methods: ['GET'])]
    public function index(HistoDemandesRepository $histoDemandesRepository , secure $security ): Response
    {
        return $this->render('histo_demandes/table-datatable-histo_d.html.twig', [
            'histo_demandes' => $histoDemandesRepository->Histo_Demandes($security->getUser()->getPersonnel()->getId()),
        ]);
    }

    #[Route('/histo_demandes_gen', name: 'app_histo_demandes_gen', methods: ['GET'])]
    public function histo_generale(HistoDemandesRepository $histoDemandesRepository , secure $security ): Response
    {
        return $this->render('histo_demandes/table-datatable-histo_g.html.twig', [
            'histo_demandes' => $histoDemandesRepository->Histo_Demandes_Generale(),
        ]);
    }




    #[Route('/histo_vals', name: 'app_histo_demandes_vals', methods: ['GET'])]
    public function histo_vals(HistoDemandesRepository $histoDemandesRepository , secure $security ): Response
    {
        return $this->render('histo_demandes/table-datatable-histo_v.html.twig', [
            'histo_vals' => $histoDemandesRepository->Histo_validations($security->getUser()->getPersonnel()->getId()),
        ]);
    }

    #[Route('/histo_demandes_new', name: 'app_histo_demandes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HistoDemandesRepository $histoDemandesRepository): Response
    {
        $histoDemande = new HistoDemandes();
        $form = $this->createForm(HistoDemandesType::class, $histoDemande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $histoDemandesRepository->save($histoDemande, true);

            return $this->redirectToRoute('app_histo_demandes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('histo_demandes/new.html.twig', [
            'histo_demande' => $histoDemande,
            'form' => $form,
        ]);
    }


    #[Route('/histo_demandes_{id}_show_attestation', name: 'app_histo_demandes_show_attestation', methods: ['GET', 'POST'])]
    public function show_auto_attestation(secure $security , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        
        $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande' => $id , 'demandeur' => $security->getUser()->getPersonnel()->getId() , 'type_demande'=>'attestation' ));

        return $this->render('histo_demandes/show_histo_demande.html.twig', [
            'histo_demandes' => $histo_demandes,
        ]);
    } 



    #[Route('/histo_demandes_{id}_show_conge', name: 'app_histo_demandes_show_conge', methods: ['GET', 'POST'])]
    public function show_auto_conge(secure $security , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        
        $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande' => $id , 'demandeur' => $security->getUser()->getPersonnel()->getId() , 'type_demande'=>'conge' ));

        return $this->render('histo_demandes/show_histo_demande.html.twig', [
            'histo_demandes' => $histo_demandes,
        ]);
    } 


    #[Route('/histo_demandes_{id}_show_auto', name: 'app_histo_demandes_show_auto', methods: ['GET', 'POST'])]
    public function show_auto(secure $security , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        
        $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande' => $id , 'demandeur' => $security->getUser()->getPersonnel()->getId() , 'type_demande'=>'autorisation' ));

        return $this->render('histo_demandes/show_histo_demande.html.twig', [
            'histo_demandes' => $histo_demandes,
        ]);
    } 

    #[Route('/histo_demandes_{id}_show_ordremission', name: 'app_histo_demandes_show_ordremission', methods: ['GET', 'POST'])]
    public function show_ordremission(secure $security , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        
        $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande' => $id , 'demandeur' => $security->getUser()->getPersonnel()->getId() , 'type_demande'=>'ordre de mission' ));

        return $this->render('histo_demandes/show_histo_demande.html.twig', [
            'histo_demandes' => $histo_demandes,
        ]);
    } 

    #[Route('/histo_demandes_{id}_show_ficheheure', name: 'app_histo_demandes_show_ficheheure', methods: ['GET', 'POST'])]
    public function show_ficheheure(secure $security , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        
        $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande' => $id , 'demandeur' => $security->getUser()->getPersonnel()->getId() , 'type_demande'=>'fiche heure' ));

        return $this->render('histo_demandes/show_histo_demande.html.twig', [
            'histo_demandes' => $histo_demandes,
        ]);
    } 

///////////////////////////////////



#[Route('/histo_demandes_{id}_show_attestation_g', name: 'app_histo_demandes_show_attestation_g', methods: ['GET', 'POST'])]
public function show_auto_attestation_g(secure $security , $id): Response
{
    $em = $this->getDoctrine()->getManager();
    $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande'=>$id , 'type_demande'=>'attestation' ));

    return $this->render('histo_demandes/show_histo_demande.html.twig', [
        'histo_demandes' => $histo_demandes,
    ]);
} 



#[Route('/histo_demandes_{id}_show_conge_g', name: 'app_histo_demandes_show_conge_g', methods: ['GET', 'POST'])]
public function show_auto_conge_g($id): Response
{
    $em = $this->getDoctrine()->getManager();
    $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande'=>$id , 'type_demande'=>'conge' ));
   // dd($histo_demandes);
    return $this->render('histo_demandes/show_histo_demande.html.twig', [
        'histo_demandes' => $histo_demandes,
    ]);
} 


#[Route('/histo_demandes_{id}_show_auto_g', name: 'app_histo_demandes_show_auto_g', methods: ['GET', 'POST'])]
public function show_auto_g($id): Response
{
    $em = $this->getDoctrine()->getManager();
    $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande'=>$id , 'type_demande'=>'autorisation' ));

    return $this->render('histo_demandes/show_histo_demande.html.twig', [
        'histo_demandes' => $histo_demandes,
    ]);
} 

#[Route('/histo_demandes_{id}_show_ordremission_g', name: 'app_histo_demandes_show_ordremission_g', methods: ['GET', 'POST'])]
public function show_ordremission_g($id): Response
{
    $em = $this->getDoctrine()->getManager();
    $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande'=>$id , 'type_demande'=>'ordre de mission' ));

    return $this->render('histo_demandes/show_histo_demande.html.twig', [
        'histo_demandes' => $histo_demandes,
    ]);
} 

#[Route('/histo_demandes_{id}_show_ficheheure_g', name: 'app_histo_demandes_show_ficheheure_g', methods: ['GET', 'POST'])]
public function show_ficheheure_g($id): Response
{
    $em = $this->getDoctrine()->getManager();
    $histo_demandes=$em->getRepository(HistoDemandes::class)->findBy(array('id_demande'=>$id , 'type_demande'=>'fiche heure' ));

    return $this->render('histo_demandes/show_histo_demande.html.twig', [
        'histo_demandes' => $histo_demandes,
    ]);
} 

/////////////////

















    #[Route('/histo_demandes_{id}_edit', name: 'app_histo_demandes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HistoDemandes $histoDemande, HistoDemandesRepository $histoDemandesRepository): Response
    {
        $form = $this->createForm(HistoDemandesType::class, $histoDemande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $histoDemandesRepository->save($histoDemande, true);

            return $this->redirectToRoute('app_histo_demandes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('histo_demandes/edit.html.twig', [
            'histo_demande' => $histoDemande,
            'form' => $form,
        ]);
    }

    #[Route('/histo_demandes_{id}', name: 'app_histo_demandes_delete', methods: ['POST','GET'])]
    public function delete(Request $request, HistoDemandes $histoDemande, HistoDemandesRepository $histoDemandesRepository ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$histoDemande->getId())) {
            $histoDemandesRepository->remove($histoDemande, true);
        }

        return $this->redirectToRoute('app_histo_demandes_index', [], Response::HTTP_SEE_OTHER);
    }

/**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DIR') ")
     */
    #[Route('/histo_demandes_gen_var/{type}/{entite}/{code}', name: 'app_histo_demandes_gen_var', methods: ['POST','GET'])]
    public function histo_generale_var(HistoDemandesRepository $histoDemandesRepository , secure $security , $type , $entite, $code): Response
    {


      $result =  $histoDemandesRepository->get_dem_current($type,$entite,$code) ;

      $em = $this->getDoctrine()->getManager();

        if($entite == 'DIR'){
            $desc_entite = 'Le Directeur';
        }
        if($entite == 'SG'){
            $desc_entite = 'Le Secrétaire Générale';
        }

        if($entite == 'RH'){
            $desc_entite = $em->getRepository(Service::class)->find(14)->getNomService();
        }
        if($entite == 'SER'){
            $desc_entite = $em->getRepository(Service::class)->find($code)->getNomService();;
        }
        if($entite == 'DEP'){
            $desc_entite = $em->getRepository(Departement::class)->find($code)->getLibelleDep() . " - " . $em->getRepository(Departement::class)->find($code)->getAbrevDep();
           // dd($desc_entite);
        }
        if($entite == 'STR'){
            $desc_entite = $em->getRepository(StructRech::class)->find($code)->getLibelleStructure() . " - " . $em->getRepository(StructRech::class)->find($code)->getAbrevStructure();
        } 
        if($entite == 'FIL'){
            $desc_entite = $em->getRepository(Filiere::class)->find($code)->getNomFiliere() . " - " . $em->getRepository(Filiere::class)->find($code)->getCodeApo();
        }

        if($entite == 'DIRADJ' && $code == 1){
            $desc_entite = 'Directeur Adjoint (Affaires Pédagogiques)';
        }
        if($entite == 'DIRADJ' && $code == 2){
            $desc_entite = 'Directeur Adjoint (Recherche Scientifique & Coopération)';
        }


        if($type == 'AUTO'){
            return $this->render('histo_demandes/auto_trait.html.twig', [
                'result' =>$result,
                'desc_entite' =>$desc_entite
            ]);

        }

        if($type == 'CONGE'){
            return $this->render('histo_demandes/conge_trait.html.twig', [
                'result' =>$result,
                'desc_entite' =>$desc_entite
            ]);

        }

        if($type == 'OM'){
            return $this->render('histo_demandes/om_trait.html.twig', [
                'result' =>$result,
                'desc_entite' =>$desc_entite
            ]);

        }


        if($type == 'FH'){
            return $this->render('histo_demandes/fh_trait.html.twig', [
                'result' =>$result,
                'desc_entite' =>$desc_entite
            ]);

        }


       
      return 0;
 


    }






}
