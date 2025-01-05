<?php

namespace App\Repository;

use App\Entity\Personnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\DriverManager;



/**
 * @extends ServiceEntityRepository<Personnel>
 *
 * @method Personnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnel[]    findAll()
 * @method Personnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnel::class);
    }

    public function save(Personnel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Personnel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }





    public function search($searchParam) {
       
        extract($searchParam);   

        $query = $this->createQueryBuilder('p');

        if(!empty($ids))
           $query->andWhere('p.id in (:ids)')->setParameter('ids', $ids);

        if(!empty($perPage))
            $query->setFirstResult(($page - 1) * $searchParam['perPage'])->setMaxResults($searchParam['perPage']);
        
        if(!empty($keyword)){
            $query->andWhere('p.nom like :keyword')->setParameter('keyword', '%'.$keyword.'%');
            $query->orWhere('p.prenom like :keyword1')->setParameter('keyword1', '%'.$keyword.'%');
            $query->orWhere('p.numPpr like :keyword2')->setParameter('keyword2', '%'.$keyword.'%');
        }

        $paginator = new Paginator($query->getQuery(),false);

        return $paginator;

        
    }

     public function counter() {
        $sql = 'SELECT count(p) FROM App\Entity\personnel p';
        $query = $this->_em->createQuery($sql);
         
      return $query->getOneOrNullResult();
    }

    public function PersonnelsParDepartement() {

        $query="select  count(p.id) nb, d.libelle_dep from personnel p left Join departement d on d.id = p.departement_id_id where activite = 'N'  group By d.libelle_dep  ";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
     
        return  $result ;

    }

    public function PersonnelsParType() {

        $query="select count(p.id) nb, t.libelle_personnel , t.id from personnel p left Join type_personnel t on t.id = p.type_personnel_id_id where activite = 'N' group By t.id;";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
   
        return  $result ;

    }

    public function PersonnelsParGenre() {

        $query="select count(p.id) nb, p.genre from personnel p where activite = 'N' group By p.genre;";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
   
        return  $result ;

    }

     public function PersonnelsEffectifevolution() {

        $query="select  DISTINCT EXTRACT( YEAR FROM p.date_affectation_ensat ) year, count(p.id) nb from personnel p left Join type_personnel t on t.id = p.type_personnel_id_id  where p.date_affectation_ensat is not null  group By year";
      
      
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative();  
      
     
       return $result ;

    }

    public function PersonnelsNbYearProf() {

        $query="select  DISTINCT EXTRACT( YEAR FROM p.date_affectation_ensat ) year, count(p.id) nb from personnel p left Join type_personnel t on t.id = p.type_personnel_id_id  where t.id <> 2 and p.date_affectation_ensat is not null group By year";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative();  

      
        return  $result;

    }

    public function PersonnelsNbYearAd() {

        $query="select  DISTINCT EXTRACT( YEAR FROM p.date_affectation_ensat ) year, count(p.id) nb from personnel p left Join type_personnel t on t.id = p.type_personnel_id_id  where t.id = 2 and p.date_affectation_ensat is not null group By year";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative();  

       // $stmt = $this->getEntityManager()->getConnection()->prepare($query);
       // $stmt->execute();
        return  $result;

    }

    public function PersonnelsNbProf() {

        $query="select  count(p.id) nb from personnel p left Join type_personnel t on t.id = p.type_personnel_id_id  where t.id <> 2 and activite = 'N' ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute();
        return  $stmt->fetchAll();

    }

    public function PersonnelsNbAd() {

        $query="select  count(p.id) nb from personnel p left Join type_personnel t on t.id = p.type_personnel_id_id  where t.id = 2 and activite = 'N' ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute();
        return  $stmt->fetchAll();

    }


    public function PersonnelsParCorpsEnseignant() {

        $query="select count(p.id) nb, c.designation_fr from personnel p left Join corps c on c.id = p.corps_id_id where p.type_personnel_id_id <> 2 and activite = 'N' group By c.designation_fr";
       // $stmt = $this->getEntityManager()->getConnection()->prepare($query);
       // $stmt->execute();
       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  

        return $result ;// $stmt->fetchAll();

    }

    public function PersonnelsParCorpsAdmin() {

        $query="select count(p.id) nb, g.designation_fr from personnel p left Join grades g on g.id = p.grade_id_id where p.type_personnel_id_id in (2,4) and activite = 'N' group By g.designation_fr";
     
       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;//$stmt->fetchAll();

    }

    public function PersonnelsParActivite() {

        $query="select count(p.id) nb, p.activite from personnel p group By p.activite";
  
       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;

    }

    public function PersonnelsParService() {

        $query="select count(p.id) nb, s.nom_service from personnel p left Join service s on s.id = p.service_affectation_id_id where p.type_personnel_id_id in (2,4) and activite = 'N' group By s.nom_service;";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ; 

    }



    public function nb_dem_at_trav($statut) 
    {
        $query=" SELECT COUNT(att.id) as n  FROM `attestation` att where att.type = 'AT' and att.statut = '".$statut."'";
        if($statut=="T"){
        $query=" SELECT COUNT(att.id) as n  FROM `attestation` att where att.type = 'AT' ";
        }
        if($statut=="TR"){
       $query=" SELECT COUNT(att.id) as n  FROM `attestation` att where att.type = 'AT' and att.statut not in ('-1') ";
            }
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $r = $statement->executeQuery()->fetchAllAssociative();  
        return $r;
    }

    public function nb_dem_at_sal($statut) 
    {
        $query=" SELECT COUNT(att.id) as n  FROM `attestation` att where att.type = 'AS' and att.statut = '".$statut."'";
        if($statut=="T"){
        $query=" SELECT COUNT(att.id) as n  FROM `attestation` att where att.type = 'AS' ";
        }
        if($statut=="TR"){
       $query=" SELECT COUNT(att.id) as n  FROM `attestation` att where att.type = 'AS' and att.statut not in ('-1') ";
            }
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $r = $statement->executeQuery()->fetchAllAssociative();  
        return $r;
    }


    public function nb_dem_auto($statut) 
    {
        $query=" SELECT COUNT(autos.id) as n  FROM `autorisation` autos where autos.statut = '".$statut."'";
        if($statut=="T"){
        $query=" SELECT COUNT(autos.id) as n  FROM `autorisation` autos   ";
        }
        if($statut=="TR"){
       $query=" SELECT COUNT(autos.id) as n  FROM `autorisation` autos where autos.statut not in ('-1','0') ";
            }
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $r = $statement->executeQuery()->fetchAllAssociative();  
        return $r;
    }


    
    public function nb_dem_conge($statut) 
    {
        $query=" SELECT COUNT(c.id) as n  FROM `conge` c where c.statut = '".$statut."'";
        if($statut=="T"){
        $query=" SELECT COUNT(c.id) as n  FROM `conge` c   ";
        }
        if($statut=="TR"){
       $query=" SELECT COUNT(c.id) as n  FROM `conge` c where c.statut not in ('-1','0') ";
            }
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $r = $statement->executeQuery()->fetchAllAssociative();  
        return $r;
    }


    public function nb_dem_om($statut) 
    {
        $query=" SELECT COUNT(om.id) as n  FROM `ordre_mission` om where om.statut = '".$statut."'";
        if($statut=="T"){
        $query=" SELECT COUNT(om.id) as n  FROM `ordre_mission` om   ";
        }
        if($statut=="TR"){
       $query=" SELECT COUNT(om.id) as n  FROM `ordre_mission` om where om.statut not in ('-1','0') ";
            }
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $r = $statement->executeQuery()->fetchAllAssociative();  
        return $r;
    }

    public function nb_dem_fh($statut) 
    {
        $query=" SELECT COUNT(fh.id) as n FROM `ficheheure` fh where fh.statut = '".$statut."'";
        if($statut=="T"){
        $query=" SELECT COUNT(fh.id) as n FROM `ficheheure` fh ";
        }
        if($statut=="TR"){
       $query=" SELECT COUNT(fh.id) as n FROM `ficheheure` fh where fh.statut not in ('-1','0') ";
            }
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $r = $statement->executeQuery()->fetchAllAssociative();  
        return $r;
    }



    public function get_dem_by_niveau($var , $service, $niveau , $code) {  


        if($var == "TR_FH"){
        
                if($service){
                    $query="select count(h.id) as n from ficheheure h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";
                }else{
                    $query="select count(h.id) as n from ficheheure h WHERE h.statut not in ('0','1','2','-3') and h.niveau='".$niveau."' order by h.id desc";    
                }
                if($code && $niveau =="ROLE_DIR_ADJ" ){
                    $query="select count(h.id) as n from ficheheure h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') 
                    
                    
                    order by h.id desc";
                }
                if($code && $niveau =="ROLE_CHEF_DEP" ){
                    $query="select count(h.id) as n from ficheheure h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
                }
                if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                    $query="select count(h.id) as n from ficheheure h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

                }

             
        }
        
        if($var == "TR_OM"){
        
            if($service){
                $query="select count(h.id) as n from ordre_mission h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";
            }else{
                $query="select count(h.id) as n from ordre_mission h WHERE h.statut not in ('0','1','2','-3') and h.niveau='".$niveau."' order by h.id desc";    
            }
        
            if($code && $niveau =="ROLE_CHEF_DEP" ){
                $query="select count(h.id) as n from ordre_mission h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                $query="select count(h.id) as n from ordre_mission h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

            }

            if($code && $niveau =="ROLE_DIR_ADJ" ){

                $query_adm="SELECT count(h.id) as n from ordre_mission h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id in (select p.id from personnel p where p.service_affectation_id_id in (select s.id from service s where s.role_superieur = '".$niveau."' and s.codes = ".$code." ) and p.type_personnel_id_id = 2 )
                order by h.id desc";

                $query_pr = null ;

                $statement = $this->getEntityManager()->getConnection()->prepare($query_adm);
                $result_1 = $statement->executeQuery()->fetchAllAssociative()[0]; 

                if($code==1){
                    $query_pr="SELECT count(h.id) as n from ordre_mission h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.type_mission !='R' and h.personnel_id in (select p.id from personnel p where p.type_personnel_id_id !=2)  
                    order by h.id desc";
                }

                if($code==2){
                    $query_pr="SELECT count(h.id) as n from ordre_mission h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.type_mission ='R' and h.personnel_id in (select p.id from personnel p where p.type_personnel_id_id !=2)  
                    order by h.id desc";
                }
              
                $statement = $this->getEntityManager()->getConnection()->prepare($query_pr);
                $result_2 = $statement->executeQuery()->fetchAllAssociative()[0]; 

                $n = $result_1['n'] + $result_2['n'] ;
                $result_1['n'] = $n ;

                return $result_1 ;

              
           
            }
            
        }
        
        if($var == "TR_AUTO"){
        
            if($service){
                $query="select count(h.id) as n from autorisation h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";       
            }else{
                $query="select count(h.id) as n from autorisation h WHERE h.statut not in ('0','1','2','-3') and h.niveau='".$niveau."' order by h.id desc";    
            }
            if($code && $niveau =="ROLE_DIR_ADJ" ){
                $query="select count(h.id) as n from autorisation h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.service_affectation_id_id in (select s.id from service s where s.role_superieur='".$niveau."' and s.codes =".$code.") )  order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_DEP" ){
                $query="select count(h.id) as n from autorisation h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                $query="select count(h.id) as n from autorisation h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

            }
                
            }
        
       
        
        
        if($var == "TR_CONGE"){
        
            if($service){
                $query="select count(h.id) as n from conge h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";
            }else{
                $query="select count(h.id) as n from conge h WHERE h.statut not in ('0','1','2','-3') and h.niveau='".$niveau."' order by h.id desc";    
            }
            if($code && $niveau =="ROLE_DIR_ADJ" ){
                $query="select count(h.id) as n from conge h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.service_affectation_id_id in (select s.id from service s where s.role_superieur='".$niveau."' and s.codes =".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_DEP" ){
                $query="select count(h.id) as n from conge h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                $query="select count(h.id) as n from conge h WHERE h.niveau='".$niveau."' and h.statut not in ('0','1','2','-3') and h.personnel_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

            }
                
            }


         if($var == "TR_ATT"){
        
   
         $query="select count(c.id) as n  from attestation c WHERE c.statut not in ('1','2')  order by c.id desc";
                   
                 
                   }

        
        if($var == "T_CONGE")
        {
          
            if($service){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='conge' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";
            }else{
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='conge' and h.niveau='".$niveau."' order by h.id desc";    
            }
            if($code && $niveau =="ROLE_DIR_ADJ" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='conge' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.service_affectation_id_id in (select s.id from service s where s.role_superieur='".$niveau."' and s.codes =".$code.") )  order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_DEP" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='conge' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='conge' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

            }
                
        }
        
         if($var == "T_FH")
         {
           
            if($service){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='fiche heure' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";
            }else{
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='fiche heure' and h.niveau='".$niveau."' order by h.id desc";    
            }
            if($code==1 && $niveau =="ROLE_DIR_ADJ" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='fiche heure' and h.niveau='".$niveau."'
                 
                order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_DEP" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='fiche heure' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='fiche heure' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

            } 
            
         }
         
        
          if($var == "T_OM")
         {
           
            if($service){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='ordre de mission' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";
            }else{
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='ordre de mission' and h.niveau='".$niveau."' order by h.id desc";    
            }
            if($code && $niveau =="ROLE_DIR_ADJ" ){


                if($code==1){

                    $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='ordre de mission' and h.niveau='".$niveau."' and 
                    ( h.demandeur_id in (select p.id from personnel p where p.service_affectation_id_id in (select s.id from service s where s.role_superieur = '".$niveau."' and s.codes = ".$code." ) and p.type_personnel_id_id = 2  ) or h.demandeur_id in (select p.id from personnel p where p.type_personnel_id_id !=2 ) and h.id_demande in (select o.id from ordre_mission o where o.type_mission !='R')  )
                       
                    order by h.id desc";
                }


                if($code==2){

                    $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='ordre de mission' and h.niveau='".$niveau."' and 
                    ( h.demandeur_id in (select p.id from personnel p where p.service_affectation_id_id in (select s.id from service s where s.role_superieur = '".$niveau."' and s.codes = ".$code." ) and p.type_personnel_id_id = 2  ) or h.demandeur_id in (select p.id from personnel p where p.type_personnel_id_id !=2 ) and h.id_demande in (select o.id from ordre_mission o where o.type_mission ='R')  )
                       
                    order by h.id desc";
                }




            }
            if($code && $niveau =="ROLE_CHEF_DEP" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='ordre de mission' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='ordre de mission' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

            }
          }
        
          if($var == "T_AUTO")
        {
              
            if($service){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='autorisation' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.service_affectation_id_id  = ".$service." ) order by h.id desc";
            }else{
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='autorisation' and h.niveau='".$niveau."' order by h.id desc";    
            }
            if($code && $niveau =="ROLE_DIR_ADJ" ){
                
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='autorisation' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.service_affectation_id_id in (select s.id from service s where s.role_superieur='".$niveau."' and s.codes =".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_DEP" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='autorisation' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.departement_id_id  in (".$code.") ) order by h.id desc";
            }
            if($code && $niveau =="ROLE_CHEF_STRUCT" ){
                $query="select count(h.id) as n from histo_demandes h WHERE h.type_demande ='autorisation' and h.niveau='".$niveau."' and h.demandeur_id  in (select p.id from personnel p where p.structure_rech_id  in (".$code.")  ) order by h.id desc";

            }
        }
          



           if($var == "T_ATT")
           {
             
            $query="select count(c.id) as n from attestation c  order by c.id desc";
                  
                   
            }
            $statement = $this->getEntityManager()->getConnection()->prepare($query);
            $result = $statement->executeQuery()->fetchAllAssociative();  


        /*     $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult(); */
            return  $result ;
        }


        public function get_resps($role , $code) {
            if($code){
                $query = " SELECT CONCAT(p.nom , ' ' ,p.prenom) as chef FROM personnel p where p.id_user_id = (SELECT u.id FROM utilisateurs u where u.roles like '%".$role."\"%' and u.codes like '%".$code."\"%')   ";
              //  dd($query);

            }else{
                $query = " SELECT CONCAT(p.nom , ' ' ,p.prenom) as chef FROM personnel p where p.id_user_id = (SELECT u.id FROM utilisateurs u where u.roles like '%".$role."\"%' )   ";

            }


            $statement = $this->getEntityManager()->getConnection()->prepare($query);
            $result = $statement->executeQuery()->fetchAllAssociative(); 

          //  dd($result);

            if($result){
                $result = $result[0]['chef'];
            }else{
                $result = null ;
            }
            return $result   ;

         }




         public function Tableau_calendrier_perso_om($annee) {

            $query="select p.id as p_id , p.nom , p.prenom , o.id  as o_id, o.date_debut , o.date_fin from personnel p left Join ordre_mission o on o.personnel_id = p.id where o.statut=1 and o.date_debut >= '".$annee."-01-01' 
            and o.date_debut <= '".$annee."-12-31' ORDER by p.id , o.date_debut , o.date_fin";
            $statement = $this->getEntityManager()->getConnection()->prepare($query);
            $result = $statement->executeQuery()->fetchAllAssociative(); 
         
            return  $result ;
    
        }

        
        public function Tableau_calendrier_perso_auto($annee) {

            $query="select p.id as p_id , p.nom , p.prenom , a.id  as a_id, a.date_sortie , a.date_rentree from personnel p left Join autorisation a on a.personnel_id = p.id where  a.statut=1 and a.date_sortie >= '".$annee."-01-01' 
            and a.date_sortie <= '".$annee."-12-31' ORDER by p.id , a.date_sortie , a.date_rentree";
            $statement = $this->getEntityManager()->getConnection()->prepare($query);
            $result = $statement->executeQuery()->fetchAllAssociative(); 
         
            return  $result ;
    
        }

        public function Tableau_calendrier_perso_conge($annee) {

            $query="select p.id as p_id , p.nom , p.prenom , c.id  as c_id, c.date_debut , c.date_reprise from personnel p left Join conge c on c.personnel_id = p.id where  c.statut=1 and c.date_debut >= '".$annee."-01-01' 
            and c.date_debut <= '".$annee."-12-31' ORDER by p.id , c.date_debut , c.date_reprise";
            $statement = $this->getEntityManager()->getConnection()->prepare($query);
            $result = $statement->executeQuery()->fetchAllAssociative(); 
         
            return  $result ;
    
        }

//    /**
//     * @return Personnel[] Returns an array of Personnel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Personnel
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
