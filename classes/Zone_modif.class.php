<?php
	//Modif par Roadster31 - singleton de connexion à la BDD
        include_once("Price_modif.class.php");
        include_once("Cnx_oracle.class.php");

        class Zone_modif extends Cnx_oracle {

            var $id = '';
            var $modif_id = '';
            var $carte_menu = '';
            var $price = '';                           

            var $table = "easymenu_zone_modif";
            
            function __construct(){

            }        

            function charger($id){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where id = '".$id."'");                
                $row = oci_fetch_object($statement);                
                $this->id = $row->ID;
                $this->modif_id = $row->MODIF_ID;
                $this->carte_menu= $row->CARTE_MENU;
                $this->price= $row->PRICE;                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }
            
            function charger_modif($id,$modif_id){
                $connexion = $this->oracle_connect();
                //print("select * from kfcstorea01.".$this->table." where id = '".$id."' and modif_id = '".$modif_id."'<br/>"); //exit();
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where id = '".$id."' and modif_id = '".$modif_id."'");                
                $row = oci_fetch_object($statement);                
                $this->id = $row->ID;
                $this->modif_id = $row->MODIF_ID;
                $this->carte_menu= $row->CARTE_MENU;
                $this->price= $row->PRICE;                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }
            
            function get_price_modif($id,$zone_id){
                $price_modif = new Price_modif();                
                $price_modif->charger_zone_recent($zone_id);
                //print("print id = ".$price_modif->id); exit();
                $this->charger_modif($id,$price_modif->id);                                
                return $this->price;
            }
            
            function print_table_content(){
                $connexion = $this->oracle_connect();                
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table);    
                print("ZONE MODIF<br/><br/>");
                while($row = oci_fetch_object($statement)){
                    
                    print("ID &nbsp;&nbsp; MODIF_ID &nbsp;&nbsp; CARTE_MENU &nbsp;&nbsp; PRICE<br/></br>");
                    print($row->ID." ".$row->MODIF_ID." ".$row->CARTE_MENU." ".$row->PRICE."<br/>");                             
                }
                print("<br/>");
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);   
            }
            
            function add(){
                $connexion = $this->oracle_connect();
                //$last_id = $this->last_id();
                //print('<br>last_id : '.$last_id.'<br>');
                $query =  " insert into kfcstorea01.".$this->table."
                                (id,modif_id,carte_menu,price) 
                            values
                                (".$this->id.",".$this->modif_id.",'".$this->carte_menu."',".$this->price.")                    
                            ";
                                
                //print("$query<br/>");
                
                $statement = $this->oracle_query($connexion,$query);
                //oci_execute($statement);
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return ($last_id+1);
            }

            function delete($modif_id){
                $connexion = $this->oracle_connect();
                $query =  " delete from kfcstorea01.".$this->table."                                
                            where
                                modif_id = '".$modif_id."'                                
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