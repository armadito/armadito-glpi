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

class NewComputersTest extends CommonTestCase
{
    /**
     * @test
     */
    public function NewInventories()
    {
        $this->addComputer(1, "lw007", "4C4C4544-0033-4A10-8051-BBBBBBBBBBBB");
        $this->addComputer(2, "lw009", "4C4C4544-0033-4A10-8051-YYYYYYYYYYYY");
    }

    protected function addComputer($computer_id, $name, $uuid)
    {
        global $DB;

        $query = "INSERT INTO `glpi`.`glpi_computers` (`id`, `entities_id`, `name`, `serial`, `otherserial`, `contact`, `contact_num`, `users_id_tech`, `groups_id_tech`, `comment`, `date_mod`, `operatingsystems_id`, `operatingsystemversions_id`, `operatingsystemservicepacks_id`, `operatingsystemarchitectures_id`, `os_license_number`, `os_licenseid`, `os_kernel_version`, `autoupdatesystems_id`, `locations_id`, `domains_id`, `networks_id`, `computermodels_id`, `computertypes_id`, `is_template`, `template_name`, `manufacturers_id`, `is_deleted`, `is_dynamic`, `users_id`, `groups_id`, `states_id`, `ticket_tco`, `uuid`, `date_creation`, `is_recursive`) VALUES (NULL, '0', '".$name."', NULL, NULL, NULL, NULL, '0', '0', NULL, NULL, '0', '0', '0', '0', NULL, NULL, NULL, '0', '0', '0', '0', '0', '0', '0', NULL, '0', '0', '0', '0', '0', '0', '0.0000', '". $uuid ."', NULL, '0');";

        $DB->query($query);

        $this->fusionInventoryHook($computer_id, $uuid);
    }

    protected function fusionInventoryHook($computer_id, $computer_uuid)
    {
         $params = array( "computers_id"   => $computer_id,
                          "inventory_data" => array( "Computer" => array( "uuid" => $computer_uuid) ));

         PluginArmaditoAgentAssociation::checkHookParams($params);

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

