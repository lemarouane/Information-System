<?php

namespace App\Repository;

use App\Entity\Etudiant\ChoixAffecter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChoixAffecter>
 *
 * @method ChoixAffecter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChoixAffecter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChoixAffecter[]    findAll()
 * @method ChoixAffecter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoixAffecterRepository extends EntityRepository
{
    
    public function save(ChoixAffecter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChoixAffecter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ChoixAffecter[] Returns an array of ChoixAffecter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChoixAffecter
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
