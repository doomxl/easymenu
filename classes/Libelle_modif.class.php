<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

        include_once("Cnx_oracle.class.php");

        class Libelle_modif extends Cnx_oracle {

            var $id = '';
            var $date_modif = '';                                    

            var $table = "easymenu_libelle_modif";
            
            function __construct(){

            }        

            function charger(){
                $connexion = $this->oracle_connect();
                //print('bp1');
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where id in (select max(id) from kfcstorea01.".$this->table.")");
                //print('bp2');
                
                //print("select * from kfcstorea01.".$this->table." where id in (select max(id) from kfcstorea01.".$this->table.")<br>");
                
                $row = oci_fetch_object($statement);
                
                $this->id = $row->ID;
                $this->date_modif = $row->DATE_MODIF;               
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }
           
            function get_date_modif(){
                $connexion = $this->oracle_connect();  
                $query = "  select to_date(DATE_MODIF,'dd/mm/yy') as DATE_MODIF from kfcstorea01.".$this->table." 
                            where 
                            id in (select max(id) from kfcstorea01.".$this->table.") and
                            to_date(DATE_MODIF,'dd/mm/yyyy') > to_date(CURRENT_DATE, 'dd/mm/yyyy')
                            ";
                
                //print($query.'<br>');
                
                $statement = $this->oracle_query($connexion,$query);               
                
                $row = oci_fetch_object($statement);                                            
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
                
                
                $date = split("/", $row->DATE_MODIF);
                
                //print("date_modifs");
                //print_r($row->DATE_MODIF);
                
                $result = ($row->DATE_MODIF) ? date("d/m/Y", mktime(0, 0, 0, $date[1], $date[0], $date[2])) : "";
                
                return $result;               
                                         
            }
            
            function add(){
                $connexion = $this->oracle_connect();
                //$last_id = $this->last_id();
                //print('<br>last_id : '.$last_id.'<br>');
                $query =  " insert into kfcstorea01.".$this->table."
                                (id,date_modif) 
                            values
                                (1,to_date('".$this->date_modif."','dd/mm/yyyy'))                    
                            ";
                
                //print($query);
                //print("<br/>");
                
                $statement = $this->oracle_query($connexion,$query);
                //oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return (1);
            }                        
            
            function delete(){
                $connexion = $this->oracle_connect();
                $query =  " delete from kfcstorea01.".$this->table."                                
                            where
                                id = 1                                
                            ";
                
                $statement = $this->oracle_query($connexion,$query);
                oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
            }
            
            function deleteAll(){
                $connexion = $this->oracle_connect();
                $query =  " delete from kfcstorea01.".$this->table."                                
                            where
                                id >= 0
                            ";
                
                //print("$query<br/>");
                
                $statement = $this->oracle_query($connexion,$query);
                oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
            }
            /**
            function last_id(){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select max(id) as last_id from kfcstorea01.".$this->table);                                
                $row = oci_fetch_object($statement);                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return $row->LAST_ID;
            }  
             * 
             */                
        }    
?>