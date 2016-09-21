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

function pluginArmaditoInstall($version, $migrationname = 'Migration')
{
    global $DB;
    
    ini_set("memory_limit", "-1");
    ini_set("max_execution_time", "0");
    
    $migration = new $migrationname($version);
    
    /*
     * Load classes
     */
    foreach (glob(GLPI_ROOT . '/plugins/armadito/inc/*.php') as $file) {
        require_once($file);
    }
    
    $migration->displayMessage("Installation of plugin Armadito");
    
    
    /*
     * Manage profiles
     */
    $migration->displayMessage("Initialize profiles");
    PluginArmaditoProfile::initProfile();
    
    /*
     * Create DB structure
     */
    $migration->displayMessage("Creation of tables in database");
    $DB_file = GLPI_ROOT . "/plugins/armadito/install/mysql/plugin_armadito-empty.sql";
    if (!$DB->runFile($DB_file)) {
        $migration->displayMessage("Error on creation of tables in database");
    } else {
        $migration->displayMessage("Installation of plugin Armadito successful");
    }
    
    /*
     * Add config
     */
    $migration->displayMessage("Initialize configuration");
    $pfConfig = new PluginArmaditoConfig();
    $pfConfig->initConfigModule();
    
    
    PluginArmaditoLastContactStat::init();
}
?>
