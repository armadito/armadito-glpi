<?php

/*
Copyright (C) 2010-2016 by the FusionInventory Development Team.
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

class NewAgentTest extends RestoreDatabaseTestCase
{

    /**
     * @test
     */
    public function addAgent()
    {
        $json  = '{
  "task": {"obj":"{}",
           "name":"Enrollment",
           "antivirus": {
                         "name":"Armadito",
                         "version":"0.10.2"
                        }
          },
  "agent_version":"2.3.18",
  "agent_id":1,
  "uuid":"4C4C4544-0033-4A10-8051-C7C04F503732"}';

        $jobj = PluginArmaditoToolbox::parseJSON($json);
        $agent = new PluginArmaditoAgent();
        $agent->initFromJson($jobj);
        $agent->insertOrUpdateInDB();

        $Scheduler = new PluginArmaditoScheduler();
        $Scheduler->init($agent->getId());
        $Scheduler->insertOrUpdateInDB();
        $Agent->updateSchedulerId($Scheduler->getId());

        writeHttpOKResponse($Agent->toJson());

        return $agent;
    }

    /**
     * @test
     */
    public function updateAgent()
    {
        $agent = new PluginArmaditoAgent();
        $json  = '{
  "task": {"obj":"{}",
           "name":"Enrollment",
           "antivirus": {
                         "name":"Armadito",
                         "version":"0.10.2"
                        }
          },
  "agent_version":"2.3.18",
  "agent_id":1,
  "uuid":"4C4C4544-0033-4A10-8051-C7C04F503732"}';

        $jobj = PluginArmaditoToolbox::parseJSON($json);
        $agent->initFromJson($jobj);
        $agent->getAntivirus()->run();
        $agent->insertAgentInDB();

        return $agent;
    }

}

