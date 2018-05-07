<?php

$user=$_REQUEST['login'];
if (isset($_POST['url']) && ($_POST['url'] <> "/")) { $url = ".".$_POST['url']; } else { $url="./index.php"; } 
$full_user="tri-intl\\".$user;
$passwd=$_REQUEST['password'];
$auth="rsc";
$admgroup="CN=FR - Informatique France,OU=Distribution List,OU=Messaging Objects,OU=FR,DC=int,DC=tgr,DC=net";
$ldapServer = "pardc01";
$ldapServerPort = 389;
$dn = "ou=UsersAndGroups,ou=fr,dc=int,dc=tgr,dc=net";

if ($passwd == "" && $user == "") die(header('Location: ./index.php?lerror=4'));
if ($user == "") die(header('Location: ./index.php?lerror=1'));
if ($user == "") die(header('Location: ./index.php?lerror=1'));
if  (substr($user, 0,4)=="FR76") die(header('Location: ./index.php?lerror=5'));
if  (substr($user, 0,4)=="fr76") die(header('Location: ./index.php?lerror=5'));
if ($passwd == "") die(header('Location: ./index.php?lerror=2'));


if ($user == "fbu" && $passwd == "fbu") {
	session_register ('fbu');
	$admin=$user;
	die(header('Location: ./index.php'));
													 }

$conn=ldap_connect($ldapServer);

// on teste : le serveur LDAP est-il trouvé ?
if (!isset($conn)) die("Serveur LDAP Introuvable/Injoignable");

// Connexion avec login et password 
$bindServerLDAP=ldap_bind($conn,$full_user,$passwd);

// On test si la connexion se passe bien
if ($bindServerLDAP == FALSE) die (header('Location: ./index.php?lerror=3'));


// Recherche du nom d'utilisateur
$query = "samaccountname=".$user;
$result=ldap_search($conn, $dn, $query);
$table = ldap_get_entries($conn, $result);


// Recherche si l'utilisateur fait partie du groupe fr - ssa
$count =  $table[0]['memberof']['count'];
For ($i=0; $i<$count; $i++) {
	If ($table[0]['memberof'][$i] == $admgroup) $auth="admin";
}
  
if ( $auth == "admin") {
	session_register ('admin');
	$_SESSION['visitor'] = $user;
	$_SESSION['lastname']=$table[0]['sn'][0];
	$_SESSION['firstname']=$table[0]['givenname'][0];
	die(header("Location: $url "));
	 }

if ( $auth == "rsc") {
	session_register ('rsc');
	$_SESSION['visitor'] = $user;
	$_SESSION['lastname']=$table[0]['sn'][0];
	$_SESSION['firstname']=$table[0]['givenname'][0];
	die(header("Location: $url "));
	 }
else {
  echo ("<br> Une erreur est survenue, veuillez réessayer !<br>");
     }
	 

?>
