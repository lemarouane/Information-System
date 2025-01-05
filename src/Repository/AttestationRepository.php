<?php

namespace App\Repository;

use App\Entity\Attestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attestation>
 *
 * @method Attestation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attestation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attestation[]    findAll()
 * @method Attestation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attestation::class);
    }

    public function save(Attestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Attestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


public function bloque_attestation($id) {

    $query = $this->createQueryBuilder('a')
    ->update()
    ->set('a.bloque', true)
    ->andWhere('a.id = (:id)')->setParameter('id', $id)
    ->getQuery()
    ->execute();

;

}
public function debloque_attestation($id) {

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
     
        $query="select count(a.id) from App\Entity\Attestation a WHERE a.dateEnvoie between '".$annee_array[$i]."-01-01' and '".$annee_array[$i]."-12-31' and a.personnel = ".$id_personel;
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        //dd($result[0][1]);
        array_push($result_array,$result[0][1]);
    
    }
    
    return  $result_array ;
    
    }
    
    
    public function find_by_annee_and_persid($annee,$id_personel) {
         
            $query="select c from App\Entity\Attestation c WHERE c.dateEnvoie between '".$annee."-01-01' and '".$annee."-12-31' and c.personnel = ".$id_personel;
            $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult();
    
        return  $result ;
        
        }

}
