<?php

/*
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
}*/

ob_start();
ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
ini_set('display_errors', 1);

if (session_id()=="") {
   session_start();
}

if (!defined('GLPI_ROOT')) {
   include_once("../../../inc/includes.php");
}

$_SESSION['glpi_use_mode'] = Session::NORMAL_MODE;
if (!isset($_SESSION['glpilanguage'])) {
   $_SESSION['glpilanguage'] = 'fr_FR';
}

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
set_error_handler(array('Toolbox', 'userErrorHandlerDebug'));

$_SESSION['glpi_use_mode'] = 0;
$_SESSION['glpiparententities'] = '';
$_SESSION['glpishowallentities'] = TRUE;

ob_end_clean();

header("server-type: glpi/armadito ".PLUGIN_ARMADITO_VERSION);

if (!class_exists("PluginArmaditoArmadito")) {
   header("Content-Type: application/json");
   echo '{ "plugin_response" : "Plugin armadito not installed." }';
   session_destroy();
   exit();
}

// $paCommunication  = new PluginArmaditoCommunication();
if (!isset($rawdata)) {
   // GET requests
   header("Content-Type: application/json");
   echo '{ "plugin_response" : "Plugin armadito GET OK ! }';
}
else{
   // POST requests
   $rawdata = file_get_contents("php://input");
   header("Content-Type: application/json");
   echo '{ "plugin_response" : "Plugin armadito POST OK ! }';
}

session_destroy();

?>
