<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
        include_once("../classes/Item_categorie.class.php");  
        include_once("../classes/Item.class.php");  
        include_once("../classes/Zone_modif.class.php"); 
        include_once("../classes/Price_modif.class.php"); 
        include_once("../classes/Easyadmin.class.php"); 
        include_once("../include/divers.php");  
        
        include_once("auth.php");
        
        //echo '<pre>';
        
        $timestamp1 = mktime (date("H") ,date("i") ,date("s") , date("n") , date("j") ,date("Y")  );

        
        $date_date1 = date("Ymd H:i:s");
        $date1 = strtotime($date_date1);
        
        $date2 = strtotime('20100301 01:36:00');

        //echo 'Nombre de jours : '. (date('d',$date1 - $date2)-1)."\n";
        //echo 'Nombre de mois : '. (date('m',$date1 - $date2)-1)."\n";
        //echo 'Nombre de minutes : '. (date('i',$date1 - $date2)+0)."\n";

        
        //print("le bon modif<br/>");
        //print_pre($_POST);
        
        $active_directory = new Active_directory();          
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        $email = $active_directory->getEmail($user, $passwd);        
        //$menu_array = $active_directory->getMenuArray($user, $passwd);       
        
        $store_params = new Store_params();         
        $itemCategorie = new Item_categorie(); 
        $item = new Item();
        
        
        // philippe.prat@yum.com
        // jerome.roure@yum.com
        // jmlvx@unite-jbk.fr
        
        $acces_email = $email;  
        
        
        
        $transferredArray = array();
        
         if(isset($_SESSION['cIdsTransfer'])){
             $transferredArray = decodeFromURL ($_SESSION['cIdsTransfer']);
             //unset($_SESSION['cIdsTransfer']);
         }
        
         
        $_SESSION['save_flag'] = '1';
        
        //print_pre($transferredArray);
        
        
        /**
        if(isset($_GET["str_post_array"])) $price_post_array = unserialize(urldecode ($_REQUEST["str_post_array"])); 
        
        print_pre($price_post_array);
        */
        //$store_array = $store_params->getSoreIDs($acces_email);         
        //$enum = $store_params->getEnumStoreIDs($acces_email);        
        //$priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        
        //$obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        //if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        if(isset($_REQUEST["priceZone"])) $obj_num = $_REQUEST["priceZone"];        
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);
        
        $priceZone = $store_params->getPriceZoneName($restaurantArray);
        
        //print_pre($restaurantArray);
        
        
        $itemCategorie_carte_array = $itemCategorie->getItemCategorieEntries("Carte");
        $itemCategorie_carte_array2 = $itemCategorie->getItemCategorieEntries2("Carte");
        $itemCategorie_menu_array = $itemCategorie->getItemCategorieEntries("Menus");
        $itemCategorie_menu_array2 = $itemCategorie->getItemCategorieEntries2("Menus");
        
        //print_pre($itemCategorie_carte_array2);
        
        //print_pre(group_categories($itemCategorie_carte_array2));
        
        $itemCategorie_carte_array = group_categories($itemCategorie_carte_array2);
        $itemCategorie_menu_array = group_categories($itemCategorie_menu_array2);
        
        
        //print_pre($itemCategorie_carte_array);
        
        //$itemCategorie_array = array();
        
        //$item->getCategorieItems("Z13-Louvradoux");
        //$array_item = $item->getCategorieItems2("Z13-Louvradoux");
        /**
        print('<pre>');
        print_r($array_item);
        print('</pre>');
        */
        
        $zone_modif = new Zone_modif();
        $price_modif = new Price_modif();
        
        //$price_modif->print_table_content();
        //$zone_modif->print_table_content();
        
        $price_modif->charger_zone_recent($_REQUEST["priceZone"]);
        
        /*
        print('priceZone = '.$_REQUEST["priceZone"].'<br/>');
        print('id = '.$price_modif->id.'<br/>');
        print('zone_id = '.$price_modif->zone_id.'<br/>');
        print('date_modif = '.$price_modif->date_modif.'<br/>');
        */
        $easyadmin = new Easyadmin();
        $easyadmin->charger();
        
        $national_price_menu_array = $national_price_carte_array = array();
        
        if(isset($_REQUEST["prix_futur"]) && ($_REQUEST["prix_futur"] == "1")){
            $national_price_menu_array = $national_price_carte_array = $item->get_national_price_array();
            /*
            print("oracle array ::<br>");
            print_pre($national_price_menu_array);
            print("end oracle array ::<br>");
             * 
             */
            //exit();
        }
        
        //print("id = ".$price_modif->id." - zone_id = ".$price_modif->zone_id."<br/>");
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/gestion_prix.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone"); 
        
        
        
            //$array_1 = $item->get_national_menu_price_array($_REQUEST["prix_futur"]);
            
            //print_pre($array_1);
            //for($i=0;$i<count($itemCategorie_carte_array);$i++){
        
        
            
        
            foreach ($itemCategorie_carte_array as $key => $value) {
                    
                
                
            
                // recuperation des item d'un range si celle si est vide pa s d'instantciation de zone categorie ni affichage de ces items.
                
                
                //$items_array_by_range = $item->getItemsByRange($priceZone, $itemCategorie_carte_array[$i]["BEGIN"], $itemCategorie_carte_array[$i]["END"],"carte");
                //print("bp1");
                $items_array_by_range = $item->getItemsByRangeCarte($priceZone,$value,$_REQUEST["prix_futur"],$national_price_carte_array);
                //print("bp2");
                
                if(count($items_array_by_range) > 0){                
                    $vtp->addSession($handle,"itemCategorieZone"); 
                    //$vtp->setVar($handle,"itemCategorieZone.categorie",$itemCategorie_carte_array[$i]["RANGE_NAME"]);   
                    $vtp->setVar($handle,"itemCategorieZone.categorie",$key);   
                        //$items_array_by_range = $item->getItemsByRange("Z13-Louvradoux", $itemCategorie_carte_array[$i]["BEGIN"], $itemCategorie_carte_array[$i]["END"],"carte");

                        //print_pre($items_array_by_range);

                        for($k=0;$k<count($items_array_by_range);$k++){
                            $vtp->addSession($handle,"itemZone"); 
                            $vtp->setVar($handle,"itemZone.mi_seq",$items_array_by_range[$k]["mi_seq"]);  
                            $vtp->setVar($handle,"itemZone.obj_num",$items_array_by_range[$k]["obj_num"]);
                            $vtp->setVar($handle,"itemZone.pourcentage_modif",$easyadmin->prct_modif);
                            $vtp->setVar($handle,"itemZone.libelle",$items_array_by_range[$k]["libelle"]);     
                            $vtp->setVar($handle,"itemZone.prix_actuel",number_format($items_array_by_range[$k]["prix"],2)); 

							$vtp->setVar($handle,"itemZone.menu_type",$items_array_by_range[$k]["menu_type"]); 
                            
                            $prix_modif = $zone_modif->get_price_modif($items_array_by_range[$k]["mi_seq"], $_REQUEST["priceZone"]);
                            
                            $items_array_by_range[$k]["modifie"] = $prix_modif;                            
                            
                            $vtp->setVar($handle,"itemZone.prix_modifie",fill_modif_price($items_array_by_range[$k],$transferredArray));     
                            
                            //print(($items_array_by_range[$k]["gold"])."<br>");
                            
                            $vtp->setVar($handle,"itemZone.prix_bronze",number_format(str_replace(",",".",$items_array_by_range[$k]["bronze"]),2));     
                            $vtp->setVar($handle,"itemZone.prix_silver",number_format(str_replace(",",".",$items_array_by_range[$k]["silver"]),2));     
                            $vtp->setVar($handle,"itemZone.prix_gold",number_format(str_replace(",",".",$items_array_by_range[$k]["gold"]),2));     
                            $vtp->closeSession($handle,"itemZone"); 
                        }
                    $vtp->closeSession($handle,"itemCategorieZone"); 
                }
            }
            
            
            
             //for($j=0;$j<count($itemCategorie_menu_array);$j++){
             foreach ($itemCategorie_menu_array as $key => $value) {
                 
                 
                 //$menu_array_by_range = $item->getItemsByRange($priceZone, $itemCategorie_menu_array[$j]["BEGIN"], $itemCategorie_menu_array[$j]["END"],"menu");
                 
                 $menu_array_by_range = $item->getItemsByRangeMenu($priceZone,$value,$_REQUEST["prix_futur"],$national_price_menu_array);
                 
                 //print_pre($menu_array_by_range);
                 //if($key == "BUCKETS") print_pre($menu_array_by_range);
                 
                 if(count($menu_array_by_range) > 0){  
                    $vtp->addSession($handle,"menuCategorieZone");  
                    $vtp->setVar($handle,"menuCategorieZone.categorie",$key);  


                        //print_pre($menu_array_by_range); exit;

                        for($k=0;$k<count($menu_array_by_range);$k++){
                            $vtp->addSession($handle,"menuZone"); 
                            $vtp->setVar($handle,"menuZone.mi_seq",$menu_array_by_range[$k]["mi_seq"]);  
                            $vtp->setVar($handle,"menuZone.obj_num",$menu_array_by_range[$k]["obj_num"]);
                            $vtp->setVar($handle,"menuZone.pourcentage_modif",$easyadmin->prct_modif);
                            $vtp->setVar($handle,"menuZone.libelle",$menu_array_by_range[$k]["libelle"]);     
                            $vtp->setVar($handle,"menuZone.prix_actuel",number_format($menu_array_by_range[$k]["prix"],2));
                            
                            $prix_modif = $zone_modif->get_price_modif($menu_array_by_range[$k]["mi_seq"], $_REQUEST["priceZone"]);
                            
                            $prix_apres_add = (strlen($prix_modif) == 0) ? "" : floatval(str_replace(",",".",$prix_modif)) + floatval(str_replace(",",".",$itemCategorie->get_menu_enfants_const($menu_array_by_range[$k]["obj_num"])));   
                            
                            
                            
                            //$menu_array_by_range[$k]["modifie"] = floatval(str_replace(",",".",$prix_modif)) + floatval(str_replace(",",".",$itemCategorie->get_menu_enfants_const($menu_array_by_range[$k]["obj_num"])));   
                            $menu_array_by_range[$k]["modifie"] = $prix_apres_add;   
                            
                            $vtp->setVar($handle,"menuZone.prix_modifie",fill_modif_price($menu_array_by_range[$k],$transferredArray));  
                            
                            $vtp->setVar($handle,"menuZone.prix_bronze",number_format(str_replace(",",".",$menu_array_by_range[$k]["bronze"]),2));     
                            $vtp->setVar($handle,"menuZone.prix_silver",number_format(str_replace(",",".",$menu_array_by_range[$k]["silver"]),2));     
                            $vtp->setVar($handle,"menuZone.prix_gold",number_format(str_replace(",",".",$menu_array_by_range[$k]["gold"]),2));     
                            $vtp->closeSession($handle,"menuZone"); 
                        }
                    $vtp->closeSession($handle,"menuCategorieZone"); 
                 }
            }                                 
            
        $zoneIndex = (isset($_REQUEST["priceZone"])) ? "?zoneIndex=".$_REQUEST["priceZone"] : "";
        List($ac_prix_checked,$fu_prix_checked) = (isset($_REQUEST["prix_futur"]) && ($_REQUEST["prix_futur"] == "1")) ? array("","checked") : array("checked","");
        
        
        $vtp->setVar($handle,"mainZone.zoneIndex",$zoneIndex);
            
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom); 
        $vtp->setVar($handle,"mainZone.ac_prix_checked",$ac_prix_checked); 
        $vtp->setVar($handle,"mainZone.fu_prix_checked",$fu_prix_checked); 
        $vtp->setVar($handle,"mainZone.priceZone",$priceZone);
        $vtp->setVar($handle,"mainZone.priceZoneNum",$_REQUEST["priceZone"]);                      
        $vtp->setVar($handle,"mainZone.dateModif",$price_modif->date_modif);       
	$vtp->setVar($handle,"mainZone.pied","pied"); 
        
        
        $date_date2 = date("Ymd H:i:s");
        $date2 = strtotime($date_date2);
        
        $timestamp2 = mktime (date("H") ,date("i") ,date("s") , date("n") , date("j") ,date("Y")  );                
        
        //echo 'Nombre de secondes TS : '. ($timestamp2 - $timestamp1)."\n";
        
        
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

