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
<script src="./script/jquery-3.3.1.min.js"></script>
<script src="./script/script.js"></script>
<script src="./node_modules/date-input-polyfill/date-input-polyfill.dist"></script>
<h2>
    <?php
    if ($monControleur == 'validerFrais') {
        echo 'Valider une fiche de frais';
    } elseif ($monControleur == 'paiementFrais') {
        echo 'Suivre le paiement des fiches de frais';
    }
    ?>
</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un visiteur médical puis un mois :</h3>
    </div>
    <div class="col-md-4">
        <form id="frmListeVisiteursMois" action="index.php?uc=<?php echo $monControleur ?>&action=consulterFrais" 
              method="post">
          <div class="form-group">
                <input type="hidden" id="hdMonControleur" name="hdMonControleur" value="<?php echo $monControleur ?>">
                <label for="lstVisiteurs" accesskey="n">Visiteurs médical : </label>
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control"> 
                <?php
                foreach ($lesVisiteurs as $unVisiteur) {
                    $nomVisiteur = $unVisiteur['nom'];
                    $prenomVisiteur = $unVisiteur['prenom'];
                    $adresseVisiteur = $unVisiteur['adresse'];
                    $villeVisiteur = $unVisiteur['ville'];
                    $cpVisiteur = $unVisiteur['cp'];
                    $LigneVisiteurMedical = $unVisiteur['nom']
                            ." ".$unVisiteur['prenom']." ".$unVisiteur['adresse']
                            ." ".$unVisiteur['cp']." ".$unVisiteur['ville'];
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
            <input id="cmdOkVisiteursMois" type="submit" value="Consulter" class="btn btn-success">
            <input id="brAnnulerVisiteursMois" type="reset" value="Effacer" class="btn btn-danger">
            <br><br>
        </form>
    </div>
</div>
