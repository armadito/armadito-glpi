<?php

/*
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

define("PLUGIN_ARMADITO_VERSION", "0.10.1");

function plugin_init_armadito()
{
    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['csrf_compliant']['armadito'] = TRUE;
    $debug_mode = getDebugmode();

    $Plugin   = new Plugin();
    if ($Plugin->isActivated('armadito'))
    {
        registerPluginArmaditoClasses();
        setPluginArmaditoHooks($debug_mode);
    }
}

function getDebugmode()
{
    if (!isset($_SESSION['glpi_use_mode'])) {
        return ($_SESSION['glpi_use_mode'] == Session::DEBUG_MODE);
    }

    return false;
}

function registerPluginArmaditoClasses()
{
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
    Plugin::registerClass('PluginArmaditoStateUpdateDetail');
}

function setPluginArmaditoHooks( $debug_mode )
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['use_massive_action']['armadito'] = 1;
    $PLUGIN_HOOKS['add_javascript']['armadito'] = array();
    $PLUGIN_HOOKS['add_css']['armadito']        = array();

    if (scriptEndsWith("menu.php") || scriptEndsWith("board.php") || scriptEndsWith("config.form.php"))
    {
        $PLUGIN_HOOKS['add_javascript']['armadito'][] = "js/stats" . ($debug_mode ? "" : ".min") . ".js";
        array_push($PLUGIN_HOOKS['add_javascript']['armadito'],
                   "vendor/mbostock/d3/d3" . ($debug_mode ? "" : ".min") . ".js",
                   "vendor/novus/nvd3/build/nv.d3" . ($debug_mode ? "" : ".min") . ".js");
    }

    if (Session::haveRight('plugin_armadito_configuration', READ) || Session::haveRight('profile', UPDATE)) {
        $PLUGIN_HOOKS['config_page']['armadito'] = 'front/config.form.php' . '?itemtype=pluginarmaditoconfig&glpi_tab=1';
    }

    $_SESSION["glpi_plugin_armadito_profile"]['armadito'] = 'w';
    if (isset($_SESSION["glpi_plugin_armadito_profile"])) {

        $PLUGIN_HOOKS['menu_toadd']['armadito']['plugins'] = 'PluginArmaditoMenu';
    }

    $PLUGIN_HOOKS['fusioninventory_inventory']['armadito']
     = array('PluginArmaditoAgentAssociation', 'fusionInventoryHook');

    $PLUGIN_HOOKS['item_purge']['armadito'] = array('PluginArmaditoAgent' => array('PluginArmaditoAgent', 'purgeHook'));
}

function scriptEndsWith($scriptname)
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
        'homepage' => 'http://armadito-glpi.readthedocs.io/en/dev/index.html',
        'minGlpiVersion' => '9.1'
    );
}

function plugin_armadito_check_prerequisites()
{
    if (!isset($_SESSION['glpi_plugins'])) {
        $_SESSION['glpi_plugins'] = array();
    }

    if (version_compare(GLPI_VERSION, '9.1', 'lt')) {
        echo __('Your GLPI version not compatible, require >= 9.1', 'armadito');
        return FALSE;
    }

    return true;
}

function plugin_armadito_check_config($verbose = false)
{
    return true;
}

// center all columns of plugin
function plugin_armadito_displayConfigItem($itemtype, $ID, $data, $num)
{
    return "align='center'";
}
?>
