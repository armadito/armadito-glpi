<?php

// Page qui reçoit les requêtes de l'agent

include_once ("../../inc/includes.php");
include_once ("inc/armadito-includes.php");
include_once ("inc/armadito-state.php");

function parseIncomingRequest($json_content){

      $json = json_decode($json_content);

      if(json_last_error() == JSON_ERROR_NONE)
      {
         echo "json::success";
         var_dump($json);
      }
      else
      {
         echo "json::".json_last_error_msg();
      }
}

$rawdata = file_get_contents("php://input");
if ((isset($_GET['action'])
   && isset($_GET['machineid']))
      || !empty($rawdata)) {

   include_once("front/communication.php");
}
else{
  header("Content-Type: application/json");
  echo '{ glpi_response : "invalid GET request"}';
}

if($_SERVER['REQUEST_METHOD'] == "POST"){ 

    header("Content-Type: application/json");
    echo '{ glpi_response : "ok_post"}';
}

?>
