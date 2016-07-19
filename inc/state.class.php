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
class PluginArmaditoState extends CommonDBTM {
     protected $agentid;
     protected $jobj;
     
     function __construct($jobj) {

      $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
      $this->jobj = $jobj;

      PluginArmaditoToolbox::logIfExtradebug(
         'pluginArmadito-state',
         'New PluginArmaditoState object.'
      );
     }

     function toJson() {
         return '{}';
     }

    /**
    * Insert or Update states
    *
    * @return PluginArmaditoError obj
    **/
     function run(){

         // Update global Antivirus state
         if($this->isStateinDB()) {
            $error = $this->updateState();
         }
         else {
            $error = $this->insertState();
         }

         // if AV is Armadito, we also update states of each modules
         if($this->jobj->task->antivirus->name == "armadito"){
               # foreach($this->jobj->task->msg)
         }
      
         return $error;
     }

    /**
    * Check if state is already in database
    *
    * @return TRUE or FALSE
    **/
    function isStateinDB(){
      global $DB;

      $query = "SELECT update_status FROM `glpi_plugin_armadito_states`
                 WHERE `id`='".$this->agent_id."'";
      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error isStateinDB : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         return true;
      }

      return false;
    }

    /**
    * Insert state in database
    *
    * @return PluginArmaditoError obj
    **/
    function insertState(){
      global $DB;
      $error = new PluginArmaditoError();
      $error->setMessage(0, 'State successfully inserted.');
      return $error;
    }

    /**
    * Uptate state in database
    *
    * @return PluginArmaditoError obj
    **/
    function updateState(){
      global $DB;
      $error = new PluginArmaditoError();
      $error->setMessage(0, 'State successfully updated.');
      return $error;
    }
}
?>
