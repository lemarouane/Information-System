<?php

namespace App\Repository;

use App\Entity\ReceptionLigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceptionLigne>
 *
 * @method ReceptionLigne|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceptionLigne|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceptionLigne[]    findAll()
 * @method ReceptionLigne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceptionLigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceptionLigne::class);
    }

    public function save(ReceptionLigne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReceptionLigne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReceptionLigne[] Returns an array of ReceptionLigne objects
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

//    public function findOneBySomeField($value): ?ReceptionLigne
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
