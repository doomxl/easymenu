<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php"); 
        include_once("../classes/Link_modif.class.php"); 
        include_once("../classes/Promo_modif.class.php");
        include_once("../classes/Tracker.class.php");
        include_once("../classes/Mail.class.php");
        include_once("../include/divers.php"); 
      
        include_once("auth.php");
        
        
        
        if(isset($_SESSION["availabe_elements"]) || isset($_SESSION["selected_elements"])) unset($_SESSION["availabe_elements"], $_SESSION["selected_elements"]);                     
        
        
        //sprint_pre($_POST);
        
       
        
        $saved_selected_elements_array = decodeFromURL ($_SESSION["saved_selected_elements_array"]);
        
      
         
         $formated_array = array();
         
         foreach ($saved_selected_elements_array as $key => $value) {
             $formated_array[] = sprintf('%07d',$value["obj_num"])." - ".$value["name"];
         }
        /*
         print('selected elements');
         print_pre($_POST["selected_elements"]);
         print('initial elements');
         print_pre($formated_array);
         */
         list($array_del,$array_add) = diff_array($formated_array,$_POST["selected_elements"]);
         /*
         print('array_del<br>');
         print_pre($array_del);
         print('array_add<br>');
         print_pre($array_add);
      */
        
        $active_directory = new Active_directory();   
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        
       
        $link_modif = new Link_modif();
        $promo_modif = new Promo_modif();
        
        //$link_modif->deleteAll();
        //$promo_modif->deleteAll();
        
        //exit();
        
        
        $promo_modif->date_modif = $_POST["date_modif"];
        $last_promo_id = $promo_modif->add();                
             
        $mail = new Mail();
        
        if($_SESSION["email"] == "fahri.dahmani@yum.com")  $_SESSION["email"] = "fahri.dahmani@hotmail.fr";
        
        $ln = $mail->retour_chariot($_SESSION["email"]);
        
        $message = "Easymenu notification : ".$ln.$ln;
        $message .= "User :      ".$prenom_nom.$ln;
        $message .= "Date :      ".date("d/m/Y H:i:s").$ln;
        $message .= "Date mep :  ".$_REQUEST["date_modif"].$ln;
        $message .= "Operation : Gestion des promos".$ln.$ln;     
        
        if($_REQUEST['typePromo'] == "1"){ // promos par restaurants
            foreach ($_POST["previous_elements"] as $key1 => $value1) {
                foreach ($array_del as $key2 => $value2) {                    
                    list($promo_id,$promo_name) = split("-",$value2);
                    list($store_id,$store_name) = split("-",$value1);
                    $link_modif->promo_id = $last_promo_id;
                    $link_modif->id = intval(trim($promo_id));
                    $link_modif->store_id = intval(trim($store_id));
                    $link_modif->link_type = 0;                                       
                    $link_modif->add();
                    
                    $tracker = new Tracker();
                    $tracker->user = $_SESSION['login'];
                    $tracker->date = date("d/m/Y");
                    $tracker->time = date("d/m/Y H:i:s");
                    $tracker->action = "desouscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id));
                    $tracker->add();  
                    
                    $message .= "desouscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id))."".$ln."\n ";
                    
                }
                
                foreach ($array_add as $key2 => $value2) {
                    list($promo_id,$promo_name) = split("-",$value2);
                    list($store_id,$store_name) = split("-",$value1);                    
                    $link_modif->promo_id = $last_promo_id;
                    $link_modif->id = intval(trim($promo_id));
                    $link_modif->store_id = intval(trim($store_id));
                    $link_modif->link_type = 1;                                       
                    $link_modif->add();
                    
                    $tracker = new Tracker();
                    $tracker->user = $_SESSION['login'];
                    $tracker->date = date("d/m/Y");
                    $tracker->time = date("d/m/Y H:i:s");
                    $tracker->action = "souscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id));
                    $tracker->add(); 
                    
                    $message .= "souscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id))."".$ln."\n ";
                }
            }
        }else{
            foreach ($_POST["previous_elements"] as $key1 => $value1) {
                foreach ($array_del as $key2 => $value2) {                   
                    list($promo_id,$promo_name) = split("-",$value1);
                    list($store_id,$store_name) = split("-",$value2);
                    $link_modif->promo_id = $last_promo_id;
                    $link_modif->id = intval(trim($promo_id));
                    $link_modif->store_id = intval(trim($store_id));
                    $link_modif->link_type = 0;                                       
                    $link_modif->add();
                    
                    $tracker = new Tracker();
                    $tracker->user = $_SESSION['login'];
                    $tracker->date = date("d/m/Y");
                    $tracker->time = date("d/m/Y H:i:s");
                    $tracker->action = "desouscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id));
                    $tracker->add(); 
                    
                    $message .= "desouscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id))."".$ln."\n ";
                }
                
                foreach ($array_add as $key2 => $value2) {                   
                    list($promo_id,$promo_name) = split("-",$value1);
                    list($store_id,$store_name) = split("-",$value2);
                    $link_modif->promo_id = $last_promo_id;
                    $link_modif->id = intval(trim($promo_id));
                    $link_modif->store_id = intval(trim($store_id));
                    $link_modif->link_type = 1;                                       
                    $link_modif->add();
                    
                    $tracker = new Tracker();
                    $tracker->user = $_SESSION['login'];
                    $tracker->date = date("d/m/Y");
                    $tracker->time = date("d/m/Y H:i:s");
                    $tracker->action = "souscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id));
                    $tracker->add();
                    
                    $message .= "souscription;store_id : ".intval(trim($store_id)).";promo_id = ".intval(trim($promo_id))."".$ln."\n ";
                }
            }
        }
        
        $mail->mail = $_SESSION["email"];
        $mail->message = $message;
        $mail->sujet = "Norification EasyMenu : Gestion des promos";
        $mail->envoi();         
                    
        //$link_modif->print_table_content();
        //$promo_modif->print_table_content();
        
        
        $active_directory = new Active_directory();          
        //$prenom_nom = $active_directory->getFullName($user, $passwd);
        $email = $active_directory->getEmail($user, $passwd);        
        //$menu_array = $active_directory->getMenuArray($user, $passwd);       
        
        $store_params = new Store_params();         
        // philippe.prat@yum.com
        // jerome.roure@yum.com
        // jmlvx@unite-jbk.fr
        
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
        
	$vtp = new VTemplate;                                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/valider_enregistrement_promos.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");            
        
        $operationTitle = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gestion" : "Consultation";
        $operationButton = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gerer" : "Consulter";
        $action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php";   
        $js_get = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "&action=gestion" : "";    
              
        $message_1 = "l'application des modifications sur la zone de prix a bien été enregsitré ".$_REQUEST["date_modif"];
        $message_2 = "Les données ont bien été sauvegardés pour la date du : <b>".$_REQUEST["date_modif"]."</b>";
        
        $save_message = ((isset($_REQUEST['saveType']) && ($_REQUEST['saveType'] == "valider_modif"))) ? $message_1 : $message_2;
               
        $vtp->setVar($handle,"mainZone.save_message",$message_2);      
        $vtp->setVar($handle,"mainZone.js_get",$js_get);      
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);              	
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

