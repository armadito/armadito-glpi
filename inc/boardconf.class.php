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


class PluginArmaditoBoardConf extends PluginArmaditoConf
{
    function showForm()
    {
        $paConfig = $this->initConfigForm([], __('Boards', 'armadito'));

        $h = htmlspecialchars($paConfig->getValue('colorpalette_h'));
        $s = htmlspecialchars($paConfig->getValue('colorpalette_s'));
        $v = htmlspecialchars($paConfig->getValue('colorpalette_v'));

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Color Palette Hue', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        echo "<input type='text' name='colorpalette_h' value='" . $h . "'/>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Color Palette Saturation', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        echo "<input type='text' name='colorpalette_s' value='" . $s . "'/>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Color Palette Value', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        echo "<input type='text' name='colorpalette_v' value='" . $v . "'/>";
        echo "</td>";
        echo "</tr>";

        $this->showBoardExample($h, $s, $v);

        $paConfig->showFormFooter();
    }

    function showBoardExample ($h, $s, $v)
    {
        $board = new PluginArmaditoExampleBoard();
        $board->setHue($h);
        $board->setSaturation($s);
        $board->setValue($v);
        $board->displayBoard();
    }
}
?>
