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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

function initGLPISession()
{
    ob_start();
    ini_set("memory_limit", "-1");
    ini_set("max_execution_time", "0");
    ini_set('display_errors', 1);

    if (session_id() == "") {
        session_start();
    }

    $_SESSION['glpi_use_mode'] = Session::NORMAL_MODE;
    if (!isset($_SESSION['glpilanguage'])) {
        $_SESSION['glpilanguage'] = 'fr_FR';
    }

    ini_set('display_errors', 'On');
    error_reporting(E_ALL | E_STRICT);
    set_error_handler(array(
        'Toolbox',
        'userErrorHandlerDebug'
    ));

    $_SESSION['glpi_use_mode']       = 0;
    $_SESSION['glpiparententities']  = '';
    $_SESSION['glpishowallentities'] = TRUE;

    ob_end_clean();

    header("server-type: glpi/armadito " . PLUGIN_ARMADITO_VERSION);
}
function writeHttpHeader()
{
    header("Content-Type: application/json");
    header("X-ArmaditoPlugin-Version: " . PLUGIN_ARMADITO_VERSION);
}

function writeHttpErrorResponse($error_message, $code)
{
    PluginArmaditoLog::Error($error_message);

    http_response_code($code);
    writeHttpHeader();
    echo '{"error" : "'.$error_message.'"}';
}

function writeHttpOKResponse($response_content)
{
    writeHttpHeader();
    echo $response_content;
}

function checkPluginInstallation()
{
    if (!class_exists("PluginArmaditoAgent")) {
        writeHttpErrorResponse("Plugin Armadito is not installed.");
        session_destroy();
        exit();
    }
}

function isValidPOSTRequest($rawdata)
{
    return !empty($rawdata);
}
