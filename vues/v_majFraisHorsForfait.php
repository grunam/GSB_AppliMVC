<?php
/**
 * Vue modifier les frais hors forfait
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
    <div class="panel-heading">Descriptif des éléments hors forfait - 
        <?php echo $nbJustificatifs ?> justificatifs reçus</div>
    
   <form id="FormFraisHorsForfait" method="post" action="index.php?uc=<?php echo $monControleur ?>&action=modifierFraisHorsForfait" role="form">
     
    <input type="hidden" name="leVisiteur" value="<?php echo $idVisiteur ?>">
    <input type="hidden" name="leMois" value="<?php echo $moisASelectionner ?>">
    <input id="valModFraisHorsForfait" type="hidden" name="valModFraisHorsForfait" value="">
    <input id="FraisHorsForfait" type="hidden" name="FraisHorsForfait" value="">
    
    
    
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class="montant">Montant</th>
             <?php   
             if($idEtat == 'CL'){
             ?>
            <th>Sélection</th>
            <?php   
            }
            ?>
        </tr>
        <?php
        
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            
           
            $date = date($unFraisHorsForfait['date']);
            
            $libelle = $unFraisHorsForfait['libelle'];
            $montant = $unFraisHorsForfait['montant'];
            $idFrais = $unFraisHorsForfait['id'];
            $refus = $unFraisHorsForfait['refuse'];
            ?>
            <tr>
                <?php   
                if($idEtat == 'CL' && $refus == 0){
                    //mb_convert_encoding($libelle, 'latin1_swedish_ci', 'UTF-8')
                ?>
                
                <td>
                
                    <input type="date" 
                               name="txtDateHF<?php echo $idFrais ?>"
                               id="txtDateHF<?php echo $idFrais ?>"
                               value="<?php echo $date ?>" 
                               class="form-control" required>
                
                </td>
                <td>
                    <input type="text" id="txtLibelleHF<?php echo $idFrais ?>" name="txtLibelleHF<?php echo $idFrais ?>" 
                       class="form-control" value="<?php echo $libelle ?>" required>
                
                </td>
                <td>
                    <input id="txtMontantHF<?php echo $idFrais ?>" type="number" step="0.05" name="txtMontantHF<?php echo $idFrais ?>" 
                           class="form-control" value="<?php echo $montant ?>" required>    
                
                </td>
               
                <?php 
                  } else {
                ?>  
                
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
                
                <?php 
                  } 
                 
                if($idEtat == 'CL'){
                    //mb_convert_encoding($libelle, 'latin1_swedish_ci', 'UTF-8')
                ?>
                <td>
                <?php   
                  if(!$refus){
                      
                ?>
                    <input type="checkbox" name="lesFraisHorsForfait[]" value="<?php echo $idFrais ?>">
                <?php 
                  } 
                ?>  
                </td>
                <?php   
                }
                ?>
            </tr>
           
            
            <?php
        }
        
        if($nbFraisHorsForfait > 0 && $idEtat == 'CL') {
           
        ?>
      
            <tr>
                <td class="text-center" colspan="4">
                    
                    <button onclick="javascript:return confirm('Voulez-vous vraiment modifier ce(s) frais?');" id="modifierFraisHorsForfait" class="btn btn-success" type="button">Modifier</button>
                    <button onclick="javascript:return confirm('Voulez-vous vraiment reporter ce(s) frais?');" id="reporterFraisHorsForfait" class="btn btn-success" type="button">Reporter</button>    
                    <button onclick="javascript:return confirm('Voulez-vous vraiment supprimer ce(s) frais?');" id="supprimerFraisHorsForfait" class="btn btn-success" type="button">Supprimer</button>
                    <button class="btn btn-danger" type="reset">Effacer</button> 
                    
                </td>
            </tr>
            
        
        <?php
        }
        ?>
            
    </table>
    
   </form>
</div>
