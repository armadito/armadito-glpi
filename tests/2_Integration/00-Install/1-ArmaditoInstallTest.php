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

require_once(GLPI_ROOT . "/plugins/armadito/install/climigration.class.php");
include(GLPI_ROOT . "/plugins/armadito/install/update.php");
include(GLPI_ROOT . "/plugins/armadito/setup.php");
include(GLPI_ROOT . "/plugins/armadito/hook.php");

class ArmaditoInstallTest extends CommonTestCase
{
    public function testInstall()
    {
        global $DB;

        $DB = new DB();
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

        $this->installPlugin();

        $GLPIlog = new GLPIlogs();
        $GLPIlog->testSQLlogs();
        $GLPIlog->testPHPlogs();

        $FusinvDBTest = new ArmaditoDB();
        $FusinvDBTest->checkInstall("armadito", "install new version");

        PluginArmaditoConfig::loadCache();
    }

    protected function installPlugin()
    {
        $_SESSION['glpi_use_mode'] = Session::DEBUG_MODE;
        $_SESSION['glpilanguage']  = "en_GB";

        Session::LoadLanguage();

        $CFG_GLPI["debug_sql"]        = $CFG_GLPI["debug_vars"] = 0;
        $CFG_GLPI["use_log_in_files"] = 1;

        ini_set('display_errors', 'On');
        error_reporting(E_ALL | E_STRICT);

        $DB = new DB();
        if (!$DB->connected) {
            die("No DB connection\n");
        }

        if (!TableExists("glpi_configs")) {
            die("GLPI not installed\n");
        }

        $plugin = new Plugin();
        $current_version = pluginArmaditoGetCurrentVersion();
        $migration = new CliMigration($current_version);

        if (!isset($current_version)) {
            $current_version = 0;
        }
        if ($current_version == '0') {
            $migration->displayWarning("***** Install process of plugin ARMADITO *****");
        } else {
            $migration->displayWarning("***** Update process of plugin ARMADITO *****");
        }

        $migration->displayWarning("Current Armadito plugin version: $current_version");
        $migration->displayWarning("Version to update: " . PLUGIN_ARMADITO_VERSION);

        echo "Current Armadito plugin version: $current_version\n";
        echo "Version to update: " . PLUGIN_ARMADITO_VERSION ."\n";

        // To prevent problem of execution time
        ini_set("max_execution_time", "0");
        ini_set("memory_limit", "-1");
        ini_set("session.use_cookies", "0");

        if (($current_version != PLUGIN_ARMADITO_VERSION) && $current_version != '0') {
            $mess = "Update needed.";
        } else if ($current_version == PLUGIN_ARMADITO_VERSION) {
            $mess = "No migration needed.";
        } else {
            $mess = "installation done.";
        }

        $migration->displayWarning($mess);

        $user = new User();
        $user->getFromDBbyName("glpi");
        $auth                = new Auth();
        $auth->auth_succeded = true;
        $auth->user          = $user;
        Session::init($auth);

        $plugin->getFromDBbyDir("armadito");

        global $HEADER_LOADED;
        $HEADER_LOADED = true;

        $plugin->install($plugin->fields['id']);
        $plugin->activate($plugin->fields['id']);
        echo "Plugin state : ".$plugin->fields['state'];

        $plugin->load("armadito");

        registerPluginArmaditoClasses();
        setPluginArmaditoHooks(false);
    }
}
?>
