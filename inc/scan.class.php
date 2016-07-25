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
    protected $jobj;

	static function getTypeName($nb=0) {
	  return __('Scan', 'armadito');
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

      /*
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

      $i++;

      $tab[$i]['table']     = 'glpi_plugin_armadito_scandetails';
      $tab[$i]['field']     = 'id';
      $tab[$i]['name']      = __('Details', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginArmaditoScandetail';
      $tab[$i]['massiveaction'] = FALSE;

      */

      return $tab;
   }

    /**
    * Insert or Update Scans
    *
    * @return PluginArmaditoError obj
    **/
     function run(){

         /*
         // Update global Antivirus Scan
         if($this->isScaninDB()) {
            $error = $this->updateScan();
         }
         else {
            $error = $this->insertScan();
         }

		   if($error->getCode() != 0){
			   return $error;
		   }


         // if AV is Armadito, we also update Scans of each modules
         if($this->jobj->task->antivirus->name == "Armadito"){
               foreach($this->jobj->task->msg->info->modules as $jobj_module){
                  $module = new PluginArmaditoScanModule();
                  $module->init($this->agentid, $this->jobj, $jobj_module);
					   $error = $module->run();
					   if($error->getCode() != 0){
						   return $error;
					   }
			      }
         } */


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
