<?php

/**
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

/**
 * Class dealing with Jobs
 **/
class PluginArmaditoJob extends CommonDBTM
{
    protected $id;
    protected $obj;
    protected $jobj;
    protected $type;
    protected $priority;
    protected $agentid;
    protected $agent;

    function __construct()
    {
        $this->type     = -1;
        $this->priority = -1;
        $this->id       = -1;
        $this->obj      = "";
        $this->agentid  = -1;
    }

    function initFromForm($key, $type, $POST)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($key);
        $this->agent   = new PluginArmaditoAgent();
        $this->agent->initFromDB($this->agentid);

        $this->type = $type;
        $this->setPriority($POST["job_priority"]);

        // init Scan Obj for example or an other job_type
        $this->initObjFromForm($key, $type, $POST);
    }

    function initFromDB($data)
    {
        $error = new PluginArmaditoError();

        $this->id       = $data["id"];
        $this->agentid  = $data["plugin_armadito_agents_id"];
        $this->type     = $data["job_type"];
        $this->priority = $data["job_priority"];
        $this->status   = $data["job_status"];

        $this->agent = new PluginArmaditoAgent();
        $this->agent->initFromDB($this->agentid);

        // init Scan Obj for example or an other job_type
        $error = $this->initObjFromDB();
        return $error;
    }

    function initFromJson($jobj)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
        $this->id      = PluginArmaditoToolbox::validateInt($jobj->task->obj->{"job_id"});
        $this->jobj    = $jobj;
    }

    function getId()
    {
        return $this->id;
    }

    function setId($id_)
    {
        $this->id = $id_;
    }

    function getAgentId()
    {
        return $this->agentid;
    }

    /**
     * Get name of this type
     *
     * @return text name of this type by language of the user connected
     *
     **/
    static function getTypeName($nb = 0)
    {
        return __('Job', 'armadito');
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

    function initObjFromForm($key, $type, $POST)
    {
        $error = new PluginArmaditoError();
        switch ($this->type) {
            case "Scan":
                $this->obj = new PluginArmaditoScan();
                $error     = $this->obj->initFromForm($this, $POST);
                break;
            default:
                $this->obj = "unknown";
                $error->setMessage(0, 'Unknown Job Type.');
                break;
        }
        return $error;
    }

    function initObjFromDB()
    {
        $error = new PluginArmaditoError();
        switch ($this->type) {
            case "Scan":
                $this->obj = new PluginArmaditoScan();
                $error     = $this->obj->initFromDB($this->id);
                break;
            default:
                $this->obj = "unknown";
                $error->setMessage(0, 'Unknown Job Type.');
                break;
        }
        return $error;
    }

    function getPriorityValue()
    {
        switch ($this->priority) {
            case "0 - low":
                return 0;
            case "1 - medium":
                return 1;
            case "2 - high":
                return 2;
            case "3 - urgent":
                return 3;
            default:
                return 0;
        }
    }

    function setPriority($id)
    {
        PluginArmaditoToolbox::validateInt($id);
        switch ($id) {
            case 0:
                $this->priority = "0 - low";
                break;
            case 1:
                $this->priority = "1 - medium";
                break;
            case 2:
                $this->priority = "2 - high";
                break;
            case 3:
                $this->priority = "3 - urgent";
                break;
            default:
                $this->priority = "0 - low";
                break;
        }
    }

    static function getAvailableStatuses()
    {
        $gcolors = PluginArmaditoColorToolbox::getGoldenColors();

        return array(
            "queued" => "#dedede",
            "downloaded" => $gcolors["blue"],
            "successful" => $gcolors["green1"],
            "failed" => $gcolors["red"],
            "cancelled" => $gcolors["orange"]
        );
    }

    function updateStatus($status)
    {
        if ($this->getFromDB($this->getId())) {
            $input               = array();
            $input['id']         = $this->getId();
            $input['job_status'] = $status;
            if (!$this->update($input)) {
                PluginArmaditoToolbox::logE("Error when updating job n°" . $this->getId() . " status in DB.");
                return false;
            }
            return true;
        }
        return false;
    }

    function updateJobErrorInDB($err_code, $err_msg)
    {
        $error     = new PluginArmaditoError();
        $dbmanager = new PluginArmaditoDbManager();
        $dbmanager->init();

        $params["job_error_code"]["type"] = "i";
        $params["job_error_msg"]["type"]  = "s";
        $params["id"]["type"]             = "i";

        $query_name = "UpdateJobError";
        $dbmanager->addQuery($query_name, "UPDATE", $this->getTable(), $params, "id");

        if (!$dbmanager->prepareQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        if (!$dbmanager->bindQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->setQueryValue($query_name, "job_error_code", $err_code);
        $dbmanager->setQueryValue($query_name, "job_error_msg", $err_msg);
        $dbmanager->setQueryValue($query_name, "id", $this->id); # WHERE

        if (!$dbmanager->executeQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->closeQuery($query_name);
        $error->setMessage(0, 'JobError successfully updated in database.');
        return $error;
    }

    function getSearchOptions()
    {

        $tab           = array();
        $tab['common'] = __('Scan', 'armadito');

        $i = 1;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'id';
        $tab[$i]['name']          = __('Job Id', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoJob';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_agents';
        $tab[$i]['field']         = 'id';
        $tab[$i]['name']          = __('Agent Id', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoAgent';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'job_type';
        $tab[$i]['name']          = __('Job Type', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'job_priority';
        $tab[$i]['name']          = __('Job Priority', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'job_status';
        $tab[$i]['name']          = __('Job Status', 'armadito');
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

    function toJson()
    {
        return '{"job_id": ' . $this->id . ',
                  "job_type": "' . $this->type . '",
                  "job_priority": "' . $this->getPriorityValue() . '",
                  "obj": ' . $this->obj->toJson() . '
                 }';
    }

    function getJobId()
    {
        return $this->id;
    }

    function insertInJobs()
    {
        global $DB;

        $error     = new PluginArmaditoError();
        $dbmanager = new PluginArmaditoDbManager();
        $dbmanager->init();

        $params["plugin_armadito_agents_id"]["type"]      = "i";
        $params["plugin_armadito_antiviruses_id"]["type"] = "i";
        $params["job_type"]["type"]                       = "s";
        $params["job_priority"]["type"]                   = "s";
        $params["job_status"]["type"]                     = "s";

        $query_name = "NewJob";
        $dbmanager->addQuery($query_name, "INSERT", $this->getTable(), $params);

        if (!$dbmanager->prepareQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        if (!$dbmanager->bindQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->setQueryValue($query_name, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query_name, "plugin_armadito_antiviruses_id", $this->agent->getAntivirusId());
        $dbmanager->setQueryValue($query_name, "job_type", $this->type);
        $dbmanager->setQueryValue($query_name, "job_priority", $this->priority);
        $dbmanager->setQueryValue($query_name, "job_status", "queued");

        if (!$dbmanager->executeQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->closeQuery($query_name);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        if ($this->id > 0) {
            PluginArmaditoToolbox::validateInt($this->id);
            $error->setMessage(0, 'New Job successfully added in database.');
        } else {
            $error->setMessage(1, 'Unable to get new Job Id');
        }
        return $error;
    }

    function addJob()
    {
        $error = new PluginArmaditoError();
        $error = $this->insertInJobs();
        if ($error->getCode() != 0) {
            $error->log();
            return false;
        }

        $error = $this->obj->addObj($this->id, $this->agent);
        if ($error->getCode() != 0) {
            $error->log();
            return false;
        }
        return true;
    }

    function cancelJob()
    {
        if ($this->getFromDB($this->id)) {
            if ($this->fields["job_status"] == "queued") {
                return $this->updateStatus("cancelled");
            } else if ($this->fields["job_status"] == "cancelled") {
                return true;
            } else {
                PluginArmaditoToolbox::logE("You cannot cancel a job already downloaded.");
            }
        }
        return false;
    }

    /**
     * Massive action ()
     */
    function getSpecificMassiveActions($checkitem = NULL)
    {

        $actions = array();
        if (Session::haveRight("plugin_armadito_jobs", UPDATE)) {
            $actions[__CLASS__ . MassiveAction::CLASS_ACTION_SEPARATOR . 'canceljob'] = __('Cancel', 'armadito');
        }

        return $actions;
    }

    /**
     * @since version 0.85
     *
     * @see CommonDBTM::showMassiveActionsSubForm()
     **/
    static function showMassiveActionsSubForm(MassiveAction $ma)
    {

        switch ($ma->getAction()) {
            case 'canceljob':
                PluginArmaditoJob::showCancelForm();
                return true;
        }

        return parent::showMassiveActionsSubForm($ma);
    }

    static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item, array $ids)
    {

        $job = new self();

        switch ($ma->getAction()) {

            case 'canceljob':
                foreach ($ids as $job_id) {
                    $job->setId($job_id);
                    if ($job->cancelJob()) {
                        $ma->itemDone($item->getType(), $job_id, MassiveAction::ACTION_OK);
                    } else {
                        $ma->itemDone($item->getType(), $job_id, MassiveAction::ACTION_KO);
                    }
                }
                return;
        }

        return;
    }

    static function showCancelForm()
    {
        echo "<b>Only queued jobs can be cancelled.</b><br>";
        echo "Do you want to continue anyway ?<br>";
        echo "<br><br>" . Html::submit(__('Post'), array(
            'name' => 'massiveaction'
        ));
    }


    function defineTabs($options = array())
    {

        $ong = array();
        $this->addDefaultFormTab($ong);
        $this->addStandardTab('Log', $ong, $options);

        return $ong;
    }

    /**
     * Display form
     *
     * @param $agent_id integer ID of the agent
     * @param $options array
     *
     * @return bool TRUE if form is ok
     *
     **/
    function showForm($table_id, $options = array())
    {
        PluginArmaditoToolbox::validateInt($table_id);
        $this->initForm($table_id, $options);
        $this->showFormHeader($options);

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Agent Id', 'armadito') . "&nbsp;:</td>";
        echo "<td align='left'>";
        echo "<b>" . htmlspecialchars($this->fields["plugin_armadito_agents_id"]) . "</b>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Job Id', 'armadito') . "&nbsp;:</td>";
        echo "<td align='left'>";
        echo "<b>" . htmlspecialchars($this->fields["id"]) . "</b>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Job Type', 'armadito') . "&nbsp;:</td>";
        echo "<td align='left'>";
        echo "<b>" . htmlspecialchars($this->fields["job_type"]) . "</b>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Job Priority', 'armadito') . "&nbsp;:</td>";
        echo "<td align='left'>";
        echo htmlspecialchars($this->fields["job_priority"]);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Job Status', 'armadito') . "&nbsp;:</td>";
        echo "<td align='left'>";
        echo htmlspecialchars($this->fields["job_status"]);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Job Error Code', 'armadito') . "&nbsp;:</td>";
        echo "<td align='left'>";
        echo htmlspecialchars($this->fields["job_error_code"]);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Job Error Message', 'armadito') . "&nbsp;:</td>";
        echo "<td align='left'>";
        echo htmlspecialchars(base64_decode($this->fields["job_error_msg"]));
        echo "</td>";
        echo "</tr>";
    }
}
?>
