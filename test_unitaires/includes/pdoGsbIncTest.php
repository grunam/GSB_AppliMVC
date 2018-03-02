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
                Tests unitaires fonctions class.pdogsb.inc
                
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
                            <h3 class="panel-title">fonction getInfosVisiteur($login, $mdp)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                             
                                $str = 'Pour le paramètre $login de valeur "lvillachane" et le paramètre $mdp de valeur "jux7g" '; 
                                $str .= '<br>la function doit renvoyer : <br>';
                                $str .= '<br>un attribut "id" de valeur "a131" '; 
                                $str .= '<br>un attibut "nom" de valeur "Villechalane" ';
                                $str .= '<br>un attribut "prénom" de valeur "Louis" ';
                                $str .= '<br>un attribut "comptable" de valeur "0" <br>';
                                $str .= '<br>elle renvoie : <br><br>';
                                
                                $lesinfoVisiteur = $pdo->getInfosVisiteur("lvillachane", "jux7g");
                                foreach($lesinfoVisiteur as $k => $v){
                                  
                                   if(!is_int($k)){
                                        
                                        $str .= 'un attribut <b>'.$k;
                                        $str .= '</b> de valeur <b>'.$v;
                                        $str .= '</b><br>';
                                   }
                                    
                                }
                               
                                echo $str;
                                
                                $str ='';
                               
                                
                                echo '<hr>';
                                
                                
                                $str = 'Pour le paramètre $login de valeur "lvillacha" et le paramètre $mdp de valeur "jux7g" '; 
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br>elle renvoie : <br><br>';
                                
                                $lesinfoVisiteur = '';
                                $lesinfoVisiteur = $pdo->getInfosVisiteur("lvillacha", "jux7g");
                                if(is_array($lesinfoVisiteur)){    
                                    foreach($lesinfoVisiteur as $k => $v){

                                       if(!is_int($k)){

                                            $str .= 'un attribut <b>'.$k;
                                            $str .= '</b> de valeur <b>'.$v;
                                            $str .= '</b><br>';
                                       }

                                    }
                                } else if($lesinfoVisiteur == null){
                                    $str .= '';
                                }
                                
                                echo $str;
                                
                                $str = '';
                                
                                
                                echo '<hr>';
                                
                                
                                $str = 'Pour le paramètre $login de valeur "lvillachane" et le paramètre $mdp de valeur "99999" '; 
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br>elle renvoie : <br><br>';
                                
                                $lesinfoVisiteur = '';
                                $lesinfoVisiteur = $pdo->getInfosVisiteur("lvillachane", "99999");
                                if(is_array($lesinfoVisiteur)){    
                                    foreach($lesinfoVisiteur as $k => $v){

                                       if(!is_int($k)){

                                            $str .= 'un attribut <b>'.$k;
                                            $str .= '</b> de valeur <b>'.$v;
                                            $str .= '</b><br>';
                                       }

                                    }
                                } else if($lesinfoVisiteur == null){
                                    $str .= '';
                                }
                                
                                echo $str;
                                
                            ?>    
                            
                        </div>
                    </div>
                    
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction estUnVisiteur($idVisiteur)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                                $str = 'Pour le paramètre $idVisiteur de valeur "a131" '; 
                                $str .= '<br>la function doit renvoyer : <br>';
                                $str .= '<br>"1" '; 
                       
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estUnVisiteur("a131").'</b><br>';
                                 
                                echo $str;
                                echo '<hr>';
                                
                                $str = '';
                                
                                $str = 'Pour le paramètre $idVisiteur de valeur "131" '; 
                                $str .= '<br>la function ne doit rien renvoyer (false),<br>';
                                 
                       
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estUnVisiteur("131").'</b><br><br>';
                                
                               
                                
                                echo $str;
                                $str = '';
                            ?>    
                            
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction estUnFraisHorsForfait($idFrais, $idVisiteur, $mois)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                                $str = 'Pour le paramètre $idFrais de valeur "488", '; 
                                $str .= ' le paramètre $idVisiteur de valeur "b4", ';
                                $str .= ' le paramètre $mois de valeur "201707", ';
                                $str .= '<br>la function doit renvoyer : <br>';
                                $str .= '<br>"1" '; 
                       
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estUnFraisHorsForfait(488, "b4", "201707").'</b><br>';
                                 
                                echo $str;
                                $str = '';
                                
                                echo '<hr>';
                                
                                
                                
                                $str = 'Pour le paramètre $idFrais de valeur "ervsdg", '; 
                                $str .= ' le paramètre $idVisiteur de valeur "b4", ';
                                $str .= ' le paramètre $mois de valeur "201708", ';
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estUnFraisHorsForfait("ervsdg", "b4", "201708").'</b><br><br>';
                                
                               
                                
                                echo $str;
                                $str = '';
                                
                                
                                echo '<hr>';
                                
                                
                                $str = 'Pour le paramètre $idFrais de valeur "488", '; 
                                $str .= ' le paramètre $idVisiteur de valeur "z88sdg", ';
                                $str .= ' le paramètre $mois de valeur "201708", ';
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estUnFraisHorsForfait(488, "z88sdg", "201708").'</b><br><br>';
                                
                                
                                echo $str;
                                
                                $str = '';
                                
                                echo '<hr>';
                                
                                $str = 'Pour le paramètre $idFrais de valeur "488", '; 
                                $str .= ' le paramètre $idVisiteur de valeur "b4", ';
                                $str .= ' le paramètre $mois de valeur "299908", ';
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estUnFraisHorsForfait(488, "b4", "299908").'</b><br><br>';
                                
                                echo $str;
                            ?>    
                            
                        </div>
                    </div>
                    
                    
                    
                    
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction getLesFraisHorsForfait($idVisiteur, $mois)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                             
                                $str = 'Pour le paramètre $idVisiteur de valeur "e39" et le paramètre $mois de valeur "201708" '; 
                                $str .= '<br>la function doit renvoyer : <br>';
                                
                                $str .= '<br>un attribut "id" de valeur "981"'; 
                                $str .= '<br>un attibut "idVisiteur" de valeur "e39"';
                                $str .= '<br>un attribut "mois" de valeur "201708"';
                                $str .= '<br>un attribut "libelle" de valeur "Frais vestimentaire/représentation"';
                                $str .= '<br>un attribut "date" de valeur "09/08/2017"';
                                $str .= '<br>un attribut "montant" de valeur "79.00"';
                                $str .= '<br>un attribut "refuse" de valeur "NULL"<br>';
                                
                                
                                 $str .= '<br>un attribut "id" de valeur "982"'; 
                                $str .= '<br>un attibut "idVisiteur" de valeur "e39"';
                                $str .= '<br>un attribut "mois" de valeur "201708"';
                                $str .= '<br>un attribut "libelle" de valeur "Rémunération intervenant/spécialiste"';
                                $str .= '<br>un attribut "date" de valeur "14/08/2017"';
                                $str .= '<br>un attribut "montant" de valeur "1106.00"';
                                $str .= '<br>un attribut "refuse" de valeur "NULL"<br>';
                                
                                
                                
                                $str .= '<br>un attribut "id" de valeur "983"'; 
                                $str .= '<br>un attibut "idVisiteur" de valeur "e39"';
                                $str .= '<br>un attribut "mois" de valeur "201708"';
                                $str .= '<br>un attribut "libelle" de valeur "Location salle conférence"';
                                $str .= '<br>un attribut "date" de valeur "27/08/2017"';
                                $str .= '<br>un attribut "montant" de valeur "357.00"';
                                $str .= '<br>un attribut "refuse" de valeur "NULL"';
                                $str .= '<br><br>elle renvoie : <br><br>';
                                
                               
                                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait("e39", "201708");
                                
                                foreach($lesFraisHorsForfait as $leFraisHorsForfait){ 
                                    
                                    foreach($leFraisHorsForfait as $k => $v){

                                       if(!is_int($k)){

                                            $str .= 'un attribut <b>'.$k;
                                            $str .= '</b> de valeur <b>'.$v;
                                            $str .= '</b><br>';
                                       }
                                       
                                    }
                                    $str .= '<br>';
                                }  
                                
                                echo $str;
                                
                                $str = '';
                             
                               
                                echo '<hr>';
                               
                                $str = 'Pour le paramètre $idVisiteur de valeur "e39" et le paramètre $mois de valeur "219901" '; 
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br><br>elle renvoie : <br><br>';
                                
                                $lesFraisHorsForfait = '';
                                $leFraisHorsForfait = '';
                                
                                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait("e39", "219901");
                                
                                if(is_array($lesFraisHorsForfait)){ 
                                    
                                    foreach($lesFraisHorsForfait as $leFraisHorsForfait){ 

                                        foreach($leFraisHorsForfait as $k => $v){

                                           if(!is_int($k)){

                                                $str .= 'un attribut <b>'.$k;
                                                $str .= '</b> de valeur <b>'.$v;
                                                $str .= '</b><br>';
                                           }

                                        }
                                        $str .= '<br>';
                                    }
                                } else if($lesFraisHorsForfait == null){
                                    $str .= '';
                                }   

                                echo $str;
                                
                                $str ='';
                                
                                echo '<hr>';
                               
                                $str = 'Pour le paramètre $idVisiteur de valeur "9999" et le paramètre $mois de valeur "201701" '; 
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br><br>elle renvoie : <br><br>';
                                
                                $lesFraisHorsForfait = '';
                                $leFraisHorsForfait = '';
                                
                                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait("9999", "201701");
                                
                                if(is_array($lesFraisHorsForfait)){ 
                                    
                                    foreach($lesFraisHorsForfait as $leFraisHorsForfait){ 

                                        foreach($leFraisHorsForfait as $k => $v){

                                           if(!is_int($k)){

                                                $str .= 'un attribut <b>'.$k;
                                                $str .= '</b> de valeur <b>'.$v;
                                                $str .= '</b><br>';
                                           }

                                        }
                                        $str .= '<br>';
                                    }
                                } else if($lesFraisHorsForfait == null){
                                    $str .= '';
                                }   

                                echo $str;
                                
                                $str ='';
                             
                            ?>    
                            
                        </div>
                    </div>
                    
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction getNbjustificatifs($idVisiteur, $mois)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                             
                              $str = 'Pour le paramètre $idVisiteur de valeur "a131" et le paramètre $mois de valeur "201609" '; 
                              $str .= '<br>la function doit renvoyer : <br>';

                              $str .= '<br>un attribut "nb" de valeur "5"';

                              $str .= '<br><br>elle renvoie : <br><br>';

                              $str .= $pdo->getNbjustificatifs("a131", "201609");
                               
                              echo $str;
                                
                              $str = '';
                                
                              echo '<hr>';
                                
                                
                              $str = 'Pour le paramètre $idVisiteur de valeur "9999" et $mois de valeur "201609" '; 
                              $str .= '<br>la function ne doit rien renvoyer,<br>';

                              $str .= '<br><br>elle renvoie : <br><br>';

                              $str .= $pdo->getNbjustificatifs("9999", "201609");
                                
                               
                              echo $str;
                                
                              $str = '';
                                
                              echo '<hr>'; 
                                
                              $str = 'Pour le paramètre $idVisiteur de valeur "a131" et le paramètre $mois de valeur "299912" '; 
                              $str .= '<br>la function ne doit rien renvoyer,<br>';

                              $str .= '<br><br>elle renvoie : <br><br>';

                              $str .= $pdo->getNbjustificatifs("a131", "299912");
                                
                             
                              echo $str;
                                
                              $str = '';
                               
                            ?>    
                            
                        </div>
                    </div>
                    
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction  getLesFraisForfait($idVisiteur, $mois)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                             
                                $str = 'Pour le paramètre $idVisiteur de valeur "a17" et le paramètre $mois de valeur "201610" '; 
                                $str .= '<br>la function doit renvoyer : <br>';
                                
                                $str .= '<br>un attribut "id" de valeur "ETP"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Forfait Etape"';
                                $str .= '<br>un attribut "quantite" de valeur "2"<br>';
                                
                                $str .= '<br>un attribut "id" de valeur "KM"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Frais Kilométrique"';
                                $str .= '<br>un attribut "quantite" de valeur "627"<br>';
                                
                                $str .= '<br>un attribut "id" de valeur "NUI"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Nuitée Hôtel"';
                                $str .= '<br>un attribut "quantite" de valeur "13"<br>';
                                
                                $str .= '<br>un attribut "id" de valeur "REP"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Repas Restaurant"';
                                $str .= '<br>un attribut "quantite" de valeur "14"';
                                
                                
                                $str .= '<br><br>elle renvoie : <br><br>';
                                
                               
                                $lesFraisForfait = $pdo->getLesFraisForfait("a17", "201610");
                                
                                foreach($lesFraisForfait as $leFraisForfait){ 
                                    
                                    foreach($leFraisForfait as $k => $v){

                                       if(!is_int($k)){

                                            $str .= 'un attribut <b>'.$k;
                                            $str .= '</b> de valeur <b>'.$v;
                                            $str .= '</b><br>';
                                       }
                                       
                                    }
                                    $str .= '<br>';
                                }  
                                
                                echo $str;
                                
                                $str = '';
                             
                               
                                echo '<hr>';
                                
                                
                               
                                $str = 'Pour le paramètre $idVisiteur de valeur "zegswbdsfh" et le paramètre $mois de valeur "201610" '; 
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                
                                $str .= '<br><br>elle renvoie : <br><br>';
                                
                                
                               
                                $lesFraisForfait = '';
                                $leFraisForfait = '';
                                
                                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait("zegswbdsfh", "201610");
                                
                                if(is_array($lesFraisForfait)){ 
                                    
                                    foreach($lesFraisForfait as $leFraisForfait){ 

                                        foreach($leFraisForfait as $k => $v){

                                           if(!is_int($k)){

                                                $str .= 'un attribut <b>'.$k;
                                                $str .= '</b> de valeur <b>'.$v;
                                                $str .= '</b><br>';
                                           }

                                        }
                                        $str .= '<br>';
                                    }
                                } else if($lesinfoVisiteur == null){
                                    $str .= '';
                                }   

                                echo $str;
                                
                                $str ='';
                                
                                echo '<hr>';
                               
                                $str = 'Pour le paramètre $idVisiteur de valeur "a17" et le paramètre $mois de valeur "458020" '; 
                                $str .= '<br>la function ne doit rien renvoyer,<br>';
                                $str .= '<br><br>elle renvoie : <br><br>';
                                
                                $lesFraisHorsForfait = '';
                                $leFraisHorsForfait = '';
                                
                                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait("a17", "458020");
                                
                                if(is_array($lesFraisHorsForfait)){ 
                                    
                                    foreach($lesFraisHorsForfait as $leFraisHorsForfait){ 

                                        foreach($leFraisHorsForfait as $k => $v){

                                           if(!is_int($k)){

                                                $str .= 'un attribut <b>'.$k;
                                                $str .= '</b> de valeur <b>'.$v;
                                                $str .= '</b><br>';
                                           }

                                        }
                                        $str .= '<br>';
                                    }
                                } else if($lesFraisHorsForfait == null){
                                    $str .= '';
                                }   

                                echo $str;
                                
                                $str ='';
                             
                            ?>    
                            
                        </div>
                    </div>
                    
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction getLesIdFrais()</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                             
                               
                              $str .= '<br>la function doit renvoyer : <br>';

                              $str .= 'ETP<br>';
                              $str .= 'KM<br>';
                              $str .= 'NUI<br>';
                              $str .= 'REP';
                              
                              $str .= '<br><br>elle renvoie : <br><br>';

                               $lesId = print_r($pdo->getLesIdFrais(), true);
                               
                               $str .= $lesId;
                               
                              echo $str;
                                
                              $str = '';
                                
                              
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
                                      
                              $str .=  ' la table lignefraisforfait<br>';
                              $str .=  ' pour le visiteur "a17" et la mois "201612"<br>';
                              $str .=  ' avec les nouvelles quantités de frais forfaitaires, <br>';

                              $str .= 'ETP = 1,<br>';
                              $str .= 'KM = 2,<br>';
                              $str .= 'NUI = 3,<br>';
                              $str .= 'REP = 4';
                              
                              $str .= '<br><br>elle met à jour : <br><br>';
                             
                              $lesFraisForfaitInitiaux = $pdo->getLesFraisForfait("a17", "201612");  
                              $pdo->majFraisForfait("a17", "201612", array("ETP" => 1, "KM" => 2, "NUI" => 3, "REP" => 4));     
                              $lesFraisForfaitMaj = $pdo->getLesFraisForfait("a17", "201612");
                               
                              $str .=  ' la table lignefraisforfait<br>';
                              $str .=  ' pour le visiteur "a17" et la mois "201612"<br>';
                              $str .=  ' avec les nouvelles quantités de frais forfaitaires : <br>';
                              
                              $str .= 'ETP = '.$lesFraisForfaitMaj[0]["quantite"];
                              $str .= '<br>KM = '.$lesFraisForfaitMaj[1]["quantite"];
                              $str .= '<br>NUI = '.$lesFraisForfaitMaj[2]["quantite"];
                              $str .= '<br>REP = '.$lesFraisForfaitMaj[3]["quantite"];
                              
                              $pdo->majFraisForfait("a17", "201612", array("ETP" => $lesFraisForfaitInitiaux[0]["quantite"], "KM" => $lesFraisForfaitInitiaux[1]["quantite"], "NUI" => $lesFraisForfaitInitiaux[2]["quantite"], "REP" => $lesFraisForfaitInitiaux[3]["quantite"]));
                             
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
                                      
                              $str .=  ' le tuple de la table lignefraishorsforfait<br>';
                              $str .=  ' pour le frais d\'id "1" et de mois "201609"<br>';
                              $str .=  ' doit avoir l\'attribut mois reporté au mois suivant "201610"<br>';

                              $str .= '<br><br>elle met à jour : <br><br>';
                             
                              $pdo->reporterUnFraisHorsForfait(1, Utils::getMoisSuivant("201609"));
                             
                              $leFraisHorsForfaitMaj =  $pdo->getLeFraisHorsForfait(1);
                              
                              $str .=  ' le tuple de la table lignefraishorsforfait<br>';
                              $str .=  ' pour le frais l\'id "1" et la mois "201609"<br>';
                              $str .=  ' a l\'attribut mois reporté au mois suivant <b>'.$leFraisHorsForfaitMaj["mois"].'</b>';
 
                              $pdo->reporterUnFraisHorsForfait(1, "201609");
                              
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
                                      
                              $str .=  ' le tuple de la table lignefraishorsforfait<br>';
                              $str .=  ' pour le frais d\'id "1" et de mois "201609"<br>';
                              $str .=  ' doit avoir le contenu de l\'attribut libelle ';
                              $str .= ' modifié avec la mention "REFUSE-" ajouté au début et l\'attribut refuse modifié avec la valeur "1" <br>';

                              $str .= '<br><br>elle met à jour : <br><br>';
                             
                              //$pdo->reporterUnFraisHorsForfait(1, Utils::getMoisSuivant("201609"));
                             
                              $leFraisHorsForfaitInitial =  $pdo->getLeFraisHorsForfait(1);
                              
                              $pdo->refuserUnFraisHorsForfait(1, Utils::mentionRefuse($leFraisHorsForfaitInitial["libelle"]));
                             
                              $leFraisHorsForfaitMaj = '';
                              $leFraisHorsForfaitMaj = $pdo->getLeFraisHorsForfait(1);
                              
                              
                              $str .=  ' le tuple de la table lignefraishorsforfait<br>';
                              $str .=  ' pour le frais d\'id "1" et de mois "201609"<br>';
                              $str .=  ' a le contenu de l\'attribut "libelle" ';
                              $str .= ' modifié avec la mention "REFUSE-" ajouté au début : <br><b>';
                              $str .=  $leFraisHorsForfaitMaj["libelle"];
                              $str .= '</b><br>';
                              $str .= ' et l\'attribut refuse modifié avec la valeur : <br><b>';
                              $str .= $leFraisHorsForfaitMaj["refuse"];
                              $str .= '</b>';          
                              
                              
                              
                              $pdo->accepterUnFraisHorsForfait(1, $leFraisHorsForfaitInitial["libelle"]);
                              
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
                                      
                              $str .=  ' le tuple de la table fichefrais<br>';
                              $str .=  ' pour le frais d\'idVisiteur "a131" et de mois "201609"<br>';
                              $str .=  ' doit avoir le contenu de l\'attribut "nbjustificatifs" ';
                              $str .= '  prendre la valeur "999" <br>';

                              $str .= '<br><br>elle met à jour : <br><br>';
                             
                              //$pdo->reporterUnFraisHorsForfait(1, Utils::getMoisSuivant("201609"));
                             
                              $lesInfosFicheDeFraisInitiales =  $pdo->getLesInfosFicheFrais("a131", "201609");
                              
                              $pdo->majNbJustificatifs("a131", "201609", 999);
                             
                             $lesInfosFicheDeFraisMaj =  $pdo->getLesInfosFicheFrais("a131", "201609");
                              
                             echo 'justifs : '.$lesInfosFicheDeFraisInitiales["nbJustificatifs"];
                              
                              $str .=  ' le tuple de la table fichefrais<br>';
                              $str .=  ' pour le frais d\'idVisiteur "a131" et de mois "201609"<br>';
                              $str .=  ' a le contenu de l\'attribut "nbjustificatifs" ';
                              $str .= '  prendre la valeur : <b>';
                              $str .= $lesInfosFicheDeFraisMaj["nbJustificatifs"];
                              $str .='</b>';
                              
                              $pdo->majNbJustificatifs("a131", "201609", $lesInfosFicheDeFraisInitiales["nbJustificatifs"]);
                              
                              echo $str;
                             
                              $str = '';
                               
                             
                            ?>    
                            
                        </div>
                    </div>
                    
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction function estPremierFraisMois($idVisiteur, $mois)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                              
                                // $str .= ' le paramètre $mois de valeur "'.Utils::getMoisSuivant($pdo->dernierMoisSaisi("a131"))."'";
                                $str = 'Pour le paramètre $idVisiteur de valeur "a131", '; 
                                $str .= ' le paramètre $mois de valeur "'.Utils::getMoisSuivant($pdo->dernierMoisSaisi("a131"))."'";
                                $str .= '<br>la function doit renvoyer : <br>';
                                $str .= '<br>"1" '; 
                       
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estPremierFraisMois("a131", Utils::getMoisSuivant($pdo->dernierMoisSaisi("a131"))).'</b><br>';
                                 
                                echo $str;
                                $str = '';
                                
                                echo '<hr>';
                             
                                $str = 'Pour le paramètre $idVisiteur de valeur "a131", '; 
                                $str .= ' le paramètre $mois de valeur "201609"';
                                $str .= '<br>la function ne doit rien renvoyer (false).';
                              
                       
                                $str .= '<br><br>elle renvoie : <br><br>';
                                $str .= '<b>'.$pdo->estPremierFraisMois("a131", "201609").'</b><br>';
                                 
                                echo $str;
                                $str = '';
                                
                                
                            ?>    
                            
                        </div>
                    </div>
                    
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">fonction dernierMoisSaisi($idVisiteur)</h3> 
                        </div>
                        <div class="panel-body">
                             <?php
                                
                             
                              $str = 'Pour le paramètre $idVisiteur de valeur "f39" '; 
                              $str .= '<br>la function doit renvoyer : <br>';

                              $str .= '<br>un attribut "mois" de valeur "201710"';

                              $str .= '<br><br>elle renvoie : <br><br>';
                              $str .= '<b>';      
                              $str .= $pdo->dernierMoisSaisi("f39");
                              $str .= '</b>'; 
                              echo $str;
                                
                              $str = '';
                                
                              echo '<hr>';
                                
                                
                              $str = 'Pour le paramètre $idVisiteur de valeur "9999" '; 
                              $str .= '<br>la function ne doit rien renvoyer,<br>';

                              $str .= '<br><br>elle renvoie : <br><br>';

                              $str .= $pdo->dernierMoisSaisi("9999");;
                                
                               
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
                                $str .= '<br>la function doit créer une fiche de frais avec : <br>';
                                
                               
                                $str .= '<br>un attribut "idvisiteur" de valeur "a17"'; 
                                $str .= '<br>un attibut "mois" de valeur "250012"';
                                $str .= '<br>un attribut "nbjustificatifs" de valeur "0"';
                                $str .= '<br>un attribut "montantvalide" de valeur "0.00"';
                                $str .= '<br>un attribut "datemodif" de valeur '.date("Y-m-d");
                                $str .= '<br>un attribut "idetat" de valeur "CR"';
                                
                                $str .= '<br><br>la function doit créer des lignes de frais forfaitaires avec : <br>';
                                
                                
                                $str .= '<br>un attribut "id" de valeur "ETP"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Forfait Etape"';
                                $str .= '<br>un attribut "quantite" de valeur "0"<br>';
                                
                                $str .= '<br>un attribut "id" de valeur "KM"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Frais Kilométrique"';
                                $str .= '<br>un attribut "quantite" de valeur "0"<br>';
                                
                                $str .= '<br>un attribut "id" de valeur "NUI"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Nuitée Hôtel"';
                                $str .= '<br>un attribut "quantite" de valeur "0"<br>';
                                
                                $str .= '<br>un attribut "id" de valeur "REP"'; 
                                $str .= '<br>un attibut "libelle" de valeur "Repas Restaurant"';
                                $str .= '<br>un attribut "quantite" de valeur "0"';
                                
                                $pdo->creeNouvellesLignesFrais("a17", "250012");
                                $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais("a17", "250012");
                                $lesFraisForfait = $pdo->getLesFraisForfait("a17", "250012");
                                
                                ////  montantValide  nbJustificatifs   dateModif    idEtat 
    
                                $str .= '<br><br>elle renvoie pour la fiche de frais : <br><br>';
                                
                                $idVisiteur = (is_array($lesInfosFicheFrais))?'a17':'';
                                $mois = (is_array($lesInfosFicheFrais))?'250012':'';
                                
                                $str .= 'un attribut "idvisiteur" de valeur <b>'.$idVisiteur.'</b>'; 
                                $str .= '<br>un attibut "mois" de valeur <b>'.$mois.'</b>';
                                $str .= '<br>un attribut "nbjustificatifs" de valeur <b>'.$lesInfosFicheFrais["nbJustificatifs"].'</b>';
                                $str .= '<br>un attribut "montantvalide" de valeur <b>'.$lesInfosFicheFrais["montantValide"].'</b>';
                                $str .= '<br>un attribut "datemodif" de valeur  <b>'.$lesInfosFicheFrais["dateModif"].'</b>';
                                $str .= '<br>un attribut "idetat" de valeur <b>'.$lesInfosFicheFrais["idEtat"].'</b>';
                                
                                $str .= '<br><br>elle renvoie pour les lignes de frais forfaitaires : <br><br>';
                                
                                foreach($lesFraisForfait as $leFraisForfait){ 
                                    
                                    foreach($leFraisForfait as $k => $v){

                                       if(!is_int($k)){

                                            $str .= 'un attribut <b>'.$k;
                                            $str .= '</b> de valeur <b>'.$v;
                                            $str .= '</b><br>';
                                       }
                                       
                                    }
                                    $str .= '<br>';
                                }  
                                
                                echo $str;
                                
                                $pdo->supprimerNouvellesLignesFrais("a17", "250012");
                                
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
                                $str .= '<br>le paramètre libelle de valeur "dépannage véhicule", le paramètre date de valeur "15/12/2030" ';
                                $str .= 'le paramètre montant de valeur "120" ';
                                $str .= '<br>la function doit créer un nouveau frais hors forfait avec : <br>';
                                
                               
                                $str .= '<br>un attribut "idvisiteur" de valeur "a17"'; 
                                $str .= '<br>un attibut "mois" de valeur "203012"';
                                $str .= '<br>un attribut "libelle" de valeur "dépannage véhicule" ';
                                $str .= '<br>un attribut "date" de valeur "2030-12-15" ';
                                $str .= '<br>un attribut "montant" de valeur "120.00" ';
                                $str .= '<br>un attribut "refuse" de valeur "1"';
                                
                               $pdo->creeNouvellesLignesFrais("a17", "203012");
                               $pdo->creeNouveauFraisHorsForfait("a17", "203012", "dépannage véhicule", "15/12/2030", 120);
                               $leDernierFraisHorsForfait = $pdo->getLeDernierFraisHorsForfait("a17", "203012");
                               
                               $str .= '<br><br>elle renvoie pour le nouveau frais hors forfait : <br><br>';
                               
                               $str .= 'un attribut "id" de valeur <b>'.$leDernierFraisHorsForfait["id"].'</b>'; 
                               $str .= '<br>un attribut "idvisiteur" de valeur <b>'.$leDernierFraisHorsForfait["idvisiteur"].'</b>'; 
                               $str .= '<br>un attibut "mois" de valeur <b>'.$leDernierFraisHorsForfait["mois"].'</b>';
                               $str .= '<br>un attribut "libelle" de valeur <b>'.$leDernierFraisHorsForfait["libelle"].'</b>';
                               $str .= '<br>un attribut "date" de valeur <b>'.$leDernierFraisHorsForfait["date"].'</b>';
                               $str .= '<br>un attribut "montant" de valeur  <b>'.$leDernierFraisHorsForfait["montant"].'</b>';
                               $str .= '<br>un attribut "refuse" de valeur <b>'.$leDernierFraisHorsForfait["refuse"].'</b>';
                               
                               echo $str;
                                
                               /*
                               un attribut "id" de valeur 59
                               un attribut "idvisiteur" de valeur a17
                               un attibut "mois" de valeur 201609
                               un attribut "libelle" de valeur Frais vestimentaire/représentation
                               un attribut "date" de valeur 2016-09-24
                               un attribut "montant" de valeur 114.00
                               un attribut "refuse" de valeur

                                un attribut "id" de valeur 1409
                                un attribut "idvisiteur" de valeur a17
                                un attibut "mois" de valeur 201609
                                un attribut "libelle" de valeur Frais vestimentaire/représentation
                                un attribut "date" de valeur 2016-09-24
                                un attribut "montant" de valeur 114.00
                                un attribut "refuse" de valeur                                 
                                */
                               
                              
                               $pdo->supprimerFraisHorsForfait($leDernierFraisHorsForfait["id"]);                               
                             
                               $pdo->supprimerNouvellesLignesFrais("a17", "203012"); 
                               
                               
                                                       
                                 
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
