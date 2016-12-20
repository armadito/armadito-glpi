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

class PluginArmaditoChartVerticalBar extends PluginArmaditoChart
{
    protected $palette;

    function __construct()
    {
    }

    function setPalette($palette)
    {
        $this->palette = $palette;
    }

    function showChart()
    {
        parent::showBackground();
        echo "<script>A6oVerticalBar('" . $this->name . "',
                    '" . json_encode($this->data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . "',
                    '" . $this->title . "',
                    '" . ($this->width - 30) . "',
                    '" . json_encode($this->palette). "');</script>";
    }
}
?>
