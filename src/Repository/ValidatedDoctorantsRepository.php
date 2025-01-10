<?php

namespace App\Repository;

use App\Entity\ValidatedDoctorants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValidatedDoctorants>
 *
 * @method ValidatedDoctorants|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValidatedDoctorants|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValidatedDoctorants[]    findAll()
 * @method ValidatedDoctorants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValidatedDoctorantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValidatedDoctorants::class);
    }

    public function save(ValidatedDoctorants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ValidatedDoctorants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByDoctorant(Doctorants $doctorant): array
{
    return $this->findBy(['doctorant' => $doctorant]);
}


//    /**
//     * @return ValidatedDoctorants[] Returns an array of ValidatedDoctorants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ValidatedDoctorants
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
