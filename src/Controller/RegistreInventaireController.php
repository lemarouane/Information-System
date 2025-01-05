<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\RegistreInventaire;
use App\Entity\Affectation;
use App\Entity\Reception;
use App\Entity\Decharge;
use App\Form\RegistreInventaireType;
use App\Form\DechargeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;

class RegistreInventaireController extends AbstractController
{


    /**
     * @Route("/registreinventaires", name="registreinventaires")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $registreinventaires = $em->getRepository(RegistreInventaire::class)->findAll();
        $entity=new Decharge();
        $form = $this->createForm(DechargeType::class, $entity);
       $form->handleRequest($request);
         
       return $this->render('registre_inventaire/liste.html.twig', array('registreinventaires' => $registreinventaires,'entity' => $entity, 'form' => $form->createView()));

        
    }
    /**
     *  @Route("/showInventaire/{id}", name="showInventaire")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(RegistreInventaire::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('registre_inventaire/show.html.twig', array('inventaire' => $entity));
    }

    /**
     * @Route("/editInventaire/{id}", name="editInventaire")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(RegistreInventaire $entity)
    {
        $form = $this->createForm(RegistreInventaireType::class, $entity);
        return $this->render('registre_inventaire/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateInventaire/{id}", name="updateInventaire")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, RegistreInventaire $registreinventaire) {
        $em = $this->getDoctrine()->getManager('default');
        $form = $this->createForm(RegistreInventaireType::class, $registreinventaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($registreinventaire->getPersonnel()!=NULL){
                $registreinventaire->setAffecterA($registreinventaire->getPersonnel()->getNom().' '.$registreinventaire->getPersonnel()->getPrenom());
                $affectation = new Affectation();
                $affectation->setArticle($registreinventaire->getArticle());
                $affectation->setPersonnel($registreinventaire->getPersonnel());
                $affectation->setNumInventaire($registreinventaire->getNumInventaire());
                $affectation->setLocal($registreinventaire->getLocal());
                $affectation->setDateDebut(new \DateTime());
                $affectation->setInventaire($registreinventaire);
                $em->persist($affectation);
             }
             else{
                $registreinventaire->setAffecterA('-');
                $registreinventaire->setLocal('-');
                }
                foreach($registreinventaire->getAffectations() as $affectation){
                    if($affectation->getDateFin()==NULL)
                    $affectation->setDateFin(new \DateTime());
                    $em->persist($affectation);
             }
             $em->persist($registreinventaire);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirect($this->generateUrl('showInventaire', array('id' => $registreinventaire->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('registre_inventaire/edit.html.twig', array('entity' => $registreinventaire, 'form' => $form->createView()));
    }


    /**
     * @Route("/addInventaire", name="addInventaire")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new RegistreInventaire();
        $form = $this->createForm(RegistreInventaireType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
           return $this->render('registre_inventaire/show.html.twig', array('registreinventaire' => $entity));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('registre_inventaire/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('registre_inventaire/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_Inventaire/{id}", name="remove_Inventaire")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,RegistreInventaire $registreinventaire)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($registreinventaire)){

	        $registreinventaire = $em->getRepository(RegistreInventaire::class)->find($id);
	        $em->remove($registreinventaire);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('registreinventaires'));
	    }else{
	    	return new Response('1');
	    }
    }

 


    #[Route('/etatInventaire', name: 'etatInventaire', methods: ['GET', 'POST'])]
    public function attest_pdf(Pdf $knpSnappyPdf )
    {
      
        $em = $this->getDoctrine()->getManager();


        $inventaire = $em->getRepository(RegistreInventaire::class)->findAll();

        $html = $this->renderView('document/etatinventaire.html.twig', [
            'inventaires' => $inventaire,
        ]);

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'Etat INVENTAIRE'.date('Ymd').'.pdf' ,
        );
    }


   #[Route('/etatDecharge/{id}', name: 'etatDecharge', methods: ['GET', 'POST'])]
   public function decharge_pdf(Pdf $knpSnappyPdf , Decharge $decharge)
   {
     
       $em = $this->getDoctrine()->getManager();

       $html = $this->renderView('document/decharge.html.twig', [
           'decharge' => $decharge,
       ]);

       return new PdfResponse(
           $knpSnappyPdf->getOutputFromHtml($html),
           'Etat DECHARGE '.$decharge->getNumDecharge().'.pdf' ,
       );
   }

   #[Route('/etatDechargeBloc/{id}', name: 'etatDechargeBloc', methods: ['GET', 'POST'])]
   public function dechargeBloc_pdf(Pdf $knpSnappyPdf , Decharge $decharge)
   {
     
       $em = $this->getDoctrine()->getManager();
       $blocs =$em->getRepository(Decharge::class)->getBlocs($decharge);
       //dd($blocs);

       $html = $this->renderView('document/dechargebloc.html.twig', [
           'decharge' => $decharge,
           'blocs' => $blocs,
       ]);

       return new PdfResponse(
           $knpSnappyPdf->getOutputFromHtml($html),
           'Etat DECHARGE '.$decharge->getNumDecharge().' Bloc.pdf' ,
       );
   }

   #[Route('/etatDechargeInv/{id}', name: 'etatDechargeInv', methods: ['GET', 'POST'])]
   public function decharge_pdf2(Pdf $knpSnappyPdf , RegistreInventaire $Inventaire)
   {
     if ($Inventaire->getPersonnel() ){
       $em = $this->getDoctrine()->getManager();
       $decharge=new Decharge();
       $decharge->setPersonnel($Inventaire->getPersonnel());
       $decharge->addInventaire($Inventaire);
       $decharge->setNumDecharge($Inventaire->getNumDecharge());
       $decharge->setDateDecharge($Inventaire->getDateDecharge());
        $decharge->setExercice(date('Y'));

       $html = $this->renderView('document/decharge.html.twig', [
           'decharge' => $decharge,
       ]);

       return new PdfResponse(
           $knpSnappyPdf->getOutputFromHtml($html),
           'Etat DECHARGE '.$decharge->getNumDecharge().'.pdf' ,
       );
    }
    return $this->redirect($this->generateUrl('registreinventaires'));;
   }

   /**
     * @Route("/add_list_decharge", name="add_list_decharge")
     */
    public function add_list_dechargeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listes= explode(",",$request->query->get("liste"));
        $_SESSION['listes']=$listes; 
        $session=$this->get('session');
        $session->set('ok',true);
           return new JsonResponse($request->query->get("liste"));
}
/**
     * @Route("/newDecharge", name="newDecharge")
     *  @Security("is_granted('ROLE_FONC')")
     */

     public function newDecharge(Request $request)
     {
         $em = $this->getDoctrine()->getManager();
         $entity = new Decharge();
         $listes=$_SESSION['listes'];
         $session=$this->get('session');
        $session->set('ok',false);
         $registreinventaires = $em->getRepository(RegistreInventaire::class)->findAll();
         $form = $this->createForm(DechargeType::class, $entity);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
            
         $entity->setNumDecharge('D'.str_pad($em->getRepository(Decharge::class)->getNumDecharge()['nextval'], 6, '0', STR_PAD_LEFT));
         $em->getRepository(Decharge::class)->NextNumDecharge();
         $entity->setExercice(date('Y'));
         foreach ($listes as $code) {
            $inventaire = $em->getRepository(RegistreInventaire::class)->findOneBy(array('id'=> $code));
            if($inventaire){           
                $entity->addInventaire($inventaire);
            }
            $affectations = $em->getRepository(Affectation::class)->findBy(array('Inventaire' => $inventaire,'DateFin'=>Null));
            foreach ($affectations as $affectationOld){
                $affectationOld->setDateFin(new \DateTime());
                $em->persist($affectationOld); 
            }
            $inventaire->setNumDecharge($entity->getNumDecharge());
            $inventaire->setDateDecharge($entity->getDateDecharge());
            $inventaire->setLocal($entity->getLocal());
        $inventaire->setPersonnel($entity->getPersonnel());
        $inventaire->setAffecterA($inventaire->getPersonnel()->getNom().' '.$inventaire->getPersonnel()->getPrenom());
                $affectation = new Affectation();
                $affectation->setArticle($inventaire->getArticle());
                $affectation->setPersonnel($inventaire->getPersonnel());
                $affectation->setNumInventaire($inventaire->getNumInventaire());
                $affectation->setDateDebut($entity->getDateDecharge());
            $affectation->setLocal($entity->getLocal());
            $affectation->setDecharge($entity);
                $affectation->setInventaire($inventaire);
                $em->persist($affectation);
                $em->persist($inventaire);
           $em->flush();
        }
             $em->persist( $entity );
             $em->flush();
             $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
             return $this->redirectToRoute('showDecharge', array('id' => $entity->getId()));
         }
        if ($form->isSubmitted() && !$form->isValid()) {
         $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
         return $this->render('registre_inventaire/liste.html.twig', array('entity' => $entity, 'form' => $form->createView()));
         }
  
         return $this->render('registre_inventaire/liste.html.twig', array('registreinventaires'=>$registreinventaires,'entity' => $entity, 'form' => $form->createView()));
     }

     	/**
     * @Route("/invQR/{id}", name="invQR")
     */
    public function carteQRAction(Pdf $knpSnappyPdf ,$id)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(RegistreInventaire::class)->find($id);

		$writer = new PngWriter();
        $options = [
        	'orientation'   => 'Landscape',
        	'page-height'   => 50,
            'page-width'    => 25,
            'margin-top'    => 0,
    		'margin-right'  => 0,
    		'margin-bottom' => 0,
    		'margin-left'   => 0,
        ];
        // Create a basic QR code
		$qrCode = new QrCode($document->getNumInventaire());
        $numinventaire = $document->getNumInventaire();
		$qrCode->setSize(85);
		$qrCode->setBackgroundColor(new Color(245,245,245));

		//$dataUri = $qrCode->writeDataUri();
		$dataUri = $writer->write($qrCode)->getDataUri();
        $html = $this->renderView('registre_inventaire/carteQR.html.twig', array(
        	'dataUri' => $dataUri ,
        	'numinventaire'    => $numinventaire
        ));
		return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            'QR_'.$id.'.pdf' ,
        );
                
    }
	
	
    /**
     * @Route("/invQRALL/{id}", name="invQRALL")
     */
    public function invQRALLAction(Pdf $knpSnappyPdf, $id = null)
    {
        $em = $this->getDoctrine()->getManager();

        $documents = [];

        if(!is_null($id)) {
            $reception = $em->getRepository(Reception::class)->find($id);
            foreach ($reception->getReceptionLignes() as $line){
                foreach ($line->getRegistreInventaires() as $inventaire){
                    array_push($documents,$inventaire);
                }
            }
        }
        else $documents = $em->getRepository(RegistreInventaire::class)->findAll();

        $carteALL = array();
		foreach ($documents as $document) {


				$options = [
					'orientation'   => 'Landscape',
					'page-height'   => 50,
					'page-width'    => 25,
					'margin-top'    => 0,
					'margin-right'  => 0,
					'margin-bottom' => 0,
					'margin-left'   => 0,
				];
				// Create a basic QR code
				$qrCode = new QrCode($document->getNumInventaire());
				$qrCode->setSize(85);
				$qrCode->setBackgroundColor(new Color(245,245,245));
				$writer = new PngWriter();
				$dataUri = $writer->write($qrCode)->getDataUri();
				array_push($carteALL,array($dataUri,$document->getNumInventaire()));

	        
		}
		
        $html = $this->renderView('registre_inventaire/carteQRAll.html.twig', array(
        	'carteALL' => $carteALL ,
            'base_dir' => $this->getParameter('kernel.project_dir'). '/../'
        ));

		return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            'INV_QR_All.pdf' ,
        );
        
        
    }
}