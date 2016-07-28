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
 * Class used for State Details
 **/
class PluginArmaditoStatedetail extends CommonDBTM {

   function __construct() {
      //
   }

   /**
   * Get name of this type
   *
   * @return text name of this type by language of the user connected
   *
   **/
   static function getTypeName($nb=0) {
      return __('Antivirus State', 'armadito');
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

   // unused
   static function getDefaultDisplayPreferences(){
       $prefs = "";
       $nb_columns = 7;
       for( $i = 1; $i <= $nb_columns; $i++){
            $prefs .= "(NULL, 'PluginArmaditoStateDetail', '".$i."', '".$i."', '0'),";
       }
       return $prefs;
   }

   // unused
	function getSearchOptions() {

      $tab = array();
      $tab['common'] = __('StateDetail', 'armadito');

      $i = 1;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'agent_id';
      $tab[$i]['name']      = __('Agent Id', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'module_name';
      $tab[$i]['name']      = __('Module Name', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'module_version';
      $tab[$i]['name']      = __('Module Version', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'module_update_status';
      $tab[$i]['name']      = __('Module Update Status', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'module_last_update';
      $tab[$i]['name']      = __('Module Last Update', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      return $tab;
   }

   function defineTabs($options=array()){

      $ong = array();
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('PluginArmaditoStateModule', $ong, $options);
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

      // get global state for agent_id
      $state = new PluginArmaditoState();
      $state->setAgentId($this->fields["agent_id"]);
      $state_id = $state->getTableIdForAgentId("glpi_plugin_armadito_states");

      PluginArmaditoToolbox::logE("state_id : ".$state_id."; agent_id : ".$this->fields["agent_id"]);

      $state->getFromDB($state_id);

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Name')." :</td>";
      echo "<td align='center'>";
      Html::autocompletionTextField($this,'name', array('size' => 40));
      echo "</td>";
      echo "<td>".__('Agent Id', 'armadito')."&nbsp;:</td>";
      echo "<td align='center'>";
      echo "<b>".htmlspecialchars($this->fields["agent_id"])."</b>";
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Antivirus Name', 'armadito')." :</td>";
      echo "<td align='center'>";
      echo "<b>".htmlspecialchars($state->fields["antivirus_name"])."</b>";
      echo "</td>";

      echo "<td>".__('Antivirus Version', 'armadito')."&nbsp;:</td>";
      echo "<td align='center'>";
      echo "".htmlspecialchars($state->fields["antivirus_version"])."";
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Antivirus On-access', 'armadito')." :</td>";
      echo "<td align='center'>";
      echo "".htmlspecialchars($state->fields["antivirus_realtime"])."";
      echo "</td>";

      echo "<td>".__('Antivirus Service', 'armadito')."&nbsp;:</td>";
      echo "<td align='center'>";
      echo "".htmlspecialchars($state->fields["antivirus_service"])."";
      echo "</td>";
      echo "</tr>";
   }
}
?>
