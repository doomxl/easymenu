<?php


        include_once("../classes/Easyadmin.class.php"); 

        session_start();

        if($_GET['action'] == "deconnect"){               
            $_SESSION = array();
            session_destroy();
            header("Location: ../index.php");
        }
        
        //session_start();
        
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
                $_SESSION = array();
                session_destroy();               
                header("Location: ../index.php"); 
                exit;
            }
        }
        
        $_SESSION['timeout']=time();                                    // maj timeout
        
        if(!isset($_SESSION['login'])) $_SESSION['login']=$user;        // maj SESSION login
        if(!isset($_SESSION['passwd'])) $_SESSION['passwd']=$passwd;    // maj SESSION passwd
        if(!isset($_SESSION['lang'])) $_SESSION['lang']=1;              // francais par defaut si pas de SESSION lang
?>
