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


class PluginArmaditoMenuEntry
{
    protected $name;
    protected $pic;
    protected $link;

    function __construct($name, $pic, $link)
    {
        $this->name = __($name, 'armadito');
        $this->pic = $this->getPluginPath() . "/pics/" . $pic;
        $this->link = $this->getPluginPath() . "/front/". $link;
    }

    function getPluginPath()
    {
        global $CFG_GLPI;
        return $CFG_GLPI['root_doc'] . "/plugins/armadito";
    }

    function display ($colspan, $submenu_width)
    {
        echo "<tr>";
        echo "<th>";
        echo "<img src='". $this->pic ."' width='16' height='16'/>";
        echo "</th>";
        echo "<th colspan='". $colspan ."' width='". $submenu_width ."' style='text-align: left'>";
        echo "<a href='".  $this->link ."'>". $this->name ."</a>";
        echo "</th>";
        echo "</tr>";
    }
}
