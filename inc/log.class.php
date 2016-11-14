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

class PluginArmaditoLog {

    const ERROR_LEVEL = 4;
    const WARNING_LEVEL = 3;
    const INFO_LEVEL = 2;
    const DEBUG_LEVEL = 1;
    const VERBOSE_LEVEL = 0;

    static function writeToLogFile( $label, $level, $text )
    {
        $paConfig = new PluginArmaditoConfig();
        $minlevel = $paConfig->getValue("debug_minlevel");

        if($level >= $minlevel ){
            return error_log(date("Y-m-d H:i:s") . " [". $label ."] " . $text . "\n", 3, GLPI_LOG_DIR . "/pluginArmadito.log");
        }

        return true;
    }

    static function Error($text)
    {
        return static::writeToLogFile("Error", self::ERROR_LEVEL, $text);
    }

    static function Warning($text)
    {
        return static::writeToLogFile("Warning", self::WARNING_LEVEL, $text);
    }

    static function Info($text)
    {
        return static::writeToLogFile("Info", self::INFO_LEVEL, $text);
    }

    static function Debug($text)
    {
        return static::writeToLogFile("Debug", self::DEBUG_LEVEL, $text);
    }

    static function Verbose($text)
    {
        return static::writeToLogFile("Verbose", self::VERBOSE_LEVEL, $text);
    }
}
?>
