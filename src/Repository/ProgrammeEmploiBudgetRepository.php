<?php

namespace App\Repository;

use App\Entity\ProgrammeEmploiBudget;
use App\Entity\ProgrammeElementBudget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProgrammeEmploiBudget>
 *
 * @method ProgrammeEmploiBudget|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgrammeEmploiBudget|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgrammeEmploiBudget[]    findAll()
 * @method ProgrammeEmploiBudget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeEmploiBudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgrammeEmploiBudget::class);
    }

    public function save(ProgrammeEmploiBudget $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProgrammeEmploiBudget $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function getMaxId()
    {
        $query="select MAX(CAST(p.reference AS SIGNED)) as max_reference from programme_emploi_budget p;";
 
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative(); 
        return $result ;
    }


  

//    /**
//     * @return ProgrammeEmploiBudget[] Returns an array of ProgrammeEmploiBudget objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProgrammeEmploiBudget
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
