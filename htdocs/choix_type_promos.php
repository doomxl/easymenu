<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
      
        include_once("auth.php");
        
        if(isset($_SESSION["availabe_elements"]) || isset($_SESSION["selected_elements"])) unset($_SESSION["availabe_elements"], $_SESSION["selected_elements"]);             
        
       
                
        $active_directory = new Active_directory();   
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        $email = $active_directory->getEmail($user, $passwd);           
        //$menu_array = $active_directory->getMenuArray($user, $passwd);       
        
        $store_params = new Store_params();         
        // philippe.prat@yum.com
        // jerome.roure@yum.com
        // jmlvx@unite-jbk.fr
        
        $acces_email = "philippe.prat@yum.com";      
        
        $store_array = $store_params->getSoreIDs($acces_email);         
        $enum = $store_params->getEnumStoreIDs($acces_email);        
        $priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        // print("bp1");
        $obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);               
        //print("bp1"); 
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/choix_type_promos.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	//print("bp1");
	$vtp->addSession($handle,"mainZone");
        
        $operationTitle = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gestion" : "Consultation";
        $operationButton = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gerer" : "Consulter";
        $actionHidden = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion" : "consulter";
        
        $selected_rest = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "checked" : "";
        $selected_promo = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "" : "checked";                
        
        $vtp->setVar($handle,"mainZone.operationTitle",$operationTitle);
        $vtp->setVar($handle,"mainZone.operationButton",$operationButton);
        if(isset($_REQUEST['typePromo'])){
            $vtp->setVar($handle,"mainZone.selected_rest",$selected_rest);
            $vtp->setVar($handle,"mainZone.selected_promo",$selected_promo);
        }
        $vtp->setVar($handle,"mainZone.actionHidden",$actionHidden);        
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);              
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

