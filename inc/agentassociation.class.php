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
    protected $uuid;
    protected $computerid;
    protected $agentid;

    function __construct($uuid = "")
    {
        $this->uuid = PluginArmaditoToolbox::validateInt($uuid);
        $this->agentid = -1;
        $this->computerid = -1;
    }

    function setComputerId($computerid)
    {
        $this->computerid = PluginArmaditoToolbox::validateInt($computerid);
    }

    function setAgentId($agentid)
    {
        $this->agentid = PluginArmaditoToolbox::validateInt($agentid);
    }

    function getAgentIdFromDB()
    {
        $table = "glpi_plugin_armadito_agents";

        if ($this->computerid > 0)
        {
            return $this->getTableId($table, "computers_id", $this->computerid);
        }

        return $this->getTableId($table, "uuid", $this->uuid);
    }

    function getComputerIdFromDB()
    {
        $table = "glpi_computers";

        return $this->getTableId($table, "uuid", $this->uuid);
    }

    protected function getTableId( $table, $where_label, $where_value )
    {
        global $DB;

        $query = "SELECT id FROM `".$table."`
                WHERE `". $where_label ."`='". $where_value ."'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getTableId : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return $data["id"];
        }
        return -1; 
    }

    static function FusionInventoryHook($params = array())
    {
        if (!empty($params)
         && isset($params['inventory_data'])
         && !empty($params['inventory_data'])) {

             $uuid = $params['inventory_data']['Computer']['uuid'];

             $association = new PluginArmaditoAgentAssociation($uuid);
             $agent_id = $association->getAgentIdFromDB();

             if ($agent_id > 0)
             {
                $association->setAgentId($agent_id);
                $association->setComputerId($params['computers_id']);
                $association->run();
             }
        }
    }

    function run()
    {
        $paAgent = new PluginArmaditoAgent();

        if ($paAgent->getFromDB($this->agentid))
        {
            $input                 = array();
            $input['id']           = $this->agentid;
            $input['computers_id'] = $this->computerid;

            return $paAgent->update($input);
        }
    }
}

?>
