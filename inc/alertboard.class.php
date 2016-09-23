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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginArmaditoAlertBoard extends PluginArmaditoBoard
{

    function __construct()
    {
        //
    }

    /**
     * Display a board in HTML with JS libs (nvd3)
     *
     *@return nothing
     **/
    function displayBoard()
    {
        $restrict_entity = getEntitiesRestrictRequest(" AND", 'comp');

        echo "<table align='center'>";
        echo "<tr height='420'>";
        
        $this->addLastAlertsChart($restrict_entity);
        
        echo "</tr>";
        echo "</table>";
    }
    
    /**
     * Get data and display last alerts chart (bar)
     *
     *@return nothing
     **/
    function addLastAlertsChart($restrict_entity)
    {

        // Number of alerts in last hour, 6 hours, 24 hours
        $data = PluginArmaditoLastAlertStat::getLastHours();

        $bchart = new PluginArmaditoChartBar();
        $bchart->init('lastalerts', __('Virus alerts of last hours', 'armadito'), $data);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
    }
}
?>
