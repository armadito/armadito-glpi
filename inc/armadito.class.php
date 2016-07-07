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

      $tab[1]['table']     = 'glpi_computers'; // $this->getTable();
      $tab[1]['field']     = 'name';
      $tab[1]['name']      = __('Name', 'armadito');
      $tab[1]['datatype']  = 'itemlink';
      $tab[1]['itemlink_type'] = 'Computer';
      $tab[1]['massiveaction'] = FALSE;

      $tab[2]['table']     = 'glpi_plugin_fusioninventory_agents'; // $this->getTable();
      $tab[2]['field']     = 'useragent';
      $tab[2]['name']      = __('Agent', 'armadito');
      $tab[2]['datatype']  = 'itemlink';
      $tab[2]['itemlink_type'] = 'PluginFusioninventoryAgent';
      $tab[2]['massiveaction'] = FALSE;

      $tab[3]['table']     = $this->getTable();
      $tab[3]['field']     = 'last_contact';
      $tab[3]['name']      = __('Last Contact', 'armadito');
      $tab[3]['datatype']  = 'text';
      $tab[3]['massiveaction'] = FALSE;

      $tab[4]['table']     = $this->getTable();
      $tab[4]['field']     = 'antivirus_version';
      // $tab[4]['linkfield'] = 'version_av';
      $tab[4]['name']      = __('Antivirus Version', 'armadito');
      $tab[4]['datatype']  = 'text';
      $tab[4]['massiveaction'] = FALSE;

      $tab[5]['table']     = $this->getTable();
      $tab[5]['field']     = 'agent_version';
      $tab[5]['name']      = __('Agent Version', 'armadito');
      $tab[5]['datatype']  = 'text';
      $tab[5]['massiveaction'] = FALSE;

      return $tab;
   }

   static function isAgentAlreadyInDB (PluginFusioninventoryAgent $item){
      global $DB;

      $query = "SELECT * FROM `glpi_plugin_armadito_armaditos`
                 WHERE `computers_id`='".$item->getField("computers_id")."'";
      $ret = $DB->query($query); 
       
      if(!$ret){
         PluginArmaditoToolbox::logE("Error isAgentAlreadyInDB : ".$DB->error());
         return false;
      }
           
      if($DB->numrows($ret) > 0){
         return true;
      } 
      
      return false;
   }

   static function insertAgentInDB (PluginFusioninventoryAgent $item){
      global $DB;

      $query = "INSERT INTO `glpi_plugin_armadito_armaditos`
                       (`id`, `entities_id`, `computers_id`, `plugin_fusioninventory_agents_id`, `version_av`, `version_agent`, `agent_port`, `device_id`, `last_contact` )
                VALUES (NULL, ".$item->getField("entities_id").", ".$item->getField("computers_id").", ".$item->getField("id").",'',
		'".$item->getField("version")."', '".$item->getField("agent_port")."', '".$item->getField("device_id")."', '".$item->getField("last_contact")."' )";

      if(PluginArmaditoToolbox::ExecQuery($query)){
         PluginArmaditoToolbox::logE("Agent ".$item->getField("id")." (c".$item->getField("computers_id").") successfully inserted in db.");
      }
   }

   static function updateAgentInDB (PluginFusioninventoryAgent $item){
      global $DB;

      $query = "UPDATE `glpi_plugin_armadito_armaditos`
                 SET `entities_id`=".$item->getField("entities_id").", 
                     `agent_port`='".$item->getField("agent_port")."',
                     `device_id`='".$item->getField("device_id")."',
                     `last_contact`='".$item->getField("last_contact")."',
                     `computers_id`=".$item->getField("computers_id").",
		     `version_agent`='".$item->getField("version")."'
               WHERE `plugin_fusioninventory_agents_id`=".$item->getField("id");

      if(PluginArmaditoToolbox::ExecQuery($query)){
         PluginArmaditoToolbox::logE("Agent ".$item->getField("id")." (c".$item->getField("computers_id").") successfully updated in db.");
      }
   }

   static function insertOrUpdateAgentInDB (PluginFusioninventoryAgent $item){
      global $DB;
   
      if(PluginArmaditoArmadito::isAgentAlreadyInDB($item)){
         PluginArmaditoArmadito::updateAgentInDB($item);
      }
      else{
         PluginArmaditoArmadito::insertAgentInDB($item);
      }
   }

   static function item_update_agent(PluginFusioninventoryAgent $item){

      if($item->getFromDB($item->getID())) {
         PluginArmaditoArmadito::insertOrUpdateAgentInDB($item);
      } 
      else{
         PluginArmaditoToolbox::logE("Error - Can't get PluginFusioninventoryAgent object fromDB.");
      }

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
