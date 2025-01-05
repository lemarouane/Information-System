<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Etudiant\Absence;

 /**
 * @extends ServiceEntityRepository<Absence>
 *
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbsenceRepository extends \Doctrine\ORM\EntityRepository
{
	



    public function absenceByDate($code,$debut,$fin) {
       


        $query = $this->createQueryBuilder('a');
        $query->andWhere('a.idUser = :idUser')->setParameter('idUser', $code);
        $query->andWhere('a.dateabsence >= :datedebut')->setParameter('datedebut', $debut);
        $query->andWhere('a.dateabsence <= :datefin')->setParameter('datefin', $fin);
        return $query->getQuery()->getResult(); 
    }

    public function absenceByProf($prof,$annee) {
       
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.idUser)', 'a')  
            ->groupBy('a.idUser')
			->addGroupBy('a.module');
        if($prof){
            $qb=$qb->andWhere('a.idProf = :prof')->setParameter('prof', $prof);
        }
        $qb=$qb->andWhere('a.anneeuniv = :annee')->setParameter('annee', $annee);
        $qb=$qb->getQuery()->getResult();
        return $qb; 
    }

    public function absenceByProfAll($prof,$annee) {
       
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.idUser)', 'a')  
            ->groupBy('a.idUser')
			->addGroupBy('a.module');
        if($prof){
            $qb=$qb->andWhere('a.idProf = :prof')->setParameter('prof', $prof);
        }
        $qb=$qb->andWhere('a.anneeuniv = :annee')->setParameter('annee', $annee);
        $qb=$qb->getQuery()->getResult();
        return $qb; 
    }

    public function absenceByEtape($prof,$annee) {
       
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.idUser)', 'a')  
            ->groupBy('a.etape');
        if($prof){
            $qb=$qb->andWhere('a.idProf = :prof')->setParameter('prof', $prof);
        }
        $qb=$qb->andWhere('a.anneeuniv = :annee')->setParameter('annee', $annee);
        $qb=$qb->getQuery()->getResult();
        return $qb; 
    }

     public function nbAbsenceFiliereParMois($anneescolaire) {

        $query="select  DISTINCT EXTRACT( MONTH FROM a.dateAbsence ) mois, count(a.id) nb_Absence,a.etape from Absence a where a.anneeuniv = '".$anneescolaire."' group By a.etape,mois";
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt = $stmt->execute();
        return  $stmt->fetchAll();

    }

    
}
