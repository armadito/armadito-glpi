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

class ApiAgentsTest extends CommonTestCase
{
    /**
     * @test
     */
    public function POSTrequests()
    {
        // Scheduler id should be from 0 to N without any gaps
        // Agent id is just an auto_incremented id
        $this->insertOrUpdateAgent(0, 1, 1, "4C4C4544-0033-4A10-8051-BBBBBBBBBBBB");
        $this->insertOrUpdateAgent(0, 2, 2, "4C4C4544-0033-4A10-8051-FFFFFFFFFFFF");
        $this->insertOrUpdateAgent(2, 2, 2, "4C4C4544-0033-4A10-8051-FFFFFFFFFFFF");
        $this->insertOrUpdateAgent(0, 2, 2, "4C4C4544-0033-4A10-8051-FFFFFFFFFFFF");

        $this->purgeAgent(1);

        $this->insertOrUpdateAgent(0, 3, 1, "4C4C4544-0033-4A10-8051-BBBBBBBBBBBB", 1);
        $this->insertOrUpdateAgent(0, 4, 3, "4C4C4544-0033-4A10-8051-AAAAAAAAAAAA", 1);
    }

    protected function insertOrUpdateAgent($current_agentid, $new_agentid, $new_schedulerid, $uuid)
    {
        $json  = '{
  "task": {"obj":"{}",
           "name":"Enrollment",
           "antivirus": {
                         "name":"Armadito",
                         "version":"0.10.2",
                         "os_info": {"name":"linux","libpath":"linux"}
                        }
          },
  "agent_version":"2.3.18",
  "agent_id":'.$current_agentid.',
  "uuid":"'.$uuid.'"}';

        $jobj = PluginArmaditoToolbox::parseJSON($json);
        $agent = new PluginArmaditoAgent();
        $agent->initFromJson($jobj);
        $agent->insertOrUpdateInDB();
        $this->assertEquals($new_agentid, $agent->getId());

        $Scheduler = new PluginArmaditoScheduler();
        $Scheduler->init($agent->getId());
        $Scheduler->insertOrUpdateInDB();
        $this->assertEquals($new_schedulerid, $Scheduler->getId());

        $agent->updateSchedulerId($Scheduler->getId());
        $json = $agent->toJson();
        $jobj = PluginArmaditoToolbox::parseJSON($json);
        $this->assertEquals($new_agentid, $jobj->agent_id);
        $this->assertEquals($new_schedulerid, $jobj->scheduler_id);
    }

    // Simulate purge from glpi gui
    protected function purgeAgent($agentid)
    {
        global $DB;
        $query = "DELETE from `glpi_plugin_armadito_agents` WHERE `id` = '".$agentid."'";
        $DB->query($query);

        $Scheduler = new PluginArmaditoScheduler();
        $Scheduler->init($agentid);
        $Scheduler->setUnused();
    }
}

