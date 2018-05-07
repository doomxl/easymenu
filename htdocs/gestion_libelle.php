<?php
	include_once("../include/vtemplate.class.php"); 		// Inclusion du fichier	
        include_once("../include/divers.php");  
        
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");  
        include_once("../classes/Item_categorie.class.php");  
        include_once("../classes/Item.class.php");  
        include_once("../classes/Libelle_modif.class.php");  
        include_once("../classes/New_libelle.class.php");  
        include_once("../classes/Item.class.php");  
        include_once("../classes/Zone_modif.class.php"); 
        include_once("../classes/Price_modif.class.php");        
        
        
        
        include_once("auth.php");
        
        //ob_start();
        
        $active_directory = new Active_directory();          
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        $email = $active_directory->getEmail($user, $passwd);                
        
        $store_params = new Store_params();         
        $itemCategorie = new Item_categorie(); 
        $item = new Item();
        
        $acces_email = $email;  
        $transferredArray = array();            
         
        $store_array = $store_params->getSoreIDs($email);  
        $obj_num = $store_array[0];                 
        $priceZoneArray = $store_params->getPriceZoneArray($email);             
        $priceZone = $priceZoneArray[0]["name"];            
        
        $itemCategorie_carte_array = $itemCategorie->getItemCategorieEntries("Carte");
        $itemCategorie_carte_array2 = $itemCategorie->getItemCategorieEntries2("Carte");
        $itemCategorie_menu_array = $itemCategorie->getItemCategorieEntries("Menus");
        $itemCategorie_menu_array2 = $itemCategorie->getItemCategorieEntries2("Menus");                
        
        $itemCategorie_carte_array = group_categories($itemCategorie_carte_array2);
        $itemCategorie_menu_array = group_categories($itemCategorie_menu_array2);      
        
        $zone_modif = new Zone_modif();
        $price_modif = new Price_modif();
        
        $new_libelle = new New_libelle();
        $libelle_modif = new Libelle_modif();       
        $libelle_modif->charger();
       
        
        
        $price_modif->charger_zone($_REQUEST["priceZone"]);             
        
	$vtp = new VTemplate;                                           // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/gestion_libelle.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");         
            foreach ($itemCategorie_carte_array as $key => $value) {                    
                $items_array_by_range = $item->getItemsByRangeCarte($priceZone,$value,4,array());                               
                if(count($items_array_by_range) > 0){                
                    $vtp->addSession($handle,"itemCategorieZone");                      
                    $vtp->setVar($handle,"itemCategorieZone.categorie",$key);                           
                        for($k=0;$k<count($items_array_by_range);$k++){
                            $vtp->addSession($handle,"itemZone"); 
                            $vtp->setVar($handle,"itemZone.mi_seq",$items_array_by_range[$k]["mi_seq"]);  
                            $vtp->setVar($handle,"itemZone.obj_num",$items_array_by_range[$k]["obj_num"]);                            
                            $vtp->setVar($handle,"itemZone.libelle",$items_array_by_range[$k]["libelle"]);     
                            $vtp->setVar($handle,"itemZone.prix_actuel",number_format($items_array_by_range[$k]["prix"],2));                                                       
                            
                            $vtp->setVar($handle,"itemZone.nouveau_libelle",$new_libelle->getLibelle($items_array_by_range[$k]["mi_seq"]));     
                                                        
                            $vtp->setVar($handle,"itemZone.prix_bronze",number_format($items_array_by_range[$k]["bronze"],2));     
                            $vtp->setVar($handle,"itemZone.prix_silver",number_format($items_array_by_range[$k]["silver"],2));     
                            $vtp->setVar($handle,"itemZone.prix_gold",number_format($items_array_by_range[$k]["gold"],2));     
                            $vtp->closeSession($handle,"itemZone"); 
                        }
                    $vtp->closeSession($handle,"itemCategorieZone"); 
                }
            }
                                                
            foreach ($itemCategorie_menu_array as $key => $value) {                 
                 $menu_array_by_range = $item->getItemsByRangeMenu($priceZone,$value,4,array());                 
                 if(count($menu_array_by_range) > 0){  
                    $vtp->addSession($handle,"menuCategorieZone");  
                    $vtp->setVar($handle,"menuCategorieZone.categorie",$key);  
                        for($k=0;$k<count($menu_array_by_range);$k++){
                            $vtp->addSession($handle,"menuZone"); 
                            $vtp->setVar($handle,"menuZone.mi_seq",$menu_array_by_range[$k]["mi_seq"]);  
                            $vtp->setVar($handle,"menuZone.obj_num",$menu_array_by_range[$k]["obj_num"]);                            
                            $vtp->setVar($handle,"menuZone.libelle",$menu_array_by_range[$k]["libelle"]);     
                            $vtp->setVar($handle,"menuZone.prix_actuel",number_format($menu_array_by_range[$k]["prix"],2));
                            
                            $vtp->setVar($handle,"menuZone.nouveau_libelle",$new_libelle->getLibelle($menu_array_by_range[$k]["mi_seq"]));     
                            
                            $vtp->setVar($handle,"menuZone.prix_bronze",number_format($menu_array_by_range[$k]["bronze"],2));     
                            $vtp->setVar($handle,"menuZone.prix_silver",number_format($menu_array_by_range[$k]["silver"],2));     
                            $vtp->setVar($handle,"menuZone.prix_gold",number_format($menu_array_by_range[$k]["gold"],2));     
                            $vtp->closeSession($handle,"menuZone"); 
                        }
                    $vtp->closeSession($handle,"menuCategorieZone"); 
                 }
            }         
                   
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);
        $vtp->setVar($handle,"mainZone.date_modif",$libelle_modif->get_date_modif());
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
        
        //ob_flush();
?>

