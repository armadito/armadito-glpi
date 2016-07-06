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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

include_once("toolbox.class.php");

class PluginArmaditoSetup {
   
   // Uninstallation function
   static function uninstall() {
      global $DB;

         PluginArmaditoProfile::uninstallProfile();
         ProfileRight::deleteProfileRights(array('armadito:read'));

         $pfSetup  = new PluginFusioninventorySetup();
         $user     = new User();



         // Current version tables
         if (TableExists("glpi_plugin_armadito_configs")) {
            $query = "DROP TABLE `glpi_plugin_armadito_configs`";
            if(!PluginArmaditoToolbox::ExecQuery($query)){
               die();
            }
         }

         // Current version tables
         if (TableExists("glpi_plugin_armadito_armaditos")) {
            $query = "DROP TABLE `glpi_plugin_armadito_armaditos`";
            if(!PluginArmaditoToolbox::ExecQuery($query)){
               die();
            }
         }

         // erase user display preferences TOFIX
         // cleanDefaultDisplayPreferences();
   }
}
?>
