<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

        include_once("Cnx_oracle.class.php");

        class Easyadmin extends Cnx_oracle {

            var $prct_modif = '';
            var $const_boxy = '';
            var $const_zebag = '';
            var $time_out = '';                           
            var $nb_zone_prix_national = '';  
            var $em_server = '';      

            var $table = "easymenu_admin";
            
            function __construct(){

            }        

            function getRow($row){
                switch($row->LIBELLE){
                    case "PRCT_MODIF" : $this->prct_modif = $row->VALUE; break;
                    case "CONST_BOXY" : $this->const_boxy = $row->VALUE; break;
                    case "CONST_ZEBAG" : $this->const_zebag = $row->VALUE; break;
                    case "NB_ZONE_PRIX_NATIONAL" : $this->nb_zone_prix_national = $row->VALUE; break;
                    case "TIME_OUT" : $this->time_out = $row->VALUE; break;
                    case "PAREM0" : $this->em_server = $row->VALUE; break;
                }
            }
            
            function charger(){
                $connexion = $this->oracle_connect();
                $query = "select * from kfcstorea01.".$this->table;                
                $statement = $this->oracle_query($connexion,$query);
                
                while($row = oci_fetch_object($statement)){
                    $this->getRow($row);
                }                              
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }                                 
            
            function maj($prct_modif,$const_boxy,$const_zebag,$time_out,$nb_zone_prix_national,$em_server){
                $connexion = $this->oracle_connect();
                
                $statement = $this->oracle_query($connexion,"update kfcstorea01.".$this->table." set value = ".$prct_modif." where libelle = 'PRCT_MODIF'");
                oci_execute($statement);
                                
                $statement = $this->oracle_query($connexion,"update kfcstorea01.".$this->table." set value = ".$const_boxy." where libelle = 'CONST_BOXY'");
                oci_execute($statement);
                                
                $statement = $this->oracle_query($connexion,"update kfcstorea01.".$this->table." set value = ".$const_zebag." where libelle = 'CONST_ZEBAG'");
                oci_execute($statement);
                                
                $statement = $this->oracle_query($connexion,"update kfcstorea01.".$this->table." set value = ".$time_out." where libelle = 'TIME_OUT'");
                oci_execute($statement);
                                
                $statement = $this->oracle_query($connexion,"update kfcstorea01.".$this->table." set value = ".$nb_zone_prix_national." where libelle = 'NB_ZONE_PRIX_NATIONAL'");
                oci_execute($statement);                
                
                $statement = $this->oracle_query($connexion,"update kfcstorea01.".$this->table." set value = ".$em_server." where libelle = 'PAREM0'");
                oci_execute($statement);                
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
            }                                
        }    
?>