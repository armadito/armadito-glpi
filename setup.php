<?php

// LICENSE HERE

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

// Init the hooks of the plugins -Needed
function plugin_init_armadito() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['armadito'] = TRUE;

   /* $types = array('Central', 'Computer', 'ComputerDisk', 'Notification', 'Phone',
                  'Preference', 'Profile', 'Supplier');

   Plugin::registerClass('ArmaditoMainClass',
                         array('addtabon'  => $types,
                              'link_types' => true));
   */  

}

function plugin_version_armadito() {

   return array('name'           => 'Plugin Armadito',
                'version'        => '0.1',
                'author'         => 'Valentin HAMON',
                'license'        => 'GPLv2+',
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
      _e('Installed / not configured', 'example');
   }
   return false;
}

?>
