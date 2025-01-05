<?php

namespace App\Controller;

use App\Entity\Etudiants;
use App\Entity\Edt\Enseignements;
use App\Entity\Edt\Seances;
use App\Entity\Utilisateurs;

use App\Entity\Customer\Filiere;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as droitAcces; 


class EmploiController extends AbstractController
{

    #[Route(path: '/emploisearch', name: 'emploisearch')]
    public function emploiAction_old(Security $security,Connection $conn)
    {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['EDT_DATABASE_URL'].'',);
        $dbh = DriverManager::getConnection($connectionParams, $config);

        $sqlgroupe = "SELECT ressources_groupes.codeGroupe AS codeE ,ressources_groupes.alias AS nom FROM ressources_groupes WHERE deleted='0' ORDER BY nom ASC";
       
        $req_groupes2=$dbh->prepare($sqlgroupe);
		$req_groupes2=$req_groupes2->execute();
		$entitiesLength=$req_groupes2->fetchAll();

        $sqlprof = "SELECT CONCAT(CONCAT(ressources_profs.nom , ' '),ressources_profs.prenom) AS nom ,ressources_profs.codeProf AS codeE  FROM ressources_profs WHERE deleted='0'   ORDER BY nom,prenom";

        $req_profs2=$dbh->prepare($sqlprof);
		$req_profs2=$req_profs2->execute();
		$profs=$req_profs2->fetchAll();
        
        $sqlsalles = "SELECT ressources_salles.codeSalle AS codeE ,ressources_salles.nom AS nom FROM ressources_salles WHERE deleted='0' and codeZoneSalle='1' ORDER BY nom ASC";

        $req_salles2=$dbh->prepare($sqlsalles);
		$req_salles2=$req_salles2->execute();
		$salles=$req_salles2->fetchAll();

    	
        $weekNumber = date("W");
        $annee = date("Y");

        $groupe = $entitiesLength;

