<?php

namespace App\Controller;

use App\Entity\NoteFonctionnaire;
use App\Entity\Personnel;
use App\Entity\EchelonAv;
use App\Entity\GradeAv;
use App\Entity\Grade;
use App\Entity\Echelon;
use App\Entity\Avancement;
use App\Form\NoteFonctionnaireType;
use App\Repository\NoteFonctionnaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_RH') or is_granted('ROLE_SUPER_ADMIN') ")
     */
class NoteFonctionnaireController extends AbstractController
{
   

    #[Route('/note_fonctionnaire_{id}_new', name: 'app_note_fonctionnaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Personnel $personnel , Pdf $knpSnappyPdf ,NoteFonctionnaireRepository $noteFonctionnaireRepository ,$id): Response
    {
        $noteFonctionnaire = new NoteFonctionnaire();

        $personnel->setId($id) ;   ////
        $noteFonctionnaire->setPersonnel($personnel) ;//// ;
        $notes = $noteFonctionnaireRepository->findBy(['personnel'=>$personnel]);

        $form = $this->createForm(NoteFonctionnaireType::class, $noteFonctionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           

            $em = $this->getDoctrine()->getManager();
            $avancement = $em->getRepository(Avancement::class)->findBy(array('personnel' => $noteFonctionnaire->getPersonnel()->getId()),array('dateDeci' => 'DESC'),1);
            $nbannee = 1;
            $limite=1;
            if($avancement){
                if(intval($noteFonctionnaire->getAnnee()) - intval($avancement[0]->getDateDeci()->format('Y'))>0){
                    $limite=$avancement[0]->getEchelon()->getNbAnnee();
                }
                else{
                    $limite=1;
                }
            }else{
                $nbannee = 1;
            }
            $noteHistorique = $em->getRepository(NoteFonctionnaire::class)->findBy(['personnel' => $noteFonctionnaire->getPersonnel()],['annee' => 'DESC'],$limite); 
            $noteHistorique1 = $em->getRepository(NoteFonctionnaire::class)->findBy(['personnel' => $noteFonctionnaire->getPersonnel()],['annee' => 'DESC'],1); ///
    
            if(count($noteHistorique)>1){
                if($limite  == $nbannee ){
    
                    $html = $this->renderView('document/fiche-note.html.twig', [
                        'note' => $noteFonctionnaire,
                        'noteHistorique' => $noteHistorique,
                    ]);
                }else{
                    $html = $this->renderView('document/fiche-note.html.twig', [
                        'note' => $noteFonctionnaire,
                        'noteHistorique' => $noteHistorique1,
                    ]);
                }
            }else{
    
                $html = $this->renderView('document/fiche-note.html.twig', [
                    'note' => $noteFonctionnaire,
                    'noteHistorique' => $noteHistorique1,
                ]);
            }
     
       
            $filename = 'Note N.'.$noteFonctionnaire->GetId().' '. $noteFonctionnaire->getPersonnel()->GetNom() ." ".$noteFonctionnaire->getPersonnel()->GetPrenom().'.pdf';

            $noteFonctionnaire->setLien($filename);
            $noteFonctionnaireRepository->save($noteFonctionnaire, true);

            $dir = $this->getParameter('webroot_doc'). $noteFonctionnaire->getPersonnel()->GetNom().'_'.$noteFonctionnaire->getPersonnel()->GetPrenom().'/Note_Fonctionnaire/' ;
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }
    
            if (!file_exists($dir.$filename)) {
              $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            }else{
              unlink($dir.$filename);
              $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            }
      
        

            return $this->redirectToRoute('app_note_fonctionnaire_new', ["id"=>$id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('note_fonctionnaire/new-avancement.html.twig', [
            'note_fonctionnaire' => $noteFonctionnaire,
            'notes'=>$notes ,
            'form' => $form,
        ]);
    }

 

    #[Route('/note_fonctionnaire_{id}_edit', name: 'app_note_fonctionnaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NoteFonctionnaire $noteFonctionnaire, NoteFonctionnaireRepository $noteFonctionnaireRepository  ,$id, Pdf $knpSnappyPdf): Response
    {
        $form = $this->createForm(NoteFonctionnaireType::class, $noteFonctionnaire);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $avancement = $em->getRepository(Avancement::class)->findBy(array('personnel' => $noteFonctionnaire->getPersonnel()->getId()),array('dateDeci' => 'DESC'),1);
            $nbannee = 1;
            $limite=1;
            if($avancement){
                if(intval($noteFonctionnaire->getAnnee()) - intval($avancement[0]->getDateDeci()->format('Y'))>0){
                    $limite=$avancement[0]->getEchelon()->getNbAnnee();
                }
                else{
                    $limite=1;
                }
            }else{
                $nbannee = 1;
            }
            $noteHistorique = $em->getRepository(NoteFonctionnaire::class)->findBy(['personnel' => $noteFonctionnaire->getPersonnel()],['annee' => 'DESC'],$limite); 
            $noteHistorique1 = $em->getRepository(NoteFonctionnaire::class)->findBy(['personnel' => $noteFonctionnaire->getPersonnel()],['annee' => 'DESC'],1); ///
    
            if(count($noteHistorique)>1){
                if($limite  == $nbannee ){
    
                    $html = $this->renderView('document/fiche-note.html.twig', [
                        'note' => $noteFonctionnaire,
                        'noteHistorique' => $noteHistorique,
                    ]);
                }else{
                    $html = $this->renderView('document/fiche-note.html.twig', [
                        'note' => $noteFonctionnaire,
                        'noteHistorique' => $noteHistorique1,
                    ]);
                }
            }else{
    
                $html = $this->renderView('document/fiche-note.html.twig', [
                    'note' => $noteFonctionnaire,
                    'noteHistorique' => $noteHistorique1,
                ]);
            }
            $filename = 'Note N.'.$noteFonctionnaire->GetId().' '. $noteFonctionnaire->getPersonnel()->GetNom() ." ".$noteFonctionnaire->getPersonnel()->GetPrenom().'.pdf';

            $noteFonctionnaire->setLien($filename);
            $noteFonctionnaireRepository->save($noteFonctionnaire, true);

            $dir = $this->getParameter('webroot_doc'). $noteFonctionnaire->getPersonnel()->GetNom().'_'.$noteFonctionnaire->getPersonnel()->GetPrenom().'/Note_Fonctionnaire/' ;
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }
    
            if (!file_exists($dir.$filename)) {
              $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            }else{
              unlink($dir.$filename);
              $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            }
            return $this->redirectToRoute('app_note_fonctionnaire_new', ["id"=>$noteFonctionnaire->getPersonnel()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('note_fonctionnaire/edit-note-fonctionnaire.html.twig', [
            'note_fonctionnaire' => $noteFonctionnaire,
            'form' => $form,
        ]);
    }

    #[Route('/note_fonctionnaire_{id}_{_token}', name: 'app_note_fonctionnaire_delete', methods: ['GET','POST'])]
    public function delete(Request $request, NoteFonctionnaire $noteFonctionnaire, NoteFonctionnaireRepository $noteFonctionnaireRepository , $_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$noteFonctionnaire->getId(), $_token)) {
            $noteFonctionnaireRepository->remove($noteFonctionnaire, true);
        }
        return $this->redirectToRoute('app_note_fonctionnaire_new', ["id"=>$noteFonctionnaire->getPersonnel()->getId()], Response::HTTP_SEE_OTHER);

    }


     #[Route('/ficheNote_{id}', name: 'ficheNotePdf', methods: ['GET', 'POST'])]
    public function fichenote_pdf(Pdf $knpSnappyPdf , NoteFonctionnaire $note )
    {    
      
        $em = $this->getDoctrine()->getManager();
        $avancement = $em->getRepository(Avancement::class)->findBy(array('personnel' => $note->getPersonnel()->getId()),array('dateDeci' => 'DESC'),1);
        
        $nbannee = 1;
        $limite=1;
        if($avancement){
            if(intval($note->getAnnee()) - intval($avancement[0]->getDateDeci()->format('Y'))>0){
               $limite=$avancement[0]->getEchelon()->getNbAnnee();
            }
            else{
                $limite=1;
            }
        }else{
            $nbannee = 1;
        }
  
        $noteHistorique = $em->getRepository(NoteFonctionnaire::class)->findBy(['personnel' => $note->getPersonnel()],['annee' => 'DESC'],$limite); 
        $noteHistorique1 = $em->getRepository(NoteFonctionnaire::class)->findBy(['personnel' => $note->getPersonnel()],['annee' => 'DESC'],1); ///

        if(count($noteHistorique)>1){
            if($limite  == $nbannee){

                $html = $this->renderView('document/fiche-note.html.twig', [
                    'note' => $note,
                    'noteHistorique' => $noteHistorique,
                ]);
            }else{
                $html = $this->renderView('document/fiche-note.html.twig', [
                    'note' => $note,
                    'noteHistorique' => $noteHistorique1,
                ]);
            }
        }else{

            $html = $this->renderView('document/fiche-note.html.twig', [
                'note' => $note,
                'noteHistorique' => $noteHistorique1,
            ]);
        }


        $filename = 'Note '.$note->getAnnee().' '.$note->GetId().' '. $note->getPersonnel()->GetNom() .' '.$note->getPersonnel()->GetPrenom().'.pdf';
      

        $dir = $this->getParameter('webroot_doc'). $note->getPersonnel()->GetNom().'_'.$note->getPersonnel()->GetPrenom().'/Note_Fonctionnaire/' ;
        if (!file_exists($dir)) {
          mkdir($dir, 0777, true);
        }

        if (!file_exists($dir.$filename)) {
          $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
        }else{
          unlink($dir.$filename);
          $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
        }
  
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            $filename ,
        );
    }
    

  


}
