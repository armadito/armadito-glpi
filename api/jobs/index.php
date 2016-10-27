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

if (isset($_GET['agent_id'])) {

    PluginArmaditoToolbox::checkPluginInstallation();

    $error         = new PluginArmaditoError();
    $communication = new PluginArmaditoCommunication();
    $communication->init();

    PluginArmaditoLastContactStat::increment();

    $jobmanager = new PluginArmaditoJobmanager();
    $jobmanager->init($_GET['agent_id']);
    $error = $jobmanager->getJobs("queued");

    if ($error->getCode() == 0) { // success
        $communication->setMessage($jobmanager->toJson(), 200);
        $jobmanager->updateJobStatuses("downloaded");
    } else {
        $communication->setMessage($error->toJson(), 500);
    }

    $error->log();
    $communication->sendMessage();
    session_destroy();
} else if (!empty($rawdata)) {

    PluginArmaditoToolbox::checkPluginInstallation();

    $error         = new PluginArmaditoError();
    $communication = new PluginArmaditoCommunication();
    $communication->init();

    PluginArmaditoLastContactStat::increment();

    $jobj = PluginArmaditoToolbox::parseJSON($rawdata);
    if (!$jobj) {
        $error->setMessage(1, "Fail parsing incoming json : " . json_last_error_msg());
        $error->log();
        $communication->setMessage($error->toJson(), 405);
        $communication->sendMessage();
        session_destroy();
        exit();
    }

    $job = new PluginArmaditoJob();
    $job->initFromJson($jobj);
    if ($jobj->task->obj->code == 0) {
        $job->updateStatus("successful");
        $error->setMessage(0, "Updated JobStatus successfully.");
    } else {
        $job->updateStatus("failed");
        $error = $job->updateJobErrorInDB($jobj->task->obj->code, $jobj->task->obj->message);
    }

    $communication->setMessage($error->toJson(), 200);
    $communication->sendMessage();
    session_destroy();
} else {
    http_response_code(400);
    header("Content-Type: application/json");
    header("X-ArmaditoPlugin-Version: " . PLUGIN_ARMADITO_VERSION);
    echo '{"code": 1, "message": "Invalid request sent to plugin index."}';
}

?>

