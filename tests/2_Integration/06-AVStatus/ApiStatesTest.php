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

class ApiStatesTest extends CommonTestCase
{
    /**
     * @test
     */
    public function POSTrequests()
    {
        $this->insertOrUpdateState(2, "4C4C4544-0033-4A10-8051-FFFFFFFFFFFF");
    }

    protected function insertOrUpdateState($agentid, $uuid)
    {
        $task_obj = '{ "dbinfo": {"event_type":"StatusEvent",
                                "global_update_timestamp":1479467793,
                                "modules": [{"name":"clamav","mod_update_timestamp":1479467793,"mod_status":"up-to-date","bases":[{"version":"22558","signature_count":890643,"name":"daily.cld","full_path":"/home/vhamon/workspace/armadito-av/build/linux/scripts/../out/install/armadito-av/var/lib/armadito/bases/clamav/daily.cld","base_update_ts":1479467793},{"full_path":"/home/vhamon/workspace/armadito-av/build/linux/scripts/../out/install/armadito-av/var/lib/armadito/bases/clamav/main.cvd","base_update_ts":1458170226,"version":"57","name":"main.cvd","signature_count":4218790},{"name":"bytecode.cld","signature_count":54,"version":"284","base_update_ts":1477591452,"full_path":"/home/vhamon/workspace/armadito-av/build/linux/scripts/../out/install/armadito-av/var/lib/armadito/bases/clamav/bytecode.cld"}]},{"mod_status":"up-to-date","mod_update_timestamp":1409556600,"name":"moduleH1"}],
                                "global_status":"up-to-date"}
                       "avdetails": [] }';

        $json  = '{
  "task": {"obj": '.$task_obj.',
           "name":"State",
           "antivirus": {
                         "name":"Armadito",
                         "version":"0.10.2"
                        }
          },
  "agent_version":"2.3.18",
  "agent_id":'.$agentid.',
  "uuid":"'.$uuid.'"}';

        $jobj = PluginArmaditoToolbox::parseJSON($json);

        $state = new PluginArmaditoState();
        $state->initFromJson($jobj);
        $state->run();
    }
}

