<?php
		include("../include/vtemplate.class.php"); 		// Inclusion du fichier	                       
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php"); 
        include_once("../classes/Zone_modif.class.php"); 
        include_once("../classes/Price_modif.class.php");
        include_once("../classes/Tracker.class.php");
        include_once("../classes/Item_categorie.class.php");
        include_once("../classes/Mail.class.php");
        include_once("../include/divers.php"); 
      
        include_once("auth.php");
        
        //print_pre($_POST);        
        
        $active_directory = new Active_directory();          
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        $email = $active_directory->getEmail($user, $passwd);        
       
        
        $transferredArray = array();
        $transferredArray = (isset($_SESSION['cIdsTransfer'])) ? decodeFromURL ($_SESSION['cIdsTransfer']) : $_POST;           
                                                               
         $zone_modif = new Zone_modif();
         $price_modif = new Price_modif();                  

         $modif_id = 0;

         
         
         if($price_modif->have_row($_POST["priceZoneNum"])){            
            $price_modif->charger_zone($_POST["priceZoneNum"]);
            $price_modif->date_modif = $_POST["date_modif"]; 
            $price_modif->maj(); 
            $modif_id = $price_modif->id;
         }else{
           
                      
            $price_modif->zone_id = $_POST["priceZoneNum"];
            $price_modif->date_modif = $_POST["date_modif"];
            $modif_id = $price_modif->add();
         
            
         }
            
         if(isset($_SESSION['cIdsTransfer']) || isset($_SESSION['save_flag'])) $zone_modif->delete($price_modif->id);         
         $result_array = linearizePostArray($transferredArray,5);                  

         //print_pre($result_array);
         
        $mail = new Mail();
        
        if($_SESSION["email"] == "fahri.dahmani@yum.com")  $_SESSION["email"] = "fahri.dahmani@hotmail.fr";
        
        $ln = $mail->retour_chariot($_SESSION["email"]);
        
        $message = "Easymenu notification : ".$ln.$ln;
        $message .= "User :      ".$prenom_nom.$ln;
        $message .= "Date :      ".date("d/m/Y H:i:s").$ln;
        $message .= "Date mep :  ".$_REQUEST["date_modif"].$ln;
        $message .= "Operation : Gestion des prix".$ln.$ln;   
         
         $item_categorie = new Item_categorie();
         
         foreach($result_array as $key => $value){
             if($value["prix_modif"] != null && $value["prix_modif"] > 0){                                                                                                                  
                 // zone_modif
                 
                 //print("prix modif : = ".floatval($value["prix_modif"])."<br/>");
                 //print("const modif : = ".floatval(str_replace(",",".",$item_categorie->get_menu_enfants_const($value["obj_num"])))."<br/>");
                 
                 $zone_modif->id = $value["mi_seq"];
                 $zone_modif->modif_id = $modif_id;
                 $zone_modif->carte_menu = $value["type"];
                 $zone_modif->price = (floatval($value["prix_modif"]) - floatval(str_replace(",",".",$item_categorie->get_menu_enfants_const($value["obj_num"]))));
		 //$zone_modif->add();
                 
                 if(isset($_SESSION['cIdsTransfer']) || isset($_SESSION['save_flag'])) $zone_modif->add(); //revoir avec sauvegard

                 $act = (isset($_SESSION['cIdsTransfer'])) ? "application modif" : "sauvegarde modif";

                 // Track
                 
                 $tracker = new Tracker();
                 $tracker->user = $_SESSION['login'];
                 $tracker->date = date("d/m/Y");
                 $tracker->time = date("d/m/Y H:i:s");
                 $tracker->action = $act.";PriceZone = ".$_POST["priceZoneNum"].";add new price;".$value["mi_seq"].";".$value["prix_modif"];
                 $tracker->add();  
                 
                 $message .= $act.";PriceZone = ".$_POST["priceZoneNum"].";add new price;".$value["mi_seq"].";".$value["prix_modif"].$ln."\n";

             }
         }
          
        $mail->mail = $_SESSION["email"];
        $mail->message = $message;
        $mail->sujet = "Norification EasyMenu : Gestion des prix";
        $mail->envoi();    
         
        if(isset($_SESSION['cIdsTransfer'])) unset($_SESSION['cIdsTransfer']);  // $_SESSION['save_flag']
        if(isset($_SESSION['save_flag'])) unset($_SESSION['save_flag']); 
         
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/valider_enregistrement.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");                
        
        $operationTitle = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gestion" : "Consultation";
        $operationButton = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gerer" : "Consulter";
        $action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php";   
        $js_get = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "&action=gestion" : "";    
                
        $message_1 = "l'application des modifications sur la zone de prix a bien été enregsitrée ".$_REQUEST["date_modif"];
        $message_2 = "les modifications ont bien été sauvegardées";
        
        $save_message = ((isset($_REQUEST['saveType']) && ($_REQUEST['saveType'] == "valider_modif"))) ? $message_1 : $message_2;
               
        $vtp->setVar($handle,"mainZone.save_message",$save_message);
        $vtp->setVar($handle,"mainZone.js_get",$js_get);       
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);               
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

