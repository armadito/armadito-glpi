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

      static function getDefaultDisplayPreferences(){
          $prefs = "";
          $nb_columns = 8;
          for( $i = 1; $i <= $nb_columns; $i++){
               $prefs .= "(NULL, 'PluginArmaditoJob', '".$i."', '".$i."', '0'),";
          }
          return $prefs;
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

      function setJobId($id) {
         $this->id = $id;
      }

      function getJobId(){
         return $this->id;
      }

       // in Jobs table
      function insertInJobs() {
         global $DB;

         $error = new PluginArmaditoError();
         $query = "INSERT INTO `glpi_plugin_armadito_jobs` (`plugin_armadito_agents_id`, `job_type`, `job_priority`) VALUES (?,?)";
         $stmt = $DB->prepare($query);

         if(!$stmt) {
            $error->setMessage(1, 'Job insert preparation failed.');
            $error->log();
            return $error;
         }

         if(!$stmt->bind_param('iss', $agent_id, $job_type, $job_priority)) {
               $error->setMessage(1, 'Job insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
               $error->log();
               $stmt->close();
               return $error;
         }

         $agent_id = $this->agentid;
         $job_type = $this->type;
         $job_priority = $this->priority;

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

         return $error;
      }

      // in Jobs/Agents table
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

         $stmt->close();
         return $error;
      }

      function insertJob() {
         $error = new PluginArmaditoError();

         $error = $this->insertInJobs();
         $error = $this->insertInJobsAgents();

         $error->setMessage(0, 'Job successfully inserted.');
         return $error;
      }
}
?>
