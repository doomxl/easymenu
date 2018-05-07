<?php
	//Modif par Roadster31 - singleton de connexion à la BDD





    class StaticConnection
    {
        public static $db_handle = -1;

        public static function getHandle()
        {
            if (self::$db_handle == -1)
            {
                //self::$db_handle = @mysql_connect(Cnx::$host, Cnx::$login_mysql, Cnx::$password_mysql) or die('Le serveur MySQL n\'est pas accessible.');
		//		mysql_query("SET NAMES UTF8", self::$db_handle);
		
                self::$db_handle = @sybase_connect(Cnx_sybase::$host, Cnx_sybase::$login_mysql, Cnx_sybase::$password_mysql) or die('Le serveur Sybase n\'est pas accessible.');
		//		mysql_query("SET NAMES UTF8", self::$db_handle);
                                
                if(! self::$db_handle && $_REQUEST['erreur'] != 1)
                {
					header('HTTP/1.1 503 Service Temporarily Unavailable');
					header('Status: 503 Service Temporarily Unavailable');
					echo "Connexion a�la base de donnees impossible"; exit;
		        }

                sybase_select_db(Cnx_sybase::$db, self::$db_handle);
            }

            return self::$db_handle;
        }
    }

    // Classe Cnx
	// host --> votre serveur mysql
    // login_mysql --> login de connexion
    // password_mysql --> mot de passe de connexion
    // db --> nom de la base de donnée
    class Cnx_sybase{
        /**
	public static $host= "localhost";
        public static $login_mysql= "root";
        public static $password_mysql= "";
        public static $db = "thelia";
        */
        
        public static $db_handle = -1;
        
        public static $host= "parem01";
        public static $login_mysql= "custom";
        public static $password_mysql= "custom";
        public static $db = "micros";
        
        var $table = "";
        var $link="";

        function __construct() {

            $this->link = StaticConnection::getHandle();

			self::$host = '';
			self::$login_mysql = '';
			self::$password_mysql = '';
			self::$db = '';
        }

        public function query($query) {
                $resul = sybase_query($query, $this->link);

                // A décommenter pour debug
                /*
                if ($resul === false) {
                        die("Erreur: ".mysql_error().": requête: $query");
                }
                */

                return $resul;
        }
    }
?>