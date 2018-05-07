<?php
	//Modif par Roadster31 - singleton de connexion à la BDD

        include_once("Cnx_oracle.class.php");

        class New_libelle extends Cnx_oracle {

            var $id = '';
            var $modif_id = '';
            var $old_libelle = '';
            var $new_libelle = '';                           

            var $table = "easymenu_new_libelle";
            
            function __construct(){

            }        

            function charger($id){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where id = '".$id."'");
                
                $row = oci_fetch_object($statement);
                
                $this->id = $row->ID;
                $this->modif_id = $row->MODIF_ID;
                $this->old_libelle= $row->OLD_LIBELLE;
                $this->new_libelle= $row->NEW_LIBELLE;
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);                                            
            }
            
            function printvalues(){
                $connexion = $this->oracle_connect();
                $statement = $this->oracle_query($connexion,"select * from kfcstorea01.".$this->table." where id >= 0 ");
                //print("la liste de mes valeurs begin<br>");
                while ($row = oci_fetch_object($statement)) {
                    print($row->ID." ".$row->MODIF_ID." ".$row->OLD_LIBELLE." ".$row->NEW_LIBELLE."<br>");
                }
                //print("la liste de mes valeurs end<br>");
                /**
                $this->id = $row->ID;
                $this->modif_id = $row->MODIF_ID;
                $this->old_libelle = $row->OLD_LIBELLE;
                $this->new_libelle = $row->NEW_LIBELLE;
                */
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion);           
            }
            
            function getLibelle($mi_seq){
                $connexion = $this->oracle_connect();
                
                $query = "  select n.new_libelle as new_libelle from kfcstorea01.".$this->table." n, kfcstorea01.easymenu_libelle_modif l   
                            where 
                            n.modif_id = l.id and                            
                            to_date(l.date_modif,'dd/mm/yyyy') > to_date(CURRENT_DATE, 'dd/mm/yyyy') and                           
                            n.id = '".$mi_seq."'";
                
                //print($query.'<br>');
                
                $statement = $this->oracle_query($connexion,$query);
                
                $row = oci_fetch_object($statement);                              
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                               
                return $row->NEW_LIBELLE;
            }
            
            function add(){ // important last id no c'est le num_seq
                $connexion = $this->oracle_connect();
                //$last_id = $this->last_id();
                $query =  " insert into kfcstorea01.".$this->table."
                                (id,modif_id,old_libelle,new_libelle) 
                            values
                                ('".$this->id."','".$this->modif_id."','".$this->old_libelle."','".$this->new_libelle."')                    
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
                $statement = $this->oracle_query($connexion,"select max(id) from kfcstorea01.".$this->table);
                
                $row = oci_fetch_object($statement);                                               
                
                $this->oracle_statement_close($statement);
                $this->oracle_close($connexion); 
                
                return $this->id;
            }  
             * 
             */                
        }    
?>