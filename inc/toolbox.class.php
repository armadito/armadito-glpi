<?php 

/**
   Copyright (C) 2010-2016 by the FusionInventory Development Team.
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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Toolbox of various utility methods
 **/
class PluginArmaditoToolbox {

   /**
    * Log when extra-debug is activated
    */
   static function logIfExtradebug($file, $message) {
      $config = new PluginArmaditoConfig();
      //if ($config->getValue('extradebug')) {
         if (is_array($message)) {
            $message = print_r($message, TRUE);
         }
         Toolbox::logInFile($file, $message);
     //}
   }

   /**
    * Log error message
    */
   static function logE($text) {

      $ok = error_log(date("Y-m-d H:i:s")." ".$text."\n", 3, GLPI_LOG_DIR."/pluginArmadito.log");
      return $ok;
   }

   /**
    * Executes a query in database
    */
   static function ExecQuery ($query) {
      global $DB;

      if($DB->query($query)){
         return true;
      }
      else{
         PluginArmaditoToolbox::logE("Error $query :".$DB->error());
         return false;
      }
   }

   /**
    * Integer validation to avoid SQL injections
    */
   static function validateInt($var) {
      $ret = filter_var($var, FILTER_VALIDATE_INT);
      if ($ret != $var) {
         throw new Exception(sprintf('Invalid int : "%s"', $var));
      }
      return $ret;
   }

   /**
    * Checksum validation to avoid SQL injections
    */
   static function validateHash($var) {
      $ret = filter_var($var, FILTER_VALIDATE_REGEXP,
                           array("options" =>
                                array("regexp" => "/^[a-f0-9]{64}$/"))
                        );
      if ($ret != $var) {
         throw new Exception(sprintf('Invalid hash : "%s"', $var));
      }
      return $ret;
   }

   /**
    * Win32 path validation
    */
   static function isValidWin32Path($var) {
      $ret = filter_var($var, FILTER_VALIDATE_REGEXP,
                           array("options" =>
                                array("regexp" => "/^[A-Za-z]:\\[A-Za-z0-9 ]*$/"))
                        );
      return $ret;
   }

   /**
    * Unix path validation
    */
   static function isValidUnixPath($var) {
      $ret = filter_var($var, FILTER_VALIDATE_REGEXP,
                           array("options" =>
                                array("regexp" => '/^\/[^\0]*$/'))
                        );
      if ($ret != $var) {
         return false;
      }
      return true;
   }

   /**
    * Parse a json string given
    *
    * @param $message XML message
    *
    * @return null or jobj
    */
   static function parseJSON($json_content){

      $jobj = json_decode($json_content);

      if(json_last_error() == JSON_ERROR_NONE)
      {
         return $jobj;
      }
      else
      {
         return null;
      }
   }

   /**
    * Dropdown for display hours
    *
    * @return type
    */
   static function showHours($name, $options=array()) {

      $p['value']          = '';
      $p['display']        = true;
      $p['width']          = '80%';
      $p['step']           = 5;
      $p['begin']          = 0;
      $p['end']            = (24 * 3600);

      if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }

      if ($p['step'] <= 0) {
         $p['step'] = 5;
      }

      $values   = array();

      $p['step'] = $p['step'] * 60; // to have in seconds
      for ($s=$p['begin'] ; $s<=$p['end'] ; $s+=$p['step']) {
         $values[$s] = PluginArmaditoToolbox::getHourMinute($s);
      }
      return Dropdown::showFromArray($name, $values, $p);
   }

   /**
    * Get hour:minute from number of seconds
    */
   static function getHourMinute($seconds) {
      $hour = floor($seconds / 3600);
      $minute = (($seconds - ((floor($seconds / 3600)) * 3600)) / 60);
      return sprintf("%02s", $hour).":".sprintf("%02s", $minute);
   }

   /**
   *  Check plugin install and exit if it is not installed
   */
   static function checkPluginInstallation() {
      if (!class_exists("PluginArmaditoAgent")) {
         http_response_code(404);
         header("Content-Type: application/json");
         header("X-ArmaditoPlugin-Version: unknown");
         echo '{ "code": 1, "message": "Plugin Armadito is not installed." }';
         session_destroy();
         exit();
      }
   }
}
?>
