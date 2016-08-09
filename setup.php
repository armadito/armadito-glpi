<?php

/**
   Copyright (C) 2016 Teclib'
   Copyright (C) 2010-2016 by the FusionInventory Development Team.

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

   $Plugin = new Plugin();
   $moduleId = 0;

   if ( isset($_SESSION['glpi_use_mode']) ) {
      $debug_mode = ($_SESSION['glpi_use_mode'] == Session::DEBUG_MODE);
   } else {
      $debug_mode = false;
   }


   if ($Plugin->isActivated('armadito')) {

      $types = array('Central', 'Computer', 'ComputerDisk',
                     'Preference', 'Profile');

      Plugin::registerClass('PluginArmaditoAgent',
                            array('addtabon'  => $types,
                                 'link_types' => true));

      Plugin::registerClass('PluginArmaditoState');
      Plugin::registerClass('PluginArmaditoStatedetails');

      /**
       * Load the relevant javascript/css files only on pages that need them.
       */
      $PLUGIN_HOOKS['add_javascript']['armadito'] = array();
      $PLUGIN_HOOKS['add_css']['armadito'] = array();
      if (strpos($_SERVER['SCRIPT_FILENAME'], "plugins/armadito") != false) {
         array_push(
            $PLUGIN_HOOKS['add_javascript']['armadito'],
            "lib/d3-3.4.3/d3".($debug_mode?"":".min").".js",
            "lib/nvd3/nv.d3".($debug_mode?"":".min").".js"
         );
      }

      if (script_endswith("menu.php") || script_endswith("board.php") ) {
            $PLUGIN_HOOKS['add_javascript']['armadito'][] = "js/stats".($debug_mode?"":".min").".js";
      }

      if (Session::haveRight('plugin_armadito_configuration', READ)
              || Session::haveRight('profile', UPDATE)) {// Config page
         $PLUGIN_HOOKS['config_page']['fusioninventory'] = 'front/config.form.php'.
                 '?itemtype=pluginarmaditoconfig&glpi_tab=1';
      }

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
   } else { // plugin not active
      // TODO
      // include_once(GLPI_ROOT.'/plugins/armadito/inc/module.class.php');
      // $moduleId = PluginArmaditoModule::getModuleId('armadito');
   }

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

// center all columns of plugin
function plugin_armadito_displayConfigItem($itemtype, $ID, $data, $num){
   return "align='center'";
}
?>
