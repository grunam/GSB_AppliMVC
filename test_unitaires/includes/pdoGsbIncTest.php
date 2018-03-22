<?php
require_once '../../includes/class.utils.inc.php';
require_once '../../includes/class.pdogsb.inc.php';
session_start();
$pdo = PdoGsb::getPdoGsb();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="UTF-8">
        <title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title> 
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../styles/bootstrap/bootstrap.css" rel="stylesheet">
        <link href="../../styles/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="container"> 
            <h1>
                <img src="../../images/logo.jpg" 
                     class="img-responsive"
                     alt="Laboratoire Galaxy-Swiss Bourdin"
                     title="Laboratoire Galaxy-Swiss Bourdin">
            </h1>
            <h2>
                Tests unitaires pour les fonctions de mis à jour, d'insertion et de suppression de la classe class.pdogsb.inc   
            </h2>
            <div class="row">
                <div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction getPdoGsb()</h3> 
                        </div>
                        <div class="panel-body">
                            <?php
                            $str = 'la function doit renvoyer un type "object : PdoGsb", ';
                            $str .= 'elle renvoie : ';
                            $str .= 'un type <b>';
                            $str .= gettype($pdo->getPdoGsb());
                            $str .= ' : ';
                            $str .=  get_class($pdo->getPdoGsb());
                            $str .= '</b>';
                            echo $str;
                            $str ='';
                            ?>
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction majFraisForfait($idVisiteur, $mois, $lesFrais)</h3> 
                        </div>
                        <div class="panel-body">
                            <?php
                            $str = 'Pour le paramètre $idVisiteur de valeur "a17", le paramètre $mois de valeur "201612" et le paramètre $lesFrais de valeur Array ( "ETP" => 1, "KM" => 2, "NUI" => 3, "REP" => 4 ) ';  
                            $str .= '<br>la function doit mettre à jour : <br><br>';       
                            $str .=  'la table lignefraisforfait';
                            $str .=  '<br>pour le visiteur "a17" et la mois "201612"';
                            $str .=  '<br>avec les nouvelles quantités de frais forfaitaires,';
                            $str .= '<br>ETP = 1,';
                            $str .= '<br>KM = 2,';
                            $str .= '<br>NUI = 3,';
                            $str .= '<br>REP = 4';
                            $str .= '<br><br>elle met à jour : <br><br>';
                            $lesFraisForfaitInitiaux = $pdo->getLesFraisForfait('a17', '201612');  
                            $pdo->majFraisForfait('a17', '201612', array('ETP' => 1, 'KM' => 2, 'NUI' => 3, 'REP' => 4));     
                            $lesFraisForfaitMaj = $pdo->getLesFraisForfait('a17', '201612');
                            $str .=  'la table lignefraisforfait';
                            $str .=  '<br>pour le visiteur "a17" et la mois "201612"';
                            $str .=  '<br>avec les nouvelles quantités de frais forfaitaires :';
                            $str .= '<br>ETP = '.$lesFraisForfaitMaj[0]['quantite'];
                            $str .= '<br>KM = '.$lesFraisForfaitMaj[1]['quantite'];
                            $str .= '<br>NUI = '.$lesFraisForfaitMaj[2]['quantite'];
                            $str .= '<br>REP = '.$lesFraisForfaitMaj[3]['quantite'];
                            $pdo->majFraisForfait('a17', '201612', array('ETP' => $lesFraisForfaitInitiaux[0]['quantite'], 'KM' => $lesFraisForfaitInitiaux[1]['quantite'], 'NUI' => $lesFraisForfaitInitiaux[2]['quantite'], 'REP' => $lesFraisForfaitInitiaux[3]['quantite']));
                            echo $str;
                            $str = '';
                            ?>
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction reporterUnFraisHorsForfait($id, $mois)</h3> 
                        </div>
                        <div class="panel-body">
                            <?php
                            $str = 'Pour le paramètre $id de valeur "1", le paramètre $mois de valeur "201609"';  
                            $str .= '<br>la function doit mettre à jour : <br><br>';       
                            $str .=  'le tuple de la table lignefraishorsforfait';
                            $str .=  '<br>pour le frais d\'id "1" et de mois "201609"';
                            $str .=  '<br>doit avoir l\'attribut mois reporté au mois suivant "201610"';
                            $str .= '<br><br>elle met à jour : <br><br>';
                            $pdo->reporterUnFraisHorsForfait(1, Utils::getMoisSuivant('201609'));
                            $leFraisHorsForfaitMaj =  $pdo->getLeFraisHorsForfait(1);
                            $str .=  'le tuple de la table lignefraishorsforfait';
                            $str .=  '<br>pour le frais l\'id "1" et la mois "201609"';
                            $str .=  '<br>a l\'attribut mois reporté au mois suivant <b>'.$leFraisHorsForfaitMaj['mois'].'</b>';
                            $pdo->reporterUnFraisHorsForfait(1, '201609');
                            echo $str;
                            $str = '';
                            ?>
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction refuserUnFraisHorsForfait($id, $libelle)</h3> 
                        </div>
                        <div class="panel-body">
                            <?php
                            $str = 'Pour le paramètre $id de valeur "1", le paramètre $mois de valeur "201609"';  
                            $str .= '<br>la function doit mettre à jour : <br><br>';      
                            $str .=  'le tuple de la table lignefraishorsforfait';
                            $str .=  '<br>pour le frais d\'id "1" et de mois "201609"';
                            $str .=  '<br>doit avoir le contenu de l\'attribut libelle ';
                            $str .= '<br>modifié avec la mention "REFUSE-" ajouté au début et l\'attribut refuse modifié avec la valeur "1" <br>';
                            $str .= '<br><br>elle met à jour : <br><br>';
                            $leFraisHorsForfaitInitial =  $pdo->getLeFraisHorsForfait(1);
                            $pdo->refuserUnFraisHorsForfait(1, Utils::mentionRefuse($leFraisHorsForfaitInitial['libelle']));    
                            $leFraisHorsForfaitMaj = '';
                            $leFraisHorsForfaitMaj = $pdo->getLeFraisHorsForfait(1);   
                            $str .=  'le tuple de la table lignefraishorsforfait';
                            $str .=  '<br>pour le frais d\'id "1" et de mois "201609"';
                            $str .=  '<br>a le contenu de l\'attribut "libelle"';
                            $str .= '<br>modifié avec la mention "REFUSE-" ajouté au début :<br><b>';
                            $str .=  $leFraisHorsForfaitMaj['libelle'];
                            $str .= '</b>';
                            $str .= '<br> et l\'attribut refuse modifié avec la valeur :<br><b>';
                            $str .= $leFraisHorsForfaitMaj['refuse'];
                            $str .= '</b>'; 
                            $pdo->accepterUnFraisHorsForfait(1, $leFraisHorsForfaitInitial['libelle']);                              
                            echo $str;                             
                            $str = '';                                                          
                            ?>                                
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)</h3> 
                        </div>
                        <div class="panel-body">
                            <?php
                            $str = 'Pour le paramètre $idVisiteur de valeur "a131", le paramètre $mois de valeur "201609"';  
                            $str .= '<br>la function doit mettre à jour : <br><br>';       
                            $str .=  'le tuple de la table fichefrais';
                            $str .=  '<br>pour le frais d\'idVisiteur "a131" et de mois "201609"';
                            $str .=  '<br>doit avoir un attribut "nbjustificatifs" ';
                            $str .= '  de valeur "999" <br>';
                            $str .= '<br><br>elle met à jour : <br><br>';
                            $lesInfosFicheDeFraisInitiales =  $pdo->getLesInfosFicheFrais('a131', '201609');
                            $pdo->majNbJustificatifs('a131', '201609', 999);
                            $lesInfosFicheDeFraisMaj =  $pdo->getLesInfosFicheFrais('a131', '201609');                                     
                            $str .=  'le tuple de la table fichefrais';
                            $str .=  '<br>pour le frais d\'idVisiteur "a131" et de mois "201609"';
                            $str .=  '<br>a un attribut "nbjustificatifs"';
                            $str .= '<br>de valeur :';
                            $str .= $lesInfosFicheDeFraisMaj['nbJustificatifs'];
                            $str .='</b>';
                            $pdo->majNbJustificatifs('a131', '201609', $lesInfosFicheDeFraisInitiales['nbJustificatifs']);
                            echo $str;
                            $str = '';
                            ?>
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction creeNouvellesLignesFrais($idVisiteur, $mois)</h3> 
                        </div>
                        <div class="panel-body">
                            <?php
                            $str = 'Pour le paramètre $idVisiteur de valeur "a17" et le paramètre $mois de valeur "250012" '; 
                            $str .= '<br>la function doit créer une fiche de frais avec : <br><br>';
                            $str .= 'un attribut "idvisiteur" de valeur "a17"'; 
                            $str .= '<br>un attibut "mois" de valeur "250012"';
                            $str .= '<br>un attribut "nbjustificatifs" de valeur "0"';
                            $str .= '<br>un attribut "montantvalide" de valeur "0.00"';
                            $str .= '<br>un attribut "datemodif" de valeur '.date('Y-m-d');
                            $str .= '<br>un attribut "idetat" de valeur "CR"';
                            $str .= '<br><br>la function doit créer des lignes de frais forfaitaires avec : <br><br>';
                            $str .= 'un attribut "id" de valeur "ETP"'; 
                            $str .= '<br>un attibut "libelle" de valeur "Forfait Etape"';
                            $str .= '<br>un attribut "quantite" de valeur "0"'; 
                            $str .= '<br>un attribut "id" de valeur "KM"'; 
                            $str .= '<br>un attibut "libelle" de valeur "Frais Kilométrique"';
                            $str .= '<br>un attribut "quantite" de valeur "0"';  
                            $str .= '<br>un attribut "id" de valeur "NUI"'; 
                            $str .= '<br>un attibut "libelle" de valeur "Nuitée Hôtel"';
                            $str .= '<br>un attribut "quantite" de valeur "0"';
                            $str .= '<br>un attribut "id" de valeur "REP"'; 
                            $str .= '<br>un attibut "libelle" de valeur "Repas Restaurant"';
                            $str .= '<br>un attribut "quantite" de valeur "0"';
                            $pdo->creeNouvellesLignesFrais('a17', '250012');
                            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais('a17', '250012');
                            $lesFraisForfait = $pdo->getLesFraisForfait('a17', '250012');
                            $str .= '<br><br>elle renvoie pour la fiche de frais : <br><br>'; 
                            $idVisiteur = (is_array($lesInfosFicheFrais))?'a17':'';
                            $mois = (is_array($lesInfosFicheFrais))?'250012':'';
                            $str .= 'un attribut "idvisiteur" de valeur <b>'.$idVisiteur.'</b>'; 
                            $str .= '<br>un attibut "mois" de valeur <b>'.$mois.'</b>';
                            $str .= '<br>un attribut "nbjustificatifs" de valeur <b>'.$lesInfosFicheFrais['nbJustificatifs'].'</b>';
                            $str .= '<br>un attribut "montantvalide" de valeur <b>'.$lesInfosFicheFrais['montantValide'].'</b>';
                            $str .= '<br>un attribut "datemodif" de valeur  <b>'.$lesInfosFicheFrais['dateModif'].'</b>';
                            $str .= '<br>un attribut "idetat" de valeur <b>'.$lesInfosFicheFrais['idEtat'].'</b>';  
                            $str .= '<br><br>elle renvoie pour les lignes de frais forfaitaires : <br><br>'; 
                            foreach($lesFraisForfait as $leFraisForfait){    
                                foreach($leFraisForfait as $k => $v){
                                    if (!is_int($k)) {
                                        $str .= 'un attribut <b>'.$k;
                                        $str .= '</b> de valeur <b>'.$v;
                                        $str .= '</b><br>';
                                    }   
                                }
                                $str .= '<br>';
                            }  
                            echo $str; 
                            $pdo->supprimerNouvellesLignesFrais('a17', '250012'); 
                            $pdo->majEtatFicheFrais('a17', $pdo->dernierMoisSaisi('a17'), 'CR');                        
                            $str = '';
                            echo '<hr>';  
                            ?>
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant)</h3> 
                        </div>
                        <div class="panel-body">
                            <?php
                            $str = 'Pour le paramètre $idVisiteur de valeur "a17" et le paramètre $mois de valeur "203012" '; 
                            $str .= '<br>le paramètre libelle de valeur "billet d\'avion", le paramètre date de valeur "15/12/2030" ';
                            $str .= 'le paramètre montant de valeur "120" ';
                            $str .= '<br>la function doit créer un nouveau frais hors forfait avec : <br>';
                            $str .= '<br>un attribut "idvisiteur" de valeur "a17"'; 
                            $str .= '<br>un attibut "mois" de valeur "203012"';
                            $str .= '<br>un attribut "libelle" de valeur "billet d\'avion" ';
                            $str .= '<br>un attribut "date" de valeur "2030-12-15" ';
                            $str .= '<br>un attribut "montant" de valeur "120.00" ';
                            $str .= '<br>un attribut "refuse" de valeur "NULL"';
                            $pdo->creeNouvellesLignesFrais('a17', '203012');
                            $pdo->creeNouveauFraisHorsForfait('a17', '203012', 'billet d&#039;avion', '2030-12-15', 120);
                            $leDernierFraisHorsForfait = $pdo->getLeDernierFraisHorsForfait('a17', '203012');
                            $str .= '<br><br>elle renvoie pour le nouveau frais hors forfait : <br><br>';
                            $str .= 'un attribut "id" de valeur <b>'.$leDernierFraisHorsForfait['id'].'</b>'; 
                            $str .= '<br>un attribut "idvisiteur" de valeur <b>'.$leDernierFraisHorsForfait['idvisiteur'].'</b>'; 
                            $str .= '<br>un attibut "mois" de valeur <b>'.$leDernierFraisHorsForfait['mois'].'</b>';
                            $str .= '<br>un attribut "libelle" de valeur <b>'.$leDernierFraisHorsForfait['libelle'].'</b>';
                            $str .= '<br>un attribut "date" de valeur <b>'.$leDernierFraisHorsForfait['date'].'</b>';
                            $str .= '<br>un attribut "montant" de valeur  <b>'.$leDernierFraisHorsForfait['montant'].'</b>';
                            $str .= '<br>un attribut "refuse" de valeur <b>'.$leDernierFraisHorsForfait['refuse'].'</b>';
                            echo $str;
                            $pdo->supprimerFraisHorsForfait($leDernierFraisHorsForfait['id']);                               
                            $pdo->supprimerNouvellesLignesFrais('a17', '203012');  
                            $pdo->majEtatFicheFrais('a17', $pdo->dernierMoisSaisi('a17'), 'CR');      
                            $str = '';
                            echo '<hr>';                             
                            ?>                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>