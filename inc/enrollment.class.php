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
 * Class managing Armadito devices' Enrollment
 **/
class PluginArmaditoEnrollment {
     protected $agentid;
     protected $jobj;

     function __construct($jobj) {

         $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
         $this->jobj = $jobj;

         PluginArmaditoToolbox::logIfExtradebug(
            'pluginArmadito-Enrollment',
            'New PluginArmaditoEnrollment object.'
         );
     }

    /**
    * Get agentId
    *
    * @return agentid
    **/
     function getAgentid(){
        return $this->agentid;
     }

    /**
    * Set agentId
    *
    * @return nothing
    **/
     function setAgentid($agentid_){
         $this->agentid = PluginArmaditoToolbox::validateInt($agentid_);
     }

    /**
    * Get enrollment json string
    *
    * @return a json string
    **/
     function toJson(){
       return '{"agent_id": '.$this->agentid.'}';
    }

    /**
    * Try to enroll
    *
    * @return PluginArmaditoError obj
    **/
     function run(){

         if($this->isAlreadyEnrolled()) {
            $error = $this->updateEnrolledDevice();
         }
         else {
            $error = $this->enrollNewDevice();
         }

         return $error;
     }

    /**
    * Check if device is already enrolled
    *
    * @return TRUE or FALSE
    **/
    function isAlreadyEnrolled(){
      global $DB;

      PluginArmaditoToolbox::validateInt($this->jobj->agent_id);

      $query = "SELECT last_contact FROM `glpi_plugin_armadito_agents`
                 WHERE `id`='".$this->jobj->agent_id."'";
      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error isAlreadyEnrolled : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         return true;
      }

      return false;
    }

    /**
    * Try to enroll a new Armadito device
    *
    * @return PluginArmaditoError obj
    **/
     function updateEnrolledDevice(){
         global $DB;

         $error = new PluginArmaditoError();

         $query = "UPDATE `glpi_plugin_armadito_agents`
                 SET `entities_id`=?,
                     `computers_id`=?,
                     `plugin_fusioninventory_agents_id`=?,
                     `device_id`=?,
                     `agent_version`=?,
                     `antivirus_name`=?,
                     `antivirus_version`=?,
                     `antivirus_state`=?,
                     `last_contact`=?,
		               `last_alert`=?
                  WHERE `id`=?";

         $stmt = $DB->prepare($query);

         if(!$stmt) {
            $error->setMessage(1, 'Enrollment insert preparation failed.');
            $error->log();
            return $error;
         }

         if(!$stmt->bind_param('iiisssssssi', $entities_id, $computers_id, $fusion_table_id, $fusion_device_id, $agent_version, $antivirus_name, $antivirus_version, $antivirus_state, $last_contact, $last_alert, $agent_id)) {
            $error->setMessage(1, 'Enrollment insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
          }

         # We set values
         $entities_id = 0;
         $computers_id = 0;
         $fusion_table_id = 0;
         $fusion_device_id = $this->jobj->fusion_id;
         $agent_version = $this->jobj->agent_version;
         $antivirus_name = $this->jobj->task->antivirus->name;
         $antivirus_version = $this->jobj->task->antivirus->version;
         $antivitus_state = "unknown";
         $last_contact = date("Y-m-d H:i:s", time());
         $last_alert = '1970-01-01 00:00:00';
         $agent_id = $this->jobj->agent_id;

         if(!$stmt->execute()){
            $error->setMessage(1, 'Enrollment insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
         }

         $stmt->close();

         PluginArmaditoToolbox::logIfExtradebug(
            'pluginArmadito-Enrollment',
            'ReEnroll Device with id '.$this->agentid
         );

         $error->setMessage(0, 'New device successfully re-enrolled.');

         return $error;
    }

    /**
    * Try to enroll a new Armadito device
    *
    * @return PluginArmaditoError obj
    **/
     function enrollNewDevice(){
         global $DB;

         $error = new PluginArmaditoError();

         $query = "INSERT INTO `glpi_plugin_armadito_agents`(`entities_id`, `computers_id`, `plugin_fusioninventory_agents_id`,`device_id`, `agent_version`, `antivirus_name`, `antivirus_version`, `antivirus_state`, `last_contact`, `last_alert`) VALUES (?,?,?,?,?,?,?,?,?,?)";

         $stmt = $DB->prepare($query);

         if(!$stmt) {
            $error->setMessage(1, 'Enrollment insert preparation failed.');
            $error->log();
            return $error;
         }

         if(!$stmt->bind_param('iiisssssss', $entities_id, $computers_id, $fusion_table_id, $fusion_device_id, $agent_version, $antivirus_name, $antivirus_version, $antivirus_state, $last_contact, $last_alert)) {
               $error->setMessage(1, 'Enrollment insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
               $error->log();
               $stmt->close();
               return $error;
         }

         # We set values
         $entities_id = 0;
         $computers_id = 0;
         $fusion_table_id = 0;
         $fusion_device_id = $this->jobj->fusion_id;
         $agent_version = $this->jobj->agent_version;
         $antivirus_name = $this->jobj->task->antivirus->name;
         $antivirus_version = $this->jobj->task->antivirus->version;
         $antivitus_state = "unknown";
         $last_contact = date("Y-m-d H:i:s", time());
         $last_alert = '1970-01-01 00:00:00';

         if(!$stmt->execute()){
            $error->setMessage(1, 'Enrollment insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
         }

         $stmt->close();

         $result = $DB->query("SELECT LAST_INSERT_ID()");
         if($result){
            $data = $DB->fetch_array($result);
            $this->setAgentid($data[0]);
         }
         else {
            $error->setMessage(1, 'Enrollment get agent_id failed.');
            $error->log();
            return $error;
         }

         PluginArmaditoToolbox::logIfExtradebug(
            'pluginArmadito-Enrollment',
            'Enroll new Device with id '.$this->agentid
         );

         $error->setMessage(0, 'New device successfully enrolled.');
         return $error;
     }
}
?>
