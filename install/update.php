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

function pluginArmaditoUpdate($current_version, $new_version, $migrationname = 'Migration')
{
    ini_set("max_execution_time", "0");
    ini_set("memory_limit", "-1");

    loadPluginClasses();

    $migration = new $migrationname($current_version);
    $migration->displayMessage("Migration Classname : " . $migrationname);
    $migration->displayMessage("Update of plugin Armadito");

    do_migration($migration, $current_version, $new_version);
}

function do_migration($migration, $from_version, $to_version)
{
    global $DB;

    # TODO : Validate versions

    $migration->displayMessage("Migration from ".$from_version." to ".$to_version);
    $DB_file = GLPI_ROOT . "/plugins/armadito/install/mysql/".$from_version."_to_".$to_version.".sql";

    if (!$DB->runFile($DB_file)) {
        $migration->displayMessage("Error on creation of tables in database");
    } else {
        $migration->displayMessage("Migration of plugin Armadito successful");
    }
}
?>
