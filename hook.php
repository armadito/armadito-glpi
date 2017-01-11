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

include_once("inc/toolbox.class.php");

function setDefaultDisplayPreferences( $classes)
{
    $query = "INSERT INTO `glpi_displaypreferences` (`id`, `itemtype`, `num`, `rank`,
                        `users_id`) VALUES ";

    foreach ( $classes as $class => $rounds ){
        $query .= getDefaultDisplayPreferences($class, $rounds);
    }

    $query = rtrim($query, ",");

    if (!PluginArmaditoToolbox::ExecQuery($query)) {
        die();
    }
}

function getDefaultDisplayPreferences( $class, $rounds )
{
    $prefs      = "";
    for ($i = 1; $i <= $rounds; $i++) {
        $prefs .= "(NULL, '".$class."', '" . $i . "', '" . $i . "', '0'),";
    }
    return $prefs;
}

function cleanDefaultDisplayPreferences($class)
{
    $query = "DELETE FROM `glpi_displaypreferences`
      WHERE `itemtype`='".$class."'";

    PluginArmaditoToolbox::ExecQuery($query);
}

function cleanAllDisplayPreferences( $classes )
{
    foreach ( $classes as $class => $rounds ){
        cleanDefaultDisplayPreferences($class);
    }
}

function plugin_armadito_install()
{
    ini_set("max_execution_time", "0");

    if (basename($_SERVER['SCRIPT_NAME']) != "cli_install.php") {
        Html::header(__('Setup'), $_SERVER['PHP_SELF'], "config", "plugins");
        $migrationname = 'Migration';
    } else {
        $migrationname = 'CliMigration';
    }

    require_once(GLPI_ROOT . "/plugins/armadito/install/update.php");
    $version_detected = pluginArmaditoGetCurrentVersion();

    if (isset($version_detected) &&
        (defined('FORCE_UPGRADE') || ($version_detected != PLUGIN_ARMADITO_VERSION && $version_detected != '0'))) {
        pluginArmaditoUpdate($version_detected, $migrationname);
    } else if ((isset($version_detected)) && ($version_detected == PLUGIN_ARMADITO_VERSION)) {
        //Same version : Nothing to do
    } else {
        require_once(GLPI_ROOT . "/plugins/armadito/install/install.php");
        pluginArmaditoInstall(PLUGIN_ARMADITO_VERSION, $migrationname);
    }

    // erase user display preferences and set default instead
    $classes = array( 'PluginArmaditoAgent' => 10,
                      'PluginArmaditoJob' => 10,
                      'PluginArmaditoState' => 10,
                      'PluginArmaditoAlert' => 15,
                      'PluginArmaditoScan' => 10,
                      'PluginArmaditoScanConfig' => 10,
                      'PluginArmaditoStateDetail' => 10,
                      'PluginArmaditoAVConfig' => 10,
                      'PluginArmaditoAntivirus' => 10,
                      'PluginArmaditoEnrollmentKey' => 10
                     );

    cleanAllDisplayPreferences($classes);
    setDefaultDisplayPreferences($classes);

    return true;
}

function plugin_armadito_uninstall()
{
    require_once(GLPI_ROOT . "/plugins/armadito/inc/setup.class.php");
    require_once(GLPI_ROOT . "/plugins/armadito/inc/profile.class.php");
    return PluginArmaditoSetup::uninstall();
}

/**
 * Add massive actions to GLPI itemtypes
 */
function plugin_armadito_MassiveActions($type)
{
    $sep = MassiveAction::CLASS_ACTION_SEPARATOR;
    $ma = array();

    if($type == "Computer" && Session::haveRight('plugin_armadito_jobs', UPDATE)) {
        $ma["PluginArmaditoAgent".$sep."newscan"]
         = __('Armadito - New Scan', 'armadito');
    }

    return $ma;
}

?>
