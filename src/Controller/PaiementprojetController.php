<?php

namespace App\Controller;

use App\Repository\PaiementprojetRepository;
use App\Entity\Config;
use App\Entity\Personnel;
use App\Entity\Etudiant\Etudiants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Form\PaiementprojetType;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Security as secure;
use App\Entity\Paiementprojet;
use App\Entity\FiliereFcResponsable;

use App\Twig\ConfigExtension;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use NumberToWords\NumberToWords; 

use App\Entity\ProgrammeEmploiProjet;
use App\Repository\ProgrammeEmploiProjetRepository;



class PaiementprojetController extends AbstractController
{
    /**
     * @Route("/paiementprojet", name="paiementprojet")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function index(PaiementprojetRepository $paiementprojetRepository): Response
    {
        $em= $this->getDoctrine()->getManager();
        $pai=$em->getRepository(Paiementprojet::class)->ldBY();
        return $this->render('paiementprojet/index.html.twig',['paiementprojets' => $pai]);
    }
 
    /**
     * @Route("/paiementprojet_new_{id}", name="addPaiementprojet")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function new(Request $request, PaiementprojetRepository $paiementprojetRepository , Pdf $knpSnappyPdf ,  $id): Response 
    {
        $paiementprojet = new Paiementprojet();

        $em = $this->getDoctrine()->getManager();
        $resp  = $em->getRepository(Personnel::class)->findOneBy(array('id'=>$id )); 
        $annee= Date('Y');
       // $paiementprojet->setAnnee($annee);
        $paiementprojet->setType('PROJET');

        $form = $this->createForm(PaiementprojetType::class, $paiementprojet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if(  $form['rubrique']->getData()==NULL || $form['montant']->getData()==NULL  || $form['dateOperation']->getData()==NULL || $form['numRP']->getData()==NULL   ){
 
                $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER_INCOMPLETE");

                return $this->renderForm('paiementprojet/new.html.twig', [
                    'paiementprojet' => $paiementprojet, 
                    'form' => $form,
                    'entity'=> $resp,
                ]);

            }


            $paiementprojet->setResponsable($resp) ;
            $paiementprojetRepository->save($paiementprojet, true);

            $numberToWords = new NumberToWords();
 
            // build a new number transformer using the RFC 3066 language identifier
            $numberTransformer = $numberToWords->getNumberTransformer('fr');

            $montant_whole = floor($paiementprojet->getMontant());
            $montant_fraction =  round( ($paiementprojet->getMontant() - $montant_whole) *100  , 2);

            $html = $this->renderView('paiementprojet/ordre_recette.html.twig', [
                'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
                'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
                'montant_f_num' =>  $montant_fraction ,
                'montant_num' => number_format($paiementprojet->getMontant(), 2, ',', '.') ,
                'num' => $paiementprojet->getNumRP(),
                'paiement' => $paiementprojet,

            ]);
 
            $filename = 'default.pdf'; 

            $dir = $this->getParameter('webroot_doc').'/Ordre_recette_projet/'.$paiementprojet->getResponsable()->GetNom().'_'.$paiementprojet->getResponsable()->GetPrenom().'/';
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }

             if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiementprojet->setLien($filename);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiementprojet::class)->save($paiementprojet, true);
   

          }


          $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
          return $this->redirectToRoute('addNewProgProj', array('id'=>$paiementprojet->getId()), Response::HTTP_SEE_OTHER);
          //  return $this->redirectToRoute('paiementprojet', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paiementprojet/new.html.twig', [
            'paiementprojet' => $paiementprojet, 
            'form' => $form,
            'entity'=> $resp,

        ]);
    }

    /**
     * @Route("/showPaiementprojet/{id}", name="showPaiementprojet")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function show($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $paiementprojet = $em->getRepository(Paiementprojet::class)->findBy(array('responsable' => $id));
        return $this->render('paiementprojet/show.html.twig', [
            'paiementprojet' => $paiementprojet,
        ]);
    }


    /**
     * @Route("/editPaiementprojet/{id}", name="editPaiementprojet")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function edit(Request $request, Paiementprojet $paiementprojet, PaiementprojetRepository $paiementprojetRepository): Response
    {
        $form = $this->createForm(PaiementprojetType::class, $paiementprojet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            if(  $form['rubrique']->getData()==NULL || $form['montant']->getData()==NULL  || $form['dateOperation']->getData()==NULL || $form['rp']->getData()==NULL   ){
 
                $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER_INCOMPLETE");

                return $this->renderForm('paiementprojet/edit.html.twig', [
                    'paiementprojet' => $paiementprojet,
                    'form' => $form,
        
                ]);

            }


            $paiementprojetRepository->save($paiementprojet, true);

            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            return $this->redirectToRoute('paiementprojet', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paiementprojet/edit.html.twig', [
            'paiementprojet' => $paiementprojet,
            'form' => $form,

        ]);
    }



/**
     * @Route("/updatePaiementprojet/{id}", name="updatePaiementprojet")
     *  @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_ADMIN')")
     */
    public function updateAction(Pdf $knpSnappyPdf ,Request $request, $id , ProgrammeEmploiProjetRepository $ProgrammeEmploiProjetRepository ) {
       
        $em = $this->getDoctrine()->getManager();
        $paiement = $em->getRepository(Paiementprojet::class)->find($id);
        $form = $this->createForm(PaiementprojetType::class, $paiement);
        $form->handleRequest($request);


        $prgram_projet = $em->getRepository(ProgrammeEmploiProjet::class)->findOneBy(array('numpaiementprojet' => $id));
    
        if( $prgram_projet!=NULL ){
            if( $prgram_projet->isActiver()){

                $this->get('session')->getFlashBag()->add('danger', "MOD_PE_DESAC");
                return $this->redirectToRoute('paiementprojet', [], Response::HTTP_SEE_OTHER);
            }
    

        }


        if ($form->isValid()) {

            $em->persist($paiement);
            $em->flush();
           $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
           $numberToWords = new NumberToWords();
           // build a new number transformer using the RFC 3066 language identifier
           $numberTransformer = $numberToWords->getNumberTransformer('fr');

           $montant_whole = floor($paiement->getMontant());
           $montant_fraction =  round( ($paiement->getMontant() - $montant_whole)  * 100 ,2) ;

           $html = $this->renderView('paiementprojet/ordre_recette.html.twig', [
               'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
               'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
               'montant_f_num' =>  $montant_fraction ,
               'montant_num' => number_format($paiement->getMontant(), 2, ',', '.') ,
               'num' => $paiement->getNumRP(),
               'paiement' => $paiement,
           ]);


           $dir = $this->getParameter('webroot_doc').'/Ordre_recette_projet/'.$paiement->getResponsable()->GetNom().'_'.$paiement->getResponsable()->GetPrenom().'/';
           if (!file_exists($dir)) {
             mkdir($dir, 0777, true);
           }

           $filename =  $paiement->getLien();

            if (!file_exists($dir.$filename)) {
           $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
           $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
           $paiement->setLien($filename);

           $em = $this->getDoctrine()->getManager();
           $em->getRepository(Paiementprojet::class)->save($paiement, true);
     
 
         }else{

           unlink($dir.$filename);
           $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
           $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
           $paiement->setLien($filename);

           $em = $this->getDoctrine()->getManager();
           $em->getRepository(Paiementprojet::class)->save($paiement, true);

         }
        
                  

       }

       $prgram_projet = $em->getRepository(ProgrammeEmploiProjet::class)->findOneBy(array('numpaiementprojet' => $id));

    if( $prgram_projet!=NULL ){

       $prgram_projet->setMontant($paiement->getMontant());
       $prgram_projet->setIntitule($paiement->getIntitule());
       $prgram_projet->setPersonne($paiement->getResponsable()) ;
       $prgram_projet->setAnnee($paiement->getAnnee()) ;
       $ProgrammeEmploiProjetRepository->save($prgram_projet, true);

               }
       return $this->redirect($this->generateUrl('showPaiementprojet',array('id'=>$paiement->getResponsable()->getId())));  
    
    }



