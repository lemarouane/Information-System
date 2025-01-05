<?php

namespace App\Repository;

use App\Entity\Etudiant\EtuAttestation;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtuAttestation>
 *
 * @method EtuAttestation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtuAttestation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtuAttestation[]    findAll()
 * @method EtuAttestation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtuAttestationRepository extends EntityRepository
{
    

    public function save(EtuAttestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EtuAttestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EtuAttestation[] Returns an array of EtuAttestation objects
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

//    public function findOneBySomeField($value): ?EtuAttestation
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
