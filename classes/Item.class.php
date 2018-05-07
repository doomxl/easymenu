<?php
    /*
     * Connexion à l'active directory et procedures de filtrage de profils
     */

    include_once("../include/divers.php");  
    include_once("../classes/Cnx_oracle.class.php");
    include_once("../classes/Store_params.class.php");

    class Item {
             
        var $connexion = "";
        
        //const SYBASE_HOST = "parem01.int.tgr.net";
        const SYBASE_HOST = "parem02.int.tgr.net";
        //const SYBASE_SERVER_NAME = "sqlPAREM01"; // prod
        const SYBASE_SERVER_NAME = "sqlPAREM02";    // test
        const SYBASE_DB_NAME = "micros";
        const SYBASE_DB_FILE= 'micros.db';
        const SYBASE_CONN_NAME   = "easymenu";
        const SYBASE_USER = "custom";
        const SYBASE_PASSWD = "custom";
                                  
        
        function __construct(){
                    
        }
        
        function connection_str(){
            $conn_str = "";
            $conn_str .=     "Driver={Adaptive Server Anywhere 9.0};";
            $conn_str .=    "CommLinks=tcpip(Host=".Store_params::get_SYBASE_HOST().");"; 
            $conn_str .=    "ServerName=".Store_params::get_SYBASE_SERVER_NAME().";";
            $conn_str .=    "DatabaseName=".Store_params::SYBASE_DB_NAME.";";
            $conn_str .=    "DatabaseFile=".Store_params::SYBASE_DB_FILE.";";
            $conn_str .=    "ConnectionName=".Store_params::SYBASE_CONN_NAME.";";
            $conn_str .=    "uid=".Store_params::SYBASE_USER.";pwd=".Store_params::SYBASE_PASSWD;
            return $conn_str; 
        } 
        
        function sybase_odbc_connect($connect_string,$db_user,$db_pass){
            $connexion = odbc_connect($connect_string,$db_user,$db_pass) or die('Could not connect to the server');            
            return $connexion;
        }
        
        function sybase_odbc_query($conn,$query){
            $result = odbc_exec($conn,$query) or die('Query failed');               
            return $result;
        }                
        
        
        function getCategorieItems2($zoneprix){
            $categorieItemsArray = array();
            
            
            
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query =    "   
                              select obj_num, name_1, menu_preset_amt_1, price_tier_name from custom.v_kfc_easymenu 
                                        where
                                        menu_preset_amt_1 is not NULL and
                                        price_tier_name = 'Z13-Louvradoux'                          
                                        order by obj_num, name_1
                            ";
            
            $query =    "   
                                        select * from custom.v_kfc_easymenu 
                                        where     
                                        carte_preset_amt_1 is not NULL and
                                        price_tier_name = 'Z13-Louvradoux'                                        
                            ";
            
            $result = $this->sybase_odbc_query($connexion,$query);
            
            $nbr = odbc_num_rows($result);
            print('nbr rows = '.$nbr.'<br>');
            print('<table border="1">');
            print('<tr><th>Obj_num</th><th>Libelle</th><th>Prix</th><th>Zone prix</th>');
            while($row = odbc_fetch_object($result)){
                print('<tr><td>&nbsp;'.$row->obj_num.'</td><td>&nbsp;'.$row->name_1.'</td><td>&nbsp;'.$row->carte_preset_amt_1.'</td><td>&nbsp;'.$row->price_tier_name.'</td></tr>');
            }
            print('</table>');
            
            /**
            while($arrayZZ = odbc_fetch_array($result)){
                $categorieItemsArray[] = $arrayZZ; 
            }
             * 
             */
            //print("<table>");
            /*
            while(odbc_fetch_row($result)){
                
                $categorieItemsArray[] = array(
                                            "price_tier_seq" => odbc_result($result, 'price_tier_seq'),
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "name" => odbc_result($result, 'name')
                                        );
            }
             * 
             */
            return $categorieItemsArray;
        }               
        
        
        function getCategorieItems($zoneprix){
            $categorieItemsArray = array();
            
            
            
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query =    "   
                            select distinct t1.name_1 as a, t1.carte_preset_amt_1 as b, t1.price_tier_name as c, t2.carte_preset_amt_1 as d, t2.price_tier_name as e, t3.carte_preset_amt_1 as f, t3.price_tier_name as g, t4.carte_preset_amt_1 as h, t4.price_tier_name as i
                                from custom.v_kfc_easymenu as t2, custom.v_kfc_easymenu as t3, custom.v_kfc_easymenu as t4,

                                    (   select name_1, carte_preset_amt_1, price_tier_name from custom.v_kfc_easymenu 
                                        where
                                        carte_preset_amt_1 is not NULL and
                                        
                                        price_tier_name = 'Z13-Louvradoux') as t1 

                                where

                                    t1.name_1 = t2.name_1 and
                                    t2.name_1 = t3.name_1 and
                                    t3.name_1 = t4.name_1 and
                                    t2.carte_preset_amt_1 is not NULL and
                                    t3.carte_preset_amt_1 is not NULL and
                                    t4.carte_preset_amt_1 is not NULL and
                                    t2.price_tier_name = 'Bronze' and
                                    t3.price_tier_name = 'Silver' and
                                    t4.price_tier_name = 'Gold' 
                                
                                order by t1.name_1
                            ";
            
            $result = $this->sybase_odbc_query($connexion,$query);
            /**
            $nbr = odbc_num_rows($result);
            print('nbr rows = '.$nbr.'<br>');
            print('<table border="1">');
            print('<tr><th>Libelle</th><th>Prix</th><th></th><th>Bronze</th><th>Silver</th><th>Gold</th></tr>');
            while($row = odbc_fetch_object($result)){
                print('<tr><td>&nbsp;'.$row->a.'</td><td>&nbsp;'.$row->b.'</td><td></td><td>&nbsp;'.$row->d.'</td><td>&nbsp;'.$row->f.'</td><td>&nbsp;'.$row->h.'</td></tr>');
            }
            print('</table>');
            */
            /*
            while(odbc_fetch_row($result)){
                
                $categorieItemsArray[] = array(
                                            "price_tier_seq" => odbc_result($result, 'price_tier_seq'),
                                            "obj_num" => odbc_result($result, 'obj_num'),
                                            "name" => odbc_result($result, 'name')
                                        );
            }
             * 
             */
            return $categorieItemsArray;
        }               
                
        //function getItemsByRange2($priceZone,$itemCategorie_carte_array,"carte")
        
        function get_national_price_array(){
            $array_1 = array();
            $store_params = new Store_params();
            $query = "
                        select z.id as mi_seq, z.price as prix, p.zone_id as zone_id
                        from
                            kfcstorea01.easymenu_zone_modif z, kfcstorea01.easymenu_price_modif p
                        where
                            p.date_modif > CURRENT_DATE and
                            p.id = z.modif_id and                               
                            p.zone_id in (8, 9, 10)

                        order by zone_id, mi_seq
                        ";
            /*
             $query = "
                        select z.id as mi_seq, z.price as prix, p.zone_id as zone_id
                        from
                            kfcstorea01.easymenu_zone_modif z, kfcstorea01.easymenu_price_modif p
                        where
                           
                            p.id = z.modif_id                            
                            

                        order by zone_id, mi_seq
                        ";
            */
            $cnx_oracle = new Cnx_oracle();
            $connexion = $cnx_oracle->oracle_connect();
            $statement = $cnx_oracle->oracle_query($connexion,$query);
            $i = 3;
            while($row = oci_fetch_object($statement)){
                //print_pre($row);
                //print("reponse ".$i++."<br>");
                $priceZone = "";
                switch($row->ZONE_ID){                        
                    case 10 : $priceZone = "Bronze"; break;
                    case 9 : $priceZone = "Silver"; break;
                    case 8: $priceZone = "Gold"; break;
                }
                $array_1[$priceZone][$row->MI_SEQ] = str_replace(",",".",$row->PRIX);
            }    
            //print_pre($array_1);
            return $array_1; 
        }
        
        function get_national_carte_price_array($set_up_type){            
            //print("bp1<br>");
            $array_1 = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $type = ($set_up_type == "Menus") ? "menu_preset_amt_1" : "carte_preset_amt_1";
            //$query = "select $type as prix from custom.v_kfc_easymenu where mi_seq = $mi_seq and price_tier_name = '$priceZone'";
            $query = "
                        select mi_seq, $type as prix, price_tier_name as priceZone from custom.v_kfc_easymenu 
                        where 
                            price_tier_name in ('Silver', 'Bronze', 'Gold') ";
            //print("$query<br/>");
            $result = $this->sybase_odbc_query($connexion,$query);
            while($row = odbc_fetch_object($result)){
                $array_1[$row->priceZone][$row->mi_seq] = $row->prix;
            }
            /*
            print("bp2<br>");
            print_pre($array_1);
            print("bp3<br>");
             */ 
            return $array_1;            
              
            
        }
        
        function get_national_price_carte($priceZone,$mi_seq, $set_up_type){                
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $type = ($set_up_type == "Menus") ? "menu_preset_amt_1" : "carte_preset_amt_1";
            $query = "select $type as prix from custom.v_kfc_easymenu where mi_seq = $mi_seq and price_tier_name = '$priceZone'";
            $result = $this->sybase_odbc_query($connexion,$query);   
            $row = odbc_fetch_object($result);
            return $row->prix;            
        }
        
        function get_national_menu_price_array(){            
            $array_1 = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query = "
                        select mi_seq, menu_preset_amt_1 as prix, price_tier_name as priceZone from custom.v_kfc_easymenu 
                        where 
                            price_tier_name in ('Silver', 'Bronze', 'Gold') ";
            //print("$query<br/>");
            $result = $this->sybase_odbc_query($connexion,$query);
            while($row = odbc_fetch_object($result)){
                $array_1[$row->priceZone][$row->mi_seq] = $row->prix;
            }

            //print_pre($array_1);
            
            return $array_1;            
        }
        
        function get_national_price_menu($priceZone,$mi_seq){            
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query = "select menu_preset_amt_1 from custom.v_kfc_easymenu where mi_seq = $mi_seq and price_tier_name = '$priceZone'";
            //print("$query<br/>");
            $result = $this->sybase_odbc_query($connexion,$query);
            $row = odbc_fetch_object($result);
            return $row->menu_preset_amt_1;            
        }
        
        
        function getItemsByRangeCarte($priceZone,$array,$prix_futur,$oracle_array){
            //print("bp1");
            $categorieItemsArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            //print("range des bucketes");
            //print_pre($array); exit();
            $tmp_array = array();
            foreach ($array as $key => $value) {
                $query1 = "
                                select distinct mi_seq, obj_num, name_1, carte_preset_amt_1 as prix from custom.v_kfc_easymenu
                                where
                                    carte_preset_amt_1 is not null and
                                    price_tier_name = '$priceZone' and                                    
                                    mi_seq  not in (select sub_mi_seq from custom.v_kfc_easymenu where price_tier_name = '$priceZone' and sub_mi_seq is not NULL) and
                                    
                                    obj_num between ".$value[0]." and ".$value[1]."
                                    order by obj_num
                            ";
                
                $query2 = "
                                select distinct mi_seq, obj_num, name_1, menu_preset_amt_1 as prix  from custom.v_kfc_easymenu
                                where
                                    menu_preset_amt_1 is not null and 
                                    price_tier_name = '$priceZone' and 
                                    
                                    obj_num between ".$value[0]." and ".$value[1]."
                                    order by obj_num
                            ";
                
                $query = ($value[2] == "Menus") ? $query2 : $query1;
                
				//if($value[2] == "Menus") print($query.'<br/>');
				
                $result = $this->sybase_odbc_query($connexion,$query);
                                                               
				$menu_type = ($value[2] == "Menus") ? "menu" : "carte";
                
                
                
                while($row = odbc_fetch_object($result)){
                    //$m++;
                    
                   $prix_bronze = $prix_silver = $prix_gold = "0";

                    if(isset($prix_futur) && ($prix_futur == "1")){                    
                        $prix_bronze = $oracle_array["Bronze"][$row->mi_seq];
                        $prix_silver = $oracle_array["Silver"][$row->mi_seq];
                        $prix_gold = $oracle_array["Gold"][$row->mi_seq];                                       
                    }else{                    
                        $prix_bronze = $this->get_national_price_carte('Bronze', $row->mi_seq, $value[2]);                    
                        $prix_silver = $this->get_national_price_carte('Silver', $row->mi_seq, $value[2]);
                        $prix_gold = $this->get_national_price_carte('Gold', $row->mi_seq, $value[2]);
                    }
                    
                    $categorieItemsArray[] = array(
                                                    "mi_seq" => $row->mi_seq,
                                                    "obj_num" => $row->obj_num,
                                                    "libelle" => $row->name_1,
                                                    "prix" => $row->prix,
                                                    "modifie" => "",
													"menu_type" => $menu_type,
                                                    "bronze" => $prix_bronze,
                                                    "silver" => $prix_silver,
                                                    "gold" => $prix_gold
                                                ); 
                }                
            }
            /*
            if($value[2] == "Menus"){
                print("\$m = $m<br>");
                print_pre($categorieItemsArray);
            }
             * 
             */
            return $categorieItemsArray;
        }
        
        
        function getItemsByRangeMenu($priceZone,$array,$prix_futur,$oracle_array){
            $categorieItemsArray = array();
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            //print("range des bucketes");
            //print_pre($array); exit();
            $tmp_array = array();
            foreach ($array as $key => $value) {
                $query = "
                                select distinct mi_seq, obj_num, name_1, menu_preset_amt_1 + ".str_replace(",",".",$value[3])." price1 from custom.v_kfc_easymenu
                                where
                                    menu_preset_amt_1 is not null and 
                                    price_tier_name = '$priceZone' and 
                                    
                                    obj_num between ".$value[0]." and ".$value[1]."
                                    order by obj_num
                            ";
                /**
                $query = "
                                select distinct mi_seq, obj_num, name_1, carte_preset_amt_1 from custom.v_kfc_easymenu
                                where
                                    carte_preset_amt_1 is not null and
                                    price_tier_name = '$priceZone' and                                    
                                    mi_seq  not in (select sub_mi_seq from custom.v_kfc_easymenu where price_tier_name = '$priceZone' and sub_mi_seq is not NULL) and
                                    obj_num < 90000 and
                                    obj_num between ".$value[0]." and ".$value[1]."
                                    order by obj_num
                            ";
                 * 
                 */
                //print("$query<br/>");   
                
                //$national_price_menu_array = (isset($prix_futur) && ($prix_futur == "1")) ? $oracle_array : $this->get_national_menu_price_array($value[2]);
                
                                  
                
                $result = $this->sybase_odbc_query($connexion,$query);
                while($row = odbc_fetch_object($result)){
                    
                    $prix_bronze = $prix_silver = $prix_gold = "0";
                
                    if(isset($prix_futur) && ($prix_futur == "1")){                    
                        $prix_bronze = $oracle_array["Bronze"][$row->mi_seq];
                        $prix_silver = $oracle_array["Silver"][$row->mi_seq];
                        $prix_gold = $oracle_array["Gold"][$row->mi_seq];                                       
                    }else{
                        $prix_bronze = floatval($this->get_national_price_menu('Bronze', $row->mi_seq)) + floatval(str_replace(",",".",$value[3]));
                        $prix_silver = floatval($this->get_national_price_menu('Silver', $row->mi_seq)) + floatval(str_replace(",",".",$value[3]));
                        $prix_gold = floatval($this->get_national_price_menu('Gold', $row->mi_seq)) + floatval(str_replace(",",".",$value[3]));
                    }            
                    
                    
                    $categorieItemsArray[] = array(
                                                    "mi_seq" => $row->mi_seq,
                                                    "obj_num" => $row->obj_num,
                                                    "libelle" => $row->name_1,
                                                    "prix" => $row->price1,
                                                    "modifie" => "",
                                                    "bronze" => $prix_bronze,
                                                    "silver" => $prix_silver,
                                                    "gold" => $prix_gold
                                                ); 
                }                
            }
            return $categorieItemsArray;
        }
        
        function getItemsByRange($zoneprix,$begin_range,$end_range,$type){ // Z13-Louvradoux
            $categorieItemsArray = array();                        
            
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);
            $query =    "   
                            select distinct t1.mi_seq as a1, t1.obj_num as a2, t1.name_1 as a, t1.".$type."_preset_amt_1 as b, t1.price_tier_name as c, t2.".$type."_preset_amt_1 as d, t2.price_tier_name as e, t3.".$type."_preset_amt_1 as f, t3.price_tier_name as g, t4.".$type."_preset_amt_1 as h, t4.price_tier_name as i
                                from custom.v_kfc_easymenu as t2, custom.v_kfc_easymenu as t3, custom.v_kfc_easymenu as t4,

                                    (   select mi_seq, obj_num, name_1, ".$type."_preset_amt_1, price_tier_name from custom.v_kfc_easymenu 
                                        where
                                        ".$type."_preset_amt_1 is not NULL and
                                        (obj_num between ".$begin_range." and ".$end_range.") and
                                        (".$type."_effective_from <= current timestamp and (".$type."_effective_to is null or ".$type."_effective_to >= current timestamp)) and
                                        price_tier_name = '".$zoneprix."') as t1 

                                where

                                    t1.name_1 = t2.name_1 and
                                    t2.name_1 = t3.name_1 and
                                    t3.name_1 = t4.name_1 and
                                    
                                    t2.".$type."_preset_amt_1 is not NULL and
                                    t3.".$type."_preset_amt_1 is not NULL and
                                    t4.".$type."_preset_amt_1 is not NULL and
                                        
                                    (t2.obj_num between ".$begin_range." and ".$end_range.") and
                                    (t3.obj_num between ".$begin_range." and ".$end_range.") and
                                    (t4.obj_num between ".$begin_range." and ".$end_range.") and    
                                    
                                    (t2.".$type."_effective_from <= current timestamp and (t2.".$type."_effective_to is null or t2.".$type."_effective_to >= current timestamp)) and
                                    (t3.".$type."_effective_from <= current timestamp and (t3.".$type."_effective_to is null or t3.".$type."_effective_to >= current timestamp)) and
                                    (t4.".$type."_effective_from <= current timestamp and (t4.".$type."_effective_to is null or t4.".$type."_effective_to >= current timestamp)) and

                                    t2.price_tier_name = 'Bronze' and
                                    t3.price_tier_name = 'Silver' and
                                    t4.price_tier_name = 'Gold' 
                                
                                order by t1.name_1
                            ";                       
            
            $result = $this->sybase_odbc_query($connexion,$query);
            
            //$nbr = odbc_num_rows($result);
            //print('nbr rows = '.$nbr.'<br>');
            //print('<table border="1">');
            //print('<tr><th>Libelle</th><th>Prix</th><th></th><th>Bronze</th><th>Silver</th><th>Gold</th></tr>');
            while($row = odbc_fetch_object($result)){
                //print('<tr><td>&nbsp;'.$row->a.'</td><td>&nbsp;'.$row->b.'</td><td></td><td>&nbsp;'.$row->d.'</td><td>&nbsp;'.$row->f.'</td><td>&nbsp;'.$row->h.'</td></tr>');
                $categorieItemsArray[] = array(
                                                    "mi_seq" => $row->a1,
                                                    "obj_num" => $row->a2,
                                                    "libelle" => $row->a,
                                                    "prix" => $row->b,
                                                    "modifie" => "",
                                                    "bronze" => $row->d,
                                                    "silver" => $row->f,
                                                    "gold" => $row->h
                                                ); 
            }           
            return $categorieItemsArray;
        } 
        
        function getDiscounts(){
            $discountArray = array();                                    
            $connexion = $this->sybase_odbc_connect($this->connection_str(), Store_params::SYBASE_USER, Store_params::SYBASE_PASSWD);            
            
            $query = "
                        select obj_num, name, dsvc_seq from micros.dsvc_def 
                        where
                        (effective_from <= current timestamp and effective_to >= current timestamp) and
                        (effective_from is not null and effective_to is not null) and
                        dsvc_slu_seq is not null
                        order by name asc                       
                        
                        ";
            
            $query = "
                        select obj_num, name, dsvc_seq from micros.dsvc_def
                        where
                                (
                                    (effective_from <= current timestamp or effective_from is null) and
                                    (effective_to >= current timestamp or effective_to is null )
                                 ) 
                        and dsvc_slu_seq is not null
                        order by name asc 
                        
                            ";
            
			//print($query.'<br>');
			
            $result = $this->sybase_odbc_query($connexion,$query);   
            
            while($row = odbc_fetch_object($result)){                                                                
                $discountArray[] = array(                                                 
                                                    "obj_num" => $row->obj_num,
                                                    "name" => $row->name,   
                                                    "dsvc_seq" => $row->dsvc_seq  
                                                ); 
            }
            
            return $discountArray;
        }
        
        
        
        /*
         * 
         *  SYBASE SERVER FUNCTIONS
         * 
         */
        
                      
    }

?>
