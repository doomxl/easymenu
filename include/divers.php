<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

   include_once("../classes/Store_params.class.php"); 
   include_once("../classes/Item.class.php");
   
   //include_once("../include/divers.class.php");

    function print_pre($obj){
        print('<pre>');
        print_r($obj);
        print('</pre>');
    } 
    
    function tdox_post_array($post_array){
        $result_array = array(); // initialisation du tableau array_result
        foreach($post_array as $key => $value){  
            if("post_" == substr($key, 0, 5)) $result_array[$key] = $value;
        }
        return$result_array; 
    }
    
    function linearizePostArray($post_array,$nbr_colonnes){                    
        $post_array = tdox_post_array($post_array); 
        $j = 0; // $post_array index
        $result_array = array(); // initialisation du tableau array_result
        $index = 0; // index des colonnes dans le tableau array result.
        foreach($post_array as $key => $value){            
            $n = $j % $nbr_colonnes;            
            //echo("$j - $n - $index <br/>");
             preg_match('@^post_(.*)[_](.*)@i',$key, $matches); // index 2 dans le tableau. 
             //$key2 = $matches[1];
            $result_array[$index][$matches[1]] = $value;   
            
            $j++;
            if(($n+1) == $nbr_colonnes) $index++;
        }        
        return $result_array;                
    }
    
    
    function do_query($array,$nbr_colonnes,$modif_id){
        $result_array = linearizePostArray($array,$nbr_colonnes);
        foreach($result_array as $key => $value){ 
            
        }
    }
    
    function fromPostToQuery($array,$nbr_colonnes,$modif_id){
        $query = "";
        $result_array = linearizePostArray($array,$nbr_colonnes);
        foreach($result_array as $key => $value){ 
            
            if($value["prix_modif"] != null){
            
            $query .= "  INSERT INTO MI_PRICE_MODIF 
                            (id, modif_id, carte_menu, price)
                        VALEUS
                            ('".$value["mi_seq"]."','".$modif_id."','".$value["type"]."','".$value["prix_modif"]."')
                        <br/>";
            }
        }
        return $query;
    }
    
    function group_categories($array){
        $result_array = array();
        foreach ($array as $key => $value){
            //print_pre($value);
            $const_enfant = (is_null($value["VALUE"])) ? "0" : $value["VALUE"]; 
            $result_array[$value["WEB_NAME"]][] = array($value["BEGIN"],$value["END"],$value["SET_UP_TYPE"],$const_enfant);
        }
        
        //print_pre($result_array);
        
        return $result_array;
    }
    
    function fill_modif_price($val,$session_array){
        
        $str = "post_prix_modif_".$val["mi_seq"];        
        if(isset($session_array[$str])){            
            return $session_array[$str];
        }else{                       
            return (strlen($val["modifie"]) == 0) ? '' : number_format(str_replace(",",".",$val["modifie"]),2);
        }             
    }
    
    function encodeForURL ($stringArray) {
        $s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray),9))), '+/=', '-_,');
        return $s;
    }

    function decodeFromURL ($stringArray) {
        $s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '-_,', '+/=')))));
        return $s;
    }
    
    function getAllRestaurants($email,$selected_elements_array){
        $store_params = new Store_params();
        $allRestaurantsArray = $store_params->getAllRestaurantArray($email);
        
        $tmp = array();
        foreach ($allRestaurantsArray as $key => $value) {
            $tmp[] = array (
                                "obj_num" => $value["obj_num"],
                                "name" => $value["rest_name"],
                                "store_seq" => $value["store_id"]               
                            );
        }             
        
        $final_array = array_minus($tmp,$selected_elements_array);
        foreach ($final_array as $key => $value) {
            $result .= "<option value=\"".sprintf('%05d',$value["obj_num"])." - ".$value["name"]."\">&nbsp;".sprintf('%05d',$value["obj_num"])." - ".$value["name"]."</option>\n"; 
        }
        return $result; 
    }
    
    function getAllDiscounts($selected_elements_array){
        $result = "";
        $item = new Item();
        $discountArray = $item->getDiscounts();              
        $final_array = array_minus($discountArray,$selected_elements_array);
        foreach ($final_array as $key => $value) {
            $result .= "<option value=\"".sprintf('%07d',$value["obj_num"])." - ".$value["name"]."\">&nbsp;".$value["name"]."</option>\n"; 
        }                                                    
        return $result; 
    }
    
    function get_selected_elements_options($array,$typePromo){
        //print("**********------------");
        //print_pre($array);
        if($typePromo == "1"){            
            foreach ($array as $key => $value) {
                $result .= "<option value=\"".sprintf('%07d',$value["obj_num"])." - ".$value["name"]."\">&nbsp;".$value["name"]."</option>\n"; 
            }
        }else{
            
            foreach ($array as $key => $value) {
                $result .= "<option value=\"".sprintf('%05d',$value["obj_num"])." - ".$value["name"]."\">&nbsp;".sprintf('%05d',$value["obj_num"])." - ".$value["name"]."</option>\n"; 
            }
        }
        return $result; 
    }
    
    function get_availabe_elements_options($typePromo,$email,$selected_elements_array){ // sprintf('%05d',$restaurantArray[$i]["obj_num"])
        $result = "";
        if($typePromo == "1"){
            $result = getAllRestaurants($email,$selected_elements_array);
        }else{
            $result = getAllDiscounts($selected_elements_array);                                                          
        }
        return $result; 
    }
    
    function array_minus($array1,$array2){
        $result_array = array();
        foreach ($array1 as $key => $value) {
            if(!(in_array($value, $array2))) $result_array[] = $value;
        }
        return $result_array;
    }
    
    function diff_array($arrayA,$arrayB){
        //$array1 = array('un', 'deux', 'trois', 'quatre');
        //$array2 = array('trois', 'quatre', 'cinq', 'six');
        
		$arraytoto = array();
		
		foreach($arraytoto as $key => $value) {
		}
		
		/*
		print('array A');
		print_pre($arrayA);
		print('array B');
		print_pre($arrayB);
		
		print('len A = '.count($arrayA).'<br/>');
		print('len B = '.count($arrayB).'<br/>');
		*/
		
        $array1 = $array2 = $empty_array = array();
        
        //sensé renvoyer une liste de deux tableau (si on part du pricipe que array1 est le tableau de depart)
        // array_retrait = array('un', 'deux'); array_ajout = array('cinq', 'six')
        
		if(count($arrayA)>0){
			foreach ($arrayA as $key => $value) {
				list($promo_id,$promo_name) = split("-",$value); 
				$array1[] = intval(trim($promo_id))." - ".trim($promo_name);
			}
		}
		
        if(count($arrayB)>0){
			foreach ($arrayB as $key => $value) {
				list($promo_id,$promo_name) = split("-",$value); 
				$array2[] = intval(trim($promo_id))." - ".trim($promo_name);
			}
        }
		
        $array1 = (isset($array1)) ? $array1 : $empty_array;
        $array2 = (isset($array2)) ? $array2 : $empty_array;
        
        $array_del = array();
        
        foreach ($array1 as $key => $value) {
            if(!(in_array($value, $array2))) $array_del[] = $value;
        }
        
        $array_add = array();
        
        foreach ($array2 as $key => $value) {
            if(!(in_array($value, $array1))) $array_add[] = $value;
        }        
        
        $result = array($array_del,$array_add); 
        return $result;
    }
    
?>
