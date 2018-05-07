<?php
	include("../include/vtemplate.class.php"); 		// Inclusion du fichier
	//include("../include/connect_mysql.inc.php");          // Parametres de connexion
	//include("../include/dataBase_mysql.inc.php");         // Parametres de connexion
        
        include_once("../classes/Tracker.class.php");           
        include_once("../classes/Store_params.class.php");
        include_once("../classes/Active_directory.class.php");  
        include_once("../classes/Easyadmin.class.php"); 
        include_once("../include/divers.php"); 
        
        //print(" Je suis dans easymenu dev");
        
        /*
         *  isset des requests et start session 
         */                             
        
        if(isset($_SESSION['cIdsTransfer'])) unset($_SESSION['cIdsTransfer']); 
        
        //echo date("d/m/Y");
        
        //print_pre($_SESSION);
        session_start();
        
        if($_GET['action'] == "deconnect"){  
            
            $tracker = new Tracker();
            $tracker->user = $_SESSION['login'];
            $tracker->date = date("d/m/Y");
            $tracker->time = date("d/m/Y H:i:s");
            $tracker->action = "deconnexion";
            $tracker->add();
            
            $_SESSION = array();
            
            session_destroy();
            header("Location: ../index.php");
        }             
              
        
        /*
         * Si il y pas de variable REQUEST login ET mot de passe
         * et si il n'y pas de variable SESSION login ET mot de passe
         * alors retour a la page d'autentification
         */
        
        $user=$_REQUEST['login'];
        $passwd=$_REQUEST['passwd'];
        
        if(!isset ($_REQUEST['login']) && !isset ($_REQUEST['passwd'])){
            if(!isset ($_SESSION['login']) && !isset ($_SESSION['passwd'])){  
                header("Location: ../index.php");
            }else{
                //session_destroy();
                //session_start();
                $user=$_SESSION['login'];       // init les variables user et passwd si les variables SESSION login passwd existent
                $passwd=$_SESSION['passwd'];                                                                                              
            }
        }
                       
        /* definition de la duree du timeout exemple : 900 secondes = 15min * 60sec */
        $easyadmin = new Easyadmin();
        $easyadmin->charger();
        
        $nombre_minutes = $easyadmin->time_out;
        if(isset($_SESSION['timeout'])){
            if((time()-$_SESSION['timeout'])>($nombre_minutes*60)){
                session_destroy();               
                header("Location: ../index.php");
                exit;
            }
        }
        
        $_SESSION['timeout']=time();                                    // maj timeout
        
        if(!isset($_SESSION['login'])) $_SESSION['login']=$user;        // maj SESSION login
        if(!isset($_SESSION['passwd'])) $_SESSION['passwd']=$passwd;    // maj SESSION passwd
        if(!isset($_SESSION['lang'])) $_SESSION['lang']=1;              // francais par defaut si pas de SESSION lang
        
        $active_directory = new Active_directory();         
        $user_params = $active_directory->getUserGroups($user,$passwd);               
        
        $_SESSION['level'] = $active_directory->getUserLevel($user, $passwd);
        
        //print("bp1 session_level ".$_SESSION['level']."<br/>");
        
        $store_params = new Store_params(); 
        
        $email_acces = $active_directory->getEmail($user, $passwd);
        
        if(!isset($_SESSION['email'])) $_SESSION['email']=$email_acces;
        
        //print_pre($active_directory->getUserParams($user, $passwd));
        
        //print("bp1 '".$email_acces."'<br/>");
        
        //print("bp3");
        
        $priceZoneArray = $store_params->getPriceZoneArray($email_acces);
        
		//print_pre($priceZoneArray);
		
		
        $priceZoneNames = array();
        
        //print("bp2");
        
        foreach ($priceZoneArray as $key => $value) {
            $priceZoneNames[] = $value['name'];
        }      
        
		//print_pre($priceZoneNames);
		
		$have_only_equity = true;
		
		foreach ($priceZoneArray as $key => $value) {
            if(($value['name'] != 'Gold') && ($value['name'] != 'Bronze') && ($value['name'] != 'Silver')) $have_only_equity = false; 
        }
		
        $franchise_zone_nat = (($have_only_equity) && ($_SESSION['level'] == 20) && (in_array('Gold',$priceZoneNames) || in_array('Bronze',$priceZoneNames) || in_array('Silver',$priceZoneNames)))?true:false; 
      
        //print("bp4 franchise_zone_nat = ".$franchise_zone_nat."<br/>");
        
        $prenom_nom = $active_directory->getFullName($user, $passwd);
        
        
        
        $menu_array = $active_directory->getMenuArray($user, $passwd, $franchise_zone_nat);      
        
       
        
        if(isset ($_REQUEST['login']) && isset ($_REQUEST['passwd'])){
                $tracker = new Tracker();
                $tracker->user = $user;
                $tracker->date = date("d/m/Y");
                $tracker->time = date("d/m/Y H:i:s");
                $tracker->action = "connexion";
                $tracker->add();
        }
        
        
	$vtp = new VTemplate; 					// Déclaration de l'object 
	$handle = $vtp->Open("../vtemplates/accueil.html"); 	// Dans le fichier test.vtp, il y a une zone "mazone"
	
	$vtp->addSession($handle,"mainZone");        
	$vtp->setVar($handle,"mainZone.prenom_nom",$prenom_nom);
	//$vtp->setVar($handle,"mainZone.url_deconnexion",$_SERVER["PHP_SELF"]."?action=deconnect");
        
        for($i=0;$i<count($menu_array);$i++){
            $vtp->addSession($handle,"menuZone");
            $vtp->setVar($handle,"menuZone.menu",$menu_array[$i][0]);
            $vtp->setVar($handle,"menuZone.menu_href",$menu_array[$i][1]);
            $vtp->closeSession($handle,"menuZone"); 	
        }
        
	$vtp->setVar($handle,"mainZone.pied","pied"); 
	$vtp->closeSession($handle,"mainZone"); 	
	$vtp->Display();
?>

