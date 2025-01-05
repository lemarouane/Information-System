<?php

namespace App\Repository;

use App\Entity\Ficheheure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ficheheure>
 *
 * @method Ficheheure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ficheheure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ficheheure[]    findAll()
 * @method Ficheheure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheheureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ficheheure::class);
    }

    public function save(Ficheheure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ficheheure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


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

 public function searchDemandesByDep($codes,$niveau) {

        $query = $this->createQueryBuilder('o')
                    ->leftJoin('o.personnel', 'p')
                    ->addSelect('p')
                    ->leftJoin('p.departementId', 'd')
                    ->addSelect('d');
        $query->andWhere('o.niveau in (:niveau)')->setParameter('niveau', $niveau);
        $query->andWhere("o.statut ='-1'");
        $query->andWhere("concat('DEP_',d.id) in (:codes)")->setParameter('codes', $codes);
        

    
        return $query->getQuery()->getResult();      
    }




     public function searchDemandesByService($codes,$niveau) {

        $query = $this->createQueryBuilder('o');
        $query->andWhere("o.statut ='-1'");          
        $query->andWhere('o.niveau in (:niveau)')->setParameter('niveau', $niveau);
             
        return $query->getQuery()->getResult();      
    }
    

    public function bloque_ficheheure($id) {

        $query = $this->createQueryBuilder('o')
        ->update()
        ->set('o.bloque', true)
        ->andWhere('o.id = (:id)')->setParameter('id', $id)
        ->getQuery()
        ->execute();
    
    ;
    
    }
    public function debloque_ficheheure($id) {
    
        $query = $this->createQueryBuilder('o')
        ->update()
        ->set('o.bloque', 0)
        ->andWhere('o.id = (:id)')->setParameter('id', $id)
        ->getQuery()
        ->execute();
    
    ;
    
    }
    
    
    public function count_by_annee($annee_array,$id_personel) {
        $result_array =[];
        
        for ($i=0; $i < count($annee_array); $i++) { 
         
            $query="select count(a.id) from App\Entity\Ficheheure a WHERE a.dateEnvoie between '".$annee_array[$i]."-01-01' and '".$annee_array[$i]."-12-31' and a.personnel = ".$id_personel;
            $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult();
            //dd($result[0][1]);
            array_push($result_array,$result[0][1]);
        
        }
        
        return  $result_array ;
        
        }
        
        
        public function find_by_annee_and_persid($annee,$id_personel) {
             
                $query="select c from App\Entity\Ficheheure c WHERE c.dateEnvoie between '".$annee."-01-01' and '".$annee."-12-31' and c.personnel = ".$id_personel;
                $query = $this->getEntityManager()->createQuery($query);
                $result = $query->getResult();
        
            return  $result ;
            
            }


}
