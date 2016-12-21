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


class PluginArmaditoJobConf extends PluginArmaditoConf
{
    function showForm()
    {
        $paConfig = $this->initConfigForm($options, __('Jobs configuration', 'armadito'));

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Maximum jobs agents can get by round', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        Dropdown::showNumber("getjobs_limit", array(
             'value' => $paConfig->getValue('getjobs_limit'),
             'min' => 1,
             'max' => 100)
         );
        echo "</td>";
        echo "</tr>";

        $paConfig->showFormFooter();
    }
}
?>
