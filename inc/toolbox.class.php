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

class PluginArmaditoToolbox
{
    static function logIfExtradebug($file, $message)
    {
        $config = new PluginArmaditoConfig();
        if ($config->getValue('extradebug')) {
            if (is_array($message)) {
                $message = print_r($message, TRUE);
            }
            Toolbox::logInFile($file, $message);
        }
    }

    static function ExecQuery($query)
    {
        global $DB;

        if ($DB->query($query)) {
            return true;
        } else {
            PluginArmaditoLog::Error($query ." :". $DB->error());
            return false;
        }
    }

    static function validateInt($var)
    {
        $ret = filter_var($var, FILTER_VALIDATE_INT);
        if ($ret != $var) {
            throw new InvalidArgumentException(sprintf('Invalid int : "%s"', $var));
        }
        return $ret;
    }

    static function validateHash($var)
    {
        $ret = filter_var($var, FILTER_VALIDATE_REGEXP, array(
            "options" => array(
                "regexp" => "/^[a-f0-9]{64}$/"
            )
        ));
        if ($ret != $var) {
            throw new InvalidArgumentException(sprintf('Invalid hash : "%s"', $var));
        }
        return $ret;
    }

    static function validateUUID($var)
    {
        $ret = filter_var($var, FILTER_VALIDATE_REGEXP, array(
            "options" => array(
                "regexp" => "/^[A-Z0-9]{8}\-[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{12}$/"
            )
        ));
        if ($ret != $var) {
            throw new InvalidArgumentException(sprintf('Invalid UUID : "%s"', $var));
        }
        return $ret;
    }

    static function isValidWin32Path($var)
    {
        return filter_var($var, FILTER_VALIDATE_REGEXP, array(
            "options" => array(
                "regexp" => "/^[A-Za-z]:\\[A-Za-z0-9 ]*$/"
            )
        ));
    }

    static function isValidUnixPath($var)
    {
        return filter_var($var, FILTER_VALIDATE_REGEXP, array(
            "options" => array(
                "regexp" => '/^\/[^\0]*$/'
            )
        ));
    }

    static function isValidBasicString($var)
    {
        return filter_var($var, FILTER_VALIDATE_REGEXP, array(
            "options" => array(
                "regexp" => '/^[\w-]*$/'
            )
        ));
    }

    static function parseJSON($json_content)
    {
        $jobj = json_decode($json_content);

        if (json_last_error() == JSON_ERROR_NONE) {
            return $jobj;
        } else {
            throw new PluginArmaditoJsonException(sprintf("Json parsing error : %s", json_last_error_msg()));
        }
    }

    static function showHours($name, $options = array())
    {
        $p['value']   = '';
        $p['display'] = true;
        $p['width']   = '80%';
        $p['step']    = 5;
        $p['begin']   = 0;
        $p['end']     = (24 * 3600);

        if (is_array($options) && count($options)) {
            foreach ($options as $key => $val) {
                $p[$key] = $val;
            }
        }

        if ($p['step'] <= 0) {
            $p['step'] = 5;
        }

        $values = array();
        $p['step'] = $p['step'] * 60;

        for ($s = $p['begin']; $s <= $p['end']; $s += $p['step']) {
            $values[$s] = PluginArmaditoToolbox::getHourMinute($s);
        }

        return Dropdown::showFromArray($name, $values, $p);
    }

    static function ISO8601DateTime_to_MySQLDateTime($ISO8601_datetime) {
        $datetime = new DateTime($ISO8601_datetime);
        return $datetime->format("Y-m-d H:i:s");
    }

    static function getDayOfYearFromISO8601DateTime($ISO8601_datetime) {
        $datetime = new DateTime($ISO8601_datetime);
        return $datetime->format("z");
    }

    static function getHourFromISO8601DateTime($ISO8601_datetime) {
        $datetime = new DateTime($ISO8601_datetime);
        return $datetime->format("G");
    }

    static function FormatISO8601DateInterval($ISO8601_dateinterval) {
        $interval = new DateInterval($ISO8601_dateinterval);
        return $interval->format('%dd %Hh %Im %Ss');
    }

    static function getHourMinute($seconds)
    {
        $hour   = floor($seconds / 3600);
        $minute = (($seconds - ((floor($seconds / 3600)) * 3600)) / 60);
        return sprintf("%02s", $hour) . ":" . sprintf("%02s", $minute);
    }
}
?>
