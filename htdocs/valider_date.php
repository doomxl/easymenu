<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
        include_once("../include/divers.php");  
        
        include_once("auth.php");
        
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
        
        $obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);               
        
        //print_pre($_POST);
        
        
        $_SESSION['cIdsTransfer'] = encodeForURL($_POST);
        
        /**
        $price_post_array = $_POST;
        
        $str_of_price_post_array = urlencode(serialize($_POST));
        
        print_pre($str_of_price_post_array);
        */
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/valider_date.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");
        
        /**
        if(isset($_GET['zoneIndex'])){
            $vtp->addSession($handle,"zoneHidden");
            $vtp->setVar($handle,"zoneHidden.zoneIndex",$_GET['zoneIndex']);   
            $vtp->closeSession($handle,"zoneHidden");
        }        
        
         * 
         */
        
        $operationTitle = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gestion" : "Consultation";
        $operationButton = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "Gerer" : "Consulter";
        
        //$action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php"; 
        
        $js_get = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "&action=gestion" : "";    
        
        //$vtp->setVar($handle,"mainZone.operationTitle",$operationTitle);
        $vtp->setVar($handle,"mainZone.priceZoneNum",$_POST["priceZoneNum"]);
         $vtp->setVar($handle,"mainZone.priceZone",$obj_num);
        //$vtp->setVar($handle,"mainZone.str_post_array",$str_of_price_post_array);
        $vtp->setVar($handle,"mainZone.operationButton",$operationButton); //str_post_array
        $vtp->setVar($handle,"mainZone.js_get",$js_get);
        $vtp->setVar($handle,"mainZone.action","valider_enregistrement.php");
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);        
        $vtp->setVar($handle,"mainZone.zoneOptions",$store_params->getZoneOptions($acces_email, $selected));
        $vtp->setVar($handle,"mainZone.restaurantOptions",$store_params->getRestaurantOptions($acces_email, $selected, $obj_num));	
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