        return $this->render('emploi/planning.html.twig', array(
                'groupes' => $groupe,'weck' =>$weekNumber ,'annee' =>$annee,'profs' => $profs, 'salles' => $salles));
    }

   	#[Route(path: '/textToimageedt', name: 'textToimageedt')]
	public function saveTextAsImage(Request $request,Security $security) {
		
        //recuperation de la largeur de l ecran a laquelle on enleve 50 pour que ca rentre en largeur dans firefox

        $largeur=1500-50;
		
		
		//recuperation de la hauteur de l ecran a laquelle on enleve 235 pour que ca rentre en hauteur dans firefox

		$hauteur=912-210;
		

		// Nom de la police à utiliser
		$font = "uploads/fonts/verdana.ttf";
		$fontb = "uploads/fonts/verdanab.ttf";
        //test si prof concerne par rdv perso a son edt affiche sur ressources multi. Si non, on agrandi l'image de 75 px
        //pour rappel : affichage de la possibilite de mettre des reservations que si on est logge avec compte prof perso et si javascript actif



        //activation ou non du mode debug

        //si mode debug actif il faut changer dans url index.php par drawvtstudent.php

        $debug=0;


        //heure du début et de fin de  journée (pour 8h30 par exemple, il faut mettre 08.50)
		$heure_debut_journee=08.00;
		$heure_fin_journee=18.30;

		//heure du début et de fin de la pause de midi (pour 11h30 par exemple, il faut mettre 11.50)
		$heure_debut_pause_midi=13.27;
		$heure_fin_pause_midi=14.52;

		//heure de début et de fin de journée
		$starttime=$heure_debut_journee;
		$endtime=$heure_fin_journee;


        //heure de début et de fin de la pause de midi
        $lunchstart=$heure_debut_pause_midi;
        $lunchstop=$heure_fin_pause_midi;

        $hideprivate=0;
        
        $current_year = intval($request->request->get('annee'));
		$current_week = intval($request->request->get('weck'));


        // 1er Jour de la semaine
        $gr= explode(',',$request->request->get('groupe'));
        $prof= explode(',',$request->request->get('profs'));
        $salle= explode(',',$request->request->get('salles'));
       
        $jour=date("w",mktime(0,0,0,1,1,$current_year));

        if($jour==0){$jour=7;}

        if($jour>4){$premieran=0;}else{$premieran=-1;}

        $lundi=mktime(0,0,0,1,(($current_week+$premieran)*7),$current_year); 

        $jsem=date("w",$lundi);

        $lundi=mktime(0,0,0,1,(($current_week+$premieran)*7)+(1-$jsem),$current_year); 

        $current_day=date("Y-m-d",$lundi);

	

        if(!empty($salle)){
            if($salle[0]!=null){
                $salles_multi=$salle;
            }else{
                $salles_multi=array();
            }
            
        }else{
            $salles_multi=array();
        }
        //nombre de salles à afficher

        $nbdesalle=count($salles_multi);

        if(!empty($prof)){
            if($prof[0]!=null){
                $profs_multi=$prof;
            }else{
                $profs_multi=array();
            }
            
        }else{
            $profs_multi=array();
        }
        //nombre de profs à afficher

        $nbdeprof=count($profs_multi);

        //nombre de groupes à afficher
        if(!empty($gr)){
            if($gr[0]!=null){
                $groupes_multi=$gr;
            }else{
                $groupes_multi=array();
            }
            
        }else{
            $groupes_multi=array();
        }

        $nbdegroupe=count($groupes_multi);

        if($nbdegroupe==0 && $nbdeprof==0 and $nbdesalle==0){
            $profs_multi=array(77);
            $nbdeprof=1;
        }
        //nombre de materiels à afficher
        $materiels_multi=array();

        $nbdemateriel=count($materiels_multi);

        //nombre de ressources a afficher

        $nbressource=$nbdesalle+$nbdeprof+$nbdegroupe+$nbdemateriel;

        //heures début et fin de journée et affichage du samedi ou du dimanche avec les logins perso prof quand on fait export pdf
        



        // Pour le calcul de la durée de traitement

        $debut = explode(" ",microtime());

        $debut = $debut[1]+$debut[0];





        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                         Génération trame calendrier                       */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/



        // [GD] On genere un png

        if (!$debug) header ("Content-type: image/png");



        // Largeur et hauteur des entêtes du calendrier

        $leftwidth=50;

        $topheight=75;



        // [GD] On crée l'image

        // le 75 sert pour inclure la legende sous l edt

        $im = imagecreate ($largeur + ($largeur-$leftwidth)*($nbressource-1), $hauteur+75) or die ("Erreur lors de la création de l'image");


        $affichage_mot_materiel_pour_prof='0';
        // [GD] Declaration des couleurs

        $blanc = imagecolorallocate ($im, 255, 255, 255);

        $noir = imagecolorallocate ($im, 0, 0, 0);

        $gris = imagecolorallocate ($im, 200, 200, 200);

        $grisclair = imagecolorallocate ($im, 225, 225, 225);

        $couleur_vacances=imagecolorallocate ($im, 206, 243, 187);



        $couleur_TP = imagecolorallocate ($im, 169, 252, 173);

        $couleur_TD = imagecolorallocate ($im, 249,252,169);

        $couleur_CR = imagecolorallocate ($im, 181,169, 252);

        $couleur_DS = imagecolorallocate ($im, 252, 169, 169);

        $couleur_defaut = imagecolorallocate ($im, 30, 255, 30);

        $couleur_pro = imagecolorallocate($im, 255, 200,0);

        $couleur_jur = imagecolorallocate($im, 64, 224, 208);



        $cours = imagecolorallocate ($im, 211, 255, 236);





        $rdv[1] = imagecolorallocate ($im, 255, 187, 246);

        $rdv[2] = imagecolorallocate ($im, 255, 222, 132);

        $rdv[3] = imagecolorallocate ($im, 135, 206, 235);

        $rdv[4] = imagecolorallocate ($im, 255, 255, 0);

        $rdv[5] = imagecolorallocate ($im, 139, 172, 255);



        $samedi='1';
        $dimanche='0';

        // affichage des vacances scolaires des profs
        //pour chaque bdd

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['EDT_DATABASE_URL'].'',);
        $dbh = DriverManager::getConnection($connectionParams, $config);

        for ($k=0;$k<=1-1;$k++)
	    {
	        
            if (count($profs_multi)>=1 )
            {
                if ($samedi=='1' && $dimanche=='0')
                {
                    $days='6';
                }
                elseif ( $dimanche=='1')
                {
                    $days='7';
                }
                else
                {
                    $days='5';
                }
            	//preparation des requete pour la boucle suivante
                $sql="SELECT * from vacances where date=:current_day";
                $req_vacances=$dbh->prepare($sql);
                for ($day=0;$day<$days;$day++)
                {
                    $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));
                    $req_vacances1 = $req_vacances->execute(array(':current_day'=>$current_day));
                    $vacance=$req_vacances1->fetchAll();
                    
                    foreach($vacance as $vacances)
                    {
                        if ($days==6)
                        {
                            imagefilledrectangle($im, $leftwidth+$nbdegroupe*round((($largeur-$leftwidth)*$nbressource)/6)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/6), $topheight, $leftwidth+($nbdegroupe+$nbdeprof)*round((($largeur-$leftwidth)*$nbressource)/6)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/6), $hauteur, $couleur_vacances);
                        }
                            
                        elseif ($days==7)
                        {
                            imagefilledrectangle($im, $leftwidth+$nbdegroupe*round((($largeur-$leftwidth)*$nbressource)/7)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/7), $topheight, $leftwidth+($nbdegroupe+$nbdeprof)*round((($largeur-$leftwidth)*$nbressource)/7)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/7), $hauteur, $couleur_vacances);
                        }				
                        else
                        {
                            imagefilledrectangle($im, $leftwidth+$nbdegroupe*round((($largeur-$leftwidth)*$nbressource)/5)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/5), $topheight, $leftwidth+($nbdegroupe+$nbdeprof)*round((($largeur-$leftwidth)*$nbressource)/5)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/5), $hauteur, $couleur_vacances);
                        }
                    }
                }
            }

            // affichage des vacances scolaires des groupes (case coloriée en vert)

	        // affichage des vacances scolaires des groupes
            if (count($groupes_multi)>=1  )
            {
                if ($samedi=='1' && $dimanche=='0')
                {
                    $days='6';
                }
                elseif ( $dimanche=='1')
                {
                    $days='7';
                }
                else
                {
                    $days='5';
                }
                //preparation requete
                $sql="SELECT * from calendriers_groupes where date=:current_day and codeRessource=:groupeaafficher and deleted='0'";
                $req_vacances_groupe=$dbh->prepare($sql);	
                $sql="SELECT * FROM hierarchies_groupes WHERE codeRessourceFille=:groupeaafficher AND deleted= '0'";
                $req_groupes_de_niveau_sup=$dbh->prepare($sql);
                for ($i=0; $i<count($groupes_multi); $i++)
                {
                    $groupeaafficher=$groupes_multi[$i];
                    $stop=0;
                    while ($stop!=1)
                    {
                        for ($day=0;$day<$days;$day++)
                        {
                            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));
                            $req_vacances_groupe1=$req_vacances_groupe->execute(array(':current_day'=>$current_day,':groupeaafficher'=>$groupeaafficher));
                            $vacance_groupe=$req_vacances_groupe1->fetchAll();
                            if (count($vacance_groupe)>0)
                            {
                                foreach ($vacance_groupe as $vacance_groupes)
                                {
                                    if ($vacance_groupes['date']!="")
                                    {               
                                        if ($days==6)
                                        {
                                            imagefilledrectangle($im,  $leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/6)+$i*( ($largeur-$leftwidth))/6, $topheight, $leftwidth+($i+1)*( ($largeur-$leftwidth))/6+$day*round((($largeur-$leftwidth)*$nbressource)/6), $hauteur, $couleur_vacances);
                                        }
                                        elseif ($days==7)
                                        {
                                            imagefilledrectangle($im, $leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/7)+$i*( ($largeur-$leftwidth))/7, $topheight, $leftwidth+($i+1)*( ($largeur-$leftwidth))/7+$day*round((($largeur-$leftwidth)*$nbressource)/7), $hauteur, $couleur_vacances);
                                        }								
                                        else
                                        {	
                                            imagefilledrectangle($im, $leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/5)+$i*( ($largeur-$leftwidth))/5, $topheight, $leftwidth+($i+1)*( ($largeur-$leftwidth))/5+$day*round((($largeur-$leftwidth)*$nbressource)/5), $hauteur, $couleur_vacances);
                                        }
                                    }            
                                }
                            }
                        }
                        $req_groupes_de_niveau_sup1=$req_groupes_de_niveau_sup->execute(array(':groupeaafficher'=>$groupeaafficher));
                        $res_groupes_de_niveau_sup=$req_groupes_de_niveau_sup1->fetchAll();
                        if (count($res_groupes_de_niveau_sup)>0)
                        {
                            foreach ($res_groupes_de_niveau_sup as $groupe_de_niveau_sup)
                            {
                                $groupeaafficher=$groupe_de_niveau_sup['codeRessource'];
                            }
                        }
                        else 
                        {
                            $stop=1;		
                        }
                            
                    }
                }                  
            }
	
	

            // affichage des jours fériés de la filière


            if ($samedi=='1' && $dimanche=='0')
            {
                $days='6';
            }
            elseif ( $dimanche=='1')
            {
                $days='7';
            }
            else
            {
                $days='5';
            }
            //preparation requete
            $sql="SELECT * from calendriers_filieres where date=:current_day and deleted='0'";
            $req_vacances_filiere=$dbh->prepare($sql);	
            $i=count($groupes_multi)+count($profs_multi)+count($salles_multi)+count($materiels_multi);
            for ($day=0;$day<$days;$day++)
            {
                $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));
                $req_vacances_filiere1=$req_vacances_filiere->execute(array(':current_day'=>$current_day));
                $vacance=$req_vacances_filiere1->fetchAll();
                
                foreach($vacance as $vacances)
                {
                    if ($days==6)
                    {
                        imagefilledrectangle($im, $leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/6), $topheight, $leftwidth+$i*round((($largeur-$leftwidth)*$nbressource)/6)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/6), $hauteur, $couleur_vacances);
                        
                    }
                    elseif ($days==7)
                    {
                        imagefilledrectangle($im, $leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/7), $topheight, $leftwidth+$i*round((($largeur-$leftwidth)*$nbressource)/7)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/7), $hauteur, $couleur_vacances);
                        
                    }								
                        
                    else
                    {
                        imagefilledrectangle($im, $leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/5), $topheight, $leftwidth+$i*round((($largeur-$leftwidth)*$nbressource)/5)/$nbressource+$day*round((($largeur-$leftwidth)*$nbressource)/5), $hauteur, $couleur_vacances);
                    }
                }
            }            
        }


        // [GD] Création d'un polygone

        $greycadre= array(0, 0, $largeur + ($largeur-$leftwidth)*($nbressource-1), 0, $largeur + ($largeur-$leftwidth)*($nbressource-1), $topheight, $leftwidth, $topheight, $leftwidth, $hauteur-1, 0, $hauteur-1, 0, 0);

        // [GD] Mise en gris des entêtes

        imagefilledpolygon ($im, $greycadre, 7, $gris);

        // [GD] pause de midi

        if ($lunchstart>$starttime)

            imagefilledrectangle($im, $leftwidth+1 ,abs($lunchstart-$starttime)/($endtime-$starttime+0.25)*($hauteur-$topheight)+$topheight , $largeur + ($largeur-$leftwidth)*($nbressource-1)-1, abs($lunchstop-$starttime)/($endtime-$starttime+0.25)*($hauteur-$topheight)+$topheight, $grisclair);



        // [GD] Dessin des cadres

        imageline ($im,  $leftwidth+1, $topheight, $largeur + ($largeur-$leftwidth)*($nbressource-1)-1, $topheight, $noir);





        // [GD] On affiche les heures

        $currenttime=$starttime;

        $nbintervalles=round(($endtime-$starttime)/0.25)+1;

        for($i=0;$i<=$nbintervalles-1;$i++)

            {

            imageline ($im, $leftwidth+1, $topheight+(($hauteur-$topheight)/$nbintervalles)*$i, $largeur + ($largeur-$leftwidth)*($nbressource-1)-1, $topheight+(($hauteur-$topheight)/$nbintervalles)*$i, $gris);

            if (!($i%2))

                imagestring ($im, 2, 10, 68+($hauteur-$topheight)/$nbintervalles*$i, substr(intval($currenttime)+100,-2,2).":".substr(($currenttime-intval($currenttime))*60+100,-2,2), $noir);

            $currenttime+=0.25;

            }







        // [GD] On trace les lignes, on met les jours



        if ($samedi=='1' && $dimanche=='0')

            {

            for($i=0;$i<=6;$i++)

            {

                imageline ($im, round(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6*$i+$leftwidth), 0,  round(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6*$i+$leftwidth), $hauteur-1, $noir);

                //separation en pointillet entre les ressources

                for($numeroressource=1;$numeroressource<$nbressource;$numeroressource++)

                {	

                $style = array($noir, $noir, $noir, $noir, $noir, $blanc, $blanc, $blanc, $blanc, $blanc);

        imagesetstyle($im, $style);

                imageline ($im, $leftwidth+$i*round(($largeur-$leftwidth)*$nbressource/6)+$numeroressource*round(($largeur-$leftwidth)*$nbressource/6)/$nbressource, $topheight,  $leftwidth+$i*round(($largeur-$leftwidth)*$nbressource/6)+$numeroressource*round(($largeur-$leftwidth)*$nbressource/6)/$nbressource, $hauteur, IMG_COLOR_STYLED);	

                }	

            }	

                

                

                

                

                

                //largeur en px Lundi 36 Mardi 36 Mercredi 59 Jeudi 36  Vendredi 59  Samedi 44

                

            imagestring ($im, 12,$leftwidth+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-36)/2, 10, "Lundi", $noir);

            imagestring ($im, 12,$leftwidth+(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-36)/2, 10, "Mardi", $noir);

            imagestring ($im, 12,$leftwidth+2*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-59)/2, 10, "Mercredi", $noir);

            imagestring ($im, 12,$leftwidth+3*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-36)/2, 10, "Jeudi", $noir);

            imagestring ($im, 12,$leftwidth+4*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-59)/2, 10, "Vendredi", $noir);

            imagestring ($im, 12,$leftwidth+5*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-44)/2, 10, "Samedi", $noir);

            //largeur en pixel de la date egal 37

            

            imagestring ($im, 12, $leftwidth+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-37)/2, 30, date("d/m",$lundi), $noir);

            imagestring ($im, 12, $leftwidth+(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-37)/2, 30, date("d/m",strtotime("+1 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+2*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-37)/2, 30, date("d/m",strtotime("+2 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+3*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-37)/2, 30, date("d/m",strtotime("+3 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+4*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-37)/2, 30, date("d/m",strtotime("+4 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+5*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/6)-37)/2, 30, date("d/m",strtotime("+5 day",$lundi)), $noir);

            $days=6;

            }

        elseif ($dimanche=='1')

            {

            for($i=0;$i<=7;$i++)

            {

                imageline ($im, round(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7*$i+$leftwidth), 0,  round(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7*$i+$leftwidth), $hauteur-1, $noir);

                //separation en pointillet entre les ressources

                for($numeroressource=1;$numeroressource<$nbressource;$numeroressource++)

                {	

                $style = array($noir, $noir, $noir, $noir, $noir, $blanc, $blanc, $blanc, $blanc, $blanc);

        imagesetstyle($im, $style);

                imageline ($im, $leftwidth+$i*round(($largeur-$leftwidth)*$nbressource/7)+$numeroressource*round(($largeur-$leftwidth)*$nbressource/7)/$nbressource, $topheight,  $leftwidth+$i*round(($largeur-$leftwidth)*$nbressource/7)+$numeroressource*round(($largeur-$leftwidth)*$nbressource/7)/$nbressource, $hauteur, IMG_COLOR_STYLED);	

                }	

            }	

            
                

                

                //largeur en px Lundi 36 Mardi 36 Mercredi 59 Jeudi 36  Vendredi 59  Samedi 44

                

            imagestring ($im, 12,$leftwidth+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-36)/2, 10, "Lundi", $noir);

            imagestring ($im, 12,$leftwidth+(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-36)/2, 10, "Mardi", $noir);

            imagestring ($im, 12,$leftwidth+2*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-59)/2, 10, "Mercredi", $noir);

            imagestring ($im, 12,$leftwidth+3*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-36)/2, 10, "Jeudi", $noir);

            imagestring ($im, 12,$leftwidth+4*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-59)/2, 10, "Vendredi", $noir);

            imagestring ($im, 12,$leftwidth+5*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-44)/2, 10, "Samedi", $noir);

            imagestring ($im, 12,$leftwidth+6*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-59)/2, 10, "Dimanche", $noir);

            
            
            
            //largeur en pixel de la date egal 37

            

            imagestring ($im, 12, $leftwidth+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-37)/2, 30, date("d/m",$lundi), $noir);

            imagestring ($im, 12, $leftwidth+(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-37)/2, 30, date("d/m",strtotime("+1 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+2*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-37)/2, 30, date("d/m",strtotime("+2 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+3*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-37)/2, 30, date("d/m",strtotime("+3 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+4*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-37)/2, 30, date("d/m",strtotime("+4 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+5*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-37)/2, 30, date("d/m",strtotime("+5 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+6*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/7)-37)/2, 30, date("d/m",strtotime("+6 day",$lundi)), $noir);

            $days=7;

            }

        else

            {

            for($i=0;$i<=5;$i++)

            {

                imageline ($im, round(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5*$i+$leftwidth), 0,  round(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5*$i+$leftwidth), $hauteur-1, $noir);

                    //separation en pointillet entre les ressources

                for($numeroressource=1;$numeroressource<$nbressource;$numeroressource++)

                {	

                $style = array($noir, $noir, $noir, $noir, $noir, $blanc, $blanc, $blanc, $blanc, $blanc);

        imagesetstyle($im, $style);

                imageline ($im, $leftwidth+$i*round(($largeur-$leftwidth)*$nbressource/5)+$numeroressource*round(($largeur-$leftwidth)*$nbressource/5)/$nbressource, $topheight,  $leftwidth+$i*round(($largeur-$leftwidth)*$nbressource/5)+$numeroressource*round(($largeur-$leftwidth)*$nbressource/5)/$nbressource, $hauteur, IMG_COLOR_STYLED);	

                }		

                

                

                

            }	

            imagestring ($im, 12,$leftwidth+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-36)/2, 10, "Lundi", $noir);

            imagestring ($im, 12,$leftwidth+(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-36)/2, 10, "Mardi", $noir);

            imagestring ($im, 12,$leftwidth+2*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-59)/2, 10, "Mercredi", $noir);

            imagestring ($im, 12,$leftwidth+3*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-36)/2, 10, "Jeudi", $noir);

            imagestring ($im, 12,$leftwidth+4*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-59)/2, 10, "Vendredi", $noir);

            imagestring ($im, 12,$leftwidth+5*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-44)/2, 10, "Samedi", $noir);

            //largeur en pixel de la date egal 37

            

            imagestring ($im, 12, $leftwidth+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-37)/2, 30, date("d/m",$lundi), $noir);

            imagestring ($im, 12, $leftwidth+(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-37)/2, 30, date("d/m",strtotime("+1 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+2*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-37)/2, 30, date("d/m",strtotime("+2 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+3*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-37)/2, 30, date("d/m",strtotime("+3 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+4*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-37)/2, 30, date("d/m",strtotime("+4 day",$lundi)), $noir);

            imagestring ($im, 12, $leftwidth+5*(($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)+((($largeur + ($largeur-$leftwidth)*($nbressource-1)-$leftwidth)/5)-37)/2, 30, date("d/m",strtotime("+5 day",$lundi)), $noir);

            $days=5;

            }


        //on affiche à quoi correspondent les congés des groupes ( exam, stage...)

            
        if (count($groupes_multi)>=1  )
            {
            if ($samedi=='1' && $dimanche=='0')
                {
                $days='6';
                }
            elseif ( $dimanche=='1')
                {
                $days='7';
                }
            else
                {
                $days='5';
                }
        //preparation requete
        //requete déjà préparé avec les vacances  des groupes
            for ($i=0; $i<count($groupes_multi); $i++)
                {
                $groupeaafficher=$groupes_multi[$i];
                $stop=0;
                while ($stop!=1)
                    {
                    for ($day=0;$day<$days;$day++)
                        {
                        $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));
                        $req_vacances_groupe1=$req_vacances_groupe->execute(array(':current_day'=>$current_day,':groupeaafficher'=>$groupeaafficher));
                        $vacance_groupe=$req_vacances_groupe1->fetchAll();
                        if (count($vacance_groupe)>0)
                            {
                            foreach ($vacance_groupe as $vacance_groupes)
                                {
                                if ($vacance_groupes['date']!="")
                                    {
                                    
                                    if ($vacance_groupes['etat']==2)
                                    {
                                    $text="Congé";
                                    }
                                    elseif ($vacance_groupes['etat']==3)
                                    {
                                    $text="Examen";
                                    }
                                    elseif ($vacance_groupes['etat']==5)
                                    {
                                    $text="Stage / Entreprise";
                                    }	
                                    else
                                    {
                                    $text="";
                                    }							
                                    $size=imagettfbbox (18 , 0, $fontb, $text);

                                    $box_lenght=$size[2]-$size[0];

                                    $box_width=$size[1]-$size[7];




                                    
                                    
                                    
                                    if ($days==6)
                                        {
                                    imagettftext ($im, 18, 90,$leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/6)+$i*( ($largeur-$leftwidth))/6 +((( ($largeur-$leftwidth))/6))/2 +$box_width/2  ,$topheight+ ($hauteur-$topheight)/2+$box_lenght/2  , $noir, $fontb, $text);
                                        }
                                    elseif ($days==7)
                                        {
                                        
                                        

                                        
                                        imagettftext ($im, 18, 90,$leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/7)+$i*( ($largeur-$leftwidth))/7 +((( ($largeur-$leftwidth))/7))/2 +$box_width/2  ,$topheight+ ($hauteur-$topheight)/2+$box_lenght/2  , $noir, $fontb, $text);
                                        

                                        
                                        }								
                                    else
                                        {	
                                        imagettftext ($im, 18, 90,$leftwidth+$day*round((($largeur-$leftwidth)*$nbressource)/5)+$i*( ($largeur-$leftwidth))/5 +((( ($largeur-$leftwidth))/5))/2 +$box_width/2  ,$topheight+ ($hauteur-$topheight)/2+$box_lenght/2  , $noir, $fontb, $text);
                                        }
                                    }
                
                                }
                            }
                        }
                        $req_groupes_de_niveau_sup11=$req_groupes_de_niveau_sup->execute(array(':groupeaafficher'=>$groupeaafficher));
                        $res_groupes_de_niveau_sup=$req_groupes_de_niveau_sup11->fetchAll();
                        if (count($res_groupes_de_niveau_sup)>0)
                            {
                            foreach ($res_groupes_de_niveau_sup as $groupe_de_niveau_sup)
                                {
                                $groupeaafficher=$groupe_de_niveau_sup['codeRessource'];
                                }
                            }
                        else 
                            {
                            $stop=1;		
                            }
                        
                    }
                }
                
            }




        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage cours  GROUPES                   */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/

        //pour chaque bdd

            for ($k=0;$k<=1-1;$k++)

            {
            
        //preparation requete pour la boucle qui suit
        $sql="SELECT * FROM hierarchies_groupes WHERE codeRessourceFille=:groupeaafficher AND deleted= '0'";
        $req_groupes_de_niveau_supbis=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_groupes LEFT JOIN ressources_groupes ON (ressources_groupes.codeGroupe=seances_groupes.codeRessource) WHERE codeSeance=:codeSeance AND seances_groupes.deleted=0 AND ressources_groupes.deleted='0' ";
        $req_groupes=$dbh->prepare($sql);	

        $sql="SELECT * FROM seances_profs LEFT JOIN ressources_profs ON (ressources_profs.codeProf=seances_profs.codeRessource) WHERE codeSeance=:codeSeance AND seances_profs.deleted=0 AND ressources_profs.deleted='0' order by ressources_profs.nom ";
        $req_profs=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_salles LEFT JOIN ressources_salles ON (ressources_salles.codeSalle=seances_salles.codeRessource) WHERE codeSeance=:codeSeance AND seances_salles.deleted=0  and ressources_salles.deleted='0' order by ressources_salles.nom";
        $req_salles=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_materiels LEFT JOIN ressources_materiels ON (ressources_materiels.codeMateriel=seances_materiels.codeRessource) WHERE codeSeance=:codeSeance AND seances_materiels.deleted=0  and ressources_materiels.deleted='0' order by ressources_materiels.nom";
        $req_materiels=$dbh->prepare($sql);

        $sql="SELECT * FROM ressources_groupes WHERE deleted='0' and codeGroupe=:current_student";
        $req_groupes2=$dbh->prepare($sql);


        for ($i=0; $i<count($groupes_multi); $i++)
            {
            $groupeaafficher=$groupes_multi[$i];
            $critere="AND (";
            $stop=0;
            while ($stop!=1 )
                {
                    $req_groupes_de_niveau_supbis1=$req_groupes_de_niveau_supbis->execute(array(':groupeaafficher'=>$groupeaafficher));
                $res_groupes_de_niveau_supbis=$req_groupes_de_niveau_supbis1->fetchAll();

                        $critere .= "seances_groupes.codeRessource='".$groupeaafficher."' OR ";
                        if (count($res_groupes_de_niveau_supbis)>0)
                        {
                        $groupeaafficher=$res_groupes_de_niveau_supbis['0']['codeRessource'];
                        }
                        else 
                            {
                            $stop=1;
                            }
                    
                
                }

            $critere .= "0)";

        // Pour les 5 ou 6 jours à afficher, on interroge la DB

        for ($day=0;$day<$days;$day++)
            {
            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));
            unset($req_seance);
            $sql="SELECT *, seances.dureeSeance, seances.commentaire, matieres.couleurFond, enseignements.nom as nom_enseignement, enseignements.alias as alias_enseignement FROM seances_groupes LEFT JOIN (seances) ON (seances_groupes.codeSeance=seances.codeSeance) RIGHT JOIN (enseignements) ON (seances.codeEnseignement=enseignements.codeEnseignement)  left join matieres on enseignements.codeMatiere=matieres.codeMatiere WHERE matieres.deleted=0 and seances.dateSeance=:current_day AND seances.deleted=0 ".$critere." AND seances_groupes.deleted=0 AND enseignements.deleted='0' ";
            $req_seance=$dbh->prepare($sql);	
            $req_seance=$req_seance->execute(array(':current_day'=>$current_day));
            $res_seances=$req_seance->fetchAll();

                

            // Pour chaque séance
                foreach($res_seances as $res_seance)
                {

                // On convertit l'horaire en %age de la journée



                /* Explication conversion :



                On extrait d'une part les minutes et d'autre part l'heure.

                On transforme les minutes en fraction d'heure.

                On enlève starttime et on divise par la durée de la journée affichée endtime-starttime.

                On obtient un %age correspondant à la position du début du cours.

                Idem pour la durée mais sans enlever 8.15



                */


                $start_time=((substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                $duree=((substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60))/($endtime-$starttime+0.25);


                $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);

                $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);

                $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



            // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +($i*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche


                $req_groupes1=$req_groupes->execute(array(':codeSeance'=>$res_seance['codeSeance']));
                $res_groupe=$req_groupes1->fetchAll();
                $dechex="FFFFFF"; //couleur par défaut : blanc
                foreach($res_groupe as $res_groupes)
                {

            

                // [GD] On trace la case correspondante
                $couleur_des_seances_groupe_prof=0;
                //recuperation de la couleur associee au groupe ou à la matiere ou au prof et conversion en rvb
                if ($couleur_des_seances_groupe_prof==0) // si couleur des groupes
                {
                $dechex=dechex($res_groupes['couleurFond']);
                }
                elseif ($couleur_des_seances_groupe_prof==1) // si couleur des matieres
                {
                $dechex=dechex($res_seance['couleurFond']);
                }
                elseif ($couleur_des_seances_groupe_prof==2) // si couleur des matieres
                {
                    $req_profs1=$req_profs->execute(array(':codeSeance'=>$res_seance['codeSeance']));
                    $res_prof=$req_profs1->fetchAll();
                    foreach($res_prof as $res_profs)
                    {
                        $dechex=dechex($res_profs['couleurFond']);
                    }
                    unset ($res_prof);
                    }
                    else
                    {
                        $dechex=dechex($res_groupes['couleurFond']);
                    }

                    while (strlen($dechex)<6) {

                        $dechex = "0".$dechex;

                    }





                        //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
                    //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins
                    //couleur
                    $couleur = imagecolorallocate ($im, hexdec(substr($dechex,-2,2)), hexdec(substr($dechex,-4,2)), hexdec(substr($dechex,-6,2)));
                    //valeur du rayon 15min
                    //$ray=($hauteur-$topheight)/$nbintervalles;
                    if ($res_seance['dureeSeance']<=100)
                    {
                    $ray=3;
                    }
                    else
                    {

                    $ray=5;
                    }


            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$couleur);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
            
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);



        }



                

                

                // On compte le nombre d'elements dans la case

                

                // on met deja nbelement egal 1 pour le nom de la matiere

                $nbelements=1;



                // On compte le nombre de profs a afficher
                $req_profs1=$req_profs->execute(array(':codeSeance'=>$res_seance['codeSeance']));
                $res_prof=$req_profs1->fetchAll();
                
                
                    if (count($res_prof)>=1)
                        {
                        $nbelements+=count($res_prof);
                        }
                $nbprof=count($res_prof);



                // On compte le nombre de salles a afficher
                $req_salles1=$req_salles->execute(array(':codeSeance'=>$res_seance['codeSeance']));
                $res_salle=$req_salles1->fetchAll();		
                


                if (count($res_salle))
                    {
                    //on affiche les salles sur 1 seule ligne donc nbelement +1

                    $nbelements+=1;
                    }
                $nbsalle=count($res_salle);
                
                
                // On compte le nombre de materiels a afficher
                $req_materiels1=$req_materiels->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_materiel=$req_materiels1->fetchAll();		
                


                if (count($res_materiel))
                    {
                    //on affiche les materiels sur 1 seule ligne donc nbelement +1

                    $nbelements+=1;
                    }
                $nbmateriel=count($res_materiel);		
                

                // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                                            }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne+1;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }

                    $nbelements+=$compteur_ligne;	

                    }



                

                



                // [GD] On affiche le type et la date des seances
        //découpage du nom de l'enseignement afin de récupérer ce qu'il y a entre les 2 premiers _
            
        $nom_enseignement_afficher_alias=0;
        if ($nom_enseignement_afficher_alias==1)
        {
        $cursename=explode("_",$res_seance['alias_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['alias_enseignement'];
        }
        }
        else
        {
        $cursename=explode("_",$res_seance['nom_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['nom_enseignement'];
        }
        }

                    
                
        //affectation de la couleur au type d'enseignement
                if ($res_seance['codeTypeActivite']==2)

                {

                    $couleur = $couleur_TD;

                }



                elseif ($res_seance['codeTypeActivite']==1)

                {

                    $couleur = $couleur_CR;

                }



                elseif ($res_seance['codeTypeActivite']==3)

                {

                    $couleur = $couleur_TP;

                }



                elseif ($res_seance['codeTypeActivite']==9)

                {

                    $couleur = $couleur_DS;

                }



                elseif ($res_seance['codeTypeActivite']==4)

                {

                    $couleur = $couleur_pro;

                }



                else

                {

                    $couleur = $couleur_defaut;

                }


            unset($req_type);
            $sql="SELECT * FROM types_activites WHERE codeTypeActivite=:type_activite" ;
            $req_type=$dbh->prepare($sql);	
            $req_type=$req_type->execute(array(':type_activite'=>$res_seance['codeTypeActivite']));
            $res_types=$req_type->fetchAll();		
                    
                foreach($res_types as $res_type)
            {
            $text = $res_type['alias']." - ";
            }
        
                

                // [GD] On affiche les horaires

                $size=imagettfbbox (8 , 0, $font, $text.$horaire_debut." - ".$horaire_fin);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];



            //dessin de la case de l en-tete
            if ($res_seance['dureeSeance']<=100)
            {
            $epaisseur=11;
            }
            else
            {
            $epaisseur=16;
            }
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$couleur);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $couleur,IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $couleur,IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);

            if ($res_seance['dureeSeance']<=100)
            {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2  , $topy + 10  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
            }
            else
            {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2  , $topy + 12  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
            }		
                
                






                //on affiche le nom de la seance

                    //dix caracteres ont une longueur de 92px

                $cursename=substr($cursename[1],0,round((($largeur-$leftwidth)/$days)*10/92)-1);

                $size=imagettfbbox (9 , 0, $fontb, $cursename);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];

                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy+$epaisseur) / 2 +12 -10 * ($nbelements/2) , $noir, $fontb, $cursename);

                $position=($bottomy + $topy+$epaisseur) / 2 +11- 10 * ($nbelements/2)+10;







                // [GD] On affiche les commentaires sur la seance

            

            // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                        

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (7 , 0, $font, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (7 , 0, $font, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $resaname);

                            $position+=10;

                            }

            

                    }

            



            

            

                // [GD] On affiche les profs concernés
        foreach ($res_prof as $res_profs)


                    {

                    if ($res_profs['nom']!="")

                        {

            

                        $size=imagettfbbox (8 , 0, $font, substr($res_profs['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                        $box_lenght=$size[2]-$size[0];

                        $box_width=$size[1]-$size[7];

                        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position  , $noir, $font, substr($res_profs['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                        $position+=10;

                        }



                    }

                    

                    

                // [GD] On affiche les salles

                $nbsalles=0;

                unset($salles);

                $salles="";



                foreach ($res_salle as $res_salles)

                    {

                    if ($nbsalles>0)
                        {
                        $salles.=", ";
                        }
                    $nbsalles++;
                    $nom_salle_afficher_alias=1;
                    $nb_caractere_salle_pour_vue_prof='20';
            if ($nom_salle_afficher_alias==1)
            {
            $salles .= substr($res_salles['alias'],0,$nb_caractere_salle_pour_vue_prof);
            }
            else
            {
            $salles .= substr($res_salles['nom'],0,$nb_caractere_salle_pour_vue_prof);
            }	
                    

                    }

                    $affichage_mot_salle_pour_prof='1';
                    //affichage du mot "salle" devant la liste des salles
                    if ($affichage_mot_salle_pour_prof=='1')
                    {
                    if ($nbsalles==1)
                        {
                        $salles="Salle : ".$salles;
                        }
                    
                    if($nbsalles>1)
                        {
                        $salles="Salles : ".$salles;
                        }
                    }
                    
                    
                    

                    $size=imagettfbbox (7 , 0, $font, $salles);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-2, $noir, $font, $salles);

                    
                    
                    
                    
            // [GD] On affiche les materiels
        $position+=10;
                $nbmateriels=0;

                unset($materiels);

                $materiels="";



                foreach ($res_materiel as $res_materiels)

                    {

                    if ($nbmateriels>0)
                        {
                        $materiels.=", ";
                        }
                    $nbmateriels++;
                    $nom_materiel_afficher_alias=0;
                    $nb_caractere_materiel_pour_vue_prof='20';
            if ($nom_materiel_afficher_alias==1)
            {
            $materiels .= substr($res_materiels['alias'],0,$nb_caractere_materiel_pour_vue_prof);
            }
            else
            {
            $materiels .= substr($res_materiels['nom'],0,$nb_caractere_materiel_pour_vue_prof);
            }	
                    

                    }

                    $affichage_mot_materiel_pour_prof='0';
                    //affichage du mot "materiel" devant la liste des materiels
                    if ($affichage_mot_materiel_pour_prof=='1')
                    {
                    if ($nbmateriels==1)
                        {
                        $materiels="Materiel : ".$materiels;
                        }
                    
                    if($nbmateriels>1)
                        {
                        $materiels="Materiels : ".$materiels;
                        }
                    }
                    
                    
                    

                    $size=imagettfbbox (7 , 0, $font, $materiels);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-4, $noir, $font, $materiels);





                }

                

                        //on affiche le nom des groupes en dessous de la date
                        //seulement pour la derniere base de données car sinon les nom sont écrit les uns sur les autres. De plus comme dans la liste déroulante il n'y a que les noms de la derniere base...

                        $current_student=$groupes_multi[$i];
                        $req_groupes21=$req_groupes2->execute(array(':current_student'=>$current_student));
                        $res_groupe2=$req_groupes21->fetchAll();
                        foreach($res_groupe2 as $res)	


                        {

                        $size=imagettfbbox (8 , 0, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

                        $box_lenght=$size[2]-$size[0];

                        $box_width=$size[1]-$size[7];

                        $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +($i*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                        $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , 60  , $noir, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

                        

                        }	
                        
                

                

            }



            

            }

            }
            
            
            
            
            
            
            
            
            
            

            
                    
            
            //Coloriage en gris des séances des groupes de niveau infiérieur
            //pour chaque bdd


            for ($k=0;$k<=0;$k++)

            {
            




        for ($i=0; $i<count($groupes_multi); $i++)
            {
            
                //liste des groupes de niv inférieur
            $groupeaafficher=$groupes_multi[$i];
            $groupeaafficher2="codeRessource=".$groupes_multi[$i];
                

            $critere2 = "and (";
                    
                $premier_groupe='0';
                $stop=0;
            
                while ($stop!=1 )
                    {
                    $sql="SELECT * FROM hierarchies_groupes WHERE (".$groupeaafficher2.") AND deleted= '0'";
                    $req_groupes_de_niveau_inf_conflit=$dbh->prepare($sql);
                    $req_groupes_de_niveau_inf_conflit=$req_groupes_de_niveau_inf_conflit->execute();
                    
                    $res_groupes_de_niveau_inf=$req_groupes_de_niveau_inf_conflit->fetchAll();
                    
                $groupeaafficher2="";	
                            
                    if (count($res_groupes_de_niveau_inf)>0)
                        {
                        foreach ($res_groupes_de_niveau_inf as $res_groupe_de_niveau_inf)
                            {
                            $critere2 .= "seances_groupes.codeRessource='".$res_groupe_de_niveau_inf['codeRessourceFille']."' or ";
                            $groupeaafficher2.="codeRessource=".$res_groupe_de_niveau_inf['codeRessourceFille']." or ";

                            }
                        $groupeaafficher2.="0";	
                        
                        }
                    else 
                        {
                        $stop=1;
                        }

                unset ($req_groupes_de_niveau_inf_conflit);
                    }


            $critere2 .= "0)";	
            

            
            
            
            


        // Pour les 5 ou 6 jours à afficher, on interroge la DB

        for ($day=0;$day<$days;$day++)
            {
            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));
            unset($req_seance);
            
            $sql="SELECT *, seances.dureeSeance FROM seances_groupes LEFT JOIN (seances) ON (seances_groupes.codeSeance=seances.codeSeance) WHERE seances.dateSeance=:current_day AND seances.deleted=0 ".$critere2." AND seances_groupes.deleted=0  ";
            $req_seance=$dbh->prepare($sql);	
            $req_seance=$req_seance->execute(array(':current_day'=>$current_day));
            $res_seances=$req_seance->fetchAll();

                

            // Pour chaque séance
                foreach($res_seances as $res_seance)
                {

                // On convertit l'horaire en %age de la journée
                $start_time=((substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                $duree=((substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60))/($endtime-$starttime+0.25);


                $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);

                $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);

                $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



            // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +($i*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche









            //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins
        //couleur
        $couleur = imagecolorallocate ($im, 100, 100, 100);
        

        //valeur du rayon 
        $ray=5;

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$couleur);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $couleur);

            

        }





            }
            
            }
            
            
            
            
            }
            
            
            

            
            
            
            
            
            
            
            
            
            

            

                

            

        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage reservation  groupes             */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/	

        //pour chaque bdd

            for ($k=0;$k<=0;$k++)

            {
            

        //preparation requete pour la boucle qui suit
        $sql="SELECT * FROM hierarchies_groupes WHERE codeRessourceFille=:groupeaafficher AND deleted= '0'";
        $req_groupes_de_niveau_supbis2=$dbh->prepare($sql);

        for ($i=0; $i<count($groupes_multi); $i++)
            {
            $groupeaafficher=$groupes_multi[$i];
            $critere="AND (";
            $stop=0;
            while ($stop!=1 )
                {
                $req_groupes_de_niveau_supbis2->bindValue(':groupeaafficher', $groupeaafficher);
                $req_groupes_de_niveau_supbis21=$req_groupes_de_niveau_supbis2->execute(array(':groupeaafficher'=>$groupeaafficher));
                $res_groupes_de_niveau_supbis2=$req_groupes_de_niveau_supbis21->fetchAll();
                
                
            
                        $critere .= "reservations_groupes.codeRessource='".$groupeaafficher."' OR ";
                        if (count($res_groupes_de_niveau_supbis2)>0)
                        {
                        $groupeaafficher=$res_groupes_de_niveau_supbis2['0']['codeRessource'];
                        }
                        else 
                            {
                            $stop=1;
                            }
                    
                
                }

            $critere .= "0)";

        //preparation de requetes
        $sql="SELECT * FROM reservations_salles left join (ressources_salles ) on ressources_salles.codeSalle=reservations_salles.codeRessource WHERE reservations_salles.codeReservation=:codeReservation AND reservations_salles.deleted=0 and ressources_salles.deleted=0 order by ressources_salles.nom";
        $req_resa_salle=$dbh->prepare($sql);




        // Pour les 5 ou 6 jours à afficher, on interroge la DB

        for ($day=0;$day<$days;$day++)
            {
            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));



            // On recherche pour les groupes de l'etudiant et le jour choisi l'ensemble des séances

                unset ($req_resa);
                $sql="SELECT *, reservations.dureeReservation, reservations.commentaire FROM reservations_groupes LEFT JOIN (reservations) ON (reservations_groupes.codereservation=reservations.codereservation)  WHERE reservations.datereservation=:current_day AND reservations.deleted=0 ".$critere." AND reservations_groupes.deleted=0  ";
                $req_resa=$dbh->prepare($sql);	
                $req_resa=$req_resa->execute(array(':current_day'=>$current_day));
                $res_resas=$req_resa->fetchAll();



                // Pour chaque réservation

                foreach($res_resas as $res_resa)

                {



                    // On convertit l'horaire en %age de la journée


                    $start_time=((substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                    $duree=((substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60))/($endtime-$starttime+0.25);


                    $horaire_debut = substr((100+substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)),-2,2)."h".substr($res_resa['heureReservation'],-2,2);

                    $horaire_fin = (substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60) + (substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60);

                    $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



            // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +($i*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche

                    

            //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins

        //valeur du rayon 15min
        //$ray=($hauteur-$topheight)/$nbintervalles;
        if ($res_resa['dureeReservation']<=100)
        {
        $ray=3;
        }
        else
        {

        $ray=5;
        }

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$rdv[1]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$rdv[1]);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
            
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);		





                    // [GD] On affiche les horaires
        $text=$horaire_debut." - ".$horaire_fin;
                    $size=imagettfbbox (8 , 0, $font, $text);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];



                    

            //dessin de la case de l en-tete
            // dessin des 2 rectangles
            if ($res_resa['dureeReservation']<=100)
            {
            $epaisseur=11;
            }
            else
            {
            $epaisseur=16;
            }	
                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$rdv[4]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$rdv[4]);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $rdv[4],IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $rdv[4],IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);

            if ($res_resa['dureeReservation']<=100)
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 10  , $noir, $font, $text);
            }
            else
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 12  , $noir, $font, $text);
            }

                    

                    

                // recherche si une salle est associée à la réservation
                $req_resa_salle=$req_resa_salle->execute(array(':codeReservation'=>$res_resa['codeReservation']));
        $res_resa_salles=$req_resa_salle->fetchAll();
        $nb_resa_salle=0;
        $nom_resa_salle="";
        foreach($res_resa_salles as $res_resa_salle)
            {
            if ($nb_resa_salle>0)
                {
                $nom_resa_salle.=", ";
                }
            $nb_resa_salle++;
            
            if ($nom_salle_afficher_alias==1)
            {
            $nom_resa_salle .= substr($res_resa_salle['alias'],0,$nb_caractere_salle_pour_vue_prof);
            }
            else
            {
            $nom_resa_salle .= substr($res_resa_salle['nom'],0,$nb_caractere_salle_pour_vue_prof);
            }		
            
            
            }
            
            
        //affichage du mot "salle" devant la liste des salles
        if ($affichage_mot_salle_pour_prof=='1')
        {
        if ($nb_resa_salle==1)
            {
            $nom_resa_salle="Salle : ".$nom_resa_salle;
            }
        
        if($nb_resa_salle>1)
            {
            $nom_resa_salle="Salles : ".$nom_resa_salle;
            }
        }		

                    

                // comptage des lignes a afficher pour la reservation

                // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_resa['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace+1,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                        }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne+1;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }
                    if  ($nb_resa_salle>0)
                    {
                    $compteur_ligne+=1;
                    }
                    

                    }				

                    

                    

                    //On affiche le titre de la reservation

                    // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    //coordonnees en y de la premiere ligne

                    
        $position=($bottomy + $topy+$epaisseur) / 2 +10- 10 * ($compteur_ligne/2);
                    

                    

                    if($res_resa['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                        

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (9 , 0, $fontb, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $resaname);

                            $position+=10;

                            }

                    //affichage de la salle associée à la réservation	
        if ($nb_resa_salle>0)
            {		
                    $size=imagettfbbox (7 , 0, $font,  $nom_resa_salle);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position, $noir, $font,  $nom_resa_salle);
            

                    }

                    }

                    

                







                }

                }



            }



        }


            //Coloriage en gris des réservations des groupes de niveau infiérieur
            //pour chaque bdd

            for ($k=0;$k<=0;$k++)

            {





        for ($i=0; $i<count($groupes_multi); $i++)
            {
            
                //liste des groupes de niv inférieur
            $groupeaafficher=$groupes_multi[$i];
            $groupeaafficher2="codeRessource=".$groupes_multi[$i];
                

            $critere2 = "and (";
                    
                $premier_groupe='0';
                $stop=0;
            
                while ($stop!=1 )
                    {
                    $sql="SELECT * FROM hierarchies_groupes WHERE (".$groupeaafficher2.") AND deleted= '0'";
                    $req_groupes_de_niveau_inf_conflit=$dbh->prepare($sql);
                    $req_groupes_de_niveau_inf_conflit=$req_groupes_de_niveau_inf_conflit->execute();
                    
                    $res_groupes_de_niveau_inf=$req_groupes_de_niveau_inf_conflit->fetchAll();
                    
                $groupeaafficher2="";	
                            
                    if (count($res_groupes_de_niveau_inf)>0)
                        {
                        foreach ($res_groupes_de_niveau_inf as $res_groupe_de_niveau_inf)
                            {
                            $critere2 .= "reservations_groupes.codeRessource='".$res_groupe_de_niveau_inf['codeRessourceFille']."' or ";
                            $groupeaafficher2.="codeRessource=".$res_groupe_de_niveau_inf['codeRessourceFille']." or ";

                            }
                        $groupeaafficher2.="0";	
                        
                        }
                    else 
                        {
                        $stop=1;
                        }

                unset ($req_groupes_de_niveau_inf_conflit);
                    }


            $critere2 .= "0)";	
            

            // Pour les jours à afficher, on interroge la DB
        for ($day=0;$day<$days;$day++)

            {



            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));


            // On recherche pour les groupes de l'etudiant et le jour choisi l'ensemble des séances
                unset ($req_resa);
                $sql="SELECT *, reservations.dureeReservation, reservations.commentaire FROM reservations_groupes LEFT JOIN (reservations) ON (reservations_groupes.codereservation=reservations.codereservation)  WHERE reservations.datereservation=:current_day AND reservations.deleted=0 ".$critere2." AND reservations_groupes.deleted=0  ";
                $req_resa=$dbh->prepare($sql);	
                $req_resa=$req_resa->execute(array(':current_day'=>$current_day));
                $res_resas=$req_resa->fetchAll();



                // Pour chaque réservation

                foreach($res_resas as $res_resa)

                {



                    
                    // On convertit l'horaire en %age de la journée


                    $start_time=((substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                    $duree=((substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60))/($endtime-$starttime+0.25);


                    $horaire_debut = substr((100+substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)),-2,2)."h".substr($res_resa['heureReservation'],-2,2);

                    $horaire_fin = (substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60) + (substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60);

                    $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



            // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +($i*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche









            //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins
        //couleur
        $couleur = imagecolorallocate ($im, 100, 100, 100);

        //valeur du rayon 
        $ray=5;

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$couleur);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $couleur);

            

        }


            }
            }
            
            }	
            

        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage cours     prof                   */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/

        //pour chaque bdd

            for ($k=0;$k<=0;$k++)

            {
            

            

        //preparation des requetes
        $sql="SELECT *, seances.dureeSeance, seances.commentaire, matieres.couleurFond,enseignements.nom as nom_enseignement, enseignements.alias as alias_enseignement  FROM seances_profs LEFT JOIN (seances) ON (seances_profs.codeSeance=seances.codeSeance) RIGHT JOIN (enseignements) ON (seances.codeEnseignement=enseignements.codeEnseignement) left join matieres on enseignements.codeMatiere=matieres.codeMatiere WHERE matieres.deleted=0 and  seances_profs.codeRessource=:current_prof AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_profs.deleted=0 ";
        $req_seance2=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_groupes LEFT JOIN ressources_groupes ON (ressources_groupes.codeGroupe=seances_groupes.codeRessource) WHERE codeSeance=:codeSeance AND seances_groupes.deleted=0 order by ressources_groupes.nom";
        $req_groupes3=$dbh->prepare($sql);	

        $sql="SELECT * FROM seances_salles LEFT JOIN ressources_salles ON (ressources_salles.codeSalle=seances_salles.codeRessource) WHERE codeSeance=:codeSeance AND seances_salles.deleted=0 AND ressources_salles.deleted=0 order by ressources_salles.nom";
        $req_salles2=$dbh->prepare($sql);	

        $sql="SELECT * FROM ressources_profs WHERE deleted='0' and codeProf=:current_prof";
        $req_profs2=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_materiels LEFT JOIN ressources_materiels ON (ressources_materiels.codeMateriel=seances_materiels.codeRessource) WHERE codeSeance=:codeSeance AND seances_materiels.deleted=0  and ressources_materiels.deleted='0' order by ressources_materiels.nom";
        $req_materiels2=$dbh->prepare($sql);

        $sql="SELECT * FROM hierarchies_groupes WHERE codeRessourceFille=:groupeaafficher AND deleted= '0'";
                    $req_groupes_de_niveau_supbis_conflit=$dbh->prepare($sql);

        $sql="SELECT * FROM hierarchies_groupes WHERE codeRessourceFille=:groupeaafficher AND deleted= '0'";
        $req_groupes_de_niveau_supbis_conflit2=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_profs LEFT JOIN ressources_profs ON (ressources_profs.codeProf=seances_profs.codeRessource) WHERE codeSeance=:codeSeance AND seances_profs.deleted=0 AND ressources_profs.deleted='0'  order by ressources_profs.nom";
        $req_profs4=$dbh->prepare($sql);
            
            
            
        for ($i=0; $i<count($profs_multi); $i++)

        {

        $current_prof= $profs_multi[$i];



        // Pour les 5 ou 6 jours à afficher, on interroge la DB

        for ($day=0;$day<$days;$day++)

            {



            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));



            // On recherche pour le prof et le jour choisi l'ensemble des séances

            $req_seance21=$req_seance2->execute(array(':current_day'=>$current_day, ':current_prof'=>$current_prof));
        $res_seances=$req_seance21->fetchAll();

            // Pour chaque séance

            foreach($res_seances as $res_seance)
                {
                // On convertit l'horaire en %age de la journée
                /* Explication conversion :

                On extrait d'une part les minutes et d'autre part l'heure.
                On transforme les minutes en fraction d'heure.
                On enlève starttime et on divise par la durée de la journée affichée endtime-starttime.
                On obtient un %age correspondant à la position du début du cours.
                Idem pour la durée mais sans enlever 8.15
                */

                $start_time=((substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);
                $duree=((substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60))/($endtime-$starttime+0.25);
                $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);
                $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);
                $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);


            // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-(($i+$nbdegroupe)+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche



                $req_groupes31=$req_groupes3->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_groupe=$req_groupes31->fetchAll();
        $dechex="FFFFFF";//couleur par défaut si pas de groupe (blanc)

        foreach($res_groupe as $res_groupes)
        {

            $couleur_des_seances_prof_prof=0;
                //recuperation de la couleur associee au groupe ou à la matiere ou au prof et conversion en rvb
        if ($couleur_des_seances_prof_prof==0) // si couleur des groupes
        {
        $dechex=dechex($res_groupes['couleurFond']);
        }
        elseif ($couleur_des_seances_prof_prof==1) // si couleur des matieres
        {
        $dechex=dechex($res_seance['couleurFond']);
        }
        elseif ($couleur_des_seances_prof_prof==2) // si couleur des matieres
        {
            $req_profs4=$req_profs4->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_prof=$req_profs4->fetchAll();
        foreach($res_prof as $res_profs)
            {
            $dechex=dechex($res_profs['couleurFond']);
            }
        unset ($res_prof);
        }
        else
        {
        $dechex=dechex($res_groupes['couleurFond']);
        }
                while (strlen($dechex)<6)

                    {

                    $dechex = "0".$dechex;

                    }
        }
            //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins
        //couleur
        $couleur = imagecolorallocate ($im, hexdec(substr($dechex,-2,2)), hexdec(substr($dechex,-4,2)), hexdec(substr($dechex,-6,2)));
        //valeur du rayon 15min
        //$ray=($hauteur-$topheight)/$nbintervalles;
        if ($res_seance['dureeSeance']<=100)
        {
        $ray=3;
        }
        else
        {
        $ray=5;
        }

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$couleur);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
            
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);



                

                

                

                // On compte le nombre d'elements dans la case

                // on met deja nb element egal 1 pour le nom de la matiere

                $nbelements=1;





                // On recherche pour cette séance le ou les groupes associés

                $req_groupes31=$req_groupes3->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_groupes=$req_groupes31->fetchAll();
                
                
                    if (count($res_groupes)>=1)
                        {
                        $nbelements+=count($res_groupes);
                        }


                // On compte le nombre de materiels a afficher
                $req_materiels21=$req_materiels2->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_materiel=$req_materiels21->fetchAll();		
                


                if (count($res_materiel))
                    {
                    //on affiche les materiels sur 1 seule ligne donc nbelement +1

                    $nbelements+=1;
                    }
                $nbmateriel=count($res_materiel);	




                // On recherche pour cette séance la ou les salles associées

                $req_salles21=$req_salles2->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_salle=$req_salles21->fetchAll();		
                if (count($res_salle)>=1)
                    {
                    //on affiche les salles sur 1 seule ligne donc nbelement +1

                    $nbelements+=1;
                    }

                $position= - ceil($nbelements / 2) + 1;	

                

                

                //on calcule le nombre de ligne de la chaine commentaire

                // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                                            }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne+1;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }

                    $nbelements+=$compteur_ligne;	

                    }



                

                

            

                

                

                


                // [GD] On affiche le type et la date des seances
        //découpage du nom de l'enseignement afin de récupérer ce qu'il y a entre les 2 premiers _
        $nom_enseignement_afficher_alias=0;       
        if ($nom_enseignement_afficher_alias==1)
        {
        $cursename=explode("_",$res_seance['alias_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['alias_enseignement'];
        }
        }
        else
        {
        $cursename=explode("_",$res_seance['nom_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['nom_enseignement'];
        }
        }


                
                
        //affectation de la couleur au type d'enseignement
                if ($res_seance['codeTypeActivite']==2)

                {

                    $couleur = $couleur_TD;

                }



                elseif ($res_seance['codeTypeActivite']==1)

                {

                    $couleur = $couleur_CR;

                }



                elseif ($res_seance['codeTypeActivite']==3)

                {

                    $couleur = $couleur_TP;

                }



                elseif ($res_seance['codeTypeActivite']==9)

                {

                    $couleur = $couleur_DS;

                }



                elseif ($res_seance['codeTypeActivite']==4)

                {

                    $couleur = $couleur_pro;

                }



                else

                {

                    $couleur = $couleur_defaut;

                }


            unset($req_type);
            $sql="SELECT * FROM types_activites WHERE codeTypeActivite=:type_activite" ;
            $req_type=$dbh->prepare($sql);	
            $req_type=$req_type->execute(array(':type_activite'=>$res_seance['codeTypeActivite']));
            $res_types=$req_type->fetchAll();		
                    
                foreach($res_types as $res_type)
            {
            $text = $res_type['alias']." - ";
            }


                

                // [GD] On affiche les horaires		

                $size=imagettfbbox (8 , 0, $font, $text.$horaire_debut." - ".$horaire_fin);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];

            //dessin de la case de l en-tete
            if ($res_seance['dureeSeance']<=100)
            {
            $epaisseur=11;
            }
            else
            {
            $epaisseur=16;
            }	
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$couleur);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $couleur,IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $couleur,IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);
        if ($res_seance['dureeSeance']<=100)
        {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 10  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
        }
        else
        {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 12  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
        }	
                




                

                //on affiche le nom de la seance

            //pense bete   dix caracteres ont une longueur de 92px

                $cursename=substr($cursename[1],0,round((($largeur-$leftwidth)/$days)*10/92)-1);

                $size=imagettfbbox (9 , 0, $fontb, $cursename);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];

                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy+$epaisseur) / 2 +12 -10 * ($nbelements/2)  , $noir, $fontb, $cursename);

                $position=($bottomy + $topy+$epaisseur) / 2 +12- 10 * ($nbelements/2)+10;



                

                

                

                

            // [GD] On affiche les commentaires sur la seance

            

            // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                        

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (7 , 0, $font, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (7 , 0, $font, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $resaname);

                            $position+=10;

                            }

            

                    }



                // [GD] On affiche les groupes concernés

        foreach ($res_groupes as $res_groupe)

                    {

                    if ($res_groupe['nom']!="")

                        {

                    $size=imagettfbbox (8 , 0, $font, substr($res_groupe['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, substr($res_groupe['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                    $position+=10;

                    }

                    }


                    

                $nbsalles=0;

                unset($salles);
                $salles="";


            foreach ($res_salle as $res_salles)

                    {

                    if ($nbsalles>0)

                        $salles.=", ";

                    $nbsalles++;
                    $nom_salle_afficher_alias=1;
                    $nb_caractere_salle_pour_vue_prof=20;
            if ($nom_salle_afficher_alias==1)
            {
            $salles .= substr($res_salles['alias'],0,$nb_caractere_salle_pour_vue_prof);
            }
            else
            {
            $salles .= substr($res_salles['nom'],0,$nb_caractere_salle_pour_vue_prof);
            }	
                    

                    }

                    $affichage_mot_salle_pour_prof='1';
                    //affichage du mot "salle" devant la liste des salles
                    if ($affichage_mot_salle_pour_prof=='1')
                    {
                    if ($nbsalles==1)
                        {
                        $salles="Salle : ".$salles;
                        }
                    
                    if($nbsalles>1)
                        {
                        $salles="Salles : ".$salles;
                        }
                    }


                    $size=imagettfbbox (7 , 0, $font, $salles);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-2  , $noir, $font, $salles);

                        // [GD] On affiche les materiels
        $position+=10;
                $nbmateriels=0;

                unset($materiels);

                $materiels="";



                foreach ($res_materiel as $res_materiels)

                    {

                    if ($nbmateriels>0)
                        {
                        $materiels.=", ";
                        }
                    $nbmateriels++;
            if ($nom_materiel_afficher_alias==1)
            {
            $materiels .= substr($res_materiels['alias'],0,$nb_caractere_materiel_pour_vue_prof);
            }
            else
            {
            $materiels .= substr($res_materiels['nom'],0,$nb_caractere_materiel_pour_vue_prof);
            }	
                    

                    }

                    $affichage_mot_materiel_pour_prof='1';
                    //affichage du mot "materiel" devant la liste des materiels
                    if ($affichage_mot_materiel_pour_prof=='1')
                    {
                    if ($nbmateriels==1)
                        {
                        $materiels="Materiel : ".$materiels;
                        }
                    
                    if($nbmateriels>1)
                        {
                        $materiels="Materiels : ".$materiels;
                        }
                    }
                    
                    
                    

                    $size=imagettfbbox (7 , 0, $font, $materiels);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-4, $noir, $font, $materiels);



                    
                    $hideprobleme='1';    
                    
        //affichage des causes de conflits			
        if ($hideprobleme!='1') {			
                
        //conflit groupe 

        unset ($req_groupes_conflit);
        $sql="SELECT * FROM seances_groupes LEFT JOIN ressources_groupes ON (ressources_groupes.codeGroupe=seances_groupes.codeRessource) WHERE codeSeance=:codeSeance AND seances_groupes.deleted=0 AND ressources_groupes.deleted='0' ";
        $req_groupes_conflit=$dbh->prepare($sql);
        $critere=" AND (";	
        $premier_groupe='1';
        $au_moins_un_groupe_affecte_a_la_seance="0";

                //liste des groupes de niv supérieur
        foreach ($res_groupes as $res_groupe)
            {
            $au_moins_un_groupe_affecte_a_la_seance="1";
            if ($res_groupe['codeGroupe']!="")
                {
                $groupeaafficher=$res_groupe['codeGroupe'];
                if ($premier_groupe!="1")
                    {
                    $critere=$critere." or ";

                    }
                $premier_groupe='0';
                $stop=0;

                while ($stop!=1 )
                    {
                    
                    $req_groupes_de_niveau_supbis_conflit->bindValue(':groupeaafficher', $groupeaafficher);
                    $req_groupes_de_niveau_supbis_conflit=$req_groupes_de_niveau_supbis_conflit->execute(array(':groupeaafficher'=>$groupeaafficher));
                    $res_groupes_de_niveau_supbis=$req_groupes_de_niveau_supbis_conflit->fetchAll();

                    $critere .= "seances_groupes.codeRessource='".$groupeaafficher."' or ";
                    if (count($res_groupes_de_niveau_supbis)>0)
                        {
                        $groupeaafficher=$res_groupes_de_niveau_supbis['0']['codeRessource'];
                        }
                    else 
                        {
                        $stop=1;
                        }

                
                    }

                $critere .= "0";
                }


            }
            
            if ($au_moins_un_groupe_affecte_a_la_seance=="0")
                {
                $critere .= "0";
                }
            
            $critere .= " ";
                
            
            //liste des groupes de niv inférieur
            $critere2 = "or ";
                //grp de niv inf
                foreach ($res_groupes as $res_groupe)
            {
            if ($res_groupe['codeGroupe']!="")
                {
                $groupeaafficher2="codeRessource=".$res_groupe['codeGroupe'];
                $groupeaafficher=$res_groupe['codeGroupe'];
            
                                
                $premier_groupe='0';
                $stop=0;

                while ($stop!=1 )
                    {
                    $sql="SELECT * FROM hierarchies_groupes WHERE (".$groupeaafficher2.") AND deleted= '0'";
                    $req_groupes_de_niveau_inf_conflit=$dbh->prepare($sql);
                    $req_groupes_de_niveau_inf_conflit=$req_groupes_de_niveau_inf_conflit->execute();
                    
                    $res_groupes_de_niveau_inf=$req_groupes_de_niveau_inf_conflit->fetchAll();
                    
                $groupeaafficher2="";	
                
                
                
                                
                    
                    if (count($res_groupes_de_niveau_inf)>0)
                        {
                        foreach ($res_groupes_de_niveau_inf as $res_groupe_de_niveau_inf)
                            {
                            $critere2 .= "seances_groupes.codeRessource='".$res_groupe_de_niveau_inf['codeRessourceFille']."' or ";
                            $groupeaafficher2.="codeRessource=".$res_groupe_de_niveau_inf['codeRessourceFille']." or ";

                            }
                        $groupeaafficher2.="0";	
                        
                        }
                    else 
                        {
                        $stop=1;
                        }

                unset ($req_groupes_de_niveau_inf_conflit);
                    }

                
                
                }
            }
            
            $critere2 .= "0)";
            
            
                
            
            
            
            

        $conflit_groupe1='0';	
        $conflit_groupe2='0';	
        //cas 1 (seance qui chevauche l'heure de début)
        unset($req_seance_conflit0);
        $sql="SELECT * FROM seances_groupes LEFT JOIN (seances) ON (seances_groupes.codeSeance=seances.codeSeance)  WHERE  seances.heureSeance<=:h_debut  ".$critere.$critere2." AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_groupes.deleted=0  AND seances.codeSeance!=:code_seance ";
        $req_seance_conflit0=$dbh->prepare($sql);
        $req_seance_conflit0=$req_seance_conflit0->execute(array(':current_day'=>$current_day, ':h_debut'=>$res_seance['heureSeance'], ':code_seance'=>$res_seance['codeSeance']));

        $res_seances_conflit0=$req_seance_conflit0->fetchAll();
        foreach($res_seances_conflit0 as $res_seance_conflit0)
            {
            $horaire_debut = substr((100+substr($res_seance_conflit0['heureSeance'],-strlen($res_seance_conflit0['heureSeance']),strlen($res_seance_conflit0['heureSeance'])-2)),-2,2)."h".substr($res_seance_conflit0['heureSeance'],-2,2);
            $horaire_fin = (substr($res_seance_conflit0['heureSeance'],-strlen($res_seance_conflit0['heureSeance']),strlen($res_seance_conflit0['heureSeance'])-2)+substr($res_seance_conflit0['heureSeance'],-2,2)/60) + (substr($res_seance_conflit0['dureeSeance'],-strlen($res_seance_conflit0['dureeSeance']),strlen($res_seance_conflit0['dureeSeance'])-2)+substr($res_seance_conflit0['dureeSeance'],-2,2)/60);
            $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);		

            if ($horaire_fin>$res_seance['heureSeance'])
                {
                $conflit_groupe1='1';
                }
            }
                
            
            
        //cas 2 (seance qui chevauche l'heure de fin)
        unset($req_seance_conflit0bis);
        //mise en forme de l'heure de fin en faisant l'adition heure de debut plus durée.
        $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);
        $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);
        $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);
        $sql="SELECT * FROM seances_groupes LEFT JOIN (seances) ON (seances_groupes.codeSeance=seances.codeSeance)  WHERE  (seances.heureSeance>=:heure_debut  AND seances.heureSeance<:heure_fin )  ".$critere.$critere2." AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_groupes.deleted=0  AND seances.codeSeance!=:code_seance ";	
        $req_seance_conflit0bis=$dbh->prepare($sql);
        $req_seance_conflit0bis=$req_seance_conflit0bis->execute(array(':current_day'=>$current_day, ':heure_debut'=>$res_seance['heureSeance'], ':heure_fin'=>$horaire_fin, ':code_seance'=>$res_seance['codeSeance']));


        $res_seances_conflit0bis=$req_seance_conflit0bis->fetchAll();
        foreach($res_seances_conflit0bis as $res_seance_conflit0bis)
            {
            $conflit_groupe2='1';
            }

            
            
                
                
        //conflit salle

        $critere=" AND (";	

        foreach ($res_salle as $res_salles)
            {
            if ($res_salles['codeSalle']!="")
                {
                $salleaafficher=$res_salles['codeSalle'];


                $critere .= "seances_salles.codeRessource='".$salleaafficher."' or ";
                }
            }
            $critere .= "0)";
            

            

            $conflit_salle1='0';	
            $conflit_salle2='0';	
        //cas 1 (seance qui chevauche l'heure de début)
        unset($req_seance_conflit3);
        $sql="SELECT * FROM seances_salles LEFT JOIN (seances) ON (seances_salles.codeSeance=seances.codeSeance)  WHERE  seances.heureSeance<=:h_debut  ".$critere." AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_salles.deleted=0  AND seances.codeSeance!=:code_seance ";
        $req_seance_conflit3=$dbh->prepare($sql);
        $req_seance_conflit3=$req_seance_conflit3->execute(array(':current_day'=>$current_day, ':h_debut'=>$res_seance['heureSeance'], ':code_seance'=>$res_seance['codeSeance']));

        $res_seances_conflit3=$req_seance_conflit3->fetchAll();
        foreach($res_seances_conflit3 as $res_seance_conflit3)
            {
            $horaire_debut = substr((100+substr($res_seance_conflit3['heureSeance'],-strlen($res_seance_conflit3['heureSeance']),strlen($res_seance_conflit3['heureSeance'])-2)),-2,2)."h".substr($res_seance_conflit3['heureSeance'],-2,2);
            $horaire_fin = (substr($res_seance_conflit3['heureSeance'],-strlen($res_seance_conflit3['heureSeance']),strlen($res_seance_conflit3['heureSeance'])-2)+substr($res_seance_conflit3['heureSeance'],-2,2)/60) + (substr($res_seance_conflit3['dureeSeance'],-strlen($res_seance_conflit3['dureeSeance']),strlen($res_seance_conflit3['dureeSeance'])-2)+substr($res_seance_conflit3['dureeSeance'],-2,2)/60);
            $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);		

            if ($horaire_fin>$res_seance['heureSeance'])
                {
                $conflit_salle1='1';
                }
            }

            
                
            

        //cas 2 (seance qui chevauche l'heure de fin)
        unset($req_seance_conflit4);
        //mise en forme de l'heure de fin en faisant l'adition heure de debut plus durée.
        $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);
        $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);
        $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);
        $sql="SELECT * FROM seances_salles LEFT JOIN (seances) ON (seances_salles.codeSeance=seances.codeSeance)  WHERE  (seances.heureSeance>=:heure_debut  AND seances.heureSeance<:heure_fin )  ".$critere." AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_salles.deleted=0  AND seances.codeSeance!=:code_seance ";	
        $req_seance_conflit4=$dbh->prepare($sql);
        $req_seance_conflit4=$req_seance_conflit4->execute(array(':current_day'=>$current_day, ':heure_debut'=>$res_seance['heureSeance'], ':heure_fin'=>$horaire_fin, ':code_seance'=>$res_seance['codeSeance']));


        $res_seances_conflit4=$req_seance_conflit4->fetchAll();
        foreach($res_seances_conflit4 as $res_seance_conflit4)
            {
            $conflit_salle2='1';
            }	



                
        //recherche conflit prof
            $conflit_prof1='0';	
            $conflit_prof2='0';	



        //cas 1 (seance qui chevauche l'heure de début)
        unset($req_seance_conflit1);
        $sql="SELECT * FROM seances_profs LEFT JOIN (seances) ON (seances_profs.codeSeance=seances.codeSeance)  WHERE  seances.heureSeance<=:h_debut  and  seances_profs.codeRessource=:current_prof AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_profs.deleted=0  AND seances.codeSeance!=:code_seance ";
        $req_seance_conflit1=$dbh->prepare($sql);
        $req_seance_conflit1=$req_seance_conflit1->execute(array(':current_day'=>$current_day, ':current_prof'=>$current_prof,  ':h_debut'=>$res_seance['heureSeance'], ':code_seance'=>$res_seance['codeSeance']));

        $res_seances_conflit1=$req_seance_conflit1->fetchAll();
        foreach($res_seances_conflit1 as $res_seance_conflit1)
            {
            $horaire_debut = substr((100+substr($res_seance_conflit1['heureSeance'],-strlen($res_seance_conflit1['heureSeance']),strlen($res_seance_conflit1['heureSeance'])-2)),-2,2)."h".substr($res_seance_conflit1['heureSeance'],-2,2);
            $horaire_fin = (substr($res_seance_conflit1['heureSeance'],-strlen($res_seance_conflit1['heureSeance']),strlen($res_seance_conflit1['heureSeance'])-2)+substr($res_seance_conflit1['heureSeance'],-2,2)/60) + (substr($res_seance_conflit1['dureeSeance'],-strlen($res_seance_conflit1['dureeSeance']),strlen($res_seance_conflit1['dureeSeance'])-2)+substr($res_seance_conflit1['dureeSeance'],-2,2)/60);
            $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);		

            if ($horaire_fin>$res_seance['heureSeance'])
                {
                $conflit_prof1='1';
                }
            }
                
                        
            

        //cas 2 (seance qui chevauche l'heure de fin)
        unset($req_seance_conflit2);
        //mise en forme de l'heure de fin en faisant l'adition heure de debut plus durée.
        $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);
        $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);
        $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);
        $sql="SELECT *, seances.dureeSeance, seances.commentaire FROM seances_profs LEFT JOIN (seances) ON (seances_profs.codeSeance=seances.codeSeance)  WHERE  (seances.heureSeance>=:heure_debut  AND seances.heureSeance<:heure_fin )  and seances_profs.codeRessource=:current_prof AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_profs.deleted=0  AND seances.codeSeance!=:code_seance ";	
        $req_seance_conflit2=$dbh->prepare($sql);
        $req_seance_conflit2=$req_seance_conflit2->execute(array(':current_day'=>$current_day, ':current_prof'=>$current_prof,  ':heure_debut'=>$res_seance['heureSeance'], ':heure_fin'=>$horaire_fin, ':code_seance'=>$res_seance['codeSeance']));


        $res_seances_conflit2=$req_seance_conflit2->fetchAll();
        foreach($res_seances_conflit2 as $res_seance_conflit2)
            {
            $conflit_prof2='1';
            }	
            
                
        //conflit materiel

        $critere=" AND (";	

        foreach ($res_materiel as $res_materiels)
            {
            if ($res_materiels['codeMateriel']!="")
                {
                $materielaafficher=$res_materiels['codeMateriel'];


                $critere .= "seances_materiels.codeRessource='".$materielaafficher."' or ";
                }
            }
            $critere .= "0)";
            

            

            $conflit_materiel1='0';	
            $conflit_materiel2='0';	
        //cas 1 (seance qui chevauche l'heure de début)
        unset($req_seance_conflit5);
        $sql="SELECT * FROM seances_materiels LEFT JOIN (seances) ON (seances_materiels.codeSeance=seances.codeSeance)  WHERE  seances.heureSeance<=:h_debut  ".$critere." AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_materiels.deleted=0  AND seances.codeSeance!=:code_seance ";
        $req_seance_conflit5=$dbh->prepare($sql);
        $req_seance_conflit5=$req_seance_conflit5->execute(array(':current_day'=>$current_day, ':h_debut'=>$res_seance['heureSeance'], ':code_seance'=>$res_seance['codeSeance']));

        $res_seances_conflit5=$req_seance_conflit5->fetchAll();
        foreach($res_seances_conflit5 as $res_seance_conflit5)
            {
            $horaire_debut = substr((100+substr($res_seance_conflit5['heureSeance'],-strlen($res_seance_conflit5['heureSeance']),strlen($res_seance_conflit5['heureSeance'])-2)),-2,2)."h".substr($res_seance_conflit5['heureSeance'],-2,2);
            $horaire_fin = (substr($res_seance_conflit5['heureSeance'],-strlen($res_seance_conflit5['heureSeance']),strlen($res_seance_conflit5['heureSeance'])-2)+substr($res_seance_conflit5['heureSeance'],-2,2)/60) + (substr($res_seance_conflit5['dureeSeance'],-strlen($res_seance_conflit5['dureeSeance']),strlen($res_seance_conflit5['dureeSeance'])-2)+substr($res_seance_conflit5['dureeSeance'],-2,2)/60);
            $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);		

            if ($horaire_fin>$res_seance['heureSeance'])
                {
                $conflit_materiel1='1';
                }
            }

            
                
            

        //cas 2 (seance qui chevauche l'heure de fin)
        unset($req_seance_conflit6);
        //mise en forme de l'heure de fin en faisant l'adition heure de debut plus durée.
        $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);
        $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);
        $horaire_fin = substr(intval($horaire_fin + 100),-2,2).substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);
        $sql="SELECT * FROM seances_materiels LEFT JOIN (seances) ON (seances_materiels.codeSeance=seances.codeSeance)  WHERE  (seances.heureSeance>=:heure_debut  AND seances.heureSeance<:heure_fin )  ".$critere." AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_materiels.deleted=0  AND seances.codeSeance!=:code_seance ";	
        $req_seance_conflit6=$dbh->prepare($sql);
        $req_seance_conflit6=$req_seance_conflit6->execute(array(':current_day'=>$current_day, ':heure_debut'=>$res_seance['heureSeance'], ':heure_fin'=>$horaire_fin, ':code_seance'=>$res_seance['codeSeance']));


        $res_seances_conflit6=$req_seance_conflit6->fetchAll();
        foreach($res_seances_conflit6 as $res_seance_conflit6)
            {
            $conflit_materiel2='1';
            }	


            
            $affichage_message_pas_salle='1';
        // verification si nb de salle supérieur à 0 et si pas conflit prof, groupe...

        if ( ($nbsalles=='0' && $affichage_message_pas_salle=='1') || $conflit_prof1=='1' ||$conflit_prof2=='1' || $conflit_groupe1=='1'  || $conflit_groupe2=='1'|| $conflit_salle1=='1' || $conflit_salle2=='1' || $conflit_materiel1=='1' || $conflit_materiel2=='1')
        {
        if ($nbsalles=='0' && $affichage_message_pas_salle=='1')
        {
        $manque_salle='1';
        }
        else 
        {
        $manque_salle='0';
        }

        if($conflit_prof1=='1' || $conflit_prof2=='1')
        {
        $conflit_prof='1';
        }
        else
        {
        $conflit_prof='0';
        }


        if($conflit_groupe1=='1' || $conflit_groupe2=='1')
        {
        $conflit_groupe='1';
        }
        else
        {
        $conflit_groupe='0';
        }


        if($conflit_salle1=='1' || $conflit_salle2=='1')
        {
        $conflit_salle='1';
        }
        else
        {
        $conflit_salle='0';
        }

        if($conflit_materiel1=='1' || $conflit_materiel2=='1')
        {
        $conflit_materiel='1';
        }
        else
        {
        $conflit_materiel='0';
        }

        $nb_de_ligne=$manque_salle+$conflit_prof+$conflit_groupe+$conflit_salle+$conflit_materiel;
            

            //dessiner un rectangle blanc avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins
        //couleur noire
        $couleur = imagecolorallocate ($im, 0, 0,0 );
        //valeur du rayon 2

        $ray=2;
        /*
            // dessin des 2 rectangles

        imagefilledrectangle($im,$leftx+2+10,$topy+$ray+10,$rightx-2-10,$bottomy-$ray-4,$couleur);
        imagefilledrectangle($im,$leftx+2+$ray+10,$topy+10,$rightx-2-$ray-10,$bottomy-4,$couleur);

            

            // dessin des 4 cercles

        imagefilledellipse($im, $leftx+2+$ray+10, $topy+$ray+10, $ray*2, $ray*2, $couleur);
        imagefilledellipse($im, $leftx+2+$ray+10, $bottomy-$ray-4, $ray*2, $ray*2, $couleur);
        imagefilledellipse($im, $rightx-2-$ray-10, $bottomy-$ray-4, $ray*2, $ray*2, $couleur);
        imagefilledellipse($im, $rightx-2-$ray-10, $topy+$ray+10, $ray*2, $ray*2, $couleur);

        imagearc($im, $leftx+2+$ray+10, $topy+$ray+10, $ray*2, $ray*2,180,270, $couleur);
        imagearc($im, $leftx+2+$ray+10, $bottomy-$ray-4, $ray*2, $ray*2,90,180, $couleur);
        imagearc($im, $rightx-2-$ray-10, $bottomy-$ray-4, $ray*2, $ray*2,0,90, $couleur);
        imagearc($im, $rightx-2-$ray-10, $topy+$ray+10, $ray*2, $ray*2,270,0, $couleur);
        */
            // dessin des 2 rectangles

        imagefilledrectangle($im,$leftx+2+10,($bottomy + $topy+10) / 2-5*$nb_de_ligne+$ray,$rightx-2-10,($bottomy + $topy+10) / 2+5*$nb_de_ligne-$ray,$couleur);
        imagefilledrectangle($im,$leftx+2+$ray+10,($bottomy + $topy+10) / 2-5*$nb_de_ligne,$rightx-2-$ray-10,($bottomy + $topy+10) / 2+5*$nb_de_ligne,$couleur);

            

            // dessin des 4 cercles

        imagefilledellipse($im, $leftx+2+$ray+10, ($bottomy + $topy+10) / 2-5*$nb_de_ligne+$ray, $ray*2, $ray*2, $couleur);
        imagefilledellipse($im, $leftx+2+$ray+10, ($bottomy + $topy+10) / 2+5*$nb_de_ligne-$ray, $ray*2, $ray*2, $couleur);
        imagefilledellipse($im, $rightx-2-$ray-10, ($bottomy + $topy+10) / 2+5*$nb_de_ligne-$ray, $ray*2, $ray*2, $couleur);
        imagefilledellipse($im, $rightx-2-$ray-10, ($bottomy + $topy+10) / 2-5*$nb_de_ligne+$ray, $ray*2, $ray*2, $couleur);

        imagearc($im, $leftx+2+$ray+10, ($bottomy + $topy+10) / 2-5*$nb_de_ligne+$ray, $ray*2, $ray*2,180,270, $couleur);
        imagearc($im, $leftx+2+$ray+10, ($bottomy + $topy+10) / 2+5*$nb_de_ligne-$ray, $ray*2, $ray*2,90,180, $couleur);
        imagearc($im, $rightx-2-$ray-10, ($bottomy + $topy+10) / 2+5*$nb_de_ligne-$ray, $ray*2, $ray*2,0,90, $couleur);
        imagearc($im, $rightx-2-$ray-10, ($bottomy + $topy+10) / 2-5*$nb_de_ligne+$ray, $ray*2, $ray*2,270,0, $couleur);




        //marque "pas de salle"
        $position='0';
        if ($nbsalles=='0' && $res_seance['annulee']!='1' && $affichage_message_pas_salle=='1')
            {
            $pasdesalle="Pas de salle";
            $size=imagettfbbox (7 , 0, $fontb, $pasdesalle);
            $box_lenght=$size[2]-$size[0];
            $box_width=$size[1]-$size[7];
            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy) / 2 +4 -10 * ($nb_de_ligne/2) +10 , $blanc, $fontb, $pasdesalle);
            $position=$position+10;		
            }
            
        //marque conflit prof	
        if ( $res_seance['annulee']!='1' && ($conflit_prof1=='1' ||$conflit_prof2=='1') )
            {
            $conflitprof="Prof : 2 cours simultanés";
            $size=imagettfbbox (7 , 0, $fontb, $conflitprof);
            $box_lenght=$size[2]-$size[0];
            $box_width=$size[1]-$size[7];
            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy) / 2 +4 -10 * ($nb_de_ligne/2) +10+$position , $blanc, $fontb, $conflitprof);
            $position=$position+10;			
            }

        //marque conflit groupe	
        if ( $res_seance['annulee']!='1'  && ($conflit_groupe1=='1' ||$conflit_groupe2=='1' ))
            {
            $conflitgroupe="Groupe : 2 cours simultanés";
            $size=imagettfbbox (7 , 0, $fontb, $conflitgroupe);
            $box_lenght=$size[2]-$size[0];
            $box_width=$size[1]-$size[7];
            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy) / 2 +4 -10 * ($nb_de_ligne/2) +10+$position , $blanc, $fontb, $conflitgroupe);
            $position=$position+10;			
            }

        //marque conflit salle	
        if ( $res_seance['annulee']!='1' && ($conflit_salle1=='1' ||$conflit_salle2=='1'))
            {
            $conflitsalle="Salle : 2 cours simultanés";
            $size=imagettfbbox (7 , 0, $fontb, $conflitsalle);
            $box_lenght=$size[2]-$size[0];
            $box_width=$size[1]-$size[7];
            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy) / 2 +4 -10 * ($nb_de_ligne/2) +10+$position , $blanc, $fontb, $conflitsalle);
            $position=$position+10;			
            }	

        //marque conflit materiel	
        if ( $res_seance['annulee']!='1' && ($conflit_materiel1=='1' ||$conflit_materiel2=='1'))
            {
            $conflitmateriel="Materiel : 2 cours simultanés";
            $size=imagettfbbox (7 , 0, $fontb, $conflitmateriel);
            $box_lenght=$size[2]-$size[0];
            $box_width=$size[1]-$size[7];
            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy) / 2 +4 -10 * ($nb_de_ligne/2) +10+$position , $blanc, $fontb, $conflitmateriel);
            $position=$position+10;			
            }
            
                
        //marque seance annulée
        if ( $res_seance['annulee']=='1')
            {
            $seance_annulee="Séance annulée";
            $size=imagettfbbox (7 , 0, $fontb, $seance_annulee);
            $box_lenght=$size[2]-$size[0];
            $box_width=$size[1]-$size[7];
            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy) / 2 +4 -10 * ($nb_de_ligne/2) +10+$position , $blanc, $fontb, $seance_annulee);
            }			
                
        }			
                    
                    
            }		
                    

                }

                
                
                
                        //on affiche le nom des profs en dessous de la date
                        //seulement pour la derniere base de données car sinon les nom sont écrit les uns sur les autres. De plus comme dans la liste déroulante il n'y a que les noms de la derniere base...

                            $req_profs21=$req_profs2->execute(array(':current_prof'=>$current_prof));
                    $res_prof2=$req_profs21->fetchAll();	

                    foreach($res_prof2 as $res)

                        {

                        $size=imagettfbbox (8 , 0, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

                        $box_lenght=$size[2]-$size[0];

                        $box_width=$size[1]-$size[7];

                        $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                        $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-(($i+$nbdegroupe)+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , 60  , $noir, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

                        }

                        	




        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage des reservation      profs       */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/

                

                

                

        if (!$hideprivate) {



        //test si prof loggué avec login perso
        $test_login=0;
        if (isset($_SESSION['logged_prof_perso']))
        {
        if ($current_prof==$_SESSION['logged_prof_perso'])
        {
        $test_login=1;
        }
        }


        //preparation de requetes
        $sql="SELECT * FROM reservations_salles left join (ressources_salles ) on ressources_salles.codeSalle=reservations_salles.codeRessource WHERE reservations_salles.codeReservation=:codeReservation AND reservations_salles.deleted=0 and ressources_salles.deleted=0 order by ressources_salles.nom";
        $req_resa_salle=$dbh->prepare($sql);

        $pas_afficher_reservation_privee=0;
        $contenu_reservation_privee=1;
        if ($pas_afficher_reservation_privee==1)
        {
        $sql="SELECT * FROM reservations_profs LEFT JOIN (reservations) USING (codeReservation) WHERE reservations_profs.codeRessource=:current_prof AND reservations.dateReservation=:current_day AND reservations.deleted=0 AND reservations.diffusable=1  AND reservations_profs.deleted=0 ";
        }

        elseif($contenu_reservation_privee==0)
        {
        $sql="SELECT * FROM reservations_profs LEFT JOIN (reservations) USING (codeReservation) WHERE reservations_profs.codeRessource=:current_prof AND reservations.dateReservation=:current_day AND reservations.deleted=0  AND reservations_profs.deleted=0 ";
        }
        elseif ($test_login==1 && $contenu_reservation_privee==1)
        {
        $sql="SELECT * FROM reservations_profs LEFT JOIN (reservations) USING (codeReservation) WHERE reservations_profs.codeRessource=:current_prof AND reservations.dateReservation=:current_day AND reservations.deleted=0  AND reservations_profs.deleted=0 ";
        }
        elseif ($test_login==0 && $contenu_reservation_privee==1)
        {
        $sql="SELECT * FROM reservations_profs LEFT JOIN (reservations) USING (codeReservation) WHERE reservations_profs.codeRessource=:current_prof AND reservations.dateReservation=:current_day AND reservations.deleted=0 AND reservations.diffusable=1  AND reservations_profs.deleted=0 ";
        }

        //$sql="SELECT * FROM reservations_profs LEFT JOIN (reservations) USING (codeReservation) WHERE reservations_profs.codeRessource=:current_prof AND reservations.dateReservation=:current_day AND reservations.deleted=0  AND reservations_profs.deleted=0 ";
        $req_resa2=$dbh->prepare($sql);	
        $req_resa2=$req_resa2->execute(array(':current_day'=>$current_day,':current_prof'=>$current_prof));
        $res_resas=$req_resa2->fetchAll();

                // Pour chaque reservation

            foreach($res_resas as $res_resa)

                {





                

                    // On convertit l'horaire en %age de la journée



                    /* Explication conversion :



                    On extrait d'une part les minutes et d'autre part l'heure.

                    On transforme les minutes en fraction d'heure.

                    On enlève starttime et on divise par la durée de la journée affichée endtime-starttime.

                    On obtient un %age correspondant à la position du début du cours.

                    Idem pour la durée mais sans enlever 8.15



                    */







                    $start_time=((substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                    $duree=((substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60))/($endtime-$starttime+0.25);

                    $horaire_debut = substr((100+substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)),-2,2)."h".substr($res_resa['heureReservation'],-2,2);

                    $horaire_fin = (substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60) + (substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60);

                    $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



            // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  )+10; // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-(($i+$nbdegroupe)+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource)-10; // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche

                    

                

                //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins

        //valeur du rayon 15min
        //$ray=($hauteur-$topheight)/$nbintervalles;
        if ($res_resa['dureeReservation']<=100)
        {
        $ray=3;
        }
        else
        {

        $ray=5;
        }
        if ($res_resa['diffusable']=='1')
        {

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$rdv[1]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$rdv[1]);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
            }
            else
            {
                    imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$rdv[3]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$rdv[3]);
            
                    imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $rdv[3]);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[3]);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[3]);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $rdv[3]);
            
            
            }
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);		


        if (isset($_SESSION['logged_prof_perso']))
        {
                    

                    if ($res_resa['codeProprietaire']=='999' && $_SESSION['logged_prof_perso']==$res_resa['codeRessource'] )

                    {

                    // [GD] On affiche les horaires
        $text=$horaire_debut." - ".$horaire_fin." "."X";
                    $size=imagettfbbox (8 , 0, $font, $text);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];





                    



                    //dessin de la case de l en-tete
        if ($res_resa['dureeReservation']<=100)
        {
        $epaisseur=11;
        }
        else
        {
        $epaisseur=16;
        }			
                    
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$rdv[4]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$rdv[4]);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $rdv[4],IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $rdv[4],IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);
            if ($res_resa['dureeReservation']<=100)
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 10  , $noir, $font, $text);
            }
            else
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 12  , $noir, $font, $text);
            }
                    

                    
                    
                    
                    
                    
                    }			

                                else

                    {

                    // [GD] On affiche les horaires
        $text=$horaire_debut." - ".$horaire_fin;
                    $size=imagettfbbox (8 , 0, $font, $text);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];





                



                    //dessin de la case de l en-tete
                    if ($res_resa['dureeReservation']<=100)
                    {
                    $epaisseur=11;
                    }
                    else
                    {
                    $epaisseur=16;
                    }	
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$rdv[4]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$rdv[4]);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $rdv[4],IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $rdv[4],IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);

            if ($res_resa['dureeReservation']<=100)
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 10  , $noir, $font, $text);
            }
            else
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 12  , $noir, $font, $text);
            }		
                
                    

                    }

                    }

                    else

                    {

                    // [GD] On affiche les horaires
        $text=$horaire_debut." - ".$horaire_fin;
                    $size=imagettfbbox (8 , 0, $font, $text);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];





                    



                    //dessin de la case de l en-tete
                    if ($res_resa['dureeReservation']<=100)
                    {
                    $epaisseur=11;
                    }
                    else
                    {
                    $epaisseur=16;
                    }	
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$rdv[4]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$rdv[4]);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $rdv[4],IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $rdv[4],IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);
            if ($res_resa['dureeReservation']<=100)
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 10  , $noir, $font, $text);
            }
            else
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 12  , $noir, $font, $text);
            }	
                    

                    }

                    

                            // recherche si une salle est associée à la réservation
                            $req_resa_salle=$req_resa_salle->execute(array(':codeReservation'=>$res_resa['codeReservation']));
        $res_resa_salles=$req_resa_salle->fetchAll();
        $nb_resa_salle=0;
        $nom_resa_salle="";
        foreach($res_resa_salles as $res_resa_salle)
            {
            if ($nb_resa_salle>0)
                {
                $nom_resa_salle.=", ";
                }
            $nb_resa_salle++;
            
                if ($nom_salle_afficher_alias==1)
            {
            $nom_resa_salle .= substr($res_resa_salle['alias'],0,$nb_caractere_salle_pour_vue_prof);
            }
            else
            {
            $nom_resa_salle .= substr($res_resa_salle['nom'],0,$nb_caractere_salle_pour_vue_prof);
            }	
            
            
            }
            
            
        //affichage du mot "salle" devant la liste des salles
        if ($affichage_mot_salle_pour_prof=='1')
        {
        if ($nb_resa_salle==1)
            {
            $nom_resa_salle="Salle : ".$nom_resa_salle;
            }
        
        if($nb_resa_salle>1)
            {
            $nom_resa_salle="Salles : ".$nom_resa_salle;
            }
        }		

                

                // comptage des lignes a afficher pour la reservation

                // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_resa['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                                            }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne+1;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }

                    if  ($nb_resa_salle>0)
                    {
                    $compteur_ligne+=1;
                    }			

                    }			

                    

                    

                    

                    //On affiche le titre de la reservation

                    // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    //coordonnees en y de la premiere ligne

                    $position=($bottomy + $topy+$epaisseur) / 2 +10- 10 * ($compteur_ligne/2);

                    

                    

                    if($res_resa['commentaire']!=""  && $res_resa['diffusable']=='1' || $test_login==1   )

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];
                                                        

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)
                                

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (8 , 0, $fontb, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $resaname);

                            $position+=10;

                            }

            

                    }

                    

                    else

                    {

                    $position=($bottomy + $topy) / 2 +4- 5 +10;

                    $resaname="Privé";

                    $size=imagettfbbox (8 , 0, $fontb, $resaname);

                    $box_lenght=$size[2]-$size[0];

                    imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $resaname);

                    $position+=10;

                    }

                    //affichage de la salle associée à la réservation	
        if ($nb_resa_salle>0)
            {		
                    $size=imagettfbbox (7 , 0, $font,  $nom_resa_salle);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position, $noir, $font,  $nom_resa_salle);
            

                    }



                }

            }

        }	

            }



        }



        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage cours  SALLES                    */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/



        //pour chaque bdd

            for ($k=0;$k<=0;$k++)

            {
            


        //preparation des requetes
        $sql="SELECT * FROM ressources_salles WHERE deleted='0' and codeSalle=:current_salle";
        $req_salle3=$dbh->prepare($sql);

        $sql="SELECT *, seances.dureeSeance, seances.commentaire, matieres.couleurFond,enseignements.nom as nom_enseignement, enseignements.alias as alias_enseignement  FROM seances_salles LEFT JOIN (seances) ON (seances_salles.codeSeance=seances.codeSeance) RIGHT JOIN (enseignements) ON (seances.codeEnseignement=enseignements.codeEnseignement)  left join matieres on enseignements.codeMatiere=matieres.codeMatiere WHERE matieres.deleted=0 and seances_salles.codeRessource=:current_salle AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_salles.deleted=0 ";
        $req_seance3=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_groupes LEFT JOIN ressources_groupes ON (ressources_groupes.codeGroupe=seances_groupes.codeRessource) WHERE codeSeance=:codeSeance AND seances_groupes.deleted=0 order by ressources_groupes.nom";
        $req_groupes4=$dbh->prepare($sql);	

        $sql="SELECT * FROM seances_profs LEFT JOIN ressources_profs ON (ressources_profs.codeProf=seances_profs.codeRessource) WHERE codeSeance=:codeSeance AND seances_profs.deleted=0 AND ressources_profs.deleted=0 order by ressources_profs.nom";
        $req_profs3=$dbh->prepare($sql);


        $sql="SELECT * FROM seances_materiels LEFT JOIN ressources_materiels ON (ressources_materiels.codeMateriel=seances_materiels.codeRessource) WHERE codeSeance=:codeSeance AND seances_materiels.deleted=0  and ressources_materiels.deleted='0' order by ressources_materiels.nom";
        $req_materiels3=$dbh->prepare($sql);




        for ($i=0; $i<count($salles_multi); $i++)

        {

        $current_salle= $salles_multi[$i];




        // Pour les 5 ou 6 jours à afficher, on interroge la DB

        for ($day=0;$day<$days;$day++)

            {



            

        //on affiche le nom de la salle en dessous de la date
                        //seulement pour la derniere base de données car sinon les nom sont écrit les uns sur les autres. De plus comme dans la liste déroulante il n'y a que les noms de la derniere base...
                        
                        $req_salle31=$req_salle3->execute(array(':current_salle'=>$current_salle));
                $res_salle3=$req_salle31->fetchAll();				
                foreach ($res_salle3 as $res)

            {

            $size=imagettfbbox (8 , 0, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

            $box_lenght=$size[2]-$size[0];

            $box_width=$size[1]-$size[7];

            $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe+$nbdeprof)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

            $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+$nbdegroupe+$nbdeprof+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , 60  , $noir, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

            }

            

            

            

            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));



            // On recherche pour la salle et le jour choisi l'ensemble des séances
            $req_seance31=$req_seance3->execute(array(':current_day'=>$current_day, ':current_salle'=>$current_salle));
        $res_seances=$req_seance31->fetchAll();


            // Pour chaque séance
            foreach($res_seances as $res_seance)

                {



                // On convertit l'horaire en %age de la journée



                /* Explication conversion :



                On extrait d'une part les minutes et d'autre part l'heure.

                On transforme les minutes en fraction d'heure.

                On enlève starttime et on divise par la durée de la journée affichée endtime-starttime.

                On obtient un %age correspondant à la position du début du cours.

                Idem pour la durée mais sans enlever 8.15



                */



                





                $start_time=((substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                $duree=((substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60))/($endtime-$starttime+0.25);



                $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);

                $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);

                $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



                // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe+$nbdeprof)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+$nbdegroupe+$nbdeprof+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche



                $req_groupes45=$req_groupes4->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_groupe=$req_groupes45->fetchAll();
        $dechex="FFFFFF"; //couleur par défaut : blanc

        $couleur_des_seances_salle_prof=0;
                //recuperation de la couleur associee au groupe ou à la matiere ou au prof et conversion en rvb
        if ($couleur_des_seances_salle_prof==0) // si couleur des groupes
        {
        foreach($res_groupe as $res_groupes)
        {
        $dechex=dechex($res_groupes['couleurFond']);
        }
        }
        elseif ($couleur_des_seances_salle_prof==1) // si couleur des matieres
        {
        $dechex=dechex($res_seance['couleurFond']);
        }
        elseif ($couleur_des_seances_salle_prof==2) // si couleur des matieres
        {
            $req_profs31=$req_profs3->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_prof=$req_profs31->fetchAll();
        foreach($res_prof as $res_profs)
            {
            $dechex=dechex($res_profs['couleurFond']);
            }
        unset ($res_prof);
        }
        else
        {
        $dechex=dechex($res_groupes['couleurFond']);
        }
                while (strlen($dechex)<6) {

                $dechex = "0".$dechex;

                }



        //creation de la case
            //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins
        //couleur
        $couleur = imagecolorallocate ($im, hexdec(substr($dechex,-2,2)), hexdec(substr($dechex,-4,2)), hexdec(substr($dechex,-6,2)));
        //valeur du rayon 15min
        //$ray=($hauteur-$topheight)/$nbintervalles;
        if ($res_seance['dureeSeance']<=100)
        {
        $ray=3;
        }
        else
        {

        $ray=5;
        }

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$couleur);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
            
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);






                // On compte le nombre d'elements dans la case

                

                // on met deja nbelement egal 1 pour le nom de la matiere

                $nbelements=1;





                // On recherche pour cette séance le ou les groupes associés



                $req_groupes41=$req_groupes4->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_groupes=$req_groupes41->fetchAll();
                    if (count($res_groupes)>=1)
                        {
                        $nbelements+=count($res_groupes);
                        }

                // On compte le nombre de materiels a afficher
                $req_materiels31=$req_materiels3->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_materiel=$req_materiels31->fetchAll();		
                


                if (count($res_materiel))
                    {
                    //on affiche les materiels sur 1 seule ligne donc nbelement +1

                    $nbelements+=1;
                    }
                $nbmateriel=count($res_materiel);	



                        
                // On recherche pour cette séance le ou les profs associées
                $req_profs31=$req_profs3->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_profs3=$req_profs31->fetchAll();
                
                

                if (count($res_profs3))
                    {
                    $nbelements+=count($res_profs3);
                    }
                $nbprof=count($res_profs3);
            

                    // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                                            }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne+1;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }

                    $nbelements+=$compteur_ligne;	

                    }




                // [GD] On affiche le type et la date des seances
        //découpage du nom de l'enseignement afin de récupérer ce qu'il y a entre les 2 premiers _
        $nom_enseignement_afficher_alias=0;          
        if ($nom_enseignement_afficher_alias==1)
        {
        $cursename=explode("_",$res_seance['alias_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['alias_enseignement'];
        }
        }
        else
        {
        $cursename=explode("_",$res_seance['nom_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['nom_enseignement'];
        }
        }
                
            
        //affectation de la couleur au type d'enseignement
                if ($res_seance['codeTypeActivite']==2)

                {

                    $couleur = $couleur_TD;

                }



                elseif ($res_seance['codeTypeActivite']==1)

                {

                    $couleur = $couleur_CR;

                }



                elseif ($res_seance['codeTypeActivite']==3)

                {

                    $couleur = $couleur_TP;

                }



                elseif ($res_seance['codeTypeActivite']==9)

                {

                    $couleur = $couleur_DS;

                }



                elseif ($res_seance['codeTypeActivite']==4)

                {

                    $couleur = $couleur_pro;

                }



                else

                {

                    $couleur = $couleur_defaut;

                }


            unset($req_type);
            $sql="SELECT * FROM types_activites WHERE codeTypeActivite=:type_activite" ;
            $req_type=$dbh->prepare($sql);	
            $req_type=$req_type->execute(array(':type_activite'=>$res_seance['codeTypeActivite']));
            $res_types=$req_type->fetchAll();		
                    
                foreach($res_types as $res_type)
            {
            $text = $res_type['alias']." - ";
            }

                

                // [GD] On affiche les horaires

                $size=imagettfbbox (8 , 0, $font, $text.$horaire_debut." - ".$horaire_fin);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];

            //dessin de la case de l en-tete
            if ($res_seance['dureeSeance']<=100)
        {
        $epaisseur=11;
        }
        else
        {
        $epaisseur=16;
        }
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$couleur);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $couleur,IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $couleur,IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);

                if ($res_seance['dureeSeance']<=100)
        {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 10  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
        }
        else
        {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 12  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
        }	
                



        

        

        

        

        

                //on affiche le nom de la seance

            //dix caracteres ont une longueur de 92px

                $cursename=substr($cursename[1],0,round((($largeur-$leftwidth)/$days)*10/92)-1);

                $size=imagettfbbox (9 , 0, $fontb, $cursename);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];



                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy+$epaisseur) / 2 +12 -10 * ($nbelements/2) , $noir, $fontb, $cursename);

                $position=($bottomy + $topy+$epaisseur) / 2 +11- 10 * ($nbelements/2)+10;



                    // [GD] On affiche les commentaires sur la seance

            

            // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                        

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (7 , 0, $font, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (7 , 0, $font, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $resaname);

                            $position+=10;

                            }

            

                    }





                // [GD] On affiche les groupes concernés

        foreach ($res_groupes as $res_groupe)

                    {

                    if ($res_groupe['nom']!="")

                        {

                    $size=imagettfbbox (8 , 0, $font, substr($res_groupe['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position  , $noir, $font, substr($res_groupe['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                    $position+=10;

                    }

                    }



        foreach ($res_profs3 as $res_profs)

                    {

                    if ($res_profs['nom']!="")

                        {

            

                        $size=imagettfbbox (7 , 0, $font, substr($res_profs['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                        $box_lenght=$size[2]-$size[0];

                        $box_width=$size[1]-$size[7];

                        imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-2  , $noir, $font, substr($res_profs['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                        $position+=8;

                        }



                    }

                        // [GD] On affiche les materiels

                $nbmateriels=0;

                unset($materiels);

                $materiels="";



                foreach ($res_materiel as $res_materiels)

                    {

                    if ($nbmateriels>0)
                        {
                        $materiels.=", ";
                        }
                    $nbmateriels++;
            if ($nom_materiel_afficher_alias==1)
            {
            $materiels .= substr($res_materiels['alias'],0,$nb_caractere_materiel_pour_vue_prof);
            }
            else
            {
            $materiels .= substr($res_materiels['nom'],0,$nb_caractere_materiel_pour_vue_prof);
            }	
                    

                    }

                    
                    //affichage du mot "materiel" devant la liste des materiels
                    if ($affichage_mot_materiel_pour_prof=='1')
                    {
                    if ($nbmateriels==1)
                        {
                        $materiels="Materiel : ".$materiels;
                        }
                    
                    if($nbmateriels>1)
                        {
                        $materiels="Materiels : ".$materiels;
                        }
                    }
                    
                    
                    

                    $size=imagettfbbox (7 , 0, $font, $materiels);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-2, $noir, $font, $materiels);






                }





        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage réservations SALLES              */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/

                

                

                

        if (!$hideprivate) {
        $sql="SELECT * FROM reservations_salles LEFT JOIN (reservations) USING (codeReservation) WHERE reservations_salles.codeRessource=:current_salle AND reservations.dateReservation=:current_day AND reservations.deleted=0  AND reservations_salles.deleted=0 AND diffusable='1'";
        $req_resa3=$dbh->prepare($sql);	
        $req_resa31=$req_resa3->execute(array(':current_day'=>$current_day,':current_salle'=>$current_salle));
        $res_resas=$req_resa31->fetchAll();





                // Pour chaque reservation
            foreach($res_resas as $res_resa)

                {



                    // On convertit l'horaire en %age de la journée



                    /* Explication conversion :



                    On extrait d'une part les minutes et d'autre part l'heure.

                    On transforme les minutes en fraction d'heure.

                    On enlève starttime et on divise par la durée de la journée affichée endtime-starttime.

                    On obtient un %age correspondant à la position du début du cours.

                    Idem pour la durée mais sans enlever 8.15



                    */









                    $start_time=((substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                    $duree=((substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60))/($endtime-$starttime+0.25);







                    $horaire_debut = substr((100+substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)),-2,2)."h".substr($res_resa['heureReservation'],-2,2);

                    $horaire_fin = (substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60) + (substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60);

                    $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe+$nbdeprof)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+$nbdegroupe+$nbdeprof+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche

                    

                

                //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins

        //valeur du rayon 
        if ($res_resa['dureeReservation']<=100)
        {
        $ray=3;
        }
        else
        {

        $ray=5;
        }

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$rdv[1]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$rdv[1]);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
            
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);		



                    

                

                    

                    

                    // [GD] On affiche les horaires
        $text=$horaire_debut." - ".$horaire_fin;
                    $size=imagettfbbox (8 , 0, $font, $text);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];



                    



                    //dessin de la case de l en-tete
                        //dessin de la case de l en-tete
            if ($res_resa['dureeReservation']<=100)
            {
            $epaisseur=11;
            }
            else
            {
            $epaisseur=16;
            }	
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$rdv[4]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$rdv[4]);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $rdv[4],IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $rdv[4],IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);
            if ($res_resa['dureeReservation']<=100)
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2, $topy + 10  , $noir, $font, $text);
            }
            else
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2, $topy + 12  , $noir, $font, $text);
            }
                    



                // comptage des lignes a afficher pour la reservation

                // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_resa['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace+1,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                                            }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }

                    

                    }			

                    

                    

                    

                    //On affiche le titre de la reservation

                    // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    //coordonnees en y de la premiere ligne

                    $position=($bottomy + $topy+$epaisseur) / 2 +10- 10 * ($compteur_ligne/2);

                    

                    

                    if($res_resa['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                        

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (9 , 0, $fontb, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $resaname);

                            $position+=10;

                            }

            

                    }





                }

            }

        }



        }



        }





        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage cours  MATERIELS                 */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/



        //pour chaque bdd

            for ($k=0;$k<=0;$k++)

            {
            
        //preparation des requetes
        $sql="SELECT * FROM ressources_materiels WHERE deleted='0' and codeMateriel=:current_materiel";
        $req_materiel4=$dbh->prepare($sql);

        $sql="SELECT *, seances.dureeSeance, seances.commentaire, matieres.couleurFond,enseignements.nom as nom_enseignement, enseignements.alias as alias_enseignement  FROM seances_materiels LEFT JOIN (seances) ON (seances_materiels.codeSeance=seances.codeSeance) RIGHT JOIN (enseignements) ON (seances.codeEnseignement=enseignements.codeEnseignement)  left join matieres on enseignements.codeMatiere=matieres.codeMatiere WHERE matieres.deleted=0 and seances_materiels.codeRessource=:current_materiel AND seances.dateSeance=:current_day AND seances.deleted=0  AND seances_materiels.deleted=0 ";
        $req_seance4=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_groupes LEFT JOIN ressources_groupes ON (ressources_groupes.codeGroupe=seances_groupes.codeRessource) WHERE codeSeance=:codeSeance AND seances_groupes.deleted=0 order by ressources_groupes.nom";
        $req_groupes5=$dbh->prepare($sql);	

        $sql="SELECT * FROM seances_profs LEFT JOIN ressources_profs ON (ressources_profs.codeProf=seances_profs.codeRessource) WHERE codeSeance=:codeSeance AND seances_profs.deleted=0 AND ressources_profs.deleted=0 order by ressources_profs.nom";
        $req_profs4=$dbh->prepare($sql);

        $sql="SELECT * FROM seances_salles LEFT JOIN ressources_salles ON (ressources_salles.codeSalle=seances_salles.codeRessource) WHERE codeSeance=:codeSeance AND seances_salles.deleted=0 and ressources_salles.deleted='0' order by ressources_salles.nom";
        $req_salles4=$dbh->prepare($sql);




        for ($i=0; $i<count($materiels_multi); $i++)

        {

        $current_materiel= $materiels_multi[$i];




        // Pour les 5 ou 6 jours à afficher, on interroge la DB

        for ($day=0;$day<$days;$day++)

            {



            

        //on affiche le nom du materiel en dessous de la date
                        //seulement pour la derniere base de données car sinon les nom sont écrit les uns sur les autres. De plus comme dans la liste déroulante il n'y a que les noms de la derniere base...
                        
                        $req_materiel4=$req_materiel4->execute(array(':current_materiel'=>$current_materiel));
                $res_materiel4=$req_materiel4->fetchAll();				
                foreach ($res_materiel4 as $res)

            {

            $size=imagettfbbox (8 , 0, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

            $box_lenght=$size[2]-$size[0];

            $box_width=$size[1]-$size[7];

            $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe+$nbdeprof+$nbdesalle)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

            $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+$nbdegroupe+$nbdeprof+$nbdesalle+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , 60  , $noir, $font, substr($res['nom'],0,round((($largeur*$nbressource-$leftwidth)/$days)*10/92)-1));

            }

            

            

            

            $current_day=date('Y-m-d',(strtotime("+".$day." days",$lundi)));



            // On recherche pour le materiel et le jour choisi l'ensemble des séances
            $req_seance4=$req_seance4->execute(array(':current_day'=>$current_day, ':current_materiel'=>$current_materiel));
        $res_seances=$req_seance4->fetchAll();


            // Pour chaque séance
            foreach($res_seances as $res_seance)

                {



                // On convertit l'horaire en %age de la journée



                /* Explication conversion :



                On extrait d'une part les minutes et d'autre part l'heure.

                On transforme les minutes en fraction d'heure.

                On enlève starttime et on divise par la durée de la journée affichée endtime-starttime.

                On obtient un %age correspondant à la position du début du cours.

                Idem pour la durée mais sans enlever 8.15



                */



                





                $start_time=((substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                $duree=((substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60))/($endtime-$starttime+0.25);



                $horaire_debut = substr((100+substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)),-2,2)."h".substr($res_seance['heureSeance'],-2,2);

                $horaire_fin = (substr($res_seance['heureSeance'],-strlen($res_seance['heureSeance']),strlen($res_seance['heureSeance'])-2)+substr($res_seance['heureSeance'],-2,2)/60) + (substr($res_seance['dureeSeance'],-strlen($res_seance['dureeSeance']),strlen($res_seance['dureeSeance'])-2)+substr($res_seance['dureeSeance'],-2,2)/60);

                $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



                // On calcule les coordonnées du rectangle :

                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe+$nbdeprof+$nbdesalle)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+$nbdegroupe+$nbdeprof+$nbdesalle+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche



                $req_groupes5=$req_groupes5->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_groupe=$req_groupes5->fetchAll();
        $dechex="FFFFFF"; //couleur par défaut : blanc
        foreach($res_groupe as $res_groupes)
        {
            $couleur_des_seances_materiel_prof=0;
                //recuperation de la couleur associee au groupe ou à la matiere ou au prof et conversion en rvb
        if ($couleur_des_seances_materiel_prof==0) // si couleur des groupes
        {
        $dechex=dechex($res_groupes['couleurFond']);
        }
        elseif ($couleur_des_seances_materiel_prof==1) // si couleur des matieres
        {
        $dechex=dechex($res_seance['couleurFond']);
        }
        elseif ($couleur_des_seances_materiel_prof==2) // si couleur des matieres
        {
            $req_profs4=$req_profs4->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_prof=$req_profs4->fetchAll();
        foreach($res_prof as $res_profs)
            {
            $dechex=dechex($res_profs['couleurFond']);
            }
        unset ($res_prof);
        }
        else
        {
        $dechex=dechex($res_groupes['couleurFond']);
        }
                while (strlen($dechex)<6) {

                $dechex = "0".$dechex;

                }



        //creation de la case
            //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins
        //couleur
        $couleur = imagecolorallocate ($im, hexdec(substr($dechex,-2,2)), hexdec(substr($dechex,-4,2)), hexdec(substr($dechex,-6,2)));
        //valeur du rayon 15min
        //$ray=($hauteur-$topheight)/$nbintervalles;
        if ($res_seance['dureeSeance']<=100)
        {
        $ray=3;
        }
        else
        {

        $ray=5;
        }

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$couleur);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $couleur);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $couleur);
            
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);


        }



                // On compte le nombre d'elements dans la case

                

                // on met deja nbelement egal 1 pour le nom de la matiere

                $nbelements=1;





                // On recherche pour cette séance le ou les groupes associés



                $req_groupes41= $req_groupes4->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_groupes=$req_groupes41->fetchAll();
                    if (count($res_groupes)>=1)
                        {
                        $nbelements+=count($res_groupes);
                        }
                
                // On compte le nombre de salles a afficher
                $req_salles4=$req_salles4->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_salle=$req_salles4->fetchAll();		
                
                if (count($res_salle))
                    {
                    //on affiche les salles sur 1 seule ligne donc nbelement +1

                    $nbelements+=1;
                    }
                $nbsalle=count($res_salle);



                // On recherche pour cette séance le ou les profs associées
        $req_profs4->execute(array(':codeSeance'=>$res_seance['codeSeance']));
        $res_profs4=$req_profs4->fetchAll();
                
                

                if (count($res_profs4))
                    {
                    $nbelements+=count($res_profs4);
                    }
                $nbprof=count($res_profs4);
            

                    // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                                            }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne+1;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }

                    $nbelements+=$compteur_ligne;	

                    }




                // [GD] On affiche le type et la date des seances
        //découpage du nom de l'enseignement afin de récupérer ce qu'il y a entre les 2 premiers _
        $nom_enseignement_afficher_alias=0;        
        if ($nom_enseignement_afficher_alias==1)
        {
        $cursename=explode("_",$res_seance['alias_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['alias_enseignement'];
        }
        }
        else
        {
        $cursename=explode("_",$res_seance['nom_enseignement']);
        if (!isset($cursename[1]))
        {
        $cursename[1]=$res_seance['nom_enseignement'];
        }
        }
                
            
        //affectation de la couleur au type d'enseignement
                if ($res_seance['codeTypeActivite']==2)

                {

                    $couleur = $couleur_TD;

                }



                elseif ($res_seance['codeTypeActivite']==1)

                {

                    $couleur = $couleur_CR;

                }



                elseif ($res_seance['codeTypeActivite']==3)

                {

                    $couleur = $couleur_TP;

                }



                elseif ($res_seance['codeTypeActivite']==9)

                {

                    $couleur = $couleur_DS;

                }



                elseif ($res_seance['codeTypeActivite']==4)

                {

                    $couleur = $couleur_pro;

                }



                else

                {

                    $couleur = $couleur_defaut;

                }


            unset($req_type);
            $sql="SELECT * FROM types_activites WHERE codeTypeActivite=:type_activite" ;
            $req_type=$dbh->prepare($sql);	
            $req_type=$req_type->execute(array(':type_activite'=>$res_seance['codeTypeActivite']));
            $res_types=$req_type->fetchAll();		
                    
                foreach($res_types as $res_type)
            {
            $text = $res_type['alias']." - ";
            }

                

                // [GD] On affiche les horaires

                $size=imagettfbbox (8 , 0, $font, $text.$horaire_debut." - ".$horaire_fin);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];

            //dessin de la case de l en-tete
            if ($res_seance['dureeSeance']<=100)
        {
        $epaisseur=11;
        }
        else
        {
        $epaisseur=16;
        }
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$couleur);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$couleur);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $couleur,IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $couleur,IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);

                if ($res_seance['dureeSeance']<=100)
        {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 10  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
        }
        else
        {
        imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $topy + 12  , $noir, $font, $text.$horaire_debut." - ".$horaire_fin);
        }	
                


                //on affiche le nom de la seance

            //dix caracteres ont une longueur de 92px

                $cursename=substr($cursename[1],0,round((($largeur-$leftwidth)/$days)*10/92)-1);

                $size=imagettfbbox (9 , 0, $fontb, $cursename);

                $box_lenght=$size[2]-$size[0];

                $box_width=$size[1]-$size[7];



                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , ($bottomy + $topy+$epaisseur) / 2 +12 -10 * ($nbelements/2) , $noir, $fontb, $cursename);

                $position=($bottomy + $topy+$epaisseur) / 2 +11- 10 * ($nbelements/2)+10;



                    // [GD] On affiche les commentaires sur la seance

            

            // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_seance['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    if($res_seance['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_seance['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                        

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (7 , 0, $font, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (7 , 0, $font, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (7 , 0, $font, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $font, $resaname);

                            $position+=10;

                            }

            

                    }





                // [GD] On affiche les groupes concernés

        foreach ($res_groupes as $res_groupe)

                    {

                    if ($res_groupe['nom']!="")

                        {

                    $size=imagettfbbox (8 , 0, $font, substr($res_groupe['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position  , $noir, $font, substr($res_groupe['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                    $position+=10;

                    }

                    }



        foreach ($res_profs4 as $res_profs)

                    {

                    if ($res_profs['nom']!="")

                        {

            

                        $size=imagettfbbox (7 , 0, $font, substr($res_profs['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                        $box_lenght=$size[2]-$size[0];

                        $box_width=$size[1]-$size[7];

                        imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-2  , $noir, $font, substr($res_profs['nom'],0,round((($largeur-$leftwidth)/$days)*10/92)-1));

                        $position+=8;

                        }



                    }


                // [GD] On affiche les salles

                $nbsalles=0;

                unset($salles);

                $salles="";



                foreach ($res_salle as $res_salles)

                    {

                    if ($nbsalles>0)
                        {
                        $salles.=", ";
                        }
                    $nbsalles++;
            if ($nom_salle_afficher_alias==1)
            {
            $salles .= substr($res_salles['alias'],0,$nb_caractere_salle_pour_vue_prof);
            }
            else
            {
            $salles .= substr($res_salles['nom'],0,$nb_caractere_salle_pour_vue_prof);
            }	
                    

                    }

                    
                    //affichage du mot "salle" devant la liste des salles
                    if ($affichage_mot_salle_pour_prof=='1')
                    {
                    if ($nbsalles==1)
                        {
                        $salles="Salle : ".$salles;
                        }
                    
                    if($nbsalles>1)
                        {
                        $salles="Salles : ".$salles;
                        }
                    }
                    
                    
                    

                    $size=imagettfbbox (7 , 0, $font, $salles);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];

                    imagettftext ($im, 7, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position-2, $noir, $font, $salles);






                }


        /*****************************************************************************/

        /*                                                                           */

        /*                                                                           */

        /*                                Affichage réservations MATERIELS           */

        /*                                                                           */

        /*                                                                           */

        /*****************************************************************************/
       

        if (!$hideprivate) {
        $sql="SELECT * FROM reservations_materiels LEFT JOIN (reservations) USING (codeReservation) WHERE reservations_materiels.codeRessource=:current_materiel AND reservations.dateReservation=:current_day AND reservations.deleted=0  AND reservations_materiels.deleted=0 AND diffusable='1'";
        $req_resa4=$dbh->prepare($sql);	
        $req_resa41=$req_resa4->execute(array(':current_day'=>$current_day,':current_materiel'=>$current_materiel));
        $res_resas=$req_resa41->fetchAll();


                // Pour chaque reservation
            foreach($res_resas as $res_resa)

                {



                    // On convertit l'horaire en %age de la journée



                    /* Explication conversion :



                    On extrait d'une part les minutes et d'autre part l'heure.

                    On transforme les minutes en fraction d'heure.

                    On enlève starttime et on divise par la durée de la journée affichée endtime-starttime.

                    On obtient un %age correspondant à la position du début du cours.

                    Idem pour la durée mais sans enlever 8.15



                    */



                    $start_time=((substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60)-$starttime)/($endtime-$starttime+0.25);

                    $duree=((substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60))/($endtime-$starttime+0.25);

                    $horaire_debut = substr((100+substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)),-2,2)."h".substr($res_resa['heureReservation'],-2,2);

                    $horaire_fin = (substr($res_resa['heureReservation'],-strlen($res_resa['heureReservation']),strlen($res_resa['heureReservation'])-2)+substr($res_resa['heureReservation'],-2,2)/60) + (substr($res_resa['dureeReservation'],-strlen($res_resa['dureeReservation']),strlen($res_resa['dureeReservation'])-2)+substr($res_resa['dureeReservation'],-2,2)/60);

                    $horaire_fin = substr(intval($horaire_fin + 100),-2,2)."h".substr(($horaire_fin-intval($horaire_fin))*60+100,-2,2);



                $leftx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * $day +(($i+$nbdegroupe+$nbdeprof+$nbdegroupe)*(($largeur - $leftwidth)*$nbressource) / $days)/$nbressource  ); // Coté gauche

                $rightx = round($leftwidth + (($largeur - $leftwidth)*$nbressource) / $days * ($day + 1)-($nbressource-($i+$nbdegroupe+$nbdeprof+$nbdegroupe+1))*(($largeur - $leftwidth)*$nbressource / $days)/$nbressource); // Coté droit

                $topy = $start_time * ($hauteur - $topheight) + $topheight; // Haut

                $bottomy = ($start_time + $duree) * ($hauteur - $topheight) + $topheight; // Coté gauche

                    

                

                //dessiner un rectangle avec les angles arrondies car gd ne le fait pas de base
        //on dessine 2 rectangle pour faire une croix et on met 4 cercles dans les coins

        //valeur du rayon 
        if ($res_resa['dureeReservation']<=100)
        {
        $ray=3;
        }
        else
        {

        $ray=5;
        }

            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$bottomy-$ray,$rdv[1]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$bottomy,$rdv[1]);

            

            // dessin des 4 cercles

                imagefilledellipse($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2, $rdv[1]);
                imagefilledellipse($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2, $rdv[1]);
            
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $leftx+2+$ray, $bottomy-$ray, $ray*2, $ray*2,90,180, $noir);
                imagearc($im, $rightx-2-$ray, $bottomy-$ray, $ray*2, $ray*2,0,90, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);		


                    // [GD] On affiche les horaires
                $text=$horaire_debut." - ".$horaire_fin;
                    $size=imagettfbbox (8 , 0, $font, $text);

                    $box_lenght=$size[2]-$size[0];

                    $box_width=$size[1]-$size[7];



                    



                    //dessin de la case de l en-tete
                        //dessin de la case de l en-tete
            if ($res_resa['dureeReservation']<=100)
            {
            $epaisseur=11;
            }
            else
            {
            $epaisseur=16;
            }	
            // dessin des 2 rectangles

                imagefilledrectangle($im,$leftx+2,$topy+$ray,$rightx-2,$topy+$epaisseur,$rdv[4]);
                imagefilledrectangle($im,$leftx+2+$ray,$topy,$rightx-2-$ray,$topy+$epaisseur,$rdv[4]);

            // dessin des 2 cercles

                imagefilledarc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $rdv[4],IMG_ARC_EDGED);
                imagefilledarc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $rdv[4],IMG_ARC_EDGED);	
                imagearc($im, $leftx+2+$ray, $topy+$ray, $ray*2, $ray*2,180,270, $noir);
                imagearc($im, $rightx-2-$ray, $topy+$ray, $ray*2, $ray*2,270,0, $noir);
                
                //ajout des traits noir qui manquent autour de la seance
                imageline($im, $leftx+2+$ray, $topy,$rightx-2-$ray, $topy, $noir);
                imageline($im, $leftx+2+$ray, $bottomy,$rightx-2-$ray, $bottomy, $noir);
                imageline($im, $leftx+2, $topy+$ray,$leftx+2, $bottomy-$ray, $noir);
                imageline($im, $rightx-2, $topy+$ray,$rightx-2, $bottomy-$ray, $noir);
                imageline($im, $leftx+2, $topy+$epaisseur,$rightx-2, $topy+$epaisseur, $noir);
            if ($res_resa['dureeReservation']<=100)
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2, $topy + 10  , $noir, $font, $text);
            }
            else
            {
            imagettftext ($im, 8, 0, $leftx + ($rightx - $leftx - $box_lenght)/2, $topy + 12  , $noir, $font, $text);
            }
                    



                // comptage des lignes a afficher pour la reservation

                // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                

                    $compteur_ligne='0';

                    if($res_resa['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                

                                }

                            

                            $compteur_ligne=1;

                            



                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace+1,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    

                                $dernier_espace=2000;

                                $compteur_ligne+=1;

                                                            }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000)

                                    {

                                    $compteur_ligne=$compteur_ligne+1;

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                $compteur_ligne=$compteur_ligne;

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                                        

                                }			

                                }

                            }

                        else					

                            {

                            $compteur_ligne=1;

                            }

                    

                    }			

                    

                    

                    

                    //On affiche le titre de la reservation

                    // on decoupe la chaine commantaire en fonction de la taille de la case et au niveau des espaces

                    //conversion en majuscule

                    $resaname=str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å','î', 'ï', 'ì', 'í', 'ô', 'ö', 'ò', 'ó', 'õ', 'ø','ù', 'û', 'ü', 'ú','é', 'è', 'ê', 'ë','ç', 'ÿ', 'ñ'),array('a', 'a', 'a', 'a', 'a', 'a','i', 'i', 'i', 'i','o', 'o', 'o', 'o', 'o', 'o','u', 'u', 'u', 'u','e', 'e', 'e', 'e','c', 'y', 'n'),$res_resa['commentaire']);

                    $resaname=strtoupper($resaname);

                    

                    //coordonnees en y de la premiere ligne

                    $position=($bottomy + $topy+$epaisseur) / 2 +10- 10 * ($compteur_ligne/2);

                    

                    

                    if($res_resa['commentaire']!="")

                    {

                        if (strlen($resaname)>round((($largeur-$leftwidth)/$days)*10/92))

                            {

                            $chaine2=substr($resaname,0,round((($largeur-$leftwidth)/$days)*10/92));

                            $dernier_espace=strrpos($chaine2," ");

                            if ($dernier_espace=="")

                                {

                                $dernier_espace=round((($largeur-$leftwidth)/$days)*10/92);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                }

                            else

                            {	

                            $chaine2=substr($resaname,0,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                    

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                            }

                                        

                            while ($dernier_espace<strlen($res_resa['commentaire']) )

                                {

                                $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));



                                if (strlen($chaine2)<round((($largeur-$leftwidth)/$days)*10/92))

                                    {

                                    $chaine2=substr($resaname,$dernier_espace);

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                                        

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                $dernier_espace=2000;

                                

                                    }

                                $dernier_espace2=strrpos($chaine2," ");

                                //si mot vraiment trop long pour rentrer dans case on le coupe en deux

                                if ($dernier_espace2=="" && $dernier_espace<1000 )

                                    {

                                    $chaine2=substr($resaname,$dernier_espace,round((($largeur-$leftwidth)/$days)*10/92));

                                    $dernier_espace+=round((($largeur-$leftwidth)/$days)*10/92);

                                    $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                    $box_lenght=$size[2]-$size[0];

                                    

                                    imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                    }

                                elseif ($dernier_espace<1000)

                                {

                                

                                

                                $chaine2=substr($resaname,$dernier_espace,$dernier_espace2);

                                $dernier_espace+= $dernier_espace2;

                                $size=imagettfbbox (9 , 0, $fontb, $chaine2);

                                $box_lenght=$size[2]-$size[0];

                                

                                imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $chaine2);

                                $position+=10;

                                

                                }			

                                }

                            }

                        else

                            {

                            $size=imagettfbbox (9 , 0, $fontb, $resaname);

                            $box_lenght=$size[2]-$size[0];

                            imagettftext ($im, 9, 0, $leftx + ($rightx - $leftx - $box_lenght)/2 , $position , $noir, $fontb, $resaname);

                            $position+=10;

                            }

            

                    }





                }

            }

        }



        }



        }
















        // [GD] On dessine la légende


        //largeur en px de toute la legende : 558 px



        imagestring($im, 10, ($largeur-558)/2, $hauteur+13, utf8_decode('Légende')." des en-tetes :", $noir);
        imagefilledrectangle ($im, ($largeur-558)/2+200, $hauteur+10, ($largeur-558)/2+200+30, $hauteur+30, $couleur_CR);
        imagerectangle($im, ($largeur-558)/2+200, $hauteur+10, ($largeur-558)/2+200+30, $hauteur+29, $noir);
        imagestring($im, 10, ($largeur-558)/2+200+40, $hauteur+13, "Cours", $noir);
        imagefilledrectangle ($im, ($largeur-558)/2+300, $hauteur+10, ($largeur-558)/2+300+30, $hauteur+30, $couleur_TD);
        imagerectangle($im, ($largeur-558)/2+300, $hauteur+10, ($largeur-558)/2+300+30, $hauteur+29, $noir);
        imagestring($im, 10, ($largeur-558)/2+300+40, $hauteur+13, "TD", $noir);
        imagefilledrectangle ($im, ($largeur-558)/2+400, $hauteur+10, ($largeur-558)/2+400+30, $hauteur+30, $couleur_TP);
        imagerectangle($im, ($largeur-558)/2+400, $hauteur+10, ($largeur-558)/2+400+30, $hauteur+29, $noir);
        imagestring($im, 10, ($largeur-558)/2+400+40, $hauteur+13, "TP", $noir);
        imagefilledrectangle ($im, ($largeur-558)/2+500, $hauteur+10, ($largeur-558)/2+500+30, $hauteur+30, $couleur_DS);
        imagerectangle($im, ($largeur-558)/2+500, $hauteur+10, ($largeur-558)/2+500+30, $hauteur+29, $noir);
        imagestring($im, 10, ($largeur-558)/2+500+40, $hauteur+13, "DS", $noir);
        imagefilledrectangle ($im, ($largeur-558)/2+200, $hauteur+35, ($largeur-558)/2+200+30, $hauteur+55, $couleur_pro);
        imagerectangle($im, ($largeur-558)/2+200, $hauteur+35, ($largeur-558)/2+200+30, $hauteur+54, $noir);
        imagestring($im, 10, ($largeur-558)/2+200+40, $hauteur+38, "Projet", $noir);
        imagefilledrectangle ($im, ($largeur-558)/2+300, $hauteur+35, ($largeur-558)/2+300+30, $hauteur+55, $couleur_defaut);
        imagerectangle($im, ($largeur-558)/2+300, $hauteur+35, ($largeur-558)/2+300+30, $hauteur+54, $noir);
        imagestring($im, 10, ($largeur-558)/2+300+40, $hauteur+38, "Autre", $noir);
        imagefilledrectangle ($im, ($largeur-558)/2+400, $hauteur+35, ($largeur-558)/2+400+30, $hauteur+55, $rdv[4]);
        imagerectangle($im, ($largeur-558)/2+400, $hauteur+35, ($largeur-558)/2+400+30, $hauteur+54, $noir);
        imagestring($im, 10, ($largeur-558)/2+400+40, $hauteur+38, utf8_decode('Réservation'), $noir);

        // [GD] On dessine un cadre autour de l'EDT

        imagerectangle ($im, 0, 0, $largeur + ($largeur-$leftwidth)*($nbressource-1)-1, $hauteur-1, $noir);



        // Calcul du temps d'execution du script

        $fin = explode(" ",microtime());

        $fin = $fin[1]+$fin[0];

        $temps_passe = $fin-$debut;



        // [GD] Affichage durée execution

        imagestring($im, 1, ($largeur-94)/2, $hauteur+60,utf8_decode('Généré')." en : ".number_format($temps_passe,3)." s ", $noir);

        $usr= $usr = $security->getUser();
        $file_name = "uploads/img/".$usr->getPersonnel()->getNumPPR().".png";
   		imagepng($im, $file_name);
		//$base64 = base64_encode(imagepng($im));
   		imagedestroy($im);
		$response = new Response($file_name);
		
		$response->headers->set('Content-Type', 'image/png');
		//$response->setStatusCode(200);
	   
		return $response;
        // [GD] Generation de l'image

        //if (!$debug) ImagePng ($im);

    }
   
   


    /**
     * @Route("/emploi", name="emploi")
     * @droitAcces("is_granted('ROLE_ADMIN')")
    */
    public function emploiAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager('edt');
    	$entitiesLength= $em->getRepository(Enseignements::class)->nomGroupe();

        return $this->render('emploi/index.html.twig', array(
                'groupes' => $entitiesLength));
    }

    /**
     * @Route("/etaleremploi", name="etaleremploi")
     * @droitAcces("is_granted('ROLE_ADMIN')")
    */
    public function etaleremploiAction(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager('edt');
        $searchParam = $request->get('emploi');
        extract($searchParam);
        $year = date("Y");

        $dateTime = new \DateTime();
        $dateTime->setISODate($year, $week);
        
        $result['start_date'] = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d');

        $dateStartWeek = $result['start_date']; // First day of week
        $dateStopWeek = $result['end_date']; // Last day of week
        if($groupe=='Tous'){
            $groupe='';
        }
        $resultS_verif= $em->getRepository(Seances::class)->seancesexist($dateStopWeek,$nbr_week,$groupe);
        $rows_verif = count($resultS_verif);

        $r =0;
        $datePer_Start = date( 'Y-m-d',strtotime($dateStopWeek.'+1 days' ));
        $datePer_Stop = date( 'Y-m-d',strtotime($dateStopWeek."+".$nbr_week." week" ));

        $entitiesLength= $em->getRepository(Enseignements::class)->nomGroupe();
        $em1 = $this->getDoctrine()->getManager('default');
        $user = $em1->getRepository(Utilisateurs::class)->findOneBy(array('email' =>'a.elomari@uae.ac.ma'));

        $checkPass = $passwordEncoder->isPasswordValid($user,$password , $user->getSalt()) ; 
        if($checkPass){

            if($rows_verif >0 ){
                foreach ($resultS_verif as  $row_S_verif){
                    $em->getRepository(Seances::class)->seanceupdate($row_S_verif['codeSeance']);
                }
            }

            $resultSeances = $em->getRepository(Seances::class)->seancesexist1($dateStartWeek,$dateStopWeek,$groupe);
            $codeSeance = $em->getRepository(Seances::class)->codeSeanceMax();
            $codeSeance = $codeSeance[0]['codeSeance'];
            $i = 0 ;

            foreach ($resultSeances as $row) {
                $w = 7 ;
                while( $w <= $nbr_week * 7){
                    $actuelDate = date("Y-m-d h:i:s");
                    $codeSeance++;
                    
                    $em->getRepository(Seances::class)->insertSeance($codeSeance,$row,$w,$actuelDate);

                    $em->getRepository(Seances::class)->insertModifications($codeSeance,$actuelDate);

                    $result_S_Grp = $em->getRepository(Seances::class)->seancesGroupe($row['codeSeance']);
                    
                    foreach ($result_S_Grp as $row_S_Grp) {
                        
                        $em->getRepository(Seances::class)->insertseancesGroupe($codeSeance,$row_S_Grp,$actuelDate);
                        
                    }
                    
                    $em->getRepository(Seances::class)->insertseancesHistorique($codeSeance,$row,$w,$actuelDate);

                    $result_S_Prof = $em->getRepository(Seances::class)->seancesProfs($row['codeSeance']);
                    
                    foreach ($result_S_Prof as $row_S_Prof) {

                        $em->getRepository(Seances::class)->insertseancesProfs($codeSeance,$row_S_Prof,$actuelDate);

                    }
                    
                    $result_S_salle = $em->getRepository(Seances::class)->seancesSalles($row['codeSeance']);

                    foreach ($result_S_salle as $row_S_salle) {

                        $em->getRepository(Seances::class)->insertseancesSalles($codeSeance,$row_S_salle,$actuelDate);

                    }
                    
                    $result_user_num_cmd = $em->getRepository(Seances::class)->numeroCommandeMax();
                    $result_user_num_cmd = $result_user_num_cmd[0]['numeroCommande'];
                    $result_user_num_cmd++;
                    
                    $em->getRepository(Seances::class)->insertUserNumCommandes($result_user_num_cmd,$actuelDate);

                    $result_user_act = $em->getRepository(Seances::class)->codeActionMax();
                    $result_user_act = $result_user_act[0]['codeAction'];
                    $result_user_act++;
                    
                    $em->getRepository(Seances::class)->insertUserActions($result_user_act,$actuelDate,$result_user_num_cmd);
                    $r = 1;
                     
                    $w += 7;
                }
            }
        }else{
            
            $this->get('session')->getFlashBag()->add('success', "Il y a ".$rows_verif." seances programées entre ".$datePer_Start ." et ".$datePer_Stop." voulez-vous nettoyer et continuer?");

           return $this->render('emploi/index.html.twig', array(
                    'groupes' => $entitiesLength));
        }
        
        if($r ==1 ){
           $this->get('session')->getFlashBag()->add('success', "Le planning a été dupliqué avec succès");

           return $this->render('emploi/index.html.twig', array(
                    'groupes' => $entitiesLength));
        }else{
            $this->get('session')->getFlashBag()->add('danger', "Il y a des erreurs");
           return $this->render('emploi/index.html.twig', array(
                    'groupes' => $entitiesLength));
        }

            
            
    }

}