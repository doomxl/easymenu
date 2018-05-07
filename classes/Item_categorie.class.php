<?php
    /*
     * Connexion à l'active directory et procedures de filtrage de profils
     */

    class Item_categorie {
             
        var $connexion = "";
                        
        const ORA_CHAINE_CONN = "ora1";
        const ORA_LOGIN = "kfcselect";
        const ORA_PASSWD = "kfc";                              
        
        function __construct(){
                    
        }
        
        /*
         * 
         *  SYBASE SERVER FUNCTIONS
         * 
         */
        
        function oracle_connect(){
            $connexion = ocilogon(Item_categorie::ORA_LOGIN,  Item_categorie::ORA_PASSWD,  Item_categorie::ORA_CHAINE_CONN) or die('Could not connect to the server');            
            return $connexion;
        }
        
        function oracle_query($connexion,$query){
            //$connexion = $this->oracle_connect();
            $statement = ociparse($connexion,$query) or die('Could not parse and create the statement the query');
            if(!ociexecute($statement)) die('Could not execute the query');
            return $statement;
        }
        
        function oracle_statement_close($statement){
            oci_free_statement($statement);
        }
        
        function oracle_close($connexion){
            ocilogoff($connexion);
        }        
        
        function getItemCategorieEntries($type){
            $connexion = $this->oracle_connect();
            $query = "SELECT * FROM kfcstorea01.EASYMENU_ITEM_STRUCTURE WHERE DISPLAY_TYPE = '".$type."'";            
            $statement = $this->oracle_query($connexion,$query);
          
            $itemCategorie_array = array();
                       
            while (($row = oci_fetch_object($statement))) {                
                $itemCategorie_array[] = array(
                                                "RANGE_ID" => $row->RANGE_ID, 
                                                "RANGE_NAME" => $row->RANGE_NAME, 
                                                "BEGIN" => $row->BEGIN, 
                                                "END" => $row->END, 
                                                "DISPLAY_TYPE" => $row->DISPLAY_TYPE,
                                                "WEB_NAME" => $row->WEB_NAME,
                                                "DISPLAY" => $row->DISPLAY,
                                                "DISPLAY_ORDER" => $row->DISPAY_ORDER,
                                                "SET_UP_TYPE" => $row->SET_UP_TYPE
                                                );
            }
            
            $this->oracle_statement_close($statement);
            $this->oracle_close($connexion);
            
            return $itemCategorie_array; 
        }  
        
        function getItemCategorieEntries2($type){
            $connexion = $this->oracle_connect();
            
            $query = "
                        SELECT * FROM kfcstorea01.EASYMENU_ITEM_STRUCTURE i left outer join kfcstorea01.EASYMENU_ADMIN a on i.const_num = a.id  
                        WHERE
                        DISPLAY_ORDER IS NOT NULL AND
                        DISPLAY = 'Y' AND                        
                        DISPLAY_TYPE = '".$type."'
                        ORDER BY DISPLAY_ORDER, WEB_NAME, BEGIN";            
            
            //print($query);
            
            $statement = $this->oracle_query($connexion,$query);
          
            $itemCategorie_array = array();
                       
            while (($row = oci_fetch_object($statement))) {                
                $itemCategorie_array[] = array(
                                                "WEB_NAME" => $row->WEB_NAME,
                                                "BEGIN" => $row->BEGIN, 
                                                "END" => $row->END,
                                                "SET_UP_TYPE" => $row->SET_UP_TYPE,
                                                "VALUE" => $row->VALUE
                                                );
            }
            
            $this->oracle_statement_close($statement);
            $this->oracle_close($connexion);
            
            return $itemCategorie_array; 
        }
        
        function get_menu_enfants_const($obj_num){
            $connexion = $this->oracle_connect();            
            $query = "
                        select BEGIN,end ,nvl(Value,0) as VALUE from kfcstorea01.EASYMENU_ITEM_STRUCTURE i left outer join kfcstorea01.EASYMENU_ADMIN a on i.CONST_NUM=a.ID 
                        where (BEGIN <= $obj_num and END >= $obj_num )
                        order by 1   ";                                    
            
            //print($query.'<br>');
            
            $statement = $this->oracle_query($connexion,$query);                                             
            $row = oci_fetch_object($statement);              
            
            //print("brut value from oracle : ".$row->VALUE);
            
            $this->oracle_statement_close($statement);
            $this->oracle_close($connexion);
            
            return $row->VALUE; 
        }
        
    }

?>
