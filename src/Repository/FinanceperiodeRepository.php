<?php

namespace App\Repository;

use App\Entity\Financeperiode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Financeperiode>
 *
 * @method Financeperiode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Financeperiode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Financeperiode[]    findAll()
 * @method Financeperiode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinanceperiodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Financeperiode::class);
    }

    public function save(Financeperiode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Financeperiode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function setFermerPeriodes($id_exception)
    {
        $query="UPDATE financeperiode p SET p.actif = 'F' WHERE p.id != ".$id_exception ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery(); 
        return $result ;
    }
    
    

//    /**
//     * @return Financeperiode[] Returns an array of Financeperiode objects
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

//    public function findOneBySomeField($value): ?Financeperiode
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
