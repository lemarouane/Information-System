<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Pagination\Paginator;
use App\Entity\ExecutionPE;
use App\Entity\Rubrique;
use App\Entity\ProgrammeEmploi;
use App\Entity\Personnel;
use App\Form\executionPEType;
use App\Entity\ExecutionElement;
//use App\Entity\BudgetPourcentage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Security;
use Symfony\Component\Security\Core\Security as secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Entity\Utilisateurs;
use App\Twig\ConfigExtension;
use App\Entity\Config;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Mime\Address;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use App\Form\executionPEBudgetType;
use App\Entity\ProgrammeEmploiBudget;



class ExecutionPEController extends AbstractController 
{
    
     /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/showExecProg_{id}', name: 'showExecProg', methods: ['GET','POST'])]
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ExecutionPE::class)->findBy(array('programme'=>$id));
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('executionPE/table-datatable-exec-prof.html.twig', array('execs' => $entity));
    }

    
     /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/showAllExec', name: 'showAllExec', methods: ['GET','POST'])]
    public function showAllExec()
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ExecutionPE::class)->findAll();
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('executionPE/table-datatable-exec.html.twig', array('execs' => $entity));
    }

   
     /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/execListe', name: 'execListe', methods: ['GET','POST'])]
    public function executionPElisteAction(Request $request,secure $security)
    {
        $em = $this->getDoctrine()->getManager('default');
        $usr = $security->getUser();  
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('numPPR' =>$usr->getNumPPR()));
        $pe = $em->getRepository(ProgrammeEmploi::class)->findOneBy(array('personne' =>$personne));
        $entity = $em->getRepository(ExecutionPE::class)->findBy(array('programme' => $pe));
        return $this->render('executionPE/executionEmploiProf.html.twig', array('ExecutionPE' => $entity));
    }

  
  

   
     /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/updateExec_{id}', name: 'updateExec', methods: ['GET','POST'])]
    public function updateAction(Request $request, ExecutionPE $executionPE ,secure $security , $id) {

        $em = $this->getDoctrine()->getManager('default');
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$usr->getId()));

        $form = $this->createForm(executionPEType::class, $executionPE,array('label' => $personne->getId(),'trim' => $executionPE->getProgramme()->getId()));
        $form->handleRequest($request);
        $montant=0;
        if ($form->isSubmitted()) {
            if($executionPE->getProgramme()->isValider()=== false){
                $this->get('session')->getFlashBag()->add('danger', "l'execution de PE n'est pas encore validé");
                if($this->get('security.authorization_checker')->isGranted("ROLE_USER")){
                    return $this->redirect($this->generateUrl('paramStock', array('param' => 'EPE')));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{
              /*   $budgets = $em->getRepository(BudgetPourcentage::class)->findAll();
                foreach ($budgets as $key) {
                    $bud=0;
                    foreach ($executionPE->getElement() as $ele) {
                        if($key->getId() == $ele->getRubrique()->getBudget()->getId()){
                            $bud=$bud+$ele->getMontant();
                        }
                    }

                    if($bud>(($executionPE->getProgramme()->getMontant()*$key->getPourcentage())/100)){
                        $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé le ".$key->getPourcentage()."% du budget de ".$key->getLibelle());
                        return $this->redirect($this->generateUrl('editExecutionPE', array('id' => $executionPE->getId())));
                    }
                } */
                foreach ($executionPE->getExecutionElements() as $element) {
                    $montant=$montant+$element->getMontant();
                }
                if($montant>$executionPE->getProgramme()->getMontant()){
                    $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé la recette !");
                    return $this->redirect($this->generateUrl('editExec', array('id' => $executionPE->getId())));
                }

                $executionPE->setDateMOD(new \DateTime());
                $em->merge($executionPE);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
                return $this->redirect($this->generateUrl('ProgrammeEmploi_prof'));
                
               // return $this->redirect($this->generateUrl('editExec', array('id' => $executionPE->getId())));
            }
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->redirect($this->generateUrl('editExec', array('id' => $executionPE->getId())));
    
    }



       /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/editExec_{id}', name: 'editExec', methods: ['GET','POST'])]
    public function editAction(ExecutionPE $entity ,secure $security,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $pe = $entity->getProgramme();
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$usr->getId()));
    
    
        $form = $this->createForm(executionPEType::class, $entity,array('label' => $personne->getId(),'trim' => $pe->getId() ) );

       
        if($entity->getProgramme()->isValider()=== false){
             $this->get('session')->getFlashBag()->add('danger', "l'élaborer de PE est désactivé par l'administrateur");
            if($this->get('security.authorization_checker')->isGranted("ROLE_USER")){
                return $this->redirect($this->generateUrl('app_dashboard'));
            }else{
                return new RedirectResponse($this->generateUrl('app_dashboard'));
            } 
            return $this->render('executionPE/exec_new.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit' ,'programme' => $pe));
        }else{

            return $this->render('executionPE/exec_new.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit' ,'programme' => $pe));
        }

        
    }
   
 /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/addExec_{id}', name: 'addExec', methods: ['GET','POST'])]
   public function addAction(Request $request,secure $security,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $pe = $em->getRepository(ProgrammeEmploi::class)->find($id);

      /*   $exec_already_exist = $em->getRepository(ExecutionPE::class)->findOneBy(array('programme'=>$pe->getId()));

        if($exec_already_exist!=NULL){
            return new RedirectResponse($this->generateUrl('editExec', array('id'=>$pe->getExecutionPEs()[0]->getId() ) ));
        } */
        
        if( $pe->isValider() == false){
                $this->get('session')->getFlashBag()->add('danger', "l'execution de PE n'est pas encore validé");
                if($this->get('security.authorization_checker')->isGranted("ROLE_USER")){
                    return $this->redirect($this->generateUrl('app_dashboard'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
        }else{

            $usr = $security->getUser();
            $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$usr->getId()));
            $montant=0;
            $entity = new ExecutionPE();
            $element= new ExecutionElement();
            $entity->addExecutionElement($element);
            $entity->setProgramme($pe);

            $form = $this->createForm(executionPEType::class, $entity,array('label' => $personne->getId(),'trim' => $pe->getId() ) );

            $form->handleRequest($request);
            if ($form->isSubmitted() ) { // && $form->isValid()

              /*   $budgets = $em->getRepository(BudgetPourcentage::class)->findAll();

                foreach ($budgets as $key) {
                    $bud=0;
                    foreach ($entity->getElement() as $ele) {

                        if($key->getId() == $ele->getRubrique()->getBudget()->getId()){
                            $bud=$bud+$ele->getMontant();
                        }
                        
                    }

                    if($bud>(($entity->getProgramme()->getMontant()*$key->getPourcentage())/100)){
                        $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé le ".$key->getPourcentage()."% du budget de ".$key->getLibelle());
                        return $this->redirect($this->generateUrl('addExecutionPE', array('id' => $pe->getId())));
                    }
                } */

                foreach ($entity->getExecutionElements() as $element) {
                    $montant=$montant+$element->getMontant();
                }
                if($montant>$entity->getProgramme()->getMontant()){
                    $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé la recette !");
                    return $this->redirect($this->generateUrl('addExec', array('id' => $pe->getId())));
                }
                
                $entity->setDateCRE(new \DateTime());
                $entity->setStatut("en cours de traitement");
                $em->persist( $entity );
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', "La demande a été ajouté avec succès.".$entity->getId());

                return $this->redirect($this->generateUrl('ProgrammeEmploi_prof'));
              //  return $this->render('executionPE/exec_new.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new','programme' => $pe));
            }
         /*   if ($form->isSubmitted() && !$form->isValid()) {
            $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
            return $this->render('executionPE/exec_new.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new','programme' => $pe));
            } */

            return $this->render('executionPE/exec_new.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new','programme' => $pe));
        }
    }

    
    /*
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/decisionExec', name: 'decisionExec', methods: ['GET','POST'])]
    public function decisionExecutionAction(secure $security,Request $request,$id)
    {

    
        $searchParam = $request->get('searchParam');
        $em = $this->getDoctrine()->getManager('default');
        extract($searchParam);
        $Motifs='';
        $execution = $em->getRepository(ExecutionPE::class)->find($id);

        if($decision==1){
            
            $execution->setStatut("Accepte");
  
        }elseif($decision==0){

            $execution->setStatut("Refusé");
            $Motifs ='à été refusé :'.$motif;
            $execution->setMotif("votre demande ".$Motifs);
        }

        $em->merge($execution);
        $em->flush();

        return $this->redirect($this->generateUrl('paramStock', array('param' => 'EPE')));


    }


     
     /*
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/removeExec_{id}', name: 'removeExec', methods: ['GET','POST'])]
    public function removeUsersAction(Request $request,$id,  TokenStorageInterface $token,ExecutionPE $executionPE)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($executionPE)){

	        $executionPE = $em->getRepository(ExecutionPE::class)->find($id);
	        $em->remove($executionPE);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('paramStock', array('param' => 'EPE')));
	    }else{
	    	return new Response('1');
	    }
    }

    



  /*
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/ExportExec', name: 'ExportExec', methods: ['GET','POST'])]
    public function export_exec_list(Request $request , secure $security)
    {
        $searchParam = $request->get('importFile');

        ///////////////////////////////////////////////////////////////////////////////////
        
                $em1 = $this->getDoctrine()->getManager();
              
                $security->getUser()->getRoles();
            
                $liste_executions =  $em1->getRepository(ExecutionPE::class)->findExecs();
              // dd($liste_executions);
        
        
                $objPHPExcel = new Spreadsheet();
        
                // Get the active sheet.
                $objPHPExcel->setActiveSheetIndex(0);
        
                $objPHPExcel->getProperties()
                    ->setCreator("Abdessamad")
                    ->setLastModifiedBy("Abdessamad")
                    ->setTitle("listes des PFE")
                    ->setSubject("listes des PFE")
                    ->setDescription("description du fichier")
                    ->setKeywords("");
                $sheet = $objPHPExcel->getActiveSheet();
                $j=1;
             
        
              
        
                $objPHPExcel->getActiveSheet()
                    ->getStyle('A'.$j.':G'.$j)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('09594C');
                $styleA1=array(
                    'font'=>array(
                        'bold'=>true,
                        'color'=>array('rgb'=>'FFFFFF'),
                        'name'=>'Times New Roman'
                    ),
                    'alignment'=>array(
                        'horizontal'=>Alignment::HORIZONTAL_LEFT
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray($styleA1);
        
                $styleA2=array(
                    'font'=>array(
                        'bold'=>true,
                        'color'=>array('rgb'=>'FFFFFF'),
                        'name'=>'Times New Roman'
                    ),
                    'alignment'=>array(
                        'horizontal'=>Alignment::HORIZONTAL_LEFT
                    ),
                    'fill' => array(
                        'type' => Fill::FILL_SOLID,
                        'color' => array('rgb' => '008fb3')
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => Border::BORDER_THIN
                        )
                    ),
                );
                $styleBordure1=array(
                    'borders' => array(
                        'allborders'     => array(
                            'style' => Border::BORDER_THIN
                        )
                    ),
                    'font'=>array(
                        'name'=>'Times New Roman'
                    ),
                    'alignment'=>array(
                        'horizontal'=>Alignment::HORIZONTAL_LEFT
                    )
                );
        
                    $entities = $liste_executions;
        
                    $sheet->getColumnDimension('A')->setWidth(60);
                    $sheet->getColumnDimension('B')->setWidth(60);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(15);
                    $sheet->getColumnDimension('G')->setWidth(20);
        
        
                    foreach( $entities as $e  ){
        
        
                
                        $sheet->setCellValue('A'.$j,'REF. PROG');
                        $sheet->setCellValue('B'.$j,'INTITULE');
                        $sheet->setCellValue('C'.$j,'NOM');
                        $sheet->setCellValue('D'.$j,'PRENOM');
                        $sheet->setCellValue('E'.$j,'MONTANT PE');
                        $sheet->setCellValue('F'.$j,'NUM. EXEC');
                        $sheet->setCellValue('G'.$j,'DATE DEMANDE');
                       
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A'.$j.':G'.$j)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('000000');
        
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray($styleA1);
        
        
        
                        $j++;    
                   
        
                            $sheet->setCellValue('A'.$j,$e->getProgramme()->getReference());
                            $sheet->setCellValue('B'.$j,$e->getProgramme()->getIntitule());
                            $sheet->setCellValue('C'.$j,$e->getProgramme()->getPersonne()->getNom());
                            $sheet->setCellValue('D'.$j,$e->getProgramme()->getPersonne()->getPrenom());
                            $sheet->setCellValue('E'.$j,$e->getProgramme()->getMontant());
                            $sheet->setCellValue('F'.$j,$e->getId());
                            $sheet->setCellValue('G'.$j,$e->getDateCRE());
        
                            $j++;
                            
                            $sheet->setCellValue('A'.$j,'RUBRIQUE DEMANDE');
                            $sheet->setCellValue('B'.$j,'INTITULE');
                            $sheet->setCellValue('C'.$j,'DESCRIPTION');
                            $sheet->setCellValue('D'.$j,'MONTANT EXEC');
        
                            $objPHPExcel->getActiveSheet()
                        ->getStyle('A'.$j.':D'.$j)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('095FFF');
               
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':D'.$j)->applyFromArray($styleA1);
                        $j++;
                            foreach($e->getExecutionElements() as $en  ){
                              
        
                                $sheet->setCellValue('A'.$j,$en->getRubrique()->getLibelle());
                                $sheet->setCellValue('B'.$j,$en->getIntitule());
                                $sheet->setCellValue('C'.$j,$en->getDescription());
                                $sheet->setCellValue('D'.$j,$en->getMontant());
                            $j++;
                            }
                            $j++; 
                         
                            $sheet->getStyle('A1'.':G'.$j)->getAlignment()->setHorizontal('center');
                }
        
                // Create your Office 2007 Excel (XLSX Format)
                $writer = new Xlsx($objPHPExcel);
                
                // Create a Temporary file in the system
                $fileName = 'Liste_Execs_FC.xlsx';
                $temp_file = tempnam(sys_get_temp_dir(), $fileName);
                
                // Create the excel file in the tmp directory of the system
                $writer->save($temp_file);
                
                // Return the excel file as an attachment
                return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
 



////////////////////////////////////////




 /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/showExecProgBudget_{id}', name: 'showExecProgBudget', methods: ['GET','POST'])]
    public function showActionProgBudget($id)
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ExecutionPE::class)->findBy(array('programme'=>$id));
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('executionPE/table-datatable-exec-prof.html.twig', array('execs' => $entity));
    }

    
     /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/showAllExecProgBudget', name: 'showAllExecProgBudget', methods: ['GET','POST'])]
    public function showAllExecProgBudget()
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(ExecutionPE::class)->findAll();
        if (!$entity) throw $this->createNotFoundException('Unable to find posts entity.');

        return $this->render('executionPE/table-datatable-exec.html.twig', array('execs' => $entity));
    }

   
     /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/execListeProgBudget', name: 'execListeProgBudget', methods: ['GET','POST'])]
    public function executionPElisteActionProgBudget(Request $request,secure $security)
    {
        $em = $this->getDoctrine()->getManager('default');
        $usr = $security->getUser();  
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('numPPR' =>$usr->getNumPPR()));
        $pe = $em->getRepository(ProgrammeEmploiBudget::class)->findOneBy(array('personne' =>$personne));
        $entity = $em->getRepository(ExecutionPE::class)->findBy(array('programme' => $pe));
        return $this->render('executionPE/executionEmploiProf.html.twig', array('ExecutionPE' => $entity));
    }

  
  

   
     /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/updateExecProgBudget_{id}', name: 'updateExecProgBudget', methods: ['GET','POST'])]
    public function updateActionProgBudget(Request $request, ExecutionPE $executionPE ,secure $security , $id) {

        $em = $this->getDoctrine()->getManager('default');
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$usr->getId()));

        $form = $this->createForm(executionPEBudgetType::class, $executionPE,array('label' => $personne->getId(),'trim' => $executionPE->getProgrammeBudget()->getId()));
        $form->handleRequest($request);
        $montant=0;
        if ($form->isSubmitted()) {
            if($executionPE->getProgrammeBudget()->isValider()=== false){
                $this->get('session')->getFlashBag()->add('danger', "l'execution de PE n'est pas encore validé");
                if($this->get('security.authorization_checker')->isGranted("ROLE_USER")){
                    return $this->redirect($this->generateUrl('paramStock', array('param' => 'EPE')));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
            }else{
              /*   $budgets = $em->getRepository(BudgetPourcentage::class)->findAll();
                foreach ($budgets as $key) {
                    $bud=0;
                    foreach ($executionPE->getElement() as $ele) {
                        if($key->getId() == $ele->getRubrique()->getBudget()->getId()){
                            $bud=$bud+$ele->getMontant();
                        }
                    }

                    if($bud>(($executionPE->getProgramme()->getMontant()*$key->getPourcentage())/100)){
                        $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé le ".$key->getPourcentage()."% du budget de ".$key->getLibelle());
                        return $this->redirect($this->generateUrl('editExecutionPE', array('id' => $executionPE->getId())));
                    }
                } */
                foreach ($executionPE->getExecutionElements() as $element) {
                    $montant=$montant+$element->getMontant();
                }
                if($montant>$executionPE->getProgrammeBudget()->getMontant()){
                    $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé la recette !");
                    return $this->redirect($this->generateUrl('editExecProgBudget', array('id' => $executionPE->getId())));
                }

                $executionPE->setDateMOD(new \DateTime());
                $em->merge($executionPE);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', "modification avec succes!");
                
                return $this->redirect($this->generateUrl('editExecProgBudget', array('id' => $executionPE->getId())));
            }
        }

        $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->redirect($this->generateUrl('editExecProgBudget', array('id' => $executionPE->getId())));
    
    }



       /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/editExecProgBudget_{id}', name: 'editExecProgBudget', methods: ['GET','POST'])]
    public function editActionProgBudget(ExecutionPE $entity ,secure $security,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $pe = $entity->getProgrammeBudget();
        $usr = $security->getUser();
        $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$usr->getId()));
    
    
        $form = $this->createForm(executionPEBudgetType::class, $entity,array('label' => $personne->getId(),'trim' => $pe->getId() ) );

       
        if($entity->getProgrammeBudget()->isValider()=== false){
             $this->get('session')->getFlashBag()->add('danger', "l'élaborer de PE est désactivé par l'administrateur");
            if($this->get('security.authorization_checker')->isGranted("ROLE_USER")){
                return $this->redirect($this->generateUrl('app_dashboard'));
            }else{
                return new RedirectResponse($this->generateUrl('app_dashboard'));
            } 
            return $this->render('executionPE/exec_new_budget.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit' ,'programme' => $pe));
        }else{

            return $this->render('executionPE/exec_new_budget.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'edit' ,'programme' => $pe));
        }

        
    }
   
 /**
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/addExecProgBudget_{id}', name: 'addExecProgBudget', methods: ['GET','POST'])]
   public function addActionProgBudget(Request $request,secure $security,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $pe = $em->getRepository(ProgrammeEmploiBudget::class)->find($id);

      /*   $exec_already_exist = $em->getRepository(ExecutionPE::class)->findOneBy(array('programme'=>$pe->getId()));

        if($exec_already_exist!=NULL){
            return new RedirectResponse($this->generateUrl('editExec', array('id'=>$pe->getExecutionPEs()[0]->getId() ) ));
        } */
        
        if( $pe->isValider() == false){
                $this->get('session')->getFlashBag()->add('danger', "l'execution de PE n'est pas encore validé");
                if($this->get('security.authorization_checker')->isGranted("ROLE_USER")){
                    return $this->redirect($this->generateUrl('app_dashboard'));
                }else{
                    return new RedirectResponse($this->generateUrl('app_dashboard'));
                }
        }else{

            $usr = $security->getUser();
            $personne = $em->getRepository(Personnel::class)->findOneBy(array('idUser' =>$usr->getId()));
            $montant=0;
            $entity = new ExecutionPE();
            $element= new ExecutionElement();
            $entity->addExecutionElement($element);
            $entity->setProgrammeBudget($pe);

            $form = $this->createForm(executionPEBudgetType::class, $entity,array('label' => $personne->getId(),'trim' => $pe->getId() ) );

            $form->handleRequest($request);
            if ($form->isSubmitted() ) { // && $form->isValid()

              /*   $budgets = $em->getRepository(BudgetPourcentage::class)->findAll();

                foreach ($budgets as $key) {
                    $bud=0;
                    foreach ($entity->getElement() as $ele) {

                        if($key->getId() == $ele->getRubrique()->getBudget()->getId()){
                            $bud=$bud+$ele->getMontant();
                        }
                        
                    }

                    if($bud>(($entity->getProgramme()->getMontant()*$key->getPourcentage())/100)){
                        $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé le ".$key->getPourcentage()."% du budget de ".$key->getLibelle());
                        return $this->redirect($this->generateUrl('addExecutionPE', array('id' => $pe->getId())));
                    }
                } */

                foreach ($entity->getExecutionElements() as $element) {
                    $montant=$montant+$element->getMontant();
                }
                if($montant>$entity->getProgrammeBudget()->getMontant()){
                    $this->get('session')->getFlashBag()->add('danger', "Merci de vérifier, vous avez dépassé la recette !");
                    return $this->redirect($this->generateUrl('addExecProgBudget', array('id' => $pe->getId())));
                }
                
                $entity->setDateCRE(new \DateTime());
                $entity->setStatut("en cours de traitement");
                $em->persist( $entity );
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', "La demande a été ajouté avec succès.".$entity->getId());
              //  return $this->render('executionPE/exec_new_budget.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new','programme' => $pe));

              return $this->redirect($this->generateUrl('ProgrammeEmploiBudget_prof'));

            }
         /*   if ($form->isSubmitted() && !$form->isValid()) {
            $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs dans le formulaire soumis !");
            return $this->render('executionPE/exec_new.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new','programme' => $pe));
            } */

            return $this->render('executionPE/exec_new_budget.html.twig', array('entity' => $entity, 'form' => $form->createView(), 'page' => 'new','programme' => $pe));
        }
    }

    
    /*
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/decisionExecProgBudget', name: 'decisionExecProgBudget', methods: ['GET','POST'])]
    public function decisionExecutionActionProgBudget(secure $security,Request $request,$id)
    {

    
        $searchParam = $request->get('searchParam');
        $em = $this->getDoctrine()->getManager('default');
        extract($searchParam);
        $Motifs='';
        $execution = $em->getRepository(ExecutionPE::class)->find($id);

        if($decision==1){
            
            $execution->setStatut("Accepte");
  
        }elseif($decision==0){

            $execution->setStatut("Refusé");
            $Motifs ='à été refusé :'.$motif;
            $execution->setMotif("votre demande ".$Motifs);
        }

        $em->merge($execution);
        $em->flush();

        return $this->redirect($this->generateUrl('paramStock', array('param' => 'EPE')));


    }


     
     /*
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/removeExecProgBudget_{id}', name: 'removeExecProgBudget', methods: ['GET','POST'])]
    public function removeUsersActionProgBudget(Request $request,$id,  TokenStorageInterface $token,ExecutionPE $executionPE)
    {

        $em = $this->getDoctrine()->getManager('default');
        if(!empty($executionPE)){

	        $executionPE = $em->getRepository(ExecutionPE::class)->find($id);
	        $em->remove($executionPE);

	        $em->flush();
	        $this->get('session')->getFlashBag('success', "Vos modifications ont été enregistré avec succée.");
	        return $this->redirect($this->generateUrl('paramStock', array('param' => 'EPE')));
	    }else{
	    	return new Response('1');
	    }
    }

    



  /*
     *
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route('/ExportExecProgBudget', name: 'ExportExecProgBudget', methods: ['GET','POST'])]
    public function export_exec_list_ProgBudget(Request $request , secure $security)
    {
        $searchParam = $request->get('importFile');

        ///////////////////////////////////////////////////////////////////////////////////
        
                $em1 = $this->getDoctrine()->getManager();
              
                $security->getUser()->getRoles();
            
                $liste_executions =  $em1->getRepository(ExecutionPE::class)->findExecs_ProgBudget();
              // dd($liste_executions);
        
        
                $objPHPExcel = new Spreadsheet();
        
                // Get the active sheet.
                $objPHPExcel->setActiveSheetIndex(0);
        
                $objPHPExcel->getProperties()
                    ->setCreator("Abdessamad")
                    ->setLastModifiedBy("Abdessamad")
                    ->setTitle("listes des PFE")
                    ->setSubject("listes des PFE")
                    ->setDescription("description du fichier")
                    ->setKeywords("");
                $sheet = $objPHPExcel->getActiveSheet();
                $j=1;
             
        
              
        
                $objPHPExcel->getActiveSheet()
                    ->getStyle('A'.$j.':G'.$j)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('09594C');
                $styleA1=array(
                    'font'=>array(
                        'bold'=>true,
                        'color'=>array('rgb'=>'FFFFFF'),
                        'name'=>'Times New Roman'
                    ),
                    'alignment'=>array(
                        'horizontal'=>Alignment::HORIZONTAL_LEFT
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray($styleA1);
        
                $styleA2=array(
                    'font'=>array(
                        'bold'=>true,
                        'color'=>array('rgb'=>'FFFFFF'),
                        'name'=>'Times New Roman'
                    ),
                    'alignment'=>array(
                        'horizontal'=>Alignment::HORIZONTAL_LEFT
                    ),
                    'fill' => array(
                        'type' => Fill::FILL_SOLID,
                        'color' => array('rgb' => '008fb3')
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => Border::BORDER_THIN
                        )
                    ),
                );
                $styleBordure1=array(
                    'borders' => array(
                        'allborders'     => array(
                            'style' => Border::BORDER_THIN
                        )
                    ),
                    'font'=>array(
                        'name'=>'Times New Roman'
                    ),
                    'alignment'=>array(
                        'horizontal'=>Alignment::HORIZONTAL_LEFT
                    )
                );
        
                    $entities = $liste_executions;
        
                    $sheet->getColumnDimension('A')->setWidth(60);
                    $sheet->getColumnDimension('B')->setWidth(60);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(15);
                    $sheet->getColumnDimension('G')->setWidth(20);
        
        
                    foreach( $entities as $e  ){
        
        
                
                        $sheet->setCellValue('A'.$j,'REF. PROG');
                        $sheet->setCellValue('B'.$j,'INTITULE');
                        $sheet->setCellValue('C'.$j,'NOM');
                        $sheet->setCellValue('D'.$j,'PRENOM');
                        $sheet->setCellValue('E'.$j,'MONTANT PE');
                        $sheet->setCellValue('F'.$j,'NUM. EXEC');
                        $sheet->setCellValue('G'.$j,'DATE DEMANDE');
                       
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A'.$j.':G'.$j)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('000000');
        
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray($styleA1);
        
        
        
                        $j++;    
                   
        
                            $sheet->setCellValue('A'.$j,$e->getProgrammeBudget()->getReference());
                            $sheet->setCellValue('B'.$j,$e->getProgrammeBudget()->getIntitule());
                            $sheet->setCellValue('C'.$j,$e->getProgrammeBudget()->getPersonne()->getNom());
                            $sheet->setCellValue('D'.$j,$e->getProgrammeBudget()->getPersonne()->getPrenom());
                            $sheet->setCellValue('E'.$j,$e->getProgrammeBudget()->getMontant());
                            $sheet->setCellValue('F'.$j,$e->getId());
                            $sheet->setCellValue('G'.$j,$e->getDateCRE());
        
                            $j++;
                            
                            $sheet->setCellValue('A'.$j,'RUBRIQUE DEMANDE');
                            $sheet->setCellValue('B'.$j,'INTITULE');
                            $sheet->setCellValue('C'.$j,'DESCRIPTION');
                            $sheet->setCellValue('D'.$j,'MONTANT EXEC');
        
                            $objPHPExcel->getActiveSheet()
                        ->getStyle('A'.$j.':D'.$j)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('095FFF');
               
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':D'.$j)->applyFromArray($styleA1);
                        $j++;
                            foreach($e->getExecutionElements() as $en  ){
                              
        
                                $sheet->setCellValue('A'.$j,$en->getRubrique()->getLibelle());
                                $sheet->setCellValue('B'.$j,$en->getIntitule());
                                $sheet->setCellValue('C'.$j,$en->getDescription());
                                $sheet->setCellValue('D'.$j,$en->getMontant());
                            $j++;
                            }
                            $j++; 
                         
                            $sheet->getStyle('A1'.':G'.$j)->getAlignment()->setHorizontal('center');
                }
        
                // Create your Office 2007 Excel (XLSX Format)
                $writer = new Xlsx($objPHPExcel);
                
                // Create a Temporary file in the system
                $fileName = 'Liste_Execs_ProgBudget.xlsx';
                $temp_file = tempnam(sys_get_temp_dir(), $fileName);
                
                // Create the excel file in the tmp directory of the system
                $writer->save($temp_file);
                
                // Return the excel file as an attachment
                return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
 



























    

}