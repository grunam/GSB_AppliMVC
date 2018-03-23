<?php
/**
 * Gestion des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
$idVisiteur = $_SESSION['idVisiteur'];
$mois = Utils::getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) {
    case 'saisirFrais':
        if ($pdo->estPremierFraisMois($idVisiteur, $mois)) {
            $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
        }
        break;
    case 'validerMajFraisForfait':
        $lesFrais = filter_input(INPUT_POST, 'txtLesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        try {
            if (Utils::lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
                Utils::ajouterSucces('Mise à jour des frais forfaitaires effectuée.');
                include 'vues/v_succes.php';
            } else {
                Utils::ajouterErreur('Les valeurs des frais doivent être numériques');
                include 'vues/v_erreurs.php';
            }
        } catch (Exception $e) {
            Utils::ajouterErreur($e->getMessage());
            include 'vues/v_erreurs.php';
        }
        break;
    case 'validerCreationFrais':
        $dateFrais = filter_input(INPUT_POST, 'txtDateHF', FILTER_SANITIZE_STRING);
        $libelle = filter_input(INPUT_POST, 'txtLibelleHF', FILTER_SANITIZE_STRING);
        $montant = filter_input(INPUT_POST, 'txtMontantHF', FILTER_VALIDATE_FLOAT);
        Utils::valideInfosFrais($dateFrais, $libelle, $montant);
        try {
            if (Utils::nbErreurs() != 0) {
                include 'vues/v_erreurs.php';
            } else {
                $pdo->creeNouveauFraisHorsForfait(
                    $idVisiteur,
                    $mois,
                    $libelle,
                    $dateFrais,
                    $montant
                );
                Utils::ajouterSucces('Création de frais hors forfait effectuée.');
                include 'vues/v_succes.php';
            }
        } catch (Exception $e) {
            Utils::ajouterErreur($e->getMessage());
            include 'vues/v_erreurs.php';
        }
        break;
    case 'supprimerFrais':
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
        try {
            $pdo->supprimerFraisHorsForfait($idFrais);
            Utils::ajouterSucces('Suppression du frais hors forfait effectuée.');
            include 'vues/v_succes.php';
        } catch (Exception $ex) {
            Utils::ajouterErreur($e->getMessage());
            include 'vues/v_erreurs.php';
        }
        break;
}
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
require 'vues/v_listeFraisForfait.php';
require 'vues/v_listeFraisHorsForfait.php';
