<?php

namespace App\Repository;

use App\Entity\ProgrammeEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProgrammeEmploi>
 *
 * @method ProgrammeEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgrammeEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgrammeEmploi[]    findAll()
 * @method ProgrammeEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgrammeEmploi::class);
    }

    public function save(ProgrammeEmploi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProgrammeEmploi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function getMaxId()
    {
        $query="select MAX(CAST(p.reference AS SIGNED)) as max_reference from programme_emploi p;";
 
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative(); 
        return $result ;
    }

    public function getAllRubriqueByProgramme()
    {
        $query="SELECT SUM(montant) as montant, rubrique_id FROM programme_element p GROUP BY rubrique_id;";
 
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
        return $result ;
    }


  

//    /**
//     * @return ProgrammeEmploi[] Returns an array of ProgrammeEmploi objects
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

//    public function findOneBySomeField($value): ?ProgrammeEmploi
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
