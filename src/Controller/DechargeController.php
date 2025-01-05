<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\Decharge;
use App\Form\DechargeCType;
use App\Form\DechargeInvType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class DechargeController extends AbstractController
{


    /**
     * @Route("/Decharges", name="Decharges")
     * @Security("is_granted('ROLE_FONC') or is_granted('ROLE_PROF')")
     */
    public function paragrapheAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();


        $decharges = $em->getRepository(Decharge::class)->findNotAnuler();
        
        return $this->render('decharge/liste.html.twig', array('decharges' => $decharges));

        
    }
    /**
     *  @Route("/showDecharge/{id}", name="showDecharge")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Decharge::class)->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');
        if(substr($entity->getNumDecharge(), 0, 1) === 'D')
        return $this->render('registre_inventaire/showdecharge.html.twig', array('decharge' => $entity));
        elseif(substr($entity->getNumDecharge(), 0, 2) === 'BS')
        return $this->render('consommable/showdecharge.html.twig', array('decharge' => $entity));
        else return $this->redirectToRoute('Decharges');
    }

    /**
     * @Route("/editDecharge/{id}", name="editDecharge")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function editAction(Decharge $entity)
    {

        $form = $this->createForm(DechargeInvType::class, $entity);
        $formC = $this->createForm(DechargeCType::class, $entity);

       // dd($form);
        if(substr($entity->getNumDecharge(), 0, 1) === 'D')
        return $this->render('decharge/edit.html.twig', array('decharge' => $entity, 'form' => $form->createView()));
        elseif(substr($entity->getNumDecharge(), 0, 2) === 'BS')
        return $this->render('decharge/editC.html.twig', array('decharge' => $entity, 'form' => $formC->createView()));
        else return $this->redirectToRoute('Decharges');
        
    }

  /**
     * @Route("/updateDecharge/{id}", name="updateDecharge")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateAction(Request $request, Decharge $decharge) {
        $em = $this->getDoctrine()->getManager('default');
        $idOld = $decharge->getId();
        $affectations = [];
        foreach($decharge->getAffectations() as $affectation) $affectations[] = clone $affectation;
        $form = $this->createForm(DechargeInvType::class, $decharge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dechargeOld = clone $em->getRepository(Decharge::class)->find($idOld);
            $dechargeOld->setNumDecharge('anuler')->setId((int)NULL);
            $em->persist($dechargeOld);
            $em->flush();
            foreach($dechargeOld->getAffectations() as $affectation) $dechargeOld->removeAffectation($affectation);
            foreach($affectations as $affectation) {
                $affectation->setDateFin(new \DateTime());
                $affectation->setDecharge($dechargeOld);
                    $affectation->getInventaire()->setPersonnel(NULL)
                    ->setAffecterA('STOCK')
                    ->setLocal('STOCK')
                    ->setDateDecharge(new \DateTime())
                    ->setNumDecharge('');
            foreach($affectation->getInventaire()->getAffectations() as $af) if($af->getDateFin() == NULL) $af->setDateFin(new \DateTime());
            $dechargeOld->addAffectation($affectation);
            $em->persist($affectation);
            $em->flush();
            }
        foreach($decharge->getAffectations() as $affectation) {
            $affectation->setPersonnel($decharge->getPersonnel())->setNumInventaire($affectation->getInventaire()->getNumInventaire())->setLocal($decharge->getLocal())->setDateDebut($decharge->getDateDecharge());  
            foreach($affectation->getInventaire()->getAffectations() as $af) if($af->getDateFin() == NULL) $af->setDateFin(new \DateTime());
            $affectation->setDateFin(NULL);
        }
             $em->persist($decharge);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirectToRoute('showDecharge', array('id' => $decharge->getId()));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('decharge/edit.html.twig', array('entity' => $decharge, 'form' => $form->createView()));
    }

  /**
     * @Route("/updateDechargeC/{id}", name="updateDechargeC")
     *  @Security("is_granted('ROLE_FONC')")
     */
    public function updateActionC(Request $request, Decharge $decharge) {
        $em = $this->getDoctrine()->getManager('default');
        $affectations = [];
        foreach($decharge->getAffectations() as $affectation) $affectations[] = clone $affectation;
        $form = $this->createForm(DechargeCType::class, $decharge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($affectations as $affectation) {
                $affectation->getArticle()->setQte($affectation->getArticle()->getQte() + $affectation->getQte());
                $em->persist($affectation->getArticle());
             $em->flush();
            }
            foreach($decharge->getAffectations() as $affectation) {
                $affectation->getArticle()->setQte($affectation->getArticle()->getQte() - $affectation->getQte());
                $em->persist($affectation->getArticle());
             $em->flush();
            }
             $em->persist($decharge);
             $em->flush();
            $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
            
            return $this->redirectToRoute('showDecharge', array('id' => $decharge->getId()));
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('decharge/editC.html.twig', array('entity' => $decharge, 'form' => $form->createView()));
    }


}