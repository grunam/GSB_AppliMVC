<?php
/**
 * Gestion affichage de la vue en cas d'erreur de paramètre d'url
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
Utils::ajouterErreur('Une erreur est survenue, la chaîne de paramètres passés dans l\'url est incorrecte !');
include 'vues/v_erreurs.php';
?>