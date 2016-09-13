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
	protected $id;
    protected $agentid;
    protected $agent;
    protected $jobj;
    protected $job;
    protected $scanconfigid;
    protected $scanconfigobj;

	static function getTypeName($nb=0) {
	  return __('Scan', 'armadito');
	}

   static function canCreate() {
      if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
         return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w');
      }
      return false;
   }

   static function canView() {

      if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
         return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w'
                 || $_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'r');
      }
      return false;
   }

    function __construct() {
      //
    }

	function initFromForm($jobobj, $POST) {
      $this->agentid = $jobobj->getAgentId();
      $this->scanconfigid = PluginArmaditoToolbox::validateInt($POST["scanconfig_id"]);
	}

	function initFromJson($jobj) {
      $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
      $this->jobj = $jobj;
	}

	function initFromDB($job_id) {
      global $DB;
      $error = new PluginArmaditoError();
      $query = "SELECT * FROM `glpi_plugin_armadito_scans`
              WHERE `plugin_armadito_jobs_id`='".$job_id."'";

      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error getJobs : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         if($data = $DB->fetch_assoc($ret)){
            $this->agentid = $data["plugin_armadito_agents_id"];
            $this->scanconfigid = $data["plugin_armadito_scanconfigs_id"];

            $this->scanconfigobj = new PluginArmaditoScanConfig();
            if(!$this->scanconfigobj->initFromDB($this->scanconfigid)){
                $error->setMessage(1, 'Init scanconfig from DB failed.');
                return $error;
            }

            $error->setMessage(0, 'Successfully scan init from DB.');
            return $error;
         }
      }

      $error->setMessage(1, 'No scans found for job_id '.$job_id);
      return $error;
	}

   function toJson() {
       return $this->scanconfigobj->toJson();
   }

   static function getDefaultDisplayPreferences(){
       $prefs = "";
       $nb_columns = 10;
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
      $tab[$i]['field']     = 'id';
      $tab[$i]['name']      = __('Scan Id', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginArmaditoScan';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = 'glpi_plugin_armadito_jobs';
      $tab[$i]['field']     = 'id';
      $tab[$i]['name']      = __('Job Id', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginArmaditoJob';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = 'glpi_plugin_armadito_agents';
      $tab[$i]['field']     = 'id';
      $tab[$i]['name']      = __('Agent Id', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginArmaditoAgent';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = 'glpi_plugin_armadito_scanconfigs';
      $tab[$i]['field']     = 'scan_name';
      $tab[$i]['name']      = __('Scan Name', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginArmaditoScanConfig';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'progress';
      $tab[$i]['name']      = __('Progress', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'suspicious_count';
      $tab[$i]['name']      = __('Suspicious', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'malware_count';
      $tab[$i]['name']      = __('Malware', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'scanned_count';
      $tab[$i]['name']      = __('Scanned', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'duration';
      $tab[$i]['name']      = __('Duration', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

	  $tab[$i]['table']     = 'glpi_plugin_armadito_antiviruses';
	  $tab[$i]['field']     = 'fullname';
	  $tab[$i]['name']      = __('Antivirus', 'armadito');
	  $tab[$i]['datatype']  = 'itemlink';
	  $tab[$i]['itemlink_type'] = 'PluginArmaditoAntivirus';
	  $tab[$i]['massiveaction'] = FALSE;
      return $tab;
   }

    /**
    * Check if Scan is already in database
    *
    * @return TRUE or FALSE
    **/
    function isScaninDB(){
      global $DB;

      $query = "SELECT id FROM `glpi_plugin_armadito_scans`
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
    * Add Scan in database
    *
    * @return PluginArmaditoError obj
    **/
    function addObj( $job_id_ ){
     $error = new PluginArmaditoError();
	 $dbmanager = new PluginArmaditoDbManager();
	 $dbmanager->init();

	 $params["plugin_armadito_jobs_id"]["type"] = "i";
	 $params["plugin_armadito_agents_id"]["type"] = "i";
	 $params["plugin_armadito_scanconfigs_id"]["type"] = "i";
	 $params["plugin_armadito_antiviruses_id"]["type"] = "i";

	 $query_name = "NewScan";
	 $dbmanager->addQuery($query_name, "INSERT", $this->getTable(), $params);

	 if(!$dbmanager->prepareQuery($query_name)){
		return $dbmanager->getLastError();
	 }

	 if(!$dbmanager->bindQuery($query_name)){
		return $dbmanager->getLastError();
	 }

	 $dbmanager->setQueryValue($query_name, "plugin_armadito_jobs_id", $job_id_);
	 $dbmanager->setQueryValue($query_name, "plugin_armadito_agents_id", $this->agentid);
	 $dbmanager->setQueryValue($query_name, "plugin_armadito_scanconfigs_id", $this->scanconfigid);
	 $dbmanager->setQueryValue($query_name, "plugin_armadito_antiviruses_id", 0);

	 if(!$dbmanager->executeQuery($query_name)){
		return $dbmanager->getLastError();
	 }

	 $dbmanager->closeQuery($query_name);

	 $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
	 if($this->id > 0){
		PluginArmaditoToolbox::validateInt($this->id);
		$error->setMessage(0, 'New Scan successfully added in database.');
	 }
     else{
		$error->setMessage(1, 'Unable to get new Scan Id');
	 }
     return $error;
    }

    /**
    * Add Scan Results in database
    *
    * @return PluginArmaditoError obj
    **/
    function updateScanInDB(){
     $error = new PluginArmaditoError();
	 $dbmanager = new PluginArmaditoDbManager();
	 $dbmanager->init();

	 $params["start_time"]["type"] = "s";
	 $params["duration"]["type"] = "s";
	 $params["malware_count"]["type"] = "i";
	 $params["suspicious_count"]["type"] = "i";
	 $params["scanned_count"]["type"] = "i";
	 $params["progress"]["type"] = "i";
	 $params["plugin_armadito_jobs_id"]["type"] = "i";

	 $query_name = "UpdateScan";
	 $dbmanager->addQuery($query_name, "UPDATE", $this->getTable(), $params, "plugin_armadito_jobs_id");

	 if(!$dbmanager->prepareQuery($query_name)){
		return $dbmanager->getLastError();
	 }

	 if(!$dbmanager->bindQuery($query_name)){
		return $dbmanager->getLastError();
	 }

	 $dbmanager->setQueryValue($query_name, "duration", $this->jobj->task->obj->duration);
	 $dbmanager->setQueryValue($query_name, "start_time", $this->jobj->task->obj->start_time);
	 $dbmanager->setQueryValue($query_name, "malware_count", $this->jobj->task->obj->malware_count);
	 $dbmanager->setQueryValue($query_name, "suspicious_count", $this->jobj->task->obj->suspicious_count);
	 $dbmanager->setQueryValue($query_name, "scanned_count", $this->jobj->task->obj->scanned_count);
	 $dbmanager->setQueryValue($query_name, "progress", $this->jobj->task->obj->progress);
	 $dbmanager->setQueryValue($query_name, "plugin_armadito_jobs_id",$this->jobj->task->obj->job_id); # WHERE

	 if(!$dbmanager->executeQuery($query_name)){
		return $dbmanager->getLastError();
	 }

	 $dbmanager->closeQuery($query_name);
	 $error->setMessage(0, 'Scan successfully updated in database.');
     return $error;
    }

      function defineTabs($options=array()){

         $ong = array();
         $this->addDefaultFormTab($ong);
         $this->addStandardTab('Log', $ong, $options);

         return $ong;
      }

      /**
      * Display form
      *
      * @param $agent_id integer ID of the agent
      * @param $options array
      *
      * @return bool TRUE if form is ok
      *
      **/
      function showForm($table_id, $options=array()) {

         // Protect against injections
         PluginArmaditoToolbox::validateInt($table_id);

         // Init Form
         $this->initForm($table_id, $options);
         $this->showFormHeader($options);

         echo "<tr class='tab_bg_1'>";
         echo "<td>".__('Name')." :</td>";
         echo "<td align='center'>";
         Html::autocompletionTextField($this,'name', array('size' => 40));
         echo "</td>";
         echo "<td>".__('Agent Id', 'armadito')."&nbsp;:</td>";
         echo "<td align='center'>";
         echo "<b>".htmlspecialchars($this->fields["plugin_armadito_agents_id"])."</b>";
         echo "</td>";
         echo "</tr>";
      }
}
?>
