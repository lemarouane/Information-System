<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Entity\Personnel;
use App\Twig\ConfigExtension;
use App\Form\UtilisateursType;
use App\Form\ProfileUserType;
use App\Form\ProfileUserEditPassType;
use App\Repository\UtilisateursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security as secure;
use App\Entity\Config ;
use App\Entity\Etudiant\Stage;
use App\Entity\Etudiant\Etudiants;
use App\Entity\EtuHistoDemandes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

   
class UtilisateursController extends AbstractController
{
     /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/utilisateurs', name: 'app_utilisateurs_index', methods: ['GET', 'POST'])]
    public function index(secure $security ,UtilisateursRepository $utilisateursRepository): Response
    {
        
        return $this->render('utilisateurs/table-datatable-utilisateurs.html.twig', [
            'utilisateurs' => $utilisateursRepository->findAll(),
     
        ]);
    }

     /**
     *
     * @Security("is_granted('ROLE_CHEF_FIL') or is_granted('ROLE_SCOLARITE') or is_granted('ROLE_SERVICEEXT')")
     */
    #[Route('/historiques_validation', name: 'historiques_validation', methods: ['GET'])]
    public function historiques_validation(secure $security): Response
    {
    
        $usr = $security->getUser();
        $em = $this->getDoctrine()->getManager();
        $list_id = array();
        $list_nom = array();
        $list_filiere= array();
        $list_d_debut= array();
        $list_d_fin= array();
        $historique= $em->getRepository(EtuHistoDemandes::class)->findHistoriqueById($usr->getPersonnel());
       // dd($historique) ;

       $em1 = $this->getDoctrine()->getManager('etudiant');
     foreach ($historique as $key => $value) {
        $id =  $em1->getRepository(Stage::class)->findOneBy(array('id'=>$historique[$key]->getIdDemande()))->getId();
        $nom =  $em1->getRepository(Stage::class)->findOneBy(array('id'=>$historique[$key]->getIdDemande()))->getUser()->getNom();
        $prenom =  $em1->getRepository(Stage::class)->findOneBy(array('id'=>$historique[$key]->getIdDemande()))->getUser()->getPrenom();
        $filiere =  $em1->getRepository(Stage::class)->findOneBy(array('id'=>$historique[$key]->getIdDemande()))->getFiliere();
        $d_debut =  $em1->getRepository(Stage::class)->findOneBy(array('id'=>$historique[$key]->getIdDemande()))->getDateDebut();
        $d_fin =  $em1->getRepository(Stage::class)->findOneBy(array('id'=>$historique[$key]->getIdDemande()))->getDateFin();
        array_push($list_id,$id);
        array_push($list_nom,$nom." ".$prenom);
        array_push($list_filiere,$filiere);
        array_push($list_d_debut,$d_debut);
        array_push($list_d_fin,$d_fin);
    }


        return $this->render('utilisateurs/historiques_validation.html.twig', [
            'validation' => $historique,
            'list_id' => $list_id,
            'list_nom' => $list_nom,
            'list_filiere' => $list_filiere, 
            'list_d_debut' => $list_d_debut, 
            'list_d_fin' => $list_d_fin, 
     
        ]);
    }
    
    

    
    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/utilisateurs_new', name: 'app_utilisateurs_new', methods: ['GET','POST'])]
    public function new(Request $request, UtilisateursRepository $utilisateursRepository , UserPasswordEncoderInterface $passwordEncoder,  FileUploader $fileUploader): Response
    {
/* 
        $em = $this->getDoctrine()->getManager(); 
        $param= new ConfigExtension($em);
        $em->getRepository(Config::class)->updateBy('logo',"2.png");
        dd($param->app_config('logo')); */
      
        $utilisateur = new Utilisateurs();
        $form = $this->createForm(UtilisateursType::class, $utilisateur);

      //  $form['nom']->setData($utilisateur->getPersonnel()->getNom()) ; 
      //  $form['prenom']->setData($utilisateur->getPersonnel()->getPrenom()) ;
      //  $img = $utilisateur->getPersonnel()->getImageName() ;


        $form->handleRequest($request);
           //&& $form->isValid()
       if ($form->isSubmitted() ) {

        $utilisateur->setPassword(
            $passwordEncoder->encodePassword(
                $utilisateur,
                $form->get('password')->getData()
            )
        );

        $image = $form->get('imageFile')->getData();
        if(!empty($image)){
            $imageName = $fileUploader->upload($image);
            $utilisateur->setImageName($imageName);
        }else{
            $utilisateur->setImageName('default.png');
        }
        
            $utilisateur->getPersonnel()->setNom($form['nom']->getData());  ////////////
            $utilisateur->getPersonnel()->setPrenom($form['prenom']->getData());///////////
            $img = $utilisateur->getPersonnel()->getImageName() ;///////////////

            $utilisateursRepository->save($utilisateur, true);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateurs/new-utilisateurs.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,

        ]);
    }

  /*   #[Route('/utilisateurs/{id}/show', name: 'app_utilisateurs_show', methods: ['GET'])]
    public function show(Utilisateurs $utilisateur): Response
    {
        return $this->render('utilisateurs/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }  */

    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/utilisateurs_{id}_edit', name: 'app_utilisateurs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateurs $utilisateur, UtilisateursRepository $utilisateursRepository ): Response
    {
        $codes_old=$utilisateur->getCodes();
        $roles_old=$utilisateur->getRoles();
       
        $form = $this->createForm(UtilisateursType::class, $utilisateur);
        $form->handleRequest($request);
        


        if ($form->isSubmitted()) {
            $searchParam = $request->get('searchParam');
            
            if($searchParam==null){
                $codes=$codes_old ;
            }else{
                extract($searchParam);
                foreach($codes as $code){
                    if(!in_array($code,$codes_old)){
                        array_push($codes_old,$code);
                    }
                }
                
            }
            foreach($utilisateur->getRoles() as $role ){
                if(!in_array($role,$roles_old) && $role!="ROLE_USER"){
                    array_push($roles_old,$role);
                }
            }
            
            $utilisateur->setRoles($roles_old);
            $utilisateur->setCodes($codes_old);
            $image = $form->get('imageFile')->getData();
            if(!empty($image)){
                if($utilisateur->getImageName() != 'anonymous.png'){
                    unlink($this->getParameter('brochures_directory').'/'.$utilisateur->getImageName());
                }
                
                $fileUploader = new FileUploader($this->getParameter('brochures_directory'));
                $imageName = $fileUploader->upload($image);
                $utilisateur->setImageName($imageName);
            }
            
            $utilisateursRepository->save($utilisateur, true);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");

            return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateurs/edit-utilisateurs.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,

        ]);
    }
    /**
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') ")
     */
    #[Route('/utilisateurs_{id}_{_token}', name: 'app_utilisateurs_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Utilisateurs $utilisateur, UtilisateursRepository $utilisateursRepository,$_token): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $_token)) {
            $utilisateursRepository->remove($utilisateur, true);
        }
        return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     *
     * @Security("is_granted('ROLE_USER') ")
     */
    #[Route(path: '/utilisateurs_profile', name: 'app_profile_user')]
    public function profile_user(Request $request , secure $security , UtilisateursRepository $utilisateursRepository): Response
    {

        $usr = $security->getUser();
        

        $form = $this->createForm(ProfileUserType::class, $usr);
        $form['nom']->setData($usr->getPersonnel()->getNom()) ; 
        $form['prenom']->setData($usr->getPersonnel()->getPrenom()) ;
        $img = $usr->getPersonnel()->getImageName() ;
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            // encode the plain password
            $image = $form->get('imageFile')->getData();
     

            if(!empty($image)){
                if($usr->getImageName() != 'anonymous.png'){
                    unlink($this->getParameter('brochures_directory').'/'.$usr->getImageName());
                }
                
                $fileUploader = new FileUploader($this->getParameter('brochures_directory'));
                $imageName = $fileUploader->upload($image);
                $usr->getPersonnel()->setImageName($imageName); 
                $usr->setImageName($imageName); 
            }
            

        ///    $personnel->getIdUser()->setEmail($form['email']->getData()) ;
            $usr->getPersonnel()->setNom($form['nom']->getData());
            $usr->getPersonnel()->setPrenom($form['prenom']->getData());
       

            $toto = $utilisateursRepository->save($usr, true);

            return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
            $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
            // do anything else you need here, like send an email

            
        }

        return $this->render('profile_user/profile_user.html.twig', [
            'Profile' => $usr ,
            'ProfileUser' => $form->createView(),
            'img' => $img
        
        ]);
       
    }

     

    /**
     *
     * @Security("is_granted('ROLE_USER') ")
     */
    #[Route(path: '/utilisateurs_pep', name: 'app_profile_user_pep')]
    public function profile_user_change_pass(Request $request , secure $security , UtilisateursRepository $utilisateursRepository , UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(ProfileUserEditPassType::class, $user);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid() ) {
            

            $old_pwd = $form->get('oldPassword')->getData();
            $new_pwd = $form->get('password')['first']->getData(); 
         //  dd($old_pwd);
            $checkPass = $passwordEncoder->isPasswordValid($user ,$old_pwd) ;     
            // encode the plain password
            if ($checkPass === true) {  

                $this->get('session')->getFlashBag()->add('success', "MOD_SUCCESS");
                    
                    $encoded = $passwordEncoder->encodePassword($user, $new_pwd );
                    $user->setPassword($encoded);
                   // $em->merge($user);
                   // $em->flush();
                    $utilisateursRepository->save($user, true);
                    return new RedirectResponse($this->generateUrl('app_login'));
                
                
            } else {  
                $this->get('session')->getFlashBag()->add('success', "MOD_DANGER");
                
                return new RedirectResponse($this->generateUrl('app_profile_user_pep'));    
            }
            
            // do anything else you need here, like send an email

            
        }

        return $this->render('profile_user/profile_user_edit_pass.html.twig', [
            'ProfileUserEditPass' => $form->createView(),
            
        ]);
    }



    #[Route(path: '/info_by_role', name: 'info_by_role')]
    public function info_by_role(Request $request): Response
    {
        $result = null;
        $role = $request->get('role');
        $em = $this->getDoctrine()->getManager();

        if($role =="ROLE_CHEF_SERV"){
            $result =$em->getRepository(Utilisateurs::class)->get_serv();
        }

        if($role =="ROLE_CHEF_FIL"){
            $result =$em->getRepository(Utilisateurs::class)->get_fil();
        }

        if($role =="ROLE_CHEF_DEP"){
            $result =$em->getRepository(Utilisateurs::class)->get_dep();
        }

        if($role =="ROLE_DIR_ADJ"){
            $result =$em->getRepository(Utilisateurs::class)->get_dir();
        }
        if($role =="ROLE_CHEF_STRUCT"){
            $result =$em->getRepository(Utilisateurs::class)->get_struct();
        }
        return new JsonResponse($result);

    }

 


}
