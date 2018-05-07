<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

        include_once("Cnx_oracle.class.php");

        class Tracker extends Cnx_oracle {

            var $user = '';
            var $date = '';
            var $time = '';
            var $action = '';                                       

            var $table = "easymenu_log";  
            
            function __construct(){

            }        
            
            function add(){
                $connexion = $this->oracle_connect();                                                                                     
                $query =  " insert into kfcstorea01.".$this->table."                               
                            values
                                ('".$this->user."',to_date('".$this->date."','dd/mm/yyyy'),to_date('".$this->time."','dd/mm/yyyy hh24:mi:ss'),'".$this->action."')                    
                            ";  
                
                
                $statement = $this->oracle_query($connexion,$query);
                //oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
            }                      
        }    
?>