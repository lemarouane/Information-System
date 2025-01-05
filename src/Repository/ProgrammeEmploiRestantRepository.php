<?php

namespace App\Repository;

use App\Entity\ProgrammeEmploiRestant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProgrammeEmploiRestant>
 *
 * @method ProgrammeEmploiRestant|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgrammeEmploiRestant|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgrammeEmploiRestant[]    findAll()
 * @method ProgrammeEmploiRestant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeEmploiRestantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgrammeEmploiRestant::class);
    }

    public function save(ProgrammeEmploiRestant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProgrammeEmploiRestant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function find_by_id($id)
    {
        $query="select * from programme_emploi_restant p where p.id = ".$id;
 
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative(); 
        return $result ;
    }

//    /**
//     * @return ProgrammeEmploiRestant[] Returns an array of ProgrammeEmploiRestant objects
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

//    public function findOneBySomeField($value): ?ProgrammeEmploiRestant
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
