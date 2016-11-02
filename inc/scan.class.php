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

class PluginArmaditoScan extends CommonDBTM
{
    protected $id;
    protected $agentid;
    protected $agent;
    protected $jobj;
    protected $job;
    protected $scanconfigid;
    protected $scanconfigobj;

    static $rightname = 'plugin_armadito_scans';

    static function getTypeName($nb = 0)
    {
        return __('Scan', 'armadito');
    }

    static function canDelete()
    {
        return true;
    }

    static function canCreate()
    {
        if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
            return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w');
        }
        return false;
    }

    static function canView()
    {

        if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
            return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w' || $_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'r');
        }
        return false;
    }

    function __construct()
    {
    }

    function initFromForm($jobobj, $POST)
    {
        $this->agentid      = $jobobj->getAgentId();
        $this->scanconfigid = PluginArmaditoToolbox::validateInt($POST["scanconfig_id"]);
    }

    function initFromJson($jobj)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
        $this->jobj    = $jobj;
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
        $tab           = array();
        $tab['common'] = __('Scan', 'armadito');

        $i = 1;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'id';
        $tab[$i]['name']          = __('Scan Id', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoScan';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_agents';
        $tab[$i]['field']         = 'id';
        $tab[$i]['name']          = __('Agent Id', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoAgent';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_jobs';
        $tab[$i]['field']         = 'job_status';
        $tab[$i]['name']          = __('Job', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoJob';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_scanconfigs';
        $tab[$i]['field']         = 'scan_name';
        $tab[$i]['name']          = __('Configuration', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoScanConfig';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'progress';
        $tab[$i]['name']          = __('Progress', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'suspicious_count';
        $tab[$i]['name']          = __('Suspicious', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'malware_count';
        $tab[$i]['name']          = __('Malware', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'scanned_count';
        $tab[$i]['name']          = __('Scanned', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'duration';
        $tab[$i]['name']          = __('Duration', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_antiviruses';
        $tab[$i]['field']         = 'fullname';
        $tab[$i]['name']          = __('Antivirus', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoAntivirus';
        $tab[$i]['massiveaction'] = FALSE;
        return $tab;
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

        $duration = PluginArmaditoToolbox::FormatISO8601DateInterval($this->jobj->task->obj->duration);
        $start_time = date("Y-m-d H:i:s", strtotime($this->jobj->task->obj->start_time));

        $dbmanager->setQueryValue($query, "duration", $duration);
        $dbmanager->setQueryValue($query, "start_time", $start_time);
        $dbmanager->setQueryValue($query, "malware_count", $this->jobj->task->obj->malware_count);
        $dbmanager->setQueryValue($query, "suspicious_count", $this->jobj->task->obj->suspicious_count);
        $dbmanager->setQueryValue($query, "scanned_count", $this->jobj->task->obj->scanned_count);
        $dbmanager->setQueryValue($query, "progress", $this->jobj->task->obj->progress);
        $dbmanager->setQueryValue($query, "plugin_armadito_jobs_id", $this->jobj->task->obj->job_id); # WHERE

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
