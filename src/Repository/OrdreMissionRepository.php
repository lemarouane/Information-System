<?php

namespace App\Repository;

use App\Entity\OrdreMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrdreMission>
 *
 * @method OrdreMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdreMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdreMission[]    findAll()
 * @method OrdreMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdreMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdreMission::class);
    }

    public function save(OrdreMission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrdreMission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function searchDemandesByService($codes,$niveau) {

       
            $niveau1=implode("','", $niveau);
            $codes1=implode("','", $codes);
            $i=0;
            $query="SELECT o from  App\Entity\OrdreMission o , App\Entity\Personnel p , App\Entity\Service s
                    WHERE o.personnel=p.id  AND p.serviceAffectationId=s.id AND o.niveau IN ('".$niveau1."') AND o.statut ='-1' AND (";
            if(in_array('ROLE_CHEF_SERV',$niveau) ){

                $query .="  concat('SER_',s.id) IN  ('".$codes1."') OR   ";
                $i=1;
            }
            if(in_array('ROLE_RH',$niveau)){
            
                $query .= " o.niveau ='ROLE_RH' OR   ";
                $i=1;
            }
            if(in_array('ROLE_DIR_ADJ',$niveau) ){
                if(in_array('DIR_1',$codes)){
                    $query .=" (o.niveau ='ROLE_DIR_ADJ' AND (s.codes != '2' OR s.codes is null)  )  OR   ";
                }
                $query .=" concat('DIR_',s.codes) IN  ('".$codes1."') OR   ";
                $i=1;
      
            }
            $query=substr($query, 0, -5);
            if($i==1){
                $query .= " )";
            }
            
           

            $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult();

            return  $result ;
    
          
        
   
         
        //return $query->getQuery()->getResult();      
    }
    

public function searchDemandesByAnnee($personnel,$annee) {
  
    $d = $annee."-01-01"  ;
    $f = $annee."-12-31"  ;
    $query = $this->createQueryBuilder('c');
    $query->Where('c.personnel = (:personnel)')->setParameter('personnel', $personnel);
    $query->andwhere('c.dateEnvoie BETWEEN :debut AND :fin')
    ->setParameter('debut', $d)
    ->setParameter('fin', $f);

   return $query->getQuery()->getResult();  
}

    public function searchDemandesByDep($codes,$niveau) {


        $query = $this->createQueryBuilder('o')
                    ->leftJoin('o.personnel', 'p')
                    ->addSelect('p')
                    ->leftJoin('p.departementId', 'd')
                    ->addSelect('d');
        $query->andWhere('o.niveau in (:niveau)')->setParameter('niveau', $niveau);
        $query->andWhere("o.statut ='-1'");
    
        $query->andWhere("concat('DEP_', d.id) in (:codes)")->setParameter('codes', $codes);
        

    
        return $query->getQuery()->getResult();      
    }
    

    public function searchDemandesByLab($codes,$niveau) {


        $query = $this->createQueryBuilder('o')
                    ->leftJoin('o.personnel', 'p')
                    ->addSelect('p')
                    ->leftJoin('p.structureRech', 's')
                    ->addSelect('s');
        $query->andWhere('o.niveau = (:niveau)')->setParameter('niveau', 'ROLE_CHEF_STRUCT');
        $query->andWhere("o.statut ='-1'");
    
        $query->andWhere("concat('STR_', s.id) in (:codes)")->setParameter('codes', $codes);

    
   
      
        return $query->getQuery()->getResult();      
    }
    

    public function searchDemandesValideAnnee($statut,$annee) {

        $annee_debut =$annee."-01-01" ;
        $annee_fin =$annee."-12-31";

        $query = $this->createQueryBuilder('o');
        $query->andWhere("o.statut in (:statut)")->setParameter('statut', $statut);
        $query->andWhere("o.dateEnvoie between (:annee_debut) and (:annee_fin) ");
        $query->setParameter('annee_debut', $annee_debut);
        $query->setParameter('annee_fin', $annee_fin);
   
        return $query->getQuery()->getResult();      
    }
    

   

    
    public function bloque_ordremission($id) {

        $query = $this->createQueryBuilder('o')
        ->update()
        ->set('o.bloque', true)
        ->andWhere('o.id = (:id)')->setParameter('id', $id)
        ->getQuery()
        ->execute();
    
    ;
    
    }
    public function debloque_ordremission($id) {
    
        $query = $this->createQueryBuilder('o')
        ->update()
        ->set('o.bloque', 0)
        ->andWhere('o.id = (:id)')->setParameter('id', $id)
        ->getQuery()
        ->execute();
    
    ;
    
    }
    
    public function count_by_annee($annee_array,$id_personel) {
        $result_array =[];
        
        for ($i=0; $i < count($annee_array); $i++) { 
         
            $query="select count(c.id) from App\Entity\OrdreMission c WHERE c.dateEnvoie between '".$annee_array[$i]."-01-01' and '".$annee_array[$i]."-12-31' and c.personnel = ".$id_personel;
            $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult();
            //dd($result[0][1]);
            array_push($result_array,$result[0][1]);
        
        } 
        
        return  $result_array ;
        
        }


        public function find_by_annee_and_persid($annee,$id_personel) {
     
            $query="select c from App\Entity\OrdreMission c WHERE c.dateEnvoie between '".$annee."-01-01' and '".$annee."-12-31' and c.personnel = ".$id_personel;
            $query = $this->getEntityManager()->createQuery($query);
            $result = $query->getResult();
    
        return  $result ;
        
        }

}
