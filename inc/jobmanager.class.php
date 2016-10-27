<?php

/**
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

include_once("toolbox.class.php");

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

/**
 * Class used to manage multiple PluginArmaditoJob
 **/
class PluginArmaditoJobmanager extends CommonDBTM
{
    protected $jobs;
    protected $agentid;

    function __construct()
    {
        $this->agentid = -1;
    }

    function init($id)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($id);
        $this->jobs    = array();
    }

    function toJson()
    {
        $json = '{ "jobs": [';
        foreach ($this->jobs as $job) {
            PluginArmaditoToolbox::logE("ToJson Job " . $job->getId());
            $json .= $job->toJson() . ",";
        }
        return rtrim($json, ",") . "]}";
    }

    function getJobs($status)
    {
        global $DB;
        $error = new PluginArmaditoError();
        $paConfig = new PluginArmaditoConfig();

        $getjobs_limit = PluginArmaditoToolbox::validateInt($paConfig->getValue('getjobs_limit'));

        $query = "SELECT * FROM `glpi_plugin_armadito_jobs`
                 WHERE `plugin_armadito_agents_id`='" . $this->agentid . "' AND `job_status`='" . $status . "' LIMIT ".$getjobs_limit;

        $ret = $DB->query($query);

        if (!$ret) {
            throw new RuntimeException(sprintf('Error getJobs : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $i = 0;
            while ($data = $DB->fetch_assoc($ret)) {
                $job   = new PluginArmaditoJob();
                $error = new PluginArmaditoError();
                $error = $job->initFromDB($data);
                if ($error->getCode() == 0) {
                    array_push($this->jobs, $job);
                    $i++;
                }
            }

            $error->setMessage(0, 'Got ' . $i . ' jobs for this agent.');
            return $error;
        }

        $error->setMessage(0, 'No jobs queued for this agent.');
        return $error;
    }

    function updateJobStatuses($status)
    {
        foreach ($this->jobs as $job) {
            $job->updateStatus($status);
        }
    }
}
?>
