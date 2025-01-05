<?php

namespace App\Repository;

use App\Entity\Financeprojetperiode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Financeprojetperiode>
 *
 * @method Financeprojetperiode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Financeprojetperiode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Financeprojetperiode[]    findAll()
 * @method Financeprojetperiode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinanceprojetperiodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Financeprojetperiode::class);
    }

    public function save(Financeprojetperiode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Financeprojetperiode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function setFermerPeriodes($id_exception)
    {
        $query="UPDATE financeprojetperiode p SET p.actif = 'F' WHERE p.id != ".$id_exception ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery(); 
        return $result ;
    }
    

//    /**
//     * @return Financeprojetperiode[] Returns an array of Financeprojetperiode objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Financeprojetperiode
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
