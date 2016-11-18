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

class NewScansTest extends CommonTestCase
{
    /**
     * @test
     */
    public function FromMassiveAction()
    {
        $this->NewScanForAgent(2, 1, 0);
        $this->NewScanForAgent(2, 1, 1);
        // $this->NewScanForComputer(1, 1, 0);
    }

    protected function NewScanForAgent($agentid, $scanconfigid, $jobpriority)
    {
        $this->NewScan($agentid, $scanconfigid, $jobpriority);
    }

    protected function NewScanForComputer($computerid, $scanconfigid, $jobpriority)
    {
        $association = new PluginArmaditoAgentAssociation();
        $association->setComputerId($computerid);
        $agentid = $association->getAgentIdFromDB();

        $this->NewScan($agentid, $scanconfigid, $jobpriority);
    }

    protected function NewScan($agentid, $scanconfigid, $jobpriority)
    {
        $post_data = array(
            "scanconfig_id" => $scanconfigid,
            "job_priority" => $jobpriority
        );

        $job = new PluginArmaditoJob();
        $job->initFromForm($agentid, "Scan", $post_data);
        $job->addJob();
    }
}

