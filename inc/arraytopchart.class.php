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

class PluginArmaditoArrayTopChart
{
    protected $size;
    protected $data;

    function __construct($size)
    {
        $this->size = $size;
        $this->data = array();
    }

    function init()
    {
        for($i = 0; $i < $this->size; $i++) {
            $this->data[$i]['value'] = 0;
            $this->data[$i]['label'] = '';
        }
    }

    function toChartArray($key)
    {
        $data = array();
        $data['key'] = $key;
        for($i = 0; $i < $this->size; $i++) {

            $data['values'][] = array(
                'label' => $this->data[$i]['label'],
                'value' => $this->data[$i]['value']
            );
        }

        return $data;
    }

    function insertIfRelevant($value, $label)
    {
        for($i = 0; $i < $this->size; $i++)
        {
            if($value >= $this->data[$i]['value'])
            {
                $this->rotateArrayForInsertion($i);

                $this->data[$i]['value'] = $value;
                $this->data[$i]['label'] = $label;
                break;
            }
        }
    }

    function rotateArrayForInsertion($i_to_beinserted)
    {
         $rotated_array = array();

         for($i = 0; $i < $this->size; $i++)
         {
            if($i > $i_to_beinserted && $i > 0) {
                $rotated_array[$i] = $this->data[$i-1];
            }
            else{
                $rotated_array[$i] = $this->data[$i];
            }
         }

         $this->data = $rotated_array;
    }

}
