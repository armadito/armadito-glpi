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

include_once("toolbox.class.php");

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginArmaditoStateModule extends CommonDBTM
{
    protected $jobj;
    protected $state_jobj;
    protected $agentid;

    function __construct()
    {
    }

    function init($agent_id, $state_jobj, $jobj)
    {
        $this->agentid    = $agent_id;
        $this->state_jobj = $state_jobj;
        $this->jobj       = $jobj;

        PluginArmaditoToolbox::logIfExtradebug('pluginArmadito-statemodule', 'New PluginArmaditoStateModule object.');
    }

    static function getTypeName($nb = 0)
    {
        return __('State Module', 'armadito');
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item->getType() == 'PluginArmaditoStatedetail') {
            return __('Antivirus modules', 'armadito');
        }
        return '';
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item->getType() == 'PluginArmaditoStatedetail') {
            $pfStatemodule = new self();
            $pfStatemodule->showForm($item->fields["plugin_armadito_agents_id"]);
        }
        return TRUE;
    }

    function showForm($agent_id, $options = array())
    {
        PluginArmaditoToolbox::validateInt($agent_id);

        echo "<table class='tab_cadre_fixe'>";
        echo "<tr>";
        echo "<th >" . __('Module', 'armadito') . "</th>";
        echo "<th >" . __('Version', 'armadito') . "</th>";
        echo "<th >" . __('Update Status', 'armadito') . "</th>";
        echo "<th >" . __('Last update', 'armadito') . "</th>";
        echo "</tr>";

        $av_modules = $this->findModules($agent_id);

        foreach ($av_modules as $data) {
            echo "<tr class='tab_bg_1'>";
            echo "<td align='center'>" . htmlspecialchars($data["module_name"]) . "</td>";
            echo "<td align='center'>" . htmlspecialchars($data["module_version"]) . "</td>";
            echo "<td align='center'>" . htmlspecialchars($data["module_update_status"]) . "</td>";
            echo "<td align='center'>" . htmlspecialchars($data["module_last_update"]) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }

    function toJson()
    {
        return '{}';
    }

    function run()
    {
        if ($this->isStateModuleinDB()) {
            $error = $this->updateStateModule();
        } else {
            $error = $this->insertStateModule();
        }
        return $error;
    }

    function findModules($agent_id)
    {
        global $DB;

        $query = "SELECT * FROM `glpi_plugin_armadito_statedetails`
                 WHERE `plugin_armadito_agents_id`='" . $agent_id . "'";

        $data = array();
        if ($result = $DB->query($query) && $DB->numrows($result)) {
            while ($line = $DB->fetch_assoc($result)) {
                $data[$line['id']] = $line;
            }
        }

        return $data;
    }

    function isStateModuleinDB()
    {
        global $DB;

        $query = "SELECT id FROM `glpi_plugin_armadito_statedetails`
                 WHERE `plugin_armadito_agents_id`=? AND `module_name`=?";

        $stmt = $DB->prepare($query);

        if (!$stmt) {
            throw new InvalidArgumentException(sprintf("State module select preparation failed."));
        }

        if (!$stmt->bind_param('is', $agent_id, $module_name)) {
            $stmt->close();
            throw new InvalidArgumentException(sprintf("State module select bind_param failed. (%d) %s", $stmt->errno, $stmt->error));
        }

        $agent_id    = $this->agentid;
        $module_name = $this->jobj->name;

        if (!$stmt->execute()) {
            $stmt->close();
            throw new InvalidArgumentException(sprintf("State module select execution failed. (%d) %s", $stmt->errno, $stmt->error));
        }

        if (!$stmt->store_result()) {
            $stmt->close();
            throw new InvalidArgumentException(sprintf("State module select store_result failed. (%d) %s", $stmt->errno, $stmt->error));
        }

        if ($stmt->num_rows() > 0) {
            $stmt->free_result();
            $stmt->close();
            return true;
        }

        $stmt->free_result();
        $stmt->close();
        return false;
    }

    function insertStateModule()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $params["itemlink"]["type"]                  = "s";

        $query = "NewStateModule";
        $dbmanager->addQuery($query, "INSERT", "glpi_plugin_armadito_statedetails", $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->setQueryValue($query, "itemlink", "ShowAll");
        $dbmanager->executeQuery($query);
    }

    function updateStateModule()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $query = "UpdateStateModule";

        $dbmanager->addQuery($query, "UPDATE", "glpi_plugin_armadito_statedetails", $params, array(
            "plugin_armadito_agents_id",
            "module_name"
        ));
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);
    }

    function setCommonQueryParams()
    {
        $params["plugin_armadito_agents_id"]["type"] = "i";
        $params["module_name"]["type"]               = "s";
        $params["module_version"]["type"]            = "s";
        $params["module_update_status"]["type"]      = "s";
        $params["module_last_update"]["type"]        = "s";
        return $params;
    }

    function setCommonQueryValues($dbmanager, $query)
    {
        $dbmanager->setQueryValue($query, "plugin_armadito_agents_id", $this->agentid);
        $dbmanager->setQueryValue($query, "module_name", $this->jobj->name);
        $dbmanager->setQueryValue($query, "module_version", "unknown");
        $dbmanager->setQueryValue($query, "module_update_status", $this->jobj->mod_status);
        $dbmanager->setQueryValue($query, "module_last_update", date("Y-m-d H:i:s", $this->jobj->mod_update_timestamp));
        return $dbmanager;
    }
}
?>
