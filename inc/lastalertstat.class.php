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

class PluginArmaditoLastAlertStat extends CommonDBTM
{

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     **/
    static function getTypeName($nb = 0)
    {
        return "LastAlertStat";
    }


    static function init()
    {
        global $DB;

        for ($d = 1; $d <= 365; $d++) {
            for ($h = 0; $h < 24; $h++) {
                $query = "INSERT INTO `glpi_plugin_armadito_lastalertstats` " . "(`day`, `hour`) " . "VALUES ('" . $d . "', '" . $h . "')";
                $DB->query($query);
            }
        }
    }

    static function increment( $ISO8601DateTime )
    {
        global $DB;

        $dayOfYear = PluginArmaditoToolbox::getDayOfYearFromISO8601DateTime($ISO8601DateTime);
        $hour =  PluginArmaditoToolbox::getHourFromISO8601DateTime($ISO8601DateTime);

        $query = "UPDATE `glpi_plugin_armadito_lastalertstats` " . "SET `counter` = counter + 1 " . "WHERE `day`='" . $dayOfYear . "' " . "   AND `hour`='" . $hour . "'";
        $DB->query($query);
    }

    static function getLastHours($nb = 11)
    {
        global $DB;

        $a_counters        = array();
        $a_counters['key'] = 'test';

        $timestamp = date('U');
        for ($i = $nb; $i >= 0; $i--) {
            $timestampSearch        = $timestamp - ($i * 3600);
            $query                  = "SELECT * FROM `glpi_plugin_armadito_lastalertstats` " . "WHERE `day`='" . date('z', $timestampSearch) . "' " . "   AND `hour`='" . date('G', $timestampSearch) . "' " . "LIMIT 1";
            $result                 = $DB->query($query);
            $data                   = $DB->fetch_assoc($result);
            $a_counters['values'][] = array(
                'label' => date('G', $timestampSearch) . "" . __('h'),
                'value' => (int) $data['counter']
            );
        }
        return $a_counters;
    }
}

?>
