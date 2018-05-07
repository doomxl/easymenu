<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier	               
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Store_params.class.php");        
        include_once("../classes/Easyadmin.class.php");
        
        include_once("auth.php");
        
        $active_directory = new Active_directory();   
        $prenom_nom = $active_directory->getFullName($user, $passwd);
              
        $easyadmin = new Easyadmin();
        
        if(isset($_POST['action']) && ($_POST['action'] == 'maj_admin')){
            $easyadmin->maj($_POST['prct_modif'], $_POST['const_boxy'], $_POST['const_zebag'], $_POST['time_out'], $_POST['nb_zone_prix_national'], $_POST['server_em']);
        }
                       
        $easyadmin->charger();
                      
	$vtp = new VTemplate;                                   // Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/easyadmin.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"	
	$vtp->addSession($handle,"mainZone");              
      
        if(isset($_POST['action']) && ($_POST['action'] == 'maj_admin'))
            $vtp->setVar($handle,"mainZone.save_mess","Les modifications on été enregistrées");
                
        $vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);
        $vtp->setVar($handle,"mainZone.pourcentage",$easyadmin->prct_modif);
        $vtp->setVar($handle,"mainZone.timeout",$easyadmin->time_out);     
        $vtp->setVar($handle,"mainZone.const_zbag",number_format(str_replace(",",".",$easyadmin->const_zebag),2));        
	$vtp->setVar($handle,"mainZone.const_boxy",number_format(str_replace(",",".",$easyadmin->const_boxy),2));          
        $vtp->setVar($handle,"mainZone.nbr_prix_nat",$easyadmin->nb_zone_prix_national);  
        $vtp->setVar($handle,"mainZone.em_server",$easyadmin->em_server);   
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

