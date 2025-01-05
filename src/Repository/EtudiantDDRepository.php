<?php

namespace App\Repository;

use App\Entity\Etudiant\EtudiantDD;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtudiantDD>
 *
 * @method EtudiantDD|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtudiantDD|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtudiantDD[]    findAll()
 * @method EtudiantDD[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantDDRepository extends EntityRepository
{
    
    public function save(EtudiantDD $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EtudiantDD $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function searchEtudiantDDByCodes($codes,$cn,$value) {

        $query="SELECT ed from  App\Entity\Etudiant\EtudiantDD ed 
                WHERE  (";
        foreach($codes as $code){
            $list = explode("_",$code);  
            $query .= "  ed.filiere like '%".$list[1]."%' OR";
                
        }
        $query=substr($query, 0, -2);
        $query .=" ) AND ed.anneeSoutenance >= '".$value."'" ;     
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        return  $result ;

        
    
    }

    public function searchEtudiantDDAnnee($value) {

        $query="SELECT ed from  App\Entity\Etudiant\EtudiantDD ed 
                WHERE   ed.anneeSoutenance >= '".$value."'" ;     
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        return  $result ;

        
    
    }

//    /**
//     * @return EtudiantDD[] Returns an array of EtudiantDD objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EtudiantDD
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
