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

class ApiAVConfigsTest extends CommonTestCase
{
    /**
     * @test
     */
    public function POSTrequests()
    {
        $av_config = '[{ "attr": "on-access", "value": "enabled" },
            { "attr": "scan:archive", "value": 1 },
            { "attr": "scan:jar", "value": 0 },
            { "attr": "module:h1", "value": "disabled" },
            { "attr": "module:clamav", "value": "enabled" },
            { "attr": "module:pdf", "value": "disabled" },
            { "attr": "scan:whitelist-dirs", "value": "/home/noscan, /home/nothinghere" }]';

        $this->insertOrUpdateAVConfig(2, "4C4C4544-0033-4A10-8051-FFFFFFFFFFFF", $av_config);
    }

    protected function insertOrUpdateAVConfig($agentid, $uuid, $av_config)
    {
        $json  = '{
  "task": {"obj": '.$av_config.',
           "name":"AVConfig",
           "antivirus": {
                         "name":"Armadito",
                         "version":"0.10.2"
                        }
          },
  "agent_version":"2.3.18",
  "agent_id":'.$agentid.'
  "uuid": '.$uuid.' }';

        $jobj = PluginArmaditoToolbox::parseJSON($json);

        $state = new PluginArmaditoAVConfig();
        $state->initFromJson($jobj);
        $state->run();
    }
}

