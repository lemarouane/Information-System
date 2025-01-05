<?php

namespace App\Repository;

use App\Entity\Decharge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Decharge>
 *
 * @method Decharge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Decharge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Decharge[]    findAll()
 * @method Decharge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DechargeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Decharge::class);
    }

    public function save(Decharge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Decharge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNumDecharge() {

        $query="SELECT nextval FROM sequence where entity = 'Decharge'";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
        return  $result ;

    }

    public function NextNumDecharge() {

        $query="UPDATE sequence set nextval = nextval + 1 WHERE entity = 'Decharge' ";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery();  
        return  "1" ;

    }

    public function getNumBS() {

        $query="SELECT nextval FROM sequence where entity = 'Bon sortie'";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
        return  $result ;

    }

    public function NextNumBS() {

        $query="UPDATE sequence set nextval = nextval + 1 WHERE entity = 'Bon sortie' ";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery();  
        return  "1" ;

    }

    public function getBlocs($decharge) {

        $query="SELECT ar.designation designation, count(1) qte FROM affectation af, article ar, decharge d WHERE ar.id = af.article_id and af.decharge_id = d.id and d.id = ".$decharge->getId()." group by ar.designation order by ar.designation";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;

    }

    public function findNotAnuler()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.NumDecharge != :category')
            ->setParameter('category', 'anuler')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Decharge[] Returns an array of Decharge objects
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

//    public function findOneBySomeField($value): ?Decharge
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
