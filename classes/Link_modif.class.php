<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

        include_once("Cnx_oracle.class.php");

        class Link_modif extends Cnx_oracle {

            var $id = '';
            var $promo_id = '';
            var $store_id = '';
            var $link_type = '';                           

            var $table = "easymenu_link_modif";
            
            function __construct(){

            }        

            function charger($id){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select * from kfctorea01.".$this->table." where id = '".$id."'");
                
                $row = oci_fetch_object($statement);
                
                $this->id = $row->id;
                $this->promo_id = $row->promo_id;
                $this->store_id= $row->store_id;
                $this->link_type= $row->link_type;
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }
           
            function add(){
                $connexion = $this->oracle_connect();
                //$last_id = $this->last_id();
                $query =  " insert into kfcstorea01.".$this->table."
                                (id,promo_id,store_id,link_type) 
                            values
                                ('".$this->id."','".$this->promo_id."','".$this->store_id."','".$this->link_type."')                    
                            ";
                
                //print($query);
                //print("<br/>");
                
                $statement = $this->oracle_query($connexion,$query);
                //oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                //return ($last_id+1);
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
                $statement = $this->oracle_query($connexion,"select max(id) from kfcstorea01.".$this->table);
                
                $row = oci_fetch_object($statement);                                               
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return $this->id;
            }  
            
             function print_table_content(){
                $connexion = $this->oracle_connect();                
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table);    
                print("LINK MODIF<br/><br/>");
                print("ID &nbsp;&nbsp; PROMO_ID &nbsp;&nbsp; STORE_ID &nbsp;&nbsp; LINK_TYPE<br/></br>");
                while($row = oci_fetch_object($statement)){                                       
                    print($row->ID." ".$row->PROMO_ID." ".$row->STORE_ID." ".$row->LINK_TYPE."<br/>");                             
                }
                print("<br/>");
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);   
            }
        }    
?>