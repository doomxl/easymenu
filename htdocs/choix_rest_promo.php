<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
        include_once("../classes/Item.class.php");  
        include_once("../include/divers.php");  
      
        include_once("auth.php");
        
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
        //$item = new Item();
        // philippe.prat@yum.com
        // jerome.roure@yum.com
        // jmlvx@unite-jbk.fr
        
        $acces_email = "philippe.prat@yum.com";  
        $acces_email = $email;
        
        
        $store_array = $store_params->getSoreIDs($acces_email);         
        $enum = $store_params->getEnumStoreIDs($acces_email);        
        $priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        //$discountArray = $item->getDiscounts();
        
        //print_pre($discountArray);
        
        $obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        /**
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);               
        $allRestaurantArray = $store_params->getAllRestaurantArray($acces_email); 
        */
        //print_pre($allRestaurantArray);
        //$toto = get_availabe_elements_options($typePromoGet,$acces_email);
        //print("<br>options = ".$toto); 
        
        
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/choix_rest_promo.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");
        /**
        if(!(isset($_REQUEST['history']) && ($_REQUEST['history'] == "0"))){
            $vtp->addSession($handle,"historyZone");
             $vtp->setVar($handle,"historyZone.action",$_REQUEST['action']);
            $vtp->setVar($handle,"historyZone.typePromoGet",$typePromoGet);
            $vtp->closeSession($handle,"historyZone"); 
        }
        */
        //print(" request  ".$_REQUEST['action']);
        
        $operationTitle = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "Gestion" : "Consultation";
        //$typeChoix = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "choix des restaurants" : "choix des promotions";
        //$type_value = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion" : "consultation";
        
        $typePromo = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "restaurants" : "promotions";
        $typePromoGet = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "1" : "2";
        $titre_liste1 = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "Liste des restaurants disponibles" : "Liste des promotions disponibles";
        $titre_liste2 = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "Restaurants sélectionn&eacute;s" : "Promotions sélectionn&eacute;es";
        //$action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php";  
        
        //$actionGet = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "?action=gestion" : ""; 
        
        $actionGet = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "?action=gestion" : "";    
        
        
         if(!(isset($_REQUEST['history']) && ($_REQUEST['history'] == "0"))){
            $vtp->addSession($handle,"historyZone");
             $vtp->setVar($handle,"historyZone.action",$_REQUEST['action']);
            $vtp->setVar($handle,"historyZone.typePromoGet",$typePromoGet);
            $vtp->closeSession($handle,"historyZone"); 
        }
        
        
        $vtp->setVar($handle,"mainZone.operationTitle",$operationTitle);               
        //$vtp->setVar($handle,"mainZone.typeChoix",$typePromo);               
        $vtp->setVar($handle,"mainZone.typePromo",$typePromo);
        
        /**
         if(isset($_SESSION['cIdsTransfer'])){
             $transferredArray = decodeFromURL ($_SESSION['cIdsTransfer']);
             //unset($_SESSION['cIdsTransfer']);
         }
        */
        /**
        $_SESSION["availabe_elements"] = encodeForURL($_POST["availabe_elements"]);
        $_SESSION["selected_elements"] = encodeForURL($_POST["selected_elements"]);
        */
        /**
        print("\$_SESSION[\"availabe_elements\"]<br/>");
        print_pre(decodeFromURL ($_SESSION["availabe_elements"]));
        
        print("\$_SESSION[\"selected_elements\"]<br/>");        
        print_pre(decodeFromURL ($_SESSION["selected_elements"]));
        */
        if(isset($_SESSION["availabe_elements"]) && isset($_SESSION["selected_elements"])){
            $availabe_elements_options = decodeFromURL ($_SESSION["availabe_elements"]);
            $selected_elements_options = decodeFromURL ($_SESSION["selected_elements"]);
            
            $str_availabe_elements_options = "";
            $str_selected_elements_options = "";
            
            foreach ($availabe_elements_options as $key => $value) {
                //$str_availabe_elements_options .= "<option value=\"".$value."\">&nbsp;".trim(substr($value,10,30))."</option>"; 
                                $value2 = ($_REQUEST['typePromo'] == "2") ? trim(substr($value,10,40)) : $value;
				$str_availabe_elements_options .= "<option value=\"".$value."\">&nbsp;".$value2."</option>";
            }
            
            foreach ($selected_elements_options as $key => $value) {
                //$str_selected_elements_options .= "<option value=\"".$value."\">&nbsp;".trim(substr($value,10,30))."</option>";
                                $value2 = ($_REQUEST['typePromo'] == "2") ? trim(substr($value,10,40)) : $value;
				$str_selected_elements_options .= "<option value=\"".$value."\">&nbsp;".$value2."</option>";
            }
            
            $vtp->setVar($handle,"mainZone.availabe_elements_options",$str_availabe_elements_options);
            $vtp->setVar($handle,"mainZone.selected_elements_options",$str_selected_elements_options);
        }else{
            $vtp->setVar($handle,"mainZone.availabe_elements_options",get_availabe_elements_options($typePromoGet,$acces_email,array()));
            $vtp->setVar($handle,"mainZone.selected_elements_options","");
        }
        
        //  {#availabe_elements_options}
        $vtp->setVar($handle,"mainZone.titre_liste1",$titre_liste1);
        $vtp->setVar($handle,"mainZone.titre_liste2",$titre_liste2);
        $vtp->setVar($handle,"mainZone.actionGet",$actionGet);
        //$vtp->setVar($handle,"mainZone.actionHidden",$typePromo);
        $vtp->setVar($handle,"mainZone.history",$_REQUEST['history']);
        $vtp->setVar($handle,"mainZone.action",$_REQUEST['action']);
         $vtp->setVar($handle,"mainZone.typePromoGet",$typePromoGet);
        //$vtp->setVar($handle,"mainZone.type_value",$type_value);
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom); 
         $vtp->setVar($handle,"mainZone.element",substr($typePromo,0,-1));
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

