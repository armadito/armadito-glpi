<?php

// Page qui reçoit les requêtes de l'agent

include_once ("../../inc/includes.php");
include_once ("inc/armadito-includes.php");
include_once ("inc/armadito-state.php");

$rawdata = file_get_contents("php://input");
if ((isset($_GET['action'])
   && isset($_GET['machineid']))
      || !empty($rawdata)) {
   // GET or POST
   include_once("front/communication.php");
}
else{
  header("Content-Type: application/json");
  echo '{ glpi_response : "invalid request"}';
}

?>
