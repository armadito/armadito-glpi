<?php

/*
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
require_once("classloader.php");

function pluginArmaditoGetCurrentVersion()
{
    global $DB;

    if (!TableExists("glpi_plugin_armadito_configs")) {
        return "0";
    }

    $query = "SELECT version FROM glpi_plugin_armadito_configs LIMIT 1";

    $data = array();
    if ($result = $DB->query($query) && $DB->numrows($result) == "1") {
        $data = $DB->fetch_assoc($result);
    }

    return $data['version'];
}

function pluginArmaditoUpdate($current_version, $migrationname = 'Migration')
{
    ini_set("max_execution_time", "0");
    ini_set("memory_limit", "-1");

    loadPluginClasses();

    $migration = new $migrationname($current_version);
    $migration->displayMessage("Migration Classname : " . $migrationname);
    $migration->displayMessage("Update of plugin Armadito");

    do_lastcontactstat_migration($migration);
}

function do_lastalertstat_migration($migration)
{
    if (!TableExists("glpi_plugin_armadito_lastalertstats")) {
        do_laststat_migration($migration,'glpi_plugin_armadito_lastcontactstats');
        PluginArmaditoLastAlertStat::init();
    }
}

function do_lastcontactstat_migration($migration)
{
    if (!TableExists("glpi_plugin_armadito_lastcontactstats")) {
        do_laststat_migration($migration,'glpi_plugin_armadito_lastalertstats');
        PluginArmaditoLastContactStat::init();
    }
}

function do_laststat_migration($migration, $table_name)
{
    $a_table            = array();
    $a_table['name']    = $table_name;
    $a_table['oldname'] = array();

    $a_table['fields']            = array();
    $a_table['fields']['id']      = array(
        'type' => "smallint(3) NOT NULL AUTO_INCREMENT",
        'value' => ''
    );
    $a_table['fields']['day']     = array(
        'type' => "smallint(3) NOT NULL DEFAULT '0'",
        'value' => ''
    );
    $a_table['fields']['hour']    = array(
        'type' => "tinyint(2) NOT NULL DEFAULT '0'",
        'value' => ''
    );
    $a_table['fields']['counter'] = array(
        'type' => 'integer',
        'value' => NULL
    );

    $a_table['oldfields'] = array();
    $a_table['renamefields'] = array();
    $a_table['keys'] = array();
    $a_table['oldkeys'] = array();

    migrateTablesArmadito($migration, $a_table);
}

?>
