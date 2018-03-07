<?php
/**
 * Validation des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    Namik TIAB <tiabnamik@gmail.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */


$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$monControleur = 'validerFrais';


switch ($action) {

case 'selectionnerVisiteursMois':    
    
    $include_array = array();
    
    if (Utils::estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
        
        try {   
             //cloturation des fiches de frais du mois en cours
            $mois = Utils::getMoisPrecedent(date('d/m/Y'));
            $pdo->cloturerFichesFrais($mois);
      
            $numAnneePrecedente = substr($mois, 0, 4);
            $numMoisPrecedent = substr($mois, 4, 2);
            $labelMois = $numMoisPrecedent.'-'.$numAnneePrecedente; 
            $labelCloturation = 'Les fiches du mois précédent du '.$labelMois.' sont cloturées.';
            
            Utils::ajouterSucces($labelCloturation);
            array_push($include_array, 'vues/v_succes.php');
        
         } catch(Exception $e) {
            Utils::ajouterErreur($e->getMessage());
            array_push($include_array, 'vues/v_erreurs.php');
         }    
            
    }
    
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $visiteurASelectionner = $lesVisiteurs[0][0];
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($visiteurASelectionner);
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
   
    include 'vues/v_listeVisiteursMois.php';

    if (!Utils::estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
      
        Utils::ajouterErreur(
            'La campagne de validation doit être réalisée'
            . ' entre le 10 et le 20 du mois suivant '
            . 'la saisie par les visiteurs !'
        );
        array_push($include_array, 'vues/v_erreurs.php');     
    }
    
     foreach ($include_array as $include) {
        include_once $include;
    }
    
    break;
    
case 'selectionnerVisiteur':    
    
    $idLstVisiteur = filter_input(INPUT_GET, 'idLstVisiteur', FILTER_SANITIZE_STRING);
    $visiteurASelectionner = $idLstVisiteur; 
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($visiteurASelectionner);
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
    include 'vues/v_listeVisiteursMois.php';

    if (!Utils::estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
      
        Utils::ajouterErreur(
            'La campagne de validation doit être réalisée'
            . ' entre le 10 et le 20 du mois suivant '
            . 'la saisie par les visiteurs !'
        );
        include 'vues/v_erreurs.php';     
    }
    
    
    break;

case 'consulterFrais':
   
    $visiteurASelectionner = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $idVisiteur = $visiteurASelectionner; 

    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur);
    $moisASelectionner = $leMois;
   
    include 'vues/v_listeVisiteursMois.php';
    
    if (!Utils::estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
            Utils::ajouterErreur(
                'La campagne de validation doit être réalisée'
                . ' entre le 10 et le 20 du mois suivant '
                . 'la saisie par les visiteurs !'
            );
            include 'vues/v_erreurs.php';  
    }

    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois, 0);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $idEtat = $lesInfosFicheFrais['idEtat'];
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = Utils::dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    
    
    $nbFraisHorsForfait = 0;
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $refus = $unFraisHorsForfait['refuse'];
        if (!$refus) {
            $nbFraisHorsForfait++;
        }
    }
    
    include 'vues/v_etatFicheFrais.php';
    include 'vues/v_majFraisForfait.php';
    
    if (count($lesFraisHorsForfait) > 0) {
        include 'vues/v_majFraisHorsForfait.php';
    }
    
    if($idEtat == 'CL'){
        include 'vues/v_validerFrais.php';
    }
    

    break;
