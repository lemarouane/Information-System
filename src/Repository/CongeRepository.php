<?php

namespace App\Repository;

use App\Entity\Conge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conge>
 *
 * @method Conge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conge[]    findAll()
 * @method Conge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conge::class);
    }

    public function save(Conge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Conge[] Returns an array of Conge objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conge
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
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
        $query="SELECT c from  App\Entity\Conge c , App\Entity\Personnel p , App\Entity\Service s
                WHERE c.personnel=p.id  AND p.serviceAffectationId=s.id AND c.niveau IN ('".$niveau1."') AND c.statut ='-1' AND (";
        if(in_array('ROLE_CHEF_SERV',$niveau) ){

            $query .="  concat('SER_',s.id) IN  ('".$codes1."') OR   ";
            $i=1;
        }
        if(in_array('ROLE_RH',$niveau)){
            
            $query .= " c.niveau ='ROLE_RH' OR   ";
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





public function bloque_conge($id) {

    $query = $this->createQueryBuilder('c')
    ->update()
    ->set('c.bloque', true)
    ->andWhere('c.id = (:id)')->setParameter('id', $id)
    ->getQuery()
    ->execute();

;

}
public function debloque_conge($id) {

    $query = $this->createQueryBuilder('c')
    ->update()
    ->set('c.bloque', 0)
    ->andWhere('c.id = (:id)')->setParameter('id', $id)
    ->getQuery()
    ->execute();

;

}



public function count_by_annee($annee_array,$id_personel) {
    $result_array =[];
    
    for ($i=0; $i < count($annee_array); $i++) { 
     
        $query="select count(c.id) from App\Entity\Conge c WHERE c.dateEnvoie between '".$annee_array[$i]."-01-01' and '".$annee_array[$i]."-12-31' and c.personnel = ".$id_personel;
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        //dd($result[0][1]);
        array_push($result_array,$result[0][1]);
    
    }
    
    return  $result_array ;
    
    }
    
    public function find_by_annee_and_persid($annee,$id_personel) {
     
        $query="select c from App\Entity\Conge c WHERE c.dateEnvoie between '".$annee."-01-01' and '".$annee."-12-31' and c.personnel = ".$id_personel;
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();

    return  $result ; 
    
    }







}
