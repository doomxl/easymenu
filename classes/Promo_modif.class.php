<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

        include_once("Cnx_oracle.class.php");

        class Promo_modif extends Cnx_oracle {

            var $id = '';
            var $date_modif = '';                                    

            var $table = "easymenu_promo_modif";
            
            function __construct(){

            }        

            function charger($id){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select * from kfctorea01.".$this->table." id = '".$id."'");
                
                $row = oci_fetch_object($statement);
                
                $this->id = $row->ID;
                $this->date_modif = $row->DATE_MODIF;               
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }
           
            function add(){
                $connexion = $this->oracle_connect();
                $last_id = $this->last_id();
                //print('<br>last_id : '.$last_id.'<br>');
                $query =  " insert into kfcstorea01.".$this->table."
                                (id,date_modif) 
                            values
                                ('".($last_id+1)."',to_date('".$this->date_modif."','dd/mm/yyyy'))                    
                            ";
                
                //print($query);
                //print("<br/>");
                
                $statement = $this->oracle_query($connexion,$query);
                //oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return ($last_id+1);
            }

            function delete(){
                $connexion = $this->oracle_connect();
                $query =  " delete from kfcstorea01.".$this->table."                                
                            where
                                id = '".$this->id."'                                
                            ";
                
                $statement = $this->oracle_query($connexion,$query);
                oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
            }

            function deleteAll(){
                $connexion = $this->oracle_connect();
                $query =  " delete from kfcstorea01.".$this->table."                                
                                                         
                            ";
                
                $statement = $this->oracle_query($connexion,$query);
                oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
            }
            
            function last_id(){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select max(id) as last_id from kfcstorea01.".$this->table);                                
                $row = oci_fetch_object($statement);                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return $row->LAST_ID;
            }  
            
              function print_table_content(){
                $connexion = $this->oracle_connect();                
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table);  
                print("PROMO MODIF<br/><br/>");
                print("ID &nbsp;&nbsp; DATE_MODIF<br/></br>");
                while($row = oci_fetch_object($statement)){                                          
                    print($row->ID." ".$row->DATE_MODIF."<br/>");                             
                }
                print("<br/>");
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);   
            }
        }    
?>