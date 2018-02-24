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
        <div class="panel-heading" class="text-center">
        <?php
        if($idEtat == 'VA'){
            echo "Mettre en paiement la fiche";
        } else if($idEtat == 'MP'){ 
            echo "Rembourser la fiche";
        }    
        ?>    
        </div>
             <form method="post" action="index.php?uc=paiementFrais&action=paiementFrais" role="form">
                <input type="hidden" name="leVisiteur" value="<?php echo $idVisiteur ?>">
                <input type="hidden" name="leMois" value="<?php echo $moisASelectionner ?>">
                <input type="hidden" name="etat" value="<?php echo $idEtat ?>">
                
               
                <table class="table table-bordered table-responsive">
                <tr>
                    <td class="text-center">
                        <button class="btn btn-success" type="submit">
                        <?php
                        if($idEtat == 'VA'){
                            echo "Mettre en paiement";
                        } else if($idEtat == 'MP'){ 
                            echo "Rembourser";
                        }    
                        ?>
                        </button>
                    </td>
                </tr>
                </table>
             </form>
        </div>
    </div>   
