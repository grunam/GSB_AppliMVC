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
        <div class="panel-heading" class="text-center">Valider la fiche</div>
             <form method="post" action="index.php?uc=validerFrais&action=validerFrais" role="form">
                <input type="hidden" name="leVisiteur" value="<?php echo $idVisiteur ?>">
                <input type="hidden" name="leMois" value="<?php echo $moisASelectionner ?>">
                <input type="hidden" name="leNbFraisHorsForfait" value="<?php echo $nbFraisHorsForfait ?>">
    
                <table class="table table-bordered table-responsive">
                
                <?php
                if($nbFraisHorsForfait > 0)
                {
                ?>
                    
                <tr>
                    <th class="col-md-4">Nombre de justificatifs des frais hors forfaits reçus</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                
                <tr>
                    <td> 
                
                        <input type="number" id="idNbJustificatif" 
                               name="nbJustificatif" min="0" step="1"
                               size="10" maxlength="5" 
                               value="" 
                               class="form-control">
                  
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                
                </tr>
                
                <?php
                }
                ?>
            
                <tr>
                    <td colspan="4" class="text-center">
                        <button class="btn btn-success" type="submit">Valider</button>
                         
                    </td>
                </tr>
                </table>
             </form>
        </div>
    </div>   

