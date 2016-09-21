<?php

/**
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

class AgentTest extends RestoreDatabase_TestCase {

   /**
    * @test
    */
   public function addAgent() {
      $agent = new PluginArmaditoAgent();

	  $jobj = (object) array(
				'agent_id' => '0',
				'agent_version' => '0.1.0_02',
				'fusion_id'   => 'lw007-2016-07-21-09-22-29',
				'fingerprint' => '19af0324e1289255123101f3aaef97311947528cd98822775b5429160bf4ad58',
				'task' => array(
					'name' => 'Enrollment',
					'antivirus'   => (object) array(
						'name' => 'Armadito',
						'version' => '0.10.2'
					 ),
					'msg' => ''
				)
	  );

	  $agent->initFromJson($jobj);
	  $error = $agent->insertAgentInDB();
      $this->assertEquals(0, $error->getCode(), $error->getMessage());

      return $agent;
   }
}

