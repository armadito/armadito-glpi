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

chdir(dirname($_SERVER["SCRIPT_FILENAME"]));

include("../../../inc/includes.php");
require_once("../phpunit/vendor/autoload.php");

use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;

$specs = new OptionCollection;
$specs->add('f|force-upgrade:', 'Force upgrade.');
$specs->add('o|optimize:', 'Optimize tables.');
$specs->add('h|help:', 'Print usage.');
$specs->add('as-user:', 'Do install/upgrade as specified USER.' )
    ->isa('String');

$parser = new OptionParser($specs);
echo "Enabled options: \n";
try {

    $result = $parser->parse( $argv );

    if(isset($result->keys['force-upgrade'])) {
        $args['--force-upgrade'] = $result->keys['force-upgrade'];
    }
    if(isset($result->keys['as-user'])) {
        $args['--as-user'] = $result->keys['as-user']->value;
    }
    if(isset($result->keys['optimize'])) {
        $args['--optimize'] = $result->keys['optimize'];
    }

} catch( Exception $e ) {
    echo $e->getMessage();

    $printer = new ConsoleOptionPrinter();
    echo $printer->render($specs);
}

// Init debug variable
$_SESSION['glpi_use_mode'] = Session::DEBUG_MODE;
$_SESSION['glpilanguage']  = "en_GB";

Session::LoadLanguage();

// Only show errors
$CFG_GLPI["debug_sql"]        = $CFG_GLPI["debug_vars"] = 0;
$CFG_GLPI["use_log_in_files"] = 1;
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$DB = new DB();
if (!$DB->connected) {
    die("No DB connection\n");
}

/*---------------------------------------------------------------------*/

if (!TableExists("glpi_configs")) {
    die("GLPI not installed\n");
}

$plugin = new Plugin();

require_once(GLPI_ROOT . "/plugins/armadito/install/climigration.class.php");
include(GLPI_ROOT . "/plugins/armadito/install/update.php");
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
echo "Version to update: " . PLUGIN_ARMADITO_VERSION;

// To prevent problem of execution time
ini_set("max_execution_time", "0");
ini_set("memory_limit", "-1");
ini_set("session.use_cookies", "0");
$mess = '';
if (($current_version != PLUGIN_ARMADITO_VERSION) && $current_version != '0') {
    $mess = "Update needed.";
} else if ($current_version == PLUGIN_ARMADITO_VERSION) {
    $mess = "No migration needed.";
} else {
    $mess = "installation done.";
}

$migration->displayWarning($mess);

if (isset($args['--force-upgrade'])) {
    define('FORCE_UPGRADE', TRUE);
}

if (isset($args['--as-user'])) {
    $user = new User();
    $user->getFromDBbyName($args['--as-user']);
    $auth                = new Auth();
    $auth->auth_succeded = true;
    $auth->user          = $user;
    Session::init($auth);
}

$plugin->getFromDBbyDir("armadito");
print("Installing Plugin...\n");
$plugin->install($plugin->fields['id']);
print("Install Done\n");
print("Activating Plugin...\n");
$plugin->activate($plugin->fields['id']);
print("Activation Done\n");
print("Loading Plugin...\n");
$plugin->load("armadito");
print("Load Done...\n");


if (isset($args['--optimize'])) {

    $migration->displayTitle(__('Optimizing tables'));

    DBmysql::optimize_tables($migration);

    $migration->displayWarning("Optimize done.");
}
