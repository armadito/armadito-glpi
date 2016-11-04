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
        array_push($this->entries, $menu_entry);
    }

    function display()
    {
        if (!empty($this->entries)) {
            $this->displayHeader();
            $this->displayEntries();
            $this->displayFooter();
        }
    }

    function displayEntries()
    {
        $colspan = count($this->entries) - 1;
        $width = ($this->width - 40);

        foreach ($this->entries as $entry)
        {
            $entry->display($colspan, $width);
        }
    }

    function displayHeader()
    {
        echo "</td>";
        echo "<td valign='top'>";
        echo "<table class='tab_cadre' style='position: relative; z-index: 30;'
         onMouseOver='document.getElementById(\"menu" . $this->name . "\").style.display=\"block\"'
         onMouseOut='document.getElementById(\"menu" . $this->name . "\").style.display=\"none\"'>";

        echo "<tr>";
        echo "<th colspan='" . count($this->entries) . "' nowrap width='" . $this->width . "'>";
        $this->displayDeplierDown();
        echo "&nbsp;". $this->name ."&nbsp;";
        $this->displayDeplierDown();
        echo "</th>";
        echo "</tr>";

        echo "<tr class='tab_bg_1' id='menu". $this->name ."' style='display:none; position: relative; z-index: 30;'>";
        echo "<td>";
        echo "<table>";
    }

    function displayFooter()
    {
        echo "</table>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }

    function displayDeplierDown()
    {
        global $CFG_GLPI;
        echo "<img src='" . $CFG_GLPI["root_doc"] . "/pics/deplier_down.png' />";
    }
}
