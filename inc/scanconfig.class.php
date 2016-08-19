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


   function __construct() {
      //
   }

	function initFromForm($POST) {
      if(!PluginArmaditoToolbox::isValidUnixPath($POST["scan_path"])){
         return "Invalid UNIX scan_path.";
      }

      $this->antivirus_name = $POST["antivirus_name"];
      $this->scan_name = $POST["scan_name"];
      $this->scan_path = $POST["scan_path"];
      $this->scan_options = $POST["scan_options"];
      return "";
	}

   function insertInDB() {
      global $DB;
      $error = new PluginArmaditoError();

      $query = "INSERT INTO `glpi_plugin_armadito_scanconfigs`
                           (`scan_name`,
                            `scan_path`,
                            `scan_options`,
                            `antivirus_name`) VALUES (?,?,?,?)";

      $stmt = $DB->prepare($query);

      PluginArmaditoToolbox::logE("insert new scanconfig in db.");

      if(!$stmt) {
         $error->setMessage(1, 'scanconfig insert preparation failed.');
         $error->log();
         return $error;
      }

      if(!$stmt->bind_param('ssss', $scan_name, $scan_path, $scan_options, $antivirus_name)) {
            $error->setMessage(1, 'scanconfig insert bin_param failed (' . $stmt->errno . ') ' . $stmt->error);
            $error->log();
            $stmt->close();
            return $error;
      }

      $scan_name = $this->scan_name;
      $scan_path = $this->scan_path;
      $scan_options = $this->scan_options;
      $antivirus_name = $this->antivirus_name;

      if(!$stmt->execute()){
         $error->setMessage(1, 'scanconfig insert execution failed (' . $stmt->errno . ') ' . $stmt->error);
         $error->log();
         $stmt->close();
         return $error;
      }

      $stmt->close();
      $error->setMessage(0, 'scanconfig successfully inserted.');
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

      return __('Scan configuration', 'armadito');
   }

  static function getDefaultDisplayPreferences(){
       $prefs = "";
       $nb_columns = 8;
       for( $i = 1; $i <= $nb_columns; $i++){
         $prefs .= "(NULL, 'PluginArmaditoScanConfig', '".$i."', '".$i."', '0'),";
       }
       return $prefs;
   }

	function getSearchOptions() {

      $tab = array();
      $tab['common'] = __('ScanConfig', 'armadito');

      $i = 1;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'id';
      $tab[$i]['name']      = __('Scan Config Id', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginArmaditoScanConfig';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'scan_name';
      $tab[$i]['name']      = __('Scan Name', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'scan_path';
      $tab[$i]['name']      = __('Scan Name', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'scan_options';
      $tab[$i]['name']      = __('Scan Name', 'armadito');
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

      return $tab;
   }

   static function getScanConfigsList() {
      global $DB;

      $configs = array();
      $query = "SELECT id, scan_name FROM `glpi_plugin_armadito_scanconfigs`";
      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error getScanConfigsList : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         while ($data = $DB->fetch_assoc($ret)) {
             $configs[$data['id']] = $data['scan_name'];
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
   function showForm($id, $options=array()) {
      global $CFG_GLPI;

      $this->initForm($id, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Scan name', 'armadito')." :</td>";
      echo "<td>";
      echo "<input type='text' name='scan_name' value='".htmlspecialchars($this->fields["scan_name"])."'/>";
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Antivirus', 'armadito')." :</td>";
      echo "<td>";
      echo "<input type='text' name='antivirus_name' value='".htmlspecialchars($this->fields["antivirus_name"])."'/>";
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Scan path', 'armadito')." :</td>";
      echo "<td>";
      echo "<input type='text' name='scan_path' value='".htmlspecialchars($this->fields["scan_path"])."'/>";
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Scan options', 'armadito')." :</td>";
      echo "<td>";
      echo "<input type='text' name='scan_options' value='".htmlspecialchars($this->fields["scan_options"])."'/>";
      echo "</td>";
      echo "</tr>";

      $this->showFormButtons($options);
      return true;
   }

   static function showNoScanConfigForm(){
      global $CFG_GLPI;

      if (Session::haveRight('plugin_armadito_scanconfigs', READ)) {
       $scanconfig_url = $CFG_GLPI['root_doc'].PluginArmaditoScanConfig::getFormURL(false);
       echo "<b>No scan configuration found in database.</b><br>";
       echo "<a href=\"".$scanconfig_url."\"> Add a new configuration</a>";
      }
   }
}
?>
