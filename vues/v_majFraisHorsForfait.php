<?php
/**
 * Vue Valider fiche de Frais
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

<div class="panel panel-info">
    <div class="panel-heading">Descriptif des éléments hors forfait - 
        <?php echo $nbJustificatifs ?> justificatifs reçus</div>
    
   <form id="FormFraisHorsForfait" method="post" action="index.php?uc=<?php echo $monControleur ?>&action=supprimerFrais" role="form">
     
    <input type="hidden" name="leVisiteur" value="<?php echo $idVisiteur ?>">
    <input type="hidden" name="leMois" value="<?php echo $moisASelectionner ?>">
    
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
                    <input type="checkbox" name="lesFraisHorsForfait[]" value="<?php echo $idFrais ?>" onclick="javascript:if(this.checked){return confirm('Voulez-vous vraiment supprimer ce frais?')};">
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
        
        
        if(!empty($lesFraisHorsForfait) && $idEtat == 'CL') {
           
        ?>
      
            <tr>
                <td class="text-center" colspan="4">
                
                    <button class="btn btn-success" type="submit">Supprimer</button>
                    <button class="btn btn-danger" type="reset">Effacer</button> 
                    
                </td>
            </tr>
            
        
        <?php
        }
        ?>
            
    </table>
    
   </form>
</div>
