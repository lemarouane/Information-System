<?php

namespace App\Repository;

use App\Entity\Etudiant\Reinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reinscription>
 *
 * @method Reinscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reinscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reinscription[]    findAll()
 * @method Reinscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReinscriptionRepository extends EntityRepository
{
  
    public function save(Reinscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reinscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Reinscription[] Returns an array of Reinscription objects
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

//    public function findOneBySomeField($value): ?Reinscription
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
