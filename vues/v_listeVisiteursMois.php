<?php
/**
 * Vue Liste des visiteurs et liste des mois
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
?>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>-->

<script src="./script/jquery-3.3.1.min.js" type="text/javascript"></script>
<script src="./script/script.js" type="text/javascript"></script>

<h2>
     <?php
            
        if($monControleur == 'validerFrais'){
            echo 'Valider une fiche de frais'; 
        } else if($monControleur == 'paiementFrais'){
            echo 'Suivre le paiement des fiches de frais'; 
        }
     ?>
    
    




</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un visiteur médical puis un mois :</h3>
    </div>
    <div class="col-md-4">
       
        <form action="index.php?uc=<?php echo $monControleur ?>&action=consulterFrais" 
              method="post" role="form">
          <div class="form-group">
                
                <input type="hidden" id="monControleur" name="monControleur" value="<?php echo $monControleur ?>">
                  
                <label for="lstVisiteurs" accesskey="n">Visiteurs médical : </label>
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control"> 
                   <?php
                   
                   foreach ($lesVisiteurs as $unVisiteur) {
                        
                        $nomVisiteur = $unVisiteur['nom'];
                        $prenomVisiteur = $unVisiteur['prenom'];
                        $adresseVisiteur = $unVisiteur['adresse'];
                        $villeVisiteur = $unVisiteur['ville'];
                        $cpVisiteur = $unVisiteur['cp'];
                        
                        
                        $LigneVisiteurMedical = $unVisiteur['prenom']." ".$unVisiteur['nom']." ".$unVisiteur['adresse']." ".$unVisiteur['cp']." ".$unVisiteur['ville'];
                        
                        if ($unVisiteur["id"] == $visiteurASelectionner) {
                            ?>
                            <option selected value="<?php echo $unVisiteur["id"] ?>">
                                <?php echo $LigneVisiteurMedical ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $unVisiteur["id"] ?>">
                                <?php echo $LigneVisiteurMedical ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
                
                
            </div>
            <div class="form-group">
                
                <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
                    
                    <?php
                    foreach ($lesMois as $unMois) {
                        $mois = $unMois['mois'];
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
                
            </div>
            
       
            <input id="okVisiteursMois" type="submit" value="Consulter" class="btn btn-success" 
                   role="button">
            <input id="annulerVisiteursMois" type="reset" value="Effacer" class="btn btn-danger" 
                   role="button">
            
            <br><br>
        </form>
    </div>
</div>