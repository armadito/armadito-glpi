<?php 

/* This file is part of ArmaditoPlugin.

ArmaditoPlugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

ArmaditoPlugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with ArmaditoPlugin.  If not, see <http://www.gnu.org/licenses/>.

*/

// ----------------------------------------------------------------------
// Original Author of file: Valentin HAMON
// Purpose of file: 
// ----------------------------------------------------------------------


// Class of the defined type
class PluginArmaditoArmadito extends CommonDBTM {


   /**
   * Get name of this type
   *
   * @return text name of this type by language of the user connected
   *
   **/
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

   /**
    * @see CommonGLPI::getMenuName()
   **/
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

      $tab[2]['table']     = $this->getTable();
      $tab[2]['field']     = 'computers_id';
      $tab[2]['name']      = __('Inventory ID', 'armadito');

      $tab[3]['table']     = $this->getTable();
      $tab[3]['field']     = 'serial';
      $tab[3]['name']      = __('Serial Number', 'armadito');
      $tab[3]['datatype']  = 'text';

      $tab[4]['table']     = $this->getTable();
      $tab[4]['field']     = 'version_av';
     // $tab[4]['linkfield'] = 'version_av';
      $tab[4]['name']      = __('Armadito Version', 'armadito');
      $tab[4]['datatype']  = 'text';
      $tab[4]['massiveaction'] = FALSE;

      $tab[5]['table']     = $this->getTable();
      $tab[5]['field']     = 'version_agent';
     // $tab[5]['linkfield'] = 'version_agent';
      $tab[5]['name']      = __('Agent Version', 'armadito');
      $tab[5]['datatype']  = 'text';
      $tab[5]['massiveaction'] = FALSE;

      return $tab;
   }

   static function item_update_computer(Computer$item) {
      global $DB;

      if ($item->getFromDB($item->getID())) {
            Session::addMessageAfterRedirect("- ".$item->getField("name"), true);
      } 

      ## TODO if inventory_id already existing in base, update table of this id
      $query = "INSERT INTO `glpi_plugin_armadito_armaditos`
                       (`id`,`computers_id`, `name`, `serial`, `version_av`, `version_agent`)
                VALUES (NULL,".$item->getID().", '".$item->getField('name')."', '".$item->getField('serial')."','', '')";

      $DB->query($query) or die("error populate glpi_plugin_armadito ".$DB->error());

      Session::addMessageAfterRedirect("Add Armadito Computer Hook, NAME=".$item->getField('name')." ID=".$item->getID(), true);
      
      return true;
   }

}

?>
