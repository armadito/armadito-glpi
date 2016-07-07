<?php

/**
   Copyright (C) 2010-2016 by the FusionInventory Development Team
   Copyright (C) 2016 Teclib'

   This file is part of Armadito Plugin for GLPI.

   Armadito Plugin for GLPI is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Armadito Plugin for GLPI is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with Armadito Plugin for GLPI. If not, see <http://www.gnu.org/licenses/>.

**/

include_once ("../../../../inc/includes.php");

$rawdata = file_get_contents("php://input");
if (!empty($rawdata)) { // POST /states

   // init GLPI stuff
   $communication  = new PluginArmaditoCommunication();
   $communication->init();

   // Check plugin installation
   if (!class_exists("PluginArmaditoArmadito")) {
      $response = '"error" : "Plugin armadito is not installed."';
      PluginArmaditoToolbox::logE($response);
      $communication->setMessage($response);
      $communication->sendMessage();
      session_destroy();
      exit();
   }

   // Parse json obj
   $jobj = PluginArmaditoToolbox::parseJSON($rawdata);

   if(!$jobj){
      $response = '"error" : "error when parsing incoming json : '.json_last_error_msg().'"';
      PluginArmaditoToolbox::logE($response);
      $communication->setMessage($response);
      $communication->sendMessage();
      session_destroy();
      exit();
   }

   $Enrollment = new PluginArmaditoEnrollment($jobj);
   $response = $Enrollment->enroll();

   $communication->setMessage($response);
   $communication->sendMessage();

   session_destroy();
}
else{
  http_response_code(400);
  header("Content-Type: application/json");
  echo '{ "plugin_response" : { "version": "'.PLUGIN_ARMADITO_VERSION.'", "error": "Invalid request sent to plugin index." }}';
}

?>
