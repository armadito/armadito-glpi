<?php

/*
Copyright (C) 2010-2016 by the FusionInventory Development Team
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

class PluginArmaditoScheduler extends PluginArmaditoCommonDBTM
{
    protected $id;
    protected $agentid;
    protected $details;
    protected $details_id;

    function init($agent_id)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($agent_id);
    }

    static function getTypeName($nb = 0)
    {
        return __('Scheduler', 'armadito');
    }

    function initFromJson($jobj)
    {
        $this->details = new PluginArmaditoSchedulerDetail();
        $this->details->initFromJson($jobj);

        $this->agentid = $jobj->agent_id;
        $this->agent = $this->details->getAgent();
        $this->antivirus = $this->agent->getAntivirus();
        $this->setObj($jobj->task->obj);
    }

    function setObj($obj)
    {
        $this->obj = new StdClass;
    }

    function insertSchedulerInDB()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $query = "NewScheduler";

        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        PluginArmaditoToolbox::validateInt($this->id);
        $this->logNewItem();
    }

    function replaceSchedulerInDB( $scheduler_id )
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $params["id"]["type"] = "i";
        $query = "UpdateScheduler";

        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, "id");
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->setQueryValue($query, "id", $scheduler_id);
        $dbmanager->executeQuery($query);

        $this->id = $scheduler_id;
    }

    function updateSchedulerInDB()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $query = "UpdateScheduler";

        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, "plugin_armadito_agents_id");
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);
    }

    function setCommonQueryParams()
    {
        $params["plugin_armadito_agents_id"]["type"] = "i";
        $params["is_used"]["type"] = "i";
        return $params;
    }
    function setCommonQueryValues($dbmanager, $query)
    {
        $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query, "is_used", 1);
        return $dbmanager;
    }

    function getId()
    {
        return $this->id;
    }

    function setUnused()
    {
        if($this->isAgentInDB())
        {
            $input                               = array();
            $input['id']                         = $this->id;
            $input['plugin_armadito_agents_id']  = -1;
            $input['is_used']                    = 0;
            if (!$this->update($input)) {
                throw new InvalidArgumentException(sprintf('Error setUnused Scheduler'));
            }
        }
    }

    function insertOrUpdateInDB()
    {
        $this->details->run();
        $this->details_id = $this->getTableIdForAgentId("glpi_plugin_armadito_schedulerdetails");

        if($this->isSchedulerForAgentInDB())
        {
            $this->updateSchedulerInDB();
        }
        else
        {
            $unused_id = $this->getFirstUnusedId();
            if($unused_id == NULL)
            {
                $this->insertSchedulerInDB();
            }
            else
            {
                $this->replaceSchedulerInDB( $unused_id );
            }
        }
    }

    function isSchedulerForAgentInDB()
    {
        global $DB;

        $query = "SELECT id FROM `glpi_plugin_armadito_schedulers`
                WHERE `plugin_armadito_agents_id`='" . $this->agentid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error isAgentInDb : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            $this->id = PluginArmaditoToolbox::validateInt($data["id"]);
            return true;
        }

        return false;
    }

    function getFirstUnusedId()
    {
        global $DB;

        $query = "SELECT min(id) FROM `".$this->getTable()."`
                  WHERE `is_used` = '0'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error computeSchedulerId : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return $data["min(id)"];
        }

        return NULL;
    }
}

?>
