<?php

namespace App\Repository;

use App\Entity\ExecutionPE;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExecutionPE>
 *
 * @method ExecutionPE|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExecutionPE|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExecutionPE[]    findAll()
 * @method ExecutionPE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExecutionPERepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExecutionPE::class);
    }

    public function save(ExecutionPE $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExecutionPE $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findExec($id)
    {
        $query = $this->createQueryBuilder('e')
        ->leftJoin('e.programme', 'p')
        ->addSelect('p')
     
        ->leftJoin('e.executionElements', 'el')
        ->addSelect('el')

        ->leftJoin('el.rubrique', 'r')
        ->addSelect('r')

        ->leftJoin('r.element', 'ee')
        ->addSelect('ee')

       ->leftJoin('p.executionPEs', 'pe')
        ->addSelect('pe');
           

      $query->andWhere('e.id = (:id)')->setParameter('id', $id);
      return $query->getQuery()->getResult();   
    }

    public function findExecs()
    {
        $query = $this->createQueryBuilder('e')
        ->leftJoin('e.programme', 'p')
        ->addSelect('p')
     
        ->leftJoin('e.executionElements', 'el')
        ->addSelect('el')

        ->leftJoin('el.rubrique', 'r')
        ->addSelect('r')

        ->leftJoin('r.element', 'ee')
        ->addSelect('ee')

       ->leftJoin('p.executionPEs', 'pe')
        ->addSelect('pe');
           

      $query->andWhere('e.programme is not null');
      return $query->getQuery()->getResult();   
    }


    public function findExecs_ProgBudget()
    {
        $query = $this->createQueryBuilder('e')
        ->leftJoin('e.programme', 'p')
        ->addSelect('p')
     
        ->leftJoin('e.executionElements', 'el')
        ->addSelect('el')

        ->leftJoin('el.rubrique', 'r')
        ->addSelect('r')

        ->leftJoin('r.element', 'ee')
        ->addSelect('ee')

       ->leftJoin('p.executionPEs', 'pe')
        ->addSelect('pe');
           

      $query->andWhere('e.programmeBudget is not null');
      return $query->getQuery()->getResult();   
    }

//    /**
//     * @return ExecutionPE[] Returns an array of ExecutionPE objects
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

//    public function findOneBySomeField($value): ?ExecutionPE
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
