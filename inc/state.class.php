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

	static function getTypeName($nb=0) {
	  return __('State', 'armadito');
	}

    function __construct() {
		//
    }

	function init($jobj) {
		 $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
       $this->jobj = $jobj;
	}

    function toJson() {
         return '{}';
    }

   static function getDefaultDisplayPreferences(){
       $prefs = "";
		 $nb_columns = 7;   
       for( $i = 1; $i <= $nb_columns; $i++){
            $prefs .= "(NULL, 'PluginArmaditoState', '".$i."', '".$i."', '0'),";
       }
       return $prefs;
   }

	function getSearchOptions() {

      $tab = array();
      $tab['common'] = __('State', 'armadito');

      $i = 1;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'agent_id';
      $tab[$i]['name']      = __('Agent Id', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'update_status';
      $tab[$i]['name']      = __('Update Status', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'last_update';
      $tab[$i]['name']      = __('Last Update', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'antivirus_name';
      $tab[$i]['name']      = __('Antivirus Name', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'antivirus_version';
      $tab[$i]['name']      = __('Antivirus Version', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'antivirus_realtime';
      $tab[$i]['name']      = __('Antivirus On-access', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'antivirus_service';
      $tab[$i]['name']      = __('Antivirus Service', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      return $tab;
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

		 if($error->getCode() != 0){
			return $error;
		 }

         // if AV is Armadito, we also update states of each modules
         if($this->jobj->task->antivirus->name == "Armadito"){
               foreach($this->jobj->task->msg->info->modules as $jobj_module){
			   		$module = new PluginArmaditoStateModule($this->agentid, $this->jobj, $jobj_module);
					$error = $module->run();
					if($error->getCode() != 0){
						return $error;
					}
			   }
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
                 WHERE `agent_id`='".$this->agentid."'";
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

      $query = "INSERT INTO `glpi_plugin_armadito_states` (`agent_id`, `update_status`, `last_update`, `antivirus_name`, `antivirus_version`, `antivirus_realtime`, `antivirus_service`) VALUES (?,?,?,?,?,?,?)";

      $stmt = $DB->prepare($query);

      if(!$stmt) {
         $error->setMessage(1, 'State insert preparation failed.');
         $error->log();
         return $error;
      }

      if(!$stmt->bind_param('issssss', $agent_id, $update_status, $last_update, $antivirus_name, $antivirus_version, $antivirus_realtime, $antivirus_service)) {
            $error->setMessage(1, 'State insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
      }

      $agent_id = $this->agentid;
      $update_status = $this->jobj->task->msg->info->update->status;
      $last_update = $this->jobj->task->msg->info->update->{"last-update"};
      $antivirus_name = $this->jobj->task->antivirus->name;
      $antivirus_version = $this->jobj->task->antivirus->version;
      $antivirus_realtime = $this->jobj->task->msg->info->antivirus->realtime;
      $antivirus_service = $this->jobj->task->msg->info->antivirus->service;

      if(!$stmt->execute()){
         $error->setMessage(1, 'State insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
         $error->log();
         $stmt->close();
         return $error;
      }

      $stmt->close();
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

		$query = "UPDATE `glpi_plugin_armadito_states`
				 SET `update_status`=?,
				     `last_update`=?,
				     `antivirus_name`=?,
				     `antivirus_version`=?,
				     `antivirus_realtime`=?,
					 `antivirus_service`=?
				  WHERE `agent_id`=?";

		$stmt = $DB->prepare($query);

		if(!$stmt) {
			$error->setMessage(1, 'State update preparation failed.');
			$error->log();
			return $error;
		}

		if(!$stmt->bind_param('ssssssi', $update_status, $last_update, $antivirus_name, $antivirus_version, $antivirus_realtime, $antivirus_service, $agent_id)) {
			$error->setMessage(1, 'State update bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
			$error->log();
			$stmt->close();
			return $error;
		}

		$agent_id = $this->agentid;
		$update_status = $this->jobj->task->msg->info->update->status;
		$last_update = $this->jobj->task->msg->info->update->{"last-update"};
		$antivirus_name = $this->jobj->task->antivirus->name;
		$antivirus_version = $this->jobj->task->antivirus->version;
		$antivirus_realtime = $this->jobj->task->msg->info->antivirus->realtime;
		$antivirus_service = $this->jobj->task->msg->info->antivirus->service;

		if(!$stmt->execute()){
		 $error->setMessage(1, 'State update execution failed (' . $stmt->errno . ') ' . $stmt->error);
		 $error->log();
		 $stmt->close();
		 return $error;
		}

		$stmt->close();
		$error->setMessage(0, 'State successfully updated.');
		return $error;
    }
}
?>
