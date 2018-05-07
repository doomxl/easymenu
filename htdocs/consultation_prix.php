<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        //include_once("../classes/Mi_def.class.php");                   
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
        include_once("../classes/Item_categorie.class.php");  
        include_once("../classes/Item.class.php");  
        include_once("../classes/Zone_modif.class.php"); 
        include_once("../classes/Price_modif.class.php"); 
        include_once("../include/divers.php");  
        
        include_once("auth.php");
        
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
        
        $acces_email = "philippe.prat@yum.com";  
        
        
        
        
        //$store_array = $store_params->getSoreIDs($acces_email);         
        //$enum = $store_params->getEnumStoreIDs($acces_email);        
        //$priceZoneArray = $store_params->getPriceZoneArray($acces_email);
        
        //$obj_num = (isset($_GET['zoneIndex'])) ? $_GET['zoneIndex'] : $priceZoneArray[0]["obj_num"];
        //if(isset($_GET['zoneIndex'])) $selected = $_GET['zoneIndex'];
        
        if(isset($_REQUEST["priceZone"])) $obj_num = $_REQUEST["priceZone"];  
        
        //print_pre("bp1");
        
        $restaurantArray = $store_params->getRestaurantArray($acces_email,$obj_num);
        
        //print_pre("bp2");
        
        $priceZone = $store_params->getPriceZoneName($restaurantArray);
        
        //print_pre("bp3");
        
        //print_pre($restaurantArray);
        
        
        $itemCategorie_carte_array = $itemCategorie->getItemCategorieEntries("Carte");
        $itemCategorie_carte_array2 = $itemCategorie->getItemCategorieEntries2("Carte");
        $itemCategorie_menu_array = $itemCategorie->getItemCategorieEntries("Menus");
        $itemCategorie_menu_array2 = $itemCategorie->getItemCategorieEntries2("Menus");
        
        //print_pre($itemCategorie_carte_array2);
        
        //print_pre(group_categories($itemCategorie_carte_array2));
        
        $itemCategorie_carte_array = group_categories($itemCategorie_carte_array2);
        $itemCategorie_menu_array = group_categories($itemCategorie_menu_array2);
        
        //print_pre($itemCategorie_array);
        
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
        
        $price_modif->charger_zone_recent($_REQUEST["priceZone"]);
        
        
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/consultation_prix.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone"); 
        
        
        
        
            
            //for($i=0;$i<count($itemCategorie_carte_array);$i++){
            foreach ($itemCategorie_carte_array as $key => $value) {
                
                
                // recuperation des item d'un range si celle si est vide pa s d'instantciation de zone categorie ni affichage de ces items.
                //$items_array_by_range = $item->getItemsByRange($priceZone, $itemCategorie_carte_array[$i]["BEGIN"], $itemCategorie_carte_array[$i]["END"],"carte");
                $items_array_by_range = $item->getItemsByRangeCarte($priceZone,$value,4,array());
                if(count($items_array_by_range) > 0){                
                    $vtp->addSession($handle,"itemCategorieZone"); 
                    $vtp->setVar($handle,"itemCategorieZone.categorie",$key);   
                        //$items_array_by_range = $item->getItemsByRange("Z13-Louvradoux", $itemCategorie_carte_array[$i]["BEGIN"], $itemCategorie_carte_array[$i]["END"],"carte");

                        //print_pre($items_array_by_range);

                        for($k=0;$k<count($items_array_by_range);$k++){
                            $vtp->addSession($handle,"itemZone"); 
                            $vtp->setVar($handle,"itemZone.libelle",$items_array_by_range[$k]["libelle"]);    
                            
                            $vtp->setVar($handle,"itemZone.prix_actuel",number_format($items_array_by_range[$k]["prix"],2));   
                            
                            //$vtp->setVar($handle,"itemZone.prix_modifie",number_format($items_array_by_range[$k]["modifie"],2));  
                            
                            
                            
                            $prix_modif = $zone_modif->get_price_modif($items_array_by_range[$k]["mi_seq"], $_REQUEST["priceZone"]);
                            //print($prix_modif." - ".number_format($prix_modif,2)."<br/>");
                            
                            $vtp->setVar($handle,"itemZone.prix_modifie",number_format(str_replace(",",".",$prix_modif),2));     
                            
                            
                            
                            
                            
                            
                            $vtp->setVar($handle,"itemZone.prix_bronze",number_format($items_array_by_range[$k]["bronze"],2));     
                            $vtp->setVar($handle,"itemZone.prix_silver",number_format($items_array_by_range[$k]["silver"],2));     
                            $vtp->setVar($handle,"itemZone.prix_gold",number_format($items_array_by_range[$k]["gold"],2));     
                            $vtp->closeSession($handle,"itemZone"); 
                        }
                    $vtp->closeSession($handle,"itemCategorieZone"); 
                }
            }
            
            
            
            foreach ($itemCategorie_menu_array as $key => $value) {
                 
                 //$menu_array_by_range = $item->getItemsByRange($priceZone, $itemCategorie_menu_array[$j]["BEGIN"], $itemCategorie_menu_array[$j]["END"],"menu");
                 
                 $menu_array_by_range = $item->getItemsByRangeMenu($priceZone,$value,4,array());
                 
                 if(count($menu_array_by_range) > 0){  
                    $vtp->addSession($handle,"menuCategorieZone");  
                    $vtp->setVar($handle,"menuCategorieZone.categorie",$key);  


                        //print_pre($menu_array_by_range); exit;

                        for($k=0;$k<count($menu_array_by_range);$k++){
                            $vtp->addSession($handle,"menuZone"); 
                            $vtp->setVar($handle,"menuZone.libelle",$menu_array_by_range[$k]["libelle"]);     
                            $vtp->setVar($handle,"menuZone.prix_actuel",number_format($menu_array_by_range[$k]["prix"],2));   
                            
                            $prix_modif = $zone_modif->get_price_modif($menu_array_by_range[$k]["mi_seq"], $_REQUEST["priceZone"]);
                            
                            //$vtp->setVar($handle,"menuZone.prix_modifie",number_format($prix_modif,2));  
                            $vtp->setVar($handle,"menuZone.prix_modifie",number_format(str_replace(",",".",$prix_modif),2)); 
                            
                            //$vtp->setVar($handle,"menuZone.prix_modifie",number_format($menu_array_by_range[$k]["modifie"],2));     
                            $vtp->setVar($handle,"menuZone.prix_bronze",number_format($menu_array_by_range[$k]["bronze"],2));     
                            $vtp->setVar($handle,"menuZone.prix_silver",number_format($menu_array_by_range[$k]["silver"],2));     
                            $vtp->setVar($handle,"menuZone.prix_gold",number_format($menu_array_by_range[$k]["gold"],2));     
                            $vtp->closeSession($handle,"menuZone"); 
                        }
                    $vtp->closeSession($handle,"menuCategorieZone"); 
                 }
            }
            
            
            /**
            $vtp->addSession($handle,"menuCategorieZone"); 
            $vtp->setVar($handle,"menuCategorieZone.categorie",'categorie');     
                $vtp->addSession($handle,"menuZone"); 
                //$vtp->setVar($handle,"itemZone.categorie",'categorie');     
                $vtp->closeSession($handle,"menuZone"); 
            $vtp->closeSession($handle,"menuCategorieZone"); 
            */
            
        $zoneIndex = (isset($_REQUEST["priceZone"])) ? "?zoneIndex=".$_REQUEST["priceZone"] : "";
            
        $vtp->setVar($handle,"mainZone.zoneIndex",$zoneIndex);
            
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom); 
        $vtp->setVar($handle,"mainZone.priceZone",$priceZone);
        $vtp->setVar($handle,"mainZone.dateModif",$price_modif->date_modif);
        //$vtp->setVar($handle,"mainZone.zoneOptions",$store_params->getZoneOptions($acces_email, $selected));
        //$vtp->setVar($handle,"mainZone.restaurantOptions",$store_params->getRestaurantOptions($acces_email, $selected, $obj_num));	
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

