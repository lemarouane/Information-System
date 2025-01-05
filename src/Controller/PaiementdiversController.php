<?php

namespace App\Controller;

use App\Entity\Paiementdivers;
use App\Form\PaiementdiversType;
use App\Repository\PaiementdiversRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Security as secure;
use App\Twig\ConfigExtension;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use NumberToWords\NumberToWords; 


class PaiementdiversController extends AbstractController
{
    /**
     * @Route("/paiementdivers", name="paiementdivers")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function index(PaiementdiversRepository $paiementdiversRepository): Response
    {
        return $this->render('paiementdivers/index.html.twig', [
            'paiementdivers' => $paiementdiversRepository->findAll(),
        ]);
    }

     /**
     * @Route("/paiementdivers_new", name="paiementdivers_new")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function new(Request $request, PaiementdiversRepository $paiementdiversRepository , Pdf $knpSnappyPdf ): Response
    {
        $paiementdiver = new Paiementdivers();
        $annee= Date('Y');
        $paiementdiver->setAnnee($annee);
        $form = $this->createForm(PaiementdiversType::class, $paiementdiver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            if(  $form['rubrique']->getData()==NULL || $form['montant']->getData()==NULL  || $form['dateOperation']->getData()==NULL || $form['rp']->getData()==NULL   ){
 
                $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER_INCOMPLETE");

                return $this->renderForm('paiementdivers/new.html.twig', [
                    'entity' => $paiementdiver,
                    'form' => $form,
                ]);

            }

            $is_montant =  $paiementdiver->getMontant() * 0.2 ;
            $montant_moins_is =  ($paiementdiver->getMontant() -  $is_montant);
            $paiementdiver->setMontantmoinsis($montant_moins_is) ;

            $paiementdiversRepository->save($paiementdiver, true);

            $numberToWords = new NumberToWords();
 
            // build a new number transformer using the RFC 3066 language identifier
            $numberTransformer = $numberToWords->getNumberTransformer('fr');

            $montant_whole = floor($paiementdiver->getMontant());
            $montant_fraction =  round( ($paiementdiver->getMontant() - $montant_whole) *100  , 2);

        

            $html = $this->renderView('paiementdivers/ordre_recette.html.twig', [
                'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
                'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
                'montant_f_num' =>  $montant_fraction ,
                'montant_num' => number_format($paiementdiver->getMontant(), 2, ',', '.') ,
                'num' => $paiementdiver->getRp(),
                'paiement' => $paiementdiver,
                'is_montant' => number_format($is_montant, 2, ',', '.') ,
                'montantmoinsis' =>number_format($montant_moins_is, 2, ',', '.') 

            ]);
 
            $filename = 'default.pdf'; 

            $dir = $this->getParameter('webroot_doc').'/Ordre_recette_divers/'.$paiementdiver->getAnnee().'/';
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }

             if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiementdiver->setLien($filename);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiementdivers::class)->save($paiementdiver, true);
   

          }



            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

            return $this->redirectToRoute('paiementdivers', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paiementdivers/new.html.twig', [
            'entity' => $paiementdiver,
            'form' => $form,
        ]);
    }

     /**
     * @Route("/paiementdivers_show_{id}", name="paiementdivers_show")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function show(Paiementdivers $paiementdiver): Response
    {
        return $this->render('paiementdivers/show.html.twig', [
            'paiementdiver' => $paiementdiver,
        ]);
    }

     /**
     * @Route("/paiementdivers_edit_{id}", name="paiementdivers_edit")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function edit(Request $request, Paiementdivers $paiementdiver, PaiementdiversRepository $paiementdiversRepository , Pdf $knpSnappyPdf): Response
    {
      
        $form = $this->createForm(PaiementdiversType::class, $paiementdiver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(  $form['rubrique']->getData()==NULL || $form['montant']->getData()==NULL  || $form['dateOperation']->getData()==NULL || $form['rp']->getData()==NULL   ){
 
                $this->get('session')->getFlashBag()->add('danger', "MOD_DANGER_INCOMPLETE");

                return $this->renderForm('paiementdivers/edit.html.twig', [
                    'paiementdiver' => $paiementdiver,
                    'form' => $form,
                ]);

            }

            $is_montant =  $paiementdiver->getMontant() * 0.2 ;
            $montant_moins_is =  ($paiementdiver->getMontant() -  $is_montant);
            $paiementdiver->setMontantmoinsis($montant_moins_is) ;

            $paiementdiversRepository->save($paiementdiver, true);

            $numberToWords = new NumberToWords();
            // build a new number transformer using the RFC 3066 language identifier
            $numberTransformer = $numberToWords->getNumberTransformer('fr');
 
            $montant_whole = floor($paiementdiver->getMontant());
            $montant_fraction =  round( ($paiementdiver->getMontant() - $montant_whole)  * 100 ,2) ;
 
       

            $html = $this->renderView('paiementdivers/ordre_recette.html.twig', [
                'montant_w' => $numberTransformer->toWords(intval($montant_whole)),
                'montant_f' => $numberTransformer->toWords(intval($montant_fraction)),
                'montant_f_num' =>  $montant_fraction ,
                'montant_num' => number_format($paiementdiver->getMontant(), 2, ',', '.') ,
                'num' => $paiementdiver->getRp(),
                'paiement' => $paiementdiver,
                'is_montant' => number_format($is_montant, 2, ',', '.') ,
                'montantmoinsis' =>number_format($montant_moins_is, 2, ',', '.') 

            ]);
 
 
            $dir = $this->getParameter('webroot_doc').'/Ordre_recette_divers/'.$paiementdiver->getAnnee().'/';
            if (!file_exists($dir)) {
              mkdir($dir, 0777, true);
            }
 
            $filename =  $paiementdiver->getLien();
 
             if (!file_exists($dir.$filename)) {
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiementdiver->setLien($filename);
 
            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiementdivers::class)->save($paiementdiver, true);
      
  
          }else{
 
            unlink($dir.$filename);
            $filename = sha1(uniqid(mt_rand(), true)).'.pdf';
            $knpSnappyPdf->generateFromHtml($html,$dir.$filename);
            $paiementdiver->setLien($filename);
 
            $em = $this->getDoctrine()->getManager();
            $em->getRepository(Paiementdivers::class)->save($paiementdiver, true);
 
          }
         
           $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            return $this->redirectToRoute('paiementdivers', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paiementdivers/edit.html.twig', [
            'paiementdiver' => $paiementdiver,
            'form' => $form,
        ]);
    }

     /**
     * @Route("/paiementdivers_delete_{id}", name="paiementdivers_delete")
     * @Security("is_granted('ROLE_FC_PAI') or is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function delete(Request $request, Paiementdivers $paiementdiver, PaiementdiversRepository $paiementdiversRepository , $id,): Response
    {
        $em = $this->getDoctrine()->getManager();
        $paiement = $em->getRepository(Paiementdivers::class)->find($id);
        if(!empty($paiementdiver)){

            $param= new ConfigExtension($em);
     

         $dir = $this->getParameter('webroot_doc').'/Ordre_recette_divers/'.$paiementdiver->GetAnnee().'/';

         if (file_exists($dir.$paiementdiver->getLien())) {
     
            unlink($dir.$paiementdiver->getLien());

          }

	        $em->remove($paiementdiver);
	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('paiementdivers'));
	    
        }else{
	    	return new Response('1');
	    }
    }
}
