<?php
	require_once(realpath(dirname(__FILE__)) . "/Requete_sybase.class.php");
	require_once(realpath(dirname(__FILE__)) . "/CacheBase_sybase.class.php");

        include_once("Requete_sybase.class.php");
        
	// Classe Baseobj

	class Baseobj_sybase extends Requete_sybase{

		var $bddvars = array();

		function __construct(){
			parent::__construct();
		}
		
		/* Compatibilité 1.4.x */
		function Baseobj(){
			self::__construct();	
		}

		function getListVarsSql(){
			$listvars="";

			foreach($this->bddvars as $var) {
				$listvars .= '`'.$var. "`,";
			}

			return rtrim($listvars, ',');
		}


		function getListValsSql(){
			$listvals="";

			foreach($this->bddvars as $var){
				$tempvar = $this->$var;

				 if(get_magic_quotes_gpc())
			  		$tempvar = stripslashes($tempvar);

				//$tempvar = mysql_real_escape_string($tempvar, $this->link);

				$listvals .= "\"" . $tempvar . "\",";
			}

			return rtrim($listvals, ',');
		}

        function getVars($query){
         	$row=CacheBase_sybase::getCache()->get($query);
            if ($row==FALSE)
            {
            	if(! $resul = $this->query($query))
                {
                 	CacheBase_sybase::getCache()->set($query,"-");
                	return 0;
                }
                 $row = mysql_fetch_object($resul);
                 if($row=="")
                   $row="-";
                   CacheBase_sybase::getCache()->set($query,$row);
            }

            if($row && $row!="-")
            {
                foreach($this->bddvars as $var)
                {
                    $this->$var = $row->$var;
                }

                return 1;
            }
            else
            {
                return 0;
            }

            // return mysql_num_rows($resul);
        }

		function serialise_js(){

			$this->link="";

 			$json = new Services_JSON();
			return $json->encode($this);
		}

	}
?>