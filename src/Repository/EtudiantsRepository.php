<?php

namespace App\Repository;

use App\Entity\Etudiant\Etudiants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Etudiants>
 *
 * @method Etudiants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etudiants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etudiants[]    findAll()
 * @method Etudiants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantsRepository extends EntityRepository implements PasswordUpgraderInterface
{


    public function save(Etudiants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Etudiants $entity, bool $flush = false): void
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
        if (!$user instanceof Etudiants) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }




    public function rang_vet($cn,$etape,$annee) {

        $query="SELECT COUNT(*) as nombre FROM ins_adm_etp ETP WHERE ETP.COD_ETP='".$etape."' AND ETP.COD_ANU='".$annee."' AND ETP.ETA_IAE='E'";	
                    
                
        $result= $cn->fetchAssociative($query); 
        return  $result ; 

    }


    public function get_liste_module($cn,$etape=null) {

        $query="SELECT DISTINCT(IC.COD_ELP),EP.LIB_ELP ,EP.COD_PEL
                FROM ind_contrat_elp IC
                LEFT OUTER JOIN element_pedagogi EP 
                    ON IC.COD_ELP=EP.COD_ELP
                WHERE EP.COD_NEL='MO' AND  ( ";
                if($etape){
                    foreach($etape as $etp){
                        if($etp == 'IIGI'){
                            $query .=" IC.COD_ELP LIKE 'IIGI%' OR IC.COD_ELP LIKE 'IISI%' OR IC.COD_ELP LIKE 'IIGL%' OR";
                        }else{
                            $query .= "  IC.COD_ELP LIKE '".$etp."%' OR";
                        }
                    }
                    $query=substr($query, 0, -2);   
                }else{
                    $query .= "  IC.COD_ELP LIKE 'II%' ";
                }
                
                $query .= " ) AND IC.COD_ELP NOT IN ('IIEE3704','IIEE3804','IIEE4704','IIEE4804','IIEE5704','IIEE5804') ORDER BY IC.COD_ELP ASC ";	
                    
        
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }

    public function get_liste_module_importer($cn,$annee,$cmp,$etape=null) {

        $query="SELECT DISTINCT(R.COD_ELP),EP.COD_CMP,EP.COD_NEL,EP.LIB_ELP,R.TEM_EXI_NOT_ELP 
                FROM resultat_elp R, element_pedagogi EP 
                WHERE R.COD_ELP=EP.COD_ELP AND EP.COD_CMP='".$cmp."' AND EP.COD_NEL='MO' AND R.TEM_EXI_NOT_ELP ='O' AND ( ";

                if($etape){
                    foreach($etape as $etp){
                        if($etp == 'IIGI'){
                            $query .=" R.COD_ELP LIKE 'IIGI%' OR R.COD_ELP LIKE 'IISI%' OR R.COD_ELP LIKE 'IIGL%' OR";
                        }else{
                            $query .= "  R.COD_ELP LIKE '".$etp."%' OR";
                        }
                    }
                    $query=substr($query, 0, -2);   
                }else{
                    $query .= "  R.COD_ELP LIKE 'II%' OR R.COD_ELP LIKE 'IMB%' OR R.COD_ELP LIKE 'IMM%'";
                }
                    
        $query .= " ) AND R.COD_ANU='".$annee."' ORDER BY R.COD_ELP ASC";	
                    
                
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }

    public function getNbetudiantByPays($cn,$annee) {

      //  $query="SELECT count(DISTINCT i.COD_IND) as nb_by_pays , i.COD_PAY_NAT as code_pays , i.COD_PAY_NAT as code_iso  FROM  individu i , adresse a , ins_adm_etp etp WHERE  i.COD_IND = a.COD_IND AND i.COD_IND = etp.COD_IND AND etp.COD_ANU LIKE '".$annee."%' GROUP BY i.COD_PAY_NAT  
      //  ORDER BY `nb_by_pays`  DESC;";

        $query1 = "SELECT count(DISTINCT i.COD_ETU) as nb_by_pays , i.COD_PAY_NAT as code_pays , i.COD_PAY_NAT as code_iso  FROM  individu i,adresse a,ins_adm_etp etp, annee_uni au WHERE  i.COD_IND = a.COD_IND and i.COD_IND = etp.COD_IND AND
        etp.COD_ANU= au.COD_ANU AND au.COD_ANU=".$annee." AND i.COD_PAY_NAT !=350 AND (etp.COD_ETP like 'II%' or etp.COD_ETP like 'IM%') GROUP BY i.COD_PAY_NAT ORDER BY `nb_by_pays` DESC";

        $result= $cn->fetchAllAssociative($query1);

        $pays_code_apo = [319,212,303,125,352,109,130,395,441,201,415,252,501,990,110,253,436,249,246,434,131,429,327,214,148,224,418,118,347,416,225,111,331,321,234,322,401,396,323,417,216,254,419,397,324,239,238,406,326,119,407,101,399,438,301,414,247,420,317,134,106,404,315,999,156,508,105,100,328,304,255,329,133,126,435,430,409,330,314,392,428,410,411,112,223,231,204,203,136,102,207,127,426,217,222,256,332,257,513,240,241,348,107,205,302,316,108,113,137,232,543,227,334,229,335,144,350,515,390,336,405,516,151,138,242,393,311,507,215,412,337,338,103,502,250,339,258,213,261,413,510,421,135,422,220,503,122,139,313,248,312,517,408,114,132,123,340,389,128,442,306,439,440,512,506,995,394,341,398,342,226,117,145,318,343,235,104,140,437,391,206,259,236,309,344,116,432,425,427,431,308,505,219,345,509,433,351,260,208,511,155,423,514,129,424,243,251,121,346,310,997] ;
		$pays_nom = ["Acores-Madère","Afghanistan","Afrique du Sud","Albanie","Algérie","Allemagne","Andorre","Angola","Antigua-et-Barduda","Arabie Saoudite","Argentine","Arménie","Australie","Autres pays","Autriche","Azerbaïdjan","Bahamas","Bahrein","Bangladesh","Barbade","Belgique","Bélize","Bénin","Bhoutan","Biélorussie","Birmanie","Bolivie","Bosnie-Herzégovine","Botswana","Brésil","Bruneï","Bulgarie","Burkina","Burundi","Cambodge","Cameroun","Canada","Cap Vert","Centrafricaine (République)","Chili","Chine","Chypre","Colombie","Comores","Congo","République de Corée","République Populaire Démocratique de Corée)","Costa Rica","Côte d'Ivoire","Croatie","Cuba","Danemark","Djibouti","Dominique","Égypte","El Salvador","Émirats Arabes Unis","Équateur","Érythrée","Espagne","Estonie","États-Unis","Éthiopie","Étranger sans autre indication","Ex-républ. Yougoslave de Macédoine","Fidji","Finlande","France","Gabon","Gambie","Géorgie","Ghana","Gibraltar","Grèce","Grenade","Groenland : Terr. Du Danemark","Guatémala","Guinée","Guinée Équatoriale","Guinée-Bissau","Guyana","Haïti","Honduras","Hongrie","Inde","Indonésie","Iran","Iraq","Irlande ou Eire","Islande","Israël","Italie","Jamaïque","Japon","Jordanie","Kazakhstan","Kenya","Kirghizistan","Kiribati","Koweït","Laos","Lesotho","Lettonie","Liban","Libéria","Libye","Lituanie","Liechtenstein","Luxembourg","Macao","Madagascar","Malaisie","Malawi","Maldives","Mali","Malte","Maroc","Marshall (Iles)","Maurice","Mauritanie","Mexique","Micronésie (états fédérés de)","Moldavie","Monaco","Mongolie","Mozambique","Namibie","Nauru","Népal","Nicaragua","Niger","Nigéria","Norvège","Nouvelle Zélande","Oman","Ouganda","Ouzbékistan","Pakistan","Palestine","Panama","Papouasie - Nouvelle-Guinée","Paraguay","Pays-Bas","Pérou","Philippines","Pitcairn (Ile)","Pologne","Portugal","Prov.Espagnoles d'Afrique","Qatar","Rép. Démocratique du Congo (ex Zaire)","République des Iles Palaos","République Dominicaine","Roumanie","Royaume-Uni","Russie","Rwanda","Sahara Occidental","Saint Marin","Saint-Christophe-et-Nievès","Sainte-Hélène","Sainte-Lucie","Saint-Vincent-et-les Grenadines","Salomon (Iles)","Samoa Occidentales","Sans Nationalité","Sao Tomé-et-Principe","Sénégal","Seychelles","Sierra Léone","Singapour","Slovaquie","Slovénie","Somalie","Soudan","Sri Lanka","Suède","Suisse","Suriname","Swaziland","Syrie","Tadjikistan","Taïwan","Tanzanie","Tchad","Tchèque (République)","Terr. d' USA en Amérique","Terr. du Royaume-Uni aux Antilles","Terr. du Royaume-Uni dans l'Atlantique","Territoire des Pays-Bas","Territoires Britannique de l'océan Indien","Territoires des USA en Océanie","Thaïlande","Togo","Tonga","Trinité et Tobago","Tunisie","Turkménistan","Turquie","Tuvalu","Ukraine","Uruguay","Vanuatu","Vatican ou Saint-Siège","Vénézuéla","Vietnam","Yémen","Serbie et Monténégro","Zambie","Zimbabwé","Guinée-Bissau"];
		$pays_iso_code = ["ACORES","AF","ZA","AL","DZ","DE","AD","AO","AG","SA","AR","AM","AU","OTHER","AT","AZ","BS","BH","BD","BB","BE ","BZ","BJ","BT","BY","MM","BO","BA","BW","BR","BN","BG","BF","BI","KH","CM","CA","CV","CF","CL","CN","CY","CO","KM","CG","KR","KP","CR","CI","HR","CU","DK","DJ","DM","EG","SV","AE","EC","ER","ES","EE","US","ET","ETRANGER","MK","FJ","FI","FR","GA","GM","GE","GH","GI","GR","GD","GL","GT","GN","GQ","GW","GY","HT","HN","HU","IN","ID","IR","IQ","IE","IS","IL","IT","JM","JP","JO","KZ","KE","KG","KI","KW","LA","LS","LV","LB","LR","LY","LT","LI","LU","MO","MG","MY","MW","MV","ML","MT","MA","MH","MU","MR","MX","FM","MD","MC","MN","MZ","NA","NR","NP","NI","NE","NG","NO","NZ","OM","UG","UZ","PK","PS","PA","PG","PY","NL","PE","PH","PN","PL","PT","PROV_ESP_AFR","QA","CD","PW","DO","RO","GB","RU","RW","EH","SM","KN","SH","LC","VC","SB","WS","SANS_NAT","ST","SN","SC","SL","SG","SK","SI","SO","SD","LK","SE","CH","SR","SZ","SY","TJ","TW","TZ","TD","CZ","X1","X2","X3","X4","X5","X6","TH","TG","TO","TT","TN","TM","TR","TV","UA","UY","VU","VA","VE","VN","YE","RS","ZM","ZW","GW"] ;
     
    
        foreach ($result as $key=>$value) {
            
        $r_key = array_search($result[$key]['code_pays'],$pays_code_apo);
        if( $r_key == false ) // || $result[$key]['code_pays']== 990
        {
            $r_key = array_search(990,$pays_code_apo);
        }

        $result[$key]['code_pays'] = $pays_nom[$r_key];
        $result[$key]['code_iso'] = strtolower( $pays_iso_code[$r_key]);   
        }
        
        return  $result ; 

    }


    public function etudiantByInd($code,$cn,$annee=null) {

        $query="SELECT * FROM  individu i,adresse a,ins_adm_etp etp WHERE i.COD_ETU='".$code."' and i.COD_IND = a.COD_IND  and i.COD_IND = etp.COD_IND AND etp.COD_ANU LIKE '".$annee."%'";
        $result= $cn->fetchAssociative($query);
          
        return  $result ; 

    }
    public function insAdmLastByInd($code,$cn ,$cmp,$etat) {

        $query="SELECT * FROM ins_adm_etp ie, annee_uni a WHERE  ie.COD_IND='".$code."'  and ie.COD_CMP= '".$cmp."' and ie.COD_ANU=a.COD_ANU and ie.ETA_IAE='".$etat."' order by ie.COD_ANU desc";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }
    public function insPedLastByInd($code,$cn,$annee=null) {

        $query="SELECT * FROM ins_pedagogi_etp ie, annee_uni a, etape e WHERE ie.COD_ANU=a.COD_ANU and ie.COD_ETP= e.COD_ETP and ie.COD_IND='".$code."' and ie.COD_ANU like '".$annee."%'  order by ie.COD_ANU desc";
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }

    public function getAnneeUnivEncours($cn) {

        $query="SELECT * FROM annee_uni WHERE ETA_ANU_IAE='O'";
        $result= $cn->fetchAssociative($query); 
        return  $result ; 

    }

    public function getAnneeUnivAll($cn) {

        $query="SELECT COD_ANU FROM annee_uni ORDER BY COD_ANU DESC";
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }


    public function resultat_elp($cn,$initiale,$master,$code,$annee=null) {

        $query="SELECT DISTINCT(resultat_elp.COD_ELP), resultat_elp.COD_ANU, resultat_elp.COD_SES, resultat_elp.NOT_ELP,resultat_elp.NOT_PNT_JUR_ELP, resultat_elp.BAR_NOT_ELP, resultat_elp.COD_TRE, element_pedagogi.LIB_ELP, element_pedagogi.COD_NEL, typ_resultat.LIC_TRE,ins_pedagogi_etp.COD_ETP 
        FROM resultat_elp LEFT OUTER JOIN ins_pedagogi_etp  ON (resultat_elp.COD_IND=ins_pedagogi_etp.COD_IND AND resultat_elp.COD_ANU=ins_pedagogi_etp.COD_ANU ) 
            JOIN element_pedagogi ON (resultat_elp.COD_ELP=element_pedagogi.COD_ELP)
            LEFT OUTER JOIN typ_resultat ON (resultat_elp.COD_TRE=typ_resultat.COD_TRE) 
        WHERE ((  resultat_elp.COD_ELP LIKE '".$master."%' OR" ;
        foreach($initiale as $init){
                
                $query .= "  resultat_elp.COD_ELP  LIKE '".$init."%' OR";
                
        }
        $query=substr($query, 0, -2);
        $query .= " ) AND element_pedagogi.COD_NEL NOT LIKE 'AN%'  AND resultat_elp.COD_ELP NOT LIKE '%000%'
            AND ins_pedagogi_etp.COD_IND='".$code."'  AND resultat_elp.COD_ANU LIKE '".$annee."%') 
            ORDER BY resultat_elp.COD_ELP ASC,resultat_elp.COD_ANU ASC ,resultat_elp.COD_SES DESC";	
                    
                
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }

    public function resultat_vet($cn,$code,$typeResultat=null,$annee=null) {

        $query="SELECT DISTINCT(resultat_vet.COD_ETP), individu.COD_ETU, resultat_vet.COD_ANU, resultat_vet.COD_SES, resultat_vet.NOT_VET, resultat_vet.BAR_NOT_VET, resultat_vet.COD_TRE,  typ_resultat.LIC_TRE,resultat_vet.NOT_PNT_JUR_VET
                FROM individu JOIN resultat_vet ON (resultat_vet.COD_IND=individu.COD_IND) 
                    LEFT OUTER JOIN typ_resultat ON (resultat_vet.COD_TRE=typ_resultat.COD_TRE) 
                WHERE  (individu.COD_IND='".$code."' AND resultat_vet.COD_TRE LIKE '".$typeResultat."%' AND resultat_vet.COD_ANU LIKE '".$annee."%' ) 
                ORDER BY resultat_vet.COD_ANU ASC , resultat_vet.COD_ETP ASC,resultat_vet.COD_SES DESC";
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }

     public function resultat_vet_global($cn,$code,$typeResultat=null,$annee=null) {

        $query="SELECT DISTINCT(resultat_vet.COD_ETP), individu.COD_ETU, resultat_vet.COD_ANU, resultat_vet.COD_SES, resultat_vet.NOT_VET, resultat_vet.BAR_NOT_VET, resultat_vet.COD_TRE,  typ_resultat.LIC_TRE ,gr.ETA_AVC_VET ,resultat_vet.NOT_PNT_JUR_VET
                FROM individu JOIN resultat_vet ON (resultat_vet.COD_IND=individu.COD_IND) 
                    LEFT OUTER JOIN typ_resultat ON (resultat_vet.COD_TRE=typ_resultat.COD_TRE) LEFT JOIN grp_resultat_vet gr ON ( resultat_vet.COD_ETP = gr.COD_ETP )
                WHERE  (individu.COD_IND='".$code."'  AND resultat_vet.COD_ANU LIKE '".$annee."%' 
                AND  resultat_vet.COD_SES =1 AND resultat_vet.COD_ADM=1 AND gr.COD_SES = 1 AND gr.COD_ADM = 1 AND resultat_vet.COD_ANU=gr.COD_ANU) 
                ORDER BY resultat_vet.COD_ANU ASC , resultat_vet.COD_ETP ASC,resultat_vet.COD_SES DESC";
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }


    public function insAdmValidInd($code,$cn ,$etat,$cmp,$res) {

        $query="SELECT DISTINCT(r.COD_ANU),r.COD_ETP,r.NOT_VET,r.COD_TRE
                FROM ins_adm_etp ETP
                    LEFT OUTER JOIN resultat_vet r 
                    ON ETP.COD_ETP=r.COD_ETP
                WHERE  ETP.ETA_IAE='".$etat."'
                    AND  ETP.COD_CMP='".$cmp."'
                    AND  r.COD_IND='".$code."'
                    AND ( ";

                    foreach($res as $init){
                    
                            $query .= "  r.COD_TRE like '".$init."%' OR";
                                
                        }
                        $query=substr($query, 0, -2);
                        $query .=    " ) ORDER BY r.COD_ANU asc";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    public function insAdmValidInd_show($code,$cn ,$etat,$cmp,$res) {

        $query="SELECT DISTINCT(r.COD_ANU),r.COD_ETP,r.NOT_VET,r.COD_TRE
                FROM ins_adm_etp ETP
                    LEFT OUTER JOIN resultat_vet r 
                    ON ETP.COD_ETP=r.COD_ETP
                WHERE  ETP.ETA_IAE='".$etat."'
                    AND  ETP.COD_CMP='".$cmp."'
                    AND  r.COD_IND='".$code."'
                    AND (   r.COD_TRE like 'ADM%' OR r.COD_TRE like 'AJ' OR r.COD_TRE like 'ROR' OR r.COD_TRE like 'ABL') ORDER BY r.COD_ANU asc";

        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    public function insAdmDiplomeInd($code,$cn ,$etat,$cmp,$res) {

        $query="SELECT r.COD_ANU,etp.COD_ETP,r.COD_DIP
                FROM  ins_adm_etp etp 
                    INNER JOIN resultat_vdi r 
                    ON (etp.COD_IND = r.COD_IND AND etp.COD_ANU= r.COD_ANU AND etp.COD_DIP=r.COD_DIP)   
                WHERE  etp.ETA_IAE='".$etat."'
                    AND  etp.COD_CMP='".$cmp."'
                    AND r.COD_IND= '".$code."'
                    AND (r.COD_TRE like '".$res."%')";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    public function insAp2($code,$annee ,$etape,$cn) {

        $query="SELECT i.COD_IND,i.COD_ETU,rv.COD_TRE,rv.NOT_VET,rv.COD_ANU
                FROM individu i ,resultat_vet rv 
                where  i.COD_ETU='".$code."'
                    AND (rv.COD_TRE='ADM' or rv.COD_TRE='ADMR')
                    AND i.COD_IND=rv.COD_IND";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    
    public function chefFiliereEmail($diplome) {

        $query="SELECT * FROM utilisateurs u WHERE u.roles like '%ROLE_CHEF_FIL%' and u.codes like '%".$diplome."%'";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative(); 
     
        return  $result ;

    }


    public function getGroupeByInd($code,$annee,$cn) {

        $query="SELECT * FROM ind_affecte_gpe i LEFT JOIN groupe g on i.COD_GPE=g.COD_GPE where i.COD_ANU='".$annee."' and i.COD_IND='".$code."'";
        $result= $cn->fetchAssociative($query); 
        return  $result ; 

    }

    public function getExam($cn,$centre) {

        $query="SELECT * FROM periode_exa WHERE COD_CIN='".$centre."'";
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }

    public function getExamPlanning($cn,$periode) {

        $query="SELECT ind.COD_ETU,ind.LIB_NOM_PAT_IND,ind.LIB_PR1_IND,e.LIB_EPR,prd.DAT_DEB_PES ,prd.COD_EPR, prd.DUR_EXA_EPR_PES , concat(prd.DHH_DEB_PES,concat(':',prd.DMM_DEB_PES)) as heure, prd.COD_SAL,concat(pa.LIB_NOM_PAT_PER,concat(' ',pa.LIB_PR1_PER))  as responsable
        FROM prd_epr_sal_anu prd 
          LEFT JOIN pes_ind ind on prd.COD_PES = ind.COD_PES 
          LEFT JOIN epreuve e on prd.COD_EPR=e.COD_EPR 
          LEFT JOIN sal_cin s on prd.COD_SAL=s.COD_SAL 
          LEFT JOIN res_epr pe on e.COD_EPR=pe.COD_EPR 
          LEFT JOIN personnel pa on pe.COD_PER=pa.COD_PER 
        WHERE prd.COD_PXA='".$periode."' AND ind.COD_ETU='22009894';";
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }

    public function getExamSurveillantBySalle($cn,$periode,$salle,$epreuve) {

        $query="SELECT  prd.COD_SAL,concat(pa.LIB_NOM_PAT_PER,concat(' ',pa.LIB_PR1_PER))  as surveillant
        FROM prd_epr_sal_anu prd 
          INNER JOIN pes_per ind on prd.COD_PES = ind.COD_PES 
          LEFT JOIN sal_cin s on prd.COD_SAL=s.COD_SAL 
          LEFT JOIN personnel pa on ind.COD_PER=pa.COD_PER 
        WHERE prd.COD_PXA='".$periode."' AND prd.COD_SAL='".$salle."' AND prd.COD_EPR='".$epreuve."'";
        $result= $cn->fetchAllAssociative($query); 
        return  $result ; 

    }
    

    public function getEtatDelebiration($cn,$annee,$etape) {

        $query="SELECT * FROM grp_resultat_vet g WHERE g.COD_ANU= '".$annee."' and g.COD_ETP ='".$etape."' and g.COD_SES='1' and g.COD_ADM='1' ";
        $result= $cn->fetchAssociative($query); 
        return  $result ; 

    }



    public function nbEtudiantAP2($annee ,$etape,$etat,$centre,$cn) {

        $query="SELECT COUNT(I.COD_ETU) as nb
                FROM ins_adm_etp  ETP,
                    individu  I,
                    annee_uni  AU
                WHERE ETP.COD_ANU= AU.COD_ANU
                    AND AU.COD_ANU = '".$annee."'
                    AND ETP.COD_ETP='".$etape."'
                    AND ETP.ETA_IAE='".$etat."'
                    AND ETP.COD_CMP='".$centre."'
                    AND ETP.COD_IND=I.COD_IND";
        $result= $cn->fetchAssociative($query);
        return  $result ; 

    }

    public function nbEtudiantAP2_Garcon($annee ,$etape,$etat,$centre,$cn) {

        $query="SELECT COUNT(I.COD_ETU) as nb
                FROM ins_adm_etp  ETP,
                    individu  I,
                    annee_uni  AU
                WHERE ETP.COD_ANU= AU.COD_ANU
                    AND AU.COD_ANU = '".$annee."'
                    AND ETP.COD_ETP='".$etape."'
                    AND I.COD_SEX_ETU='M'
                    AND ETP.ETA_IAE='".$etat."'
                    AND ETP.COD_CMP='".$centre."'
                    AND ETP.COD_IND=I.COD_IND";
        $result= $cn->fetchAssociative($query);
        return  $result ; 

    }

    public function nbEtudiantAP2_Fille($annee ,$etape,$etat,$centre,$cn) {

        $query="SELECT COUNT(I.COD_ETU) as nb
                FROM ins_adm_etp  ETP,
                    individu  I,
                    annee_uni  AU
                WHERE ETP.COD_ANU= AU.COD_ANU
                    AND AU.COD_ANU = '".$annee."'
                    AND ETP.COD_ETP='".$etape."'
                    AND I.COD_SEX_ETU='F'
                    AND ETP.ETA_IAE='".$etat."'
                    AND ETP.COD_CMP='".$centre."'
                    AND ETP.COD_IND=I.COD_IND";
        $result= $cn->fetchAssociative($query);
        return  $result ; 

    }


    public function iiap2Liste_valide($annee ,$etape,$cn) {

        $query=" SELECT distinct(i.COD_IND),i.COD_ETU,rv.COD_TRE,rv.NOT_VET,rv.COD_ANU
                FROM individu i ,resultat_vet rv 
                WHERE rv.COD_ETP='".$etape."' 
                    AND rv.COD_ANU=".$annee."
                    AND (rv.COD_TRE='ADM' OR rv.COD_TRE='ADMR') 
                    AND i.COD_IND=rv.COD_IND order by rv.COD_ANU desc";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }


    public function resEtudiant($code,$cn) {

        $query="SELECT i.COD_ETU,rv.COD_IND,rv.COD_TRE,rv.NOT_VET,rv.COD_ETP,rv.COD_ANU
                FROM resultat_vet rv, individu i
                WHERE rv.COD_IND='".$code."' 
                    AND rv.COD_IND=i.COD_IND
                    AND rv.NOT_VET is not null order by rv.COD_ANU desc";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    /// scolarite statistique//

    public function evolutioneffectif($etat,$cmp,$initiale,$master,$cn) {

        $query="SELECT count(*) as NOMBRE,ins_adm_etp.COD_ANU
                FROM ins_adm_etp,individu
                WHERE  ins_adm_etp.ETA_IAE='".$etat."'
                    AND ins_adm_etp.COD_CMP='".$cmp."'
                    AND ins_adm_etp.COD_IND=individu.COD_IND
                    AND ( ins_adm_etp.COD_ETP like '".$master."%' OR";

                foreach($initiale as $init){
                
                    $query .= "  ins_adm_etp.COD_ETP LIKE '".$init."%' OR";
                        
                }
        $query=substr($query, 0, -2);
        $query .= " ) GROUP BY ins_adm_etp.COD_ANU
               ORDER BY ins_adm_etp.COD_ANU ASC";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    public function nbInsAsDiplome($annee,$etat,$cmp,$initiale,$master,$cn) {

        $query="SELECT count(*) as NOMBRE,ins_adm_etp.COD_DIP
                FROM ins_adm_etp,individu
                WHERE ins_adm_etp.COD_ANU=".$annee."
                    AND ins_adm_etp.ETA_IAE='".$etat."'
                    AND ins_adm_etp.COD_CMP='".$cmp."'
                    AND ins_adm_etp.COD_IND=individu.COD_IND
                    AND ( ins_adm_etp.COD_ETP like '".$master."%' OR ";
                foreach($initiale as $init){
                
                    $query .= "  ins_adm_etp.COD_ETP LIKE '".$init."%' OR";
                            
                }
        $query=substr($query, 0, -2);
        $query .= " )
               GROUP BY ins_adm_etp.COD_DIP
               ORDER BY ins_adm_etp.COD_DIP ASC";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }


    public function nbInsAsEtapeNV($annee,$etat,$cmp,$initiale,$master,$cn,$genre=null) {

        $query="SELECT COUNT(*) as NOMBRE,ETP.COD_ETP
                FROM ins_adm_etp ETP,individu I
                WHERE ETP.COD_ANU=".$annee."
                    AND ETP.ETA_IAE='".$etat."'
                    AND ETP.COD_CMP='".$cmp."'";
        if($genre!=null){
            $query= $query. " AND I.COD_SEX_ETU='".$genre."'";
        }   
        $query= $query." AND I.COD_IND=ETP.COD_IND AND ( ETP.COD_ETP like '".$master."%' OR ";
        foreach($initiale as $init){
        
            $query .= "  ETP.COD_ETP LIKE '".$init."%' OR";
                    
        }
        $query=substr($query, 0, -2);
        $query .= " )
                    AND (I.COD_IND NOT IN (SELECT A1.COD_IND 
                                   FROM ins_adm_etp A1
                                   WHERE I.COD_IND = A1.COD_IND 
                                   AND A1.COD_ANU=".($annee-1)."
                                   AND A1.ETA_IAE='".$etat."'
                                   AND A1.COD_CMP='".$cmp."')
                    OR I.COD_IND IN (SELECT r.COD_IND 
                                   FROM resultat_vet r
                                   WHERE I.COD_IND = r.COD_IND 
                                   AND r.COD_ANU=".($annee-1)."
                                   AND r.COD_ETP='IIAP2'
                                   AND (r.COD_TRE='ADM' or r.COD_TRE='ADMR')
                                  ))
            GROUP BY ETP.COD_ETP";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    public function nbInsAsEtape($annee,$etat,$cmp,$initiale,$master,$cn,$genre=null) {

        $query= "SELECT count(*) as NOMBRE,ins_adm_etp.COD_ETP
                FROM ins_adm_etp,individu 
                WHERE ins_adm_etp.COD_ANU= ".$annee;
        if($genre!=null){
            $query= $query. " AND individu.COD_SEX_ETU='".$genre."'";
        }   
        $query= $query." AND ins_adm_etp.ETA_IAE='".$etat."'
                    AND ins_adm_etp.COD_CMP='".$cmp."'
                    AND ins_adm_etp.COD_IND=individu.COD_IND
                    AND ( ins_adm_etp.COD_ETP like '".$master."%' OR ";
                foreach($initiale as $init){
                
                    $query .= "  ins_adm_etp.COD_ETP LIKE '".$init."%' OR";
                            
                }
        $query=substr($query, 0, -2);
        $query .= " )
                GROUP BY ins_adm_etp.COD_ETP
                ORDER BY ins_adm_etp.COD_ETP ASC";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    


    public function nbInsNVByAnnee($annee,$etat,$cmp,$initiale,$master,$cn) {

        $query="SELECT COUNT(*)
                FROM ins_adm_etp ETP,individu I
                WHERE ETP.COD_ANU=".$annee."
                    AND ETP.ETA_IAE='".$etat."'
                    AND ETP.COD_CMP='".$cmp."'
                    AND I.COD_IND=ETP.COD_IND
                    AND ( ETP.COD_ETP like '".$master."%' OR ";
                    foreach($initiale as $init){
                    
                        $query .= "  ETP.COD_ETP LIKE '".$init."%' OR";
                                
                    }
            $query=substr($query, 0, -2);
            $query .= " )
                    AND (I.COD_IND NOT IN (SELECT A1.COD_IND 
                                   FROM ins_adm_etp A1
                                   WHERE I.COD_IND = A1.COD_IND 
                                   AND A1.COD_ANU=".($annee-1)."
                                   AND A1.ETA_IAE='".$etat."'
                                   AND A1.COD_CMP='".$cmp."')
                    OR I.COD_IND IN (SELECT r.COD_IND 
                                   FROM resultat_vet r
                                   WHERE I.COD_IND = r.COD_IND 
                                   AND r.COD_ANU=".($annee-1)."
                                   AND r.COD_ETP='IIAP2'
                                   AND (r.COD_TRE='ADM' or r.COD_TRE='ADMR')
                    ))";
        $result= $cn->fetchFirstColumn($query);
        return  $result ; 

    }

    public function nbEtudiantByGenre($annee,$etat,$cmp,$initiale,$master,$cn,$genre=null) {

        $query="SELECT COUNT(I.COD_ETU)
                FROM ins_adm_etp  ETP,
                    individu  I,
                    annee_uni  AU
                WHERE ETP.COD_ANU= AU.COD_ANU
                    AND AU.COD_ANU=".$annee;
        if($genre!=null){
            $query= $query. " AND I.COD_SEX_ETU='".$genre."'";
        }   
        $query= $query." AND ETP.ETA_IAE='".$etat."'
                    AND ETP.COD_CMP='".$cmp."'
                    AND ETP.COD_IND=I.COD_IND
                    AND ( ETP.COD_ETP like '".$master."%' OR ";
                    foreach($initiale as $init){
                    
                        $query .= "  ETP.COD_ETP LIKE '".$init."%' OR";
                                
                    }
            $query=substr($query, 0, -2);
            $query .= " )";
        $result= $cn->fetchFirstColumn($query);
        return  $result ; 

    }


    // cooperation function

    public function nbEtudiantByGenreStage($annee,$etat,$cmp,$initiale,$master,$cn,$genre=null) {

        $query = " SELECT count(etp.COD_IND) AS homme ,etp.COD_ANU
                FROM individu i
                INNER JOIN ins_adm_etp etp 
                    ON i.COD_IND=etp.COD_IND
                INNER JOIN resultat_vdi r 
                    ON (etp.COD_IND = r.COD_IND AND etp.COD_ANU= r.COD_ANU AND etp.COD_DIP=r.COD_DIP)   
                WHERE   etp.ETA_IAE='".$etat."'
                    AND  etp.COD_CMP='".$cmp."'
                    AND r.COD_ADM = 1 and r.COD_SES = 1 AND r.COD_TRE LIKE 'ADM%' ";
        if($genre!=null){
            $query= $query. " AND i.COD_SEX_ETU='".$genre."'";
        }  
        $query= $query." AND r.COD_ANU ='".($annee-1)."' 
                AND ( etp.COD_ETP like '".$master."%' OR ";
                    foreach($initiale as $init){
                    
                        $query .= "  etp.COD_ETP LIKE '".$init."%' OR";
                                
                    }
        $query=substr($query, 0, -2);
        $query .= " ) GROUP BY etp.COD_ANU ";
        $result= $cn->fetchFirstColumn($query);
        return  $result ; 

    }

    public function evolutioneffectifStage($etat,$cmp,$initiale,$master,$cn,$typeResultat) {

        $query=" SELECT count(etp.COD_IND) as NOMBRE,etp.COD_ANU
                FROM  ins_adm_etp etp 
                INNER JOIN resultat_vdi r 
                    ON (etp.COD_IND = r.COD_IND AND etp.COD_ANU= r.COD_ANU AND etp.COD_DIP=r.COD_DIP)   
                WHERE   etp.ETA_IAE='".$etat."'
                    AND  etp.COD_CMP='".$cmp."'
                    AND r.COD_ADM = 1 and r.COD_SES = 1
                    AND etp.COD_ANU >= '2011'
                    AND r.COD_TRE LIKE '".$typeResultat."%'
                    AND ( etp.COD_ETP like '".$master."%' OR ";
                    foreach($initiale as $init){
                    
                        $query .= "  etp.COD_ETP LIKE '".$init."%' OR";
                                
                    }
        $query=substr($query, 0, -2);
        $query .= " )
                GROUP BY etp.COD_ANU
                ORDER BY etp.COD_ANU asc";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 
    }



    public function nbAsDiplomeCooperation($annee,$etat,$cmp,$initiale,$master,$cn,$typeResultat) {

        $query=" SELECT count(etp.COD_IND) as NOMBRE,etp.COD_DIP,r.COD_ANU
                FROM  ins_adm_etp etp 
                INNER JOIN resultat_vdi r 
                    ON (etp.COD_IND = r.COD_IND AND etp.COD_ANU= r.COD_ANU AND etp.COD_DIP=r.COD_DIP)   
                WHERE   etp.ETA_IAE='".$etat."'
                    AND  etp.COD_CMP='".$cmp."'
                    AND r.COD_ADM = 1 AND r.COD_SES = 1
                    AND etp.COD_ANU = '".$annee."'
                    AND r.COD_TRE LIKE '".$typeResultat."%'
                    AND ( etp.COD_ETP like '".$master."%' OR ";
                    foreach($initiale as $init){
                    
                        $query .= "  etp.COD_ETP LIKE '".$init."%' OR";
                                
                    }
        $query=substr($query, 0, -2);
        $query .= " )
                GROUP BY etp.COD_DIP,r.COD_ANU
                ORDER BY r.COD_ANU desc,etp.COD_DIP asc";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 
    }


    public function getNbAsDiplomeStage($annee,$etat,$cmp,$initiale,$master,$cn) {

        $query=" SELECT count(*) as NOMBRE,r.COD_DIP
                FROM   resultat_vdi r 
                     
                WHERE      r.COD_ADM = 1 and r.COD_SES = 1 and r.COD_TRE LIKE 'ADM%'
                    AND r.COD_ANU = '".($annee-1)."'
                    AND ( r.COD_DIP like '".$master."%' OR ";
                    foreach($initiale as $init){
                    
                        $query .= "  r.COD_DIP LIKE '".$init."%' OR";
                                
                    }
        $query=substr($query, 0, -2);
        $query .= " )
                GROUP BY r.COD_DIP
                ORDER BY r.COD_DIP asc";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    public function getInscritsFC($etat,$cmp,$initiale,$master,$cn) {

        $query="SELECT DISTINCT(i.COD_IND),i.COD_ETU,i.LIB_NOM_PAT_IND AS nom,i.LIB_PR1_IND AS prenom,i.DATE_NAI_IND AS dateNaissance,i.CIN_IND AS cin,i.COD_NNE_IND AS cne,ie.COD_DIP AS dip , ie.COD_ANU as annee , ie.COD_ETP AS etape
                FROM ins_adm_etp ie,individu i
                WHERE  ie.ETA_IAE='".$etat."'
                    AND ie.COD_CMP='".$cmp."'
                    AND ie.COD_IND=i.COD_IND
                    AND ( ie.COD_ETP like '".$master."%' OR";

                foreach($initiale as $init){
                
                    $query .= "  ie.COD_ETP LIKE '".$init."%' OR";
                        
                }
        $query=substr($query, 0, -2);
        $query .= " ) ";
        $result= $cn->fetchAllAssociative($query);
        return  $result ; 

    }

    public function getInscritsFC_COD_DIP($etat,$cmp,$initiale,$master,$cn,$CODE_DIP,$CODE_ANU) {

        $query="SELECT DISTINCT(i.COD_IND),i.COD_ETU,i.LIB_NOM_PAT_IND AS nom,i.LIB_PR1_IND AS prenom,i.DATE_NAI_IND AS dateNaissance,i.CIN_IND AS cin,i.COD_NNE_IND AS cne,ie.COD_DIP AS dip
                FROM ins_adm_etp ie,individu i
                WHERE  ie.ETA_IAE='".$etat."'
                    AND ie.COD_CMP='".$cmp."'
                    AND ie.COD_IND=i.COD_IND
                    AND ie.COD_DIP='".$CODE_DIP."'
                    AND ie.COD_ANU='".$CODE_ANU."'
                    AND ( ie.COD_ETP like '".$master."%' OR";

                foreach($initiale as $init){
                
                    $query .= "  ie.COD_ETP LIKE '".$init."%' OR";
                        
                }
        $query=substr($query, 0, -2);
        $query .= " ) ";
        $result= $cn->fetchAllAssociative($query);
        
        return  $result ; 

    }

    

// get attestation de scolarité
public function attestationScByInd($code,$cn,$annee,$sig) {

    $query="SELECT I.COD_IND,I.COD_ETU,I.CIN_IND,I.LIB_NOM_PAT_IND,I.LIB_PR1_IND,I.LIB_NOM_IND_ARB,I.LIB_PRN_IND_ARB,I.LIB_VIL_NAI_ETU,I.LIB_VIL_NAI_ETU_ARB,I.COD_PAY_NAT,P.LIB_PAY,P.LIB_PAY_ARB,I.DATE_NAI_IND,I.COD_SEX_ETU,I.COD_FAM,I.COD_NNE_IND,I.COD_DEP_PAY_NAI
    ,ETP.COD_ANU,E.COD_ETP,VE.COD_VRS_VET,VE.LIM1_VET,VE.LIB_WEB_VET,S.QUA_SIG,S.NOM_SIG
    ,VD.COD_DIP,VD.COD_VRS_VDI,VD.LIC_VDI,VD.IT1_VDI,VD.IT2_VDI,VD.TIT_1_VDI,VD.LIC_VDI_ARB,VD.IT1_VDI_ARB,VD.IT2_VDI_ARB,VD.TIT_1_VDI_ARB
    ,C.COD_CMP,C.LIB_CMP,C.LIB_AD1_CMP,C.LIB_AD2_CMP,C.LIB_AD3_CMP,C.INT_1_EDI_DIP_CMP,C.INT_2_EDI_DIP_CMP,C.LIB_CMP_ARB,C.LIB_ART_CMP,C.LIB_AD1_CMP_ARB,C.LIB_AD2_CMP_ARB,C.LIB_AD3_CMP_ARB,C.LIB_TTR,C.LIB_TTR_ARB,C.LIB_PHR1,C.LIB_PHR2,C.LIB_PHR3,C.LIB_VIL_CMP,C.LIB_VIL_CMP_ARB,C.INT_1_EDI_DIP_CMP_ARB,C.INT_2_EDI_DIP_CMP_ARB,E.LIB_ETP
    FROM ins_adm_etp ETP, individu I, pays P, etape E, version_etape VE, version_diplome VD,composante C, signataire S
    WHERE ETP.COD_IND=I.COD_IND AND I.COD_PAY_NAT=P.COD_PAY AND ETP.COD_ETP=E.COD_ETP AND ETP.COD_VRS_VET =VE.COD_VRS_VET AND ETP.COD_VRS_VDI= VD.COD_VRS_VDI AND  ETP.COD_CMP=C.COD_CMP
    AND ETP.COD_ANU='".$annee."' AND ETP.ETA_IAE='E' AND I.COD_ETU='".$code."' AND ETP.COD_ETP=VE.COD_ETP AND ETP.COD_DIP=VD.COD_DIP AND C.COD_CO_SIG=S.COD_SIG AND S.COD_SIG='".$sig."'";
    $result= $cn->fetchAssociative($query);
      
    return  $result ; 

}

 // get attestation de réussite
 public function attestationReussiteByInd($etat,$cmp,$document,$cn) {

    $query="SELECT I.COD_IND,I.COD_ETU,I.CIN_IND,I.LIB_NOM_PAT_IND
    ,I.LIB_PR1_IND,I.LIB_NOM_IND_ARB,I.LIB_PRN_IND_ARB,I.LIB_VIL_NAI_ETU,I.LIB_VIL_NAI_ETU_ARB,I.COD_PAY_NAT,P.LIB_PAY,P.LIB_PAY_ARB,I.DATE_NAI_IND,I.COD_SEX_ETU,I.COD_FAM,I.COD_NNE_IND,R.COD_ANU,R.NOT_VET
    ,VR.COD_ETP,VR.COD_VRS_VET,E.LIB_ETP,E.LIB_ETP_ARB,E.LIC_ETP,E.LIC_ETP_ARB,C.COD_CMP,C.LIB_CMP,C.LIB_AD1_CMP,C.LIB_AD2_CMP,C.LIB_AD3_CMP
    ,C.INT_1_EDI_DIP_CMP,C.INT_2_EDI_DIP_CMP,C.LIB_CMP_ARB,C.LIB_ART_CMP ,C.LIB_AD1_CMP_ARB,C.LIB_AD2_CMP_ARB,C.LIB_AD3_CMP_ARB,C.LIB_TTR,C.LIB_TTR_ARB,C.LIB_PHR1
    ,C.LIB_PHR2,C.LIB_PHR3,C.LIB_VIL_CMP,C.LIB_VIL_CMP_ARB,C.INT_1_EDI_DIP_CMP_ARB,C.INT_2_EDI_DIP_CMP_ARB 
    FROM individu I, resultat_vet R,composante C,version_etape VR ,ins_adm_etp ETP, pays P, etape E
    WHERE I.COD_IND=ETP.COD_IND AND R.COD_ETP=VR.COD_ETP AND R.COD_VRS_VET =VR.COD_VRS_VET AND  ETP.COD_IND=R.COD_IND AND ETP.COD_CMP=C.COD_CMP 
          AND R.COD_TRE LIKE 'ADM%' AND ETP.COD_ANU='".$document->getAnneeEtape()."' AND R.COD_ETP='".$document->getCodeEtape()."' AND I.COD_ETU='".$document->getCodeEtudiant()->getCode()."'
          AND I.COD_PAY_NAT=P.COD_PAY AND R.COD_ETP=E.COD_ETP AND ETP.ETA_IAE='".$etat."' AND ETP.COD_CMP='".$cmp."' AND ETP.COD_VRS_VET=VR.COD_VRS_VET";
    $result= $cn->fetchAssociative($query);
      
    return  $result ; 

}

public function releve_note_etu($cn,$etape,$code,$annee,$version) {

    $query="SELECT RV.COD_OBJ_RVN,RV.COD_VRS_OBJ_RVN,RV.NUM_OCC_RVN,RV.LIB_CMT,RV.COD_ELP,RV.DEC_OBJ_MNP,R.LIB_RVN,R.COD_ETP,R.LIB_CMT_RVN,
                    RE.COD_ANU,RE.NOT_ELP,RE.COD_TRE,RE.NOT_PNT_JUR_ELP,RE.COD_SES,
                    RT.COD_ANU AS an,RT.NOT_VET,RT.NBR_RNG_ETU_VET,RT.NOT_PNT_JUR_VET,
                    TR.COD_TRE,TR.LIB_TRE,TR.LIC_TRE,
                    S.NOM_SIG,S.QUA_SIG,
                    I.COD_ETU,I.LIB_NOM_PAT_IND,I.LIB_PR1_IND,I.LIB_VIL_NAI_ETU,I.DATE_NAI_IND,I.COD_SEX_ETU,I.COD_FAM,I.COD_NNE_IND
    FROM rvn_manipule RV, releve_note R,resultat_elp RE,resultat_vet RT,typ_resultat TR, signataire S,individu I
    WHERE RV.COD_RVN=R.COD_RVN AND RE.COD_ELP=RV.COD_ELP AND I.COD_IND=RT.COD_IND
      AND RE.COD_ELP IN (SELECT RV.COD_ELP FROM  rvn_manipule RV, releve_note R WHERE RV.COD_RVN=R.COD_RVN  AND R.COD_OBJ_RVN='".$etape."')
      AND RE.COD_SES=1  AND RE.COD_ADM=1 AND RE.COD_IND='".$code."' AND R.COD_ETP=RT.COD_ETP 
      AND RT.COD_SES=1 AND RT.COD_ADM=1 AND RT.COD_IND='".$code."' AND RT.COD_ANU=".$annee." AND RT.COD_TRE LIKE 'ADM%'  AND RT.COD_TRE=TR.COD_TRE AND RT.COD_VRS_VET=R.COD_VRS_VET
      AND S.COD_SIG=R.COD_SIG AND RV.NUM_OCC_RVN=".$version."
    ORDER BY RV.COD_ELP ASC,RE.COD_ANU ASC
    ";  
     //dd($query);           
            
    $result= $cn->fetchAllAssociative($query); 
    return  $result ; 

}


public function get_liste_etudiant_inscrits($cn,$annee,$cmp,$etat,$formation) {

    $query="SELECT I.COD_IND,I.COD_ETU,I.LIB_NOM_PAT_IND,I.LIB_PR1_IND,I.COD_NNE_IND,I.CIN_IND,I.DATE_NAI_IND,I.LIB_VIL_NAI_ETU,I.COD_SEX_ETU,I.LIB_NOM_IND_ARB,I.LIB_PRN_IND_ARB,I.LIB_VIL_NAI_ETU_ARB,P.LIB_PAY,ETP.COD_ETP,ETP.COD_VRS_VET 
            FROM individu I , ins_adm_etp ETP ,pays P 
            WHERE I.COD_IND=ETP.COD_IND AND I.COD_PAY_NAT=P.COD_PAY AND ETP.ETA_IAE='".$etat."' AND ETP.COD_CMP='".$cmp."' AND ETP.COD_ANU='".$annee."'"; 
    if($formation =='FI'){
        $query .=" AND  (ETP.COD_ETP LIKE 'II%' OR ETP.COD_ETP LIKE 'IM%')";
    }else{
        $query .=" AND  (ETP.COD_ETP LIKE 'IC%' OR ETP.COD_ETP LIKE 'ID%')";
    }     
            
    $query .=" ORDER BY ETP.COD_ETP ASC , I.LIB_NOM_PAT_IND ASC , I.LIB_PR1_IND ASC";        
            
    $result= $cn->fetchAllAssociative($query); 
    return  $result ; 

}

public function get_liste_etudiant_laureat($cn,$annee,$formation) {

    $query="SELECT I.COD_IND,I.COD_ETU,I.LIB_NOM_PAT_IND,I.LIB_PR1_IND,I.COD_NNE_IND,I.CIN_IND,I.DATE_NAI_IND,I.LIB_VIL_NAI_ETU,I.COD_SEX_ETU,I.LIB_NOM_IND_ARB,I.LIB_PRN_IND_ARB,I.LIB_VIL_NAI_ETU_ARB,P.LIB_PAY,R.COD_DIP,R.COD_VRS_VDI 
    FROM individu I ,pays P,resultat_vdi R 
    WHERE I.COD_PAY_NAT=P.COD_PAY AND I.COD_IND=R.COD_IND  AND R.COD_ANU='".$annee."'  AND R.COD_SES=1 AND R.COD_ADM=1 AND R.COD_TRE LIKE 'ADM%'"; 
    if($formation =='FI'){
        $query .=" AND  (R.COD_DIP LIKE 'II%' OR R.COD_DIP LIKE 'IM%')";
    }else{
        $query .=" AND  (R.COD_DIP LIKE 'IC%' OR R.COD_DIP LIKE 'ID%')";
    }     
            
    $query .=" ORDER BY R.COD_DIP ASC , I.LIB_NOM_PAT_IND ASC , I.LIB_PR1_IND ASC";              
            
    $result= $cn->fetchAllAssociative($query); 
    return  $result ; 

}


public function nb_dem_etu_carte($statut) 
{
    $query=" SELECT COUNT(dipcarte.id) as n FROM `etudiplomecarte` dipcarte where  dipcarte.type = 'Carte' and dipcarte.decision = '".$statut."'";
    if($statut=="T"){
        $query=" SELECT COUNT(dipcarte.id) as n  FROM `etudiplomecarte` dipcarte where dipcarte.type = 'Carte' ";
        }
    if($statut=="TR"){
         $query=" SELECT COUNT(dipcarte.id) as n  FROM `etudiplomecarte` dipcarte where  dipcarte.type = 'Carte' and dipcarte.decision not in ('-1')";
        }
    $statement = $this->getEntityManager()->getConnection()->prepare($query);
    $r = $statement->executeQuery()->fetchAllAssociative();  
    return $r;

 }
public function nb_dem_etu_attest($statut) 
{
    $query=" SELECT COUNT(etu_attest.id) as n  FROM `etuattestation` etu_attest where etu_attest.decision = '".$statut."' ";
    if($statut=="T"){
        $query=" SELECT COUNT(etu_attest.id) as n  FROM `etuattestation` etu_attest ";
        }
    if($statut=="TR"){
            $query=" SELECT COUNT(etu_attest.id) as n  FROM `etuattestation` etu_attest where etu_attest.decision not in ('-1') ";
            }
    $statement = $this->getEntityManager()->getConnection()->prepare($query);
    $r = $statement->executeQuery()->fetchAllAssociative();  
    return $r;
 }
public function nb_dem_etu_rel($statut) 
{ 
    $query=" SELECT COUNT(etu_rel.id) as n  FROM `etureleveattestation` etu_rel where  etu_rel.type = 'Relevé' and etu_rel.decision = '".$statut."'";
    if($statut=="T"){
        $query=" SELECT COUNT(etu_rel.id) as n FROM `etureleveattestation` etu_rel where etu_rel.type = 'Relevé' ";
        }
    if($statut=="TR"){
        $query=" SELECT COUNT(etu_rel.id) as n FROM `etureleveattestation` etu_rel where etu_rel.type = 'Relevé' and etu_rel.decision not in ('-1') ";
            }
    $statement = $this->getEntityManager()->getConnection()->prepare($query);
    $r = $statement->executeQuery()->fetchAllAssociative();  
    return $r;
}
public function nb_dem_etu_attreus($statut) 
{
   $query=" SELECT COUNT(etu_rel.id) as n  FROM `etureleveattestation` etu_rel where etu_rel.type = 'Attestation' and etu_rel.decision = '".$statut."'";
    if($statut=="T"){
        $query=" SELECT COUNT(etu_rel.id) as n  FROM `etureleveattestation` etu_rel where etu_rel.type = 'Attestation' ";
        }
    if($statut=="TR"){
            $query=" SELECT COUNT(etu_rel.id) as n  FROM `etureleveattestation` etu_rel where etu_rel.type = 'Attestation' and etu_rel.decision not in ('-1') ";
            }
   $statement = $this->getEntityManager()->getConnection()->prepare($query);
   $r = $statement->executeQuery()->fetchAllAssociative();  
   return $r;
 }


 public function nb_dem_etu_stage($statut,$niveau,$filiere,$annee) 
 {
    //$query=" SELECT COUNT(s.id) as n  FROM `stage` s where  s.statut = '".$statut."'";

    if($statut=="T"){

        if($filiere){

         $query=" SELECT COUNT(s.id) as n  FROM `stage` s where s.filiere like '%".$filiere."%' and s.anneeuniv = '".$annee."' ";

            if($filiere =='IIGI'){
                $query=" SELECT COUNT(s.id) as n  FROM `stage` s where s.anneeuniv = '".$annee."' and (s.filiere like '%".$filiere."%' or s.filiere like '%IISI%' or s.filiere like '%IIGL%' )";
            }


        }else{
            $query=" SELECT COUNT(s.id) as n  FROM `stage` s where s.niveau = '".$niveau."' and s.anneeuniv = '".$annee."' ";
        }
                

         }
     if($statut=="TR"){

        if($filiere){

            $query=" SELECT COUNT(s.id) as n  FROM `stage` s where s.statut in ('-1','-2') AND s.niveau = '".$niveau."' AND s.filiere like '%".$filiere."%' and s.anneeuniv = '".$annee."' ";

            if($filiere =='IIGI'){
                $query=" SELECT COUNT(s.id) as n  FROM `stage` s where s.statut in ('-1','-2') AND s.niveau = '".$niveau."' AND (s.filiere like '%".$filiere."%' or s.filiere like '%IISI%' or s.filiere like '%IIGL%') and s.anneeuniv = '".$annee."' ";
            }

        }else{
            $query=" SELECT COUNT(s.id) as n  FROM `stage` s where s.statut in ('-1','-2') AND s.niveau = '".$niveau."' and s.anneeuniv = '".$annee."' ";
          
        }
              

             }
           
    $statement = $this->getEntityManager()->getConnection()->prepare($query);
    $r = $statement->executeQuery()->fetchAllAssociative();  
    return $r;
  }
 
 
  public function nb_dem_etu_diplome($statut) 
  {
    //  $query=" SELECT COUNT(dipcarte.id) as n  FROM `etudiplomecarte` dipcarte where dipcarte.type = 'Diplome' and dipcarte.decision = '".$statut."'";
      if($statut=="T"){
      $query=" SELECT COUNT(dipcarte.id) as n  FROM `etudiplomecarte` dipcarte where dipcarte.type = 'Diplome' ";
      }
      if($statut=="TR"){
     $query=" SELECT COUNT(dipcarte.id) as n  FROM `etudiplomecarte` dipcarte where dipcarte.type = 'Diplome' and dipcarte.decision not in ('-1') ";
          }
      $statement = $this->getEntityManager()->getConnection()->prepare($query);
      $r = $statement->executeQuery()->fetchAllAssociative();  
      return $r;
  } 

    

}