case 'validerMajFraisForfait':
    
    $visiteurASelectionner = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_STRING);
    $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
    
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $idVisiteur = $visiteurASelectionner; 
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur);
    $moisASelectionner = $leMois;
    $include_array = array();        

    include 'vues/v_listeVisiteursMois.php';

    try {
        
        if (Utils::lesQteFraisValides($lesFrais)) {
        
            $pdo->majFraisForfait($visiteurASelectionner, $leMois, $lesFrais);
            Utils::ajouterSucces('Mise à jour des frais forfaitaires effectuée.');
            array_push($include_array, 'vues/v_succes.php');
            
        } else {
            
            Utils::ajouterErreur('Les valeurs des frais doivent être numériques');
            array_push($include_array, 'vues/v_erreurs.php');
        }    
            

    } catch (Exception $e) {
        Utils::ajouterErreur($e->getMessage());
        array_push($include_array, 'vues/v_erreurs.php');
    }

    if (!Utils::estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
        Utils::ajouterErreur(
            'La campagne de validation doit être réalisée'
            . ' entre le 10 et le 20 du mois suivant '
            . 'la saisie par les visiteurs !'
        );
        array_push($include_array, 'vues/v_erreurs.php');  
    }

    foreach ($include_array as $include) {
        include_once $include;
    }


    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois, 0);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $idEtat = $lesInfosFicheFrais['idEtat'];
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = Utils::dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);

    include 'vues/v_etatFicheFrais.php';
    include 'vues/v_majFraisForfait.php';

    $nbFraisHorsForfait = 0;
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $refus = $unFraisHorsForfait['refuse'];
        if (!$refus) {
            $nbFraisHorsForfait++;
        }
    }


    if (count($lesFraisHorsForfait) > 0) {
        include 'vues/v_majFraisHorsForfait.php';
    }

    if ($idEtat == 'CL') {
        include 'vues/v_validerFrais.php';
    }
    
    
    break;

case 'modifierFraisHorsForfait':
    
    $visiteurASelectionner = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_STRING);
    $FraisHorsForfait = filter_input(INPUT_POST, 'FraisHorsForfait', FILTER_DEFAULT, FILTER_SANITIZE_STRING);
    $modFraisHorsForfait = filter_input(INPUT_POST, 'valModFraisHorsForfait', FILTER_SANITIZE_STRING);
     
    $idVisiteur = $visiteurASelectionner; 
    $include_array = array();
    $moisASelectionner = $leMois;
    
    $parseFraisHorsForfait = json_decode($FraisHorsForfait, true);
    
    $nbre = (count($parseFraisHorsForfait) > 1)? 's' : '';
    $atc = (count($parseFraisHorsForfait) > 1)? 'des' : 'du';
    
    if ($modFraisHorsForfait == 'modifier') {
    
        try {
            
            $pdo->majFraisHorsForfait($parseFraisHorsForfait); 
            Utils::ajouterSucces('Modification '.$atc.' frais hors forfait'.$nbre.' effectuée.');
            array_push($include_array, 'vues/v_succes.php');
                
        } catch (Exception $e) {
            Utils::ajouterErreur($e->getMessage());
            array_push($include_array, 'vues/v_erreurs.php');
        }
    
    } else if ($modFraisHorsForfait == 'reporter') {
        
        try {
            
            $lesInfosFicheFraisMoisSuivant = $pdo->getLesInfosFicheFrais($idVisiteur, Utils::getMoisSuivant($leMois));
    
            if (isset($parseFraisHorsForfait)) {
                if (empty($lesInfosFicheFraisMoisSuivant['idEtat']) || $lesInfosFicheFraisMoisSuivant['idEtat']=='CR' 
                        || $lesInfosFicheFraisMoisSuivant['idEtat']=='CL') {
                    $pdo->reporterLesFraisHorsForfait($parseFraisHorsForfait, Utils::getMoisSuivant($leMois)); 
                    Utils::ajouterSucces('Report '.$atc.' frais hors forfait'.$nbre.' effectuée.');
                    array_push($include_array, 'vues/v_succes.php');
                } else {
                    Utils::ajouterErreur('Report '.$atc.' frais hors forfait'.$nbre
                            .' impossible, la fiche de frais du mois suivant a déjà été validé !');
                    array_push($include_array, 'vues/v_erreurs.php');
                }
            }


        } catch (Exception $e) {
            Utils::ajouterErreur($e->getMessage());
            array_push($include_array, 'vues/v_erreurs.php');
        }
        
    } else if ($modFraisHorsForfait == 'supprimer') {
        
        try {

            $pdo->refuserLesFraisHorsForfait($parseFraisHorsForfait, Utils::getMoisSuivant($leMois)); 
            Utils::ajouterSucces('Suppression '.$atc.' frais hors forfait'.$nbre.' effectuée.');
            array_push($include_array, 'vues/v_succes.php');
                
        } catch (Exception $e) {
            Utils::ajouterErreur($e->getMessage());
            array_push($include_array, 'vues/v_erreurs.php');
        }
        
    }
    
    if (!Utils::estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
        Utils::ajouterErreur(
            'La campagne de validation doit être réalisée'
            . ' entre le 10 et le 20 du mois suivant '
            . 'la saisie par les visiteurs !'
        );
        array_push($include_array, 'vues/v_erreurs.php');    
    }
    
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur); 
    include 'vues/v_listeVisiteursMois.php'; 
  
    
    foreach ($include_array as $include) {
        include_once $include;
    }
    
    
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois, 0);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $idEtat = $lesInfosFicheFrais['idEtat'];
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = Utils::dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);

    include 'vues/v_etatFicheFrais.php';
    include 'vues/v_majFraisForfait.php';
    
    $nbFraisHorsForfait = 0;
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $refus = $unFraisHorsForfait['refuse'];
        if (!$refus) {
            $nbFraisHorsForfait++;
        }
    }
    
    
    include 'vues/v_majFraisHorsForfait.php';
    
    if ($idEtat == 'CL') {
        include 'vues/v_validerFrais.php';
    }
    
    break;
    
