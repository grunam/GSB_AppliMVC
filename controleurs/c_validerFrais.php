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


$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

$monControleur = "validerFrais";


    
switch ($action) {

case 'selectionnerVisiteursMois':    
    
    $include_array = array();
    
    if(estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)){
        
        try {   
             //cloturation des fiches de frais du mois en cours
            $mois = getMoisPrecedent(date('d/m/Y'));
            $pdo->cloturerFichesFrais($mois);
      
            $numAnneePrecedente = substr($mois, 0, 4);
            $numMoisPrecedent = substr($mois, 4, 2);
            $labelMois = $numMoisPrecedent."-".$numAnneePrecedente; 
            $labelCloturation = 'Les fiches du mois précédent du '.$labelMois.' sont cloturées.';
            
            ajouterSucces($labelCloturation);
            array_push($include_array, 'vues/v_succes.php');
        
         }catch(Exception $e){
            ajouterErreur($e->getMessage());
            array_push($include_array, 'vues/v_erreurs.php');
         }    
            
    }
    
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $visiteurASelectionner = $lesVisiteurs[0][0];
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($visiteurASelectionner);
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
   
    include 'vues/v_listeVisiteursMois.php';

    if(!estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
      
        ajouterErreur('La campagne de validation doit être réalisée entre le 10 et le 20 du mois suivant la saisie par les visiteurs !');
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

    break;

case 'consulterFrais':
   
    $visiteurASelectionner = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $idVisiteur = $visiteurASelectionner; 

    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur);
    $moisASelectionner = $leMois;
    
    /*
    $montant = $pdo->getLeMontantTotalFrais($visiteurASelectionner, $leMois);
    
    echo $montant;
    */
    
    include 'vues/v_listeVisiteursMois.php';
    
    
    if(!estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
            ajouterErreur('La campagne de validation doit être réalisée entre le 10 et le 20 du mois suivant la saisie par les visiteurs !');
            include 'vues/v_erreurs.php';  
    }

    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $idEtat = $lesInfosFicheFrais['idEtat'];
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    
    
    $nbFraisHorsForfait = 0;
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $refus = $unFraisHorsForfait['refuse'];
        if(!$refus){
            $nbFraisHorsForfait++;
        }
    }
    
  
    include 'vues/v_majFraisForfait.php';
    
    if(count($lesFraisHorsForfait) > 0){
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
    
    
   // if (lesQteFraisValides($lesFrais)) {
        
        
        $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
        $idVisiteur = $visiteurASelectionner; 
        $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur);
        $moisASelectionner = $leMois;
        $include_array = array();        
        
        include 'vues/v_listeVisiteursMois.php';
        
        
        try {
            $pdo->majFraisForfait($visiteurASelectionner, $leMois, $lesFrais);
            ajouterSucces('Mise à jour des frais forfaitaires effectuée.');
            array_push($include_array, 'vues/v_succes.php');
          
        }catch(Exception $e){
            ajouterErreur($e->getMessage());
            array_push($include_array, 'vues/v_erreurs.php');
        }

        if(!estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
            ajouterErreur('La campagne de validation doit être réalisée entre le 10 et le 20 du mois suivant la saisie par les visiteurs !');
            array_push($include_array, 'vues/v_erreurs.php');  
        }
        
        foreach ($include_array as $include) {
            include_once $include;
        }
        
        
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $idEtat = $lesInfosFicheFrais['idEtat'];
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        
        
        include 'vues/v_majFraisForfait.php';
        
        $nbFraisHorsForfait = 0;
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $refus = $unFraisHorsForfait['refuse'];
            if(!$refus){
                $nbFraisHorsForfait++;
            }
        }
    
        
        if(count($lesFraisHorsForfait) > 0){
            include 'vues/v_majFraisHorsForfait.php';
        }
        
        if($idEtat == 'CL'){
            include 'vues/v_validerFrais.php';
        }
    
    /*    
    } else {
        
        ajouterErreur('Les valeurs des frais doivent être numériques');
        include 'vues/v_erreurs.php';
        
    }*/
    break;

