<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
        include_once("../classes/Link_modif.class.php");  
        include_once("../classes/Promo_modif.class.php");  
        include_once("../include/divers.php");
      
        include_once("auth.php");
        /*
        $link_modif = new Link_modif();
        $promo_modif = new Promo_modif();
        
        $link_modif->print_table_content();
        $promo_modif->print_table_content();
         * 
         */
        
        //print('bp1');
        
        /**
        list($array_del,$array_add) = diff_array();
        
        print_pre($array_del);
        
        print("******");
        
        print_pre($array_add);
        */
        $active_directory = new Active_directory();   
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        $email = $active_directory->getEmail($user, $passwd);           
        //$menu_array = $active_directory->getMenuArray($user, $passwd);       
        //print('bp1');
        $store_params = new Store_params();         
        // philippe.prat@yum.com
        // jerome.roure@yum.com
        // jmlvx@unite-jbk.fr
        
        $acces_email = $email ;  
        
        $store_array = $store_params->getSoreIDs($acces_email);         
        $enum = $store_params->getEnumStoreIDs($acces_email);        
        $priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        //print('bp1');
        $obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);               
        
        //$_SESSION['cIdsTransfer'] = encodeForURL($_POST);
        //print_pre($_POST);
        
        $_SESSION["availabe_elements"] = encodeForURL($_POST["availabe_elements"]);
        $_SESSION["selected_elements"] = encodeForURL($_POST["selected_elements"]);
        
        $rest_options = "";
        
        for($i=0;$i<count($_POST["selected_elements"]);$i++){
            $rest_options .= "<option value=\"".$_POST["selected_elements"][$i]."\">".$_POST["selected_elements"][$i]."</options>";
        }
        
        //print_pre($_POST["selected_elements"]);
        //print('bp1');
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/choix_attributions.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");
        
        /**
         if(!(isset($_REQUEST['history']) && ($_REQUEST['history'] == "0"))){
            $vtp->addSession($handle,"historyZone");
            $vtp->closeSession($handle,"historyZone"); 
            
        }
        */
        $operationTitle = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "Gestion" : "Consultation";
        $operationButton = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "Gerer" : "Consulter";
        $actionHidden = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "gestion" : "consulter";
        
        $typePromo = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "restaurants" : "promotions";
        $typePromoRev = ($typePromo == "restaurants") ? "promotions" : "restaurants" ;
        $typePromoGet = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "1" : "2";
        
        $selected_elements_title = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "Restaurants sélectionnés" : "Promotions sélectionnées";
        $availabe_elements_for_attrib = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "Promotions disponibles" : "Restaurants disponibles";
        $chosen_elements_on_selected_elements = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "Promotions sélectionnées" : "Restaurants sélectionnés";
        //$activated_elements_on_selected_elements = ((isset($_REQUEST['typePromo']) && ($_REQUEST['typePromo'] == "1"))) ? "Promotions déja en place sur ces restaurants" : "Restaurants qui ont déja ces promotions";
        
        //$action = ((isset($_GET['action']) && ($_GET['action'] == "gestion"))) ? "gestion_prix.php" : "consultation_prix.php";  
        
        //$actionGet = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "?action=gestion" : ""; 
        
        $actionGet = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == "gestion"))) ? "?action=gestion" : "";   
        
        if(!(isset($_REQUEST['history']) && ($_REQUEST['history'] == "0"))){
            $vtp->addSession($handle,"historyZone");
             $vtp->setVar($handle,"historyZone.action",$_REQUEST['action']);
            $vtp->setVar($handle,"historyZone.typePromoGet",$typePromoGet);
            $vtp->closeSession($handle,"historyZone"); 
        }
        
        
        $vtp->setVar($handle,"mainZone.history",$_REQUEST['history']);
        $vtp->setVar($handle,"mainZone.operationTitle",$operationTitle);
        $vtp->setVar($handle,"mainZone.rest_options",$rest_options);
        $vtp->setVar($handle,"mainZone.typePromo",$typePromo);
        $vtp->setVar($handle,"mainZone.typePromoRev",$typePromoRev);
        
        $vtp->setVar($handle,"mainZone.selected_elements_title",$selected_elements_title);
        $vtp->setVar($handle,"mainZone.availabe_elements_for_attrib",$availabe_elements_for_attrib);
        $vtp->setVar($handle,"mainZone.chosen_elements_on_selected_elements",$chosen_elements_on_selected_elements);
        //$vtp->setVar($handle,"mainZone.activated_elements_on_selected_elements",$activated_elements_on_selected_elements);
        
         //{#selected_elements_title}
         //{#availabe_elements_for_attrib}
         //{#chosen_elements_on selected_elements}
         //{#activated_elements_on selected_elements}
         
         
        //$vtp->setVar($handle,"mainZone.typePromoMaj",ucfirst($typePromo));
        //$vtp->setVar($handle,"mainZone.typePromoRevMaj",ucfirst($typePromoRev));
        
        //print('bp1');
        
        $store_params = new Store_params();
        $item = new Item();
        
        $selected_elements_array = array();
        
        // ************ $_POST["selected_elements"] ************
        
        if($typePromoGet == "2"){                
            //$allRestaurantsArray = $store_params->getAllRestaurantArray($email);
            //print('bp1');
            $selected_elements_array = $store_params->getCommonRestaurantArray($email,$_POST["selected_elements"]);
            /**
            print_pre($selected_elements_array); exit(); 
            
       
            $selected_indexes = array();
            //print_pre($allRestaurantsArray);
            
            $k = rand(0, count($allRestaurantsArray)-1);
            
            for($i=0;$i<2;$i++){
                //print("bool : ".in_array($k, $selected_indexes)."<br/>");
                while(in_array($k, $selected_indexes)){
                    $k = rand(0, count($allRestaurantsArray)-1);
                    //$selected_indexes[] = $k ;
                    //print("$k dans la boucle <br/>");
                }
                $selected_indexes[] = $k ;                    
                //print("$k dehors la boucle <br/>"); print_pre($selected_indexes);
                $selected_elements_array[] = $allRestaurantsArray[$k];
            }
             * 
             */
            //print("****************");
            //print_pre($selected_elements_array);
        }else{
            //print('bp2');
            $selected_elements_array = $store_params->getCommonDiscountsArray($email,$_POST["selected_elements"]);
            //print('bp2');
            /*
            $discountArray = $item->getDiscounts();
            //print('bp2');
            $selected_indexes = array();
            $k = rand(0, count($discountArray));
            for($i=0;$i<8;$i++){                                
                while(in_array($k, $selected_indexes)){
                    $k = rand(0, count($discountArray)-1);
                    //$selected_indexes[] = $k ;
                }
                $selected_indexes[] = $k ;        
                $selected_elements_array[] = $discountArray[$k];
            }
             * 
             */
        }
        
        
         //print('bp3');
        //print_pre($selected_elements_array);
        
        $_SESSION["saved_selected_elements_array"] = encodeForURL($selected_elements_array);        
        
        $vtp->setVar($handle,"mainZone.availabe_elements_for_attrib_options", get_availabe_elements_options(3-$typePromoGet,$email,$selected_elements_array));
        //print_pre($selected_elements_array);
        $vtp->setVar($handle,"mainZone.selected_elements_options", get_selected_elements_options($selected_elements_array,$typePromoGet));
        //get_selected_elements_options($array)
        
        $vtp->setVar($handle,"mainZone.actionGet",$actionGet);
        $vtp->setVar($handle,"mainZone.date_modif",$_REQUEST["date_modif"]);
        //$vtp->setVar($handle,"mainZone.actionHidden",$typePromo);
        $vtp->setVar($handle,"mainZone.action",$_REQUEST['action']);
        $vtp->setVar($handle,"mainZone.typePromoGet",$typePromoGet);
        //$vtp->setVar($handle,"mainZone.operationButton",$operationButton);        
        //$vtp->setVar($handle,"mainZone.actionHidden",$actionHidden); 
        $vtp->setVar($handle,"mainZone.element",  substr($typePromoRev,0,-1)); 
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);              
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

