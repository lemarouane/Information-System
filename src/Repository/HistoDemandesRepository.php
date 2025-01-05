<?php

namespace App\Repository;

use App\Entity\HistoDemandes;
use App\Entity\Personnel;
use App\Entity\PersonnelRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoDemandes>
 *
 * @method HistoDemandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoDemandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoDemandes[]    findAll()
 * @method HistoDemandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoDemandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoDemandes::class);
    }

    public function save(HistoDemandes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HistoDemandes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function Histo_validations($id_validateur) { 

        $query="select h from App\Entity\HistoDemandes h where h.validateur =" .$id_validateur." and h.dateValidation is not null order by h.dateValidation desc";

        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        return  $result ;

    }

    public function Histo_Demandes($id_personnel) { 

        $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd where hd.demandeur ="
        .$id_personnel."  group by hd.id_demande) order by h.id desc";

        //$statement = $this->getEntityManager()->getConnection()->prepare($query);
        //$result = $statement->executeQuery(); 
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        return  $result ;

    }


    public function Histo_Demandes_Generale() { 

        $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd group by hd.id_demande) order by h.id desc";

        //$statement = $this->getEntityManager()->getConnection()->prepare($query);
        //$result = $statement->executeQuery(); 
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        return  $result ;

    }


    public function Histo_Demandes_Generale_var($var) { 
        if($var == "T"){
            $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd group by hd.id_demande) order by h.id desc";
        }
        if($var == "NT_ATT"){
            $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd group by hd.id_demande) and h.type_demande ='attestation' and h.statut not in ('1','2') order by h.id desc";
        }
        
        if($var == "NT_AUTO"){
            $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd group by hd.id_demande) and h.type_demande ='autorisation' and h.statut not in ('1','2')  order by h.id desc";
        }
        if($var == "NT_CONGE"){
            $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd group by hd.id_demande) and h.type_demande ='conge' and h.statut not in ('1','2')  order by h.id desc";
        }
        
        if($var == "NT_OM"){
            $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd group by hd.id_demande) and h.type_demande ='ordre de mission' and h.statut not in ('1','2')  order by h.id desc";
        }
        
        if($var == "NT_FH"){
            $query="select h from App\Entity\HistoDemandes h WHERE h.id in (SELECT max(hd.id) FROM App\Entity\HistoDemandes hd group by hd.id_demande) and h.type_demande ='fiche heure' and h.statut not in ('1','2')  order by h.id desc";
        }
        
                //$statement = $this->getEntityManager()->getConnection()->prepare($query);
                //$result = $statement->executeQuery(); 
                $query = $this->getEntityManager()->createQuery($query);
                $result = $query->getResult();
                return  $result ;
        
            }


  public function Get_Fonc_By_Service($service_id) { 

  $result_str= "";
  $fonctionnaires_de_service = "select p.id from App\Entity\Personnel p WHERE p.serviceAffectationId = ". $service_id;
  $result = $this->getEntityManager()->createQuery($fonctionnaires_de_service)->getResult();
  $len = count($result);
  $i = 0;
   foreach ($result as $value){
    $i++;
    if($i < $len){
    $result_str = $result_str .$value["id"].",";
     }else{
        $result_str = $result_str .$value["id"];
     }

    }
    return  $result_str ;
  }



  public function Get_Fonc_By_Services_SG() { 

    $result_str= "";
    $services = "select s.id from App\Entity\Service s WHERE s.roleSuperieur = 'ROLE_SG' ";
    $result = $this->getEntityManager()->createQuery($services)->getResult();

    $len = count($result);
    $i = 0;
     foreach ($result as $value){
      $i++;
      if($i < $len){
      $result_str = $result_str .$value["id"].",";
       }else{
          $result_str = $result_str .$value["id"];
       }
      }

      $chefs_de_service = "select p.id  from App\Entity\Personnel p INNER JOIN App\Entity\Utilisateurs u 
      WHERE u.id = p.idUser and u.roles like '%ROLE_CHEF_SERV%' and p.serviceAffectationId in(".$result_str.")";
      $result = $this->getEntityManager()->createQuery($chefs_de_service)->getResult();

      $result_str= "";
      $len = count($result);
      $i = 0;
       foreach ($result as $value){
        $i++;
        if($i < $len){
        $result_str = $result_str .$value["id"].",";
         }else{
            $result_str = $result_str .$value["id"];
         }
        }


      return  $result_str ;
    }




    public function Get_Fonc_By_Services_DirAdj($code) { 

        $result_str= "";

      
        $code_str=implode("','", $code);
        //  $code_str = "DIR_1" ;

        $services = "select s.id from App\Entity\Service s WHERE s.roleSuperieur = 'ROLE_DIR_ADJ' and concat('DIR_', s.codes) in ('".$code_str."')";
     
        $result = $this->getEntityManager()->createQuery($services)->getResult();
    
        $len = count($result);
        $i = 0;
         foreach ($result as $value){
          $i++;
          if($i < $len){
          $result_str = $result_str .$value["id"].",";
           }else{
              $result_str = $result_str .$value["id"];
           }
          }
    
          $chefs_de_service = "select p.id  from App\Entity\Personnel p INNER JOIN App\Entity\Utilisateurs u 
          WHERE u.id = p.idUser and u.roles like '%ROLE_CHEF_SERV%' and p.serviceAffectationId in(".$result_str.")";
          $result = $this->getEntityManager()->createQuery($chefs_de_service)->getResult();
    
          $result_str= "";
          $len = count($result);
          $i = 0;
           foreach ($result as $value){
            $i++;
            if($i < $len){
            $result_str = $result_str .$value["id"].",";
             }else{
                $result_str = $result_str .$value["id"];
             }
            }
    
    
          return  $result_str ;
        }
    



 



        public function Histo_Demandes_Reprises($ids_personnel , $current_personnel) { 

            $result = NULL ;
            $query="select h.id , h.date_reprise , h.type_demande , p.nom , p.prenom from App\Entity\HistoDemandes h INNER JOIN App\Entity\Personnel p  WHERE p.id=h.demandeur and p.typePersonnelId = 2 and p.id!= ".$current_personnel." and h.demandeur in (".$ids_personnel.") and h.statut = '1' and h.reprise is null and h.date_reprise <= '".date("Y-m-d")."'" ;
            if($ids_personnel!=""){
                $query = $this->getEntityManager()->createQuery($query);
                $result = $query->getResult();
            }
           
            return  $result ;
           
        }


        public function Histo_Demandes_Reprises_RH() { 

            $result = NULL ;
            $query="select h.id , h.date_reprise , h.reprise  , h.type_demande , p.nom , p.prenom from App\Entity\HistoDemandes h INNER JOIN App\Entity\Personnel p  WHERE p.id=h.demandeur and p.typePersonnelId = 2 and h.statut = '1' and ( h.reprise is null or h.reprise = '0') and h.date_reprise <= '".date("Y-m-d")."'" ;
            $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult();
            
           
            return  $result ;
           
        }

    public function findByExampleField($value): array
       {

        $subquery = $this->_em->createQueryBuilder()
        ->select('h.id')
        ->from('histoDemandes', 'h')
        ->where('h.demandeurId = :id_demandeur')
        ->setParameter('id_demandeur', $value);

           return $this->createQueryBuilder('h')
               ->andWhere('h.id = :val')
               ->setParameter('val', $value)
               ->orderBy('h.id', 'ASC')
               ->getQuery()
               ->getResult()
           ; 
       }


       public function get_dem_current($type,$entite,$code) { 

        $niveau = null;

        switch ($entite) {
            case 'DIR': $niveau = 'ROLE_DIR';
            break;
            case 'SER': $niveau = 'ROLE_CHEF_SERV';
            break;
            case 'DIRADJ':$niveau = 'ROLE_DIR_ADJ';
            break;
            case 'DEP':$niveau = 'ROLE_CHEF_DEP';
            break;
            case 'STR':$niveau = 'ROLE_CHEF_STRUCT';
            break;   
            case 'RH':$niveau = 'ROLE_RH';
            break;   
            case 'SG':$niveau = 'ROLE_SG';
            break;  
         
        }

       

        if($type == 'AUTO'){

            if($entite=='SER'){
                $query="SELECT a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateSortie , a.dateRentree FROM App\Entity\Autorisation a , App\Entity\Personnel pp  WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (".$code.") ) ORDER BY a.id desc";
            }else{
                $query="SELECT a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateSortie , a.dateRentree FROM App\Entity\Autorisation a , App\Entity\Personnel pp WHERE pp.id = a.personnel  AND a.niveau='".$niveau."' and a.statut IN ('-1') ORDER BY a.id desc";
            }

            if($entite=='DIRADJ'){
                $query="SELECT a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateSortie , a.dateRentree FROM App\Entity\Autorisation a , App\Entity\Personnel pp 
                 WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (SELECT s.id FROM App\Entity\Service s WHERE s.roleSuperieur = '".$niveau."' AND s.codes in (".$code.")  ) ) ORDER BY a.id desc";
            }





        }


        if($type == 'CONGE'){

            if($entite=='SER'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateReprise , a.nbJour , a.typeConge FROM App\Entity\Conge a , App\Entity\Personnel pp WHERE a.niveau='".$niveau."' and a.statut IN ('-1') AND a.personnel IN (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (".$code.") ) ORDER BY a.id desc";
            }else{
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur, a.dateEnvoie , a.dateDebut , a.dateReprise , a.nbJour , a.typeConge FROM App\Entity\Conge a , App\Entity\Personnel pp WHERE pp.id = a.personnel AND a.niveau='".$niveau."' and a.statut IN ('-1') ORDER BY a.id desc";
            }

            if($entite=='DIRADJ'){
                $query="SELECT a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur, a.dateEnvoie , a.dateDebut , a.dateReprise , a.nbJour , a.typeConge FROM App\Entity\Conge a , App\Entity\Personnel pp 
                 WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (SELECT s.id FROM App\Entity\Service s WHERE s.roleSuperieur = '".$niveau."' AND s.codes in (".$code.")  ) ) ORDER BY a.id desc";
            }



        }
			
        if($type == 'OM'){

            if($entite=='SER'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission FROM App\Entity\OrdreMission a , App\Entity\Personnel pp WHERE a.niveau='".$niveau."' and a.statut IN ('-1') AND a.personnel IN (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (".$code.") ) ORDER BY a.id desc";
            }else{
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur, a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission FROM App\Entity\OrdreMission a , App\Entity\Personnel pp WHERE pp.id = a.personnel AND a.niveau='".$niveau."' and a.statut IN ('-1') ORDER BY a.id desc";
            }

            if($entite=='DEP'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission  FROM App\Entity\OrdreMission a , App\Entity\Personnel pp 
                 WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.departementId in (".$code.") ) ORDER BY a.id desc";
            }

            if($entite=='STR'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission  FROM App\Entity\OrdreMission a , App\Entity\Personnel pp 
                 WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.structureRech in (".$code.") ) ORDER BY a.id desc";
            }


             if($entite=='DIRADJ'){

                if($code == '1'){
                    $query="
                    SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission FROM App\Entity\OrdreMission a , App\Entity\Personnel pp 
                    WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND pp.typePersonnelId = 2 AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (SELECT s.id FROM App\Entity\Service s WHERE s.roleSuperieur = '".$niveau."' AND s.codes in (".$code.")  ) ) ";

                    $query = $this->getEntityManager()->createQuery($query);
                    $result1 = $query->getResult();
             
                    $query = "SELECT a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission FROM App\Entity\OrdreMission a , App\Entity\Personnel pp 
                    WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND pp.typePersonnelId != 2 AND a.typeMission!='R' ";

                    $query = $this->getEntityManager()->createQuery($query);
                    $result2 = $query->getResult();
      
                    $result =  array_merge($result1 ,$result2 );

                    return  $result ;
                }

                if($code == '2'){
                    $query="
                    SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission FROM App\Entity\OrdreMission a , App\Entity\Personnel pp 
                    WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND pp.typePersonnelId = 2  AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (SELECT s.id FROM App\Entity\Service s WHERE s.roleSuperieur = '".$niveau."' AND s.codes in (".$code.")  ) ) ";
                    $query = $this->getEntityManager()->createQuery($query);
                    $result1 = $query->getResult();

               

                    $query= "SELECT a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.dateDebut , a.dateFin , a.structureAcceuil , a.typeMission FROM App\Entity\OrdreMission a , App\Entity\Personnel pp 
                    WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND pp.typePersonnelId != 2 AND a.typeMission='R' ";

                    $query = $this->getEntityManager()->createQuery($query);
                    $result2 = $query->getResult();
   
                    $result =  array_merge($result1 ,$result2 );

                    return  $result ;
                }


              
                
        }
    }

        if($type == 'FH'){

            if($entite=='SER'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.moisDebut , a.moisFin , a.etablissement  FROM App\Entity\Ficheheure a , App\Entity\Personnel pp WHERE a.niveau='".$niveau."' and a.statut IN ('-1') AND a.personnel IN (SELECT p.id FROM App\Entity\Personnel p WHERE p.serviceAffectationId in (".$code.") ) ORDER BY a.id desc";
            }else{
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.moisDebut , a.moisFin , a.etablissement FROM App\Entity\Ficheheure a , App\Entity\Personnel pp WHERE pp.id = a.personnel AND a.niveau='".$niveau."' and a.statut IN ('-1') ORDER BY a.id desc";
            }

            if($entite=='DIRADJ'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.moisDebut , a.moisFin , a.etablissement FROM App\Entity\Ficheheure a , App\Entity\Personnel pp 
                 WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') ";
            }

            if($entite=='DEP'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.moisDebut , a.moisFin , a.etablissement FROM App\Entity\Ficheheure a , App\Entity\Personnel pp 
                 WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.departementId in (".$code.") ) ORDER BY a.id desc";
            }

            if($entite=='STR'){
                $query="SELECT  a.id , CONCAT(pp.nom ,' ', pp.prenom) as demandeur , a.dateEnvoie , a.moisDebut , a.moisFin , a.etablissement FROM App\Entity\Ficheheure a , App\Entity\Personnel pp 
                 WHERE pp.id = a.personnel AND a.niveau='".$niveau."' AND a.statut IN ('-1') AND a.personnel in (SELECT p.id FROM App\Entity\Personnel p WHERE p.structureRech in (".$code.") ) ORDER BY a.id desc";
            }


        }

      


      //  $statement = $this->getEntityManager()->getConnection()->prepare($query);
       // $result = $statement->getResult(); 

       $query = $this->getEntityManager()->createQuery($query);
       $result = $query->getResult();

        return  $result ;
       }


//    /**
//     * @return HistoDemandes[] Returns an array of HistoDemandes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistoDemandes
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
