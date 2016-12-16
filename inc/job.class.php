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

class PluginArmaditoJob extends PluginArmaditoCommonDBTM
{
    protected $id;
    protected $obj;
    protected $jobj;
    protected $type;
    protected $priority;
    protected $agentid;
    protected $agent;

    static $rightname = 'plugin_armadito_jobs';

    function initFromForm($key, $type, $POST)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($key);
        $this->agent   = new PluginArmaditoAgent();
        $this->agent->initFromDB($this->agentid);

        $this->type = $type;
        $this->setPriority($POST["job_priority"]);
        $this->initObjFromForm($key, $type, $POST);
    }

    function initFromDB($data)
    {
        $this->id       = $data["id"];
        $this->agentid  = $data["plugin_armadito_agents_id"];
        $this->type     = $data["job_type"];
        $this->priority = $data["job_priority"];
        $this->status   = $data["job_status"];

        $this->agent = new PluginArmaditoAgent();
        $this->agent->initFromDB($this->agentid);
        $this->initObjFromDB();
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

    function getAgent()
    {
        return $this->agent;
    }

    static function getTypeName($nb = 0)
    {
        return __('Job', 'armadito');
    }

    function initObjFromForm($key, $type, $POST)
    {
        if($this->type == "Scan") {
            $this->obj = new PluginArmaditoScan();
            $this->obj->initFromForm($this, $POST);
        }
        else {
            throw new InvalidArgumentException("Unknown Job type.");
        }
    }

    function initObjFromDB()
    {
        if($this->type == "Scan") {
            $this->obj = new PluginArmaditoScan();
            $this->obj->initFromDB($this->id);
        }
        else {
            throw new InvalidArgumentException("Unknown Job type.");
        }
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
        $colortbox = new PluginArmaditoColorToolbox();
        $palette   = $colortbox->getPalette(5);

        return array(
            "successful" => $palette[0],
            "failed" => $palette[1],
            "queued" => $palette[2],
            "downloaded" => $palette[3],
            "cancelled" => $palette[4]
        );
    }

    function updateJobStatus()
    {
        $error_code = $this->jobj->task->obj->code;
        $error_msg = $this->jobj->task->obj->message;

        if ( $error_code == 0) {
            $this->updateStatus("successful");
        }
        else {
            $this->updateStatus("failed");
            $this->updateJobErrorInDB($error_code, $error_msg);
        }
    }

    function updateStatus($newstatus)
    {
        if (!$this->getFromDB($this->getId())) {
            throw new PluginArmaditoDbException("Unable to get job from database.");
        }

        $input               = array();
        $input['id']         = $this->getId();
        $input['job_status'] = $newstatus;

        if (!$this->update($input)) {
            throw new PluginArmaditoDbException("Error when updating job status in DB.");
        }
    }

    function updateJobErrorInDB($err_code, $err_msg)
    {
        $dbmanager = new PluginArmaditoDbManager();

        $params["job_error_code"]["type"] = "i";
        $params["job_error_msg"]["type"]  = "s";
        $params["id"]["type"]             = "i";

        $query = "UpdateJobError";
        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, "id");
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager->setQueryValue($query, "job_error_code", $err_code);
        $dbmanager->setQueryValue($query, "job_error_msg", $err_msg);
        $dbmanager->setQueryValue($query, "id", $this->id); # WHERE

        $dbmanager->executeQuery($query);
    }

    function getSearchOptions()
    {
        $search_options = new PluginArmaditoSearchoptions('Job');

        $items['Job Id']         = new PluginArmaditoSearchitemlink('id', $this->getTable(), 'PluginArmaditoJob');
        $items['Agent Id']       = new PluginArmaditoSearchitemlink('id', 'glpi_plugin_armadito_agents', 'PluginArmaditoAgent');
        $items['Job Type']       = new PluginArmaditoSearchtext('job_type', $this->getTable());
        $items['Job Priority']   = new PluginArmaditoSearchtext('job_priority', $this->getTable());
        $items['Job Status']     = new PluginArmaditoSearchtext('job_status', $this->getTable());
        $items['Antivirus']      = new PluginArmaditoSearchitemlink('fullname', 'glpi_plugin_armadito_antiviruses', 'PluginArmaditoAntivirus');

        return $search_options->get($items);
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
        $dbmanager = new PluginArmaditoDbManager();

        $params["plugin_armadito_agents_id"]["type"]      = "i";
        $params["plugin_armadito_antiviruses_id"]["type"] = "i";
        $params["job_type"]["type"]                       = "s";
        $params["job_priority"]["type"]                   = "s";
        $params["job_status"]["type"]                     = "s";

        $query = "NewJob";
        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);

        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query, "plugin_armadito_antiviruses_id", $this->agent->getAntivirusId());
        $dbmanager->setQueryValue($query, "job_type", $this->type);
        $dbmanager->setQueryValue($query, "job_priority", $this->priority);
        $dbmanager->setQueryValue($query, "job_status", "queued");

        $dbmanager->executeQuery($query);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        PluginArmaditoToolbox::validateInt($this->id);
        $this->logNewItem();
    }

    function addJob()
    {
        $this->insertInJobs();
        $this->obj->addObj($this->id, $this->agent);
    }

    function cancelJob()
    {
        if ($this->getFromDB($this->id)) {
            if ($this->fields["job_status"] == "queued") {
                return $this->updateStatus("cancelled");
            } else if ($this->fields["job_status"] == "cancelled") {
                return true;
            } else {
                PluginArmaditoLog::Warning("You cannot cancel a job already downloaded.");
            }
        }
        return false;
    }

    function getSpecificMassiveActions($checkitem = NULL)
    {
        $actions = array();
        if (Session::haveRight("plugin_armadito_jobs", UPDATE)) {
            $actions[__CLASS__ . MassiveAction::CLASS_ACTION_SEPARATOR . 'canceljob'] = __('Cancel', 'armadito');
        }

        return $actions;
    }

    static function showMassiveActionsSubForm(MassiveAction $ma)
    {
        if($ma->getAction() == 'canceljob') {
            PluginArmaditoJob::showCancelForm();
            return true;
        }

        return parent::showMassiveActionsSubForm($ma);
    }

    static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item, array $ids)
    {
        $job = new self();
        if($ma->getAction() == 'canceljob')
        {
            foreach ($ids as $job_id)
            {
                $job->setId($job_id);
                if ($job->cancelJob()) {
                    $ma->itemDone($item->getType(), $job_id, MassiveAction::ACTION_OK);
                } else {
                    $ma->itemDone($item->getType(), $job_id, MassiveAction::ACTION_KO);
                }
            }
        }
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

    function showForm($table_id, $options = array())
    {
        PluginArmaditoToolbox::validateInt($table_id);
        $this->initForm($table_id, $options);
        $this->showFormHeader($options);

        $rows[] = new PluginArmaditoFormRow('Agent Id', $this->fields["plugin_armadito_agents_id"]);
        $rows[] = new PluginArmaditoFormRow('Job Id', $this->fields["id"]);
        $rows[] = new PluginArmaditoFormRow('Job Type', $this->fields["job_type"]);
        $rows[] = new PluginArmaditoFormRow('Job Priority', $this->fields["job_priority"]);
        $rows[] = new PluginArmaditoFormRow('Job Status', $this->fields["job_status"]);
        $rows[] = new PluginArmaditoFormRow('Job Error Code', $this->fields["job_error_code"]);
        $rows[] = new PluginArmaditoFormRow('Job Error Message', base64_decode($this->fields["job_error_msg"]));

        foreach( $rows as $row )
        {
            $row->write();
        }
    }
}
?>
