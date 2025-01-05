<?php

namespace App\Controller;

use App\Entity\Attestation;
use App\Entity\Personnel;
use App\Entity\Utilisateurs;
use App\Entity\HistoDemandes;
use App\Form\AttestationType;
use App\Form\AttestationEditType;
use App\Repository\AttestationRepository;
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

class AttestationController extends AbstractController
{
     /** 
     *
     * @Security("is_granted('ROLE_RH')")
     */
    #[Route('/attestation', name: 'app_attestation_index', methods: ['GET','POST'])]
    public function index(AttestationRepository $attestationRepository): Response
    {
        return $this->render('attestation/table-datatable-attestation.html.twig', [
            'attestations' => $attestationRepository->findBy(['statut'=>"-1"]),
        ]);
    }

    #[Route('/attestation_bloque_{id}', name: 'app_attestation_bloque', methods: ['GET','POST'])]
    public function bloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Attestation::class)->bloque_attestation($id);
        return new JsonResponse('1');

    }

    #[Route('/attestation_debloque_{id}', name: 'app_attestation_debloque', methods: ['GET','POST'])]
    public function debloque(Request $request , $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Attestation::class)->debloque_attestation($id);
        return new JsonResponse('0');
    }

    /** 
     *
     * @Security("is_granted('ROLE_RH')")
     */
    #[Route('/attestationVAL_{id}', name: 'attestationVAL', methods: ['GET','POST'])]
    public function validation(Request $request  ,secure $security , HistoDemandesRepository $HistoDemandesRepository , Attestation $attestation, AttestationRepository $attestationRepository , $id , MailerInterface $mailer  , InternetTest $int): Response
    {
        $searchParam = $request->get('searchParam');
        $attestation->setMotifs($searchParam['motif']);
        $attestation->setStatut($searchParam['statut']);

        $HistoDemandes = new HistoDemandes();
        $HistoDemandes->setTypeDemande('attestation') ; 
      
        $HistoDemandes->setDateValidation(new \DateTime()) ;
        $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
        $HistoDemandes->setStatut($attestation->getStatut()) ;
        $HistoDemandes->setNiveau("1") ;
        $HistoDemandes->setDemandeur($attestation->getPersonnel()) ;
        $HistoDemandes->setDateEnvoie($attestation->getDateEnvoie()) ;
        $HistoDemandes->setIdDemande($attestation->getId()) ; // 

        $HistoDemandesRepository->save($HistoDemandes, true);

        
        $attestationRepository->save($attestation, true);

      

   $subject = "Demande d'Attestation";
   $html = $this->renderView('attestation/email-attestation.html.twig',['attestation'  => $attestation]); 
   $email = (new TemplatedEmail())
   ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
   ->to($attestation->getPersonnel()->getIdUser()->getEmail())
   ->subject($subject)
   ->html($html)
   ;
   try {
       if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
   } catch (TransportExceptionInterface $e) {
   } 

   
        return new RedirectResponse($this->generateUrl('app_attestation_index'));
    }


    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     */
    #[Route('/attestation_new', name: 'app_attestation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, secure $security, AttestationRepository $attestationRepository , HistoDemandesRepository $HistoDemandesRepository, MailerInterface $mailer , InternetTest $int): Response
    {
        $attestation = new Attestation();
        $destinataire = null;
        $form = $this->createForm(AttestationType::class, $attestation);
        $form->handleRequest($request);

        $attestation->setPersonnel($security->getUser()->getPersonnel());
      
        if ($form->isSubmitted() && $form->isValid()) {
            $attestation->setDateEnvoie(new \DateTime());
            $attestation->setStatut("-1");
            $attestation->setBloque(0);

            $em = $this->getDoctrine()->getManager();
            $destinataire =$em->getRepository(Utilisateurs::class)->findByRole('ROLE_RH') ;
            $subject = "Traitement d'Attestation";
            $html = $this->renderView('attestation/email-attestation-notif.html.twig',['attestation'  => $attestation ,'destinataire' => $destinataire]); 
            $email = (new TemplatedEmail())
            ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
            ->to($destinataire['email'])
            ->subject($subject)
            ->html($html)
            ;


          try {
                   if($int->pingGmail() == 'alive'){
                       $mailer->send($email);
                    }
                
                } catch (TransportExceptionInterface $e) {
                } 


            $attestationRepository->save($attestation, true);

            $HistoDemandes = new HistoDemandes();
            $HistoDemandes->setTypeDemande('attestation') ; 
            $HistoDemandes->setValidateur($security->getUser()->getPersonnel()) ;
            $HistoDemandes->setStatut($attestation->getStatut()) ;
            $HistoDemandes->setNiveau("ROLE_RH") ;
            $HistoDemandes->setDemandeur($attestation->getPersonnel()) ;
            $HistoDemandes->setDateEnvoie($attestation->getDateEnvoie()) ;
            $HistoDemandes->setIdDemande($attestation->getId()) ;
    
            $HistoDemandesRepository->save($HistoDemandes, true);



            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

            return $this->redirectToRoute('app_attestation_new', [], Response::HTTP_SEE_OTHER);
        }
     
        return $this->renderForm('attestation/new-attestation.html.twig', [
            'attestation' => $attestation,
            'form' => $form,
            'attestations' => $attestationRepository->findBy(['personnel' => $security->getUser()->getPersonnel()->getId()]),
        ]);
    }

    /**
     *
     * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FONC')")
     */
    #[Route('/attestation_{id}_edit', name: 'app_attestation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Attestation $attestation, AttestationRepository $attestationRepository , secure $security): Response
    {

        if($attestation->getStatut()=="-1"  && $attestation->getPersonnel()->getId() == $security->getUser()->getPersonnel()->getId()){

            $em = $this->getDoctrine()->getManager();
            $form = $this->createForm(AttestationEditType::class, $attestation);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                
                if(!$attestation->isBloque()){
                    $attestationRepository->save($attestation, true);
                    $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
                }else{
                    $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER");
                }

            }
    
            $attestations=$em->getRepository(Attestation::class)->findBy(["personnel"=>$security->getUser()->getPersonnel()->getId()]);
            return $this->renderForm('attestation/edit-attestation.html.twig', [
                'attestation' => $attestation,
                'attestations' => $attestations,
                'form' => $form,
                'id' => $attestation->getId(), 
            ]);

        }else{
            return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
        }

         
    }
    /**
     *
     * @Security("is_granted('ROLE_FONC')")
     */
    #[Route('/attestation_{id}_{_token}', name: 'app_attestation_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Attestation $attestation, AttestationRepository $attestationRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$attestation->getId(), $_token)) {

            $em = $this->getDoctrine()->getManager();
            $histo_auto = $em->getRepository(HistoDemandes::class)->findBy(['id_demande' => strval($attestation->getId()),"type_demande"=>"attestation"]);
            foreach ($histo_auto as $auto) {$em->remove($auto);}
            $em->flush();

            $attestationRepository->remove($attestation, true);
        }
        $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
        return $this->redirectToRoute('app_attestation_new', [], Response::HTTP_SEE_OTHER); 
    }
}
