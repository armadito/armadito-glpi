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
            if ($this->getValue($type) === NULL) {
                $this->addValue($type, $value);
            } else if ($update) {
                $this->updateValue($type, $value);
            }
        }
    }

    function updateValue($name, $value)
    {
        global $DB;
        $config = current($this->find("`type`='" . $DB->escape($name) . "'"));
        if (isset($config['id'])) {
            return $this->update(array(
                'id' => $config['id'],
                'value' => $value
            ));
        } else {
            return $this->add(array(
                'type' => $name,
                'value' => $value
            ));
        }
    }

    function addValue($name, $value)
    {
        $existing_value = $this->getValue($name);
        if (!is_null($existing_value)) {
            return $existing_value;
        } else {
            return $this->add(array(
                'type' => $name,
                'value' => $value
            ));
        }
    }

    function getValue($name)
    {
        global $DB, $PF_CONFIG;

        if (isset($PF_CONFIG[$name])) {
            return $PF_CONFIG[$name];
        }

        $config = current($this->find("`type`='" . $DB->escape($name) . "'"));
        if (isset($config['value'])) {
            return $config['value'];
        }

        return NULL;
    }

    function isActive($name)
    {
        $value = $this->getValue($name);
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
