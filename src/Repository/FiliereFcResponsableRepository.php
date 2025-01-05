<?php

namespace App\Repository;

use App\Entity\FiliereFcResponsable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FiliereFcResponsable>
 *
 * @method FiliereFcResponsable|null find($id, $lockMode = null, $lockVersion = null)
 * @method FiliereFcResponsable|null findOneBy(array $criteria, array $orderBy = null)
 * @method FiliereFcResponsable[]    findAll()
 * @method FiliereFcResponsable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiliereFcResponsableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FiliereFcResponsable::class);
    }

    public function save(FiliereFcResponsable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FiliereFcResponsable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FiliereFcResponsable[] Returns an array of FiliereFcResponsable objects
// //     */
//     public function findByExampleField($value): array
//     {
//         return $this->createQueryBuilder('f')
//            ->andWhere('f.annee < :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.annee', 'DESC')
//       //     ->setMaxResults(10)
//            ->getQuery()
//           ->getResult()
//       ;
//     }

  public function findOneBySomeField($value): ?FiliereFcResponsable
    {
       return $this->createQueryBuilder('f')
           ->andWhere('f.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

}
