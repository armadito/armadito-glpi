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
 * Class dealing with Armadito AV module state
 **/
class PluginArmaditoStateModule extends CommonDBTM {
	protected $jobj;
	protected $state_jobj;
	protected $agentid;

	function __construct($agent_id, $state_jobj, $jobj) {
		$this->agentid = $agent_id;
		$this->state_jobj = $state_jobj;
		$this->jobj = $jobj;

		PluginArmaditoToolbox::logIfExtradebug(
		 'pluginArmadito-statemodule',
		 'New PluginArmaditoStateModule object.'
		);
	}

	function toJson() {
		 return '{}';
	}

	function run(){

		 if($this->isStateModuleinDB()) {
            $error = $this->updateStateModule();
         }
         else {
            $error = $this->insertStateModule();
         }
		 return $error;
	}

 	/**
    * Check if module state is already in database
    *
    * @return TRUE or FALSE
    **/
    function isStateModuleinDB(){
      global $DB;

      $query = "SELECT id FROM `glpi_plugin_armadito_states_modules`
                 WHERE `agent_id`='".$this->agentid."' AND `module_name`='".$this->jobj->name."'";

	  PluginArmaditoToolbox::logE($query);

      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error isStateModuleinDB : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         return true;
      }

      return false;
    }

    /**
    * Insert module state in database
    *
    * @return PluginArmaditoError obj
    **/
    function insertStateModule(){
      global $DB;
      $error = new PluginArmaditoError();

      $query = "INSERT INTO `glpi_plugin_armadito_states_modules` (`agent_id`, `module_name`, `module_version`, `module_update_status`, `module_last_update`) VALUES (?,?,?,?,?)";

      $stmt = $DB->prepare($query);

      if(!$stmt) {
         $error->setMessage(1, 'State module insert preparation failed.');
         $error->log();
         return $error;
      }

      if(!$stmt->bind_param('issss', $agent_id, $module_name, $module_version, $module_update_status, $module_last_update)) {
            $error->setMessage(1, 'State module insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
      }

      $agent_id = $this->agentid;
      $module_name = $this->jobj->name;
      $module_version = $this->jobj->version;
      $module_update_status = $this->jobj->update->status;
      $module_last_update = $this->jobj->update->{"last-update"};

      if(!$stmt->execute()){
         $error->setMessage(1, 'State module insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
         $error->log();
         $stmt->close();
         return $error;
      }

      $stmt->close();
      $error->setMessage(0, 'State module successfully inserted.');
      return $error;
    }

    /**
    * Uptate module state in database
    *
    * @return PluginArmaditoError obj
    **/
    function updateStateModule(){
		global $DB;
		$error = new PluginArmaditoError();

		$query = "UPDATE `glpi_plugin_armadito_states_modules`
				 SET `module_version`=?,
				     `module_update_status`=?,
					 `module_last_update`=?
				  WHERE `agent_id`=? AND `module_name`=?";

		$stmt = $DB->prepare($query);

		if(!$stmt) {
			$error->setMessage(1, 'State module update preparation failed.');
			$error->log();
			return $error;
		}

		if(!$stmt->bind_param('sssis', $module_version, $module_update_status, $module_last_update, $agent_id, $module_name)) {
			$error->setMessage(1, 'State module update bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
			$error->log();
			$stmt->close();
			return $error;
		}

		$agent_id = $this->agentid;
		$module_name = $this->jobj->name;
		$module_version = $this->jobj->version;
		$module_update_status = $this->jobj->update->status;
		$module_last_update = $this->jobj->update->{"last-update"};

		if(!$stmt->execute()){
		 $error->setMessage(1, 'State module update execution failed (' . $stmt->errno . ') ' . $stmt->error);
		 $error->log();
		 $stmt->close();
		 return $error;
		}

		$stmt->close();
		$error->setMessage(0, 'State module successfully updated.');
		return $error;
    }
}
?>
