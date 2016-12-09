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

class ApiScansTest extends CommonTestCase
{
    /**
     * @test
     */
    public function POSTrequests()
    {
        $this->OnDemandCompletedEvent(2, 1, 177, 752, "4C4C4544-0033-4A10-8051-FFFFFFFFFFFF");
        $this->OnDemandCompletedEvent(2, 2, 0, 158, "4C4C4544-0033-4A10-8051-FFFFFFFFFFFF");
    }

    protected function OnDemandCompletedEvent($agentid, $jobid, $malware_count, $scanned_count, $uuid)
    {
        $task_obj = '{"suspicious_count":0,"event_type":"OnDemandCompletedEvent","progress":100,
"malware_count":'.$malware_count.',"start_time":"1970-01-01T00:00:00Z","job_id":"'.$jobid.'","duration":"P0Y0M0DT00H00M01S","scanned_count":'.$scanned_count.'}';

        $json  = '{
  "task": {"obj": '.$task_obj.',
           "name":"Scan",
           "antivirus": {
                         "name":"Armadito",
                         "version":"0.10.2"
                        }
          },
  "agent_version":"2.3.18",
  "agent_id":'.$agentid.',
  "uuid":"'.$uuid.'"}';

        $jobj = PluginArmaditoToolbox::parseJSON($json);
        $scan = new PluginArmaditoScan();
        $scan->initFromJson($jobj);
        $scan->updateScanInDB();
    }
}

