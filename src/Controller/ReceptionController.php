<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\Reception;
use App\Entity\RegistreInventaire;
use App\Form\ReceptionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
class ReceptionController extends AbstractController
{


    /**
     * @Route("/receptions", name="receptions")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $receptions = $em->getRepository(Reception::class)->findAll();
        $this->get('session')->set('lastId',$em->getRepository(Reception::class)->getLastReception()['result']);
        
        return $this->render('reception/liste.html.twig', array('receptions' => $receptions));

        
    }
    /**
     *  @Route("/showReception/{id}", name="showReception")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Reception::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');
        $Inventaires = array();
        foreach ($entity->getReceptionLignes() as $line){
            foreach ($line->getRegistreInventaires() as $inventaire){
                array_push($Inventaires,$inventaire);
            }
        }

        return $this->render('reception/show.html.twig', array('reception' => $entity, 'registreinventaires' => $Inventaires));
    }

    /**
     * @Route("/editReception/{id}", name="editReception")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Reception $entity)
    {
        $form = $this->createForm(ReceptionType::class, $entity);
        $em = $this->getDoctrine()->getManager('default');
        if ($em->getRepository(Reception::class)->isLastReception($entity->getId())['result'] == 1)
        return $this->render('reception/edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        else
        return $this->redirectToRoute('receptions');
    }

  /**
     * @Route("/updateReception/{id}", name="updateReception")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, Reception $reception) {
        $em = $this->getDoctrine()->getManager('default');
        $idOld = $reception->getId();
        //$rec = clone $reception;
        $rec = [];
        foreach ($reception->getReceptionLignes() as $line){
            $rec[] = clone $line;
        }
        $form = $this->createForm(ReceptionType::class, $reception->setRaisonSociale('-'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $receptionOld = $em->getRepository(Reception::class)->find($idOld);
            $em->remove($receptionOld);
            $em->flush();
            //dd($j);
            //dd($rec);
            $k = $em->getRepository(RegistreInventaire::class)->getNumInv();
            $em->persist($reception);
            $em->flush();

              foreach ($rec as $receptionligne){
                if (!$receptionligne->getArticle()->isInv()){
                    $receptionligne->getArticle()->setQte($receptionligne->getArticle()->getQte() - $receptionligne->getQte());
                    $em->persist($receptionligne->getArticle());
                    $em->flush();
                }
            }

            
            $em->getRepository(RegistreInventaire::class)->deleteNumInv($k['num']);
            $j = isset($k['num'])?$k['num']:0;
            foreach ($reception->getReceptionLignes() as $receptionligne){
                if ($receptionligne->getArticle()->isInv()){
                
                    for ($i = 1; $i <= $receptionligne->getQte(); $i++){
                        $j++;
                        $inventaire = new RegistreInventaire();
                        $inventaire->setDateReception($reception->getDateReception());
                        $inventaire->setArticle($receptionligne->getArticle());
                        $inventaire->setReceptionLigne($receptionligne);
                        $inventaire->setCategorie($receptionligne->getArticle()->getCategorie());
                        $inventaire->setNumBCAO($reception->getNumBCAO());
                        $inventaire->setNumLivraison($reception->getNumLivraison());
                        $inventaire->setRaisonSocialeFournisseur($receptionligne->getArticle()->getFournisseur()->getRaisonSociale());
                        $inventaire->setQte(1);
                        $inventaire->setEtatConservation($reception->isNeuf()?'neuf':'seconde main');
                        $inventaire->setAffecterA('-');
                        $inventaire->setLocal('-');
                        $inventaire->setNumInventaire('ENSAT/'.date("Y").'/'.str_pad($j, 6, '0', STR_PAD_LEFT));
                        $em->persist( $inventaire);
                    }
                    $em->flush();
                }  else{
                    $receptionligne->getArticle()->setQte($receptionligne->getArticle()->getQte() + $receptionligne->getQte());
                    $em->persist($receptionligne->getArticle());
                    $em->flush();
                } 
            } 
            
             
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!".$reception->getId());
            
            return $this->redirectToRoute('showReception',array('id'=>$reception->getId()));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('reception/edit.html.twig', array('entity' => $reception, 'form' => $form->createView()));
    }


    /**
     * @Route("/addReception", name="addReception")
     *  @Security("is_granted('ROLE_FONC')")
     */

   public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Reception();
        $entity->setNumReception('REC'.str_pad($em->getRepository(Reception::class)->getNumReception()['nextval'], 6, '0', STR_PAD_LEFT));
        
        $form = $this->createForm(ReceptionType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->getRepository(Reception::class)->NextNumReception();
            $entity->setRaisonsociale('-');
            $em->persist( $entity );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
            $k = $em->getRepository(RegistreInventaire::class)->getNumInv();
            $j = isset($k['num'])?$k['num']:0;
            //dd($j);
            foreach ($entity->getReceptionLignes() as $receptionligne){
                if ($receptionligne->getArticle()->isInv()){
                
                    for ($i = 1; $i <= $receptionligne->getQte(); $i++){
                        $j++;
                        $inventaire = new RegistreInventaire();
                        $inventaire->setDateReception($entity->getDateReception());
                        $inventaire->setArticle($receptionligne->getArticle());
                        $inventaire->setReceptionLigne($receptionligne);
                        $inventaire->setCategorie($receptionligne->getArticle()->getCategorie());
                        $inventaire->setNumBCAO($entity->getNumBCAO());
                        $inventaire->setNumLivraison($entity->getNumLivraison());
                        $inventaire->setRaisonSocialeFournisseur($receptionligne->getArticle()->getFournisseur()->getRaisonSociale());
                        $inventaire->setQte(1);
                        $inventaire->setEtatConservation($entity->isNeuf()?'neuf':'seconde main');
                        $inventaire->setAffecterA('-');
                        $inventaire->setLocal('-');
                        $inventaire->setNumInventaire('ENSAT/'.date("Y").'/'.str_pad($j, 6, '0', STR_PAD_LEFT));
                        $em->persist( $inventaire);
                    }
                }
                else{
                    $receptionligne->getArticle()->setQte(($receptionligne->getArticle()->getQte()?$receptionligne->getArticle()->getQte():0) + $receptionligne->getQte());
                    $em->persist($receptionligne->getArticle());
                }
                $em->flush();
            }

            $Inventaires = array();
        foreach ($entity->getReceptionLignes() as $line){
            foreach ($line->getRegistreInventaires() as $inventaire){
                array_push($Inventaires,$inventaire);
            }
        }
           return $this->redirectToRoute('showReception',array('id'=>$entity->getId()));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('reception/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
        }

        return $this->render('reception/new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
   	 * @Route("/remove_Reception/{id}", name="remove_Reception")
     *  @Security("is_granted('ROLE_FONC')")
     */   
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,Reception $reception)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($reception)){

	        $reception = $em->getRepository(Reception::class)->find($id);
	        $em->remove($reception);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('receptions'));
	    }else{
	    	return new Response('1');
	    }
    }

    #[Route('/etatReception/{id}', name: 'etatReception', methods: ['GET', 'POST'])]
   public function decharge_pdf(Pdf $knpSnappyPdf , Reception $reception)
   {
     
       $em = $this->getDoctrine()->getManager();

       $html = $this->renderView('document/reception.html.twig', [
           'reception' => $reception,
       ]);

       return new PdfResponse(
           $knpSnappyPdf->getOutputFromHtml($html),
           $reception->getNumReception().'.pdf' ,
       );
   }
}