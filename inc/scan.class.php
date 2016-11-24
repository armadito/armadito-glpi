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

class PluginArmaditoScan extends PluginArmaditoCommonDBTM
{
    protected $id;
    protected $agentid;
    protected $agent;
    protected $obj;
    protected $job;
    protected $scanconfigid;
    protected $scanconfigobj;

    static $rightname = 'plugin_armadito_scans';

    static function getTypeName($nb = 0)
    {
        return __('Scan', 'armadito');
    }

    function initFromForm($jobobj, $POST)
    {
        $this->agentid      = $jobobj->getAgentId();
        $this->scanconfigid = PluginArmaditoToolbox::validateInt($POST["scanconfig_id"]);
    }

    function initFromJson($jobj)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
        $this->setObj($jobj->task->obj);
    }

    function setObj($obj)
    {
        $this->obj = new StdClass;
        $this->obj->job_id = $obj->job_id;
        $this->obj->duration = $this->setValueOrDefault($obj, "duration", "duration");
        $this->obj->start_time = $this->setValueOrDefault($obj, "start_time", "date");
        $this->obj->end_time = $this->setValueOrDefault($obj, "end_time", "date");
        $this->obj->malware_count = $this->setValueOrDefault($obj, "malware_count", "integer");
        $this->obj->suspicious_count = $this->setValueOrDefault($obj, "suspicious_count", "integer");
        $this->obj->cleaned_count = $this->setValueOrDefault($obj, "cleaned_count", "integer");
        $this->obj->scanned_count = $this->setValueOrDefault($obj, "scanned_count", "integer");
        $this->obj->progress = $this->setValueOrDefault($obj, "progress", "integer");

        $this->obj->duration = PluginArmaditoToolbox::FormatDuration($this->obj->duration);
        $this->obj->start_time = date("Y-m-d H:i:s", strtotime($this->obj->start_time));
    }

    function initFromDB($job_id)
    {
        global $DB;
        $query = "SELECT * FROM `glpi_plugin_armadito_scans`
              WHERE `plugin_armadito_jobs_id`='" . $job_id . "'";

        $ret = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getJobs : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0 && $data = $DB->fetch_assoc($ret))
        {
            $this->agentid      = $data["plugin_armadito_agents_id"];
            $this->scanconfigid = $data["plugin_armadito_scanconfigs_id"];

            $this->scanconfigobj = new PluginArmaditoScanConfig();
            $this->scanconfigobj->initFromDB($this->scanconfigid);
        }
    }

    function toJson()
    {
        return $this->scanconfigobj->toJson();
    }

    function getSearchOptions()
    {
        $search_options = new PluginArmaditoSearchoptions('Scan');

        $items['Scan Id']        = new PluginArmaditoSearchitemlink('id', $this->getTable(), 'PluginArmaditoScan');
        $items['Agent Id']       = new PluginArmaditoSearchitemlink('id', 'glpi_plugin_armadito_agents', 'PluginArmaditoAgent');
        $items['Job Status']     = new PluginArmaditoSearchitemlink('job_status', 'glpi_plugin_armadito_jobs', 'PluginArmaditoJob');
        $items['Configuration']  = new PluginArmaditoSearchitemlink('scan_name', 'glpi_plugin_armadito_scanconfigs', 'PluginArmaditoScanConfig');
        $items['Progress']       = new PluginArmaditoSearchtext('progress', $this->getTable());
        $items['Suspicious']     = new PluginArmaditoSearchtext('suspicious_count', $this->getTable());
        $items['Malware']        = new PluginArmaditoSearchtext('malware_count', $this->getTable());
        $items['Scanned']        = new PluginArmaditoSearchtext('scanned_count', $this->getTable());
        $items['Duration']       = new PluginArmaditoSearchtext('duration', $this->getTable());
        $items['Antivirus']      = new PluginArmaditoSearchitemlink('fullname', 'glpi_plugin_armadito_antiviruses', 'PluginArmaditoAntivirus');

        return $search_options->get($items);
    }

    function isScaninDB()
    {
        global $DB;

        $query = "SELECT id FROM `glpi_plugin_armadito_scans`
                 WHERE `agent_id`='" . $this->agentid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error isScaninDB : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            return true;
        }

        return false;
    }

    function addObj($job_id_, $agent)
    {
        return $this->insertScanInDB($job_id_, $agent);
    }

    function insertScanInDB($job_id_, $agent)
    {
        $dbmanager = new PluginArmaditoDbManager();

        $params["plugin_armadito_jobs_id"]["type"]        = "i";
        $params["plugin_armadito_agents_id"]["type"]      = "i";
        $params["plugin_armadito_scanconfigs_id"]["type"] = "i";
        $params["plugin_armadito_antiviruses_id"]["type"] = "i";

        $query = "NewScan";
        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager->setQueryValue($query, "plugin_armadito_jobs_id", $job_id_);
        $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query, "plugin_armadito_scanconfigs_id", $this->scanconfigid);
        $dbmanager->setQueryValue($query, "plugin_armadito_antiviruses_id", $agent->getAntivirusId());

        $dbmanager->executeQuery($query);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        PluginArmaditoToolbox::validateInt($this->id);
        $this->logNewItem();
    }


    function updateScanInDB()
    {
        $dbmanager = new PluginArmaditoDbManager();

        $params["start_time"]["type"]              = "s";
        $params["duration"]["type"]                = "s";
        $params["malware_count"]["type"]           = "i";
        $params["suspicious_count"]["type"]        = "i";
        $params["scanned_count"]["type"]           = "i";
        $params["progress"]["type"]                = "i";
        $params["plugin_armadito_jobs_id"]["type"] = "i";

        $query = "UpdateScan";
        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, "plugin_armadito_jobs_id");
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager->setQueryValue($query, "duration", $this->obj->duration);
        $dbmanager->setQueryValue($query, "start_time", $this->obj->start_time);
        $dbmanager->setQueryValue($query, "malware_count", $this->obj->malware_count);
        $dbmanager->setQueryValue($query, "suspicious_count", $this->obj->suspicious_count);
        $dbmanager->setQueryValue($query, "scanned_count", $this->obj->scanned_count);
        $dbmanager->setQueryValue($query, "progress", $this->obj->progress);
        $dbmanager->setQueryValue($query, "plugin_armadito_jobs_id", $this->obj->job_id); # WHERE

        $dbmanager->executeQuery($query);
    }

    function defineTabs($options = array())
    {
        $ong = array();
        $this->addDefaultFormTab($ong);
        $this->addStandardTab('Log', $ong, $options);

        return $ong;
    }

    function showForm($table_id, $options = array())
    {
        PluginArmaditoToolbox::validateInt($table_id);
        $this->initForm($table_id, $options);
        $this->showFormHeader($options);

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Name') . " :</td>";
        echo "<td align='center'>";
        Html::autocompletionTextField($this, 'name', array(
            'size' => 40
        ));
        echo "</td>";
        echo "<td>" . __('Agent Id', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo "<b>" . htmlspecialchars($this->fields["plugin_armadito_agents_id"]) . "</b>";
        echo "</td>";
        echo "</tr>";
    }
}
?>
