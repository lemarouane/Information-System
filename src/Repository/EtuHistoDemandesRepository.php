<?php

namespace App\Repository;

use App\Entity\EtuHistoDemandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtuHistoDemandes>
 *
 * @method EtuHistoDemandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtuHistoDemandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtuHistoDemandes[]    findAll()
 * @method EtuHistoDemandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtuHistoDemandesRepository extends EntityRepository
{


    public function save(EtuHistoDemandes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EtuHistoDemandes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return EtuHistoDemandes[] Returns an array of EtuHistoDemandes objects
    */
   public function findHistoriqueById($value): array
   {
       return $this->createQueryBuilder('e')
           ->andWhere('e.validateur = :val')
           ->setParameter('val', $value)
           ->orderBy('e.id', 'ASC')
           ->groupBy('e.id_demande')
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?EtuHistoDemandes
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
