<?php

namespace App\Repository;

use App\Entity\CorpsGrades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CorpsGrades>
 *
 * @method CorpsGrades|null find($id, $lockMode = null, $lockVersion = null)
 * @method CorpsGrades|null findOneBy(array $criteria, array $orderBy = null)
 * @method CorpsGrades[]    findAll()
 * @method CorpsGrades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorpsGradesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CorpsGrades::class);
    }

    public function save(CorpsGrades $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CorpsGrades $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CorpsGrades[] Returns an array of CorpsGrades objects
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

//    public function findOneBySomeField($value): ?CorpsGrades
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
