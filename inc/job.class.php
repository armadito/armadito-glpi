<?php

/**
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

include_once("toolbox.class.php");

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class dealing with Armadito AV state
 **/
class PluginArmaditoJob extends CommonDBTM {
      protected $id;
      protected $jobj;
      protected $type;
      protected $priority;
      protected $agentid;

      function __construct() {
         $this->type = -1;
         $this->priority = -1;
         $this->id = -1;
         $this->agentid = -1;
      }

      function initFromForm($key, $type, $POST) {
         $this->agentid = $key;
         $this->type = $type;
         $this->setPriority($POST["job_priority"]);

         PluginArmaditoToolbox::logIfExtradebug(
            'pluginArmadito-job',
            'New PluginArmaditoJob object.'
         );
      }

      function setPriority ($id){
         PluginArmaditoToolbox::validateInt($id);
         switch($id){
            case 0:
               $this->priority = "low";
               break;
            case 1:
               $this->priority = "medium";
               break;
            case 2:
               $this->priority = "high";
               break;
            case 3:
               $this->priority = "urgent";
               break;
            default:
               $this->priority = "unknown";
               break;
         }
      }

      function toJson() {
         return '{}';
      }
}
?>
