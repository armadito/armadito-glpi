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
 * Class dealing with Jobs
 **/
class PluginArmaditoJob extends CommonDBTM {
      protected $id;
      protected $obj;
      protected $jobj;
      protected $type;
      protected $priority;
      protected $agentid;
      protected $antivirus_name;
      protected $antivirus_version;

      function __construct() {
         $this->type = -1;
         $this->priority = -1;
         $this->id = -1;
         $this->obj = "";
         $this->agentid = -1;
         $this->antivirus_name = "";
         $this->antivirus_version = "";
      }

      function initFromForm($key, $type, $POST) {
         $this->agentid = PluginArmaditoToolbox::validateInt($key);
         $this->type = $type;
         $this->setPriority($POST["job_priority"]);
         $this->setAntivirusFromDB();

         // init Scan Obj for example or an other job_type
         $this->initObjFromForm($key, $type, $POST);
      }

      function initFromDB($data) {
         $this->id = $data["id"];
         $this->agentid = $data["plugin_armadito_agents_id"];
         $this->type = $data["job_type"];
         $this->antivirus_name = $data["antivirus_name"];
         $this->antivirus_version = $data["antivirus_version"];
         $this->priority = $data["job_priority"];
         $this->status = $data["job_status"];

         // init Scan Obj for example or an other job_type
         $this->initObjFromDB();
      }

      function getAntivirusName(){
         return $this->antivirus_name;
      }

      function getAntivirusVersion(){
         return $this->antivirus_version;
      }

      function getId(){
         return $this->id;
      }

      static function getDefaultDisplayPreferences(){
          $prefs = "";
          $nb_columns = 8;
          for( $i = 1; $i <= $nb_columns; $i++){
               $prefs .= "(NULL, 'PluginArmaditoJob', '".$i."', '".$i."', '0'),";
          }
          return $prefs;
      }

      function initObjFromForm ($key, $type, $POST){
         switch($this->type){
            case "scan":
               $this->obj = new PluginArmaditoScan();
               $this->obj->initFromForm($this, $POST);
               break;
            default:
               $this->obj = "unknown";
               break;
         }
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

      function setAntivirusFromDB(){
         global $DB;
         $query = "SELECT `antivirus_name`, `antivirus_version` FROM `glpi_plugin_armadito_agents`
                 WHERE `id`='".$this->agentid."'";

         $ret = $DB->query($query);

         if(!$ret){
            throw new Exception(sprintf('Error setAntivirusFromDB : %s', $DB->error()));
         }

         if($DB->numrows($ret) > 0){
            $data = $DB->fetch_assoc($ret);
            $this->antivirus_name = $data["antivirus_name"];
            $this->antivirus_version = $data["antivirus_version"];
         }
      }

      function getSearchOptions() {

         $tab = array();
         $tab['common'] = __('Scan', 'armadito');

         $i = 1;

         $tab[$i]['table']     = $this->getTable();
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
         $tab[$i]['field']     = 'job_type';
         $tab[$i]['name']      = __('Job Type', 'armadito');
         $tab[$i]['datatype']  = 'text';
         $tab[$i]['massiveaction'] = FALSE;

         $i++;

         $tab[$i]['table']     = $this->getTable();
         $tab[$i]['field']     = 'job_priority';
         $tab[$i]['name']      = __('Job Priority', 'armadito');
         $tab[$i]['datatype']  = 'text';
         $tab[$i]['massiveaction'] = FALSE;

         $i++;

         $tab[$i]['table']     = $this->getTable();
         $tab[$i]['field']     = 'job_status';
         $tab[$i]['name']      = __('Job Status', 'armadito');
         $tab[$i]['datatype']  = 'text';
         $tab[$i]['massiveaction'] = FALSE;


         return $tab;
      }

      function toJson() {
         return '{"job_id": '.$this->id.',"job_type": "'.$this->type.'","job_priority": "'.$this->priority.'","antivirus_name": "'.$this->antivirus_name.'"}';
      }

      function setJobId($id) {
         $this->id = $id;
      }

      function getJobId(){
         return $this->id;
      }

      function insertInJobs() {
         global $DB;

         $error = new PluginArmaditoError();
         $query = "INSERT INTO `glpi_plugin_armadito_jobs` (`plugin_armadito_agents_id`, `job_type`, `job_priority`, `job_status`, `antivirus_name`, `antivirus_version`) VALUES (?,?,?,?,?,?)";
         $stmt = $DB->prepare($query);

         if(!$stmt) {
            $error->setMessage(1, 'Job insert preparation failed.');
            $error->log();
            return $error;
         }

         if(!$stmt->bind_param('isssss', $agent_id, $job_type, $job_priority, $job_status, $antivirus_name, $antivirus_version)) {
               $error->setMessage(1, 'Job insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
               $error->log();
               $stmt->close();
               return $error;
         }

         $agent_id = $this->agentid;
         $job_type = $this->type;
         $job_priority = $this->priority;
         $job_status = "queued"; # Step 1
         $antivirus_name = $this->antivirus_name;
         $antivirus_version = $this->antivirus_version;

         if(!$stmt->execute()){
            $error->setMessage(1, 'Job insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
         }

         $stmt->close();

         // We get job_id
         $result = $DB->query("SELECT LAST_INSERT_ID()");
         if($result){
            $data = $DB->fetch_array($result);
            $this->setJobId($data[0]);
         }
         else {
            $error->setMessage(1, 'Enrollment get agent_id failed.');
            $error->log();
            return $error;
         }

         $error->setMessage(0, 'Job insertion successful.');
         return $error;
      }

      function insertInJobsAgents() {
         global $DB;

         $error = new PluginArmaditoError();
         $query = "INSERT INTO `glpi_plugin_armadito_jobs_agents` (`job_id`, `agent_id`) VALUES (?,?)";
         $stmt = $DB->prepare($query);

         if(!$stmt) {
            $error->setMessage(1, 'Job insert preparation failed.');
            $error->log();
            return $error;
         }

         if(!$stmt->bind_param('ii', $job_id, $agent_id)) {
               $error->setMessage(1, 'Job insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
               $error->log();
               $stmt->close();
               return $error;
         }

         $job_id = $this->id;
         $agent_id = $this->agentid;

         if(!$stmt->execute()){
            $error->setMessage(1, 'Job insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
         }

         $error->setMessage(0, 'Job insertion successful.');
         $stmt->close();
         return $error;
      }

      function addJob() {
         $error = new PluginArmaditoError();
         $error = $this->insertInJobs();
         if($error->getCode() != 0){
            $error->log();
            return false;
         }

         $error = $this->insertInJobsAgents();
         if($error->getCode() != 0){
            $error->log();
            return false;
         }

         $error = $this->obj->addObj($this->id);
         if($error->getCode() != 0){
            $error->log();
            return false;
         }
         return true;
      }
}
?>