case 'supprimerFrais':
    
    $visiteurASelectionner = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_STRING);
    $FraisHorsForfait = filter_input(INPUT_POST, 'lesFraisHorsForfait', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
          

    //$actionFraisHorsForfait = filter_input(INPUT_POST, 'actionFraisHorsForfait', FILTER_SANITIZE_STRING);
   
    //echo json_encode($actionFraisHorsForfait);    
    //$lesCles = array_keys($idsFrais);
    /*
    foreach($idsFrais as $valeur)
    {
        echo "La checkbox $valeur a été cochée<br>";
    }
    */
    
   
    $idVisiteur = $visiteurASelectionner; 
    $include_array = array();
    $moisASelectionner = $leMois;
    
   
    $lesInfosFicheFraisMoisSuivant = $pdo->getLesInfosFicheFrais($idVisiteur, getMoisSuivant($leMois));
            
    try {
     
        
       /*
        $pdo->creeNouveauFraisHorsForfait(
        'a17',
        '201711',
        'Location salle conférence',
        '15/11/2017',
        320
        );
       */

        //vol aller-retour Paris-Bordeaux
        //restaurant gastronomique
        //garagiste

        //$pdo->creeNouveauFraisHorsForfait($idVisiteur, $leMois, 'garagiste', '31/10/2017', '150' );        

        //echo getMoisSuivant($leMois);

     
        $nbre = (count($FraisHorsForfait) > 1)?'s':'';
        $atc = (count($FraisHorsForfait) > 1)?'des':'du';
        
        
        if(isset($FraisHorsForfait)){
            if(empty($lesInfosFicheFraisMoisSuivant['idEtat']) || $lesInfosFicheFraisMoisSuivant['idEtat']=='CR' || $lesInfosFicheFraisMoisSuivant['idEtat']=='CL'){
                $pdo->refuserLesFraisHorsForfait($FraisHorsForfait, getMoisSuivant($leMois)); 
                ajouterSucces('Suppression '.$atc.' frais hors forfait'.$nbre.' effectuée.');
                array_push($include_array, 'vues/v_succes.php');
            } else {
                ajouterErreur('Suppression '.$atc.' frais hors forfait'.$nbre.' impossible, la fiche de frais du mois suivant a déjà été validé !');
                array_push($include_array, 'vues/v_erreurs.php');
            }
        }
       
     
    /*    
    $lesInfosFicheFraisMoisSuivant = $pdo->getLesInfosFicheFraisPremierMoisSuivant($idVisiteur, $leMois);

    try {

       
        $nbre = (count($FraisHorsForfait) > 1)?'s':'';
        $atc = (count($FraisHorsForfait) > 1)?'des':'du';

        $moisValide = (isset($lesInfosFicheFraisMoisSuivant['mois']))?$lesInfosFicheFraisMoisSuivant['mois']:getMoisSuivant($leMois);

        if(isset($FraisHorsForfait)){
            if(empty($lesInfosFicheFraisMoisSuivant['idEtat']) || $lesInfosFicheFraisMoisSuivant['idEtat']=='CR' || $lesInfosFicheFraisMoisSuivant['idEtat']=='CL'){
                $pdo->refuserLesFraisHorsForfait($FraisHorsForfait, $moisValide); 
                ajouterSucces('Suppression '.$atc.' frais hors forfait'.$nbre.' effectuée.');
                array_push($include_array, 'vues/v_succes.php');
            } else {
                ajouterErreur('Suppression '.$atc.' frais hors forfait'.$nbre.' impossible, la fiche de frais du mois suivant a déjà été validé !');
                array_push($include_array, 'vues/v_erreurs.php');
            }
        }
     */

    }catch(Exception $e){
        ajouterErreur($e->getMessage());
        array_push($include_array, 'vues/v_erreurs.php');
    }
    
    
    if(!estJourComprisDansIntervalle(date('d/m/Y'), 10, 20)) {
        ajouterErreur('La campagne de validation doit être réalisée entre le 10 et le 20 du mois suivant la saisie par les visiteurs !');
        array_push($include_array, 'vues/v_erreurs.php');    
    }
    
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur); 
    include 'vues/v_listeVisiteursMois.php'; 
  
    
    
    foreach ($include_array as $include) {
        include_once $include;
    }
    
    
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $idEtat = $lesInfosFicheFrais['idEtat'];
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);


    include 'vues/v_majFraisForfait.php';
    
    $nbFraisHorsForfait = 0;
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $refus = $unFraisHorsForfait['refuse'];
        if(!$refus){
            $nbFraisHorsForfait++;
        }
    }
    
    
    include 'vues/v_majFraisHorsForfait.php';
    
    if($idEtat == 'CL'){
        include 'vues/v_validerFrais.php';
    }
    
    break;
    
