<?php
/**
 * Vue Liste des mois
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
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

 

<h2>Valider une fiche de frais</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un visiteur médical puis un mois : </h3>
    </div>
    <div class="col-md-4">
       
        <form action="index.php?uc=validerFrais&action=consulterFrais" 
              method="post" role="form">
          <div class="form-group">
                
                
                  
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
                        
                        if ($unVisiteur["id"] == $visiteurASelectionner["id"]) {
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
            <input id="ok" type="submit" value="Valider" class="btn btn-success" 
                   role="button">
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" 
                   role="button">
        </form>
    </div>
</div>