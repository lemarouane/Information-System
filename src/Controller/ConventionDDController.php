<?php

namespace App\Controller;

use App\Entity\Etudiant\ConventionDD;
use App\Entity\Etudiant\Etudiants;
use App\Entity\Etudiant\EtudiantDD;
use App\Entity\Etudiant\InscritEtudiant;
use App\Entity\Utilisateurs;
use App\Form\ConventionDDType;
use App\Form\EtudiantDDType;
use App\Repository\ConventionDDRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\InternetTest;

class ConventionDDController extends AbstractController
{
    #[Route('/conventionDD', name: 'app_convention_d_d_index', methods: ['GET'])]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $convention_d_ds = $em->getRepository(ConventionDD::class)->findAll();
        return $this->render('convention_dd/index.html.twig', [
            'convention_d_ds' => $convention_d_ds,
        ]);
    }


/**
     * @Route("/liste_inscrits_dd", name="liste_inscrits_dd")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SERVICEEXT')")
    */

    public function liste_inscrits_dd(Request $request , secure $security)
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $liste_dds = $em->getRepository(InscritEtudiant::class)->findAll();
      
        return $this->render('convention_dd/liste_inscrits_dd.html.twig', ['liste_dds' => $liste_dds]);

    }


 




    #[Route('/inscritDD', name: 'app_inscritDD_index', methods: ['GET'])]
    public function inscritDD(secure $security): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
        $user= $security->getUser();
        $user= $security->getUser();

        $config1 = new \Doctrine\DBAL\Configuration();
        $connectionParams1 = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
        $conn1 = \Doctrine\DBAL\DriverManager::getConnection($connectionParams1, $config1);
        
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn1);

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['ETUDIANT_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $codes = $em1->getRepository(Utilisateurs::class)->find($user)->getCodes();
        $etudiantDD = $em->getRepository(EtudiantDD::class)->searchEtudiantDDByCodes($codes,$conn,$anneeUniversitaire['COD_ANU']);
        return $this->render('convention_dd/inscritDD.html.twig', [
            'convention_d_ds' => $etudiantDD,
        ]);
    }

    #[Route('/conventionDD_new', name: 'app_convention_d_d_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $conventionDD = new ConventionDD();
        $form = $this->createForm(ConventionDDType::class, $conventionDD);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();
            if(!empty($fichier)){

                
                if ( file_exists($this->getParameter('webroot_ent').$conventionDD->getFichier()) 
                    && $conventionDD->getFichier()!="" && $conventionDD->getFichier()!=NULL){
                    unlink($this->getParameter('webroot_ent').$conventionDD->getFichier());
                }
                
                $fileUploader = new FileUploader($this->getParameter('webroot_ent'));
                $invitName = $fileUploader->upload($fichier);
         
                $conventionDD->setFichier($invitName);
            }
            $em->persist($conventionDD);
            $em->flush();
            

            return $this->redirectToRoute('app_convention_d_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('convention_dd/new.html.twig', [
            'convention_d_d' => $conventionDD,
            'form' => $form,
        ]);
    }


    #[Route('/conventionDD_edit/{id}', name: 'app_convention_d_d_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $conventionDD = $em->getRepository(ConventionDD::class)->find($id);
        $form = $this->createForm(ConventionDDType::class, $conventionDD);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();
            if(!empty($fichier)){

                
                if ( file_exists($this->getParameter('webroot_ent').$conventionDD->getFichier()) 
                    && $conventionDD->getFichier()!="" && $conventionDD->getFichier()!=NULL){
                    unlink($this->getParameter('webroot_ent').$conventionDD->getFichier());
                }
                
                $fileUploader = new FileUploader($this->getParameter('webroot_ent'));
                $invitName = $fileUploader->upload($fichier);
         
                $conventionDD->setFichier($invitName);
            }
            $em->persist($conventionDD);
            $em->flush();

            return $this->redirectToRoute('app_convention_d_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('convention_dd/edit.html.twig', [
            'convention_d_d' => $conventionDD,
            'form' => $form,
        ]);
    }

    #[Route('/conventionDD_delete/{id}/{_token}', name: 'app_convention_d_d_delete', methods: ['GET','POST'])]
    public function delete(Request $request, $id,$_token): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $conventionDD = $em->getRepository(ConventionDD::class)->find($id);
        if ($this->isCsrfTokenValid('delete'.$conventionDD->getId(), $_token)) {
            if ( file_exists($this->getParameter('webroot_ent').$conventionDD->getFichier()) 
                    && $conventionDD->getFichier()!="" && $conventionDD->getFichier()!=NULL){
                unlink($this->getParameter('webroot_ent').$conventionDD->getFichier());
            }
            $em->remove($conventionDD);
            $em->flush();
        }

        return $this->redirectToRoute('app_convention_d_d_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/etudiantDD_delete/{id}/{_token}', name: 'app_etudiant_d_d_delete', methods: ['GET','POST'])]
    public function deleteEtudiantDD(Request $request, $id,$_token): Response
    {
        $em = $this->getDoctrine()->getManager('etudiant');
        $conventionDD = $em->getRepository(EtudiantDD::class)->find($id);
        if ($this->isCsrfTokenValid('delete'.$conventionDD->getId(), $_token)) {
            $em->remove($conventionDD);
            $em->flush();
        }

        return $this->redirectToRoute('app_inscritDD_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/addConventionDD", name="addConventionDD")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SERVICEEXT')")
    */

   public function addAction(Request $request , secure $security)
   {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $config1 = new \Doctrine\DBAL\Configuration();
        $connectionParams1 = array('url' => $_ENV['ETUDIANT_DATABASE_URL'].'',);
        $conn1 = \Doctrine\DBAL\DriverManager::getConnection($connectionParams1, $config1);
        
        $em = $this->getDoctrine()->getManager('etudiant');
        $em1 = $this->getDoctrine()->getManager();
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	

        $entity = new EtudiantDD();
        $user= $security->getUser();
        $validateur_roles = $user->getRoles() ;
        $codes=[];
        if( in_array("ROLE_ADMIN",$validateur_roles) or in_array("ROLE_SERVICEEXT",$validateur_roles) ){
            
            $codes1=$em1->getRepository(Utilisateurs::class)->find($user)->getCodes();
            $conv = $em->getRepository(ConventionDD::class)->searchConventionDDByCodes($codes1,$conn1);
            
            foreach($codes1 as $code ){
                $list = explode("_",$code);
                if($list[0]=='FIL'){
                    array_push($codes,$list[1]);
                }
            } 
            $form = $this->createForm(EtudiantDDType::class, $entity,array('label' => $anneeUniversitaire['COD_ANU'],'label_attr' => $codes,'label_format' => $conv));
            $form->handleRequest($request);
            $etudiants=[];
            
            if ($form->isSubmitted() && $form->isValid()) {
 
                
                $annee      = $form->get('anneeSoutenance')->getData();
                $filiere1   = $form->get('filiere')->getData();
                $conv = $form->get('convention')->getData();

                if($annee!=null && $filiere1!=null && $conv!=null){
                    $etudiants=$conn->fetchAllAssociative("SELECT i.COD_ETU as APOGEE,i.LIB_NOM_PAT_IND as NOM,i.LIB_PR1_IND as PRENOM , i.cod_nne_ind as CNE
                                                FROM individu i,ins_pedagogi_etp etp  
                                                WHERE i.COD_IND=etp.COD_IND
                                                    AND etp.COD_ANU='".$anneeUniversitaire['COD_ANU']."'
                                                    AND etp.COD_ETP='".$filiere1."'
                                                ORDER BY i.LIB_NOM_PAT_IND asc");
                    return $this->render('convention_dd/etudiant_dd.html.twig', array('etudiants'=>$etudiants, 'form' => $form->createView()));
                }else{
                    $this->get('session')->getFlashBag()->add('danger', "MOD_TOUTE_INFO");
                    return $this->render('convention_dd/etudiant_dd.html.twig', array('etudiants'=>$etudiants, 'form' => $form->createView()));
                }
                

                
            }

            return $this->render('convention_dd/etudiant_dd.html.twig', array('etudiants'=>$etudiants,'form' => $form->createView()));
        }else{
            $this->get('session')->getFlashBag()->add('danger', "MOD_NO_ACCESS");
			return new RedirectResponse($this->generateUrl('app_dashboard'));

        }
        
   }

   /**
     * @Route("/add_list_etudiant", name="add_list_etudiant")
     * @Security("is_granted('ROLE_SERVICEEXT')")
     */
    public function add_list_etudiantAction(secure $security,Request $request)
    {

        $em = $this->getDoctrine()->getManager('etudiant');

        $listes= explode(",",$request->query->get("liste"));

        $convention = $em->getRepository(ConventionDD::class)->find($request->query->get("convention"));
        foreach ($listes as $code) {

            $entity = new EtudiantDD();
            
            $user = $em->getRepository(Etudiants::class)->findOneBy(array('code' => $code));
           
            if($user){
                $entity->setDateCreation(new \DateTime('now'));
                $entity->setEtudiants($user);
                $entity->setConvention($convention);
                $entity->setAnneeSoutenance($request->query->get("annee"));
                $entity->setFiliere($request->query->get("filiere"));

                $em->persist($entity);
            }
        }
        
        $em->flush();
        return new JsonResponse('1');
           
    }

     /**
     * @Route("/get_etudiant_non_ins", name="get_etudiant_non_ins")
     * @Security("is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_SERVICEEXT') or is_granted('ROLE_DIR')")
     */
    public function get_etudiant_non_insAction(secure $security,Request $request)
    {

        $em = $this->getDoctrine()->getManager('etudiant');
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	


        $non_inscrit = $em->getRepository(InscritEtudiant::class)->findUserByAnnee($anneeUniversitaire['COD_ANU']);
        
        return $this->render('convention_dd/add_inscrit_dd_annee.html.twig', [
            'convention' => $non_inscrit,
        ]);

           
    }


     /**
     * @Security("is_granted('ROLE_SERVICEEXT')")
     * @Route("/decisionInscription/{id}",name="decisionInscription")
     */
    public function decisionInscriptionAction(Request $request,$id)
    {

        $em = $this->getDoctrine()->getManager('etudiant');
        $searchParam = $request->get('searchParam');

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $anneeUniversitaire=$em->getRepository(Etudiants::class)->getAnneeUnivEncours($conn);	

        extract($searchParam);
        if($decision==1){
            $document = $em->getRepository(EtudiantDD::class)->find($id);
            $inscriptionDD = new InscritEtudiant();
            $inscriptionDD->setInscription($document);
            $inscriptionDD->setAnnee($anneeUniversitaire['COD_ANU']);
    
            $em->persist($inscriptionDD);
            $em->flush();
            return new JsonResponse('1');
        }else{
            return new JsonResponse('0');
        }
       

         


    }
}
