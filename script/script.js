/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function() {         

    var $lesVisiteurs = $('#lstVisiteurs');
    var $lesMois = $('#lstMois');
    var $monControleur = $('#monControleur');
    var $ok = $('#okVisiteursMois');
    var $annuler = $('#annulerVisiteursMois');
    
    var $reporter = $('#reporterFraisHorsForfait');
    var $supprimer = $('#supprimerFraisHorsForfait');
    var $modFraisHorsForfait = $('#modFraisHorsForfait');
    
    
    
    $lesVisiteurs.on('change', function() {
        
        $lesVisiteurs.attr('disabled','disabled');
        $lesMois.attr('disabled','disabled');
        $ok.attr('disabled','disabled');
        $annuler.attr('disabled','disabled');
        
        var idLstVisiteur = $lesVisiteurs.val();
        var leControleur = $monControleur.val();
        window.location.href = 'index.php?uc='+leControleur+'&action=selectionnerVisiteur&idLstVisiteur='+idLstVisiteur;
      
     });
     
     
     $reporter.on('click', function() {
         if($("input[name='lesFraisHorsForfait[]']:checked").length > 0){
            
             $modFraisHorsForfait.attr('value', 'reporter');
            $('#FormFraisHorsForfait').submit();
         }    
     });
 
    $supprimer.on('click', function() {
        if($("input[name='lesFraisHorsForfait[]']:checked").length > 0){
            
            $modFraisHorsForfait.attr('value', 'supprimer');
            $('#FormFraisHorsForfait').submit();
        }    
     });
    
   
});
     
 