case 'validerFrais' : 

    $visiteurASelectionner = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_STRING);
    $justificatif = filter_input(INPUT_POST, 'nbJustificatif', FILTER_SANITIZE_STRING);
    $nbFraisHorsForfait = filter_input(INPUT_POST, 'leNbFraisHorsForfait', FILTER_SANITIZE_STRING);
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $idVisiteur = $visiteurASelectionner; 
    
    $include_array = array();
    
    $nbJustif = (empty($justificatif))? 0 : $justificatif;
    
    if ($nbJustif >= $nbFraisHorsForfait) {
        
        $montantTotalFrais = '';
        
        try {
            $montantTotalFrais = $pdo->montantTotalFrais($visiteurASelectionner, $leMois);
        } catch (Exception $e) {
           Utils::ajouterErreur($e->getMessage());
           array_push($include_array, 'vues/v_erreurs.php');
        }
    }
    
    try {
     
        $nbre = ($nbFraisHorsForfait > 1)? 's' : '';
        
        if ($nbJustif < $nbFraisHorsForfait) {
           
             Utils::ajouterErreur('Veuillez indiquer au moins '.$nbFraisHorsForfait.' justificatif'
                     .$nbre.' pour le'.$nbre.' frais hors forfait !');
             array_push($include_array, 'vues/v_erreurs.php');
             
        } else if ($nbJustif >= $nbFraisHorsForfait) {
            
             $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'VA', $justificatif);
             Utils::ajouterSucces('La fiche de frais d\'un montant de '.$montantTotalFrais.'€ est validée.');
             array_push($include_array, 'vues/v_succes.php');
             
        }
   
       
    } catch (Exception $e) {
        Utils::ajouterErreur($e->getMessage());
        array_push($include_array, 'vues/v_erreurs.php');
    }

    
     if (!Utils::estJourComprisDansIntervalle(date('d/m/Y'), 10, 20) 
             && (Utils::getMoisSuivant($leMois) != Utils::getMois(date('d/m/Y')))) {
           Utils::ajouterErreur(
                'La campagne de validation doit être réalisée'
                . ' entre le 10 et le 20 du mois suivant '
                . 'la saisie par les visiteurs !'
            );
           array_push($include_array, 'vues/v_erreurs.php');
     }
    

     
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur);
    $moisASelectionner = $leMois;
    include 'vues/v_listeVisiteursMois.php'; 
    
    
    foreach ($include_array as $include) {
        include_once $include;
    }

    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois, 0);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $idEtat = $lesInfosFicheFrais['idEtat'];
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = Utils::dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);

    include 'vues/v_etatFicheFrais.php';    
    include 'vues/v_majFraisForfait.php';
   
    $nbFraisHorsForfait = 0;
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $refus = $unFraisHorsForfait['refuse'];
        if (!$refus) {
            $nbFraisHorsForfait++;
        }
    }
    
    
    if (count($lesFraisHorsForfait) > 0) {
        include 'vues/v_majFraisHorsForfait.php';
    }
    if($idEtat == 'CL'){
        include 'vues/v_validerFrais.php';
    }
    
    break;
    
}
