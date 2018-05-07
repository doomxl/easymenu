<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
      
        include_once("../include/divers.php"); 
        
        include_once("auth.php");
        
        if(isset($_SESSION['cIdsTransfer'])) unset($_SESSION['cIdsTransfer']);  
        
        //print_pre($_SESSION);
        //print_pre($_POST);
        if(isset($_POST["searchTxt"])){
			//print($_POST["searchTxt"]);
			if(strlen($_POST["searchTxt"]) >= 16){
				
				$_POST["searchTxt"] = substr($_POST["searchTxt"],0,16);
				
				//$var = substr($_POST["searchTxt"],0,16);
				//print('ici : '.$_POST["searchTxt"]);
				//print('ici : '.$var);
			}
		}
        //print("je suis ici</br>");
        
        $active_directory = new Active_directory();   
        //print("je suis ici<br>"); 
         //print("je suis ici $user"); 
        
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        //print("je suis ici<br>"); exit();
        $email = $active_directory->getEmail($user, $passwd);    
        
        //print("je suis ici $email"); 
        
        //$menu_array = $active_directory->getMenuArray($user, $passwd,);       
        
        //print("je suis ici<br>"); exit();
        
        $store_params = new Store_params();         
        // philippe.prat@yum.com
        // jerome.roure@yum.com
        // jmlvx@unite-jbk.fr
        
        $acces_email = "jmlvx@unite-jbk.fr";  
        $acces_email = $email;
        
        //print_pre("bp1");
        
        //$store_array = $store_params->getSoreIDs($acces_email); 
        
        //print_pre("bp2");
        
        //print_pre($store_array);
        
        //$store_array_names = $store_params->getSoreNames($acces_email); 
        
        //$store_array = $store_params->getAllRestaurantArray($acces_email);
        $store_array = $store_params->getAllRestaurantArray_level($acces_email,$_SESSION['level']);
		
        //print_pre($store_array);
        
        //print_pre("bp3");
        
        $search_vals = array();
        
        foreach ($store_array  as $key => $value) {
            $search_vals[] = $value["obj_num"];
        }
        
        foreach ($store_array as $key => $value) {
            $search_vals[] = $value["rest_name"];
        }
        
        //print_pre($search_vals);
        
        $search_str = '';
        
        foreach ($search_vals as $key => $value) {
            $search_str .= '"'.$value.'",'; 
        }
        
        $search_str = substr($search_str, 0, -1);
        
        //print("<br/>$search_str<br/>");
        
        //print_pre($store_array_names);
        
       

        //print("bp1<br>");
        
        //$enum = $store_params->getEnumStoreIDs($acces_email);        
        
       
        //print("bp2 level ".$_SESSION['level']." $acces_email<br>");
        
        // si searchTxt alors getpricezonearray avec searchTxt.
        
        
        //$priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        $priceZoneArray = $store_params->getPriceZoneArray_level($acces_email,$_SESSION['level']);
		
        //print_pre("bp4");
        //trouver le priceZoneIndex ($rest_obj_num);        
        
        $priceZoneObjnum = (isset($_POST["searchTxt"])) ? $store_params->getPriceZoneIndex($_POST["searchTxt"]) : $priceZoneArray[0]["obj_num"];
        
        //print($priceZoneObjnum);
        
        $obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneObjnum;
        if(isset($_GET['zoneIndex']) || isset($_POST["searchTxt"])) $selected =  $obj_num;
        
        if(strlen($obj_num) == 0){
			//print('redirect'); exit();
            header("Location: choix_zone_de_prix.php?action=gestion");
        }
        
        //if(isset(isset($_POST["searchTxt"]) $selected = $_GET['zoneIndex'];
        // 
                      
        //print_pre("bp5");
        
        //$restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);               
        
        //print("bp4$selected<br>");
        //print_pre("bp6");
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/choix_zone_de_prix.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
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
        $action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php";   
        $js_get = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "&action=gestion" : "";  
        $js_get2 = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "?action=gestion" : ""; 
        
        $vtp->setVar($handle,"mainZone.operationTitle",$operationTitle);
        $vtp->setVar($handle,"mainZone.operationButton",$operationButton);
        $vtp->setVar($handle,"mainZone.js_get",$js_get);
        $vtp->setVar($handle,"mainZone.js_get2",$js_get2);
        $vtp->setVar($handle,"mainZone.action",$action);
        $vtp->setVar($handle,"mainZone.search_str",$search_str);
        $vtp->setVar($handle,"mainZone.searchValue",$_POST["searchTxt"]);
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom); 
        
        //$vtp->setVar($handle,"mainZone.zoneOptions",$store_params->getZoneOptions($acces_email, $selected));
        $vtp->setVar($handle,"mainZone.zoneOptions",$store_params->getZoneOptions_level($acces_email, $selected,$_SESSION['level']));
        //print_pre("bp6 obj_num '$obj_num'");
        //$vtp->setVar($handle,"mainZone.restaurantOptions",$store_params->getRestaurantOptions($acces_email, $selected, $obj_num,$_POST["searchTxt"]));	
		$vtp->setVar($handle,"mainZone.restaurantOptions",$store_params->getRestaurantOptions_level($acces_email, $selected, $obj_num,$_SESSION['level'],$_POST["searchTxt"]));	
        //print_pre("bp7");
        
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

