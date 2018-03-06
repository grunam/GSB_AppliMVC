<?php
/**
 * Vue modifier les frais forfaitaires
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

<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés</div>
    
 <form method="post" action="index.php?uc=<?php echo $monControleur ?>&action=validerMajFraisForfait" role="form">
   
    <input type="hidden" name="leVisiteur" value="<?php echo $idVisiteur ?>">
    <input type="hidden" name="leMois" value="<?php echo $moisASelectionner ?>">
    
    <table class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle']; ?>
                <th> <?php echo $libelle ?></th>
                <?php
            }
            ?>
               
        </tr>
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $idFrais = $unFraisForfait['idfrais'];
                $quantite = $unFraisForfait['quantite'];
                ?>
       
            <td>

                <?php
                
                //echo $idEtat;
                
                if($idEtat == 'CL'){
                
                    
                ?>
                    
                <div class="form-group">
                    <!--<label for="idFrais"><?php /*echo $libelle*/ ?></label>-->
                    <input type="number" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" min="0" step="1"
                               value="<?php echo $quantite ?>" 
                               class="form-control" required>
                </div>

                <?php
                
                } else { 
                ?>
                   <?php echo $quantite ?> 
                    
                <?php   
                }
                ?>
                
             </td>
             
            <?php   
            }
            ?>
             
             
             
             
        </tr>
            <?php   
            if($idEtat == 'CL'){
            ?>
        <tr>
            <td class="text-center" colspan="4">
                
                <button class="btn btn-success" type="submit">Modifier</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
                
            </td>
        </tr>
            
            <?php   
            }
            ?>
    </table>
     
          
 </form>
     
</div>


