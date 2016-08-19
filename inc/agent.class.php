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

class PluginArmaditoAgent extends CommonDBTM {

   static function getTypeName($nb=0) {
      return __('Agent', 'armadito');
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
      return __('Armadito');
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

   static function getDefaultDisplayPreferences(){
       $prefs = "";
       $nb_columns = 10;
       for( $i = 1; $i <= $nb_columns; $i++){
            $prefs .= "(NULL, 'PluginArmaditoAgent', '".$i."', '".$i."', '0'),";
       }
       return $prefs;
   }

   function getSearchOptions() {

      $tab = array();
      $tab['common'] = __('Agent', 'armadito');

      $i = 1;

      $tab[$i]['table']     = $this->getTable();
      $tab[$i]['field']     = 'id';
      $tab[$i]['name']      = __('Agent Id', 'armadito');
      $tab[$i]['datatype']  = 'itemlink';
      $tab[$i]['itemlink_type'] = 'PluginArmaditoAgent';
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

      $tab[$i]['table']     = 'glpi_plugin_fusioninventory_agents';
      $tab[$i]['field']     = 'device_id';
      $tab[$i]['name']      = __('FusionInventory Id', 'armadito');
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

   /**
    * Massive action ()
    */
   function getSpecificMassiveActions($checkitem=NULL) {

      $actions = array();
      if (Session::haveRight("plugin_armadito_jobs", UPDATE)) {
         $actions[__CLASS__.MassiveAction::CLASS_ACTION_SEPARATOR.'newscan'] = __('Scan', 'armadito');
      }

      if (Session::haveRight("plugin_armadito_agents", UPDATE)) {
         $actions[__CLASS__.MassiveAction::CLASS_ACTION_SEPARATOR.'transfert'] = __('Transfer');
      }

      return $actions;
   }

   static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item,
                                                       array $ids) {

      $pfAgent = new self();

      switch ($ma->getAction()) {

         case 'transfert' :
            foreach ($ids as $key) {
               if ($pfAgent->getFromDB($key)) {
                  $input = array();
                  $input['id'] = $key;
                  $input['entities_id'] = $_POST['entities_id'];
                  if ($pfAgent->update($input)) {
                     //set action massive ok for this item
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
                  } else {
                     // KO
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
                  }
               }
            }
         return;

         case 'newscan' :
            foreach ($ids as $key) {
               $job = new PluginArmaditoJob();
               $job->initFromForm($key, "Scan", $_POST);
               if ($job->addJob()){
                  $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
               } else {
                  $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
               }
            }
         return;
      }

      return;
   }

  /**
    * @since version 0.85
    *
    * @see CommonDBTM::showMassiveActionsSubForm()
   **/
   static function showMassiveActionsSubForm(MassiveAction $ma) {

      switch ($ma->getAction()) {
         case 'transfert' :
            Dropdown::show('Entity');
            echo "<br><br>".Html::submit(__('Post'),
                                         array('name' => 'massiveaction'));
            return true;
         case 'newscan' :
            PluginArmaditoAgent::showNewScanForm();
            return true;
      }

      return parent::showMassiveActionsSubForm($ma);
   }

     /**
       *  Show complete form for a new on-demand scan
      **/
      static function showNewScanForm(){

         $configs = PluginArmaditoScanConfig::getScanConfigsList();
         if(empty($configs)){
             PluginArmaditoScanConfig::showNoScanConfigForm();
             return;
         }

         echo "<b> Scan Parameters </b><br>";
         echo "Configuration: ";
         Dropdown::showFromArray("scanconfig_id", $configs);
         # PluginArmaditoToolbox::showHours('beginhours', array('step' => 15));

         echo "<br><br><b> Job Parameters </b><br>";
         echo "Priority ";
         $array = array();
         $array[0] = "Low";
         $array[1] = "Medium";
         $array[2] = "High";
         $array[3] = "Urgent";
         Dropdown::showFromArray("job_priority", $array);
         echo "<br><br>".Html::submit(__('Post'),
                                      array('name' => 'massiveaction'));
      }

      function defineTabs($options=array()){

         $ong = array();
         $this->addDefaultFormTab($ong);
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

         echo "<tr class='tab_bg_1'>";
         echo "<td>".__('Agent Id', 'armadito')."&nbsp;:</td>";
         echo "<td align='center'>";
         echo "<b>".htmlspecialchars($this->fields["id"])."</b>";
         echo "</td>";
         echo "</tr>";
      }

      /**
      *  Get list of all Antiviruses managed
      **/
      static function getAntivirusList () {
         global $DB;

         $AVs = array();
         $query = "SELECT DISTINCT antivirus_name FROM `glpi_plugin_armadito_agents`";
         $ret = $DB->query($query);

         if(!$ret){
            throw new Exception(sprintf('Error getAntivirusList : %s', $DB->error()));
         }

         if($DB->numrows($ret) > 0){
            while ($data = $DB->fetch_assoc($ret)) {
                $AVs[] =  $data['antivirus_name'];
            }
         }
         return $AVs;
      }

}

?>
