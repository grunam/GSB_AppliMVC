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
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
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
     * @return l'unique objet de la classe PdoGsb
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
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
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
     * @param String $idVisiteur ID du visiteur
     *
     * @return vrai ou faux
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
     * @param String $idFrais ID du frais hors forfait
     * @param String $idVisiteur ID du visiteur
     * @param String $mois Mois sous la forme aaaamm
     * 
     * @return vrai ou faux
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
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
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
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = Utils::dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
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
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
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
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
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
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
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
     * Met à jour la table ligneFraisHorsForfait
     * Met à jour la table ligneFraisHorsForfait pour un id
     *  donné en enregistrant les nouveaux montants
     *
     * @param Int $id ID du frais hors forfait
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur pour ce frais
     *
     * @return null
     */
    public function majFraisHorsForfait($lesFrais)
    {
        
        foreach ($lesFrais as $unFrais) {
            
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraishorsforfait '
                . 'SET lignefraishorsforfait.libelle = :unLibelle, '
                . 'lignefraishorsforfait.date = :uneDate, '
                . 'lignefraishorsforfait.montant = :unMontant '
                . 'WHERE lignefraishorsforfait.id = :idFrais'
            );
           
            $requetePrepare->bindParam(':unLibelle', $unFrais['libelle'], PDO::PARAM_INT);
            $requetePrepare->bindParam(':uneDate', $unFrais['date'], PDO::PARAM_STR); 
            $requetePrepare->bindParam(':unMontant', $unFrais['montant'], PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unFrais['id'], PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }
    
    
    
    
    /**
     * Met à jour d'une ligneFraisHorsForfait dont l'id est passé en paramètre
     *  avec le mois passé en paramètre  
     *
     * @param String $id ID du frais hors forfait
     * @param String $mois       Mois sous la forme aaaamm
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
     * Met à jour d'une ligneFraisHorsForfait dont l'id est passé en paramètre avec le nouveau libelle introduit par 'REFUSE-'  
     * et le champ refuse changé à true
     *
     * @param String $id ID du frais hors forfait
     * @param String $libelle libelle du frais hors forfait
     *
     * @return null
     */
    public function refuserUnFraisHorsForfait($id, $libelle)
    {
       
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.libelle = :unLibelle, '
            . 'lignefraishorsforfait.refuse = 1 '     
            . 'WHERE lignefraishorsforfait.id = :unId '

        );
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_INT);
        $requetePrepare->execute();
        
    }
    

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
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
     * Teste si un visiteur possède une fiche de frais pour le mois passé en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
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
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
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
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $moisSuivant)
    {   
        
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
       
        
        if ($moisSuivant >= Utils::getMois(date('d/m/Y'))) {
           $idEtat = 'CR'; 
        }else{
           $idEtat = 'CL';
        }
         
        if ($laDerniereFiche['idEtat'] == 'CR' && $dernierMois < $moisSuivant) {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL'); 
        }  
        
        
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),:unEtat)"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $moisSuivant, PDO::PARAM_STR);
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
            $requetePrepare->bindParam(':unMois', $moisSuivant, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètres
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj/mm/aaaa
     * @param Float  $montant    Montant du frais
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
        
        $dateFr = Utils::dateFrancaisVersAnglais($date);
         
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant, null) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
         
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en paramètre
     *
     * @param String $idFrais ID du frais
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
        
        //return $lesFrais;
        
        
        
        foreach ($lesFrais as $unIdFrais) {
        
           //print_r($unIdFrais);
            
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
            );
            $requetePrepare->bindParam(':unIdFrais', $unIdFrais, PDO::PARAM_INT);
            $requetePrepare->execute();
            $laLigne = $requetePrepare->fetch();
            
           
            if($laLigne['refuse']!= 1){
                
                 //print_r($laLigne['id']);
                
                $this->refuserUnFraisHorsForfait($laLigne['id'], Utils::mentionRefuse($laLigne['libelle']));
         
            }
        } 
        
       
    }
    
    
    
    /**
     * Reporte le frais hors forfait dont l'id est ou les ids sont passé(s) en paramètre dans la fiche du mois suivant, 
     * créé la fiche si elle n'existe pas.
     *
     * @param Array $lesFrais tableau associatif de clé idFrais
     * @param String $mois       Mois sous la forme aaaamm
     * 
     * @return null
     */
    public function reporterLesFraisHorsForfait($lesFrais, $mois)
    {
        
        //return $lesFrais;
        foreach ($lesFrais as $unIdFrais) {
        
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
            );
            $requetePrepare->bindParam(':unIdFrais', $unIdFrais, PDO::PARAM_INT);
            $requetePrepare->execute();
            $laLigne = $requetePrepare->fetch();
            
           
            if($laLigne['refuse'] != 1){
                if ($this->estPremierFraisMois($laLigne['idvisiteur'], $mois)){       
                    
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
     *
     * @return un tableau associatif avec les clés et valeurs contenant le nom, 
     * prénom, adresse, ville et code postal
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
     * @return un tableau associatif avec les clés et valeurs contenant le nom, 
     * prénom, adresse, ville et code postal
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
     * @return un tableau associatif avec les clés et valeurs contenant le nom, 
     * prénom, adresse, ville et code postal
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
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
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
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
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
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
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
     * mois donné 
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
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
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
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
     * Retourne les le montant total des frais forfaitaires et hors forfait d'un visiteur pour un
     * mois donné et met à jour le montant validé d'un fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function montantTotalFrais($idVisiteur, $mois)
    {
       
        $montantTotal = $this->getLeMontantTotalFraisForfait($idVisiteur, $mois) + $this->getLeMontantTotalFraisHorsForfait($idVisiteur, $mois);
       
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
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
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
     * Retourne les informations de la prochaine fiche de frais d'un visiteur qui suit le
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    
    public function getLesInfosFicheFraisPremierMoisSuivant($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.mois as mois, '    
            . 'fichefrais.datemodif as dateModif, '
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = (SELECT fichefrais.mois WHERE fichefrais.mois > :unMois LIMIT 1)'
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
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat, $nbJustificatif=false)
    {
        
        $req = 'UPDATE ficheFrais SET idetat = :unEtat, datemodif = now() ';
           if($nbJustificatif){
                $req .=  ', nbjustificatifs = :unNbJustificatifs ';
            }
            $req .= 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois';
        
        
        
        $requetePrepare = PdoGSB::$monPdo->prepare($req);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        if($nbJustificatif){
            $requetePrepare->bindParam(':unNbJustificatifs', $nbJustificatif, PDO::PARAM_STR);
        }
        $requetePrepare->execute();
    }
    
    
    
    /**
     * Cloture les fiches de frais du mois précédent.
     * Passe le champ idEtat à CL et met la date de modif à aujourd'hui.
     *
     * @param String $mois       Mois sous la forme aaaammu
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
    
    
    
    


    /**************** Fonctions pour test unitaire *********************/

    /**************** Fonctions pour test unitaire *********************/
    
    

    /**
     * 
     * Fonction pour test unitaire
     * 
     * tableau associatif la ligne de frais
     * hors forfait concernée par le paramètre.
     *
     * @param String $id ID du frais hors forfait
     *
     * @return tous les champs de la ligne de frais hors forfait sous la forme
     * d'un tableau associatif
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
          return $laLigne;
    }

    
    
    /**
     * 
     * Fonction pour test unitaire
     * 
     * tableau associatif de la dernière ligne de frais
     * hors forfait insérée concernées par les deux paramètres.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs de la ligne de frais hors forfait sous la forme
     * d'un tableau associatif
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
          return $laLigne;
    }

    
    
    
    
    /**
     * Fonction pour test unitaire
     * 
     * Met à jour d'une ligneFraisHorsForfait dont l'id est passé en paramètre avec le nouveau libelle non introduit par 'REFUSE-'  
     * et le champ refuse changé à false
     *
     * @param String $id ID du frais hors forfait
     * @param String $libelle libelle du frais hors forfait
     *
     * @return null
     */
    public function accepterUnFraisHorsForfait($id, $libelle)
    {
       
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.libelle = :unLibelle, '
            . 'lignefraishorsforfait.refuse = NULL '     
            . 'WHERE lignefraishorsforfait.id = :unId '

        );
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_INT);
        $requetePrepare->execute();
        
    }
    
    
    /**
     * Fonction pour test unitaire
     * 
     * Supprime la fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
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
?>
