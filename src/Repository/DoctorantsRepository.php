<?php

namespace App\Repository;

use App\Entity\Doctorants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Doctorants>
 *
 * @method Doctorants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Doctorants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Doctorants[]    findAll()
 * @method Doctorants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctorantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Doctorants::class);
    }

    public function save(Doctorants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Doctorants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByDateEnvoi(?string $dateEnvoi): array
{
    $qb = $this->createQueryBuilder('d');

    if ($dateEnvoi) {
        $qb->andWhere('d.dateEnvoi = :dateEnvoi')
           ->setParameter('dateEnvoi', $dateEnvoi);
    }

    return $qb->orderBy('d.id', 'ASC')
              ->getQuery()
              ->getResult();
}


//    /**
//     * @return Doctorants[] Returns an array of Doctorants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Doctorants
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
