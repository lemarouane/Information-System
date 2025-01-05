<?php

namespace App\Repository;

use App\Entity\Budgetperiode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Budgetperiode>
 *
 * @method Budgetperiode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Budgetperiode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Budgetperiode[]    findAll()
 * @method Budgetperiode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetperiodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budgetperiode::class);
    }

    public function save(Budgetperiode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Budgetperiode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Budgetperiode[] Returns an array of Budgetperiode objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Budgetperiode
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
