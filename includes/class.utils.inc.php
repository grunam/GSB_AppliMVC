<?php
/**
 * Classe d'utilitaires.
 * pour l'application GSB
 * Les fonctions sont toutes statiques,
 *
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
class Utils
{
// use PHPUnit\Framework\TestCase;
//extends TestCase

    /**
     * Teste si un quelconque visiteur est connecté
     *
     * @return Boolean vrai ou faux
     */
    public static function estConnecte()
    {
        return isset($_SESSION['idVisiteur']);
    }

    /**
     * Teste si un quelconque visiteur est un comptable
     *
     * @return Boolean vrai ou faux
     */
    public static function estComptable()
    {
        return $_SESSION['comptable'] == 1;
    }

    /**
     * Teste si un quelconque identifiant passé en paramètre est compris
     * dans la liste des visiteurs passés en paramètres
     *
     * @param String $id          id du visiteur
     * @param Array $lesVisiteurs liste des visiteurs
     *
     * @return Boolean vrai ou faux
     *
     * @assert ('toto', array(array('id'=>'toto'))) == true
     * @assert ("coco", array(array('id'=>'toto'))) == false
     * @assert (2, array(array('id'=>'toto'))) == false
     * @assert ("toto", array(array('id'=>3))) == false
    */
    public static function estVisiteur($id, $lesVisiteurs)
    {
        foreach ($lesVisiteurs as $unVisiteur) {
            if ($unVisiteur["id"] == $id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Enregistre dans une variable session les infos d'un visiteur
     *
     * @param String $idVisiteur id du visiteur
     * @param String $nom        nom du visiteur
     * @param String $prenom     prénom du visiteur
     * @param Boolean $comptable état comptable
     *
     * @return null
     */
    public static function connecter($idVisiteur, $nom, $prenom, $comptable)
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
    public static function deconnecter()
    {
        session_destroy();
    }

    /**
     * Echappe les caractères spéciaux d'une chaîne.

     * Envoie la chaîne $str échappée, càd avec les caractères considérés 
     * spéciaux par MySql (tq la quote simple) précédés d'un \, ce qui annule 
     * leur effet spécial

     * @param String $str chaîne à échapper
     *
     * @return String chaîne avec les antislashes
     *
     * @assert ('&quot;le meilleur&quot; film') == '\"le meilleur\" film'
     */
    public static function filtrerChainePourBdd($str)
    {
        if (!get_magic_quotes_gpc()) {
            $str = addslashes(htmlspecialchars_decode($str, ENT_QUOTES));
        }
        return $str;
    }

    /**
     * Retire les échappements d'une chaîne.

     * Envoie la chaîne $str non échappée, càd avec les caractères considérés 
     * spéciaux par MySql (tq la quote simple) non précédé d'un \, ce qui valide 
     * leur effet spécial

     * @param String $str chaîne échappée
     *
     * @return String chaîne sans les antislashes
     *
     * @assert ('le meilleur \" film') == 'le meilleur " film'
     */
    public static function filtrerChainePourVue($str)
    {
        if (!get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        return $str;
    }
     
    /**
     * Transforme une date au format français jj/mm/aaaa vers le format anglais
     * aaaa-mm-jj
     *
     * @param String $maDate date au format  jj/mm/aaaa
     *
     * @return Date date au format anglais aaaa-mm-jj
     *
     * @assert ('26/02/2018') == '2018-02-26'
     * @assert ('09/11/1999') == '1999-11-09'
     */
    public static function dateFrancaisVersAnglais($maDate)
    {
        @list($jour, $mois, $annee) = explode('/', $maDate);
        return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
    }

    /**
     * Transforme une date au format format anglais aaaa-mm-jj vers le format
     * français jj/mm/aaaa
     *
     * @param String $maDate date au format  aaaa-mm-jj
     *
     * @return Date date au format format français jj/mm/aaaa
     *
     *
     * @assert ('2018-02-26') == '26/02/2018'
     * @assert ('1999-11-09') == '09/11/1999'
     * @assert ('1999') == '//1999'
     * @assert (02) == '//2'
    */
    public static function dateAnglaisVersFrancais($maDate)
    {
        @list($annee, $mois, $jour) = explode('-', $maDate);
        $date = $jour . '/' . $mois . '/' . $annee;
        return $date;
    }

    /**
     * Retourne le mois au format aaaamm selon le jour dans le mois
     *
     * @param String $date date au format  aaaamm
     *
     * @return String mois au format aaaamm
     *
     * @assert ('26/02/2018') == '201802'
     * @assert ('09/11/1999') == '199911'
     */
    public static function getMois($date)
    {
        @list($jour, $mois, $annee) = explode('/', $date);
        unset($jour);
        if (strlen($mois) == 1) {
            $mois = '0' . $mois;
        }
        return $annee . $mois;
    }

    /**
     * Retourne le précédent mois au format aaaamm selon une date
     *
     * @param String $dateMois date au format jj/mm/aaaa
     *
     * @return String mois au format aaaamm
     *
     * @assert ('200701') == '200612'
     * @assert ('201712') == '201711'
     */
    public static function getMoisPrecedent($dateMois)
    {
        $annee = substr($dateMois, 0, 4);
        $mois = substr($dateMois, 4, 2);
        if ($mois == '01') {
            $mois = '12';
            $annee--;
        } else {
            $mois--;
        }
        if (strlen($mois) == 1) {
            $mois = '0' . $mois;
        }
        return $annee . $mois;
    }

    /**
     * Retourne le prochain mois au format aaaamm selon un mois
     *
     * @param String $dateMois mois au format aaaamm
     *
     * @return String mois au format aaaamm
     *
     * @assert ('200701') == '200702'
     * @assert ('200712') == '200801'
     */
    public static function getMoisSuivant($dateMois)
    {
        $annee = substr($dateMois, 0, 4);
        $mois = substr($dateMois, 4, 2);
        if ($mois == '12') {
            $mois = '01';
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
     * Indique si la valeur est un entier positif ou nul
     *
     * @param Integer $valeur valeur
     *
     * @return Boolean vrai ou faux
     *
     * @assert (14.123) == false
     * @assert (1554) == true
     * @assert (-5) == false
     * @assert (-554.544) == false
     * @assert (0) == true
     */
    public static function estEntierPositif($valeur)
    {
        return preg_match('/[^0-9]/', $valeur) == 0;
    }

    /**
     * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
     *
     * @param Array $tabEntiers un tableau d'entier
     *
     * @return Boolean vrai ou faux
     *
     * @assert (array(0, 4, 2, 486, 5164)) == true
     * @assert (array(0, 4, 2, 486, -465)) == false
     * @assert (array(875, 4, 3.45, 4, 1221)) == false
     * @assert (array(0, 4, 2, -54,87, 154)) == false
     */
    public static function estTableauEntiers($tabEntiers)
    {
        $boolReturn = true;
        foreach ($tabEntiers as $unEntier) {
            if (!self::estEntierPositif($unEntier)) {
                $boolReturn = false;
            }
        }
        return $boolReturn;
    }

    /**
     * Vérifie si le jour de la date donnée en paramètre est entre les bornes données en parmètres.
     *
     * @param String $dateTestee date à tester au format  jj/mm/aaaa
     * @param Integeer $jourMin borne inférieur de l'intervalle pour le jour de dateTestee
     * @param Integeer $jourMax borne maximum de l'intervalle pour le jour de dateTestee
     *
     * @return Boolean vrai ou faux
     *
     * @assert ('18/04/2016', 10, 20) == true
     * @assert ('14/08/2017', 15, 20) == false
     * @assert ('20/12/2015', 10, 20) == true
     * @assert ('10/09/2013', 10, 20) == true
     * @assert ('21/07/2006', 10, 20) == false
     * @assert ('09/06/2011', 10, 20) == false
     */
    public static function estJourComprisDansIntervalle($dateTestee, $jourMin, $jourMax)
    {
        @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
        return ($jourMin <= $jourTeste && $jourTeste <= $jourMax);
    }

    /**
     * Vérifie si une date est inférieure d'un an à la date actuelle
     *
     * @param String $dateTestee date à tester au format  jj/mm/aaaa
     *
     * @return Boolean vrai ou faux
     *
     *
     * @assert ('2017-01-18') == true
     * @assert ('2017-02-02') == true
     * @assert ('2017-10-30') == false
     * @assert ('2018-12-18') == false
     */
    public static function estDateDepassee($dateTestee)
    {
        $dateActuelle = date('d/m/Y');
        @list($jour, $mois, $annee) = explode('/', $dateActuelle);
        $annee--;
        $anPasse = $annee . $mois . $jour;
        @list($anneeTeste, $moisTeste, $jourTeste) = explode('-', $dateTestee);
        return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
    }

    /**
     * Vérifie la validité du format d'une date anglaise aaaa-mm-jj
     *
     * @param String $date date à tester
     *
     * @return Boolean vrai ou faux
     *
     * @assert ('2017-10-02') == true
     * @assert ('2015-06-2') == true
     * @assert ('2017/10/30') == false
     * @assert ('2001-4.5-10') == false
     * @assert ('-04-12-20') == false
     * @assert ('zeze-08-02') == false
     */
    public static function estDateValide($date)
    {
        $tabDate = explode('-', $date);
        $dateOK = true;
        if (count($tabDate) != 3) {
            $dateOK = false;
        } else {
            if (!self::estTableauEntiers($tabDate)) {
                $dateOK = false;
            } else {
                //y-m-d => m, d, y
                if (!checkdate($tabDate[1], $tabDate[2], $tabDate[0])) {
                    $dateOK = false;
                }
            }
        }
        return $dateOK;
    }

    /**
     * Vérifie que le tableau de frais ne contient que des valeurs numériques
     *
     * @param Array $lesFrais tableau d'entier
     *
     * @return Boolean vrai ou faux
     *
     * @assert (array(155, 887, 65, 12, 01)) == true
     * @assert (array('zeze', 887, 65, 12, 01)) == false
     * @assert (array(1, 2, 3.5, 6, 88)) == false
     * @assert (array(-4, 2, 18, 21, 13)) == false
     */
    public static function lesQteFraisValides($lesFrais)
    {
        return self::estTableauEntiers($lesFrais);
    }

    /**
     * Vérifie la validité des trois arguments : la date, le libellé du frais
     * et le montant
     *
     * Des message d'erreurs sont ajoutés au tableau des erreurs
     *
     * @param String $dateFrais date des frais
     * @param String $libelle   libellé des frais
     * @param Float  $montant   montant des frais
     *
     * @return null
     *
     *  @backupGlobals enabled
     */
    public static function valideInfosFrais($dateFrais, $libelle, $montant)
    {
        if ($dateFrais == '') {
            self::ajouterErreur('Le champ date ne doit pas être vide.');
        } else {
            if (!self::estDatevalide($dateFrais)) {
                self::ajouterErreur('Date invalide.');
            } else {
                if (self::estDateDepassee($dateFrais)) {
                    self::ajouterErreur(
                        "Date d'enregistrement du frais dépassé, plus de 1 an."
                    );
                }
            }
        }
        if ($libelle == '') {
            self::ajouterErreur('Le champ description ne peut pas être vide.');
        }
        if ($montant == '') {
            self::ajouterErreur('Le champ montant ne peut pas être vide.');
        } elseif (!is_numeric($montant)) {
            self::ajouterErreur('Le champ montant doit être numérique.');
        }
    }
 
    /**
     * Retoune le nombre de lignes du tableau des frais hors forfait
     *
     * @param Array $lesFraisHorsForfait tableau associatif des frais hors forfait
     *        
     * @return Integer le nombre de frais
     *
     * @assert (array(array('refuse'=>1), array('refuse'=>0), array('refuse'=>0), array('refuse'=>1), array('refuse'=>0))) == 3
     * @assert (array(array('refuse'=>0), array('refuse'=>0), array('refuse'=>0), array('refuse'=>0), array('refuse'=>0))) == 5
     * @assert (array(array('refuse'=>1), array('refuse'=>1), array('refuse'=>1), array('refuse'=>1), array('refuse'=>1))) == 0
     */
    public static function nbFraisHorsForfait($lesFraisHorsForfait)
    {
        $nbFraisHorsForfait = 0;
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $refus = $unFraisHorsForfait['refuse'];
            if (!$refus) {
                $nbFraisHorsForfait++;
            }
        }
        return $nbFraisHorsForfait;
    }

    /**
     * Ajoute le libellé d'une erreur au tableau des erreurs
     *
     * @param String $msg libellé de l'erreur
     *
     * @return null
     *
     * @backupGlobals enabled
     */
    public static function ajouterErreur($msg)
    {
        if (!isset($_REQUEST['erreurs'])) {
            $_REQUEST['erreurs'] = array();
        }
        $_REQUEST['erreurs'][] = $msg;
    }

    /**
     * Ajoute le libellé d'un succès au tableau des succès
     *
     * @param String $msg libellé du succès
     *
     * @return null
     *
     * @backupGlobals enabled
     */
    public static function ajouterSucces($msg)
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
     *
     * @backupGlobals enabled
     */
    public static function nbErreurs()
    {
        if (!isset($_REQUEST['erreurs'])) {
            return 0;
        } else {
            return count($_REQUEST['erreurs']);
        }
    }
    
    
    /**
     * Retoune le nombre de lignes du tableau des succès
     *
     * @return Integer le nombre de succès
     *
     * @backupGlobals enabled
     */
    public static function nbSucces()
    {
        if (!isset($_REQUEST['succes'])) {
            return 0;
        } else {
            return count($_REQUEST['succes']);
        }
    }

    /**
     * Retourne la chaine passé en paramètre avec la mention "REFUSE-" au début, 
     * tronque la chaîne si elle est d'une longueur supérieure à 100
     *
     * @param String $string libellé du frais hors forfait
     *
     * @return String la chaîne limitée a 100 caractères avec la mention "REFUSE-" au début
     *
     * @assert ('Conaretur sunt in Gallus conaretur conducentia agitare de milites modum futuris in suae Constantius idem itinera sunt de nequo Gallus mortalem agentes in omnes futuris remoti exarsit incertus incertus nequo') == 'REFUSE-Conaretur sunt in Gallus conaretur conducentia agitare de milites modum futuris in suae Const'
     * @assert ('Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica') == 'REFUSE-Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica'
     * @assert ('On ne change pas une méthode qui marche – ou, en tout cas, qui a marché jusqu’à présent. Telle pourrait être la devise du pouvoir exécutif. Déterminé à engager une réforme en profondeur de la SNCF, il procède comme il l’a fait à l’automne 2017 sur le dossier réputé hautement inflammable du droit du travail,') == 'REFUSE-On ne change pas une méthode qui marche – ou, en tout cas, qui a marché jusqu’à présent. Tell'
     */

    public static function mentionRefuse($string)
    {
        $str = "REFUSE-".self::filtrerChainePourVue($string);
        if (mb_strlen($str) > 100) {
            $str = mb_substr($str, 0, 100);
        }
        return self::filtrerChainePourBdd($str);
    }
}
