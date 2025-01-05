<?php

namespace App\Repository;

use App\Entity\Etudiant\EtuReleveAttestation;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtuReleveAttestation>
 *
 * @method EtuReleveAttestation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtuReleveAttestation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtuReleveAttestation[]    findAll()
 * @method EtuReleveAttestation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtuReleveAttestationRepository extends EntityRepository
{
    

    public function save(EtuReleveAttestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EtuReleveAttestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function rechercheBy($code,$etape,$annee,$type) {
       
        $query = $this->createQueryBuilder('r');
        $query->andWhere('r.codeEtudiant = :code')->setParameter('code', $code);
        $query->andWhere('r.codeEtape = :etape')->setParameter('etape', $etape);
        $query->andWhere('r.anneeEtape = :annee')->setParameter('annee', $annee);
        $query->andWhere('r.type = :type')->setParameter('type', $type);
        $query->andWhere('r.decision != :decision')->setParameter('decision', 'Refusé');
        return $query->getQuery()->getResult(); 
    }
   /*  public function rechercheBy($code,$etape,$annee,$type): array
   {
       return $this->createQueryBuilder('r')
            ->addSelect('r')
            ->leftJoin('r.codeEtudiant', 'e')
            ->addSelect('e')
            ->andWhere('e.id = :val')
            ->setParameter('val', $code)
            ->andWhere('r.codeEtape = :etape')->setParameter('etape', $etape)
            ->andWhere('r.anneeEtape = :annee')->setParameter('annee', $annee)
            ->andWhere('r.type = :type')->setParameter('type', $type)
            ->andWhere('r.decision != :decision')->setParameter('decision', 'Refusé')
            ->getQuery()
            ->getResult()
       ;
   } */

    public function docBycodeNonRefu($code,$type) {
       
        $query = $this->createQueryBuilder('r');
        $query->andWhere('r.codeEtudiant = :code')->setParameter('code', $code);
        $query->andWhere('r.type = :type')->setParameter('type', $type);
        $query->andWhere('r.decision != :decision')->setParameter('decision', 'Refusé');
        return $query->getQuery()->getResult(); 
    }

//    /**
//     * @return EtuReleveAttestation[] Returns an array of EtuReleveAttestation objects
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

//    public function findOneBySomeField($value): ?EtuReleveAttestation
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
