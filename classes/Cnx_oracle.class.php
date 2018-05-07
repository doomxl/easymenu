<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

  class Cnx_oracle{
      
        var $connexion = "";
        
        /**
        const ORA_CHAINE_CONN = "ora1";
        const ORA_LOGIN = "kfcselect";
        const ORA_PASSWD = "kfc";                              
        */
        
        const ORA_CHAINE_CONN = "ora1";
        const ORA_LOGIN = "easyadmin";
        const ORA_PASSWD = "easykfc";                              
        
        function __construct(){
                    
        }
        
        /*
         * 
         *  SYBASE SERVER FUNCTIONS
         * 
         */
        
        function oracle_connect(){
            $connexion = ocilogon(Cnx_oracle::ORA_LOGIN,  Cnx_oracle::ORA_PASSWD,  Cnx_oracle::ORA_CHAINE_CONN) or die('Could not connect to the server');            
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
        
        /**
        function query($query){
            $connexion = $this->oracle_connect();            
            $statement = $this->oracle_query($connexion,$query);
          
            $itemCategorie_array = array();
                       
            while (($row = oci_fetch_object($statement))) {                
                $itemCategorie_array[] = array(
                                                "RANGE_ID" => $row->RANGE_ID, 
                                                "RANGE_NAME" => $row->RANGE_NAME, 
                                                "BEGIN" => $row->BEGIN, 
                                                "END" => $row->END, 
                                                "TYPE" => $row->TYPE
                                                );
            }
            
            $this->oracle_statement_close($statement);
            $this->oracle_close($connexion);
            
            return $itemCategorie_array; 
        }
        */
  }
    
?>