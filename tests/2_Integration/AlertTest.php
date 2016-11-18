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

class AlertTest extends CommonTestCase
{
    /**
     * @test
     */
    public function addMultipleAlerts()
    {
        $this->insertOrUpdateAlert(2, "2016-11-18T11:39:09", "Pdf.Dropper.Agent-1507034");
        $this->insertOrUpdateAlert(3, "2016-11-18T11:40:09", "Pdf.Dropper.Agent-1507034");
        $this->insertOrUpdateAlert(3, "2016-11-18T11:40:10", "Win.Trojan.Elpapok-1");
    }

    protected function insertOrUpdateAlert($agentid, $detection_time, $malware_type)
    {
        $json_alert = '{"alert":{"_z":594,"identification":{"_i":36016721,"_pos":5,"process":{"_z":476,"_i":36016861,"_pos":11,"value":"armadito-scand"},"value":"\n    ","pid":{"value":"7155","_pos":10,"_i":36016841,"_z":438},"_z":496,"hostname":{"value":"lw007","_i":36016764,"_z":372,"_pos":7},"os":{"value":"Linux","_pos":9,"_z":418,"_i":36016822},"user":{"_i":36016742,"_z":341,"_pos":6,"value":"root"},"ip":{"value":"172.28.219.59","_z":399,"_i":36016795,"_pos":8}},"module":{"_pos":12,"_z":522,"_i":36016917,"value":"clamav"},"module_specific":{"_pos":13,"_i":36016943,"_z":585,"value":"Pdf.Dropper.Agent-1507034"},"detection_time":{"_pos":4,"_z":300,"_i":36016666,"value":"'. $detection_time.'"},"_i":36016456,"_pos":1,"uri":{"value":"/home/vhamon/MalwareStore/contagio-malware/pdf/MALWARE_PDF_CVEsorted_173_files/CVE-2011-2462_PDF_25files/CVE-2011-2462_DDE3FD3E45FB8ADAA1243BEC826FEDCD.pdf","type":{"value":"path","_att":1},"_pos":3,"_z":245,"_i":36016485},"value":"\n  ","level":{"_pos":2,"_i":36016466,"_z":64,"value":"2"}},"value":"\n","_pos":0,"_z":0,"_i":0}';

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

