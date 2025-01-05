<?php

namespace App\Repository;

use App\Entity\Etudiant\Laureats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Laureats>
 *
 * @method Laureats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Laureats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Laureats[]    findAll()
 * @method Laureats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LaureatsRepository extends \Doctrine\ORM\EntityRepository
{
    

    public function save(Laureats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Laureats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Laureats[] Returns an array of Laureats objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Laureats
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