     /**
     * @Route("/paiementprojet_{id}_delete", name="remove_Paiementprojet")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function delete(Request $request, Paiementprojet $paiement , $id): Response
    {
       
        $em = $this->getDoctrine()->getManager();
        $paiement = $em->getRepository(Paiementprojet::class)->find($id);
        if(!empty($paiement)){

         $param= new ConfigExtension($em);
  
         $dir = $this->getParameter('webroot_doc').'/Ordre_recette_projet/'.$paiement->getResponsable()->GetNom().'_'.$paiement->getResponsable()->GetPrenom().'/';

         if (file_exists($dir.$paiement->getLien())) {
     
            unlink($dir.$paiement->getLien());

          }

	        $em->remove($paiement);
	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('paiementprojet'));
	    
        }else{
	    	return new Response('1');
	    }



    }


 /**
     * @Route("/list_prof_projet", name="list_prof_projet")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_FINANCE') or is_granted('ROLE_FC_PAI')")
     */
    public function list_prof_projet(Request $request)
    {
    	
		$em = $this->getDoctrine()->getManager();
		$liste_profs = $em->getRepository(Personnel::class)->findBy(array('typePersonnelId'=>array(1,3) )); 

		return $this->render('paiementprojet/liste_prof.html.twig', array('liste_profs' => $liste_profs));

	    
    } 

}
