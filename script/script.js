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
        window.location.href = 'index.php?uc=' + leControleur + '&action=selectionnerVisiteur&idLstVisiteur=' + idLstVisiteur;
      
     });
     
     
    var btn; 
     
    $reporter.on('click', function() { 
       btn = 'reporter';
    });
     
 
    $supprimer.on('click', function() {
        btn = 'supprimer';
    });
    
   
    $modifier.on('click', function() {
        btn = 'modifier';  
    }); 
     
    $('#FormFraisHorsForfait').on('submit', function() {
        
        //console.log(btn);
        //event.preventDefault();

        var tabHF = [];
         
        if ($("input[name='lesFraisHorsForfait[]']:checked").length > 0){
            
             $("input[name='lesFraisHorsForfait[]']:checked").each(function () {
                
                if (btn == 'reporter' || btn =='supprimer') { 
                    
                    tabHF.push($(this).val());
                    
                } else if (btn == 'modifier') {
                   
                   var $dateSelector = (('#txtDateHF' + $(this).val()).toString());
                   var $libelleSelector = (('#txtLibelleHF' + $(this).val()).toString());
                   var $montantSelector = (('#txtMontantHF' + $(this).val()).toString());
                   tabHF.push({id : $(this).val(), date : $($dateSelector).val(), libelle : $($libelleSelector).val(), montant : $($montantSelector).val()});
  
                }
            });
            
            $FraisHorsForfait.val(JSON.stringify(tabHF));
            
            $valModFraisHorsForfait.attr('value', btn);
          
         
        } else {
            
            event.preventDefault();
            alert('Veuillez sélectionner au moins un frais hors forfait.');
         
         }
         
    });
    
   
   
});
     
 