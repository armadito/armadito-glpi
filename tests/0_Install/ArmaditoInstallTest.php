<?php

/*
Copyright (C) 2010-2016 by the FusionInventory Development Team.
Copytight (C) 2016 by Teclib'

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

*/

require_once("0_Install/ArmaditoDB.php");

class ArmaditoInstallTest extends CommonTestCase
{
    public function testInstall()
    {
        global $DB;
        $DB->connect();
        $this->assertTrue($DB->connected, "Problem connecting to the Database");

        // Delete if Table of Armadito or Tracker yet in DB
        $query  = "SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW'";
        $result = $DB->query($query);
        while ($data = $DB->fetch_array($result)) {
            if (strstr($data[0], "armadito")) {
                $DB->query("DROP VIEW " . $data[0]);
            }
        }

        $query  = "SHOW TABLES";
        $result = $DB->query($query);
        while ($data = $DB->fetch_array($result)) {
            if (strstr($data[0], "tracker") || strstr($data[0], "armadito")) {
                $DB->query("DROP TABLE " . $data[0]);
            }
        }

        $output     = array();
        $returncode = 0;
        exec("php -f " . ARMADITO_ROOT . "/scripts/cli_install.php -- --as-user 'glpi'", $output, $returncode);
        $this->assertEquals(0, $returncode, "Error when installing plugin in CLI mode\n" . implode("\n", $output));

        $GLPIlog = new GLPIlogs();
        $GLPIlog->testSQLlogs();
        $GLPIlog->testPHPlogs();

        $FusinvDBTest = new ArmaditoDB();
        $FusinvDBTest->checkInstall("armadito", "install new version");

        PluginArmaditoConfig::loadCache();
    }
}
?>
