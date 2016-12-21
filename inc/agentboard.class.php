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

class PluginArmaditoAgentBoard extends PluginArmaditoBoard
{

    function __construct()
    {
    }

    function displayBoard()
    {
        echo "<table align='center'>";
        echo "<tr height='420'>";

        $this->showAntivirusChart();
        $this->showComputersChart();
        $this->showLastContactsChart();

        echo "</tr>";
        echo "</table>";
    }

    function showLastContactsChart()
    {
        $colortbox = new PluginArmaditoColorToolbox();

        $params = array(
            "svgname" => 'agentconnections',
            "title"   => __('Agent connections of last hours', 'armadito'),
            "palette" => $colortbox->getPalette(12),
            "width"   => 370,
            "height"  => 400,
            "data"    => PluginArmaditoLastContactStat::getLastHours(12)
        );

        $bchart = new PluginArmaditoChart("VerticalBarChart", $params);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
    }

    function showComputersChart()
    {
        $params = array(
            "svgname" => 'armaditocomputers',
            "title"   => __('Armadito computers', 'armadito'),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getComputersChartData()
        );

        $hchart = new PluginArmaditoChart("DonutChart", $params);

        echo "<td width='380'>";
        $hchart->showChart();
        echo "</td>";
    }

    function showAntivirusChart()
    {
        $params = array(
            "svgname" => 'antiviruses',
            "title"   => __('Antiviruses repartition', 'armadito'),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getAntivirusChartData()
        );

        $hchart = new PluginArmaditoChart("DonutChart", $params);

        echo "<td width='380'>";
        $hchart->showChart();
        echo "</td>";
    }

    function getComputersChartData()
    {
        global $DB;

        $restrict_entity = getEntitiesRestrictRequest(" AND", 'comp');

        $armaditoComputers  = 0;
        $query_ao_computers = "SELECT COUNT(comp.`id`) as nb_computers
                             FROM glpi_computers comp
                             LEFT JOIN glpi_plugin_armadito_agents ao_comp
                               ON ao_comp.`computers_id` = comp.`id`
                             WHERE comp.`is_deleted`  = '0'
                               AND comp.`is_template` = '0'
                               AND ao_comp.`id` IS NOT NULL
                               $restrict_entity";

        $res_ao_computers = $DB->query($query_ao_computers);
        if ($data_ao_computers = $DB->fetch_assoc($res_ao_computers)) {
            $armaditoComputers = $data_ao_computers['nb_computers'];
        }

        $allComputers = countElementsInTableForMyEntities('glpi_computers', "`is_deleted`='0' AND `is_template`='0'");

        $colortbox = new PluginArmaditoColorToolbox();
        $palette   = $colortbox->getPalette(2);

        $data   = array();
        $data[] = array(
            'key' => __('Armadito agents', 'armadito'),
            'value' => $armaditoComputers,
            'color' => $palette[0]
        );
        $data[] = array(
            'key' => __('Others', 'armadito'),
            'value' => ($allComputers - $armaditoComputers),
            'color' => $palette[1]
        );

        return $data;
    }

    function countAgentsForAV($AV_id)
    {
        return countElementsInTableForMyEntities('glpi_plugin_armadito_agents', "`plugin_armadito_antiviruses_id`='" . $AV_id . "'");
    }

    function getAntivirusChartData()
    {
        $AVs       = PluginArmaditoAntivirus::getAntivirusList();
        $colortbox = new PluginArmaditoColorToolbox();
        $palette   = $colortbox->getPalette(sizeof($AVs));

        $data = array();
        $i    = 0;
        foreach ($AVs as $id => $name) {
            $n_AVs  = $this->countAgentsForAV($id);
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
}
?>
