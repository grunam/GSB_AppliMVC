<?php
include "./fonctions.php";

if (isset($_POST["operation"])) {
    if ($_POST["operation"] == "dernier") {
        try{
            print("dernier%");
            // création d'un curseur pour récupérer les profils
            print("dernier%");
            $cnx = connexionPDO();
            $req = $cnx->prepare("select * from profil order by datemesure desc");
            $req->execute();

            // s'il y a un profil, on récupère le premier
            if ($ligne = $req->fetch(PDO::FETCH_ASSOC)){
                    print(json_encode($ligne));
            }
       } catch (PDOException $e) {
                    
            print "Erreur !" . $e->getMessage();
            die();
            
        }
    } elseif ($_POST["operation"] == "enreg") {
        
        $lesdonnees = $_REQUEST["lesdonnees"];
        $donnee = json_decode($lesdonnees);
        $datemesure = $donnee[0];
        $poids = $donnee[1];
        $taille = $donnee[2];
        $age = $donnee[3];
        $sexe = $donnee[4];
        
        try {
            
            print ("enreg%");
            $cnx = connexionPDO();
            
             
            $larequete = "insert into profil (datemesure, poids, taille, age, sexe)" ;
            $larequete .= " values (\"$datemesure\", $poids, $taille, $age, $sexe)" ;
            print ($larequete);
            $req = $cnx->prepare($larequete);
            $req->execute();
            
          
            
        } catch (PDOException $e) {
                    
            print "Erreur !" . $e->getMessage();
            die();
            
        }
    }
    
}