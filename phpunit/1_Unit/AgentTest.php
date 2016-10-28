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

class AgentTest extends RestoreDatabase_TestCase
{

    /**
     * @test
     */
    public function addAgent()
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
        $this->assertNotEquals(0, $jobj, json_last_error_msg());

        $agent->initFromJson($jobj);
        $error = $agent->getAntivirus()->run();
        $this->assertEquals(0, $error->getCode(), $error->getMessage());

        $error = $agent->insertAgentInDB();
        $this->assertEquals(0, $error->getCode(), $error->getMessage());

        return $agent;
    }

    /**
     * @test
     */
    public function agentExists()
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
        $this->assertNotEquals(0, $jobj, json_last_error_msg());

        $agent->initFromJson($jobj);
        $this->assertEquals(true, $agent->isAgentInDB(), "isAgentInDB not working.");
    }
}

