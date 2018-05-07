<?php

    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    include("include/vtemplate.class.php"); 		// Inclusion du fichier	
    
    
    unset($_SESSION["login"]);
    unset($_SESSION["passwd"]);
    
    @session_destroy();                                 // destruction de la session. et masquer les message d'erreur en cas ou la session n'existe pas.
    
    $vtp = new VTemplate; 				// Déclaration de l'object 
    $handle = $vtp->Open("vtemplates/index.html");      // Dans le fichier test.vtp, il y a une zone "mazone" 	   
    
    $vtp->addSession($handle,"mazone"); 
    if(isset($_REQUEST['err'])){                                    
	//$vtp->setVar($handle,"mazone.err_mess","login/password incorrect try again");   // La zone 'mazone' contient une variable var 	
        $vtp->setVar($handle,"mazone.err_mess","login/mot de passe incorrect");   // La zone 'mazone' contient une variable var 	
    } 
    $vtp->closeSession($handle,"mazone");    
    $vtp->Display();

?>

