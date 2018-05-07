
/*
Codes originels � DR.
Killeak : corrections pour compatibilite avec IE 6 et 7, Firefox 2 et Safari 2.
*/
/*
  deplacer( liste_depart, liste_arrivee )
  D�place depuis la liste de d�part (argument 1) et � destination de la liste d'arriv�e (argument 2) le ou les �l�ments s�lectionn�s par l'utilisateur, l'ajout se faisant � la suite des �l�ments d�j� pr�sents dans la liste d'arriv�e.
*/
  function deplacer( liste_depart, liste_arrivee )
  {
      
    //alert("je suis ici dans deplacer");  
    for( i = 0; i < liste_depart.options.length; i++ )
    {
      if( liste_depart.options[i].selected && liste_depart.options[i] != "" )
      {
        o = new Option( liste_depart.options[i].text, liste_depart.options[i].value );
        liste_arrivee.options[liste_arrivee.options.length] = o;
        liste_depart.options[i] = null;
        i = i - 1 ;
      }
      else
      {
        // alert( "aucun element selectionne" );
      }
    }
  }
/*
  deplacer_tout( liste_depart, liste_arrivee )
  D�place depuis la liste de d�part (argument 1) et � destination de la liste d'arriv�e (argument 2) tous les �l�ments pr�sents dans la liste de d�part, en les ajoutant � la suite de ceux d�j� pr�sents dans la liste d'arriv�e.
*/
  function deplacer_tout( liste_depart, liste_arrivee )
  {
    for( i = 0; i < liste_depart.options.length; i++ )
    {
      o = new Option( liste_depart.options[i].text, liste_depart.options[i].value );
      liste_arrivee.options[liste_arrivee.options.length] = o;
      liste_depart.options[i] = null;
      i = i - 1 ;
    }
  }
/*
  deplacer_hautbas( liste, sens )
  D�place au sein de la liste (argument 1) un �l�ment dans le sens (argument 2) voulu : -1 pour remonter, +1 pour descendre.
*/
  function deplacer_hautbas( liste, sens )
  {
    // init
    var listemax = liste.length - 2;
    var listesel = liste.selectedIndex;
    // debordement
    if( ( listesel < 0 ) || ( listesel < 1 && sens == -1 ) || ( listesel > listemax && sens == 1 ) )
    {
      return false;
    }
    // permutation
    tmpopt = new Option( liste.options[listesel+sens].text, liste.options[listesel+sens].value );
    liste.options[listesel+sens].text = liste.options[listesel].text;
    liste.options[listesel+sens].value = liste.options[listesel].value;
    liste.options[listesel+sens].selected = true;
    liste.options[listesel].text = tmpopt.text;
    liste.options[listesel].value = tmpopt.value;
    liste.options[listesel].selected = false;
    return true;
  }
/*
  soumettre_2listes( liste1, liste2 )
  Au moment de la soumission du formulaire, s�lectionne automatiquement toutes les valeurs des listes donn�es dans les deux arguments, afin que les valeurs choisies soit r�cup�r�es dans le script de traitement.
*/
  function soumettre_2listes( liste1, liste2 )
  {
    var listelen1 = liste1.length;
    for( i = 0; i < listelen1; i++ )
    {
      liste1.options[i].selected = true;
    }
    var listelen2 = liste2.length;
    for( j = 0; j < listelen2; j++ )
    {
      liste2.options[j].selected = true;
    }
  }
/*
  soumettre_1liste( liste )
  Au moment de la soumission du formulaire, s�lectionne automatiquement toutes les valeurs de la liste donn�e indiqu�e dans l'argument, afin que les valeurs choisies soit r�cup�r�es dans le script de traitement.
*/
  function soumettre_1liste( liste )
  {
      
    var listelen = liste.length;
    for( i = 0; i < listelen; i++ )
    {
      liste.options[i].selected = true;
    }    
  }
  
  function valider_elements(element,id){
      var selected_elements_liste = document.forms[0].selected_elements; 
      var date_value = document.getElementById(id).value; 
      
       if(date_ok(date_value)){
          if(selected_elements_liste.length == 0){
              alert("Vous n'avez pas selectionn� de "+element);
          }else{
              soumettre_1liste( document.forms[0].selected_elements  );
              soumettre_1liste( document.forms[0].availabe_elements  );        
              document.forms['choix_attribution'].submit();
          }
       }else{
          alert('Veuillez entrer une date valide (format jj/mm/aaaa).');
       }
  }
  
  function valider_date(id,date_modif){  
      //alert('Je suis dans valider date');
      var date_value = document.getElementById(id).value; 
      
       if(date_ok(date_value)){      
           //alert('Je suis dans valider date ok');
          if(date_modif.length != 0){
              if(confirm("Il y a d�ja une modification pr�vu pour le "+date_modif+".\n Voulez vous l'�craser?")){
                  document.forms['libelleForm'].submit();        
              }
          }else{  
            document.forms['libelleForm'].submit(); 
          }
       }else{
          alert('Veuillez entrer une date valide (format jj/mm/aaaa).');
       }
  }
  
  function isDate(mm,dd,yyyy) {
       var d = new Date(mm + "/" + dd + "/" + yyyy);
       return d.getMonth() + 1 == mm && d.getDate() == dd && d.getFullYear() == yyyy;
  }
  
  function date_ok(str_date){
      var dayArray = str_date.split("/"); // format francais dd/mm/yyyy
      var new_date = new Date(dayArray[1] + "/" + dayArray[0] + "/" + dayArray[2]);
      return new_date.getMonth() + 1 == dayArray[1] && new_date.getDate() == dayArray[0] && new_date.getFullYear() == dayArray[2];      
  }
  
  
  function valider_attribution(element,id){
      /*
      var selected_elements_liste = document.forms[0].selected_elements;  
     
      if(selected_elements_liste.length == 0){
          alert("Vous n'avez pas selectionn� de "+element);
      }else{
          soumettre_1liste( document.forms[0].selected_elements  );
          soumettre_1liste( document.forms[0].previous_elements  );         
          document.forms['choix_attribution'].submit();
      }   
      */
       soumettre_1liste( document.forms[0].selected_elements  );
       soumettre_1liste( document.forms[0].previous_elements  );         
       document.forms['choix_attribution'].submit();
      
  }
  
  function home(){
      window.location.href = "accueil.php";     
  }

 