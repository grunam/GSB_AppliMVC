<?php
/**
 * Vue suivre le paiement des fiches de Frais
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
    <div class="panel-heading">
    <?php
    if ($idEtat == 'VA') {
        echo 'Mettre en paiement la fiche';
    } elseif ($idEtat == 'MP') {
        echo 'Rembourser la fiche';
    }
    ?>    
    </div>
    <form method="post" action="index.php?uc=paiementFrais&action=paiementFrais">
        <input type="hidden" name="hdLeVisiteur" value="<?php echo $idVisiteur ?>">
        <input type="hidden" name="hdLeMois" value="<?php echo $moisASelectionner ?>">
        <input type="hidden" name="hdEtat" value="<?php echo $idEtat ?>">
        <table class="table table-bordered table-responsive">
        <tr>
            <td class="text-center">
                <button class="btn btn-success" type="submit">
                <?php
                if ($idEtat == 'VA') {
                    echo 'Mettre en paiement';
                } elseif ($idEtat == 'MP') {
                    echo 'Rembourser';
                }
                ?>
                </button>
            </td>
        </tr>
        </table>
    </form>
</div>
