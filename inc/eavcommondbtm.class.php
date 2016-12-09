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
        $selected = $this->getValueFromDB($type);

        if (isset($selected['id'])) {
            return $this->updateValueInDB($type, $value);
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

    function isActive($type)
    {
        $value = $this->getValue($type);
        if($value === NULL){
           return FALSE;
        }

        return $value;
    }

    function getValue($type)
    {
        global $PF_CONFIG;

        if (isset($PF_CONFIG[$type])) {
            return $PF_CONFIG[$type];
        }

        $selected = $this->getValueFromDB($type);

        if (isset($selected['value'])) {
            return $selected['value'];
        }

        return NULL;
    }

    function getValueFromDB($type)
    {
        global $DB;
        return current($this->find("`type`='" . $DB->escape($type) . "'"));
    }

    function isValueEqualForAgentInDB($type, $agentid = -1, $value)
    {
        global $DB;

        $query  = $this->getQueryForAgent($type, $agentid);
        $query .= " AND `value`='" . $DB->escape($value) . "'";

        return !empty($this->find($query));
    }

    function isValueForAgentInDB($type, $agentid = -1)
    {
        global $DB;

        $query = $this->getQueryForAgent($type, $agentid);

        return !empty($this->find($query));
    }

    function getQueryForAgent($type, $agentid)
    {
        global $DB;
        $antivirus_id = $this->antivirus->getId();

        return "`type`='". $DB->escape($type)."'".
               " AND `plugin_armadito_antiviruses_id`='". $DB->escape($antivirus_id)."'".
               " AND `plugin_armadito_agents_id`='". $DB->escape($agentid)."'";
    }

    function insertValueInDB($type, $value)
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $query = "NewConfigValue";

        if(isset($this->agentid))
        {
            $query = "NewValue";
        }

        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query, $type, $value);
        $dbmanager->executeQuery($query);
    }

    function updateValueInDB($type, $value)
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $where_params = "type";
        $query = "UpdateConfigValue";

        if(isset($this->agentid))
        {
            $query = "UpdateValue";
            $where_params = array( "plugin_armadito_antiviruses_id",
                                   "plugin_armadito_agents_id");
        }

        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, $where_params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query, $type, $value);
        $dbmanager->executeQuery($query);
    }

    function setCommonQueryParams()
    {
        $params["value"]["type"]  = "s";
        $params["type"]["type"]   = "s";

        if(isset($this->agentid) && isset($this->antivirus)) {
            $params['plugin_armadito_antiviruses_id']["type"] = "i";
            $params['plugin_armadito_agents_id']["type"] = "i";
        }

        return $params;
    }

    function setCommonQueryValues($dbmanager, $query, $type, $value)
    {
        $dbmanager->setQueryValue($query, "value", $value);
        $dbmanager->setQueryValue($query, "type", $type);

        if(($query == "UpdateValue") || ($type == "hasAVConfig")) {
            $dbmanager->setQueryValue($query, "plugin_armadito_antiviruses_id", $this->antivirus->getId());
            $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $this->agentid);
        }
        else if($query == "NewValue") {
            $dbmanager->setQueryValue($query, "plugin_armadito_antiviruses_id", $this->antivirus->getId());
            $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", 0);
        }

        return $dbmanager;
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
