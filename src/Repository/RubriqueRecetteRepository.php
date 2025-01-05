<?php

namespace App\Repository;

use App\Entity\RubriqueRecette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RubriqueRecette>
 *
 * @method RubriqueRecette|null find($id, $lockMode = null, $lockVersion = null)
 * @method RubriqueRecette|null findOneBy(array $criteria, array $orderBy = null)
 * @method RubriqueRecette[]    findAll()
 * @method RubriqueRecette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RubriqueRecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RubriqueRecette::class);
    }

    public function save(RubriqueRecette $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RubriqueRecette $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RubriqueRecette[] Returns an array of RubriqueRecette objects
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

//    public function findOneBySomeField($value): ?RubriqueRecette
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
