<?php

	require_once(realpath(dirname(__FILE__)) . "/Baseobj.class.php");
	require_once(realpath(dirname(__FILE__)) . "/Autorisation.class.php");
	require_once(realpath(dirname(__FILE__)) . "/Autorisation_administrateur.class.php");

	class Administrateur extends Baseobj{

		var $id;
		var $identifiant;
		var $motdepasse;
		var $prenom;
		var $nom;
		var $profil;
		var $lang;
		var $autorisation;

		const TABLE = "administrateur";
		var $table=self::TABLE;

		var $bddvars = array("id", "identifiant", "motdepasse", "prenom", "nom", "profil", "lang");

		function __construct($id = 0){
			parent::__construct();

			if($id > 0)
 			  $this->charger_id($id);
		}

		function charger($identifiant, $motdepasse){
			$query = sprintf("select * from $this->table where identifiant='%s' and motdepasse=PASSWORD('%s')",
			mysql_real_escape_string($identifiant),
			mysql_real_escape_string($motdepasse));

			if($this->getVars($query)){
				$this->autorisation();
				return 1;

			} else {

				return 0;

			}

		}


		function charger_id($id){
			if($this->getVars("select * from $this->table where id=\"$id\"")){
				$this->autorisation();
				return 1;

			} else {
				return 0;
			}
		}

		function autorisation(){

			$autorisation_administrateur = new Autorisation_administrateur();
			$query = "select * from $autorisation_administrateur->table where administrateur=\"" . $this->id . "\"";
			$resul = mysql_query($query, $autorisation_administrateur->link);

			while($row = mysql_fetch_object($resul)){
				$autorisation = new Autorisation();
				$autorisation->charger_id($row->autorisation);
				$temp_auth = new Autorisation_administrateur();
				$temp_auth->id = $row->id;
				$temp_auth->administrateur = $row->administrateur;
				$temp_auth->autorisation = $row->autorisation;
				$temp_auth->lecture = $row->lecture;
				$temp_auth->ecriture = $row->ecriture;

				$this->autorisation[$autorisation->nom] = new Autorisation_administrateur();
                                $this->autorisation[$autorisation->nom] = $temp_auth;

			}


		}

		function crypter(){
			$query = "select PASSWORD('$this->motdepasse') as resultat";
			$resul = mysql_query($query, $this->link);
			$this->motdepasse = mysql_result($resul, 0, "resultat");

		}

	}


?>