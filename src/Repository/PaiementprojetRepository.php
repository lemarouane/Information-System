<?php

namespace App\Repository;

use App\Entity\Paiementprojet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiementprojet>
 *
 * @method Paiementprojet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paiementprojet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paiementprojet[]    findAll()
 * @method Paiementprojet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementprojetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiementprojet::class);
    }

    public function save(Paiementprojet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Paiementprojet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function ldBY()
    {
        return $this->createQueryBuilder('p')
            ->addSelect('SUM(p.montant)','p')
            ->groupBy('p.responsable')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Paiementprojet[] Returns an array of Paiementprojet objects
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

//    public function findOneBySomeField($value): ?Paiementprojet
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
