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

include_once("inc/toolbox.class.php");

define ("PLUGIN_ARMADITO_VERSION", "9.1+0.2");

// Init the hooks of the plugins -Needed
function plugin_init_armadito() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['armadito'] = TRUE;

   $types = array('Central', 'Computer', 'ComputerDisk',
                  'Preference', 'Profile'); 

   Plugin::registerClass('PluginArmaditoAgent',
                         array('addtabon'  => $types,
                              'link_types' => true));

   if (isset($_SESSION["glpiname"])) {
         if (strstr($_SERVER['SCRIPT_FILENAME'], '/front/')
              && !strstr($_SERVER['SCRIPT_FILENAME'], 'report.dynamic.php')) {
            register_shutdown_function('plugin_armadito_footer', $CFG_GLPI['root_doc']);
         }
   }

   // Profile definition
   $_SESSION["glpi_plugin_armadito_profile"]['armadito'] = 'w';
   if (isset($_SESSION["glpi_plugin_armadito_profile"])) { // Right set in change_profile hook

      $PLUGIN_HOOKS['menu_toadd']['armadito']['plugins'] = 'PluginArmaditoMenu';
   }

   // Add fusion plugin hook
   $PLUGIN_HOOKS['item_update']['armadito']  = array('PluginFusioninventoryAgent' => array('PluginArmaditoAgent',
                                                                           'item_update_agent'));
}

function plugin_version_armadito() {

   return array('name'           => 'Armadito',
                'shortname'      => 'armadito',
                'version'        => PLUGIN_ARMADITO_VERSION,
                'author'         => '<a href="mailto:armadito@teclib.com">Teclib\'</a>',
                'license'        => 'AGPLv3+',
                'homepage'       => 'https://github.com/armadito/',
                'minGlpiVersion' => '9.1');
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_armadito_check_prerequisites() {

   if (!isset($_SESSION['glpi_plugins'])) {
      $_SESSION['glpi_plugins'] = array();
   }

   if (version_compare(GLPI_VERSION, '9.1', 'lt') || version_compare(GLPI_VERSION, '9.2', 'ge')) {
      echo __('Your GLPI version not compatible, require >= 9.1', 'armadito');
      return FALSE;
   }

   return true;
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_armadito_check_config($verbose=false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      _e('Installed / not configured', 'armadito');
   }
   return false;
}

function plugin_armadito_footer($baseroot) {

      echo "<div id='footer'>";
      echo "<table width='100%'>";
      echo "<tr>";
      echo "<td class='right'>";
      echo "<a href='https://github.com/armadito/'>";
      echo "<span class='copyright'>Armadito ".PLUGIN_ARMADITO_VERSION." | copyleft ".
           "<img src='".$baseroot."/plugins/fusioninventory/pics/copyleft.png'/> "
              . " 2016 Teclib', 2010-2016 by FusionInventory Team".
           "</span>";
      echo "</a>";
      echo "</td>";
      echo "</tr>";
      echo "</table>";
      echo "</div>";
}
?>
