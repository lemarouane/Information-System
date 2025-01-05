<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiement>
 *
 * @method Paiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]    findAll()
 * @method Paiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends EntityRepository
{
    

    public function save(Paiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Paiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }






    public function ldBY()
    {
        return $this->createQueryBuilder('p')
            ->addSelect('SUM(p.montant)','p')
            ->groupBy('p.demandeur')
            ->getQuery()
            ->getResult()
        ;
    }

    public function ldBY_prof($id_responsable,$annee)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('SUM(p.montant)','p')
           // ->andWhere('p.responsable = :id')
          //  ->andWhere('p.annee = :annee')
         //   ->setParameter('id',$id_responsable)
           // ->setParameter('annee',$annee)
            ->groupBy('p.demandeur')
            ->getQuery()
            ->getResult()
        ;
    }

    public function MaxEtuX(){
        $query="select max(p.demandeur) as max_x from App\Entity\Paiement p where p.demandeur like 'X%' ";
        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();
      return $result ;
    }

    public function ldBY_demandeur()
    {
        return $this->createQueryBuilder('p')
            ->addSelect('SUM(p.montant)','p')
           // ->andWhere('p.responsable = :id')
          //  ->andWhere('p.annee = :annee')
         //   ->setParameter('id',$id_responsable)
           // ->setParameter('annee',$annee)
            ->groupBy('p.demandeur')
            ->getQuery()
            ->getResult()
        ;
    }

    public function ldBY_demandeur_filiere($filiere , $annee_univ)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('SUM(p.montant)','p')
            ->andWhere('p.formation = :filiere')
            ->andWhere('p.anneeunuiv = :annee_univ')
            ->setParameter('filiere',$filiere)
            ->setParameter('annee_univ',$annee_univ)
            ->groupBy('p.demandeur')
            ->getQuery()
            ->getResult()
        ;
    }

    public function ldBY_prof_histo($formation,$annee)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('SUM(p.montant)','p')
            ->andWhere('p.formation = :formation')
          //  ->andWhere('p.annee < :annee')
            ->setParameter('formation',$formation)
           // ->setParameter('annee',$annee)
            ->groupBy('p.demandeur')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTranche($id)
    {
        $query="select max( p.tranche) as tranche from App\Entity\Paiement p WHERE p.demandeur =".$id;
        $query = $this->getEntityManager()->createQuery($query);
        if( $query->getResult() ) {
            $result = $query->getResult()[0]['tranche'];
        }else{
            $result = null;
        }
     
      return $result ;
    }



    public function getLastRPbyAnnee($annee)
    {
        $query="select max( p.numRP) as rp from App\Entity\Paiement p WHERE p.annee =".$annee;
        $query = $this->getEntityManager()->createQuery($query);
        if( $query->getResult() ) {
            $result = $query->getResult()[0]['rp'];
        }else{
            $result = null;
        }
     
      return $result ;
    }

    public function setLastRPbyAnnee($annee,$rp)
    {
        $query=" update App\Entity\Paiement p set p.lastrp = 0 where p.numRP != ".$rp." and p.annee =".$annee;
        $query = $this->getEntityManager()->createQuery($query);
        if( $query->getResult() ) {
            $result = 1;
        }else{
            $result = 0;
        }
     
      return $result ;
    }




    public function paiement_by_date($array_mm_dd,$annee_univ,$filiereFC,$prof){
        $date1 = "";
        $date2 = "";

        $array_result = [];

        if($annee_univ == date("Y") )//  2023 = 2023
        {
            $date1=$annee_univ."-".$array_mm_dd[1] ; //2023-03-31
            $date2=$annee_univ."-".$array_mm_dd[0] ; //2023-10-31
        }

        if($annee_univ +1 == (date("Y")))// 2023 + 1 = 2023 
        {
            $date1=$annee_univ."-".$array_mm_dd[0] ;// 2023-10-31
            $date2=($annee_univ+1)."-".$array_mm_dd[1] ; //2024-03-31
        }
       
       
        $query="select sum(p.montant) as montant from App\Entity\Paiement p where p.datePaiement > '".$date1."' and p.datePaiement < '".$date2."' and p.formation like '".$filiereFC."' and p.responsable = '".$prof."' " ;
  //dd($query);
        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();

        array_push($array_result, $date1, $date2,$result[0]['montant'] , $filiereFC );

      return $array_result ;
    }




    public function getMontant_by_FC_annee($filiereFC,$annee_univ,$prof)
    {
        $query=" select sum(p.montant) as montant , p.anneeuniv as annee from paiement p where p.formation = '".$filiereFC."' and p.responsable_id = '".$prof."' group by p.anneeuniv having p.anneeuniv ='".$annee_univ."'";
 
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative(); 
        return $result ;
    }

    public function getPaiement_by_resp_FC($filiereFC,$resp)
    {
        $query=" select sum(p.montant) as montant , p.anneeuniv as annee from paiement p where p.responsable_id = '".$resp."' and p.formation = '".$filiereFC."' group by p.anneeuniv ";
 
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
        return $result ;
    }




    public function getFC_annee_univ_paieur($filiere , $date_debut , $date_fin,$responsable)
    {
        $query="select p.anneeuniv , sum(p.montant) as montant_annee_univ ,  concat(per.nom ,' ', per.prenom) as responsable  from App\Entity\Paiement p , App\Entity\Personnel per  where p.responsable = per.id and p.formation like '".$filiere."' and p.responsable='".$responsable."' and p.datePaiement <= '".$date_fin."' and p.datePaiement >= '".$date_debut ."' group by p.anneeuniv , p.responsable";
        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();
      return $result ;
    }



    public function get_FC_montant_globale($filiere ,  $date_debut , $date_fin,$responsable)
    {
        $query="select sum(p.montant) as montant_globale , concat(per.nom ,' ', per.prenom) as responsable  from  App\Entity\FiliereFcResponsable f ,App\Entity\Paiement p , App\Entity\Personnel per where  p.responsable = per.id and f.responsable = per.id  and p.formation like '".$filiere."' and p.responsable='".$responsable."' and p.datePaiement <= '".$date_fin."' and p.datePaiement >= '".$date_debut ."' group by p.responsable";

        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();
      return $result ;
    }

    public function getForamtionByResponsable($date_debut , $date_fin)
    {
        $query="SELECT DISTINCT(p.formation) as formation ,concat(per.nom,' ',per.prenom) as responsable , per.id FROM App\Entity\Paiement p , App\Entity\Personnel per  WHERE p.datePaiement>'".$date_debut."' and p.datePaiement<='".$date_fin."' and p.responsable=per.id order by p.formation asc";
        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();
      return $result ;
    }

    public function getPayeurByAnnee($date_debut , $date_fin,$responsable,$anneeuniv = null)
    {
        $query="SELECT p.demandeur,p.nom,p.prenom,p.dateOperation,p.datePaiement,p.montant,p.numRP FROM App\Entity\Paiement p WHERE p.datePaiement>'".$date_debut."' and p.datePaiement<='".$date_fin."' and p.responsable='".$responsable."' and p.anneeuniv ='".$anneeuniv."' order by p.nom asc";
      
        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();
 
      return $result ;
    }

    public function getMontantGlobaleFC($date_debut , $date_fin)
    {
        $query="SELECT SUM(p.montant) as montant_g FROM App\Entity\Paiement p WHERE p.datePaiement > '".$date_debut."' and p.datePaiement <= '".$date_fin."'";
      
        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();
 
      return $result ;
    }


    public function get_FC_paiement_par_date($date_debut , $date_fin , $annee_exerc)
    {
        $query="SELECT distinct r.numRubrique , p.nom as etu_nom , p.prenom as etu_prenom , p.dateOperation , p.montant , p.numRP , pp.nom , pp.prenom , p.tranche , f.codeApo , f.code_version, p.tiers ,p.anneeuniv, p.modePaiement
         from App\Entity\Paiement p , App\Entity\FiliereFcResponsable fr , App\Entity\FiliereFc f , App\Entity\Personnel pp , App\Entity\Rubrique r
         WHERE p.rubrique = r.id and p.responsable = pp.id and fr.responsable= pp.id and f.id = fr.filiere_fc and p.dateOperation >= '".$date_debut."' and p.dateOperation <= '".$date_fin."' and p.annee = '".$annee_exerc."' order by p.dateOperation ASC";
        $query = $this->getEntityManager()->createQuery($query);
        $result =  $query->getResult();
      return $result ;
    }



//    /**
//     * @return Paiement[] Returns an array of Paiement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Paiement
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
