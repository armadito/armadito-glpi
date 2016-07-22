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
   $error = new PluginArmaditoError();
   $communication  = new PluginArmaditoCommunication();
   $communication->init();

   // Check plugin installation
   if (!class_exists("PluginArmaditoAgent")) {
      $error->setMessage(1, "Plugin armadito is not installed.");
      $error->log();
      $communication->setMessage($error->toJson(), 404);
      $communication->sendMessage();
      session_destroy();
      exit();
   }

   PluginArmaditoLastContactStat::increment();

   // Parse json obj
   $jobj = PluginArmaditoToolbox::parseJSON($rawdata);

   if(!$jobj){
      $error->setMessage(1, "Fail parsing incoming json : ".json_last_error_msg());
      $error->log();
      $communication->setMessage($error->toJson(), 405);
      $communication->sendMessage();
      session_destroy();
      exit();
   }

   $state = new PluginArmaditoState();
   $state->init($jobj);
   $error = $state->run();

   if($error->getCode() == 0){ // success
      $communication->setMessage($state->toJson(), 200);
   }
   else{
      $communication->setMessage($error->toJson(), 500);
      $error->log();
   }

   $communication->sendMessage();

   session_destroy();
}
else{
  http_response_code(400);
  header("Content-Type: application/json");
  echo '{ "plugin_version": "'.PLUGIN_ARMADITO_VERSION.'", "code": 1, "message": "Invalid request sent to plugin index." }}';
}

?>
