<?php
/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
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

/*
 use PHPUnit\Framework\TestCase;

class PdoGsbTest extends TestCase
{
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
   
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb';
    private static $mdp = 'secret'; 
    /*
    private static $bdd = 'dbname=wh1l2sdy_gsb_frais';
    private static $user = 'wh1l2sdy_grunam';
    private static $mdp = 'grunam1979';
    */
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur public pour PHPUnit, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    public function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return PdoGsb instance de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login login du visiteur
     * @param String $mdp   mot de passe du visiteur
     *
     * @return Array l'id, le nom et le prénom sous la forme d'un tableau associatif
     * 
     * @assert ('dandre', 'oppg5') == array('id'=>'a17', 'nom'=>'Andre', 'prenom'=>'David', 'comptable'=>'0', 0=>'a17', 1=>'Andre', 2=>'David', 3=>'0')
     * @assert ('cbedos', 'gmhxd') != array('id'=>'a55', 'nom'=>'Bedos', 'prenom'=>'Christian', 'comptable'=>'0', 0=>'a55', 1=>'Bedos', 2=>'Christian', 3=>'1')
     * @assert ('cbedos', 'gmhxd') != array('id'=>'a55', 'nom'=>'Bedos', 'prenom'=>'Christian', 'comptable'=>'0', 0=>'a55', 1=>'Bedos', 2=>'ZOZO', 3=>'0')
     * @assert ('zeze', '99999') == false
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT visiteur.id AS id, '
            . 'visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom, '
            . 'visiteur.comptable AS comptable '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }
    
    /**
     * Teste si un visiteur existe pour un id,
     * passé en paramètre
     *
     * @param String $idVisiteur id du visiteur
     *
     * @return Boolean vrai ou faux
     *
     * @assert ('a17') == true
     * @assert ('a55') == true
     * @assert (1212) == false
     * @assert ('COCO') == false
     */
    public function estUnVisiteur($idVisiteur)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM visiteur '
            . 'WHERE visiteur.id = :unVisiteur'
        );
        $requetePrepare->bindParam(':unVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if ($requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Teste si un frais hors forfait existe pour un id,
     * un visiteur et un mois  passés en paramètres
     *
     * @param String $idFrais    id du frais hors forfait
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Boolean vrai ou faux
     * 
     * @assert ('1', 'a131', '201609') == true
     * @assert ('a54', 'a131', '201712') == false
     * @assert ('496', 'b50', '201609') == true
     * @assert ('497', 'b50', '201610') == false
     */
    public function estUnFraisHorsForfait($idFrais, $idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais '
            . ' AND idvisiteur = :unIdVisiteur '
            . ' AND mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        if ($requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }
    
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux paramètres.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date et libelle -
     *
     * @param String $idVisiteur     id du visiteur
     * @param String $mois           mois sous la forme aaaamm
     * @param Boolean $convertDate   option pour la conversion EN vers FR de la date
     *
     * @return Array tous les champs des lignes de frais hors forfait sous la forme
     *               d'un tableau associatif
     *
     * @assert ('a131', '201701') == array(0=>array('id'=>'20', 'idvisiteur'=>'a131', 'mois'=>'201701', 'libelle'=>'Repas avec praticien', 'date'=>'14/01/2017', 'montant'=>'34.00', 'refuse'=>NULL, 0=>'20', 1=>'a131', 2=>'201701', 3=>'Repas avec praticien', 4=>'2017-01-14', 5=>'34.00', 6=>NULL ))
     * @assert ('a17', '201712', 0) == array( 0=>array('id'=>'1381', 'idvisiteur'=>'a17', 'mois'=>'201712', 'libelle'=>'REFUSE-materiel papeterie', 'date'=>'15/11/2017', 'montant'=>'35.00', 'refuse'=>1, 0=>'1381', 1=>'a17', 2=>'201712', 3=>'REFUSE-materiel papeterie', 4=>'2017-11-15', 5=>'35.00', 6=>1 ), 1=>array('id'=>'1382', 'idvisiteur'=>'a17', 'mois'=>'201712', 'libelle'=>'Location véhicule', 'date'=>'2017-10-04', 'montant'=>'341.00', 'refuse'=>NULL ,0=>'1382', 1=>'a17', 2=>'201712', 3=>'Location véhicule', 4=>'2017-10-04', 5=>'341.00', 6=>NULL ), 2=>array('id'=>'1389', 'idvisiteur'=>'a17', 'mois'=>'201712', 'libelle'=>'restaurant gastronomique', 'date'=>'1999-12-31', 'montant'=>'150.00', 'refuse'=>0, 0=>'1389', 1=>'a17', 2=>'201712', 3=>'restaurant gastronomique', 4=>'1999-12-31', 5=>'150.00', 6=>0 ))
     * @assert ('a131', '201701') != array(0=>array('id'=>'20', 'idvisiteur'=>'a131', 'mois'=>'201701', 'libelle'=>'Repas avec praticien', 'date'=>'14/01/2017', 'montant'=>'34.00', 'refuse'=>NULL, 0=>'20', 1=>'a131', 2=>'201701', 3=>'Repas avec praticien', 4=>'14/01/2017', 5=>'34.00', 6=>NULL ))
     * @assert ('a131', '201701', 0) == array(0=>array('id'=>'20', 'idvisiteur'=>'a131', 'mois'=>'201701', 'libelle'=>'Repas avec praticien', 'date'=>'2017-01-14', 'montant'=>'34.00', 'refuse'=>NULL, 0=>'20', 1=>'a131', 2=>'201701', 3=>'Repas avec praticien', 4=>'2017-01-14', 5=>'34.00', 6=>NULL ))
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois, $convertDate = 1)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        if ($convertDate == 1) {
            for ($i = 0; $i < count($lesLignes); $i++) {
                $date = $lesLignes[$i]['date'];
                $lesLignes[$i]['date'] = Utils::dateAnglaisVersFrancais($date);
                $libelle = $lesLignes[$i]['libelle'];
                $lesLignes[$i]['libelle'] = Utils::filtrerChainePourVue($libelle);
            }
        } elseif ($convertDate == 0) {
            for ($i = 0; $i < count($lesLignes); $i++) {
                $date = $lesLignes[$i]['date'];
                $refus = $lesLignes[$i]['refuse'];
                if ($refus == 1) {
                    $lesLignes[$i]['date'] = Utils::dateAnglaisVersFrancais($date);
                }
                $libelle = $lesLignes[$i]['libelle'];
                $lesLignes[$i]['libelle'] = Utils::filtrerChainePourVue($libelle);
            }
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un id du visiteur et un mois donnés
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Integer le nombre entier de justificatifs
     *
     * @assert ('a131', '201701') == '9'
     * @assert ('ZOZO', '201701') == null
     * @assert ('a131', 'lklmkmkm') == null
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux paramètres
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Array l'id, le libelle et la quantité sous la forme d'un tableau
     *               associatif
     *
     * @assert ('a131', '201609') == array(0=>array('idfrais'=>'ETP', 'libelle'=>'Forfait Etape', 'quantite'=>'11',  0=>'ETP', 1=>'Forfait Etape', 2=>'11'), 1=>array('idfrais'=>'KM', 'libelle'=>'Frais Kilométrique', 'quantite'=>'847', 0=>'KM', 1=>'Frais Kilométrique', 2=>'847'), 2=>array('idfrais'=>'NUI', 'libelle'=>'Nuitée Hôtel', 'quantite'=>'13', 0=>'NUI', 1=>'Nuitée Hôtel',  2=>'13'), 3=>array('idfrais'=>'REP', 'libelle'=>'Repas Restaurant', 'quantite'=>'12', 0=>'REP', 1=>'Repas Restaurant', 2=>'12'))
     * @assert ('zozo66464', '201609') == array()
     * @assert ('a131', '300040') == array()
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id des frais forfaitaires
     *
     * @return Array un tableau associatif
     *
     * @assert () == array(array('idfrais'=>'ETP', 0=>'ETP'), array('idfrais'=>'KM', 0=>'KM'), array('idfrais'=>'NUI', 0=>'NUI'), array('idfrais'=>'REP', 0=>'REP'))
     * @assert () != NULL
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait pour un visiteur,
     * un mois et les frais donnés
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour la table ligneFraisHorsForfait pour les frais
     * donnés
     *
     * @param Integer $id       id du frais hors forfait
     * @param Array $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur pour ce frais
     *
     * @return null
     */
    public function majFraisHorsForfait($lesFrais)
    {
        foreach ($lesFrais as $unFrais) {
            $libelleFiltre = Utils::filtrerChainePourBdd(Utils::filtrerChainePourVue($unFrais['libelle']));
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraishorsforfait '
                . 'SET lignefraishorsforfait.libelle = :unLibelle, '
                . 'lignefraishorsforfait.date = :uneDate, '
                . 'lignefraishorsforfait.montant = :unMontant '
                . 'WHERE lignefraishorsforfait.id = :idFrais'
            );
            $requetePrepare->bindParam(':unLibelle', $libelleFiltre, PDO::PARAM_INT);
            $requetePrepare->bindParam(':uneDate', $unFrais['date'], PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMontant', $unFrais['montant'], PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unFrais['id'], PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Reporte un frais de ligneFraisHorsForfait dont l'id
     * avec le mois sont  passés en paramètres
     *
     * @param String $id    id du frais hors forfait
     * @param String $mois  mois sous la forme aaaamm
     *
     * @return null
     */
    public function reporterUnFraisHorsForfait($id, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.mois = :unMois '
            . 'WHERE lignefraishorsforfait.id = :unId '
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Met à jour unfrais de ligneFraisHorsForfait, dont l'id
     * est passé en paramètre, avec le nouveau libelle introduit par 'REFUSE-'
     * et le champ refuse changé à true
     *
     * @param String $id id du frais hors forfait
     * @param String $libelle libelle du frais hors forfait
     *
     * @return null
     */
    public function refuserUnFraisHorsForfait($id, $libelle)
    {
        $libelleFiltre = Utils::filtrerChainePourBdd(Utils::filtrerChainePourVue($libelle));
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.libelle = :unLibelle, '
            . 'lignefraishorsforfait.refuse = 1 '
            . 'WHERE lignefraishorsforfait.id = :unId '
        );
        $requetePrepare->bindParam(':unLibelle', $libelleFiltre, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_INT);
        $requetePrepare->execute();
    }
    
    /**
     * Met à jour le nombre de justificatifs de la table fichefrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      id du visiteur
     * @param String  $mois            mois sous la forme aaaamm
     * @param Integer $nbJustificatifs nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais
     * pour le mois et l'id passés en paramètres
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Boolean vrai ou faux
     *
     * @assert ('a131', '202501') == true
     * @assert ('a131', '201612') == false
     * @assert ('ZOZO', '202501') == true
     * @assert ('ZOZO', '201612') == true
     * @assert ('a131', 'mon année est mon mois') == true
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois saisi
     * d'une fiche de frais pour un visiteur donné
     *
     * @param String $idVisiteur id du visiteur
     *
     * @return String le mois sous la forme aaaamm
     *
     * @asset ('f21') == '201710'
     * @asset ('a131') != '201710'
     * @asset ('ZOZO') == NULL
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }
    
    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($mois > $dernierMois && $mois >= Utils::getMois(date('d/m/Y'))) {
            $idEtat = 'CR';
        } else {
            $idEtat = 'CL';
        }
        if ($laDerniereFiche['idEtat'] == 'CR' &&  $mois > $dernierMois) {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),:unEtat)"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unEtat', $idEtat, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur et un mois donnés
     * à partir des informations fournies en paramètres
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     * @param String $libelle    libellé du frais
     * @param String $date       date du frais au format français jj/mm/aaaa
     * @param Float  $montant    montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $libelleFiltre = Utils::filtrerChainePourBdd(Utils::filtrerChainePourVue($libelle));
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDate,'
            . ':unMontant, null) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelleFiltre, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDate', $date, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en paramètre
     *
     * @param String $idFrais id du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Refuse un ou des frais hors forfait dont l'id est ou les ids sont passé(s) en paramètre(s)
     *
     * @param Array $lesFrais tableau associatif de clé idFrais
     *
     * @return null
     */
    public function refuserLesFraisHorsForfait($lesFrais)
    {
        foreach ($lesFrais as $unIdFrais) {
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
            );
            $requetePrepare->bindParam(':unIdFrais', $unIdFrais, PDO::PARAM_INT);
            $requetePrepare->execute();
            $laLigne = $requetePrepare->fetch();
            if ($laLigne['refuse']!= 1) {
                $this->refuserUnFraisHorsForfait($laLigne['id'], Utils::mentionRefuse($laLigne['libelle']));
            }
        }
    }
    
    /**
     * Reporte le frais hors forfait dont l'id est ou les ids
     * sont passé(s) en paramètre(s) vers la fiche du mois suivant,
     * créé la fiche si elle n'existe pas.
     *
     * @param Array $lesFrais tableau associatif de clé idFrais
     * @param String $mois    mois sous la forme aaaamm
     *
     * @return null
     */
    public function reporterLesFraisHorsForfait($lesFrais, $mois)
    {
        foreach ($lesFrais as $unIdFrais) {
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
            );
            $requetePrepare->bindParam(':unIdFrais', $unIdFrais, PDO::PARAM_INT);
            $requetePrepare->execute();
            $laLigne = $requetePrepare->fetch();
            if ($laLigne['refuse'] != 1) {
                if ($this->estPremierFraisMois($laLigne['idvisiteur'], $mois)) {               
                    $this->creeNouvellesLignesFrais($laLigne['idvisiteur'], $mois);
                    $this->reporterUnFraisHorsForfait($laLigne['id'], $mois); 
                } else {
                     $this->reporterUnFraisHorsForfait($laLigne['id'], $mois);
                }
            }
        }
    }
    
    /**
     * Retourne les visiteurs médicaux
     *
     * @return Array un tableau associatif avec les clés et valeurs contenant le nom,
     *               prénom, adresse, ville et code postal
     *
     * @assert () == Array (0 => Array ('id' => 'a17', 0 => 'a17', 'nom' => 'Andre', 1 => 'Andre', 'prenom' => 'David', 2 => 'David', 'adresse' => '1 rue Petit', 3 => '1 rue Petit', 'ville' => 'Lalbenque', 4 => 'Lalbenque', 'cp' => '46200', 5 => '46200' ), 1 => Array ('id' => 'a55', 0 => 'a55', 'nom' => 'Bedos', 1 => 'Bedos', 'prenom' => 'Christian', 2 => 'Christian', 'adresse' => '1 rue Peranud', 3 => '1 rue Peranud', 'ville' => 'Montcuq', 4 => 'Montcuq', 'cp' => '46250', 5 => '46250'), 2 => Array ('id' => 'b13', 0 => 'b13', 'nom' => 'Bentot', 1 => 'Bentot', 'prenom' => 'Pascal', 2 => 'Pascal', 'adresse' => '11 allée des Cerises', 3 => '11 allée des Cerises', 'ville' => 'Bessines', 4 => 'Bessines', 'cp' => '46512', 5 => '46512' ), 3 => Array ('id' => 'b16', 0 => 'b16', 'nom' => 'Bioret', 1 => 'Bioret', 'prenom' => 'Luc', 2 => 'Luc', 'adresse' => '1 Avenue gambetta', 3 => '1 Avenue gambetta', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ), 4 => Array ('id' => 'b25', 0 => 'b25', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Denise', 2 => 'Denise', 'adresse' => '23 rue Manin', 3 => '23 rue Manin', 'ville' => 'paris', 4 => 'paris', 'cp' => '75019', 5 => '75019' ), 5 => Array ('id' => 'b19', 0 => 'b19', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Francis', 2 => 'Francis', 'adresse' => '10 rue des Perles', 3 => '10 rue des Perles', 'ville' => 'Montreuil', 4 => 'Montreuil', 'cp' => '93100', 5 => '93100' ), 6 => Array ('id' => 'b28', 0 => 'b28', 'nom' => 'Cacheux', 1 => 'Cacheux', 'prenom' => 'Bernard', 2 => 'Bernard', 'adresse' => '114 rue Blanche', 3 => '114 rue Blanche', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75017', 5 => '75017' ), 7 => Array ('id' => 'b34', 0 => 'b34', 'nom' => 'Cadic', 1 => 'Cadic', 'prenom' => 'Eric', 2 => 'Eric', 'adresse' => '123 avenue de la République', 3 => '123 avenue de la République', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75011', 5 => '75011' ), 8 => Array ('id' => 'b4', 0 => 'b4', 'nom' => 'Charoze', 1 => 'Charoze', 'prenom' => 'Catherine', 2 => 'Catherine', 'adresse' => '100 rue Petit', 3 => '100 rue Petit', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75019', 5 => '75019' ), 9 => Array ('id' => 'b50', 0 => 'b50', 'nom' => 'Clepkens', 1 => 'Clepkens', 'prenom' => 'Christophe', 2 => 'Christophe', 'adresse' => '12 allée des Anges', 3 => '12 allée des Anges', 'ville' => 'Romainville', 4 => 'Romainville', 'cp' => '93230', 5 => '93230' ), 10 => Array ('id' => 'b59', 0 => 'b59', 'nom' => 'Cottin', 1 => 'Cottin', 'prenom' => 'Vincenne', 2 => 'Vincenne', 'adresse' => '36 rue Des Roches', 3 => '36 rue Des Roches', 'ville' => 'Monteuil', 4 => 'Monteuil', 'cp' => '93100', 5 => '93100' ), 11 => Array ('id' => 'c14', 0 => 'c14', 'nom' => 'Daburon', 1 => 'Daburon', 'prenom' => 'François', 2 => 'François', 'adresse' => '13 rue de Chanzy', 3 => '13 rue de Chanzy', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 12 => Array ('id' => 'c3', 0 => 'c3', 'nom' => 'De', 1 => 'De', 'prenom' => 'Philippe', 2 => 'Philippe', 'adresse' => '13 rue Barthes', 3 => '13 rue Barthes', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 13 => Array ('id' => 'd13', 0 => 'd13', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Jeanne', 2 => 'Jeanne', 'adresse' => '134 allée des Joncs', 3 => '134 allée des Joncs', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 14 => Array ('id' => 'c54', 0 => 'c54', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '181 avenue Barbusse', 3 => '181 avenue Barbusse', 'ville' => 'Rosny', 4 => 'Rosny', 'cp' => '93210', 5 => '93210' ), 15 => Array ('id' => 'd51', 0 => 'd51', 'nom' => 'Debroise', 1 => 'Debroise', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '2 Bld Jourdain', 3 => '2 Bld Jourdain', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 16 => Array ('id' => 'e22', 0 => 'e22', 'nom' => 'Desmarquest', 1 => 'Desmarquest', 'prenom' => 'Nathalie', 2 => 'Nathalie', 'adresse' => '14 Place d Arc', 3 => '14 Place d Arc', 'ville' => 'Orléans', 4 => 'Orléans', 'cp' => '45000', 5 => '45000' ), 17 => Array ('id' => 'e24', 0 => 'e24', 'nom' => 'Desnost', 1 => 'Desnost', 'prenom' => 'Pierre', 2 => 'Pierre', 'adresse' => '16 avenue des Cèdres', 3 => '16 avenue des Cèdres', 'ville' => 'Guéret', 4 => 'Guéret', 'cp' => '23200', 5 => '23200' ), 18 => Array ('id' => 'e39', 0 => 'e39', 'nom' => 'Dudouit', 1 => 'Dudouit', 'prenom' => 'Frédéric', 2 => 'Frédéric', 'adresse' => '18 rue de l église', 3 => '18 rue de l église', 'ville' => 'GrandBourg', 4 => 'GrandBourg', 'cp' => '23120', 5 => '23120' ), 19 => Array ('id' => 'e49', 0 => 'e49', 'nom' => 'Duncombe', 1 => 'Duncombe', 'prenom' => 'Claude', 2 => 'Claude', 'adresse' => '19 rue de la tour', 3 => '19 rue de la tour', 'ville' => 'La souteraine', 4 => 'La souteraine', 'cp' => '23100', 5 => '23100' ), 20 => Array ('id' => 'e5', 0 => 'e5', 'nom' => 'Enault-Pascreau', 1 => 'Enault-Pascreau', 'prenom' => 'Céline', 2 => 'Céline', 'adresse' => '25 place de la gare', 3 => '25 place de la gare', 'ville' => 'Gueret', 4 => 'Gueret', 'cp' => '23200', 5 => '23200' ), 21 => Array ('id' => 'e52', 0 => 'e52', 'nom' => 'Eynde', 1 => 'Eynde', 'prenom' => 'Valérie', 2 => 'Valérie', 'adresse' => '3 Grand Place', 3 => '3 Grand Place', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13015', 5 => '13015' ), 22 => Array ('id' => 'f21', 0 => 'f21', 'nom' => 'Finck', 1 => 'Finck', 'prenom' => 'Jacques', 2 => 'Jacques', 'adresse' => '10 avenue du Prado', 3 => '10 avenue du Prado', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13002', 5 => '13002' ), 23 => Array ('id' => 'f39', 0 => 'f39', 'nom' => 'Frémont', 1 => 'Frémont', 'prenom' => 'Fernande', 2 => 'Fernande', 'adresse' => '4 route de la mer', 3 => '4 route de la mer', 'ville' => 'Allauh', 4 => 'Allauh', 'cp' => '13012', 5 => '13012' ), 24 => Array ('id' => 'f4', 0 => 'f4', 'nom' => 'Gest', 1 => 'Gest', 'prenom' => 'Alain', 2 => 'Alain', 'adresse' => '30 avenue de la mer', 3 => '30 avenue de la mer', 'ville' => 'Berre', 4 => 'Berre', 'cp' => '13025', 5 => '13025' ), 25 => Array ('id' => 'a93', 0 => 'a93', 'nom' => 'Tusseau', 1 => 'Tusseau', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '22 rue des Ternes', 3 => '22 rue des Ternes', 'ville' => 'Gramat', 4 => 'Gramat', 'cp' => '46123', 5 => '46123' ), 26 => Array ('id' => 'a131', 0 => 'a131', 'nom' => 'Villechalane', 1 => 'Villechalane', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '8 rue des Charmes', 3 => '8 rue des Charmes', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ) )
     * @assert () != Array (0 => Array ('id' => 'ZOZO', 0 => 'a17', 'nom' => 'Andre', 1 => 'Andre', 'prenom' => 'David', 2 => 'David', 'adresse' => '1 rue Petit', 3 => '1 rue Petit', 'ville' => 'Lalbenque', 4 => 'Lalbenque', 'cp' => '46200', 5 => '46200' ), 1 => Array ('id' => 'a55', 0 => 'a55', 'nom' => 'Bedos', 1 => 'Bedos', 'prenom' => 'Christian', 2 => 'Christian', 'adresse' => '1 rue Peranud', 3 => '1 rue Peranud', 'ville' => 'Montcuq', 4 => 'Montcuq', 'cp' => '46250', 5 => '46250'), 2 => Array ('id' => 'b13', 0 => 'b13', 'nom' => 'Bentot', 1 => 'Bentot', 'prenom' => 'Pascal', 2 => 'Pascal', 'adresse' => '11 allée des Cerises', 3 => '11 allée des Cerises', 'ville' => 'Bessines', 4 => 'Bessines', 'cp' => '46512', 5 => '46512' ), 3 => Array ('id' => 'b16', 0 => 'b16', 'nom' => 'Bioret', 1 => 'Bioret', 'prenom' => 'Luc', 2 => 'Luc', 'adresse' => '1 Avenue gambetta', 3 => '1 Avenue gambetta', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ), 4 => Array ('id' => 'b25', 0 => 'b25', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Denise', 2 => 'Denise', 'adresse' => '23 rue Manin', 3 => '23 rue Manin', 'ville' => 'paris', 4 => 'paris', 'cp' => '75019', 5 => '75019' ), 5 => Array ('id' => 'b19', 0 => 'b19', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Francis', 2 => 'Francis', 'adresse' => '10 rue des Perles', 3 => '10 rue des Perles', 'ville' => 'Montreuil', 4 => 'Montreuil', 'cp' => '93100', 5 => '93100' ), 6 => Array ('id' => 'b28', 0 => 'b28', 'nom' => 'Cacheux', 1 => 'Cacheux', 'prenom' => 'Bernard', 2 => 'Bernard', 'adresse' => '114 rue Blanche', 3 => '114 rue Blanche', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75017', 5 => '75017' ), 7 => Array ('id' => 'b34', 0 => 'b34', 'nom' => 'Cadic', 1 => 'Cadic', 'prenom' => 'Eric', 2 => 'Eric', 'adresse' => '123 avenue de la République', 3 => '123 avenue de la République', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75011', 5 => '75011' ), 8 => Array ('id' => 'b4', 0 => 'b4', 'nom' => 'Charoze', 1 => 'Charoze', 'prenom' => 'Catherine', 2 => 'Catherine', 'adresse' => '100 rue Petit', 3 => '100 rue Petit', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75019', 5 => '75019' ), 9 => Array ('id' => 'b50', 0 => 'b50', 'nom' => 'Clepkens', 1 => 'Clepkens', 'prenom' => 'Christophe', 2 => 'Christophe', 'adresse' => '12 allée des Anges', 3 => '12 allée des Anges', 'ville' => 'Romainville', 4 => 'Romainville', 'cp' => '93230', 5 => '93230' ), 10 => Array ('id' => 'b59', 0 => 'b59', 'nom' => 'Cottin', 1 => 'Cottin', 'prenom' => 'Vincenne', 2 => 'Vincenne', 'adresse' => '36 rue Des Roches', 3 => '36 rue Des Roches', 'ville' => 'Monteuil', 4 => 'Monteuil', 'cp' => '93100', 5 => '93100' ), 11 => Array ('id' => 'c14', 0 => 'c14', 'nom' => 'Daburon', 1 => 'Daburon', 'prenom' => 'François', 2 => 'François', 'adresse' => '13 rue de Chanzy', 3 => '13 rue de Chanzy', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 12 => Array ('id' => 'c3', 0 => 'c3', 'nom' => 'De', 1 => 'De', 'prenom' => 'Philippe', 2 => 'Philippe', 'adresse' => '13 rue Barthes', 3 => '13 rue Barthes', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 13 => Array ('id' => 'd13', 0 => 'd13', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Jeanne', 2 => 'Jeanne', 'adresse' => '134 allée des Joncs', 3 => '134 allée des Joncs', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 14 => Array ('id' => 'c54', 0 => 'c54', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '181 avenue Barbusse', 3 => '181 avenue Barbusse', 'ville' => 'Rosny', 4 => 'Rosny', 'cp' => '93210', 5 => '93210' ), 15 => Array ('id' => 'd51', 0 => 'd51', 'nom' => 'Debroise', 1 => 'Debroise', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '2 Bld Jourdain', 3 => '2 Bld Jourdain', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 16 => Array ('id' => 'e22', 0 => 'e22', 'nom' => 'Desmarquest', 1 => 'Desmarquest', 'prenom' => 'Nathalie', 2 => 'Nathalie', 'adresse' => '14 Place d Arc', 3 => '14 Place d Arc', 'ville' => 'Orléans', 4 => 'Orléans', 'cp' => '45000', 5 => '45000' ), 17 => Array ('id' => 'e24', 0 => 'e24', 'nom' => 'Desnost', 1 => 'Desnost', 'prenom' => 'Pierre', 2 => 'Pierre', 'adresse' => '16 avenue des Cèdres', 3 => '16 avenue des Cèdres', 'ville' => 'Guéret', 4 => 'Guéret', 'cp' => '23200', 5 => '23200' ), 18 => Array ('id' => 'e39', 0 => 'e39', 'nom' => 'Dudouit', 1 => 'Dudouit', 'prenom' => 'Frédéric', 2 => 'Frédéric', 'adresse' => '18 rue de l église', 3 => '18 rue de l église', 'ville' => 'GrandBourg', 4 => 'GrandBourg', 'cp' => '23120', 5 => '23120' ), 19 => Array ('id' => 'e49', 0 => 'e49', 'nom' => 'Duncombe', 1 => 'Duncombe', 'prenom' => 'Claude', 2 => 'Claude', 'adresse' => '19 rue de la tour', 3 => '19 rue de la tour', 'ville' => 'La souteraine', 4 => 'La souteraine', 'cp' => '23100', 5 => '23100' ), 20 => Array ('id' => 'e5', 0 => 'e5', 'nom' => 'Enault-Pascreau', 1 => 'Enault-Pascreau', 'prenom' => 'Céline', 2 => 'Céline', 'adresse' => '25 place de la gare', 3 => '25 place de la gare', 'ville' => 'Gueret', 4 => 'Gueret', 'cp' => '23200', 5 => '23200' ), 21 => Array ('id' => 'e52', 0 => 'e52', 'nom' => 'Eynde', 1 => 'Eynde', 'prenom' => 'Valérie', 2 => 'Valérie', 'adresse' => '3 Grand Place', 3 => '3 Grand Place', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13015', 5 => '13015' ), 22 => Array ('id' => 'f21', 0 => 'f21', 'nom' => 'Finck', 1 => 'Finck', 'prenom' => 'Jacques', 2 => 'Jacques', 'adresse' => '10 avenue du Prado', 3 => '10 avenue du Prado', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13002', 5 => '13002' ), 23 => Array ('id' => 'f39', 0 => 'f39', 'nom' => 'Frémont', 1 => 'Frémont', 'prenom' => 'Fernande', 2 => 'Fernande', 'adresse' => '4 route de la mer', 3 => '4 route de la mer', 'ville' => 'Allauh', 4 => 'Allauh', 'cp' => '13012', 5 => '13012' ), 24 => Array ('id' => 'f4', 0 => 'f4', 'nom' => 'Gest', 1 => 'Gest', 'prenom' => 'Alain', 2 => 'Alain', 'adresse' => '30 avenue de la mer', 3 => '30 avenue de la mer', 'ville' => 'Berre', 4 => 'Berre', 'cp' => '13025', 5 => '13025' ), 25 => Array ('id' => 'a93', 0 => 'a93', 'nom' => 'Tusseau', 1 => 'Tusseau', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '22 rue des Ternes', 3 => '22 rue des Ternes', 'ville' => 'Gramat', 4 => 'Gramat', 'cp' => '46123', 5 => '46123' ), 26 => Array ('id' => 'a131', 0 => 'a131', 'nom' => 'Villechalane', 1 => 'Villechalane', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '8 rue des Charmes', 3 => '8 rue des Charmes', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ) )
     * @assert () != null
     */
    public function getLesVisiteurs()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT visiteur.id AS id, '
            . 'visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom, '
            . 'visiteur.adresse AS adresse, '
            . 'visiteur.ville AS ville, '
            . 'visiteur.cp AS cp '
            . 'FROM visiteur '
            . 'WHERE visiteur.comptable = 0 '
            . 'ORDER BY visiteur.nom, visiteur.prenom asc'
        );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetchAll();
        return $laLigne;
    }
    
    /**
     * Retourne les visiteurs médicaux avec des fiches de frais dont l'état est cloturé
     *
     * @return Array un tableau associatif avec les clés et valeurs contenant le nom,
     *               prénom, adresse, ville et code postal
     *
     * @assert () == Array (0 => Array ('id' => 'a17', 0 => 'a17', 'nom' => 'Andre', 1 => 'Andre', 'prenom' => 'David', 2 => 'David', 'adresse' => '1 rue Petit', 3 => '1 rue Petit', 'ville' => 'Lalbenque', 4 => 'Lalbenque', 'cp' => '46200', 5 => '46200' ), 1 => Array ('id' => 'a55', 0 => 'a55', 'nom' => 'Bedos', 1 => 'Bedos', 'prenom' => 'Christian', 2 => 'Christian', 'adresse' => '1 rue Peranud', 3 => '1 rue Peranud', 'ville' => 'Montcuq', 4 => 'Montcuq', 'cp' => '46250', 5 => '46250'), 2 => Array ('id' => 'b13', 0 => 'b13', 'nom' => 'Bentot', 1 => 'Bentot', 'prenom' => 'Pascal', 2 => 'Pascal', 'adresse' => '11 allée des Cerises', 3 => '11 allée des Cerises', 'ville' => 'Bessines', 4 => 'Bessines', 'cp' => '46512', 5 => '46512' ), 3 => Array ('id' => 'b16', 0 => 'b16', 'nom' => 'Bioret', 1 => 'Bioret', 'prenom' => 'Luc', 2 => 'Luc', 'adresse' => '1 Avenue gambetta', 3 => '1 Avenue gambetta', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ), 4 => Array ('id' => 'b25', 0 => 'b25', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Denise', 2 => 'Denise', 'adresse' => '23 rue Manin', 3 => '23 rue Manin', 'ville' => 'paris', 4 => 'paris', 'cp' => '75019', 5 => '75019' ), 5 => Array ('id' => 'b19', 0 => 'b19', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Francis', 2 => 'Francis', 'adresse' => '10 rue des Perles', 3 => '10 rue des Perles', 'ville' => 'Montreuil', 4 => 'Montreuil', 'cp' => '93100', 5 => '93100' ), 6 => Array ('id' => 'b28', 0 => 'b28', 'nom' => 'Cacheux', 1 => 'Cacheux', 'prenom' => 'Bernard', 2 => 'Bernard', 'adresse' => '114 rue Blanche', 3 => '114 rue Blanche', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75017', 5 => '75017' ), 7 => Array ('id' => 'b34', 0 => 'b34', 'nom' => 'Cadic', 1 => 'Cadic', 'prenom' => 'Eric', 2 => 'Eric', 'adresse' => '123 avenue de la République', 3 => '123 avenue de la République', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75011', 5 => '75011' ), 8 => Array ('id' => 'b4', 0 => 'b4', 'nom' => 'Charoze', 1 => 'Charoze', 'prenom' => 'Catherine', 2 => 'Catherine', 'adresse' => '100 rue Petit', 3 => '100 rue Petit', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75019', 5 => '75019' ), 9 => Array ('id' => 'b50', 0 => 'b50', 'nom' => 'Clepkens', 1 => 'Clepkens', 'prenom' => 'Christophe', 2 => 'Christophe', 'adresse' => '12 allée des Anges', 3 => '12 allée des Anges', 'ville' => 'Romainville', 4 => 'Romainville', 'cp' => '93230', 5 => '93230' ), 10 => Array ('id' => 'b59', 0 => 'b59', 'nom' => 'Cottin', 1 => 'Cottin', 'prenom' => 'Vincenne', 2 => 'Vincenne', 'adresse' => '36 rue Des Roches', 3 => '36 rue Des Roches', 'ville' => 'Monteuil', 4 => 'Monteuil', 'cp' => '93100', 5 => '93100' ), 11 => Array ('id' => 'c14', 0 => 'c14', 'nom' => 'Daburon', 1 => 'Daburon', 'prenom' => 'François', 2 => 'François', 'adresse' => '13 rue de Chanzy', 3 => '13 rue de Chanzy', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 12 => Array ('id' => 'c3', 0 => 'c3', 'nom' => 'De', 1 => 'De', 'prenom' => 'Philippe', 2 => 'Philippe', 'adresse' => '13 rue Barthes', 3 => '13 rue Barthes', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 13 => Array ('id' => 'd13', 0 => 'd13', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Jeanne', 2 => 'Jeanne', 'adresse' => '134 allée des Joncs', 3 => '134 allée des Joncs', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 14 => Array ('id' => 'c54', 0 => 'c54', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '181 avenue Barbusse', 3 => '181 avenue Barbusse', 'ville' => 'Rosny', 4 => 'Rosny', 'cp' => '93210', 5 => '93210' ), 15 => Array ('id' => 'd51', 0 => 'd51', 'nom' => 'Debroise', 1 => 'Debroise', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '2 Bld Jourdain', 3 => '2 Bld Jourdain', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 16 => Array ('id' => 'e22', 0 => 'e22', 'nom' => 'Desmarquest', 1 => 'Desmarquest', 'prenom' => 'Nathalie', 2 => 'Nathalie', 'adresse' => '14 Place d Arc', 3 => '14 Place d Arc', 'ville' => 'Orléans', 4 => 'Orléans', 'cp' => '45000', 5 => '45000' ), 17 => Array ('id' => 'e24', 0 => 'e24', 'nom' => 'Desnost', 1 => 'Desnost', 'prenom' => 'Pierre', 2 => 'Pierre', 'adresse' => '16 avenue des Cèdres', 3 => '16 avenue des Cèdres', 'ville' => 'Guéret', 4 => 'Guéret', 'cp' => '23200', 5 => '23200' ), 18 => Array ('id' => 'e39', 0 => 'e39', 'nom' => 'Dudouit', 1 => 'Dudouit', 'prenom' => 'Frédéric', 2 => 'Frédéric', 'adresse' => '18 rue de l église', 3 => '18 rue de l église', 'ville' => 'GrandBourg', 4 => 'GrandBourg', 'cp' => '23120', 5 => '23120' ), 19 => Array ('id' => 'e49', 0 => 'e49', 'nom' => 'Duncombe', 1 => 'Duncombe', 'prenom' => 'Claude', 2 => 'Claude', 'adresse' => '19 rue de la tour', 3 => '19 rue de la tour', 'ville' => 'La souteraine', 4 => 'La souteraine', 'cp' => '23100', 5 => '23100' ), 20 => Array ('id' => 'e5', 0 => 'e5', 'nom' => 'Enault-Pascreau', 1 => 'Enault-Pascreau', 'prenom' => 'Céline', 2 => 'Céline', 'adresse' => '25 place de la gare', 3 => '25 place de la gare', 'ville' => 'Gueret', 4 => 'Gueret', 'cp' => '23200', 5 => '23200' ), 21 => Array ('id' => 'e52', 0 => 'e52', 'nom' => 'Eynde', 1 => 'Eynde', 'prenom' => 'Valérie', 2 => 'Valérie', 'adresse' => '3 Grand Place', 3 => '3 Grand Place', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13015', 5 => '13015' ), 22 => Array ('id' => 'f21', 0 => 'f21', 'nom' => 'Finck', 1 => 'Finck', 'prenom' => 'Jacques', 2 => 'Jacques', 'adresse' => '10 avenue du Prado', 3 => '10 avenue du Prado', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13002', 5 => '13002' ), 23 => Array ('id' => 'f39', 0 => 'f39', 'nom' => 'Frémont', 1 => 'Frémont', 'prenom' => 'Fernande', 2 => 'Fernande', 'adresse' => '4 route de la mer', 3 => '4 route de la mer', 'ville' => 'Allauh', 4 => 'Allauh', 'cp' => '13012', 5 => '13012' ), 24 => Array ('id' => 'f4', 0 => 'f4', 'nom' => 'Gest', 1 => 'Gest', 'prenom' => 'Alain', 2 => 'Alain', 'adresse' => '30 avenue de la mer', 3 => '30 avenue de la mer', 'ville' => 'Berre', 4 => 'Berre', 'cp' => '13025', 5 => '13025' ), 25 => Array ('id' => 'a93', 0 => 'a93', 'nom' => 'Tusseau', 1 => 'Tusseau', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '22 rue des Ternes', 3 => '22 rue des Ternes', 'ville' => 'Gramat', 4 => 'Gramat', 'cp' => '46123', 5 => '46123' ), 26 => Array ('id' => 'a131', 0 => 'a131', 'nom' => 'Villechalane', 1 => 'Villechalane', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '8 rue des Charmes', 3 => '8 rue des Charmes', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ) )
     * @assert () != Array (0 => Array ('id' => 'ZOZO', 0 => 'a17', 'nom' => 'Andre', 1 => 'Andre', 'prenom' => 'David', 2 => 'David', 'adresse' => '1 rue Petit', 3 => '1 rue Petit', 'ville' => 'Lalbenque', 4 => 'Lalbenque', 'cp' => '46200', 5 => '46200' ), 1 => Array ('id' => 'a55', 0 => 'a55', 'nom' => 'Bedos', 1 => 'Bedos', 'prenom' => 'Christian', 2 => 'Christian', 'adresse' => '1 rue Peranud', 3 => '1 rue Peranud', 'ville' => 'Montcuq', 4 => 'Montcuq', 'cp' => '46250', 5 => '46250'), 2 => Array ('id' => 'b13', 0 => 'b13', 'nom' => 'Bentot', 1 => 'Bentot', 'prenom' => 'Pascal', 2 => 'Pascal', 'adresse' => '11 allée des Cerises', 3 => '11 allée des Cerises', 'ville' => 'Bessines', 4 => 'Bessines', 'cp' => '46512', 5 => '46512' ), 3 => Array ('id' => 'b16', 0 => 'b16', 'nom' => 'Bioret', 1 => 'Bioret', 'prenom' => 'Luc', 2 => 'Luc', 'adresse' => '1 Avenue gambetta', 3 => '1 Avenue gambetta', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ), 4 => Array ('id' => 'b25', 0 => 'b25', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Denise', 2 => 'Denise', 'adresse' => '23 rue Manin', 3 => '23 rue Manin', 'ville' => 'paris', 4 => 'paris', 'cp' => '75019', 5 => '75019' ), 5 => Array ('id' => 'b19', 0 => 'b19', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Francis', 2 => 'Francis', 'adresse' => '10 rue des Perles', 3 => '10 rue des Perles', 'ville' => 'Montreuil', 4 => 'Montreuil', 'cp' => '93100', 5 => '93100' ), 6 => Array ('id' => 'b28', 0 => 'b28', 'nom' => 'Cacheux', 1 => 'Cacheux', 'prenom' => 'Bernard', 2 => 'Bernard', 'adresse' => '114 rue Blanche', 3 => '114 rue Blanche', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75017', 5 => '75017' ), 7 => Array ('id' => 'b34', 0 => 'b34', 'nom' => 'Cadic', 1 => 'Cadic', 'prenom' => 'Eric', 2 => 'Eric', 'adresse' => '123 avenue de la République', 3 => '123 avenue de la République', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75011', 5 => '75011' ), 8 => Array ('id' => 'b4', 0 => 'b4', 'nom' => 'Charoze', 1 => 'Charoze', 'prenom' => 'Catherine', 2 => 'Catherine', 'adresse' => '100 rue Petit', 3 => '100 rue Petit', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75019', 5 => '75019' ), 9 => Array ('id' => 'b50', 0 => 'b50', 'nom' => 'Clepkens', 1 => 'Clepkens', 'prenom' => 'Christophe', 2 => 'Christophe', 'adresse' => '12 allée des Anges', 3 => '12 allée des Anges', 'ville' => 'Romainville', 4 => 'Romainville', 'cp' => '93230', 5 => '93230' ), 10 => Array ('id' => 'b59', 0 => 'b59', 'nom' => 'Cottin', 1 => 'Cottin', 'prenom' => 'Vincenne', 2 => 'Vincenne', 'adresse' => '36 rue Des Roches', 3 => '36 rue Des Roches', 'ville' => 'Monteuil', 4 => 'Monteuil', 'cp' => '93100', 5 => '93100' ), 11 => Array ('id' => 'c14', 0 => 'c14', 'nom' => 'Daburon', 1 => 'Daburon', 'prenom' => 'François', 2 => 'François', 'adresse' => '13 rue de Chanzy', 3 => '13 rue de Chanzy', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 12 => Array ('id' => 'c3', 0 => 'c3', 'nom' => 'De', 1 => 'De', 'prenom' => 'Philippe', 2 => 'Philippe', 'adresse' => '13 rue Barthes', 3 => '13 rue Barthes', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 13 => Array ('id' => 'd13', 0 => 'd13', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Jeanne', 2 => 'Jeanne', 'adresse' => '134 allée des Joncs', 3 => '134 allée des Joncs', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 14 => Array ('id' => 'c54', 0 => 'c54', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '181 avenue Barbusse', 3 => '181 avenue Barbusse', 'ville' => 'Rosny', 4 => 'Rosny', 'cp' => '93210', 5 => '93210' ), 15 => Array ('id' => 'd51', 0 => 'd51', 'nom' => 'Debroise', 1 => 'Debroise', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '2 Bld Jourdain', 3 => '2 Bld Jourdain', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 16 => Array ('id' => 'e22', 0 => 'e22', 'nom' => 'Desmarquest', 1 => 'Desmarquest', 'prenom' => 'Nathalie', 2 => 'Nathalie', 'adresse' => '14 Place d Arc', 3 => '14 Place d Arc', 'ville' => 'Orléans', 4 => 'Orléans', 'cp' => '45000', 5 => '45000' ), 17 => Array ('id' => 'e24', 0 => 'e24', 'nom' => 'Desnost', 1 => 'Desnost', 'prenom' => 'Pierre', 2 => 'Pierre', 'adresse' => '16 avenue des Cèdres', 3 => '16 avenue des Cèdres', 'ville' => 'Guéret', 4 => 'Guéret', 'cp' => '23200', 5 => '23200' ), 18 => Array ('id' => 'e39', 0 => 'e39', 'nom' => 'Dudouit', 1 => 'Dudouit', 'prenom' => 'Frédéric', 2 => 'Frédéric', 'adresse' => '18 rue de l église', 3 => '18 rue de l église', 'ville' => 'GrandBourg', 4 => 'GrandBourg', 'cp' => '23120', 5 => '23120' ), 19 => Array ('id' => 'e49', 0 => 'e49', 'nom' => 'Duncombe', 1 => 'Duncombe', 'prenom' => 'Claude', 2 => 'Claude', 'adresse' => '19 rue de la tour', 3 => '19 rue de la tour', 'ville' => 'La souteraine', 4 => 'La souteraine', 'cp' => '23100', 5 => '23100' ), 20 => Array ('id' => 'e5', 0 => 'e5', 'nom' => 'Enault-Pascreau', 1 => 'Enault-Pascreau', 'prenom' => 'Céline', 2 => 'Céline', 'adresse' => '25 place de la gare', 3 => '25 place de la gare', 'ville' => 'Gueret', 4 => 'Gueret', 'cp' => '23200', 5 => '23200' ), 21 => Array ('id' => 'e52', 0 => 'e52', 'nom' => 'Eynde', 1 => 'Eynde', 'prenom' => 'Valérie', 2 => 'Valérie', 'adresse' => '3 Grand Place', 3 => '3 Grand Place', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13015', 5 => '13015' ), 22 => Array ('id' => 'f21', 0 => 'f21', 'nom' => 'Finck', 1 => 'Finck', 'prenom' => 'Jacques', 2 => 'Jacques', 'adresse' => '10 avenue du Prado', 3 => '10 avenue du Prado', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13002', 5 => '13002' ), 23 => Array ('id' => 'f39', 0 => 'f39', 'nom' => 'Frémont', 1 => 'Frémont', 'prenom' => 'Fernande', 2 => 'Fernande', 'adresse' => '4 route de la mer', 3 => '4 route de la mer', 'ville' => 'Allauh', 4 => 'Allauh', 'cp' => '13012', 5 => '13012' ), 24 => Array ('id' => 'f4', 0 => 'f4', 'nom' => 'Gest', 1 => 'Gest', 'prenom' => 'Alain', 2 => 'Alain', 'adresse' => '30 avenue de la mer', 3 => '30 avenue de la mer', 'ville' => 'Berre', 4 => 'Berre', 'cp' => '13025', 5 => '13025' ), 25 => Array ('id' => 'a93', 0 => 'a93', 'nom' => 'Tusseau', 1 => 'Tusseau', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '22 rue des Ternes', 3 => '22 rue des Ternes', 'ville' => 'Gramat', 4 => 'Gramat', 'cp' => '46123', 5 => '46123' ), 26 => Array ('id' => 'a131', 0 => 'a131', 'nom' => 'Villechalane', 1 => 'Villechalane', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '8 rue des Charmes', 3 => '8 rue des Charmes', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ) )
     * @assert () != NULL
     */
    
    public function getLesVisiteursValidationFichesFrais()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT DISTINCT visiteur.id AS id, '
            . 'visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom, '
            . 'visiteur.adresse AS adresse, '
            . 'visiteur.ville AS ville, '
            . 'visiteur.cp AS cp '
            . 'FROM visiteur '
            . 'INNER JOIN fichefrais '
            . 'ON visiteur.id = fichefrais.idvisiteur '
            . "WHERE fichefrais.idetat = 'CL' "
            . "AND visiteur.comptable = 0 "
            . 'ORDER BY visiteur.nom, visiteur.prenom asc'
        );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetchAll();
        return $laLigne;
    }
    
    /**
     * Retourne les visiteurs médicaux avec des fiches de frais dont l'état est validé
     * ou mis en paiement
     *
     * @return Array un tableau associatif avec les clés et valeurs contenant le nom,
     *               prénom, adresse, ville et code postal
     *
     * @assert () == Array (0 => Array ('id' => 'a17', 0 => 'a17', 'nom' => 'Andre', 1 => 'Andre', 'prenom' => 'David', 2 => 'David', 'adresse' => '1 rue Petit', 3 => '1 rue Petit', 'ville' => 'Lalbenque', 4 => 'Lalbenque', 'cp' => '46200', 5 => '46200' ), 1 => Array ('id' => 'b13', 0 => 'b13', 'nom' => 'Bentot', 1 => 'Bentot', 'prenom' => 'Pascal', 2 => 'Pascal', 'adresse' => '11 allée des Cerises', 3 => '11 allée des Cerises', 'ville' => 'Bessines', 4 => 'Bessines', 'cp' => '46512', 5 => '46512' ), 2 => Array ('id' => 'b16', 0 => 'b16', 'nom' => 'Bioret', 1 => 'Bioret', 'prenom' => 'Luc', 2 => 'Luc', 'adresse' => '1 Avenue gambetta', 3 => '1 Avenue gambetta', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ), 3 => Array ('id' => 'b25', 0 => 'b25', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Denise', 2 => 'Denise', 'adresse' => '23 rue Manin', 3 => '23 rue Manin', 'ville' => 'paris', 4 => 'paris', 'cp' => '75019', 5 => '75019' ), 4 => Array ('id' => 'b19', 0 => 'b19', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Francis', 2 => 'Francis', 'adresse' => '10 rue des Perles', 3 => '10 rue des Perles', 'ville' => 'Montreuil', 4 => 'Montreuil', 'cp' => '93100', 5 => '93100' ), 5 => Array ('id' => 'b28', 0 => 'b28', 'nom' => 'Cacheux', 1 => 'Cacheux', 'prenom' => 'Bernard', 2 => 'Bernard', 'adresse' => '114 rue Blanche', 3 => '114 rue Blanche', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75017', 5 => '75017' ), 6 => Array ('id' => 'b34', 0 => 'b34', 'nom' => 'Cadic', 1 => 'Cadic', 'prenom' => 'Eric', 2 => 'Eric', 'adresse' => '123 avenue de la République', 3 => '123 avenue de la République', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75011', 5 => '75011' ), 7 => Array ('id' => 'b4', 0 => 'b4', 'nom' => 'Charoze', 1 => 'Charoze', 'prenom' => 'Catherine', 2 => 'Catherine', 'adresse' => '100 rue Petit', 3 => '100 rue Petit', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75019', 5 => '75019' ), 8 => Array ('id' => 'b50', 0 => 'b50', 'nom' => 'Clepkens', 1 => 'Clepkens', 'prenom' => 'Christophe', 2 => 'Christophe', 'adresse' => '12 allée des Anges', 3 => '12 allée des Anges', 'ville' => 'Romainville', 4 => 'Romainville', 'cp' => '93230', 5 => '93230' ), 9 => Array ('id' => 'b59', 0 => 'b59', 'nom' => 'Cottin', 1 => 'Cottin', 'prenom' => 'Vincenne', 2 => 'Vincenne', 'adresse' => '36 rue Des Roches', 3 => '36 rue Des Roches', 'ville' => 'Monteuil', 4 => 'Monteuil', 'cp' => '93100', 5 => '93100' ), 10 => Array ('id' => 'c14', 0 => 'c14', 'nom' => 'Daburon', 1 => 'Daburon', 'prenom' => 'François', 2 => 'François', 'adresse' => '13 rue de Chanzy', 3 => '13 rue de Chanzy', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 11 => Array ('id' => 'c3', 0 => 'c3', 'nom' => 'De', 1 => 'De', 'prenom' => 'Philippe', 2 => 'Philippe', 'adresse' => '13 rue Barthes', 3 => '13 rue Barthes', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 12 => Array ('id' => 'd13', 0 => 'd13', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Jeanne', 2 => 'Jeanne', 'adresse' => '134 allée des Joncs', 3 => '134 allée des Joncs', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 13 => Array ('id' => 'c54', 0 => 'c54', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '181 avenue Barbusse', 3 => '181 avenue Barbusse', 'ville' => 'Rosny', 4 => 'Rosny', 'cp' => '93210', 5 => '93210' ), 14 => Array ('id' => 'd51', 0 => 'd51', 'nom' => 'Debroise', 1 => 'Debroise', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '2 Bld Jourdain', 3 => '2 Bld Jourdain', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 15 => Array ('id' => 'e22', 0 => 'e22', 'nom' => 'Desmarquest', 1 => 'Desmarquest', 'prenom' => 'Nathalie', 2 => 'Nathalie', 'adresse' => '14 Place d Arc', 3 => '14 Place d Arc', 'ville' => 'Orléans', 4 => 'Orléans', 'cp' => '45000', 5 => '45000' ), 16 => Array ('id' => 'e24', 0 => 'e24', 'nom' => 'Desnost', 1 => 'Desnost', 'prenom' => 'Pierre', 2 => 'Pierre', 'adresse' => '16 avenue des Cèdres', 3 => '16 avenue des Cèdres', 'ville' => 'Guéret', 4 => 'Guéret', 'cp' => '23200', 5 => '23200' ), 17 => Array ('id' => 'e39', 0 => 'e39', 'nom' => 'Dudouit', 1 => 'Dudouit', 'prenom' => 'Frédéric', 2 => 'Frédéric', 'adresse' => '18 rue de l église', 3 => '18 rue de l église', 'ville' => 'GrandBourg', 4 => 'GrandBourg', 'cp' => '23120', 5 => '23120' ), 18 => Array ('id' => 'e49', 0 => 'e49', 'nom' => 'Duncombe', 1 => 'Duncombe', 'prenom' => 'Claude', 2 => 'Claude', 'adresse' => '19 rue de la tour', 3 => '19 rue de la tour', 'ville' => 'La souteraine', 4 => 'La souteraine', 'cp' => '23100', 5 => '23100' ), 19 => Array ('id' => 'e5', 0 => 'e5', 'nom' => 'Enault-Pascreau', 1 => 'Enault-Pascreau', 'prenom' => 'Céline', 2 => 'Céline', 'adresse' => '25 place de la gare', 3 => '25 place de la gare', 'ville' => 'Gueret', 4 => 'Gueret', 'cp' => '23200', 5 => '23200' ), 20 => Array ('id' => 'e52', 0 => 'e52', 'nom' => 'Eynde', 1 => 'Eynde', 'prenom' => 'Valérie', 2 => 'Valérie', 'adresse' => '3 Grand Place', 3 => '3 Grand Place', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13015', 5 => '13015' ), 21 => Array ('id' => 'f21', 0 => 'f21', 'nom' => 'Finck', 1 => 'Finck', 'prenom' => 'Jacques', 2 => 'Jacques', 'adresse' => '10 avenue du Prado', 3 => '10 avenue du Prado', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13002', 5 => '13002' ), 22 => Array ('id' => 'f39', 0 => 'f39', 'nom' => 'Frémont', 1 => 'Frémont', 'prenom' => 'Fernande', 2 => 'Fernande', 'adresse' => '4 route de la mer', 3 => '4 route de la mer', 'ville' => 'Allauh', 4 => 'Allauh', 'cp' => '13012', 5 => '13012' ), 23 => Array ('id' => 'f4', 0 => 'f4', 'nom' => 'Gest', 1 => 'Gest', 'prenom' => 'Alain', 2 => 'Alain', 'adresse' => '30 avenue de la mer', 3 => '30 avenue de la mer', 'ville' => 'Berre', 4 => 'Berre', 'cp' => '13025', 5 => '13025' ), 24 => Array ('id' => 'a93', 0 => 'a93', 'nom' => 'Tusseau', 1 => 'Tusseau', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '22 rue des Ternes', 3 => '22 rue des Ternes', 'ville' => 'Gramat', 4 => 'Gramat', 'cp' => '46123', 5 => '46123' ), 25 => Array ('id' => 'a131', 0 => 'a131', 'nom' => 'Villechalane', 1 => 'Villechalane', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '8 rue des Charmes', 3 => '8 rue des Charmes', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ) )
     * @assert () != Array (0 => Array ('id' => 'ZOZO', 0 => 'a17', 'nom' => 'Andre', 1 => 'Andre', 'prenom' => 'David', 2 => 'David', 'adresse' => '1 rue Petit', 3 => '1 rue Petit', 'ville' => 'Lalbenque', 4 => 'Lalbenque', 'cp' => '46200', 5 => '46200' ), 1 => Array ('id' => 'b13', 0 => 'b13', 'nom' => 'Bentot', 1 => 'Bentot', 'prenom' => 'Pascal', 2 => 'Pascal', 'adresse' => '11 allée des Cerises', 3 => '11 allée des Cerises', 'ville' => 'Bessines', 4 => 'Bessines', 'cp' => '46512', 5 => '46512' ), 2 => Array ('id' => 'b16', 0 => 'b16', 'nom' => 'Bioret', 1 => 'Bioret', 'prenom' => 'Luc', 2 => 'Luc', 'adresse' => '1 Avenue gambetta', 3 => '1 Avenue gambetta', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ), 3 => Array ('id' => 'b25', 0 => 'b25', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Denise', 2 => 'Denise', 'adresse' => '23 rue Manin', 3 => '23 rue Manin', 'ville' => 'paris', 4 => 'paris', 'cp' => '75019', 5 => '75019' ), 4 => Array ('id' => 'b19', 0 => 'b19', 'nom' => 'Bunisset', 1 => 'Bunisset', 'prenom' => 'Francis', 2 => 'Francis', 'adresse' => '10 rue des Perles', 3 => '10 rue des Perles', 'ville' => 'Montreuil', 4 => 'Montreuil', 'cp' => '93100', 5 => '93100' ), 5 => Array ('id' => 'b28', 0 => 'b28', 'nom' => 'Cacheux', 1 => 'Cacheux', 'prenom' => 'Bernard', 2 => 'Bernard', 'adresse' => '114 rue Blanche', 3 => '114 rue Blanche', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75017', 5 => '75017' ), 6 => Array ('id' => 'b34', 0 => 'b34', 'nom' => 'Cadic', 1 => 'Cadic', 'prenom' => 'Eric', 2 => 'Eric', 'adresse' => '123 avenue de la République', 3 => '123 avenue de la République', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75011', 5 => '75011' ), 7 => Array ('id' => 'b4', 0 => 'b4', 'nom' => 'Charoze', 1 => 'Charoze', 'prenom' => 'Catherine', 2 => 'Catherine', 'adresse' => '100 rue Petit', 3 => '100 rue Petit', 'ville' => 'Paris', 4 => 'Paris', 'cp' => '75019', 5 => '75019' ), 8 => Array ('id' => 'b50', 0 => 'b50', 'nom' => 'Clepkens', 1 => 'Clepkens', 'prenom' => 'Christophe', 2 => 'Christophe', 'adresse' => '12 allée des Anges', 3 => '12 allée des Anges', 'ville' => 'Romainville', 4 => 'Romainville', 'cp' => '93230', 5 => '93230' ), 9 => Array ('id' => 'b59', 0 => 'b59', 'nom' => 'Cottin', 1 => 'Cottin', 'prenom' => 'Vincenne', 2 => 'Vincenne', 'adresse' => '36 rue Des Roches', 3 => '36 rue Des Roches', 'ville' => 'Monteuil', 4 => 'Monteuil', 'cp' => '93100', 5 => '93100' ), 10 => Array ('id' => 'c14', 0 => 'c14', 'nom' => 'Daburon', 1 => 'Daburon', 'prenom' => 'François', 2 => 'François', 'adresse' => '13 rue de Chanzy', 3 => '13 rue de Chanzy', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 11 => Array ('id' => 'c3', 0 => 'c3', 'nom' => 'De', 1 => 'De', 'prenom' => 'Philippe', 2 => 'Philippe', 'adresse' => '13 rue Barthes', 3 => '13 rue Barthes', 'ville' => 'Créteil', 4 => 'Créteil', 'cp' => '94000', 5 => '94000' ), 12 => Array ('id' => 'd13', 0 => 'd13', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Jeanne', 2 => 'Jeanne', 'adresse' => '134 allée des Joncs', 3 => '134 allée des Joncs', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 13 => Array ('id' => 'c54', 0 => 'c54', 'nom' => 'Debelle', 1 => 'Debelle', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '181 avenue Barbusse', 3 => '181 avenue Barbusse', 'ville' => 'Rosny', 4 => 'Rosny', 'cp' => '93210', 5 => '93210' ), 14 => Array ('id' => 'd51', 0 => 'd51', 'nom' => 'Debroise', 1 => 'Debroise', 'prenom' => 'Michel', 2 => 'Michel', 'adresse' => '2 Bld Jourdain', 3 => '2 Bld Jourdain', 'ville' => 'Nantes', 4 => 'Nantes', 'cp' => '44000', 5 => '44000' ), 15 => Array ('id' => 'e22', 0 => 'e22', 'nom' => 'Desmarquest', 1 => 'Desmarquest', 'prenom' => 'Nathalie', 2 => 'Nathalie', 'adresse' => '14 Place d Arc', 3 => '14 Place d Arc', 'ville' => 'Orléans', 4 => 'Orléans', 'cp' => '45000', 5 => '45000' ), 16 => Array ('id' => 'e24', 0 => 'e24', 'nom' => 'Desnost', 1 => 'Desnost', 'prenom' => 'Pierre', 2 => 'Pierre', 'adresse' => '16 avenue des Cèdres', 3 => '16 avenue des Cèdres', 'ville' => 'Guéret', 4 => 'Guéret', 'cp' => '23200', 5 => '23200' ), 17 => Array ('id' => 'e39', 0 => 'e39', 'nom' => 'Dudouit', 1 => 'Dudouit', 'prenom' => 'Frédéric', 2 => 'Frédéric', 'adresse' => '18 rue de l église', 3 => '18 rue de l église', 'ville' => 'GrandBourg', 4 => 'GrandBourg', 'cp' => '23120', 5 => '23120' ), 18 => Array ('id' => 'e49', 0 => 'e49', 'nom' => 'Duncombe', 1 => 'Duncombe', 'prenom' => 'Claude', 2 => 'Claude', 'adresse' => '19 rue de la tour', 3 => '19 rue de la tour', 'ville' => 'La souteraine', 4 => 'La souteraine', 'cp' => '23100', 5 => '23100' ), 19 => Array ('id' => 'e5', 0 => 'e5', 'nom' => 'Enault-Pascreau', 1 => 'Enault-Pascreau', 'prenom' => 'Céline', 2 => 'Céline', 'adresse' => '25 place de la gare', 3 => '25 place de la gare', 'ville' => 'Gueret', 4 => 'Gueret', 'cp' => '23200', 5 => '23200' ), 20 => Array ('id' => 'e52', 0 => 'e52', 'nom' => 'Eynde', 1 => 'Eynde', 'prenom' => 'Valérie', 2 => 'Valérie', 'adresse' => '3 Grand Place', 3 => '3 Grand Place', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13015', 5 => '13015' ), 21 => Array ('id' => 'f21', 0 => 'f21', 'nom' => 'Finck', 1 => 'Finck', 'prenom' => 'Jacques', 2 => 'Jacques', 'adresse' => '10 avenue du Prado', 3 => '10 avenue du Prado', 'ville' => 'Marseille', 4 => 'Marseille', 'cp' => '13002', 5 => '13002' ), 22 => Array ('id' => 'f39', 0 => 'f39', 'nom' => 'Frémont', 1 => 'Frémont', 'prenom' => 'Fernande', 2 => 'Fernande', 'adresse' => '4 route de la mer', 3 => '4 route de la mer', 'ville' => 'Allauh', 4 => 'Allauh', 'cp' => '13012', 5 => '13012' ), 23 => Array ('id' => 'f4', 0 => 'f4', 'nom' => 'Gest', 1 => 'Gest', 'prenom' => 'Alain', 2 => 'Alain', 'adresse' => '30 avenue de la mer', 3 => '30 avenue de la mer', 'ville' => 'Berre', 4 => 'Berre', 'cp' => '13025', 5 => '13025' ), 24 => Array ('id' => 'a93', 0 => 'a93', 'nom' => 'Tusseau', 1 => 'Tusseau', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '22 rue des Ternes', 3 => '22 rue des Ternes', 'ville' => 'Gramat', 4 => 'Gramat', 'cp' => '46123', 5 => '46123' ), 25 => Array ('id' => 'a131', 0 => 'a131', 'nom' => 'Villechalane', 1 => 'Villechalane', 'prenom' => 'Louis', 2 => 'Louis', 'adresse' => '8 rue des Charmes', 3 => '8 rue des Charmes', 'ville' => 'Cahors', 4 => 'Cahors', 'cp' => '46000', 5 => '46000' ) )
     * @assert () != NULL
     */
    public function getLesVisiteursPaiementFichesFrais()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT DISTINCT visiteur.id AS id, '
            . 'visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom, '
            . 'visiteur.adresse AS adresse, '
            . 'visiteur.ville AS ville, '
            . 'visiteur.cp AS cp '
            . 'FROM visiteur '
            . 'INNER JOIN fichefrais '
            . 'ON visiteur.id = fichefrais.idvisiteur '
            . "WHERE fichefrais.idetat = 'VA' "
            . "OR fichefrais.idetat = 'MP' "
            . "AND visiteur.comptable = 0 "
            . 'ORDER BY visiteur.nom, visiteur.prenom asc'
        );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetchAll();
        return $laLigne;
    }
 
    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur id du visiteur
     *
     * @return Array un tableau associatif de clé un mois -aaaamm- et de valeurs 
     *               l'année et le mois correspondant
     *
     * @assert ('a131') == Array (0 => Array ('mois' => '201803', 'numAnnee' => '2018', 'numMois' => '03'), 1 => Array ('mois' => '201802', 'numAnnee' => '2018', 'numMois' => '02'), 2 => Array ('mois' => '201710', 'numAnnee' => '2017', 'numMois' => '10'), 3 => Array ('mois' => '201709', 'numAnnee' => '2017', 'numMois' => '09'), 4 => Array ('mois' => '201708', 'numAnnee' => '2017', 'numMois' => '08'), 5 => Array ('mois' => '201707', 'numAnnee' => '2017', 'numMois' => '07'), 6 => Array ('mois' => '201706', 'numAnnee' => '2017', 'numMois' => '06'), 7 => Array ('mois' => '201705', 'numAnnee' => '2017', 'numMois' => '05'), 8 => Array ('mois' => '201704', 'numAnnee' => '2017', 'numMois' => '04'), 9 => Array ('mois' => '201703', 'numAnnee' => '2017', 'numMois' => '03'), 10 => Array ('mois' => '201702', 'numAnnee' => '2017', 'numMois' => '02'), 11 => Array ('mois' => '201701', 'numAnnee' => '2017', 'numMois' => '01'), 12 => Array ('mois' => '201612', 'numAnnee' => '2016', 'numMois' => '12'), 13 => Array ('mois' => '201611', 'numAnnee' => '2016', 'numMois' => '11'), 14 => Array ('mois' => '201610', 'numAnnee' => '2016', 'numMois' => '10'), 15 => Array ('mois' => '201609', 'numAnnee' => '2016', 'numMois' => '09'))
     * @assert ('a131') != Array (0 => Array ('mois' => '202503', 'numAnnee' => '2025', 'numMois' => '03'), 1 => Array ('mois' => '201802', 'numAnnee' => '2018', 'numMois' => '02'), 2 => Array ('mois' => '201710', 'numAnnee' => '2017', 'numMois' => '10'), 3 => Array ('mois' => '201709', 'numAnnee' => '2017', 'numMois' => '09'), 4 => Array ('mois' => '201708', 'numAnnee' => '2017', 'numMois' => '08'), 5 => Array ('mois' => '201707', 'numAnnee' => '2017', 'numMois' => '07'), 6 => Array ('mois' => '201706', 'numAnnee' => '2017', 'numMois' => '06'), 7 => Array ('mois' => '201705', 'numAnnee' => '2017', 'numMois' => '05'), 8 => Array ('mois' => '201704', 'numAnnee' => '2017', 'numMois' => '04'), 9 => Array ('mois' => '201703', 'numAnnee' => '2017', 'numMois' => '03'), 10 => Array ('mois' => '201702', 'numAnnee' => '2017', 'numMois' => '02'), 11 => Array ('mois' => '201701', 'numAnnee' => '2017', 'numMois' => '01'), 12 => Array ('mois' => '201612', 'numAnnee' => '2016', 'numMois' => '12'), 13 => Array ('mois' => '201611', 'numAnnee' => '2016', 'numMois' => '11'), 14 => Array ('mois' => '201610', 'numAnnee' => '2016', 'numMois' => '10'), 15 => Array ('mois' => '201609', 'numAnnee' => '2016', 'numMois' => '09'))
     * @assert ('a131') != array()
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais validée 
     * ou mise en paiement
     *
     * @param String $idVisiteur id du visiteur
     *
     * @return Array un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     *
     * @assert ('a131') == Array ( 0 => Array ( 'mois' => '201709', 'numAnnee' => '2017', 'numMois' => '09' ) )
     * @assert ('a131') != Array ( 0 => Array ( 'mois' => '201709', 'numAnnee' => '2017', 'numMois' => '40' ) )
     * @assert ('a131') != array()
     */
    public function getLesMoisDisponiblesPaiementFichesFrais($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . "WHERE fichefrais.idvisiteur = :unIdVisiteur AND "
            . "(fichefrais.idetat = 'VA' OR fichefrais.idetat = 'MP') "
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }
    
    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais cloturée
     *
     * @param String $idVisiteur id du visiteur
     *
     * @return Array un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     *
     * @assert ('a131') == Array ( 0 => Array ( 'mois' => '201802', 'numAnnee' => '2018', 'numMois' => '02' ), 1 => Array ( 'mois' => '201710', 'numAnnee' => '2017', 'numMois' => '10' ) )
     * @assert ('a131') != Array ( 0 => Array ( 'mois' => '202502', 'numAnnee' => '2025', 'numMois' => '02' ), 1 => Array ( 'mois' => '201710', 'numAnnee' => '2017', 'numMois' => '10' ) )
     * @assert ('a131') != array()
     */
    public function getLesMoisDisponiblesValidationFichesFrais($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . "WHERE fichefrais.idvisiteur = :unIdVisiteur AND "
            . "fichefrais.idetat = 'CL' "
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }
    
    /**
     * Retourne les le montant total des frais forfaitaires d'un visiteur pour un
     * mois donné.
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Array un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     *
     * @assert ('a131', '201612') == '4116.30'
     * @assert ('a131', '201612') != array()
     * @assert ('a131', '199012') == NULL
     * @assert ('ZOZO', '201612') == NULL
     */
    public function getLeMontantTotalFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT SUM(lignefraisforfait.quantite*fraisforfait.montant) as totalFraisForfait '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait ON lignefraisforfait.idfraisforfait = fraisforfait.id '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['totalFraisForfait'];
    }
    
    /**
     * Retourne les le montant total des frais hors forfait d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Array un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     *
     * @assert ('a131', '201612') == '2647.00'
     * @assert ('a131', '201612') != array()
     * @assert ('a131', '199012') == NULL
     * @assert ('ZOZO', '201612') == NULL
     */
    public function getLeMontantTotalFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT SUM(lignefraishorsforfait.montant) as totalFraisHorsForfait '
            . 'FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.refuse IS NULL '
            . 'AND lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'     
        );
        
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['totalFraisHorsForfait'];
    }
    
    /**
     * Retourne le montant total des frais forfaitaires et hors forfait d'un visiteur pour un
     * mois donné et met à jour le montant validé d'un fiche de frais
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Array un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function montantTotalFrais($idVisiteur, $mois)
    {
        $montantTotal = $this->getLeMontantTotalFraisForfait($idVisiteur, $mois) + 
                $this->getLeMontantTotalFraisHorsForfait($idVisiteur, $mois);
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais SET montantvalide = :unMontantTotal, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unMontantTotal', $montantTotal, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $montantTotal;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Array un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     *
     * @assert ('a131', '201612') == Array ( 'idEtat' => 'RB', 'dateModif' => '2017-02-01', 'nbJustificatifs' => '0', 'montantValide' => '5545.91', 'libEtat' => 'Remboursée', 0 => 'RB', 1 => '2017-02-01', 2 => '0', 3 => '5545.91', 4 => 'Remboursée' )
     * @assert ('a131', '201612') != Array ( 'idEtat' => 'CL', 'dateModif' => '2017-02-01', 'nbJustificatifs' => '0', 'montantValide' => '5545.91', 'libEtat' => 'Remboursée', 0 => 'RB', 1 => '2017-02-01', 2 => '0', 3 => '5545.91', 4 => 'Remboursée' )
     * @assert ('a131', '201612') != Array ()
     * @assert ('a131', '201612') != NULL
     *
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif, '
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String  $idVisiteur      id du visiteur
     * @param String  $mois            mois sous la forme aaaamm
     * @param String  $etat            nouvel état de la fiche de frais
     * @param Boolean $nbJustificatif  paramètre optionnel nombre de justificatifs
     * 
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat, $nbJustificatif = false)
    {
        $req = 'UPDATE fichefrais SET idetat = :unEtat, datemodif = now() ';
        if ($nbJustificatif) {
            $req .=  ', nbjustificatifs = :unNbJustificatifs ';
        }
        $req .= 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
        . 'AND fichefrais.mois = :unMois';
        $requetePrepare = PdoGSB::$monPdo->prepare($req);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        if ($nbJustificatif) {
            $requetePrepare->bindParam(':unNbJustificatifs', $nbJustificatif, PDO::PARAM_STR);
        }
        $requetePrepare->execute();
    }

    /**
     * Cloture les fiches de frais du mois précédent.
     * Passe le champ idEtat à CL et met la date de modif à aujourd'hui.
     *
     * @param String $mois mois sous la forme aaaammu
     *
     * @return null
     */
    public function cloturerFichesFrais($mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . "SET idetat = 'CL', datemodif = now() "
            . "WHERE idetat = 'CR'"
            . 'AND fichefrais.mois <= :unMois'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     *
     * Fonction pour test unitaire
     *
     * tableau associatif la ligne de frais
     * hors forfait concernée par le paramètre.
     *
     * @param String $id id du frais hors forfait
     *
     * @return Array tous les champs de la ligne de frais hors forfait sous la forme
     *               d'un tableau associatif
     */
    public function getLeFraisHorsForfait($id)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unId'
        );
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $libelle = $laLigne['libelle'];
        $laLigne['libelle'] = Utils::filtrerChainePourVue($libelle);
        return $laLigne;
    }

    /**
     *
     * Fonction pour tests unitaires
     *
     * tableau associatif de la dernière ligne de frais
     * hors forfait insérée concernées par les deux paramètres.
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return Array tous les champs de la ligne de frais hors forfait sous la forme
     *         d'un tableau associatif
     */
    public function getLeDernierFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur'
            . ' AND lignefraishorsforfait.mois = :unMois'
            . ' AND lignefraishorsforfait.id = '
            . '(SELECT MAX(id) FROM lignefraishorsforfait WHERE '
            . 'lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois)'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $libelle = $laLigne['libelle'];
        $laLigne['libelle'] = Utils::filtrerChainePourVue($libelle);
        return $laLigne;
    }
 
    /**
     * Fonction pour tests unitaires
     *
     * Met à jour un frais de ligneFraisHorsForfait dont l'id est passé en paramètre
     *  avec le nouveau libelle non introduit par 'REFUSE-'
     * et le champ refuse changé à false
     *
     * @param String $id id du frais hors forfait
     * @param String $libelle libelle du frais hors forfait
     *
     * @return null
     */
    public function accepterUnFraisHorsForfait($id, $libelle)
    {
        $libelleFiltre = Utils::filtrerChainePourBdd(Utils::filtrerChainePourVue($libelle));
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.libelle = :unLibelle, '
            . 'lignefraishorsforfait.refuse = NULL '
            . 'WHERE lignefraishorsforfait.id = :unId '
        );
        $requetePrepare->bindParam(':unLibelle', $libelleFiltre, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_INT);
        $requetePrepare->execute();
    }
    
    /**
     * Fonction pour tests unitaires
     *
     * Supprime la fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * @param String $idVisiteur id du visiteur
     * @param String $mois       mois sous la forme aaaamm
     *
     * @return null
     */
    public function supprimerNouvellesLignesFrais($idVisiteur, $mois)
    {
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'DELETE FROM lignefraisforfait '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'DELETE FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
}
