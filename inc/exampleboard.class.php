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

class PluginArmaditoExampleBoard extends PluginArmaditoBoard
{

    protected $hue;
    protected $saturation;
    protected $value;

    function __construct()
    {
    }

    function setHue($h)
    {
        $this->hue = $h;
    }

    function setSaturation($s)
    {
        $this->saturation = $s;
    }

    function setValue($v)
    {
        $this->value = $v;
    }

    function displayBoard()
    {
        echo "<table align='center'>";
        echo "<tr height='420'>";
        $this->addDonutChart();
        $this->addVerticalBarChart();
        echo "</tr>";
        echo "</table>";
    }

    function addDonutChart()
    {
        $params = array(
            "svgname" => 'donut_chart_example',
            "title"   => __('Donut Chart Preview', 'armadito'),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getDonutChartData()
        );

        $hchart = new PluginArmaditoChart("DonutChart", $params);

        echo "<td width='380'>";
        $hchart->showChart();
        echo "</td>";
    }

    function addVerticalBarChart()
    {
        $colortbox = new PluginArmaditoColorToolbox();

        $params = array(
            "svgname" => 'verticalbar_chart_example',
            "title"   => __('VerticalBar Chart Preview', 'armadito'),
            "palette" => $colortbox->getPalette(12),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getVerticalBarChartData()
        );

        $bchart = new PluginArmaditoChart("VerticalBarChart", $params);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
    }

    function getVerticalBarChartData()
    {
        $data        = array();
        $data['key'] = 'test';

        $fibonacci = array("0", "1", "1", "2", "3", "5", "8",
                           "13", "21", "34", "55", "89");

        for ($i = 11; $i >= 0; $i--) {
            $data['values'][] = array(
                'label' => 12-$i.'h',
                'value' => $fibonacci[$i]
            );
        }
        return $data;
    }

    function getDonutChartData()
    {
        $colortbox = new PluginArmaditoColorToolbox();
        $colortbox->setHue($this->hue);
        $colortbox->setSaturation($this->saturation);
        $colortbox->setValue($this->value);
        $palette   = $colortbox->getPalette(7);

        $data = array();
        for ($i = 0; $i < 7; $i++) {
            $data[] = array(
                'key'   => "KEY_".$i,
                'value' => 6-$i,
                'color' => $palette[$i]
            );
        }

        return $data;
    }
}
?>
