<?php
   /**
	 * \dir classes
     * \file Active_directory.class.php
	 * \class Active_directory
     * \brief Classe contenant les méthodes et fonctions utilisées pour accéder au données de l'Active Directory.
     * \author Fahri. DAHMANI
     * 
     * \date 4 novembre 2011
     *
     */

    class Active_directory {        		               	

		/**		
		 * \param ACTIVE_DIRECTORY_ROOT \a const Cette constante contient la racine de l'AD
		 * \param LDAP_SERVER \a const Nom du serveur LDAP.
		 * \param DN \a const Distinguish Name l'entrée sous forme de chemin d'accès.
		 * \param SAMACCOUNTNAME \a const . contient la valeur "samaccountname="
		 */
		 
        const ACTIVE_DIRECTORY_ROOT = "tri-intl\\";						
        const LDAP_SERVER = "pardc01";
        const DN = "ou=UsersAndGroups,ou=fr,dc=int,dc=tgr,dc=net"; 
        const SAMACCOUNTNAME = "samaccountname=";

		/**
		 * \fn __construct()
		 * \brief Constructeur de la classe Active_directory
		 */
		 
        function __construct(){
                    
        }
        
		/**
		 * \fn getUserParams($user, $passwd)
		 * \brief Renvoi les paramètres de l'Active Directory d'un utilisateur.  
		 * \details Cette fonction prends en paramètre le login et le mot de passe d'un utilisateur et renvoi
		 * les paramètres de l'Active Directory.
		 * 	 
		 * \param $user \a Login de l'utilisateur.
		 * \param $passwd \a Mot de passe de l'utilisateur. 	 
		 *
		 *
		 * \return \a array Table contenant les paramètres de l'Active Directory.
		 */
		
        function getUserParams($user, $passwd){
            // definition du full_user avec l'ajout de la racine de l'active directory
            $full_user = Active_directory::ACTIVE_DIRECTORY_ROOT.$user;
            
            // connection au serveur LDAP 
            $ldapconn = ldap_connect(Active_directory::LDAP_SERVER) or die("Impossible de se connecter au serveur LDAP");
            
            // authentification au serveur LDAP, si echec redrection à la page index.php
            if(!($bindServerLDAP = ldap_bind($ldapconn,$full_user,$passwd))){
                session_destroy();
                die(header('Location: ../index.php?err=1'));
            }
        
            $login = $user;
            $query = Active_directory::SAMACCOUNTNAME.$login;
            //print("<br>$login<br>");
            $result = ldap_search($ldapconn, Active_directory::DN, $query);
            
            // récupération de tous les entrées LDAP de l'utilisateur
            $table = ldap_get_entries($ldapconn, $result);
			
            return $table;
        }
        
		/**
		 * \fn getUserGroups($user, $passwd)
		 * \brief Renvoi les groupes à la quelle aparient l'utilisateur.
		 * \details Cette fonction prends en paramètre le login et le mot de passe d'un utilisateur et renvoi
		 * les groupes à la quelle aparient l'utilisateur dans l'Active Directory.
		 * 	 
		 * \param $user \a Login de l'utilisateur.
		 * \param $passwd \a Mot de passe de l'utilisateur. 	 
		 *
		 *
		 * \return \a array Table des groupes de l'Active Directory à la quelle apartient l'utilisateur.
		 */
        
        function getUserGroups($user, $passwd){            
            $memberof_array = array();
            
            //récupération de tous les entrées LDAP de l'utilisateur
            $table = $this->getUserParams($user, $passwd);
            $count =  $table[0]['memberof']['count'];
            
            // creation d'un tableau de memberof
            for ($i=0; $i<$count; $i++) {
                // récupération du nom du groupe pour chaque entrée
                $group_str = preg_split('/,/', $table[0]['memberof'][$i]);
                $memberof_array[] = substr($group_str[0],3);
            }        
            
            //print_r($memberof_array);
            
            return $memberof_array; 
        }
        
		/**
		 * \fn getFullName($user, $passwd)
		 * \brief Renvoi le prénom et nom de l'utilisateur.
		 * \details Cette fonction prends en paramètre le login et le mot de passe d'un utilisateur et renvoi
		 * le prénom et nom de l'utilisateur.
		 * 	 
		 * \param $user \a Login de l'utilisateur.
		 * \param $passwd \a Mot de passe de l'utilisateur. 	 
		 *
		 *
		 * \return \a string Prénom et nom de l'utilisateur.
		 */
		
        function getFullName($user, $passwd){
            $table = $this->getUserParams($user, $passwd);
            $prenom =  $table[0]['givenname'][0];
            $nom =  $table[0]['sn'][0];
            $nomComplet = ucfirst($prenom)." ".strtoupper($nom);
            return $nomComplet; 
        }
        
		/**
		 * \fn getEmail($user, $passwd)
		 * \brief Renvoi l'email de l'utilisateur.
		 * \details Cette fonction prends en paramètre le login et le mot de passe d'un utilisateur et renvoi
		 * l'email de l'utilisateur.
		 * 	 
		 * \param $user \a Login de l'utilisateur.
		 * \param $passwd \a Mot de passe de l'utilisateur. 	 
		 *
		 *
		 * \return \a string L'email de l'utilisateur.
		 */
		
        function getEmail($user, $passwd){            
            $table = $this->getUserParams($user, $passwd);
            $email =  $table[0]['mail'][0];           
            return strtolower(trim($email)); 
        }
        
		/**
		 * \fn getUserLevel($user, $passwd)
		 * \brief Renvoi le niveau d'accès de l'utilisateur.
		 * \details Cette fonction prends en paramètre le login et le mot de passe d'un utilisateur et renvoi
		 * le niveau d'accès de l'utilisateur.
		 * 	 
		 * \param $user \a Login de l'utilisateur.
		 * \param $passwd \a Mot de passe de l'utilisateur. 	 
		 *
		 *
		 * \return \a string Le niveau d'accès de l'utilisateur.
		 */
		
        function getUserLevel($user, $passwd){ 
            $result = 0;            
            // récupération du tableau des groupes memberof
            $groups = $this->getUserGroups($user, $passwd);
            
            // trouver le niveau d'accès
            if(in_array("FR - KFC EasyMenu Admin", $groups)){
                $result = 40; // l'informatique (administrateurs)
            }else{
                if(in_array("FR - KFC EasyMenu Marketing", $groups)){
                    $result = 30; // le marketing 
                }else{
                    if(in_array("FR - KFC EasyMenu Franchise", $groups)){
                        $result = 20; // franchisé
                    }else{
                        if(in_array("FR - KFC EasyMenu Ops", $groups)){
                            $result = 10; // franchise coach et RDS
                        }else{

                        }
                    }
                }
            }            
            return $result;
        }
        
		/**
		 * \fn addAdminLine($menu_array,$access_level)
		 * \brief Renvoi les paramètres du lien de l'administrateur dans la page accueil.
		 * \details Cette fonction prends en paramètre le table des menus et le niveau d'accès d'un utilisateur et renvoi
		 * une table contenant le libéllé et la page distinataire pour le lien administrateur.
		 * 	 
		 * \param $menu_array \a Table des menus.
		 * \param $access_level \a Le niveau d'accès d'un utilisateur. 	 
		 *
		 *
		 * \return \a array Table contenant le libellé et la page destinataire.
		 */
		
        function addAdminLine($menu_array,$access_level){
            $menu_item_array = array("Administration","easyadmin.php");
            if($access_level >= 40) $menu_array[] =  $menu_item_array;            
            return $menu_array; 
        }
        
		/**
		 * \fn addLibelleLine($menu_array,$access_level)
		 * \brief Renvoi les paramètres du lien de gestion des libéllés dans la page accueil.
		 * \details Cette fonction prends en paramètre le table des menus et le niveau d'accès d'un utilisateur et renvoi
		 * une table contenant le libéllé et la page distinataire pour le lien de la gestion des libéllés.
		 * 	 
		 * \param $menu_array \a Table des menus.
		 * \param $access_level \a Le niveau d'accès d'un utilisateur. 	 
		 * 
		 *
		 * \return \a array Table contenant le libellé et la page destinataire.
		 */
		
        function addLibelleLine($menu_array,$access_level){         
            $menu_item_array = array("Gestion des libelles","gestion_libelle.php");
            if($access_level >= 30) $menu_array[] =  $menu_item_array;            
            return $menu_array; 
        }
        
		/**
		 * \fn addPriceHandlerLine($menu_array,$access_level,$franchise_zone_nat)
		 * \brief Renvoi les paramètres du lien de gestion des prix dans la page accueil.
		 * \details Cette fonction prends en paramètre le table des menus, le niveau d'accès d'un utilisateur 
		 * et un booléen désignant si l'utilisateur est un franchisé en zone national ou non. Puis renvoi
		 * une table contenant le libéllé et la page distinataire pour le lien de la gestion des prix.
		 * 	 
		 * \param $menu_array \a Table des menus.
		 * \param $access_level \a Le niveau d'accès d'un utilisateur. 	 
		 * \param $franchise_zone_nat \a Booléen indiquant si l'utilisateur est un franchisé en zone national ou non. 
		 *
		 * \return \a array Table contenant le libellé et la page destinataire.
		 */
		
        function addPriceHandlerLine($menu_array,$access_level,$franchise_zone_nat){           
            $menu_item_array = array("Gestion des prix","choix_zone_de_prix.php?action=gestion");
            if(($access_level >= 20) && !($franchise_zone_nat)) $menu_array[] =  $menu_item_array;            
            return $menu_array; 
        }
        
		/**
		 * \fn addPromoHandlerLine($menu_array,$access_level,$franchise_zone_nat)
		 * \brief Renvoi les paramètres du lien de gestion des promotions dans la page accueil.
		 * \details Cette fonction prends en paramètre le table des menus, le niveau d'accès d'un utilisateur 
		 * et un booléen désignant si l'utilisateur est un franchisé en zone national ou non. Puis renvoi
		 * une table contenant le libéllé et la page distinataire pour le lien de la gestion des promotions.
		 * 	 
		 * \param $menu_array \a Table des menus.
		 * \param $access_level \a Le niveau d'accès d'un utilisateur. 	 
		 * \param $franchise_zone_nat \a Booléen indiquant si l'utilisateur est un franchisé en zone national ou non. 
		 *
		 * \return \a array Table contenant le libellé et la page destinataire.
		 */
		
        function addPromoHandlerLine($menu_array,$access_level,$franchise_zone_nat){
            $menu_item_array = array("Gestion des promos","choix_type_promos.php?action=gestion");
            if($access_level >= 20) $menu_array[] =  $menu_item_array;            
            return $menu_array; 
        }
        
		/**
		 * \fn addPriceLine($menu_array,$access_level)
		 * \brief Renvoi les paramètres du lien de consultation des prix dans la page accueil.
		 * \details Cette fonction prends en paramètre le table des menus et le niveau d'accès d'un utilisateur et renvoi
		 * une table contenant le libéllé et la page distinataire pour le lien de la consultation des prix.
		 * 	 
		 * \param $menu_array \a Table des menus.
		 * \param $access_level \a Le niveau d'accès d'un utilisateur. 	 
		 * 
		 *
		 * \return \a array Table contenant le libellé et la page destinataire.
		 */
		
        function addPriceLine($menu_array,$access_level){           
            $menu_item_array = array("Consultation des prix","choix_zone_de_prix.php");
            if($access_level >= 10) $menu_array[] =  $menu_item_array;            
            return $menu_array;
        }
        
		/**
		 * \fn addPriceLine($menu_array,$access_level)
		 * \brief Renvoi les paramètres du lien de consultation des promotions dans la page accueil.
		 * \details Cette fonction prends en paramètre le table des menus et le niveau d'accès d'un utilisateur et renvoi
		 * une table contenant le libéllé et la page distinataire pour le lien de la consultation des promotions.
		 * 	 
		 * \param $menu_array \a Table des menus.
		 * \param $access_level \a Le niveau d'accès d'un utilisateur. 	 
		 * 
		 *
		 * \return \a array Table contenant le libellé et la page destinataire.
		 */
		
        function addPromoLine($menu_array,$access_level){
            $menu_item_array = array("Consultation des promos","consultation_promos.php");
            if($access_level >= 10) $menu_array[] =  $menu_item_array;            
            return $menu_array;
        }                       
        
		/**
		 * \fn getMenuArray($user, $passwd, $franchise_zone_nat)
		 * \brief Renvoi la liste des liens de la page accueil.
		 * \details Cette fonction prends en paramètre le table des menus, le niveau d'accès d'un utilisateur 
		 * et un booléen désignant si l'utilisateur est un franchisé en zone national ou non. Puis Renvoi la liste des liens de la page accueil.
		 * 	 
		 * \param $menu_array \a Table des menus.
		 * \param $access_level \a Le niveau d'accès d'un utilisateur. 	 
		 * \param $franchise_zone_nat \a Booléen indiquant si l'utilisateur est un franchisé en zone national ou non. 
		 *
		 * \return \a array Table contenant la liste des liens de la page accueil.
		 */
		
        function getMenuArray($user, $passwd, $franchise_zone_nat){
            $access_level = $this->getUserLevel($user, $passwd);
            $menu_array = array();
            
            $menu_array = $this->addAdminLine($menu_array,$access_level);
            $menu_array = $this->addLibelleLine($menu_array,$access_level);
            $menu_array = $this->addPriceHandlerLine($menu_array,$access_level,$franchise_zone_nat);
            $menu_array = $this->addPromoHandlerline($menu_array,$access_level,$franchise_zone_nat);
            $menu_array = $this->addPriceLine($menu_array,$access_level);
            //$menu_array = $this->addPromoLine($menu_array,$access_level);
            
            if(count($menu_array)==0) die(header('Location: ../index.php')); // pas oublier la destruction de la session             
            return $menu_array;           
        }       
        
    }

?>
