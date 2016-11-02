<?php

/*
Copyright (C) 2010-2016 by the FusionInventory Development Team
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

include_once("../../../../inc/includes.php");

$rawdata = file_get_contents("php://input");

if (isset($_GET['agent_id']))
{
    PluginArmaditoToolbox::checkPluginInstallation();
    $communication = new PluginArmaditoCommunication();
    $communication->init();

    PluginArmaditoLastContactStat::increment();

    try
    {
        $jobmanager = new PluginArmaditoJobmanager();
        $jobmanager->init($_GET['agent_id']);
        $jobmanager->getJobs("queued");
        $jobmanager->updateJobStatuses("downloaded");
        $communication->setMessage($jobmanager->toJson(), 200);
    }
    catch(Exception $e)
    {
        $communication->setMessage($e->getMessage(), 500);
        PluginArmaditoToolbox::logE($e->getMessage());
    }

    $communication->sendMessage();
    session_destroy();
}
else if (!empty($rawdata))
{

    PluginArmaditoToolbox::checkPluginInstallation();
    $communication = new PluginArmaditoCommunication();
    $communication->init();

    PluginArmaditoLastContactStat::increment();

    try
    {
        $jobj = PluginArmaditoToolbox::parseJSON($rawdata);
        $job = new PluginArmaditoJob();
        $job->initFromJson($jobj);
        $job->updateStatus("successful");
        $communication->setMessage("", 200);
    }
    catch(PluginArmaditoJsonException $e)
    {
        $communication->setMessage($e->getMessage(), 400);
        PluginArmaditoToolbox::logE($e->getMessage());
    }
    catch(Exception $e)
    {
        $communication->setMessage($e->getMessage(), 500);
        PluginArmaditoToolbox::logE($e->getMessage());

        $job->updateStatus("failed");
        $job->updateJobErrorInDB($jobj->task->obj->code, $jobj->task->obj->message);
    }

    $communication->sendMessage();
    session_destroy();
}
else
{
    http_response_code(400);
    header("Content-Type: application/json");
    header("X-ArmaditoPlugin-Version: " . PLUGIN_ARMADITO_VERSION);
    echo '{"code": 1, "message": "Invalid request sent to plugin index."}';
}

?>

