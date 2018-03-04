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
    var $modifier = $('#modifierFraisHorsForfait');
    
    var $valModFraisHorsForfait = $('#valModFraisHorsForfait');
    var $FraisHorsForfait = $('#FraisHorsForfait');
    
    
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
         
         var tabHF = [];
         
         if($("input[name='lesFraisHorsForfait[]']:checked").length > 0){
            
             $("input[name='lesFraisHorsForfait[]']:checked").each(function () {
               /*
               var $dateSelector = (("#txtDateHF"+$(this).val()).toString());
               var $libelleSelector = (("#txtLibelleHF"+$(this).val()).toString());
               var $montantSelector = (("#txtMontantHF"+$(this).val()).toString());
               */
                tabHF.push($(this).val());
                
               
            });
            
            $FraisHorsForfait.val( JSON.stringify(tabHF) );
           
            $valModFraisHorsForfait.attr('value', 'reporter');
            $('#FormFraisHorsForfait').submit();
         }  
          
     });
     
 
    $supprimer.on('click', function() {
        
        var tabHF = [];
        
        if($("input[name='lesFraisHorsForfait[]']:checked").length > 0){
            
            $("input[name='lesFraisHorsForfait[]']:checked").each(function () {
               /*
               var $dateSelector = (("#txtDateHF"+$(this).val()).toString());
               var $libelleSelector = (("#txtLibelleHF"+$(this).val()).toString());
               var $montantSelector = (("#txtMontantHF"+$(this).val()).toString());
               */
                tabHF.push($(this).val());
                
               
            });
            
            $FraisHorsForfait.val( JSON.stringify(tabHF) );
           
            $valModFraisHorsForfait.attr('value', 'supprimer');
            $('#FormFraisHorsForfait').submit();
        }    
    });
    
   
    $modifier.on('click', function() {
        
        /*
        "txtMontantHF"
        
        "txtLibelleHF"
        
        "txtDateHF"
        
        "cbHF"
        */
        
        var tabHF = [];
        if($("input[name='lesFraisHorsForfait[]']:checked").length > 0){
            
            
            $("input[name='lesFraisHorsForfait[]']:checked").each(function () {
               
               var $dateSelector = (("#txtDateHF"+$(this).val()).toString());
               var $libelleSelector = (("#txtLibelleHF"+$(this).val()).toString());
               var $montantSelector = (("#txtMontantHF"+$(this).val()).toString());
               
                tabHF.push({id : $(this).val(), date : $($dateSelector).val(), libelle : $($libelleSelector).val(), montant : $($montantSelector).val() });
                
               
            });
            
            $FraisHorsForfait.val( JSON.stringify(tabHF) );
            $valModFraisHorsForfait.attr('value', 'modifier');
            
            //console.log($FraisHorsForfait.val());
            $('#FormFraisHorsForfait').submit();
            
           
        }    
    });
   
   
      
       
   
   
});
     
 