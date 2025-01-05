<?php

namespace App\Repository;

use App\Entity\BudgetEntree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BudgetEntree>
 *
 * @method BudgetEntree|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetEntree|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetEntree[]    findAll()
 * @method BudgetEntree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetEntreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetEntree::class);
    }

    public function save(BudgetEntree $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BudgetEntree $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByBudget($id) {
     
        $query="select * from budget_entree  WHERE budget_id = ".$id;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 

        return  $result ; 

    }
//    /**
//     * @return BudgetEntree[] Returns an array of BudgetEntree objects
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

//    public function findOneBySomeField($value): ?BudgetEntree
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
