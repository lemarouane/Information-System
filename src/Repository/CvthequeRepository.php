<?php

namespace App\Repository;

use App\Entity\Etudiant\Cvtheque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;



/**

 */
class CvthequeRepository extends \Doctrine\ORM\EntityRepository
{
   

   

    public function cvExist( $id_user) {

        $result = null;

        $formations="SELECT f.id from formations f WHERE f.cvtheque_id in ( SELECT c.id FROM cvtheque c WHERE c.idUser_id = ".$id_user." and c.emailPerso is not null and c.mobile is not null ) ";
        $experiences="SELECT e.id from experience e WHERE e.cvtheque_id in ( SELECT c.id FROM cvtheque c WHERE c.idUser_id = ".$id_user." and c.emailPerso is not null and c.mobile is not null ) ";

        $result_f= $this->getEntityManager()->getConnection()->prepare($formations);
        $result_e= $this->getEntityManager()->getConnection()->prepare($experiences); 

        $result_f =  $result_f->executeQuery()->fetchAllAssociative();
        $result_e =  $result_e->executeQuery()->fetchAllAssociative();

        if($result_e!=null && $result_f!=null){
            $result = "True";
        }

        return  $result ; 

    }


//    /**
//     * @return Cvtheque[] Returns an array of Cvtheque objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cvtheque
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
