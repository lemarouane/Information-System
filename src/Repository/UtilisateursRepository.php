<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateurs>
 *
 * @method Utilisateurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateurs[]    findAll()
 * @method Utilisateurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateursRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateurs::class);
    }

    public function save(Utilisateurs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Utilisateurs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateurs) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }



    public function get_fil() {

       $query="select concat('FIL_',code_apo) as id , nom_filiere as designation from filiere";
       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;

    }

    public function get_serv() {

       $query="select concat('SER_',id) as id , nom_service as designation from service";
       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;

    }

    public function get_dep() {

        $query="select concat('DEP_',id) as id , libelle_dep as designation from departement";

       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;

    }
    public function get_dir() {

       $query="select concat('DIR_',id) as id , libelle_dir as designation from direction";
       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;

    }

    public function get_struct() {

        $query="select concat('STR_',id) as id , libelle_structure as designation from struct_rech";
       $statement = $this->getEntityManager()->getConnection()->prepare($query);
       $result = $statement->executeQuery()->fetchAllAssociative();  
        return  $result ;

    }



    public function findByRoleDep($id,$role){

        $query="select u.email , p.nom , p.prenom , d.libelle_dep as departement, u.id from utilisateurs u , departement d,  personnel p where p.id_user_id = u.id and p.departement_id_id = d.id and d.id = ".$id." and u.roles like '%".$role."%'" ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
         return  $result ;
  
     } 

     public function findByRoleStruct($id,$role){

        $query="select u.email , p.nom , p.prenom,s.libelle_structure as laboratoire,u.id from utilisateurs u , struct_rech s,  personnel p where p.id_user_id = u.id and p.structure_rech_id = s.id and s.id = ".$id." and u.roles like '%".$role."%'" ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
         return  $result ;

       }

     public function findByRole($role){

        $query="select u.email , p.nom , p.prenom from utilisateurs u , personnel p where p.id_user_id = u.id and u.roles like '%".$role."%'" ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
         return  $result ;
 
     }


     public function findByRoleExact($role){

        $query="select u.email , p.nom , p.prenom from utilisateurs u , personnel p where p.id_user_id = u.id and u.roles like '[\"ROLE_DIR\"]' " ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
         return  $result ;
 
     }

     public function findByRoleDir(){

        $query="select u.email , p.nom , p.prenom from utilisateurs u , personnel p where p.id_user_id = u.id and u.codes like '%DIRECTEUR%' " ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
         return  $result ;
 
     }

     public function findByRoleDirAdj($code,$role){

        $query="select u.email , p.nom , p.prenom from utilisateurs u , personnel p where p.id_user_id = u.id and u.codes like '%".$code."%' and u.roles like '%".$role."%'" ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
         return  $result ;
 
     }

     public function findByRoleServ($code,$role){

        $query="select u.email , p.nom , p.prenom from utilisateurs u , personnel p where p.id_user_id = u.id and u.codes like '%".$code."\"]%' and u.roles like '%".$role."%'" ;
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();  
         return  $result ;
 
     }
     


  /*   public function findByRoleDep($id,string $role)
    {
        $qb = $this->createQueryBuilder('u');
        return    $qb->addSelect('d') 
            ->leftJoin('u.departementId', 'd')
            ->andwhere('u.departementId = :parameter')
            ->setParameter('parameter', $id)
            ->andwhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%' . $role . '%')
            ->getQuery()
            ->getOneOrNullResult();
    }
 */

    /* public function findByRoleLaboratoire($id,string $role)
    {
        $qb = $this->createQueryBuilder('u');
        return    $qb->addSelect('l') 
            ->leftJoin('u.laboratoire', 'l')
            ->andwhere('u.laboratoire = :parameter')
            ->setParameter('parameter', $id)
            ->andwhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%' . $role . '%')
            ->getQuery()
            ->getResult();
    }
    public function findByRoleDirection(string $role)
    {
        $qb = $this->createQueryBuilder('u');
        return    $qb->andwhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%' . $role . '%')
            ->getQuery()
            ->getOneOrNullResult();
    }
	
	public function findByRoleRH(string $role)
    {
        $qb = $this->createQueryBuilder('u');
        return    $qb->andwhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%' . $role . '%')
            ->getQuery()
            ->getResult();
    }
	
	public function findByRoleService($id,string $role)
    {
        $qb = $this->createQueryBuilder('u');
        return    $qb->addSelect('s') // to make Doctrine actually use the join
            ->leftJoin('u.service', 's')
            ->andwhere('u.service = :parameter')
            ->setParameter('parameter', $id)
            ->andwhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%' . $role . '%')
            ->getQuery()
            ->getOneOrNullResult();
    }


 */



//    /**
//     * @return Utilisateurs[] Returns an array of Utilisateurs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Utilisateurs
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
