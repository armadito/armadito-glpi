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

        $rows[] = new PluginArmaditoFormRow('Id', $this->fields["id"]);
        $rows[] = new PluginArmaditoFormRow('Agent Id', $this->fields["plugin_armadito_agents_id"]);
        $rows[] = new PluginArmaditoFormRow('Antivirus', $antivirus->getFullName());
        $rows[] = new PluginArmaditoFormRow('Update Status', $state->fields["update_status"]);
        $rows[] = new PluginArmaditoFormRow('Last Update', $state->fields["last_update"]);
        $rows[] = new PluginArmaditoFormRow('Antivirus On-access', $state->fields["realtime_status"]);
        $rows[] = new PluginArmaditoFormRow('Antivirus Service', $state->fields["service_status"]);

        foreach( $rows as $row )
        {
            $row->write();
        }
    }
}
?>
