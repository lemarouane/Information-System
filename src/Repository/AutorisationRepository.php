<?php

namespace App\Repository;

use App\Entity\Autorisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Autorisation>
 *
 * @method Autorisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Autorisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Autorisation[]    findAll()
 * @method Autorisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autorisation::class);
    }

    public function save(Autorisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Autorisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Autorisation[] Returns an array of Autorisation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Autorisation
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function searchDemandesByAnnee($personnel,$annee) {
  
    $d = $annee."-01-01"  ;
    $f = $annee."-12-31"  ;
    $query = $this->createQueryBuilder('c');
    $query->Where('c.personnel = (:personnel)')->setParameter('personnel', $personnel);
    $query->andwhere('c.dateEnvoie BETWEEN :debut AND :fin')
    ->setParameter('debut', $d)
    ->setParameter('fin', $f);

   return $query->getQuery()->getResult();  
}

public function searchDemandesByService($codes,$niveau) {

    
    $niveau1=implode("','", $niveau);
        $codes1=implode("','", $codes);
        $i=0;
        $query="SELECT a from  App\Entity\Autorisation a , App\Entity\Personnel p , App\Entity\Service s
                WHERE a.personnel=p.id  AND p.serviceAffectationId=s.id AND a.niveau IN ('".$niveau1."') AND a.statut ='-1' AND (";
        if(in_array('ROLE_CHEF_SERV',$niveau) ){

            $query .="  concat('SER_',s.id) IN  ('".$codes1."') OR   ";
            $i=1;
        }
        if(in_array('ROLE_RH',$niveau)){
            
            $query .= " a.niveau ='ROLE_RH' OR   ";
            $i=1;
        }
        if(in_array('ROLE_DIR_ADJ',$niveau) ){
            
            $query .=" concat('DIR_',s.codes) IN  ('".$codes1."') OR   ";
            $i=1;
      
        }
        $query=substr($query, 0, -5);
        if($i==1){
            $query .= " )";
        }

            
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        
        return  $result ;
         
}





public function bloque_autorisation($id) {

    $query = $this->createQueryBuilder('a')
    ->update()
    ->set('a.bloque', true)
    ->andWhere('a.id = (:id)')->setParameter('id', $id)
    ->getQuery()
    ->execute();

;

}
public function debloque_autorisation($id) {

    $query = $this->createQueryBuilder('a')
    ->update()
    ->set('a.bloque', 0)
    ->andWhere('a.id = (:id)')->setParameter('id', $id)
    ->getQuery()
    ->execute();

;

}


public function count_by_annee($annee_array,$id_personel) {
    $result_array =[];
    
    for ($i=0; $i < count($annee_array); $i++) { 
     
        $query="select count(c.id) from App\Entity\Autorisation c WHERE c.dateEnvoie between '".$annee_array[$i]."-01-01' and '".$annee_array[$i]."-12-31' and c.personnel = ".$id_personel;
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        //dd($result[0][1]);
        array_push($result_array,$result[0][1]);
    
    }
    
    return  $result_array ;
    
    }
    


    public function find_by_annee_and_persid($annee,$id_personel) {
         
            $query="select c from App\Entity\Autorisation c WHERE c.dateEnvoie between '".$annee."-01-01' and '".$annee."-12-31' and c.personnel = ".$id_personel;
            $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult();
    
        return  $result ;
        
        }
        


}
