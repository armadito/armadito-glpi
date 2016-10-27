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
class PluginArmaditoState extends CommonDBTM
{
    protected $id;
    protected $agentid;
    protected $agent;
    protected $jobj;

    static function getTypeName($nb = 0)
    {
        return __('State', 'armadito');
    }

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

    function initFromDB($state_id)
    {
        global $DB;

        if ($this->getFromDB($state_id)) {
            $this->id    = $state_id;
            $this->agent = new PluginArmaditoAgent();
            $this->agent->initFromDB($this->fields["plugin_armadito_agents_id"]);
        } else {
            PluginArmaditoToolbox::logE("Unable to get State DB fields");
        }
    }

    function setAgentId($agentid_)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($agentid_);
    }

    static function getAvailableStatuses()
    {
        $gcolors = PluginArmaditoColorToolbox::getGoldenColors();

        return array(
            "non-available" => "#dedede",
            "up-to-date" => $gcolors["green1"],
            "critical" => $gcolors["red"],
            "late" => $gcolors["orange"]
        );
    }

    function toJson()
    {
        return '{}';
    }

    function getAgent()
    {
        return $this->agent;
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
        $tab[$i]['field']         = 'update_status';
        $tab[$i]['name']          = __('Update Status', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'last_update';
        $tab[$i]['name']          = __('Last Update', 'armadito');
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
        $tab[$i]['field']         = 'realtime_status';
        $tab[$i]['name']          = __('Antivirus On-access', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'service_status';
        $tab[$i]['name']          = __('Antivirus Service', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_statedetails';
        $tab[$i]['field']         = 'itemlink';
        $tab[$i]['name']          = __('Details', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoStatedetail';
        $tab[$i]['massiveaction'] = FALSE;

        return $tab;
    }

    /**
     * Insert or Update states
     *
     * @return PluginArmaditoError obj
     **/
    function run()
    {

        // if AV is Armadito, we also update states of each modules
        if ($this->jobj->task->antivirus->name == "Armadito") {
            foreach ($this->jobj->task->obj->modules as $jobj_module) {
                $module = new PluginArmaditoStateModule();
                $module->init($this->agentid, $this->jobj, $jobj_module);
                $error = $module->run();
                if ($error->getCode() != 0) {
                    return $error;
                }
            }
        }

        $statedetails_id = $this->getTableIdForAgentId("glpi_plugin_armadito_statedetails");

        // Update global Antivirus state
        if ($this->isStateinDB()) {
            $error = $this->updateState($statedetails_id);
        } else {
            $error = $this->insertState($statedetails_id);
        }

        return $error;
    }

    /**
     * Check if state is already in database
     *
     * @return TRUE or FALSE
     **/
    function isStateinDB()
    {
        global $DB;

        $query = "SELECT update_status FROM `glpi_plugin_armadito_states`
                 WHERE `plugin_armadito_agents_id`='" . $this->agentid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new RuntimeException(sprintf('Error isStateinDB : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get statedetails table id for itemlink
     *
     * @return id
     **/
    function getTableIdForAgentId($table)
    {
        global $DB;

        $id    = 0;
        $query = "SELECT id FROM `" . $table . "`
                 WHERE `plugin_armadito_agents_id`='" . $this->agentid . "'";

        $ret = $DB->query($query);

        if (!$ret) {
            throw new RuntimeException(sprintf('Error getTableIdForAgentId : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data = $DB->fetch_assoc($ret);
            $id   = $data["id"];
        }

        return $id;
    }

    /**
     * Insert state in database
     *
     * @return PluginArmaditoError obj
     **/
    function insertState($stateid)
    {
        $error     = new PluginArmaditoError();
        $dbmanager = new PluginArmaditoDbManager();
        $dbmanager->init();

        $params["plugin_armadito_agents_id"]["type"]       = "i";
        $params["plugin_armadito_antiviruses_id"]["type"]  = "i";
        $params["plugin_armadito_statedetails_id"]["type"] = "i";
        $params["update_status"]["type"]                   = "s";
        $params["last_update"]["type"]                     = "s";
        $params["realtime_status"]["type"]                 = "s";
        $params["service_status"]["type"]                  = "s";

        $query_name = "NewState";
        $dbmanager->addQuery($query_name, "INSERT", $this->getTable(), $params);

        if (!$dbmanager->prepareQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        if (!$dbmanager->bindQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $antivirus = $this->agent->getAntivirus();

        $dbmanager->setQueryValue($query_name, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query_name, "plugin_armadito_statedetails_id", $stateid);
        $dbmanager->setQueryValue($query_name, "plugin_armadito_antiviruses_id", $antivirus->getId());
        $dbmanager->setQueryValue($query_name, "update_status", $this->jobj->task->obj->global_status);
        $dbmanager->setQueryValue($query_name, "last_update", date("Y-m-d H:i:s", $this->jobj->task->obj->global_update_timestamp));
        $dbmanager->setQueryValue($query_name, "realtime_status", "unknown");
        $dbmanager->setQueryValue($query_name, "service_status", "unknown");

        if (!$dbmanager->executeQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->closeQuery($query_name);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        if ($this->id > 0) {
            PluginArmaditoToolbox::validateInt($this->id);
            $error->setMessage(0, 'New State successfully added in database.');
        } else {
            $error->setMessage(1, 'Unable to get new State Id');
        }
        return $error;
    }

    /**
     * Uptate state in database
     *
     * @return PluginArmaditoError obj
     **/
    function updateState($stateid)
    {
        $error     = new PluginArmaditoError();
        $dbmanager = new PluginArmaditoDbManager();
        $dbmanager->init();

        $params["plugin_armadito_agents_id"]["type"]       = "i";
        $params["plugin_armadito_antiviruses_id"]["type"]  = "i";
        $params["plugin_armadito_statedetails_id"]["type"] = "i";
        $params["update_status"]["type"]                   = "s";
        $params["last_update"]["type"]                     = "s";
        $params["realtime_status"]["type"]                 = "s";
        $params["service_status"]["type"]                  = "s";

        $query_name = "UpdateState";
        $dbmanager->addQuery($query_name, "UPDATE", $this->getTable(), $params, "plugin_armadito_agents_id");

        if (!$dbmanager->prepareQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        if (!$dbmanager->bindQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $antivirus = $this->agent->getAntivirus();
        PluginArmaditoToolbox::logE("Set AV_id = " . $antivirus->getId());

        $dbmanager->setQueryValue($query_name, "plugin_armadito_statedetails_id", $stateid);
        $dbmanager->setQueryValue($query_name, "plugin_armadito_antiviruses_id", $antivirus->getId());
        $dbmanager->setQueryValue($query_name, "update_status", $this->jobj->task->obj->global_status);
        $dbmanager->setQueryValue($query_name, "last_update", date("Y-m-d H:i:s", $this->jobj->task->obj->global_update_timestamp));
        $dbmanager->setQueryValue($query_name, "realtime_status", "unknown");
        $dbmanager->setQueryValue($query_name, "service_status", "unknown");
        $dbmanager->setQueryValue($query_name, "plugin_armadito_agents_id", $this->agentid); # WHERE

        if (!$dbmanager->executeQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->closeQuery($query_name);
        $error->setMessage(0, 'State successfully updated in database.');
        return $error;
    }
}
?>
