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

class ApiAlertsTest extends CommonTestCase
{
    /**
     * @test
     */
    public function POSTrequests()
    {
        $this->insertOrUpdateAlert(2, "2016-11-18T11:39:09", "Pdf.Dropper.Agent-1507034");
        $this->insertOrUpdateAlert(3, "2016-11-18T11:40:09", "Pdf.Dropper.Agent-1507034");
        $this->insertOrUpdateAlert(3, "2016-11-18T11:40:10", "Win.Trojan.Elpapok-1");
    }

    protected function insertOrUpdateAlert($agentid, $detection_time, $malware_type)
    {
        $json_alert = '{"alerts":
            {
             "name" : "'.$malware_type.'"
             "filepath" : "/home/malwares/X",
             "action" : "cleaned",
             "detection_time" : "'.$detection_time.'"
            }
        }';

        $json  = '{
  "task": {"obj": '.$json_alert.',
           "name":"Alerts",
           "antivirus": {
                         "name":"Armadito",
                         "version":"0.10.2"
                        }
          },
  "agent_version":"2.3.18",
  "agent_id":'.$agentid.' }';

        $jobj = PluginArmaditoToolbox::parseJSON($json);
        $alert = new PluginArmaditoAlert();
        $alert->initFromJson($jobj);
        $alert->insertAlert();
        $alert->updateAlertStat();
        $agent = new PluginArmaditoAgent();
        $agent->updateLastAlert($alert);
    }
}

