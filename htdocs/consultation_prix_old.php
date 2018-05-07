<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
      
        include_once("auth.php");
        
        $active_directory = new Active_directory();          
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        $email = $active_directory->getEmail($user, $passwd);        
        $menu_array = $active_directory->getMenuArray($user, $passwd);       
        
        $store_params = new Store_params();         
        // philippe.prat@yum.com
        // jerome.roure@yum.com
        // jmlvx@unite-jbk.fr
        
        $acces_email = "philippe.prat@yum.com";  
        
        $store_array = $store_params->getSoreIDs($acces_email);         
        $enum = $store_params->getEnumStoreIDs($acces_email);        
        $priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        
        $obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);               
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/consultation_prix.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");        
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);        
        $vtp->setVar($handle,"mainZone.zoneOptions",$store_params->getZoneOptions($acces_email, $selected));
        $vtp->setVar($handle,"mainZone.restaurantOptions",$store_params->getRestaurantOptions($acces_email, $selected, $obj_num));	
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

