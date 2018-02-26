<?php
/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Teste si un quelconque visiteur est connecté
 *
 * @return vrai ou faux
 */
function estConnecte()
{
    return isset($_SESSION['idVisiteur']);
}

/**
 * Teste si un quelconque visiteur est un comptable
 *
 * @return vrai ou faux
 */
function estComptable()
{   
   return $_SESSION['comptable'] == 1;  
}


/**
 * Teste si un quelconque identifiant passé en paramètre est compris dans la liste des visiteurs passés en paramètres
 *
 * @param String $idVisiteur ID du visiteur
 * @param String $nom        Nom du visiteur
 * @param String $prenom     Prénom du visiteur
 * @param Boolean $comptable Etat comptable 
 * 
 * @return vrai ou faux
 */

/**
 * @assert (EE, 0) == 0
 * @assert (0, 1) == 1
 * @assert (1, 0) == 1
 * @assert (1, 1) == 2
 * @assert (1, 2) == 4
 * 
*/
function estVisiteur($val, $lesVisiteurs)
{   
   foreach ($lesVisiteurs as $unVisiteur) {
        if ($unVisiteur["id"] == $val) {
            return true; 
        }    
   } 
   return false;
}




/**
 * Enregistre dans une variable session les infos d'un visiteur
 *
 * @param String $idVisiteur ID du visiteur
 * @param String $nom        Nom du visiteur
 * @param String $prenom     Prénom du visiteur
 * @param Boolean $comptable Etat comptable 
 *
 * @return null
 */
function connecter($idVisiteur, $nom, $prenom, $comptable)
{
    $_SESSION['idVisiteur'] = $idVisiteur;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['comptable'] = $comptable;
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter()
{
    session_destroy();
}

/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais
 * aaaa-mm-jj
 *
 * @param String $maDate au format  jj/mm/aaaa
 *
 * @return Date au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($maDate)
{
    @list($jour, $mois, $annee) = explode('/', $maDate);
    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
}

/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $maDate au format  aaaa-mm-jj
 *
 * @return Date au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate)
{
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = $jour . '/' . $mois . '/' . $annee;
    return $date;
}

/**
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}


/**
 * Retourne l'avant dernier mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMoisPrecedent($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    
    if ($mois == 1) {
        $mois = 12;
    } else {
        $mois--;
    }
    
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    
    
    return $annee . $mois;
}



/**
 * Retourne le prochain mois au format aaaamm selon le jour dans le mois
 *
 * @param String $dateMois au format aaaamm
 *
 * @return String Mois au format aaaamm
 */
function getMoisSuivant($dateMois)
{
    
    $newMois = substr($dateMois, 0, 4).'/'.substr($dateMois, 4, 2);
    //return $newMois;
    
    @list($annee, $mois) = explode('/', $newMois);
    //unset($jour);
    
    if ($mois == 12) {
        $mois = 1;
        $annee++;
    } else {
        $mois++;
    }
    
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    
    
    return $annee . $mois;
}



/* gestion des erreurs */

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur)
{
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers)
{
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}


/**
 * Vérifie si le jour de la date donnée en paramètre est entre les bornes données en parmètre 
 *
 * @param String $dateTestee Date à tester
 * @param Integeer $jourMin Borne inférieur de l'intervalle 
 * @param Integeer $jourMax Borne maximum de l'intervalle
 * @return Boolean vrai ou faux
 */
function estJourComprisDansIntervalle($dateTestee, $jourMin, $jourMax)
{
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($jourMin <= $jourTeste && $jourTeste <= $jourMax);
}


/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee)
{
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date)
{
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            }
        }
    }
    return $dateOK;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques
 *
 * @param Array $lesFrais Tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function lesQteFraisValides($lesFrais)
{
    return estTableauEntiers($lesFrais);
}

/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais
 * et le montant
 *
 * Des message d'erreurs sont ajoutés au tableau des erreurs
 *
 * @param String $dateFrais Date des frais
 * @param String $libelle   Libellé des frais
 * @param Float  $montant   Montant des frais
 *
 * @return null
 */
function valideInfosFrais($dateFrais, $libelle, $montant)
{
    if ($dateFrais == '') {
        ajouterErreur('Le champ date ne doit pas être vide');
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur('Date invalide');
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur(
                    "date d'enregistrement du frais dépassé, plus de 1 an"
                );
            }
        }
    }
    if ($libelle == '') {
        ajouterErreur('Le champ description ne peut pas être vide');
    }
    if ($montant == '') {
        ajouterErreur('Le champ montant ne peut pas être vide');
    } elseif (!is_numeric($montant)) {
        ajouterErreur('Le champ montant doit être numérique');
    }
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function ajouterErreur($msg)
{
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Ajoute le libellé d'un succès au tableau des succès
 *
 * @param String $msg Libellé du succès
 *
 * @return null
 */
function ajouterSucces($msg)
{
    if (!isset($_REQUEST['succes'])) {
        $_REQUEST['succes'] = array();
    }
    $_REQUEST['succes'][] = $msg;
}



/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs()
{
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}



/**
 * Retourne la chaine passé en paramètre avec la mention REFUSE- au début
 * tronque la chaîne à partir de la fin si elle est d'une longueur supérieure à 100
 * 
 * @param String $str Libellé du frais hors forfait
 * 
 * @return String la chaîne limitée a 100 caractères avec la mention REFUSE- 
 * 
 */

function mentionRefuse($string){
    
    $str = "REFUSE-".$string;
    //$str = "REFUSE-"."Conaretur sunt in Gallus conaretur conducentia agitare de milites modum futuris in suae Constantius idem itinera sunt de nequo Gallus mortalem agentes in omnes futuris remoti exarsit incertus incertus nequo incertus omnes incertus ultra cognito milites idem omnes casu perviis agitare quaedam in futuris perviis remoti industria casu futuris Constantius.";
    if (mb_strlen($str,"UTF8") > 100){
      $str = substr($str, -(mb_strlen($str,"UTF8")), -(mb_strlen($str,"UTF8")-100));
    }
    return $str;
}