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

include_once("toolbox.class.php");

class PluginArmaditoAgentAssociation
{
    static function getAgentIdForComputerId($computers_id)
    {
        global $DB;

        PluginArmaditoToolbox::validateInt($computers_id);

        $query = "SELECT id FROM `glpi_plugin_armadito_agents`
                WHERE `computers_id`='" . $computers_id . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getAgentIdForComputerId : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return PluginArmaditoToolbox::validateInt($data["id"]);
        }

        return -1;
    }

    static function getAgentIdForUUID($uuid)
    {
        global $DB;

        PluginArmaditoToolbox::validateUUID($uuid);

        $query = "SELECT id FROM `glpi_plugin_armadito_agents`
                WHERE `uuid`='" . $uuid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getAgentIdForUUID : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return PluginArmaditoToolbox::validateInt($data["id"]);
        }

        return -1;
    }

    static function getComputerIdForUUID($uuid)
    {
        global $DB;

        PluginArmaditoToolbox::validateUUID($uuid);

        $query = "SELECT id FROM `glpi_computers`
                WHERE `uuid`='". $uuid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getComputerIdForUUID : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return PluginArmaditoToolbox::validateInt($data["id"]);
        }

        return -1;
    }

    static function FusionInventoryHook($params = array())
    {
        if (!empty($params)
         && isset($params['inventory_data'])
         && !empty($params['inventory_data'])) {

             $uuid = $params['inventory_data']['Computer']['uuid'];
             $agent_id = PluginArmaditoAgentAssociation::getAgentIdForUUID($uuid);

             if($agent_id > 0)
             {
                $computers_id = PluginArmaditoToolbox::validateInt($params['computers_id']);
                PluginArmaditoAgentAssociation::withComputer($agent_id, $computers_id);
             }
        }
    }

    static function withComputer( $agent_id, $computers_id )
    {
            $pfAgent = new PluginArmaditoAgent();

            if ($pfAgent->getFromDB($agent_id))
            {
                $input                = array();
                $input['id']          = $agent_id;
                $input['computers_id'] = $computers_id;

                return $pfAgent->update($input);
            }
    }
}

?>
