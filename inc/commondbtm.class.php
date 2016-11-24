<?php

/*
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

include_once("toolbox.class.php");

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginArmaditoCommonDBTM extends CommonDBTM
{
    function __construct()
    {
    }

    static function canDelete()
    {
        return true;
    }

    static function canPurge()
    {
        return true;
    }

    static function canCreate()
    {
        if(static::getProfileRights() == 'w')
        {
            return true;
        }

        return false;
    }

    static function canView()
    {
        if(static::getProfileRights() == 'w' || static::getProfileRights() == 'r')
        {
            return true;
        }

        return false;
    }

    static function getProfileRights()
    {
        return $_SESSION["glpi_plugin_armadito_profile"]['armadito'];
    }

    function getDefaultValue($type)
    {
        $value = null;
        switch ($type) {
            case "duration":
                $value = "0:00:00";
            case "date":
                $value = "1970-01-01T00:00:00Z";
            case "string":
                $value = "n/a";
            case "integer":
                $value = "-1";
            default:
                $value = null;
        }

        return $value;
    }

    function setValueOrDefault($obj, $label, $type)
    {
        if(isset($obj->{$label})) {
            return $obj->{$label};
        }
        return $this->getDefaultValue($type);
    }

    function logNewItem()
    {
        $changes = array(0,"","");
        Log::history($this->id, $this->getType(), $changes, 0,
            Log::HISTORY_CREATE_ITEM);
    }
}
?>
