<?php
    /*
     * Connexion à l'active directory et procedures de filtrage de profils
     */     

    include_once("../classes/Easyadmin.class.php");

    $easyadmin =  new Easyadmin();
    $easyadmin->charger();
            
    define("EM_SERVER", $easyadmin->em_server);
            
    class Store_params {
             
        var $connexion = "";
        
        const SQL_SERVER = "parsql01";
        const SQL_LOGIN = "webreport";
        const SQL_PASSWD = "kfcreport";        
        const SQL_DATABASE = "kfcstoredata";         
        
        // Configure connection parameters
        //const $db_host        = "192.168.156.113";
        
        //const SYBASE_HOST = "parem02.int.tgr.net";
        const SYBASE_HOST = "parem02";
        //const SYBASE_HOST = "192.168.121.103";
        const SYBASE_SERVER_NAME = "sqlPAREM02";
        const SYBASE_DB_NAME = "micros";
        const SYBASE_DB_FILE= 'micros.db';
        const SYBASE_CONN_NAME   = "easymenu";
        const SYBASE_USER = "custom";
        const SYBASE_PASSWD = "custom";
        
        function __construct(){
                    
        }
        
        /*
         * 
         *  SYBASE SERVER FUNCTIONS
         * 
         */
        
        static function get_SYBASE_HOST(){
            
            $easyadmin =  new Easyadmin();
            $easyadmin->charger();  
            //print("server em : parem0".$easyadmin->em_server);
            $result = "parem0".$easyadmin->em_server;
            return $result;
        }
        
        static function get_SYBASE_SERVER_NAME(){
            $easyadmin =  new Easyadmin();
            $easyadmin->charger();                       
            $result = "sqlPAREM0".$easyadmin->em_server;
            return $result;
        }
        
        function connection_str(){
            $conn_str =     "Driver={Adaptive Server Anywhere 9.0};";
            //$conn_str .=    "CommLinks=tcpip(Host=".Store_params::SYBASE_HOST.");"; 
            //$conn_str .=    "ServerName=".Store_params::SYBASE_SERVER_NAME.";";
            
            $conn_str .=    "CommLinks=tcpip(Host=".Store_params::get_SYBASE_HOST().");"; 
            $conn_str .=    "ServerName=".Store_params::get_SYBASE_SERVER_NAME().";";
            
            $conn_str .=    "DatabaseName=".Store_params::SYBASE_DB_NAME.";";
            $conn_str .=    "DatabaseFile=".Store_params::SYBASE_DB_FILE.";";
            $conn_str .=    "ConnectionName=".Store_params::SYBASE_CONN_NAME.";";
            $conn_str .=    "uid=".Store_params::SYBASE_USER.";pwd=".Store_params::SYBASE_PASSWD;
            return $conn_str; 
        } 
        
        function sybase_odbc_connect($connect_string,$db_user,$db_pass){
            //print($connect_string.'<br>');
            //print($db_user.'<br>');
            //print($db_pass.'<br>');
            $connexion = odbc_connect($connect_string,$db_user,$db_pass) or die('Could not connect to the server');            
            return $connexion;
        }
        
        /**
        $conn = odbc_connect($connect_string,$db_user,$db_pass);
      if (!$conn) {
        die('Could not connect: ' . mysql_error());
      }
        */
        
        function sybase_odbc_query($conn,$query){
               $result = odbc_exec($conn,$query) or die('Query failed');               
               return $result;
        }
        
        function getPriceZoneArray($email){ 
            $priceZoneArray = array();                                    
            //$priceZoneArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query =    "   SELECT distinct                          
                            price_tier_def.price_tier_seq,
                            price_tier_def.obj_num,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL
                            and rest_def.obj_num in ".$this->getEnumStoreIDs(func_get_arg(0))."
                            order by price_tier_def.obj_num 
                            ";

            //print("<br>$query<br>");

            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $priceZoneArray[] = array(
                                            "price_tier_seq" => odbc_result($result, 'price_tier_seq'),
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "name" => odbc_result($result, 'name')
                                        );
            }           
            return $priceZoneArray;
        }
        
		 function getPriceZoneArray_level($email,$level){ 
            $priceZoneArray = array();                                    
            //$priceZoneArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
			$where = ($level == 20)  ? "and price_tier_def.name not in ('Gold','Silver','Bronze') " : "";
            $query =    "   SELECT distinct                          
                            price_tier_def.price_tier_seq,
                            price_tier_def.obj_num,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
							$where
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL
                            and rest_def.obj_num in ".$this->getEnumStoreIDs(func_get_arg(0))."
                            order by price_tier_def.obj_num 
                            ";

            //print("<br>$query<br>");

            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $priceZoneArray[] = array(
                                            "price_tier_seq" => odbc_result($result, 'price_tier_seq'),
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "name" => odbc_result($result, 'name')
                                        );
            }           
            return $priceZoneArray;
        }
		
		
        function getPriceZoneIndex($searchTxt){
            $restaurantArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            
            $rest_crit = (is_numeric($searchTxt)) ? "and rest_def.obj_num = $searchTxt" : "and rest_def.rest_name = '$searchTxt'";
            
            if(strlen($searchTxt) == 0) $rest_crit = "";
            
            $query =    "   SELECT distinct
                            
                            rest_def.rest_name,
                            rest_def.store_id,                           
                            price_tier_def.name,
                            price_tier_def.obj_num
                           
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL                            
                            $rest_crit 
                            order by  rest_def.rest_name
                            ";
            
           
            //print_pre($query);
            
            $result = $this->sybase_odbc_query($connexion,$query);
            
            //print("je suis ici<br/>");
            
            
            $row = odbc_fetch_object($result);
            /*
            print("<pre>");
            print_r($row);
            print("<pre>");
            /*
            exit();
             * 
             */
            /*
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "rest_name" => odbc_result($result, 'rest_name'),
                                            "store_id" => odbc_result($result, 'store_id'),
                                            "price_tier_name" => odbc_result($result, 'name')
                                        );
            }
             * 
             */
            return $row->obj_num;
        }
        
        
        function getRestaurantArray($email,$obj_num){
            $restaurantArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query =    "   SELECT distinct
                            rest_def.obj_num,
                            rest_def.rest_name,
                            rest_def.store_id,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL
                            and price_tier_def.obj_num = ".$obj_num."
                            and rest_def.obj_num in ".$this->getEnumStoreIDs($email)."    
                            order by  rest_def.rest_name
                            ";
            
           
            //print_pre($query);
            
            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "rest_name" => odbc_result($result, 'rest_name'),
                                            "store_id" => odbc_result($result, 'store_id'),
                                            "price_tier_name" => odbc_result($result, 'name')
                                        );
            }
            return $restaurantArray;
        }
        
         function getRestaurantArray_level($email,$obj_num,$level){
            $restaurantArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
			$where = ($level == 20)  ? "and price_tier_def.name not in ('Gold','Silver','Bronze') " : "";
            $query =    "   SELECT distinct
                            rest_def.obj_num,
                            rest_def.rest_name,
                            rest_def.store_id,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
							$where
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL
                            and price_tier_def.obj_num = ".$obj_num."
                            and rest_def.obj_num in ".$this->getEnumStoreIDs($email)."    
                            order by  rest_def.rest_name
                            ";
            
           
            //print_pre($query);
            
            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "rest_name" => odbc_result($result, 'rest_name'),
                                            "store_id" => odbc_result($result, 'store_id'),
                                            "price_tier_name" => odbc_result($result, 'name')
                                        );
            }
            return $restaurantArray;
        }
        
        
        function getCommonRestaurantArray($email,$post){
             //[0] => 0000036 - 10% franchis           
            $selected_elements_array = array();
            foreach ($post as $key => $value){                               
                list($obj_num,$name_1) = split("-",$value);                                                              
                $selected_elements_array[] = intval(trim($obj_num));
            }
            
            $restaurantArray = array();
            $tmp = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
                                                           
            foreach ($selected_elements_array as $key => $value){
                $tmp[] =    "   select s.store_seq as store_seq,s.obj_num as obj_num,s.name as name from custom.kfc_easymenu_subsdsc v
                                left outer join micros.em_store_def s on s.store_seq = v.subscriber_seq
                                where  v.obj_num in (".$value.") and
                                s.obj_num in ".$this->getEnumStoreIDs($email);
            }                       

            $query = implode(" intersect ", $tmp);                        
            
            $query .= " order by name "; 
            
            //print("<br/>$query<br/>");
            
            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "name" => odbc_result($result, 'name'),
                                            "store_seq" => odbc_result($result, 'store_seq')                                            
                                        );
            }
            return $restaurantArray;                        
        }
        
         function getCommonDiscountsArray($email,$post){
             //[0] => 0000036 - 10% franchis           
            $selected_elements_array = array();
            foreach ($post as $key => $value){                               
                list($obj_num,$name_1) = split("-",$value);                                                              
                $selected_elements_array[] = intval(trim($obj_num));
            }
            
            $restaurantArray = array();
            $tmp = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
                                                           
            foreach ($selected_elements_array as $key => $value){                                                            
                
                $tmp[] =    "   select dsvc_seq,v.obj_num as obj_num,v.name as name from custom.kfc_easymenu_subsdsc v
                                left outer join micros.em_store_def s on s.store_seq = v.subscriber_seq
                                where  s.obj_num in (".$value.")";
            }                       

            $query = implode(" intersect ", $tmp);                        
            
            $query .= " order by name asc "; 
            
            //print("$query<br>");
            
            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "name" => odbc_result($result, 'name'),
                                            "dsvc_seq" => odbc_result($result, 'dsvc_seq')                                            
                                        );
            }
            return $restaurantArray;                        
        }
        
         function getAllRestaurantArray($email){
            $restaurantArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query =    "   SELECT distinct
                            rest_def.obj_num,
                            rest_def.rest_name,
                            rest_def.store_id,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL                            
                            and rest_def.obj_num in ".$this->getEnumStoreIDs($email)."    
                            order by  rest_def.rest_name
                            ";
            
            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "rest_name" => odbc_result($result, 'rest_name'),
                                            "store_id" => odbc_result($result, 'store_id'),
                                            "price_tier_name" => odbc_result($result, 'name')
                                        );
            }
            return $restaurantArray;
        }
        
		 function getAllRestaurantArray_level($email,$level){
            $restaurantArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
			$where = ($level == 20)  ? "and price_tier_def.name not in ('Gold','Silver','Bronze') " : "";
            $query =    "   SELECT distinct
                            rest_def.obj_num,
                            rest_def.rest_name,
                            rest_def.store_id,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
							$where
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL                            
                            and rest_def.obj_num in ".$this->getEnumStoreIDs($email)."    
                            order by  rest_def.rest_name
                            ";
            
            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "rest_name" => odbc_result($result, 'rest_name'),
                                            "store_id" => odbc_result($result, 'store_id'),
                                            "price_tier_name" => odbc_result($result, 'name')
                                        );
            }
            return $restaurantArray;
        }
		
        function getPriceZoneNumFromName($name){
            $restaurantArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query =    "   SELECT distinct
                            rest_def.obj_num,
                            rest_def.rest_name,
                            rest_def.store_id,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL                            
                            and rest_def.obj_num in ".$this->getEnumStoreIDs($email)."    
                            order by  rest_def.rest_name
                            ";
            
            $query =    "
                            select * custom.v_kfc_easymenu where price_tier_name = '$name'
            
                            ";
            
           $query =    "
                            select * v_kfc_easymenu
            
                            ";
            
           
            $query =    "   SELECT distinct
                            price_tier_def.obj_num,
                            rest_def.rest_name,
                            rest_def.store_id,
                            price_tier_def.name
                            FROM micros.rest_def,
                            micros.rest_pricing_def,
                            micros.price_tier_def
                            where rest_pricing_def.store_id = rest_def.store_id
                            and rest_pricing_def.price_tier_seq = price_tier_def.price_tier_seq
                            and rest_pricing_def.price_grp_seq = 1
                            and rest_pricing_def.effective_to is NULL                            
                            and price_tier_def.name = '$name'    
                            order by  rest_def.rest_name
                            ";
           
           
            //print_pre($query);                         
            
            $result = $this->sybase_odbc_query($connexion,$query);
            while(odbc_fetch_row($result)){
                $restaurantArray[] = array(                                            
                                            "obj_num" => odbc_result($result, 'obj_num')
                                        );
            }
            return $restaurantArray[0]["obj_num"];
        }
        
        function getPriceZoneName($priceZoneArray){ // ATTention si user pas de zoner de prix try catch.
            return $priceZoneArray[0]["price_tier_name"];            
        }        
        
        function print_zoneOptions_selected($i,$priceZoneArray,$selected){ // ATTention si user pas de zoner de prix try catch.
            $default_index = $priceZoneArray[0]["obj_num"];
            $selected = (isset($selected)) ? $selected : $default_index;
            return (intval($i) == $selected) ? "selected" : "";
        }
        
         function print_restaurant_selected($i,$restaurantArray,$selected){ // ATTention si user pas de zoner de prix try catch.
            $default_index = $restaurantArray[0]["obj_num"];
            $selected = (isset($selected)) ? $selected : $default_index;
            return (intval($i) == $selected) ? "selected" : "";
        }
        
        function getZoneOptions($email,$selected){
            $str = "";
            $priceZoneArray = $this->getPriceZoneArray($email);
            for($i=0;$i<count($priceZoneArray);$i++){
                $str .= "<option value=".$priceZoneArray[$i]["obj_num"]." ".$this->print_zoneOptions_selected($priceZoneArray[$i]["obj_num"],$priceZoneArray, $selected).">&nbsp;".$priceZoneArray[$i]["name"]."</option>";
            }
            return $str;
        }
        
		function getZoneOptions_level($email,$selected,$level){
            $str = "";
            $priceZoneArray = $this->getPriceZoneArray_level($email,$level);
            for($i=0;$i<count($priceZoneArray);$i++){
                $str .= "<option value=".$priceZoneArray[$i]["obj_num"]." ".$this->print_zoneOptions_selected($priceZoneArray[$i]["obj_num"],$priceZoneArray, $selected).">&nbsp;".$priceZoneArray[$i]["name"]."</option>";
            }
            return $str;
        }
		
        function getRestaurantOptions($email,$selected,$zoneIndex){
            $str = ""; 
            if(func_num_args() == 4){    
                //print("quatrieme arg : ".func_get_arg(3)."<br>");
                $str = "";
                $restaurantArray = $this->getRestaurantArray($email,$zoneIndex);               for($i=0;$i<count($restaurantArray);$i++){
                    $selected_rest = "";
                    if(is_numeric(func_get_arg(3))){
                        //print("Je suis ici 1<br>");
                        if($restaurantArray[$i]["obj_num"] == func_get_arg(3)) $selected_rest = "selected";
                    }else{
                        //print("Je suis ici 2 ".$restaurantArray[$i]["rest_name"]." ".func_get_arg(3)."<br>");
                        
                        if($restaurantArray[$i]["rest_name"] == strtoupper(func_get_arg(3))) $selected_rest = "selected";
                    }
                    
                        $str .= "<option value=".$restaurantArray[$i]["obj_num"]." $selected_rest>&nbsp;".sprintf('%05d',$restaurantArray[$i]["obj_num"])."&nbsp;-&nbsp;".$restaurantArray[$i]["rest_name"]."</option>";
                }
            }else{
                $str = "";
                $restaurantArray = $this->getRestaurantArray($email,$zoneIndex);
                for($i=0;$i<count($restaurantArray);$i++){
                    $str .= "<option value=".$restaurantArray[$i]["obj_num"]." ".$this->print_restaurant_selected($restaurantArray[$i]["obj_num"],$restaurantArray, $selected).">&nbsp;".sprintf('%05d',$restaurantArray[$i]["obj_num"])."&nbsp;-&nbsp;".$restaurantArray[$i]["rest_name"]."</option>";
                }
            }
            return $str;
        }                        
        
		function getRestaurantOptions_level($email,$selected,$zoneIndex,$level){
            $str = ""; 
            if(func_num_args() == 5){    
                //print("quatrieme arg : ".func_get_arg(3)."<br>");
                $str = "";
                $restaurantArray = $this->getRestaurantArray_level($email,$zoneIndex,$level);               
					for($i=0;$i<count($restaurantArray);$i++){
                    $selected_rest = "";
                    if(is_numeric(func_get_arg(4))){
                        //print("Je suis ici 1<br>");
                        if($restaurantArray[$i]["obj_num"] == func_get_arg(4)) $selected_rest = "selected";
                    }else{
                        //print("Je suis ici 2 ".$restaurantArray[$i]["rest_name"]." ".func_get_arg(3)."<br>");
                        
                        if($restaurantArray[$i]["rest_name"] == strtoupper(func_get_arg(4))) $selected_rest = "selected";
                    }
                    
                        $str .= "<option value=".$restaurantArray[$i]["obj_num"]." $selected_rest>&nbsp;".sprintf('%05d',$restaurantArray[$i]["obj_num"])."&nbsp;-&nbsp;".$restaurantArray[$i]["rest_name"]."</option>";
                }
            }else{
                $str = "";
                $restaurantArray = $this->getRestaurantArray($email,$zoneIndex);
                for($i=0;$i<count($restaurantArray);$i++){
                    $str .= "<option value=".$restaurantArray[$i]["obj_num"]." ".$this->print_restaurant_selected($restaurantArray[$i]["obj_num"],$restaurantArray, $selected).">&nbsp;".sprintf('%05d',$restaurantArray[$i]["obj_num"])."&nbsp;-&nbsp;".$restaurantArray[$i]["rest_name"]."</option>";
                }
            }
            return $str;
        }     
		
        /*
         * 
         *  SQL SERVER FUNCTIONS
         * 
         */
        
        function sql_connect($server,$login,$passwd){
            $connexion = mssql_connect($server,$login,$passwd) or die('Could not connect to the server');            
            return $connexion;
        }
        
        function sql_select_database($database){
            mssql_select_db($database) or die('Could not select a database.');
        }
        
        function sql_query($query){
               $result = mssql_query($query) or die('A error occured: ' . mysql_error());
               return $result;
        }
        
        function sql_count($result){
            $count = mssql_num_rows($result);
            return $count;
        }
        
        function sql_close($connexion){
            mssql_close($connexion);
        }
        
        function check_email_in_area_manager($email){
            $this->connexion = $this->sql_connect(Store_params::SQL_SERVER,Store_params::SQL_LOGIN,Store_params::SQL_PASSWD);
            $this->sql_select_database(Store_params::SQL_DATABASE);
            $query = "SELECT * FROM dbo.stores WHERE [Area-Manager] = '$email'"; // philippe.prat@yum.com
            return $this->sql_query($query);                         
        }
        
        function check_email_in_area_description($email){            
            $this->connexion = $this->sql_connect(Store_params::SQL_SERVER,Store_params::SQL_LOGIN,Store_params::SQL_PASSWD);
            $this->sql_select_database(Store_params::SQL_DATABASE);
            $query = "SELECT * FROM dbo.stores WHERE [Area-Description] = '$email'"; // philippe.prat@yum.com
            return $this->sql_query($query);              
        }
        
        function all_store_acces(){
            $this->connexion = $this->sql_connect(Store_params::SQL_SERVER,Store_params::SQL_LOGIN,Store_params::SQL_PASSWD);
            $this->sql_select_database(Store_params::SQL_DATABASE);
            $query = "SELECT * FROM dbo.stores"; // philippe.prat@yum.com
            return $this->sql_query($query); 
        }
        
        function getSoreIDs($email){
            $result_array = array();
            
            //print("<br>".$_SESSION['level']."<br>");
            
            if($_SESSION['level'] >= 30){
                $result = $this->all_store_acces();
                while ($row = mssql_fetch_object($result)){
                    $result_array[] = $row->Store;
                }
            }else{            
                $result = $this->check_email_in_area_manager($email);
                if($this->sql_count($result) > 0){               
                    //print("test 1");
                    while ($row = mssql_fetch_object($result)){
                        $result_array[] = $row->Store;
                    }
                }else{
                    $result = $this->check_email_in_area_description($email);           
                    if($this->sql_count($result) > 0){
                        //print("test 2");
                        while ($row = mssql_fetch_object($result)){
                            $result_array[] = $row->Store;
                        }
                    }else{

                    }
                }
            }
            
            
            $this->sql_close($this->connexion);
            return $result_array;
        }
        /**
        function getStoreNames2($email){
            $result_array = array();
            $result_array = $this->getAllRestaurantArray($email);
            return $result_array;
        }
        */
        function getSoreNames($email){
            $result_array = array();
            
            //print("<br>SESSION LEVEL = ".$_SESSION['level']."<br>");
            
            if($_SESSION['level'] >= 30){
                $result = $this->all_store_acces();
                while ($row = mssql_fetch_array($result)){
                    
                    /**
                    print('<pre>');
                    print_pre($row);
                    print('</pre>');
                    
                    exit();
                    */
                    $result_array[] = $row["Store-Description"];
                }
            }else{            
                $result = $this->check_email_in_area_manager($email);
                if($this->sql_count($result) > 0){               
                    //print("test 1");
                    while ($row = mssql_fetch_object($result)){
                        $result_array[] = $row["Store-Description"];
                    }
                }else{
                    $result = $this->check_email_in_area_description($email);           
                    if($this->sql_count($result) > 0){
                        //print("test 2");
                        while ($row = mssql_fetch_object($result)){
                            $result_array[] = $row["Store-Description"];
                        }
                    }else{

                    }
                }
            }
            
            
            $this->sql_close($this->connexion);
            return $result_array;
        }
        
        function getEnumStoreIDs($email){
            $store_array = $this->getSoreIDs($email); // (6002,13001,13002,13006,13007,83002)
            
            //print_pre($email);
            //print_pre($store_array);
            
            $result = "(".$store_array[0].",";
            for($i=1;$i<count($store_array);$i++){
                $result .= $store_array[$i].",";
            }
            $result = (substr($result, 0,-1).")");  
            return $result;
        }
        
        
    }

?>
