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

    static function canView()
    {
        if(static::getProfileRights() == 'w' || static::getProfileRights() == 'r')
        {
            return true;
        }

        return false;
    }

    static function canCreate()
    {
        if(static::getProfileRights() == 'w')
        {
            return true;
        }

        return false;
    }

    static function getProfileRights()
    {
        return $_SESSION["glpi_plugin_armadito_profile"]['armadito'];
    }

    function getSearchOptions()
    {
        $search_options = new PluginArmaditoSearchoptions('AVConfig');

        $items['Type']       = new PluginArmaditoSearchtext('type', $this->getTable());
        $items['Value']      = new PluginArmaditoSearchtext('value', $this->getTable());

        return $search_options->get($items);
    }

    function showExportForm($item_type)
    {
        global $CFG_GLPI;

        echo "<form method='GET' action='".$CFG_GLPI["root_doc"]."/front/report.dynamic.php'
            target='_blank'>";

        echo Html::hidden('item_type', array('value' => $item_type));

        PluginArmaditoToolbox::showOutputFormat();
        Html::closeForm();
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

    function addOrUpdateValueForAgent($type, $value, $agentid = -1)
    {
        if($this->isValueForAgentInDB($type, $agentid))
        {
            $this->updateValueInDB($type, $value, $agentid);
        }
        else
        {
            $this->insertValueInDB($type, $value, $agentid);
        }
    }

    function isValueEqualForAgentInDB($type, $value, $agentid = -1)
    {
        $data = $this->getValueForAgentInDB($type, $agentid);

        if(isset($data['value'])) {
            return ($data['value'] == $value);
        }

        return false;
    }

    function isValueForAgentInDB($type, $agentid = -1)
    {
        $data = $this->getValueForAgentInDB($type, $agentid);
        return isset($data['value']);
    }

    function getValueForAgentInDB($type, $agentid = -1)
    {
        global $DB;

        $query  = "SELECT value FROM `".$this->getTable()."`";
        $query .= $this->getWhereQueryForAgent($type, $agentid);

        $ret   = $DB->query($query);
        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error isValueForAgentInDB : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            return $DB->fetch_assoc($ret);
        }

        return "";
    }

    function getWhereQueryForAgent($type, $agentid)
    {
        global $DB;
        $antivirus_id = $this->antivirus->getId();

        return " WHERE `type`='". $DB->escape($type)."'".
               " AND `plugin_armadito_antiviruses_id`='". $DB->escape($antivirus_id)."'".
               " AND `plugin_armadito_agents_id`='". $DB->escape($agentid)."'";
    }

    function insertValueInDB($type, $value, $agentid = -1)
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams($agentid);
        $query = "NewConfigValue";

        if($agentid >= 0) {
            $query = "NewValue";
        }

        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query, $type, $value, $agentid);
        $dbmanager->executeQuery($query);
    }

    function updateValueInDB($type, $value, $agentid = -1)
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams($agentid);
        $where_params = "type";
        $query = "UpdateConfigValue";

        if($agentid >= 0)
        {
            $query = "UpdateValue";
            $where_params = array( "type",
                                   "plugin_armadito_antiviruses_id",
                                   "plugin_armadito_agents_id");
        }

        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, $where_params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query, $type, $value, $agentid);
        $dbmanager->executeQuery($query);
    }

    function rmValueFromDB($type, $value, $agentid = -1)
    {
        global $DB;
        $query  = "DELETE from `".$this->getTable()."`";
        $query .= $this->getWhereQueryForAgent($type, $agentid);

        $ret   = $DB->query($query);
        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error isValueForAgentInDB : %s', $DB->error()));
        }
    }

    function setCommonQueryParams($agentid = -1)
    {
        $params["value"]["type"]  = "s";
        $params["type"]["type"]   = "s";

        if($agentid >= 0) {
            $params['plugin_armadito_antiviruses_id']["type"] = "i";
            $params['plugin_armadito_agents_id']["type"] = "i";
        }

        return $params;
    }

    function setCommonQueryValues($dbmanager, $query, $type, $value, $agentid = -1)
    {
        $dbmanager->setQueryValue($query, "value", $value);
        $dbmanager->setQueryValue($query, "type", $type);

        if($agentid >= 0) {
            $dbmanager->setQueryValue($query, "plugin_armadito_antiviruses_id", $this->antivirus->getId());
            $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $agentid);
            PluginArmaditoLog::Verbose(" $query - $type = $value for agent ($agentid)");
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

   function showEntriesForAgent($agent_id, $antivirus_id)
   {
        $agent_entries  = $this->findEntries($agent_id, $antivirus_id);
        $table_header   = "Configuration specific to agent n° ". htmlspecialchars($agent_id);

        if($agent_id == 0) {
            $table_header = "Common configuration";
        }

        echo "<tr class='tab_bg_1'>";
        echo "<th colspan='2' >".$table_header."</th>";
        echo "</tr>";

        foreach ($agent_entries as $data) {
            echo "<tr class='tab_bg_1'>";
            echo "<td align='center' style='word-wrap: break-word; overflow-wrap: break-word;'>" . htmlspecialchars($data["type"]) . "</td>";
            echo "<td align='center' style='word-wrap: break-word; overflow-wrap: break-word;'>" . htmlspecialchars($data["value"]) . "</td>";
            echo "</tr>";
        }
   }

   function findEntries($agent_id, $antivirus_id)
   {
        global $DB;

        $query = "SELECT id, value, type FROM `".$this->getTable()."`
                 WHERE `plugin_armadito_agents_id`='" . $agent_id . "'
                 AND `plugin_armadito_antiviruses_id`='". $antivirus_id ."'";

        $data = array();
        if ($result = $DB->query($query)) {
            if($DB->numrows($result)) {
                while ($line = $DB->fetch_assoc($result)) {
                    $data[$line['id']] = $line;
                }
            }
        }

        return $data;
   }
}
?>
