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

class PluginArmaditoScanBoard extends PluginArmaditoBoard
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
        $data            = $this->getScanStatusData($restrict_entity);

        echo "<table align='center'>";
        echo "<tr height='420'>";
        $this->showScanStatusChart($data);
        echo "</tr>";
        echo "</table>";
    }

    /**
     * Get data and display Scans' statuses (half donut)
     *
     *@return nothing
     **/
    function showScanStatusChart($data)
    {

        $chart = new PluginArmaditoChartHalfDonut();
        $chart->init('scanstatus', "Scan(s) statuses", $data);

        echo "<td width='380'>";
        $chart->showChart();
        echo "</td>";
    }

    function countScanStatus($status)
    {
        return countElementsInTableForMyEntities('glpi_plugin_armadito_jobs', "`job_status`='" . $status . "' AND `job_type`='Scan'");
    }

    function getScanStatusData($restrict_entity)
    {
        global $DB;

        $statuses = PluginArmaditoJob::getAvailableStatuses();

        $data = array();
        foreach ($statuses as $name => $color) {
            $n_status = $this->countScanStatus($name);
            $data[]   = array(
                'key' => __($name, 'armadito') . ' : ' . $n_status,
                'y' => $n_status,
                'color' => $color
            );
        }

        return $data;
    }
}
?>
