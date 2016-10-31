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

class PluginArmaditoLastContactStat extends CommonDBTM
{
    static function getTypeName($nb = 0)
    {
        return "LastContactStat";
    }

    static function init()
    {
        global $DB;

        for ($d = 1; $d <= 365; $d++) {
            for ($h = 0; $h < 24; $h++) {
                $query = "INSERT INTO `glpi_plugin_armadito_lastcontactstats` " . "(`day`, `hour`) " . "VALUES ('" . $d . "', '" . $h . "')";
                $DB->query($query);
            }
        }
    }

    static function increment()
    {
        global $DB;

        $query = "UPDATE `glpi_plugin_armadito_lastcontactstats` " . "SET `counter` = counter + 1 " . "WHERE `day`='" . date('z') . "' " . "   AND `hour`='" . date('G') . "'";
        $DB->query($query);
    }

    static function getLastHours($nbhours = 12)
    {
        global $DB;

        $a_counters        = array();
        $a_counters['key'] = 'test';
        $timestamp = date('U');

        for ($i = $nbhours-1; $i >= 0; $i--) {
            $timestampSearch        = $timestamp - ($i * 3600);
            $query                  = "SELECT * FROM `glpi_plugin_armadito_lastcontactstats` " . "WHERE `day`='" . date('z', $timestampSearch) . "' " . "   AND `hour`='" . date('G', $timestampSearch) . "' " . "LIMIT 1";
            $result                 = $DB->query($query);
            $data                   = $DB->fetch_assoc($result);
            $a_counters['values'][] = array(
                'label' => date('G', $timestampSearch) . "" . __('h'),
                'value' => (int) $data['counter'],
            );
        }
        return $a_counters;
    }
}

?>
