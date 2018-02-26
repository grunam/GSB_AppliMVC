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
    <input id="modFraisHorsForfait" type="hidden" name="modFraisHorsForfait" value="">
    
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
            
            $date = $unFraisHorsForfait['date'];
            $libelle = $unFraisHorsForfait['libelle'];
            $montant = $unFraisHorsForfait['montant'];
            $idFrais = $unFraisHorsForfait['id'];
            $refus = $unFraisHorsForfait['refuse'];
            ?>
            <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
                <?php   
                if($idEtat == 'CL'){
                    //mb_convert_encoding($libelle, 'latin1_swedish_ci', 'UTF-8')
                ?>
                <td>
                <?php   
                  if(!$refus){
                      
                ?>
                    <input type="checkbox" name="lesFraisHorsForfait[]" value="<?php echo $idFrais ?>" onclick="javascript:if(this.checked){return confirm('Voulez-vous vraiment modifier ce frais?')};">
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
                    <button id="reporterFraisHorsForfait" class="btn btn-success" type="button">Reporter</button>    
                    <button id="supprimerFraisHorsForfait" class="btn btn-success" type="button">Supprimer</button>
                    <button class="btn btn-danger" type="reset">Effacer</button> 
                    
                </td>
            </tr>
            
        
        <?php
        }
        ?>
            
    </table>
    
   </form>
</div>
