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
    protected $scanconfigid;

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
      // $scanconfigid
      // $this->setScanType($POST["scan_name"]); // corresponds scan_name in pa_scanconfigs table
      //$this->antivirus_name = $jobobj->getAntivirusName();
      //$this->antivirus_version = $jobobj->getAntivirusVersion();
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
            $this->scan_name = $data["scan_name"];
            $error->setMessage(0, 'Successfully scan init from DB.');
         }
      }

      $error->setMessage(1, 'No scans found for job_id '.$job_id);
      return $error;
	}

   function toJson() {
       return '{
                  "scan_type": "'.$this->scan_type.'",
                  "scan_path": "'.$this->scan_path.'",
                  "scan_options": "'.$this->scan_options.'"
               }';
   }

   function setScanType ($id){
      PluginArmaditoToolbox::validateInt($id);
      switch($id){
         case 0:
            $this->scan_type = "complete";
            break;
         case 1:
            $this->scan_type = "fast";
            break;
         case 2:
            $this->scan_type = "custom";
            break;
         default:
            $this->scan_type = "unknown";
            break;
      }
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

      /*
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
      */

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
      global $DB;
      $error = new PluginArmaditoError();

      $query = "INSERT INTO `glpi_plugin_armadito_scans`
                           (`plugin_armadito_jobs_id`,
                            `plugin_armadito_agents_id`,
                            `scan_type`,
                            `scan_path`,
                            `scan_options`,
                            `antivirus_name`,
                            `antivirus_version`) VALUES (?,?,?,?,?,?,?)";

      $stmt = $DB->prepare($query);

      PluginArmaditoToolbox::logE("insert into Scan db.");

      if(!$stmt) {
         $error->setMessage(1, 'Scan insert preparation failed.');
         $error->log();
         return $error;
      }

      if(!$stmt->bind_param('iisssss', $job_id, $agent_id, $scan_type, $scan_path, $scan_options, $antivirus_name, $antivirus_version)) {
            $error->setMessage(1, 'Scan insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
      }

      $job_id = $job_id_;
      $agent_id = $this->agentid;
      $scan_type = $this->scan_type;
      $scan_path = $this->scan_path;
      $scan_options = $this->scan_options;
      $antivirus_name = $this->antivirus_name;
      $antivirus_version = $this->antivirus_version;

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
