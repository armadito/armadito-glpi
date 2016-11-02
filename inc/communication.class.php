<?php

/*
Copyright (C) 2010-2016 by the FusionInventory Development Team.
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
include_once("state.class.php");

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

/*
 * Class to communicate with agents using JSON
 **/
class PluginArmaditoCommunication
{
    protected $message;
    protected $status_code;

    function __construct()
    {
        $this->message = "";
    }

    function init()
    {
        ob_start();
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "0");
        ini_set('display_errors', 1);

        if (session_id() == "") {
            session_start();
        }

        if (!defined('GLPI_ROOT')) {
            include_once("../../../inc/includes.php");
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

    function getMessage()
    {
        return $this->message;
    }

    function sendMessage()
    {
        if (!$this->message) {
            return;
        }

        header("Content-Type: application/json");
        header("X-ArmaditoPlugin-Version: " . PLUGIN_ARMADITO_VERSION);

        if ($this->status_code > 200) {
            http_response_code($this->status_code);
        }

        echo $this->message;
    }

    function setMessage($message, $status = 200)
    {
        $this->status_code = $status;
        $this->message     = $message;
    }

    function setJsonErrorMessage($message, $status = 200)
    {
        $this->status_code = $status;
        $this->message     = '{ "error" : "'.$message.'" }';
    }
}
?>
