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
 * Class dealing with Armadito AV state
 **/
class PluginArmaditoAlert extends CommonDBTM
{
    protected $jobj;
    protected $agentid;
    protected $agent;

    function __construct()
    {
        //
    }

    function initFromJson($jobj)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($jobj->agent_id);
        $this->agent   = new PluginArmaditoAgent();
        $this->agent->initFromDB($this->agentid);
        $this->jobj = $jobj;
    }

    function toJson()
    {
        return '{}';
    }

    static function canCreate()
    {
        if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
            return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w');
        }
        return false;
    }

    static function canView()
    {

        if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
            return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w' || $_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'r');
        }
        return false;
    }

    function getSearchOptions()
    {

        $tab           = array();
        $tab['common'] = __('State', 'armadito');

        $i = 1;

        $tab[$i]['table']         = 'glpi_plugin_armadito_agents';
        $tab[$i]['field']         = 'id';
        $tab[$i]['name']          = __('Agent Id', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoAgent';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'name';
        $tab[$i]['name']          = __('Threat name', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'filepath';
        $tab[$i]['name']          = __('Filepath', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_antiviruses';
        $tab[$i]['field']         = 'fullname';
        $tab[$i]['name']          = __('Antivirus', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoAntivirus';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'module_name';
        $tab[$i]['name']          = __('Module', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'impact_severity';
        $tab[$i]['name']          = __('Severity', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'detection_time';
        $tab[$i]['name']          = __('Detection Time', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        return $tab;
    }

    /* Insert Alerts in database
     *
     * @return PluginArmaditoError obj
     **/
    function run()
    {
        $error = new PluginArmaditoError();

        $error = $this->insertAlert();

        $error->setMessage(0, 'Alerts successfully inserted.');
        return $error;
    }

    /**
     * Insert alert in database
     *
     * @return PluginArmaditoError obj
     **/
    function insertAlert()
    {
        $error     = new PluginArmaditoError();
        $dbmanager = new PluginArmaditoDbManager();
        $dbmanager->init();

        $params["plugin_armadito_agents_id"]["type"]       = "i";
        $params["plugin_armadito_antiviruses_id"]["type"]  = "i";
        $params["name"]["type"]                            = "s";
        $params["module_name"]["type"]                     = "s";
        $params["filepath"]["type"]                        = "s";
        $params["impact_severity"]["type"]                 = "s";
        $params["detection_time"]["type"]                  = "s";

        $query_name = "NewAlert";
        $dbmanager->addQuery($query_name, "INSERT", $this->getTable(), $params);

        if (!$dbmanager->prepareQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        if (!$dbmanager->bindQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $antivirus = $this->agent->getAntivirus();

        $dbmanager->setQueryValue($query_name, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query_name, "plugin_armadito_antiviruses_id", $antivirus->getId());
        $dbmanager->setQueryValue($query_name, "name", $this->jobj->task->obj->alert->module_specific->value);
        $dbmanager->setQueryValue($query_name, "filepath", $this->jobj->task->obj->alert->uri->value);
        $dbmanager->setQueryValue($query_name, "module_name", $this->jobj->task->obj->alert->module->value);
        $dbmanager->setQueryValue($query_name, "detection_time", date("Y-m-d H:i:s", 1474544183)); # TODO
        $dbmanager->setQueryValue($query_name, "impact_severity", $this->jobj->task->obj->alert->level->value);

        if (!$dbmanager->executeQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->closeQuery($query_name);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        if ($this->id > 0) {
            PluginArmaditoToolbox::validateInt($this->id);
            $error->setMessage(0, 'New Alert successfully added in database.');
        } else {
            $error->setMessage(1, 'Unable to get new State Id');
        }
        return $error;
    }
}
?>
