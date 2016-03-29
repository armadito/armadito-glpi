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

   ProfileRight::addProfileRights(array('armadito:read'));

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

   
   if (!TableExists("glpi_plugin_armadito_armaditos")) {
      $query = "CREATE TABLE `glpi_plugin_armadito_armaditos` (
                  `id` int(11) NOT NULL auto_increment,
                  `name` varchar(255) collate utf8_unicode_ci default NULL,
                  `serial` varchar(255) collate utf8_unicode_ci NOT NULL,
                  `plugin_armadito_dropdowns_id` int(11) NOT NULL default '0',
                  `is_deleted` tinyint(1) NOT NULL default '0',
                  `is_template` tinyint(1) NOT NULL default '0',
                  `template_name` varchar(255) collate utf8_unicode_ci default NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

      $DB->query($query) or die("error creating glpi_plugin_armadito_armaditos ". $DB->error());

      $query = "INSERT INTO `glpi_plugin_armadito_armaditos`
                       (`id`, `name`, `serial`, `plugin_armadito_dropdowns_id`, `is_deleted`,
                        `is_template`, `template_name`)
                VALUES (1, 'armadito 1', 'serial 1', 1, 0, 0, NULL),
                       (2, 'armadito 2', 'serial 2', 2, 0, 0, NULL),
                       (3, 'armadito 3', 'serial 3', 1, 0, 0, NULL)";

      $DB->query($query) or die("error populate glpi_plugin_armadito ". $DB->error());
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
