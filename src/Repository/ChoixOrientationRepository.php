<?php

namespace App\Repository;

use App\Entity\Etudiant\ChoixOrientation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChoixOrientation>
 *
 * @method ChoixOrientation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChoixOrientation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChoixOrientation[]    findAll()
 * @method ChoixOrientation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoixOrientationRepository extends EntityRepository
{
    
    public function getchoixFiliere($annee) {

        $query="SELECT '1' as num, choix1 as choix, count(choix1) as c FROM choixorientation WHERE anneeuniv = '".$annee."' GROUP BY choix1
	            UNION
	            SELECT '2' as num, choix2 as choix, count(choix2) as c FROM choixorientation WHERE anneeuniv = '".$annee."' GROUP BY choix2
	            UNION
	            SELECT '3' as num, choix3 as choix, count(choix3) as c FROM choixorientation WHERE anneeuniv = '".$annee."' GROUP BY choix3
	            UNION
	            SELECT '4' as num, choix4 as choix, count(choix4) as c FROM choixorientation WHERE anneeuniv = '".$annee."' GROUP BY choix4
	            UNION
	            SELECT '5' as num, choix5 as choix, count(choix5) as c FROM choixorientation WHERE anneeuniv = '".$annee."' GROUP BY choix5
                UNION
                SELECT '6' as num, choix6 as choix, count(choix6) as c FROM choixorientation WHERE anneeuniv = '".$annee."' GROUP BY choix6";
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt = $stmt->execute();
        return  $stmt->fetchAll();

    }

    public function getchoixFiliere_50($annee) {

        $query="SELECT '1' as num, choix1 as choix, count(choix1) as c FROM (SELECT codeEtudiant, choix1 FROM choixorientation WHERE anneeuniv = '".$annee."' and codeEtudiant IN (SELECT codetudiant FROM etat WHERE mcal > (SELECT AVG(mcal) FROM etat))) t GROUP BY choix1
            UNION
            SELECT '2' as num, choix2 as choix, count(choix2) as c FROM (SELECT codeEtudiant, choix2 FROM choixorientation WHERE anneeuniv = '".$annee."' and codeEtudiant IN (SELECT codetudiant FROM etat WHERE mcal > (SELECT AVG(mcal) FROM etat))) t GROUP BY choix2
            UNION
            SELECT '3' as num, choix3 as choix, count(choix3) as c FROM (SELECT codeEtudiant, choix3 FROM choixorientation WHERE anneeuniv = '".$annee."' and codeEtudiant IN (SELECT codetudiant FROM etat WHERE mcal > (SELECT AVG(mcal) FROM etat))) t GROUP BY choix3
            UNION
            SELECT '4' as num, choix4 as choix, count(choix4) as c FROM (SELECT codeEtudiant, choix4 FROM choixorientation WHERE anneeuniv = '".$annee."' and codeEtudiant IN (SELECT codetudiant FROM etat WHERE mcal > (SELECT AVG(mcal) FROM etat))) t GROUP BY choix4
            UNION
            SELECT '5' as num, choix5 as choix, count(choix5) as c FROM (SELECT codeEtudiant, choix5 FROM choixorientation WHERE anneeuniv = '".$annee."' and codeEtudiant IN (SELECT codetudiant FROM etat WHERE mcal > (SELECT AVG(mcal) FROM etat))) t GROUP BY choix5
            UNION
            SELECT '6' as num, choix6 as choix, count(choix6) as c FROM (SELECT codeEtudiant, choix6 FROM choixorientation WHERE anneeuniv = '".$annee."' and codeEtudiant IN (SELECT codetudiant FROM etat WHERE mcal > (SELECT AVG(mcal) FROM etat))) t GROUP BY choix6";
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt = $stmt->execute();
        return  $stmt->fetchAll();

    }

    public function getPremierchoixAffecter($annee) {

        $query="SELECT choix1 as choix, count(choix1) as c FROM choixorientation WHERE anneeuniv = '".$annee."' GROUP BY choix1";
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt = $stmt->execute();
        return  $stmt->fetchAll();

    }

//    /**
//     * @return ChoixOrientation[] Returns an array of ChoixOrientation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChoixOrientation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
