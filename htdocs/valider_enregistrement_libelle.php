<?php
	include_once("../include/vtemplate.class.php"); 		// Inclusion du fichier
        include_once("../include/divers.php");
        
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php"); 
        include_once("../classes/Libelle_modif.class.php"); 
        include_once("../classes/New_libelle.class.php"); 
        include_once("../classes/Promo_modif.class.php");
        include_once("../classes/Tracker.class.php");
        include_once("../classes/Mail.class.php");
        include_once("../include/divers.php"); 
      
        include_once("auth.php");                       
        
        $active_directory = new Active_directory();   
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        
        $libelle_modif = new Libelle_modif();
        $new_libelle = new New_libelle();
        
       
        
        // supprimer les enregistrements
        //print('bp1<br/>');
        $libelle_modif->deleteAll();
        //print('bp2<br/>');
        $new_libelle->deleteAll();
        //print('bp3<br/>');
        
        //$libelle_modif->date_modif = date("d/m/Y");
        $libelle_modif->date_modif = $_REQUEST["date_modif"];
        $last_id = $libelle_modif->add();
        
        $mail = new Mail();
        
        if($_SESSION["email"] == "fahri.dahmani@yum.com")  $_SESSION["email"] = "fahri.dahmani@hotmail.fr";
        
        $ln = $mail->retour_chariot($_SESSION["email"]);
        
        $message = "Easymenu notification : ".$ln.$ln;
        $message .= "User :      ".$prenom_nom.$ln;
        $message .= "Date :      ".date("d/m/Y H:i:s").$ln;
        $message .= "Date mep :  ".$_REQUEST["date_modif"].$ln;
        $message .= "Operation : Modification des libelles".$ln.$ln;                
        
        
        $result_array = linearizePostArray($_POST,5);
        
        foreach($result_array as $key => $value){
             if($value["nouveau_libelle"] != null){                
                 $new_libelle->id = $value["mi_seq"];
                 $new_libelle->modif_id = $last_id;
                 $new_libelle->old_libelle = $value["name_1"];
                 $new_libelle->new_libelle = $value["nouveau_libelle"];
                 $new_libelle->add();
                 
                 //$act = (isset($_SESSION['cIdsTransfer'])) ? "application modif" : "sauvegarde modif";
                 
                 $message .= "add new libelle;id = ".$value["mi_seq"].";old libelle = ".$value["name_1"].";new libelle = ".$value["nouveau_libelle"].$ln."\n";
                 
                 $tracker = new Tracker();
                 $tracker->user = $_SESSION['login'];
                 $tracker->date = date("d/m/Y");
                 $tracker->time = date("d/m/Y H:i:s");
                 $tracker->action = "add new libelle;id = ".$value["mi_seq"].";old libelle = ".$value["name_1"].";new libelle = ".$value["nouveau_libelle"];
                 $tracker->add();   
                 
             }
         }
        
        //print($_SESSION["email"]);
        //print($message);
        
        $mail->mail = $_SESSION["email"];
        $mail->message = $message;
        $mail->sujet = "Norification EasyMenu : modification des libelles";
        $mail->envoi();         
                    
        $email = $active_directory->getEmail($user, $passwd);        
       
        $store_params = new Store_params();         
       
        
        $acces_email = $email;  
        
        $store_array = $store_params->getSoreIDs($acces_email);         
        $enum = $store_params->getEnumStoreIDs($acces_email);        
        $priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        
        $obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);               
        
        $transferredArray = array();
        
     
         if(isset($_SESSION['cIdsTransfer'])){
             $transferredArray = decodeFromURL ($_SESSION['cIdsTransfer']);
             //unset($_SESSION['cIdsTransfer']);
             
         }
        
    
            $modif_id = 23564;
         
            print(fromPostToQuery($transferredArray,5,$modif_id));
            
            
     
         
         if(isset($_REQUEST['saveType'])){
             
         }
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/valider_enregistrement_libelle.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");
         
        
        $operationTitle = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gestion" : "Consultation";
        $operationButton = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gerer" : "Consulter";
        $action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php";   
        $js_get = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "&action=gestion" : "";    
       
        $message_1 = "l'application des modifications sur la zone de prix a bien été enregsitré ".$_REQUEST["date_modif"];
        $message_2 = "Les données ont bien été sauvegardé pour la date du : <b>".$_REQUEST["date_modif"]."</b>";
        
        $save_message = ((isset($_REQUEST['saveType']) && ($_REQUEST['saveType'] == "valider_modif"))) ? $message_1 : $message_2;
             
        $vtp->setVar($handle,"mainZone.save_message",$message_2);      
        $vtp->setVar($handle,"mainZone.js_get",$js_get);        
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);                	
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

