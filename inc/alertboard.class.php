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

class PluginArmaditoAlertBoard extends PluginArmaditoBoard
{

    function __construct()
    {
    }

    function displayBoard()
    {
        $restrict_entity = getEntitiesRestrictRequest(" AND", 'comp');

        echo "<table align='center'>";
        echo "<tr height='420'>";

        $this->addAlertsByVirusNameChart($restrict_entity);
        $this->addLastAlertsChart($restrict_entity);
        $this->addAlertsByAntivirusChart($restrict_entity);

        echo "</tr>";
        echo "</table>";
    }

    function addLastAlertsChart($restrict_entity)
    {
        $data = PluginArmaditoLastAlertStat::getLastHours();

        $bchart = new PluginArmaditoChartBar();
        $bchart->init('lastalerts', __('Alerts of last hours', 'armadito'), $data);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
    }

    function addAlertsByVirusNameChart($restrict_entity)
    {
        $data = $this->getAlertsByVirusNameChartData();

        $hchart = new PluginArmaditoChartHalfDonut();
        $hchart->init('alerts_by_virusnames', __('Alerts by names', 'armadito'), $data, 370);

        echo "<td width='380'>";
        $hchart->showChart();
        echo "</td>";
    }

    function addAlertsByAntivirusChart($restrict_entity)
    {
        $data = $this->getAlertsByAntivirusChartData();

        $hchart = new PluginArmaditoChartHalfDonut();
        $hchart->init('alerts_by_antiviruses', __('Alerts by antiviruses', 'armadito'), $data, 370);

        echo "<td width='380'>";
        $hchart->showChart();
        echo "</td>";
    }

    function countAlertsForAV($AV_id)
    {
        return countElementsInTableForMyEntities('glpi_plugin_armadito_alerts', "`plugin_armadito_antiviruses_id`='" . $AV_id . "'");
    }

    function getAlertsByAntivirusChartData()
    {
        $AVs       = PluginArmaditoAntivirus::getAntivirusList();
        $colortbox = new PluginArmaditoColorToolbox();
        $palette   = $colortbox->getPalette(sizeof($AVs), 0.157079632679);

        $data = array();
        $i    = 0;
        foreach ($AVs as $id => $name) {
            $n_AVs  = $this->countAlertsForAV($id);
            if($n_AVs > 0){
                $data[] = array(
                    'key' => __($name, 'armadito') . ' : ' . $n_AVs,
                    'value' => $n_AVs,
                    'color' => $palette[$i]
                );
                $i++;
            }
        }

        return $data;
    }

    function getAlertsByVirusNameChartData()
    {
        $VirusNames = PluginArmaditoAlert::getVirusNamesList(5);
        $colortbox  = new PluginArmaditoColorToolbox();
        $palette    = $colortbox->getPalette(sizeof($VirusNames), 0.157079632679);

        $data = array();
        $i    = 0;
        foreach ($VirusNames as $name => $counter) {
            if($counter > 0){
                $data[] = array(
                    'key' => __($name, 'armadito'),
                    'value' => $counter,
                    'color' => $palette[$i]
                );
                $i++;
            }
        }

        return $data;
    }
}
?>
