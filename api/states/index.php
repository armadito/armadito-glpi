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
include_once("../../api/common.php");

$rawdata = file_get_contents("php://input");
if (!empty($rawdata))
{
    PluginArmaditoToolbox::checkPluginInstallation();
    $communication = new PluginArmaditoCommunication();
    $communication->init();

    PluginArmaditoLastContactStat::increment();

    try
    {
        $jobj = PluginArmaditoToolbox::parseJSON($rawdata);
        $state = new PluginArmaditoState();
        $state->initFromJson($jobj);
        $state->run();

        writeHttpOKResponse("");
    }
    catch(PluginArmaditoJsonException $e)
    {
        writeHttpErrorResponse($e->getMessage(), 400);
    }
    catch(Exception $e)
    {
        writeHttpErrorResponse($e->getMessage(), 500);
    }

    session_destroy();
}
else
{
    writeHttpErrorResponse("Invalid request sent to plugin index", 400);
}

?>

