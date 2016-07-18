<?php 

/**
   Copyright (C) 2010-2016 by the FusionInventory Development Team
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

class PluginArmaditoArmadito extends CommonDBTM {

   static function getTypeName($nb=0) {
      return __('Armadito', 'armadito');
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

   static function getMenuName() {
      return __('Plugin Armadito');
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if (!$withtemplate) {
         switch ($item->getType()) {
            case 'Profile' :
               if ($item->getField('central')) {
                  return __('Armadito', 'armadito');
               }
               break;

            case 'Phone' :
            case 'ComputerDisk' :
            case 'Supplier' :
            case 'Computer' :
	         return array(1 => __("Armadito AV", 'armadito'));
            case 'Central' :
            case 'Preference':
            case 'Notification':
		 return array(1 => __("Armadito Plugin", 'armadito'));

         }
      }
      return '';
   }

  static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      switch ($item->getType()) {
         case 'Phone' :
         case 'Central' :
            _e("Plugin central action", 'armadito');
            break;

         case 'Preference' :
            // Complete form display
            $data = plugin_version_armadito();

            echo "<form action='Where to post form'>";
            echo "<table class='tab_cadre_fixe'>";
            echo "<tr><th colspan='3'>".$data['name']." - ".$data['version'];
            echo "</th></tr>";

            echo "<tr class='tab_bg_1'><td>Name of the pref</td>";
            echo "<td>Input to set the pref</td>";

            echo "<td><input class='submit' type='submit' name='submit' value='submit'></td>";
            echo "</tr>";

            echo "</table>";
            echo "</form>";
            break;

	 case 'Computer' :
	    echo "Armadito AV inventory here";
	    echo "";
            break;
         case 'Notification' :
         case 'ComputerDisk' :
         case 'Supplier' :

         default :
            //TRANS: %1$s is a class name, %2$d is an item ID
            printf(__('Plugin armadito CLASS=%1$s id=%2$d', 'armadito'), $item->getType(), $item->getField('id'));
            break;
      }
      return true;
   }

   function getSearchOptions() {

      $tab = array();
      $tab['common'] = __('Armadito', 'armadito');

      $i = 1;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'id';
      $tab[$i]['name']      = __('Agent Id', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'agent_version';
      $tab[$i]['name']      = __('Agent Version', 'armadito');
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

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'antivirus_state';
      $tab[$i]['name']      = __('Antivirus State', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = 'glpi_plugin_fusioninventory_agents';
      $tab[$i]['field']     = 'device_id';
      $tab[$i]['name']      = __('Fusion Device ID', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginFusioninventoryAgent';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = 'glpi_computers';
      $tab[$i]['field']     = 'name';
      $tab[$i]['name']      = __('Inventory', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'Computer';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'last_contact';
      $tab[$i]['name']      = __('Last Contact', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      $i++;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'last_alert';
      $tab[$i]['name']      = __('Last Alert', 'armadito');
      $tab[$i]['datatype']  = 'text';
      $tab[$i]['massiveaction'] = FALSE;

      return $tab;
   }

   static function item_update_agent(PluginFusioninventoryAgent $item){
      // call when new inventory 
      return true;
   }

   static function showArmaditoHeader() {
      global $CFG_GLPI;

      echo "<center>";
      echo "<a href='http://github.com/armadito'>";
      echo "<img src='".$CFG_GLPI['root_doc']."/plugins/armadito/pics/armadito_header_logo.png' height='96' />";
      echo "</a>";
   }
}

?>
