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


class PluginArmaditoSubMenu
{
    protected $name;
    protected $entries;
    protected $width;

    function __construct($name, $width = 180)
    {
        $this->name = __($name, 'armadito');
        $this->width = $width;
        $this->entries = array();
    }

    function addEntries($menu_entries)
    {
        foreach( $menu_entries as $entry )
        {
            $this->addEntry($entry);
        }
    }

    function addEntry($menu_entry)
    {
        array_push($this->entries, $menu_entry->get());
    }

    function display()
    {
        if (!empty($this->entries)) {
            $this->htmlMenu();
        }
    }

    function htmlMenu()
    {
        global $CFG_GLPI;
        $width = 180;

        echo "</td>";
        echo "<td valign='top'>";
        echo "<table class='tab_cadre' style='position: relative; z-index: 30;'
         onMouseOver='document.getElementById(\"menu" . $this->name . "\").style.display=\"block\"'
         onMouseOut='document.getElementById(\"menu" . $this->name . "\").style.display=\"none\"'>";

        echo "<tr>";
        echo "<th colspan='" . count($this->entries) . "' nowrap width='" . $this->width . "'>
         <img src='" . $CFG_GLPI["root_doc"] . "/pics/deplier_down.png' />
         &nbsp;" . str_replace("Armadito ", "", $this->name) . "&nbsp;
         <img src='" . $CFG_GLPI["root_doc"] . "/pics/deplier_down.png' />
      </th>";
        echo "</tr>";

        echo "<tr class='tab_bg_1' id='menu" . $this->name . "' style='display:none; position: relative; z-index: 30;'>";
        echo "<td>";
        echo "<table>";

        foreach ($this->entries as $menu_id) {
            echo "<tr>";
            $menu_id['pic'] = str_replace("/menu_", "/menu_mini_", $menu_id['pic']);
            echo "<th>";
            if (!empty($menu_id['pic'])) {
                echo "<img src='" . $menu_id['pic'] . "' width='16' height='16'/>";
            }
            echo "</th>";
            echo "<th colspan='" . (count($this->entries) - 1) . "' width='" . ($this->width - 40) . "' style='text-align: left'>
                  <a href='" . $menu_id['link'] . "'>" . $menu_id['name'] . "</a></th>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }

}
