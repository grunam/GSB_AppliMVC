<?php
/**
 * Index du projet GSB
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
require_once 'includes/class.utils.inc.php';
require_once 'includes/class.pdogsb.inc.php';
session_start();
$pdo = PdoGsb::getPdoGsb();
/*
 $login = 'piepont';
 $mdp = 'zaza99';
 //$login = 'dandre';
 //$mdp = 'oppg5';
 $visiteur = $pdo->getInfosVisiteur($login, $mdp);
 $id = $visiteur['id'];
 $nom = $visiteur['nom'];
 $prenom = $visiteur['prenom'];
 $comptable = $visiteur['comptable'];
 Utils::connecter($id, $nom, $prenom, $comptable); 
 */
$estConnecte = Utils::estConnecte();
if ($estConnecte) {
    $estComptable = Utils::estComptable();
}
if ($estConnecte) {
    if ($estComptable) {
        require 'vues/v_enteteComptables.php';
    } else {
        require 'vues/v_enteteVisiteursMedicaux.php';
    }
} else {
    require 'vues/v_enteteConnexion.php';
}
require_once 'includes/class.urlchecker.inc.php';
$errorUrl = false;
if (isset($_SESSION['idVisiteur'])) {
    $errorUrl = Urlchecker::paramChecker($_SESSION['idVisiteur'], $pdo);
}
$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING);
if ($uc && !$estConnecte) {
    $uc = 'connexion';
} elseif ($errorUrl) {
    $uc = 'erreur';
} elseif (empty($uc)) {
    $uc = 'accueil';
}
switch ($uc) {
    case 'connexion':
        include 'controleurs/c_connexion.php';
        break;
    case 'erreur':
        include 'controleurs/c_erreurUrl.php';
        break;
    case 'accueil':
        include 'controleurs/c_accueil.php';
        break;
    case 'gererFrais':
        include 'controleurs/c_gererFrais.php';
        break;
    case 'etatFrais':
        include 'controleurs/c_etatFrais.php';
        break;
    case 'validerFrais':
        include 'controleurs/c_validerFrais.php';
        break;
    case 'paiementFrais':
        include 'controleurs/c_paiementFrais.php';
        break;
    case 'deconnexion':
        include 'controleurs/c_deconnexion.php';
        break;
}
require 'vues/v_pied.php';
