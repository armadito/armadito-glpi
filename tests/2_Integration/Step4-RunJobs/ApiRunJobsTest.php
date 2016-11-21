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

class ApiRunJobsTest extends CommonTestCase
{
    /**
     * @test
     */
    public function POSTrequests()
    {
        $this->updateJob(2, 1, "runJob successful", 0);
    }

    protected function updateJob($agentid, $jobid, $message, $code)
    {
        $json = '{"agent_id":"'.$agentid.'","task":
                   {"obj":{"message":"'.$message.'","code":'.$code.',"job_id":"'.$jobid.'"},
                   "name":"Runjobs","antivirus":{"version":"0.12.0-exp","name":"Armadito"}},"agent_version":"0.1.0_02"}';

        $jobj = PluginArmaditoToolbox::parseJSON($json);
        $job = new PluginArmaditoJob();
        $job->initFromJson($jobj);
        $job->updateJobStatus();
    }
}

