<?php

// Page qui reçoit les requêtes de l'agent

// include_once ("../../inc/includes.php");
include_once ("inc/armadito-includes.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){ 

   if(isset($_POST['content-json'])){

      $json = json_decode($_POST['content-json']);

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
}

?>
