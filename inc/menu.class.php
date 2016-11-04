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
        echo "<td>";
    }

    static function displayMenuFooter()
    {
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</div><br/><br/><br/>";
    }

    static function displayGeneralMenu($type = "big")
    {
        $submenu = new PluginArmaditoSubMenu('General', $type, $width_status);

        if (Session::haveRight('plugin_armadito_agents', READ))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Board', 'menu_stats.png', 'menu.php');
            $menu_entries[] = new PluginArmaditoMenuEntry('Agents', 'menu_listing.png', 'agent.php');
        }

        if (Session::haveRight('config', UPDATE)
         || Session::haveRight('plugin_armadito_configuration', UPDATE))
        {
            $menu_entries[] = new PluginArmaditoMenuEntry('Configuration', 'menu_settings.png', 'config.form.php');
        }

        $submenu->addEntries($menu_entries);
        $submenu->display();
    }

    static function displayMenu($type = "big")
    {
        global $CFG_GLPI;
        $width_status = 0;

        self::displayMenuHeader();
        self::displayGeneralMenu();

        /*
         * States
         */
        $a_menu = array();

        if (Session::haveRight('plugin_armadito_states', READ)) {

            $a_menu[] = array(
                'name' => __('Board', 'armadito'),
                'pic' => $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_stats.png",
                'link' => $CFG_GLPI['root_doc'] . "/plugins/armadito/front/stateboard.php"
            );

            $a_menu[1]['name'] = __('States', 'armadito');
            $a_menu[1]['pic']  = $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_listing.png";
            $a_menu[1]['link'] = $CFG_GLPI['root_doc'] . "/plugins/armadito/front/state.php";
        }

        if (!empty($a_menu)) {
            $width_status = PluginArmaditoMenu::htmlMenu(__('State', 'armadito'), $a_menu, $type, $width_status);
        }

        /*
         * Alerts
         */
        $a_menu = array();

        if (Session::haveRight('plugin_armadito_alerts', READ)) {

            $a_menu[] = array(
                'name' => __('Board', 'armadito'),
                'pic' => $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_stats.png",
                'link' => $CFG_GLPI['root_doc'] . "/plugins/armadito/front/alertboard.php"
            );

            $a_menu[1]['name'] = __('Alerts', 'armadito');
            $a_menu[1]['pic']  = $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_listing.png";
            $a_menu[1]['link'] = $CFG_GLPI['root_doc'] . "/plugins/armadito/front/alert.php";
        }

        if (!empty($a_menu)) {
            $width_status = PluginArmaditoMenu::htmlMenu(__('Alerts', 'armadito'), $a_menu, $type, $width_status);
        }

        /*
         * Scans
         */
        $a_menu = array();

        if (Session::haveRight('plugin_armadito_scans', READ)) {

            $a_menu[] = array(
                'name' => __('Board', 'armadito'),
                'pic' => $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_stats.png",
                'link' => $CFG_GLPI['root_doc'] . "/plugins/armadito/front/scanboard.php"
            );

            $a_menu[1]['name'] = __('Scans', 'armadito');
            $a_menu[1]['pic']  = $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_listing.png";
            $a_menu[1]['link'] = $CFG_GLPI['root_doc'] . "/plugins/armadito/front/scan.php";
        }

        if (Session::haveRight('plugin_armadito_scanconfigs', UPDATE)) {
            $a_menu[3]['name'] = __('Scan Configurations', 'armadito');
            $a_menu[3]['pic']  = $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_listing.png";
            $a_menu[3]['link'] = $CFG_GLPI['root_doc'] . "/plugins/armadito/front/scanconfig.php";
        }

        if (!empty($a_menu)) {
            $width_status = PluginArmaditoMenu::htmlMenu(__('Scans', 'armadito'), $a_menu, $type, $width_status);
        }

        /*
         * Jobs
         */
        $a_menu = array();

        if (Session::haveRight('plugin_armadito_jobs', READ)) {
            $a_menu[] = array(
                'name' => __('Board', 'armadito'),
                'pic' => $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_stats.png",
                'link' => $CFG_GLPI['root_doc'] . "/plugins/armadito/front/jobboard.php"
            );

            $a_menu[1]['name'] = __('Jobs', 'armadito');
            $a_menu[1]['pic']  = $CFG_GLPI['root_doc'] . "/plugins/armadito/pics/menu_listing.png";
            $a_menu[1]['link'] = $CFG_GLPI['root_doc'] . "/plugins/armadito/front/job.php";
        }

        if (!empty($a_menu)) {
            $width_status = PluginArmaditoMenu::htmlMenu(__('Jobs', 'armadito'), $a_menu, $type, $width_status);
        }

        self::displayMenuFooter();
    }

    static function htmlMenu($menu_name, $a_menu = array(), $type = "big", $width_status = '300')
    {
        global $CFG_GLPI;

        $width_max = 1250;
        $width = 180;

        if (($width + $width_status) > $width_max) {
            $width_status = 0;
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            echo "<table>";
            echo "<tr>";
            echo "<td valign='top'>";
        } else {
            echo "</td>";
            echo "<td valign='top'>";
        }
        $width_status = ($width + $width_status);

        echo "<table class='tab_cadre' style='position: relative; z-index: 30;'
         onMouseOver='document.getElementById(\"menu" . $menu_name . "\").style.display=\"block\"'
         onMouseOut='document.getElementById(\"menu" . $menu_name . "\").style.display=\"none\"'>";

        echo "<tr>";
        echo "<th colspan='" . count($a_menu) . "' nowrap width='" . $width . "'>
         <img src='" . $CFG_GLPI["root_doc"] . "/pics/deplier_down.png' />
         &nbsp;" . str_replace("Armadito ", "", $menu_name) . "&nbsp;
         <img src='" . $CFG_GLPI["root_doc"] . "/pics/deplier_down.png' />
      </th>";
        echo "</tr>";

        echo "<tr class='tab_bg_1' id='menu" . $menu_name . "' style='display:none; position: relative; z-index: 30;'>";
        echo "<td>";
        echo "<table>";
        foreach ($a_menu as $menu_id) {
            echo "<tr>";
            $menu_id['pic'] = str_replace("/menu_", "/menu_mini_", $menu_id['pic']);
            echo "<th>";
            if (!empty($menu_id['pic'])) {
                echo "<img src='" . $menu_id['pic'] . "' width='16' height='16'/>";
            }
            echo "</th>";
            echo "<th colspan='" . (count($a_menu) - 1) . "' width='" . ($width - 40) . "' style='text-align: left'>
                  <a href='" . $menu_id['link'] . "'>" . $menu_id['name'] . "</a></th>";
            echo "</tr>";
        }
        echo "</table>";

        echo "</td>";
        echo "</tr>";
        echo "</table>";

        return $width_status;
    }
}

?>
