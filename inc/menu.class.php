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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}


class PluginArmaditoMenu extends CommonGLPI
{
    const BOARD_PNG = 'menu_mini_stats.png';
    const LISTING_PNG = 'menu_mini_listings.png';

    static function getTypeName($nb = 0)
    {
        return 'Armadito';
    }

    static function canView()
    {
        $can_display = false;
        $profile     = new PluginArmaditoProfile();

        foreach ($profile->getAllRights() as $right) {
            if (Session::haveRight($right['field'], READ)) {
                $can_display = true;
                break;
            }
        }

        return $can_display;
    }

    static function canCreate()
    {
        return false;
    }

    static function getMenuName()
    {
        return 'Armadito';
    }

    static function getAdditionalMenuOptions()
    {
        $elements = array(
            'scanconfig' => 'PluginArmaditoScanConfig',
            'config' => 'PluginArmaditoConfig'
        );

        $options = array();

        $options['menu']['title'] = PluginArmaditoMenu::getTypeName();
        $options['menu']['page']  = PluginArmaditoMenu::getSearchURL(false);

        if (Session::haveRight('plugin_armadito_configuration', READ)) {
            $options['menu']['links']['config'] = PluginArmaditoConfig::getFormURL(false);
        }

        if (Session::haveRight('plugin_armadito_configuration', READ)) {
            $options['agent']['links']['config'] = PluginArmaditoConfig::getFormURL(false);
        }

        foreach ($elements as $type => $itemtype) {
            $options[$type]                    = array(
                'title' => $itemtype::getTypeName(),
                'page' => $itemtype::getSearchURL(false)
            );
            $options[$type]['links']['search'] = $itemtype::getSearchURL(false);
            if ($itemtype::canCreate()) {
                $options[$type]['links']['add'] = $itemtype::getFormURL(false);
            }

            if (Session::haveRight('plugin_fusioninventory_configuration', READ)) {
                $options[$type]['links']['config'] = PluginArmaditoConfig::getFormURL(false);
            }
        }
        return $options;
    }

    static function getAdditionalMenuContent()
    {
        return array();
    }

    static function displayHeader()
    {
        global $CFG_GLPI;

        echo "<center>";
        echo "<a href='http://github.com/armadito'>";
        echo "<img src='" . $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/armadito_header_logo.png' height='96' />";
        echo "</a>";
    }

    static function displayMenuHeader()
    {
        echo "<div align='center' style='height: 35px; display: inline-block; width: 100%; margin: 0 auto;'>";
        echo "<br \>";
        echo "<table width='100%'>";
        echo "<tr>";
        echo "<td align='center'>";
        echo "<table>";
        echo "<tr>";
    }

    static function displayMenuFooter()
    {
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";
        echo "<br/><br/><br/>";
    }

    static function displayGeneralMenu()
    {
        $submenu = new PluginArmaditoSubMenu('General');

        if (Session::haveRight('plugin_armadito_agents', READ))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Board', BOARD_PNG, 'menu.php');
            $menu_entries[] = new PluginArmaditoMenuEntry('Agents', LISTING_PNG, 'agent.php');
        }

        if (Session::haveRight('config', UPDATE)
         || Session::haveRight('plugin_armadito_configuration', UPDATE))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Configuration', 'menu_mini_settings.png', 'config.form.php');
        }

        $submenu->addEntries($menu_entries);
        $submenu->display();
    }

    static function displayStateMenu()
    {
        $submenu = new PluginArmaditoSubMenu('State');

        if (Session::haveRight('plugin_armadito_states', READ))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Board', BOARD_PNG, 'stateboard.php');
            $menu_entries[] = new PluginArmaditoMenuEntry('States', LISTING_PNG, 'state.php');
        }

        $submenu->addEntries($menu_entries);
        $submenu->display();
    }

    static function displayAlertsMenu()
    {
        $submenu = new PluginArmaditoSubMenu('Alerts');

        if (Session::haveRight('plugin_armadito_alerts', READ))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Board', BOARD_PNG, 'alertboard.php');
            $menu_entries[] = new PluginArmaditoMenuEntry('Alerts', LISTING_PNG, 'alert.php');
        }

        $submenu->addEntries($menu_entries);
        $submenu->display();
    }

    static function displayScansMenu()
    {
        $submenu = new PluginArmaditoSubMenu('Scans');

        if (Session::haveRight('plugin_armadito_scans', READ))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Board', BOARD_PNG, 'scanboard.php');
            $menu_entries[] = new PluginArmaditoMenuEntry('Scans', LISTING_PNG, 'scan.php');
        }

        if (Session::haveRight('plugin_armadito_scanconfigs', UPDATE))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Scan Configurations', LISTING_PNG, 'scanconfig.php');
        }

        $submenu->addEntries($menu_entries);
        $submenu->display();
    }

    static function displayJobsMenu()
    {
        $submenu = new PluginArmaditoSubMenu('Jobs');

        if (Session::haveRight('plugin_armadito_jobs', READ))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Board', BOARD_PNG, 'jobboard.php');
            $menu_entries[] = new PluginArmaditoMenuEntry('Jobs', LISTING_PNG, 'job.php');
        }

        $submenu->addEntries($menu_entries);
        $submenu->display();
    }


    static function displayMenu()
    {
        static::displayMenuHeader();
        static::displayGeneralMenu();
        static::displayStateMenu();
        static::displayAlertsMenu();
        static::displayScansMenu();
        static::displayJobsMenu();
        static::displayMenuFooter();
    }
}

?>
