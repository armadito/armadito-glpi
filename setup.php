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

include_once ("inc/armadito-includes.php");

// Init the hooks of the plugins -Needed
function plugin_init_armadito() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['armadito'] = TRUE;

   $types = array('Central', 'Computer', 'ComputerDisk',
                  'Preference', 'Profile'); 

   Plugin::registerClass('PluginArmaditoArmadito',
                         array('addtabon'  => $types,
                              'link_types' => true));

   // Definition du profil
   $_SESSION["glpi_plugin_armadito_profile"]['armadito'] = 'w';
   if (isset($_SESSION["glpi_plugin_armadito_profile"])) { // Right set in change_profile hook

      $PLUGIN_HOOKS['menu_toadd']['armadito'] = array('plugins' => 'PluginArmaditoArmadito');
   }

   // Ajout du hook de l'update d'un agent Fusion dans la bdd
   $PLUGIN_HOOKS['item_update']['armadito']  = array('PluginFusioninventoryAgent' => array('PluginArmaditoArmadito',
                                                                           'item_update_agent'));
}

function plugin_version_armadito() {

   return array('name'           => 'Plugin Armadito',
                'version'        => '0.1',
                'author'         => '<a href="mailto:vhamon@teclib.com">Valentin HAMON</a>',
                'license'        => 'GPLv3',
                'homepage'       => 'http://uhuru-am.com/en/',
                'minGlpiVersion' => '0.85');// For compatibility / no install in version < 0.80
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_armadito_check_prerequisites() {

   // Strict version check (could be less strict, or could allow various version)
   if (version_compare(GLPI_VERSION,'0.85','lt') /*|| version_compare(GLPI_VERSION,'0.84','gt')*/) {
      echo "This plugin requires GLPI >= 0.85";
      return false;
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

?>
