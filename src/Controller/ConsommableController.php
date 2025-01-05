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
use App\Entity\Article;
use App\Entity\Affectation;
use App\Entity\Decharge;
use App\Form\AffectationType;
use App\Form\DechargeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address ;

class ConsommableController extends AbstractController
{


    /**
     * @Route("/consommable", name="consommable")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $consommable = $em->getRepository(Article::class)->findBy(array('inv' => 0));
        $entity=new Decharge();
        $session=$this->get('session');
        if ($session->get('ok')){
            $listes=$_SESSION['listes'];
            foreach ($listes as $code) {
                $article = $em->getRepository(Article::class)->findOneBy(array('id'=> $code));
                if($article){ 
                    $affectation = new Affectation();          
                    $affectation->setArticle($article);
                    $entity->addAffectation($affectation);
                }
            }
        }
        $form = $this->createForm(DechargeType::class, $entity);
         
       return $this->render('consommable/liste.html.twig', array('consommable' => $consommable,'entity' => $entity, 'form' => $form->createView()));

        
    }
    /**
     *  @Route("/showConsommable/{id}", name="showConsommable")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Article::class)->findBy(array('id' => $id,'inv'=>false))[0];
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('consommable/show.html.twig', array('article' => $entity));
    }

    /**
     * @Route("/AffectationConsommable/{id}", name="AffectationConsommable")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Request $request,Article $article)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = new Affectation();
        $entity->setArticle($article);
        $entity->setDateDebut(new \DateTime());
        $form = $this->createForm(AffectationType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setQte($article->getQte() - $entity->getQte());
            $entity->setArticle($article);
            $em->persist( $entity );
            $em->persist( $article );
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
           return $this->render('consommable/show.html.twig', array('article' => $article));
        }
       if ($form->isSubmitted() && !$form->isValid()) {
        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        return $this->render('consommable/edit.html.twig', array('entity' => $entity,'article' => $article, 'form' => $form->createView()));
        }
        return $this->render('consommable/edit.html.twig', array('entity' => $entity,'article' => $article, 'form' => $form->createView()));
    }
    
        /**
     * @Route("/AffectationConsommableMasse", name="AffectationConsommableMasse")
     */
    public function AffectationConsommableMasseAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();
        $listes= explode(",",$request->query->get("liste"));
        $_SESSION['listes']=$listes; 
        $session=$this->get('session');
        $session->set('ok',true);
           return new JsonResponse($request->query->get("liste"));

        
    }

    /**
     * @Route("/newDechargeC", name="newDechargeC")
     *  @Security("is_granted('ROLE_FONC')")
     */

     public function newDecharge(Request $request, MailerInterface $mailer)
     {
         $em = $this->getDoctrine()->getManager();
         $entity = new Decharge();
         $listes=$_SESSION['listes'];
            foreach ($listes as $code) {
                $article = $em->getRepository(Article::class)->findOneBy(array('id'=> $code));
                if($article){ 
                    $affectation = new Affectation();          
                    $affectation->setArticle($article);
                    $entity->addAffectation($affectation);
                }
            }
         $form = $this->createForm(DechargeType::class, $entity);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
         $entity->setNumDecharge('BS'.str_pad($em->getRepository(Decharge::class)->getNumBS()['nextval'], 6, '0', STR_PAD_LEFT));
         $em->getRepository(Decharge::class)->NextNumBS();
         $entity->setExercice(date('Y'));
         $length = count($listes);
         for ($i = 0; $i < $length; $i++){
            $affectation = $entity->getAffectations()[$i];
            $article = $em->getRepository(Article::class)->findOneBy(array('id'=> $listes[$i]));
            $affectation->setArticle($article);
            $affectation->setPersonnel($entity->getPersonnel());
            $affectation->setDateDebut($entity->getDateDecharge());
            $affectation->setLocal($entity->getLocal());
            $article->setQte( $article->getQte() -  $affectation->getQte());
            if($article->getQte() < $article->getSeuil()) $this->sendmail($article, $mailer);

            /*     $em->persist($affectation);
                $em->persist($article);
           $em->flush(); */
        }
             $em->persist( $entity );
             $em->flush();
             $this->get('session')->getFlashBag()->add('success', "Le Fournisseur a été ajouté avec succès.".$entity->getId());
             return $this->redirectToRoute('showDecharge', array('id' => $entity->getId()));
         }
        if ($form->isSubmitted() && !$form->isValid()) {
         $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
         return $this->redirectToRoute('consommable');
         }
  
         return $this->redirectToRoute('consommable');
     }

     #[Route('/etatDechargeC/{id}', name: 'etatDechargeC', methods: ['GET', 'POST'])]
     public function decharge_pdf(Pdf $knpSnappyPdf , Decharge $decharge)
     {
       
         $em = $this->getDoctrine()->getManager();
  
         $html = $this->renderView('document/bonsortie.html.twig', [
             'decharge' => $decharge,
         ]);
  
         return new PdfResponse(
             $knpSnappyPdf->getOutputFromHtml($html),
             'BON DE SORTIE '.$decharge->getNumDecharge().'.pdf' ,
         );
     }

     public function sendmail(Article $article, MailerInterface $mailer){
        $subject = "Alerte de quantité pour l\'article ".$article->getDesignation();
            $html = $this->renderView('consommable/alertestock.html.twig',['article'  => $article]); 
            $email = (new TemplatedEmail())
       
            ->from(new Address('gcvre@uae.ac.ma', 'E-ENSA Mailer'))
            ->to($this->getUser()->getEmail())
            ->subject($subject)
            ->html($html);
            try {
              $mailer->send($email);
          
            } catch (TransportExceptionInterface $e) {
            } 
     }

}