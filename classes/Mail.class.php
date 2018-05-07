<?php
	//Modif par Roadster31 - singleton de connexion Ã  la BDD

        include_once("Cnx_oracle.class.php");

        class Mail extends Cnx_oracle {

            var $mail = '';
            var $sujet = '';
            var $message = '';
            //var $= '';
            //var $header = '';                                       

            //var $table = "easymenu_log";  
            
            function __construct(){
                
            } 
            
            function retour_chariot($email){
                return  (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) ? "\r\n" : "\n";
            }
            
            function envoi(){
                
                $mail = $this->mail; // Déclaration de l'adresse de destination.
                $passage_ligne = $this->retour_chariot($email); // On filtre les serveurs qui présentent des bogues.
                
                $message_txt = $this->message;                                

                //=====Création de la boundary.
                $boundary = "-----=".md5(rand()); 
                $boundary_alt = "-----=".md5(rand());
                
                $sujet = $this->sujet; 
               
                //=====Création du header de l'e-mail.
                $header = "From: <no-replay@easymenu.com>".$passage_ligne;
                $header.= "Reply-to: <no-replay@easymenu.com>".$passage_ligne;
                $header.= "MIME-Version: 1.0".$passage_ligne;
                //$header.= "Content-Type: multipart/mixed;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
                $header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
                //==========
                
                //=====Ajout du message au format texte.
                $message = $passage_ligne."--".$boundary.$passage_ligne; 
                $message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
                $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
                $message.= $passage_ligne.$message_txt.$passage_ligne;
                $message.= $passage_ligne."--".$boundary."--".$passage_ligne; 
               
                mail($mail,$sujet,$message,$header);
            }                             
        }    
?>