<?php
/**
 * Validation des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Namik TIAB <tiabnamik@gmail.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$idVisiteur = $_SESSION['idVisiteur'];
$mois = getMoisPrecedent(date('d/m/Y'));
//cloturation des fiches de frais du mois en cours 
$pdo->clotureFichesFrais($mois);

/*
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
*/

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) {

    
  
case 'selectionnerVisiteursMois':    
    if(estJourComprisDansIntervalle(date('d/m/Y'),10,20)){
        
         $lesVisiteurs = $pdo->getLesVisiteurs();
        // Afin de sélectionner par défaut le premier visiteur dans la zone de liste
        // on demande toutes les clés, et on prend la première,
        // les visiteur étant triés croissants par ordre alphabétique
        
       
        //$lesCles = array_keys($lesVisiteurs);
        $visiteurASelectionner = $lesVisiteurs[0];
       // echo $visiteurASelectionner;
        
        
        
        
        include 'vues/v_listeVisiteursMois.php';
      
    } else {
        
        ajouterErreur('Veuillez attendre d\'être entre le 10 et le 20 du mois pour lancer la campagne de validation');
        include 'vues/v_erreurs.php';
        
    }
    break;
/*
   case 'selectionnerMois':

    

    break;

 * 
 *  */
case 'consulterFrais':
   
  
    
    
    /*
    if ($pdo->estPremierFraisMois($idVisiteur, $mois)) {
        $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
    } 
    */
   
    break;
case 'validerMajFraisForfait':
    $lesFrais
        = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
    if (lesQteFraisValides($lesFrais)) {
        $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
    } else {
        ajouterErreur('Les valeurs des frais doivent être numériques');
        include 'vues/v_erreurs.php';
    }
    break;

case 'supprimerFrais':
    $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
    $pdo->supprimerFraisHorsForfait($idFrais);
    
  
  
}

/*
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
require 'vues/v_listeFraisForfait.php';
require 'vues/v_listeFraisHorsForfait.php';
*/