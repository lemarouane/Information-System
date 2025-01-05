<?php

namespace App\Repository;

use App\Entity\ProgrammeEmploiProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProgrammeEmploiProjet>
 *
 * @method ProgrammeEmploiProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgrammeEmploiProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgrammeEmploiProjet[]    findAll()
 * @method ProgrammeEmploiProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeEmploiProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgrammeEmploiProjet::class);
    }

    public function save(ProgrammeEmploiProjet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProgrammeEmploiProjet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getMaxId()
    {
        $query="select MAX(CAST(p.reference AS SIGNED)) as max_reference from programme_emploi_projet p;";
 
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative(); 
        return $result ;
    }



//    /**
//     * @return ProgrammeEmploiProjet[] Returns an array of ProgrammeEmploiProjet objects
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

//    public function findOneBySomeField($value): ?ProgrammeEmploiProjet
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
