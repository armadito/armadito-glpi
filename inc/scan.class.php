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
 * Class dealing with Armadito AV scan
 **/
class PluginArmaditoScan extends CommonDBTM {
    protected $agentid;
    protected $agent;
    protected $jobj;
    protected $job;
    protected $scan_type;
    protected $scan_path;
    protected $scan_options;

	static function getTypeName($nb=0) {
	  return __('Scan', 'armadito');
	}

    function __construct() {
         $this->job = new PluginArmaditoJob();
         $this->scan_type = -1;
         $this->scan_path = "";
         $this->scan_options = "";
    }

	function initFromForm($key, $POST, $agentobj) {

		 $this->agentid = $key;
       $this->job->init("scan");
       $this->scan_type = PluginArmaditoToolbox::validateInt($POST["scan_type"]);
	}

	function initFromJson($jobj) {
		 $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
       $this->jobj = $jobj;
	}

   function toJson() {
        return '{}';
   }

   static function getDefaultDisplayPreferences(){
       $prefs = "";
       $nb_columns = 8;
       for( $i = 1; $i <= $nb_columns; $i++){
            $prefs .= "(NULL, 'PluginArmaditoScan', '".$i."', '".$i."', '0'),";
       }
       return $prefs;
   }

	function getSearchOptions() {

      $tab = array();
      $tab['common'] = __('Scan', 'armadito');

      $i = 1;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'agent_id';
      $tab[$i]['name']      = __('Agent Id', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      return $tab;
   }

    /**
    * add new scan in database
    *
    * @return PluginArmaditoError obj
    **/
    function run(){
       return true;
    }

    /**
    * Insert or Update Scans
    *
    * @return PluginArmaditoError obj
    **/
     function updateDB(){

         $error = new PluginArmaditoError();
         $error->setMessage(0, 'Scan successfully inserted.');
         return $error;
     }

    /**
    * Check if Scan is already in database
    *
    * @return TRUE or FALSE
    **/
    function isScaninDB(){
      global $DB;

      $query = "SELECT update_status FROM `glpi_plugin_armadito_scans`
                 WHERE `agent_id`='".$this->agentid."'";
      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error isScaninDB : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         return true;
      }

      return false;
    }

    /**
    * Insert Scan in database
    *
    * @return PluginArmaditoError obj
    **/
    function insertScan(){
      global $DB;
      $error = new PluginArmaditoError();

      $query = "INSERT INTO `glpi_plugin_armadito_scans` (`agent_id`, `update_status`, `last_update`, `antivirus_name`, `antivirus_version`, `antivirus_realtime`, `antivirus_service`, `plugin_armadito_Scandetails_id`) VALUES (?,?,?,?,?,?,?,?)";

      $stmt = $DB->prepare($query);

      if(!$stmt) {
         $error->setMessage(1, 'Scan insert preparation failed.');
         $error->log();
         return $error;
      }

      if(!$stmt->bind_param('issssssi', $agent_id, $update_status, $last_update, $antivirus_name, $antivirus_version, $antivirus_realtime, $antivirus_service, $Scandetails_id)) {
            $error->setMessage(1, 'Scan insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
      }

      $agent_id = $this->agentid;
      $Scandetails_id = $agent_id;
      $antivirus_name = $this->jobj->task->antivirus->name;
      $antivirus_version = $this->jobj->task->antivirus->version;

      if(!$stmt->execute()){
         $error->setMessage(1, 'Scan insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
         $error->log();
         $stmt->close();
         return $error;
      }

      $stmt->close();
      $error->setMessage(0, 'Scan successfully inserted.');
      return $error;
    }

    /**
    * Uptate Scan in database
    *
    * @return PluginArmaditoError obj
    **/
    function updateScan(){
		global $DB;
		$error = new PluginArmaditoError();

		$query = "UPDATE `glpi_plugin_armadito_scans`
				 SET `update_status`=?,
				     `last_update`=?,
				     `antivirus_name`=?,
				     `antivirus_version`=?,
				     `antivirus_realtime`=?,
					  `antivirus_service`=?,
                 `plugin_armadito_scandetails_id`=?
				  WHERE `agent_id`=?";

		$stmt = $DB->prepare($query);

		if(!$stmt) {
			$error->setMessage(1, 'Scan update preparation failed.');
			$error->log();
			return $error;
		}

		if(!$stmt->bind_param('ssssssii', $update_status, $last_update, $antivirus_name, $antivirus_version, $antivirus_realtime, $antivirus_service, $agent_id, $scandetails_id)) {
			$error->setMessage(1, 'Scan update bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
			$error->log();
			$stmt->close();
			return $error;
		}

		$agent_id = $this->agentid;
      $scandetails_id = $agent_id;
		$antivirus_name = $this->jobj->task->antivirus->name;
		$antivirus_version = $this->jobj->task->antivirus->version;

		if(!$stmt->execute()){
		 $error->setMessage(1, 'Scan update execution failed (' . $stmt->errno . ') ' . $stmt->error);
		 $error->log();
		 $stmt->close();
		 return $error;
		}

		$stmt->close();
		$error->setMessage(0, 'Scan successfully updated.');
		return $error;
    }
}
?>
