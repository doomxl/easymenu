<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

        include_once("Cnx_oracle.class.php");

        class Price_modif extends Cnx_oracle {

            var $id = '';
            var $zone_id = '';                                    
            var $date_modif = '';                                    

            var $table = "easymenu_price_modif";
            
            function __construct(){

            }        

            function charger($id){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where id = '".$id."'");                
                $row = oci_fetch_object($statement);                
                $this->id = $row->ID;
                $this->zone_id = $row->ZONE_ID;
                $this->date_modif = $row->DATE_MODIF;                               
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }
            
            function charger_zone($zone_id){
                $connexion = $this->oracle_connect();
                //print("bp11");
                //print("select * from kfctorea01.".$this->table." zone_id = '".$zone_id."'"."<br>");
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where zone_id = '".$zone_id."'");  
                //print("select * from kfctorea01.".$this->table." zone_id = '".$zone_id."'"."<br>");
                $row = oci_fetch_object($statement);                
                $this->id = $row->ID;
                $this->zone_id = $row->ZONE_ID;
                $this->date_modif = $row->DATE_MODIF;                               
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }                     
            
            function charger_zone_recent($zone_id){
                $connexion = $this->oracle_connect();
                //print("bp11");
                
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where zone_id = '".$zone_id."' and ((to_date(date_modif,'dd/mm/yyyy') > to_date(CURRENT_DATE, 'dd/mm/yyyy')) or (date_modif is null)) ");  
                //print("select * from kfctorea01.".$this->table." zone_id = '".$zone_id."'"."<br>");
                $row = oci_fetch_object($statement);                
                $this->id = $row->ID;
                $this->zone_id = $row->ZONE_ID;
                $this->date_modif = $row->DATE_MODIF;                               
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }      
            
            function print_table_content(){
                $connexion = $this->oracle_connect();                
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table);  
                print("PRICE MODIF<br/><br/>");
                while($row = oci_fetch_object($statement)){  
                    
                    print("ID &nbsp;&nbsp; ZONE_ID &nbsp;&nbsp; DATE_MODIF<br/></br>");
                    print($row->ID." ".$row->ZONE_ID." ".$row->DATE_MODIF."<br/>");                             
                }
                print("<br/>");
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);   
            }
            
            function add(){
                $connexion = $this->oracle_connect();
                $last_id = $this->last_id();
                //print('<br>last_id : '.$last_id.'<br>');
                $query =  " insert into kfcstorea01.".$this->table."
                                (id,zone_id,date_modif) 
                            values
                                (".($last_id+1).",".$this->zone_id.",to_date('".$this->date_modif."','dd/mm/yyyy'))                    
                            ";
                                
                //print("$query<br/>");
                
                $statement = $this->oracle_query($connexion,$query);
                //oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return ($last_id+1);
            }

            function maj(){
                $connexion = $this->oracle_connect();
                $query =  " update kfcstorea01.".$this->table."                                
                            set
                                id = '".$this->id."',
                                zone_id = '".$this->zone_id."',
                                date_modif = '".$this->date_modif."'
                            where    
                                id = '".$this->id."'
                            ";
                
                //print("$query<br/>");
                
                
                $statement = $this->oracle_query($connexion,$query);
                oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
            }
            
            function have_row($zone_id){
                $connexion = $this->oracle_connect();
                $query =  " select count(*) as nbr  from kfcstorea01.".$this->table."                                
                            where
                                zone_id = '".$zone_id."'                                
                            ";
                //print_pre($query);
                
                $statement = $this->oracle_query($connexion,$query);
                
                $row = oci_fetch_object($statement);
                
                //print_pre("nombre de ligns : ".$row->NBR);
                
                //oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);
                
                $result = ($row->NBR > 0) ? true : false;
                return $result;
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

            function last_id(){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select max(id) as last_id from kfcstorea01.".$this->table);                                
                $row = oci_fetch_object($statement);                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return $row->LAST_ID;
            }                  
        }    
?>