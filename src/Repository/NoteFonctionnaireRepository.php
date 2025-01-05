<?php

namespace App\Repository;

use App\Entity\NoteFonctionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NoteFonctionnaire>
 *
 * @method NoteFonctionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoteFonctionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoteFonctionnaire[]    findAll()
 * @method NoteFonctionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteFonctionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoteFonctionnaire::class);
    }

    public function save(NoteFonctionnaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NoteFonctionnaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return NoteFonctionnaire[] Returns an array of NoteFonctionnaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NoteFonctionnaire
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
