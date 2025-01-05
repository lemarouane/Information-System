<?php

namespace App\Repository;

use App\Entity\Etudiant\InscritEtudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InscritEtudiant>
 *
 * @method InscritEtudiant|null find($id, $lockMode = null, $lockVersion = null)
 * @method InscritEtudiant|null findOneBy(array $criteria, array $orderBy = null)
 * @method InscritEtudiant[]    findAll()
 * @method InscritEtudiant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscritEtudiantRepository extends EntityRepository
{
    

    public function save(InscritEtudiant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InscritEtudiant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return InscritEtudiant[] Returns an array of InscritEtudiant objects
    */
   public function findUserByAnnee($value): array
   {
        $query="SELECT ed.id,e.code,e.nom,e.prenom,e.email,ed.anneeSoutenance as annee,e.phone,ed.filiere from  App\Entity\Etudiant\EtudiantDD ed , App\Entity\Etudiant\Etudiants e 
                WHERE  ed.etudiants = e.id 
                    AND ed.anneeSoutenance >= '".$value."' 
                    AND ed.id NOT IN (SELECT ed1.id FROM App\Entity\Etudiant\InscritEtudiant i,App\Entity\Etudiant\EtudiantDD ed1 WHERE i.inscription=ed1.id AND  i.annee = '".$value."')";
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        return  $result ;
   

   }

   public function findUserByAnnee_all($value): array
   {
        $query="SELECT ed.id,e.code,e.nom,e.prenom,e.email,ed.anneeSoutenance as annee,e.phone,ed.filiere from  App\Entity\Etudiant\EtudiantDD ed , App\Entity\Etudiant\Etudiants e 
                WHERE  ed.etudiants = e.id 
                    AND ed.anneeSoutenance >= '".$value."'";
        $query = $this->getEntityManager()->createQuery($query);
        $result = $query->getResult();
        return  $result ;
   

   }

//    public function findOneBySomeField($value): ?InscritEtudiant
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
