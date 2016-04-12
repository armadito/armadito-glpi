<?php

// Page qui reçoit les requêtes de l'agent

include_once ("../../../inc/includes.php");
include_once ("inc/armadito-includes.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){ 
	echo "Hello armadito !";
}

?>
