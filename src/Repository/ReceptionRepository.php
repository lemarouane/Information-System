<?php

namespace App\Repository;

use App\Entity\Reception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reception>
 *
 * @method Reception|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reception|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reception[]    findAll()
 * @method Reception[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reception::class);
    }

    public function save(Reception $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reception $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNumReception() {

        $query="SELECT nextval FROM sequence where entity = 'Reception'";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
        return  $result ;

    }

    public function NextNumReception() {

        $query="UPDATE sequence set nextval = nextval + 1 WHERE entity = 'Reception' ";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery();  
        return  "1" ;

    }

    public function isLastReception($k) {

        $query="SELECT CASE WHEN EXISTS (SELECT 1 FROM reception WHERE id > ".$k.") THEN 0 ELSE 1 END AS result";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
        return  $result ;

    }

    public function getLastReception() {

        $query="SELECT max(id) AS result FROM reception";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
        return  $result ;

    }

//    /**
//     * @return Reception[] Returns an array of Reception objects
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

//    public function findOneBySomeField($value): ?Reception
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
