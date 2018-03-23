<?php
/**
 * Classe de vérification de l'url.
 * pour l'application GSB
 * Les fonctions sont toutes statiques
 *
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Namik TIAB <tiabnamik@gmail.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
class Urlchecker
{
    
    /**
     * Vérifie si les paramètres passés en url sont correctes, c'est à dire
     * vérifie si ils correspondent à ceux enregistrés dans un tableau multidimensionnel.
     *
     * @param String $idVisiteur id du visiteur
     * @param PdoGsb $pdo        instance de la classe PdoGsb
     *
     * @return Boolean vrai ou faux
     */
    public static function paramChecker($idVisiteur, $pdo)
    {
        $datas = array();
        $datas['uc'] = array();
        $datas['uc']['connexion'] = array();
        $datas['uc']['connexion']['action'] = array();
        $datas['uc']['connexion']['action']['valideConnexion'] = '';
        $datas['uc']['connexion']['action']['demandeConnexion'] = '';
        $datas['uc']['deconnexion'] = array();
        $datas['uc']['deconnexion']['action'] = array();
        $datas['uc']['deconnexion']['action']['demandeDeconnexion'] = '';
        $datas['uc']['deconnexion']['action']['valideDeconnexion'] = '';
        $datas['uc']['erreur'] = '';
        $datas['uc']['gererFrais'] = array();
        $datas['uc']['gererFrais']['action'] = array();
        $datas['uc']['gererFrais']['action']['saisirFrais'] = '';
        $datas['uc']['gererFrais']['action']['validerMajFraisForfait'] = '';
        $datas['uc']['gererFrais']['action']['validerCreationFrais'] = '';
        $datas['uc']['gererFrais']['action']['supprimerFrais'] = array();
        $datas['uc']['gererFrais']['action']['supprimerFrais']['idFrais'] = '';
        $datas['uc']['etatFrais'] = array();
        $datas['uc']['etatFrais']['action'] = array();
        $datas['uc']['etatFrais']['action']['selectionnerMois'] = '';
        $datas['uc']['etatFrais']['action']['voirEtatFrais'] = '';
        $datas['uc']['validerFrais'] = array();
        $datas['uc']['validerFrais']['action'] = array();
        $datas['uc']['validerFrais']['action']['selectionnerVisiteursMois'] = '';
        $datas['uc']['paiementFrais']['action']['selectionnerVisiteur'] = array();
        $datas['uc']['validerFrais']['action']['selectionnerVisiteur']['idLstVisiteur'] = '';
        $datas['uc']['validerFrais']['action']['consulterFrais'] = '';
        $datas['uc']['validerFrais']['action']['validerMajFraisForfait'] = '';
        $datas['uc']['validerFrais']['action']['modifierFraisHorsForfait'] = '';
        $datas['uc']['validerFrais']['action']['validerFrais'] = '';
        $datas['uc']['paiementFrais'] = array();
        $datas['uc']['paiementFrais']['action'] = array();
        $datas['uc']['paiementFrais']['action']['selectionnerVisiteursMois'] = array();
        $datas['uc']['paiementFrais']['action']['selectionnerVisiteur'] = array();
        $datas['uc']['paiementFrais']['action']['selectionnerVisiteur']['idLstVisiteur'] = '';
        $datas['uc']['paiementFrais']['action']['consulterFrais'] = '';
        $datas['uc']['paiementFrais']['action']['paiementFrais'] = '';
        $params = null;
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
        parse_str(parse_url($requestUri, PHP_URL_QUERY), $params);
        $errorUrl = false;
        if (!empty($params)) {
            $i0 = 0;
            $vi0 = 0;
            $i1 = 0;
            $vi1 = 0;
            $i2 = 0;
            $vi2 = 0;
            $i3 = 0;
            $vi3 = 0;
            $i4 = 0;
            $vi4 = 0;
            foreach ($datas as $kUc => $vUc) {
                $i0++;
                if (key($params) == $kUc) {
                    $vi0 = 1;
                    foreach ($datas[$kUc] as $kCt => $vCt) {
                        $i1++;
                        if (current($params) == $kCt) {
                            $vi1 = 1;
                            if ($datas[$kUc][$kCt]) {
                                next($params);
                                foreach ($datas[$kUc][$kCt] as $kAc => $vAc) {
                                    $i2++;
                                    if (key($params) == $kAc) {
                                        $vi2 = 1;
                                        if ($datas[$kUc][$kCt][$kAc]) {
                                            foreach ($datas[$kUc][$kCt][$kAc] as $kLac => $vLac) {
                                                $i3++;
                                                if (current($params) == $kLac) {
                                                    $vi3 = 1;
                                                    if ($datas[$kUc][$kCt][$kAc][$kLac]) {
                                                        next($params);
                                                        foreach ($datas[$kUc][$kCt][$kAc][$kLac] as $kId => $vId) {
                                                            if (is_array($datas[$kUc][$kCt][$kAc][$kLac])) {
                                                                $i4++;
                                                                if (key($params) == $kId) {
                                                                    $vi4 = 1;
                                                                    if (key($params) == 'idFrais' && 
                                                                            !$pdo->estUnFraisHorsForfait(current($params), $idVisiteur, Utils::getMois(date('d/m/Y')))) {
                                                                        $errorUrl = true;
                                                                    }
                                                                    if (key($params) == 'idLstVisiteur' &&
                                                                            !$pdo->estUnVisiteur(current($params))) {
                                                                         $errorUrl = true;
                                                                    }
                                                                } elseif ($i4 == count($datas[$kUc][$kCt][$kAc][$kLac]) &&
                                                                        $vi4 == 0) {
                                                                    $errorUrl = true;
                                                                }
                                                            }
                                                        }
                                                    }
                                                } elseif ($i3 == count($datas[$kUc][$kCt][$kAc]) &&  $vi3 == 0) {
                                                    $errorUrl = true;
                                                }
                                            }
                                        }
                                    } elseif ($i2 == count($datas[$kUc][$kCt]) &&  $vi2 == 0) {
                                        $errorUrl = true;
                                    }
                                }
                            }
                        } elseif ($i1 == count($datas[$kUc]) &&  $vi1 == 0) {
                             $errorUrl = true;
                        }
                    }
                } elseif ($i0 == count($datas) && $vi0 == 0) {
                    $errorUrl = true;
                }
            }
        }
            return $errorUrl;
    }
}
