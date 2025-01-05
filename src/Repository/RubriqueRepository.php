<?php

namespace App\Repository;

use App\Entity\Rubrique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rubrique>
 *
 * @method Rubrique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rubrique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rubrique[]    findAll()
 * @method Rubrique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RubriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rubrique::class);
    }

    public function save(Rubrique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Rubrique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getRubriquesOrdered($art,$par) {

        $query="SELECT r.id, r.libelle as libelle_r , r.numRubrique , l.libelle as libelle_l , l.numLigne , p.libelle as libelle_p , p.numParagraphe , l.type , r.affichage , a.numArticle , a.libelle as libelle_a FROM rubrique r LEFT JOIN ligne l ON l.id = r.ligne_id LEFT JOIN paragraphe p ON p.id = l.paragraphe_id LEFT JOIN articlepe a ON a.id = p.articlePE_id  WHERE r.affichage='OUI' AND p.articlePE_id=".$art." AND l.paragraphe_id = ".$par." ORDER BY l.type DESC , l.numLigne ASC , r.numRubrique ASC";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
     
        return  $result ;

    }

    public function getRubriquesOrderedNoParagraphe($art) {

        $query="SELECT r.id, r.libelle as libelle_r , r.numRubrique , l.libelle as libelle_l , l.numLigne , p.libelle as libelle_p , p.numParagraphe ,l.type , r.affichage , a.numArticle , a.libelle as libelle_a FROM rubrique r LEFT JOIN ligne l ON l.id = r.ligne_id LEFT JOIN paragraphe p ON p.id = l.paragraphe_id LEFT JOIN articlepe a ON a.id = p.articlePE_id WHERE r.affichage='OUI' AND p.articlePE_id=".$art." ORDER BY l.type DESC ,p.numParagraphe ASC , l.numLigne ASC , r.numRubrique ASC";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
     
        return  $result ;

    }


//    /**
//     * @return Rubrique[] Returns an array of Rubrique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Rubrique
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
