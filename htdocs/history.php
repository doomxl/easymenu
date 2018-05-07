<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
      
        include_once("auth.php");
        
        if(isset($_SESSION["availabe_elements"]) || isset($_SESSION["selected_elements"])) unset($_SESSION["availabe_elements"], $_SESSION["selected_elements"]);                     
        
        $history_array = array(
                                    '11/08/2011',
                                    '13/09/2011',
                                    '21/09/2011',
                                    '17/10/2011'
                                );
        $count = count($history_array);
        $count = 0;
        if(!($count > 0)){
            header("Location: choix_rest_promo.php?action=".$_REQUEST['action']."&typePromo=".$_REQUEST['typePromo']."&history=0");
        }
        
        $str_links = "";
        
        foreach ($history_array as $key => $value) {
             $str_links .= "<a hrek=\"\">".$value."</a><br/><br/>";   
        }
        
        //print("je suis ici</br>");
        
        $active_directory = new Active_directory();   
        //print("je suis ici<br>"); 
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        //print("je suis ici<br>"); exit();
        $email = $active_directory->getEmail($user, $passwd);    
        
        //print("je suis ici<br>"); exit();
        
        //$menu_array = $active_directory->getMenuArray($user, $passwd);       
        
        //print("je suis ici<br>"); exit();
        
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
	$handle = $vtp->Open("../vtemplates/history.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");
        
        /**
        if(isset($_GET['zoneIndex'])){
            $vtp->addSession($handle,"zoneHidden");
            $vtp->setVar($handle,"zoneHidden.zoneIndex",$_GET['zoneIndex']);   
            $vtp->closeSession($handle,"zoneHidden");
        }        
        
         * 
         */
        
        //print_r($_POST);
        
        //print($_REQUEST['action']);
        
        $operationTitle = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "Gestion" : "Consultation";
        $type_value = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion" : "consultation";
        
        $typePromo = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "restaurants" : "promotions";
        $typePromoGet = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "1" : "2";
        //$action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php";  
        
        $actionGet = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "?action=gestion" : "";    
        
        $vtp->setVar($handle,"mainZone.operationTitle",$operationTitle);               
        $vtp->setVar($handle,"mainZone.typePromo",$typePromo);
        $vtp->setVar($handle,"mainZone.actionGet",$actionGet);
        $vtp->setVar($handle,"mainZone.action",$_REQUEST['action']);
        $vtp->setVar($handle,"mainZone.actionHidden",$typePromo);
        $vtp->setVar($handle,"mainZone.typePromoGet",$typePromoGet);
        
        $vtp->setVar($handle,"mainZone.history_list",$str_links);
        
        //$str_links
        
        //$vtp->setVar($handle,"mainZone.type_value",$type_value);
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);                
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

