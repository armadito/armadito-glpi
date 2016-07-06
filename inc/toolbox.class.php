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
    * Integer validation
    */
   static function validateInt($var) {
      $ret = filter_var($var, FILTER_VALIDATE_INT);
      if ($ret != $var) {
         throw new Exception(sprintf('Invalid int : "%s"', $var));
      }
      return $ret;
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

}
?>
