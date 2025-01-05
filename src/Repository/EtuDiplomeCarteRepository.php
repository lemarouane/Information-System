<?php

namespace App\Repository;

use App\Entity\Etudiant\EtuDiplomeCarte;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtuDiplomeCarte>
 *
 * @method EtuDiplomeCarte|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtuDiplomeCarte|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtuDiplomeCarte[]    findAll()
 * @method EtuDiplomeCarte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtuDiplomeCarteRepository extends EntityRepository
{
    

    public function save(EtuDiplomeCarte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EtuDiplomeCarte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function rechercheByDecision() {
       
        $query = $this->createQueryBuilder('d');
		$query->andWhere('d.type = :type')->setParameter('type', 'Diplome');
        $query->andWhere('d.decision = :decision')->setParameter('decision', '1');
        $query->orWhere('d.decision = :decision1')->setParameter('decision1', '2');
        return $query->getQuery()->getResult(); 
    }
    

//    /**
//     * @return EtuDiplomeCarte[] Returns an array of EtuDiplomeCarte objects
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

//    public function findOneBySomeField($value): ?EtuDiplomeCarte
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
