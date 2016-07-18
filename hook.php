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

include_once("inc/toolbox.class.php");

function setDefaultDisplayPreferences(){
   
    // Set preferences for search_options
   $query = "INSERT INTO `glpi_displaypreferences` (`id`, `itemtype`, `num`, `rank`,
                        `users_id`)
		      VALUES (NULL, 'PluginArmaditoAgent', '1', '1', '0'),
			     (NULL, 'PluginArmaditoAgent', '2', '2', '0'),
			     (NULL, 'PluginArmaditoAgent', '3', '3', '0'),
			     (NULL, 'PluginArmaditoAgent', '4', '4', '0'),
			     (NULL, 'PluginArmaditoAgent', '5', '5', '0'),
              (NULL, 'PluginArmaditoAgent', '6', '6', '0'),
			     (NULL, 'PluginArmaditoAgent', '7', '7', '0'),
			     (NULL, 'PluginArmaditoAgent', '8', '8', '0'),
			     (NULL, 'PluginArmaditoAgent', '9', '9', '0')     
   ";

   if(!PluginArmaditoToolbox::ExecQuery($query)){
      die();
   }
}

function cleanDefaultDisplayPreferences(){
   global $DB;

   $query = "DELETE FROM `glpi_displaypreferences`
      WHERE `itemtype`='PluginArmaditoAgent'";

   PluginArmaditoToolbox::ExecQuery($query);
}

function plugin_armadito_install() {
   global $DB;

   ProfileRight::addProfileRights(array('armadito:read'));

   ini_set("max_execution_time", "0");

   if (basename($_SERVER['SCRIPT_NAME']) != "cli_install.php") {
      Html::header(__('Setup'), $_SERVER['PHP_SELF'], "config", "plugins");
      $migrationname = 'Migration';
   } else {
      $migrationname = 'CliMigration';
   }

   require_once (GLPI_ROOT . "/plugins/armadito/install/update.php");
   $version_detected = pluginArmaditoGetCurrentVersion();

   if (
      isset($version_detected)
      AND (
         defined('FORCE_UPGRADE')
         OR (
            $version_detected != PLUGIN_ARMADITO_VERSION
            AND $version_detected!='0'
         )
      )
   ) {
      pluginArmaditoUpdate($version_detected, $migrationname);
   } else if ((isset($version_detected))
           && ($version_detected == PLUGIN_ARMADITO_VERSION)) {
         //Same version : Nothing to do
   } else {
      require_once (GLPI_ROOT . "/plugins/armadito/install/install.php");
      pluginArmaditoInstall(PLUGIN_ARMADITO_VERSION, $migrationname);
   }

   // erase user display preferences and set default instead
   cleanDefaultDisplayPreferences();
   setDefaultDisplayPreferences();

   return true;
}

function plugin_armadito_uninstall() {
   require_once(GLPI_ROOT . "/plugins/armadito/inc/setup.class.php");
   require_once(GLPI_ROOT . "/plugins/armadito/inc/profile.class.php");
   return PluginArmaditoSetup::uninstall();
}
?>