case 'validerFrais' : 

    $visiteurASelectionner = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_STRING);
    $justificatif = filter_input(INPUT_POST, 'nbJustificatif', FILTER_SANITIZE_STRING);
    $nbFraisHorsForfait = filter_input(INPUT_POST, 'leNbFraisHorsForfait', FILTER_SANITIZE_STRING);
    
    
    //$actionFraisHorsForfait = filter_input(INPUT_POST, 'actionFraisHorsForfait', FILTER_SANITIZE_STRING);
   
    //echo json_encode($actionFraisHorsForfait);    
    //$lesCles = array_keys($idsFrais);
    /*
    foreach($idsFrais as $valeur)
    {
        echo "La checkbox $valeur a été cochée<br>";
    }
    */
    
    $lesVisiteurs = $pdo->getLesVisiteursValidationFichesFrais();
    $idVisiteur = $visiteurASelectionner; 
    //$montantTotalFraisHorsForfait = $pdo->getLeMontantTotalFraisHorsForfait($idVisiteur, $leMois);
    $include_array = array();
    
    $nbJustif = (empty($justificatif))?0:$justificatif;
    
    
    
    if($nbJustif >= $nbFraisHorsForfait) {
        
        try {
            $pdo->montantTotalFrais($visiteurASelectionner, $leMois); 
        }catch(Exception $e){
           ajouterErreur($e->getMessage());
           array_push($include_array, 'vues/v_erreurs.php');
        }
    }
    
    try {
     
        
      /*  
        $pdo->creeNouveauFraisHorsForfait(
        'b25',
        '201710',
        'Location salle de conférence',
        '28/10/2017',
        300
        );
      */
        
        //vol aller-retour Paris-Bordeaux
        //restaurant gastronomique
        //garagiste
        
        //$pdo->creeNouveauFraisHorsForfait($idVisiteur, $leMois, 'garagiste', '31/10/2017', '150' );        

        //echo getMoisSuivant($leMois);
        
        ($nbFraisHorsForfait > 1)?$nbre='s':$nbre='';
        
        
        if($nbJustif < $nbFraisHorsForfait) {
           
             ajouterErreur('Veuillez indiquer au moins '.$nbFraisHorsForfait.' justificatif'.$nbre.' pour le'.$nbre.' frais hors forfait !');
             array_push($include_array, 'vues/v_erreurs.php');
             
        } else if($nbJustif >= $nbFraisHorsForfait) {
            
             $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'VA', $justificatif);
             ajouterSucces('La fiche de frais est validée.');
             array_push($include_array, 'vues/v_succes.php');
             
        }
   
       
        //include 'vues/v_succes.php';

        

    }catch(Exception $e){
        ajouterErreur($e->getMessage());
        array_push($include_array, 'vues/v_erreurs.php');
    }

    
     if(!estJourComprisDansIntervalle(date('d/m/Y'), 10, 20) && (getMoisSuivant($leMois)!=getMois(date('d/m/Y')))) {
           ajouterErreur('La campagne de validation doit être réalisée entre le 10 et le 20 du mois suivant la saisie par les visiteurs !');
           array_push($include_array, 'vues/v_erreurs.php');
     }
    

     
    $lesMois = $pdo->getLesMoisDisponiblesValidationFichesFrais($idVisiteur);
    $moisASelectionner = $leMois;
    include 'vues/v_listeVisiteursMois.php'; 
    
    
    foreach ($include_array as $include) {
        include_once $include;
    }

    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $idEtat = $lesInfosFicheFrais['idEtat'];
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);

    include 'vues/v_majFraisForfait.php';
   
    $nbFraisHorsForfait = 0;
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $refus = $unFraisHorsForfait['refuse'];
        if(!$refus){
            $nbFraisHorsForfait++;
        }
    }
    
    
    if(count($lesFraisHorsForfait) > 0){
        include 'vues/v_majFraisHorsForfait.php';
    }
    if($idEtat == 'CL'){
        include 'vues/v_validerFrais.php';
    }
    
    //break;
    
}