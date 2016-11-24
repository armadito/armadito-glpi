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

class PluginArmaditoAlert extends PluginArmaditoCommonDBTM
{
    protected $jobj;
    protected $agentid;
    protected $agent;
    protected $detection_time;
    protected $antivirus;

    static $rightname = 'plugin_armadito_alerts';

    function initFromJson($jobj)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
        $this->agent   = new PluginArmaditoAgent();
        $this->agent->initFromDB($this->agentid);
        $this->jobj = $jobj;
        $this->antivirus = $this->agent->getAntivirus();
        $this->detection_time = PluginArmaditoToolbox::ISO8601DateTime_to_MySQLDateTime($this->jobj->task->obj->alert->detection_time->value);
    }

    function getDetectionTime()
    {
        return $this->detection_time;
    }

    function getAgentId()
    {
        return $this->agentid;
    }

    function getSearchOptions()
    {
        $search_options = new PluginArmaditoSearchoptions('Alert');

        $items['Agent Id']         = new PluginArmaditoSearchitemlink('id', 'glpi_plugin_armadito_agents', 'PluginArmaditoAgent');
        $items['Threat name']      = new PluginArmaditoSearchtext('name', $this->getTable());
        $items['Filepath']         = new PluginArmaditoSearchtext('filepath', $this->getTable());
        $items['Antivirus']        = new PluginArmaditoSearchitemlink('fullname', 'glpi_plugin_armadito_antiviruses', 'PluginArmaditoAntivirus');
        $items['Module']           = new PluginArmaditoSearchtext('module_name', $this->getTable());
        $items['Severity']         = new PluginArmaditoSearchtext('impact_severity', $this->getTable());
        $items['Detection Time']   = new PluginArmaditoSearchtext('detection_time', $this->getTable());

        return $search_options->get($items);
    }

    function insertAlert()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $query = "NewAlert";

        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        PluginArmaditoToolbox::validateInt($this->id);
        $this->logNewItem();
    }

    function setCommonQueryParams()
    {
        $params["plugin_armadito_agents_id"]["type"]       = "i";
        $params["plugin_armadito_antiviruses_id"]["type"]  = "i";
        $params["name"]["type"]                            = "s";
        $params["module_name"]["type"]                     = "s";
        $params["filepath"]["type"]                        = "s";
        $params["impact_severity"]["type"]                 = "s";
        $params["detection_time"]["type"]                  = "s";
        return $params;
    }

    function setCommonQueryValues( $dbmanager, $query )
    {
        $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query, "plugin_armadito_antiviruses_id", $this->antivirus->getId());
        $dbmanager->setQueryValue($query, "name", $this->jobj->task->obj->alert->module_specific->value);
        $dbmanager->setQueryValue($query, "filepath", $this->jobj->task->obj->alert->uri->value);
        $dbmanager->setQueryValue($query, "module_name", $this->jobj->task->obj->alert->module->value);
        $dbmanager->setQueryValue($query, "detection_time", $this->detection_time);
        $dbmanager->setQueryValue($query, "impact_severity", $this->jobj->task->obj->alert->level->value);
        return $dbmanager;
    }

    function updateAlertStat ()
    {
        PluginArmaditoLastAlertStat::increment($this->jobj->task->obj->alert->detection_time->value);
    }

    static function addVirusName ($VirusNames, $data)
    {
        if(!isset($VirusNames[$data['name']])) {
            $VirusNames[$data['name']] = 0;
        }

        $VirusNames[$data['name']]++;
        return $VirusNames;
    }

    static function getVirusNamesList( $max )
    {
        global $DB;

        $VirusNames   = array();
        $query = "SELECT name FROM `glpi_plugin_armadito_alerts`";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getVirusNamesList : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            while ($data = $DB->fetch_assoc($ret)) {
                $VirusNames = PluginArmaditoAlert::addVirusName($VirusNames, $data);
            }
        }

        return PluginArmaditoAlert::prepareVirusNamesListForDisplay($VirusNames, $max);
    }

    static function prepareVirusNamesListForDisplay( $VirusNames, $max )
    {
        arsort($VirusNames);

        $i = 0;
        $others = 0;
        foreach ($VirusNames as $name => $counter) {
            if($i > $max) {
                unset($VirusNames[$name]);
                $others++;
            }
            $i++;
        }

        if( $others > 0 ){
              $VirusNames["others"] = $others;
        }

        return $VirusNames;
    }
}
?>
