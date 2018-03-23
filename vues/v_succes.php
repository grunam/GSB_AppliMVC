<?php
/**
 * Vue Succès
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
<div class="alert alert-success" role="alert">
    <?php
    foreach ($_REQUEST['succes'] as $succes) {
        echo '<p>' . htmlspecialchars($succes) . '</p>';
    }
    ?>
</div>
