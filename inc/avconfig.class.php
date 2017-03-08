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

class PluginArmaditoAVConfig extends PluginArmaditoCommonDBTM
{
    protected $id;
    protected $agentid;
    protected $agent;
    protected $obj;
    protected $antivirus;
    protected $details;
    protected $details_id;

    static function getTypeName($nb = 0)
    {
        return __('AVConfig', 'armadito');
    }

    function initFromJson($jobj)
    {
        $this->details = new PluginArmaditoAVConfigDetail();
        $this->details->initFromJson($jobj);

        $this->agentid = $jobj->agent_id;
        $this->agent = $this->details->getAgent();
        $this->antivirus = $this->agent->getAntivirus();
        $this->setObj($jobj->task->obj);
    }

    function setObj($obj)
    {
        $this->obj = new StdClass;
        $this->obj->realtime_status    = $this->setValueOrDefault($obj, "realtime_status", "string");
    }

    function toJson()
    {
        return '{}';
    }

    function run()
    {
        $this->details->run();
        $this->details_id = $this->getTableIdForAgentId("glpi_plugin_armadito_avconfigdetails");

        if ($this->isAVConfiginDB()) {
            $this->updateAVConfig();
        } else {
            $this->insertAVConfig();
        }
    }

    function getSearchOptions()
    {
        $search_options = new PluginArmaditoSearchoptions('AVConfig');

        $items['Agent Id']            = new PluginArmaditoSearchitemlink('id', 'glpi_plugin_armadito_agents', 'PluginArmaditoAgent');
        $items['Last AVConfig']       = new PluginArmaditoSearchtext('last_avconfig', $this->getTable());
        $items['Realtime Status']     = new PluginArmaditoSearchtext('realtime_status', $this->getTable());
        $items['Antivirus']           = new PluginArmaditoSearchitemlink('fullname', 'glpi_plugin_armadito_antiviruses', 'PluginArmaditoAntivirus');
        $items['Details']             = new PluginArmaditoSearchitemlink('id', 'glpi_plugin_armadito_avconfigdetails', 'PluginArmaditoAVConfigDetail');

        return $search_options->get($items);
    }

    function isAVConfiginDB()
    {
        global $DB;

        $query = "SELECT id FROM `glpi_plugin_armadito_avconfigs`
                 WHERE `plugin_armadito_agents_id`='" . $this->agentid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error isAVConfiginDB : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            return true;
        }

        return false;
    }

    function insertAVConfig()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $query = "NewAVConfig";

        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        PluginArmaditoToolbox::validateInt($this->id);
        $this->logNewItem();
    }

    function updateAVConfig()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $query = "UpdateAVConfig";

        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, "plugin_armadito_agents_id");
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);
    }

    function setCommonQueryParams()
    {
        $params["realtime_status"]["type"]                    = "s";
        $params["last_avconfig"]["type"]                      = "s";
        $params["plugin_armadito_antiviruses_id"]["type"]     = "i";
        $params["plugin_armadito_avconfigdetails_id"]["type"] = "i";
        $params["plugin_armadito_agents_id"]["type"]          = "i";
        return $params;
    }

    function setCommonQueryValues($dbmanager, $query)
    {
        $dbmanager->setQueryValue($query, "realtime_status", $this->obj->realtime_status);
        $dbmanager->setQueryValue($query, "last_avconfig", date("Y-m-d H:i:s", time()));
        $dbmanager->setQueryValue($query, "plugin_armadito_antiviruses_id", $this->antivirus->getId());
        $dbmanager->setQueryValue($query, "plugin_armadito_avconfigdetails_id", $this->details_id);
        $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $this->agentid);
        return $dbmanager;
    }
}
?>
