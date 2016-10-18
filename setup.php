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

define("PLUGIN_ARMADITO_VERSION", "9.1+0.2");

function plugin_init_armadito()
{
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['csrf_compliant']['armadito'] = TRUE;

    $Plugin   = new Plugin();
    $moduleId = 0;

    if (isset($_SESSION['glpi_use_mode'])) {
        $debug_mode = ($_SESSION['glpi_use_mode'] == Session::DEBUG_MODE);
    } else {
        $debug_mode = false;
    }

    if ($Plugin->isActivated('armadito')) {

        $types = array(
            'Central',
            'Preference',
            'Profile'
        );

        Plugin::registerClass('PluginArmaditoAgent', array(
            'addtabon' => $types,
            'link_types' => true
        ));

        Plugin::registerClass('PluginArmaditoState');
        Plugin::registerClass('PluginArmaditoStatedetails');

        $PLUGIN_HOOKS['add_javascript']['armadito'] = array();
        $PLUGIN_HOOKS['add_css']['armadito']        = array();
        if (strpos($_SERVER['SCRIPT_FILENAME'], "plugins/armadito") != false) {
            array_push($PLUGIN_HOOKS['add_javascript']['armadito'], "lib/d3-3.4.3/d3" . ($debug_mode ? "" : ".min") . ".js", "lib/nvd3/nv.d3" . ($debug_mode ? "" : ".min") . ".js");
        }

        if (script_endswith_("menu.php") || script_endswith_("board.php")) {
            $PLUGIN_HOOKS['add_javascript']['armadito'][] = "js/stats" . ($debug_mode ? "" : ".min") . ".js";
        }

        if (Session::haveRight('plugin_armadito_configuration', READ) || Session::haveRight('profile', UPDATE)) {
            $PLUGIN_HOOKS['config_page']['armadito'] = 'front/config.form.php' . '?itemtype=pluginarmaditoconfig&glpi_tab=1';
        }

        $_SESSION["glpi_plugin_armadito_profile"]['armadito'] = 'w';
        if (isset($_SESSION["glpi_plugin_armadito_profile"])) {

            $PLUGIN_HOOKS['menu_toadd']['armadito']['plugins'] = 'PluginArmaditoMenu';
        }

        $PLUGIN_HOOKS['fusioninventory_inventory']['armadito']
         = array('PluginArmaditoAgent', 'FusionInventoryHook');
    }
}

function script_endswith_($scriptname)
{
    return substr($_SERVER['SCRIPT_FILENAME'], -strlen($scriptname)) === $scriptname;
}

function plugin_version_armadito()
{

    return array(
        'name' => 'Armadito',
        'shortname' => 'armadito',
        'version' => PLUGIN_ARMADITO_VERSION,
        'author' => '<a href="mailto:armadito@teclib.com">Teclib\'</a>',
        'license' => 'AGPLv3+',
        'homepage' => 'https://github.com/armadito/',
        'minGlpiVersion' => '9.1'
    );
}

// Before install
function plugin_armadito_check_prerequisites()
{

    if (!isset($_SESSION['glpi_plugins'])) {
        $_SESSION['glpi_plugins'] = array();
    }

    if (version_compare(GLPI_VERSION, '9.1', 'lt') || version_compare(GLPI_VERSION, '9.2', 'ge')) {
        echo __('Your GLPI version not compatible, require >= 9.1', 'armadito');
        return FALSE;
    }

    return true;
}

function plugin_armadito_check_config($verbose = false)
{
    if (true) { // Your configuration check
        return true;
    }

    if ($verbose) {
        _e('Installed / not configured', 'armadito');
    }
    return false;
}

// center all columns of plugin
function plugin_armadito_displayConfigItem($itemtype, $ID, $data, $num)
{
    return "align='center'";
}
?>
