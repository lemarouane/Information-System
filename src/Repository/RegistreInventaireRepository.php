<?php

namespace App\Repository;

use App\Entity\RegistreInventaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegistreInventaire>
 *
 * @method RegistreInventaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistreInventaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistreInventaire[]    findAll()
 * @method RegistreInventaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistreInventaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistreInventaire::class);
    }

    public function save(RegistreInventaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RegistreInventaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RegistreInventaire[] Returns an array of RegistreInventaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RegistreInventaire
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getNumInv() {

        $query="SELECT CAST(SUBSTRING_INDEX(max(num_inventaire), '/', -1) AS SIGNED) as num FROM registre_inventaire";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
        return  $result ;

    }
    public function deleteNumInv($k) {

        $query="DELETE  FROM registre_inventaire WHERE CAST(SUBSTRING_INDEX(num_inventaire, '/', -1) AS SIGNED) > ".$k;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery();  
        return  "1" ;

    }
    
}
