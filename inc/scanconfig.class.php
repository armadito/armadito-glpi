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

class PluginArmaditoScanConfig extends CommonDBTM {

    protected $id;
    protected $scan_type;
    protected $scan_path;
    protected $scan_options;
    protected $antivirus_name;
    protected $antivirus_version;

   function __construct() {
      //
   }

	function initFromForm($jobobj, $POST) {
      $this->setScanType($POST["scan_type"]);
      $this->antivirus_name = $jobobj->getAntivirusName();
      $this->antivirus_version = $jobobj->getAntivirusVersion();
	}

   /**
   * Get Scan config from DB
   **/
	function initFromDB($scan_name) {
      global $DB;
      // TODO: validate scan_name against SQL injections

      $error = new PluginArmaditoError();
      $query = "SELECT * FROM `glpi_plugin_armadito_scanconfigs`
              WHERE `scan_name`='".$scan_name."'";

      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error scanconfig initFromDB : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){

         if($data = $DB->fetch_assoc($ret)){
            $this->scan_path = $data["scan_path"];
            $this->scan_options = $data["scan_options"];
            $this->antivirus_name = $data["antivirus_name"];
            $this->antivirus_version = $data["antivirus_version"];
            $error->setMessage(0, 'Successfully loade scanconfig from DB.');
         }
      }

      $error->setMessage(1, 'No scanconfig '.$scan_name.' found in DB');
      return $error;
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

   /**
    * Display name of itemtype
    *
    * @return value name of this itemtype
    **/
   static function getTypeName($nb=0) {

      return __('Scan configuration');
   }

   static function getScanConfigsList() {
      global $DB;

      $configs = array();
      $query = "SELECT DISTINCT scan_name FROM `glpi_plugin_armadito_scanconfigs`";
      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error getScanConfigsList : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         while ($data = $DB->fetch_assoc($ret)) {
             $configs[] =  $data['scan_name'];
         }
      }
      return $configs;
   }

   /**
   * Display form
   *
   * @return bool TRUE if form is ok
   *
   **/
   function showForm($options=array()) {
      global $CFG_GLPI;

      $this->showFormHeader($options);
   }

   static function showNoScanConfigForm(){
      global $CFG_GLPI;

      if (Session::haveRight('plugin_armadito_scanconfigs', READ)) {
       $scanconfig_url = $CFG_GLPI['root_doc'].PluginArmaditoScanConfig::getFormURL(false);
       echo "<b>No scan configuration found in database.</b><br>";
       echo "Please, add a new scan configuration from Scans/configuration menu.<br>";
       echo "<a href=\"".$scanconfig_url."\"> Add a new configuration</a>";
      }
   }

}
?>
