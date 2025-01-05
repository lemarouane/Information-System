<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\StructRech;
use App\Entity\Departement;
use App\Form\BudgetType;
use App\Form\BudgetAddType;
use App\Form\BudgetType1;
use App\Form\BudgetEntreeType;
use App\Entity\BudgetSortie;
use App\Entity\BudgetEntree;
use App\Repository\BudgetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


    /**
     *
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN') ")
     */
class BudgetController extends AbstractController
{
    /*
    * @Security("is_granted('ROLE_FINANCE')")
    */
    #[Route('/budget', name: 'app_budget_index', methods: ['POST','GET'])]
    public function index(BudgetRepository $budgetRepository): Response
    {
        return $this->render('budget/index.html.twig', [
            'budgets' => $budgetRepository->findAll(),
        ]);
    } 


  
    /*
    * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    #[Route('/addBudget', name: 'addBudget', methods: ['GET','POST'])]
    public function new(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Budget();
        $form = $this->createForm(BudgetAddType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { // 
  
            $budget = $em->getRepository(Budget::class)->findOneBy(array('annee' =>  $entity->getAnnee()));
            if(!$budget){
                $em->persist( $entity );
                $em->flush();
            }else{
                $this->get('session')->getFlashBag()->add('danger', "Budget de l'année ".$entity->getAnnee() ." existe déjà .");
                return $this->render('budget/budgetAdd.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new'));
            }
            
   

           return $this->redirect($this->generateUrl('editBudget', array('id' => $entity->getId())));
        }
 

        return $this->render('budget/budgetAdd.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new'));
    }




  
    /*
    * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    #[Route('/editBudget/{id}', name: 'editBudget', methods: ['GET','POST'])]
    public function editAction(Budget $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(BudgetType1::class, $entity);
        
        return $this->render('budget/budget.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit'));
        
        
    }

    /**
     * @Route("/addSortieBudget/{id}", name="addSortieBudget")
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    public function addSortieAction(Budget $entity)
    {
  
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(BudgetType::class, $entity);
        
        
        return $this->render('budget/addSortiebudget.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit'));
        
        
    }




     /**
    * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    #[Route('/updateSortieBudget/{id}', name: 'updateSortieBudget', methods: ['POST'])]
    public function updateSortieAction(Request $request, Budget $budget,$id) { 

        $em = $this->getDoctrine()->getManager('default');
//////
        $arr1= [];
        $arr2= [];
        $sortie = $em->getRepository(BudgetSortie::class)->findByBudget($budget->getId());
////
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);
        $somme = $budget->getMontant() ;
        $totale = 0;

        if ($form->isSubmitted()) {

/////
            foreach ($budget->getBudgetSorties() as $value) {
                array_push($arr1,$value->getId());
                array_push($arr2,$value->getMontant());
               // $somme = $somme - $value->getMontant() ;
                
             }

             foreach ($sortie as $key) {

                $found_key = in_array($key['id'], $arr1);
                $found_key1 =  array_search($key['id'], $arr2);
                if($found_key === false){
                    if($key['temoin'] == 'F'){
                        $this->get('session')->getFlashBag()->add('danger', "modification refusé");
            
                        return $this->render('budget/budget.html.twig', array('entity' => $budget, 'form' => $form->createView(), 'page' => 'edit'));
                    }
                }else{
                    if($key['temoin'] == 'F'){
                        if($arr2[$found_key1]<$key['montant']){
                            $this->get('session')->getFlashBag()->add('danger', "modification refusé");
            
                            return $this->render('budget/budget.html.twig', array('entity' => $budget, 'form' => $form->createView(), 'page' => 'edit'));
                        }
                    }
                }
                
             }

//////

                 foreach ($budget->getBudgetSorties() as $value) {
                    $somme = $somme - $value->getMontant() ;
                    $totale = $totale + $value->getMontant();
                 }
                 $budget->setMontant($budget->getTotaleEntree()-$totale);
                 $budget->setTotaleSortie(  $totale);

                $em->persist($budget); 

                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
                
                return $this->redirect($this->generateUrl('app_budget_index'));
            
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('budget/addSortiebudget.html.twig', array('entity' => $budget, 'form' => $form->createView(), 'page' => 'edit'));
    }


    /**
     * @Route("/updateEntreeBudget/{id}", name="updateEntreeBudget")
     * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
     */
    public function updateEntreeAction(Request $request, Budget $budget) { 
        $em = $this->getDoctrine()->getManager('default');
       $entree = $em->getRepository(BudgetEntree::class)->findByBudget($budget->getId());
     
        $arr1= [];
        $arr2= [];
        $form = $this->createForm(BudgetType1::class, $budget);
        $form->handleRequest($request);
        $somme = 0 ;
        if ($form->isSubmitted()) {
                
                 foreach ($budget->getBudgetEntrees() as $value) {
                    array_push($arr1,$value->getId());
                    array_push($arr2,$value->getMontant());
                    $somme = $somme + $value->getMontant() ;
                    
                 }

                 foreach ($entree as $key) {

                    $found_key = in_array($key['id'], $arr1);
                    $found_key1 =  array_search($key['id'], $arr2);
                    if($found_key === false){
                        if($key['temoin'] == 'F'){
                            $this->get('session')->getFlashBag()->add('danger', "modification refusé");
                
                            return $this->render('budget/budget.html.twig', array('entity' => $budget, 'form' => $form->createView(), 'page' => 'edit'));
                        }
                    }else{
                        if($key['temoin'] == 'F'){
                            if($arr2[$found_key1]<$key['montant']){
                                $this->get('session')->getFlashBag()->add('danger', "modification refusé");
                
                                return $this->render('budget/budget.html.twig', array('entity' => $budget, 'form' => $form->createView(), 'page' => 'edit'));
                            }
                        }
                    }
                    
                 }

                 $budget->setMontant($somme - $budget->getTotaleSortie());
                 $budget->setTotaleEntree( $somme);

                $em->persist($budget);

                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
                
                return $this->redirect($this->generateUrl('app_budget_index'));
            
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('budget/budget.html.twig', array('entity' => $budget, 'form' => $form->createView(), 'page' => 'edit'));
    }






    /*
    * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    #[Route('/budget_{id}_delete', name: 'app_budget_delete', methods: ['GET'])]
    public function delete(Request $request, Budget $budget, BudgetRepository $budgetRepository  , $id): Response
    {


        if ($this->isCsrfTokenValid('delete'.$budget->getId(), $request->get('_token'))) {
            $budgetRepository->remove($budget, true);
        }

        return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
    }

    /*
    * @Security("is_granted('ROLE_PROF') or is_granted('ROLE_FINANCE')")
    */
    #[Route('/budget_{id}_show', name: 'app_budget_show', methods: ['GET'])]
    public function show(Budget $budget): Response
    {
        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
        ]);
    }



    /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * @Route("/list_structures_by_type", name="list_structures_by_type")
     * @param Request $request
     * @return JsonResponse
     */
    public function list_structures_by_type(Request $request, $type)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $dep = null;
        $str = null;
        $responseArray = array();

        if($type==1){
            $dep = $em->getRepository(Departement::class);
        }else{
            $str = $em->getRepository(StructRech::class);
        }

             if($dep!=null){
                $dep = $dep->findAll();

                foreach($dep as $d){
                    $responseArray[] = array(
                        "id" => $d->getId(),
                        "libelle" => $d->getLibelleDep()
                    );
                }

                 }
              if($str!=null){
                $str = $str->findAll(); 

                foreach($str as $s){
                    $responseArray[] = array(
                        "id" => $s->getId(),
                        "libelle" => $s->getLibelleStructure()
                    );
                }
                  }
    
          
        

        return new JsonResponse($responseArray);

        
    }

    /*
    * @Security("is_granted('ROLE_FINANCE') or is_granted('ROLE_ADMIN')")
    */
    #[Route('/StructOfTypeStruct', name: 'StructOfTypeStruct', methods: ['GET', 'POST'])]
    public function listStructOfTypeStructAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $departementRepository = $em->getRepository(Departement::class);
        $structureRepository = $em->getRepository(StructRech::class);
        $responseArray = array();
        if($request->query->get("typeStruct")==1){

            $departements = $departementRepository->createQueryBuilder("d")
            ->getQuery()
            ->getResult();

            foreach($departements as $dep){
                $responseArray[] = array(
                    "id" => $dep->getId(),
                    "libelle" =>  $dep->getLibelleDep()
                );
            }
        }else{
            $structures = $structureRepository->createQueryBuilder("s")
            ->getQuery()
            ->getResult();

            foreach($structures as $struct){
                $responseArray[] = array(
                    "id" => $struct->getId(),
                    "libelle" =>  $struct->getLibelleStructure()
                );
            }
        }
        
        
        
        
        


        return new JsonResponse($responseArray);

        
    }



}
