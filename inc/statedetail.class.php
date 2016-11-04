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

class PluginArmaditoStatedetail extends PluginArmaditoCommonDBTM
{
    static function getTypeName($nb = 0)
    {
        return __('Antivirus State', 'armadito');
    }

    function getSearchOptions()
    {
        $search_options = new PluginArmaditoSearchoptions('StateDetails');

        $items['Agent Id']             = new PluginArmaditoSearchitemlink('id', 'glpi_plugin_armadito_agents', 'PluginArmaditoAgent');
        $items['Module Name']          = new PluginArmaditoSearchtext('module_name', $this->getTable());
        $items['Module Version']       = new PluginArmaditoSearchtext('module_version', $this->getTable());
        $items['Module Update Status'] = new PluginArmaditoSearchtext('module_update_status', $this->getTable());
        $items['Module Last Update']   = new PluginArmaditoSearchtext('module_last_update', $this->getTable());

        return $search_options->get($items);
    }

    function defineTabs($options = array())
    {

        $ong = array();
        $this->addDefaultFormTab($ong);
        $this->addStandardTab('PluginArmaditoStateModule', $ong, $options);
        $this->addStandardTab('Log', $ong, $options);

        return $ong;
    }

    function showForm($table_id, $options = array())
    {

        PluginArmaditoToolbox::validateInt($table_id);

        $this->initForm($table_id, $options);
        $options['colspan'] = 4;
        $this->showFormHeader($options);

        $state = new PluginArmaditoState();
        $state->setAgentId($this->fields["plugin_armadito_agents_id"]);
        $state_id = $state->getTableIdForAgentId("glpi_plugin_armadito_states");
        $state->initFromDB($state_id);
        $agent     = $state->getAgent();
        $antivirus = $agent->getAntivirus();

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Id') . " :</td>";
        echo "<td align='center'>";
        echo "<b>" . htmlspecialchars($this->fields["id"]) . "</b>";
        echo "</td>";
        echo "<td>" . __('Agent Id', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo "<b>" . htmlspecialchars($this->fields["plugin_armadito_agents_id"]) . "</b>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Antivirus Name', 'armadito') . " :</td>";
        echo "<td align='center'>";
        echo "<b>" . htmlspecialchars($antivirus->getAVName()) . "</b>";
        echo "</td>";

        echo "<td>" . __('Antivirus Version', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo "" . htmlspecialchars($antivirus->getAVVersion()) . "";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Antivirus On-access', 'armadito') . " :</td>";
        echo "<td align='center'>";
        echo "" . htmlspecialchars($state->fields["realtime_status"]) . "";
        echo "</td>";

        echo "<td>" . __('Antivirus Service', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo "" . htmlspecialchars($state->fields["service_status"]) . "";
        echo "</td>";
        echo "</tr>";
    }
}
?>
