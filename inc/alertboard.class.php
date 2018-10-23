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
        echo "<table align='center'>";
        echo "<tr height='420'>";
        $this->showAlertsByVirusNameChart();
        $this->showLastAlertsChart();
        $this->showAlertsByAntivirusChart();
        echo "</tr>";
        echo "</table>";
    }

    function showLastAlertsChart()
    {
        $colortbox = new PluginArmaditoColorToolbox();

        $params = array(
            "svgname"       => 'lastalerts',
            "title"         => __('Alerts of last hours', 'armadito'),
            "palette"       => $colortbox->getPalette(12),
            "width"         => 370,
            "height"        => 400,
            "data"          => PluginArmaditoLastAlertStat::getLastHours(12),
            "staggerLabels" => true
        );

        $bchart = new PluginArmaditoChart("VerticalBarChart", $params);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
    }

    function showAlertsByVirusNameChart()
    {
        $params = array(
            "svgname" => 'alerts_by_virusnames',
            "title"   => __('Alerts by names', 'armadito'),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getAlertsByVirusNameChartData()
        );

        $hchart = new PluginArmaditoChart("DonutChart", $params);

        echo "<td width='380'>";
        $hchart->showChart();
        echo "</td>";
    }

    function showAlertsByAntivirusChart()
    {
        $params = array(
            "svgname" => 'alerts_by_antiviruses',
            "title"   => __('Alerts by antiviruses', 'armadito'),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getAlertsByAntivirusChartData()
        );

        $hchart = new PluginArmaditoChart("DonutChart", $params);

        echo "<td width='380'>";
        $hchart->showChart();
        echo "</td>";
    }

    function getAlertsByAntivirusChartData()
    {
        $AVs       = PluginArmaditoAntivirus::getAntivirusList();
        $colortbox = new PluginArmaditoColorToolbox();
        $palette   = $colortbox->getPalette(sizeof($AVs));

        $data = array();
        $i    = 0;
        foreach ($AVs as $id => $name) {
            $n_AVs  = $this->countAlertsForAV($id);
            if($n_AVs > 0){
                $data[] = array(
                    'key' => __($name, 'armadito'),
                    'value' => $n_AVs,
                    'color' => $palette[$i]
                );
                $i++;
            }
        }

        return $data;
    }

    function countAlertsForAV($AV_id)
    {
        return countElementsInTableForMyEntities(
            'glpi_plugin_armadito_alerts',
            ['plugin_armadito_antiviruses_id' => $AV_id]
        );
    }

    function getAlertsByVirusNameChartData()
    {
        $VirusNames = PluginArmaditoAlert::getVirusNamesList(5);
        $colortbox  = new PluginArmaditoColorToolbox();
        $palette    = $colortbox->getPalette(sizeof($VirusNames));

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
