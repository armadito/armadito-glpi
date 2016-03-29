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

function plugin_armadito_install() {
   global $DB;

   // Création de la table uniquement lors de la première installation
   if (!TableExists("glpi_plugin_armadito_config")) {
        // Création de la table config
        $query = "CREATE TABLE `glpi_plugin_armadito_config` (
        `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `status` char(32) NOT NULL default '',
        `enabled` char(1) NOT NULL default '1'
        )ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $DB->query($query) or die($DB->error());
   }

   if (!TableExists("glpi_plugin_armadito_devices")) {
        // Création de la table config
        $query = "CREATE TABLE `glpi_plugin_armadito_devices` (
        `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `agent_id` char(32) NOT NULL default '',
        `alive` char(1) NOT NULL default '1'
        )ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $DB->query($query) or die($DB->error());
   }

    return true;
}

function plugin_armadito_uninstall() {
    global $DB;

    $tables = array("glpi_plugin_armadito_config", "glpi_plugin_armadito_devices");

    foreach($tables as $table) 
        {$DB->query("DROP TABLE IF EXISTS `$table`;");}

    return true;
}

?>
