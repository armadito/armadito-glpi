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

class PluginArmaditoEAVCommonDBTM extends CommonDBTM
{
    function __construct()
    {
    }

    static function getProfileRights()
    {
        return $_SESSION["glpi_plugin_armadito_profile"]['armadito'];
    }

    function addValues($values, $update = TRUE)
    {
        foreach ($values as $type => $value) {
            $this->addOrUpdateValue($type, $value, $update);
        }
    }

    function addOrUpdateValue($type, $value, $update = TRUE)
    {
        if ($this->getValue($type) === NULL) {
            $this->addValue($type, $value);
        } else if ($update) {
            $this->updateValue($type, $value);
        }
    }

    function updateValue($type, $value)
    {
        global $DB;

        $query = "`type`='" . $DB->escape($type) . "'";
        $selected = current($this->find($query));

        if (isset($selected ['id'])) {
            return $this->updateValueInDB($selected ['id'], $value);
        } else {
            return $this->insertValueInDB($type, $value);
        }
    }

    function addValue($type, $value)
    {
        $existing_value = $this->getValue($type);
        if (!is_null($existing_value)) {
            return $existing_value;
        } else {
            return $this->insertValueInDB($type, $value);
        }
    }

    function insertValueInDB($type, $value)
    {
        $row = array(
            'type' => $type,
            'value' => $value
        );

        $this->add($row);
    }

    function updateValueInDB( $id, $value )
    {
        $row = array(
            'id' => $id,
            'value' => $value
        );

        $this->update($row);
    }

    function getValue($type)
    {
        global $DB, $PF_CONFIG;

        if (isset($PF_CONFIG[$type])) {
            return $PF_CONFIG[$type];
        }

        $query = "`type`='" . $DB->escape($type) . "'";
        $selected = current($this->find($query));

        if (isset($selected['value'])) {
            return $selected['value'];
        }

        return NULL;
    }

    function isActive($type)
    {
        $value = $this->getValue($type);
        if($value === NULL){
           return FALSE;
        }

        return $value;
    }

   function setAgentFromJson($jobj)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
        $this->agent   = new PluginArmaditoAgent();

        if(!$this->agent->isAgentInDB($jobj->uuid)){
            throw new InvalidArgumentException('UUID not found in database. This agent has to be re-enrolled.');
        }

        $this->agent->initFromDB($this->agentid);
    }
}
?>
