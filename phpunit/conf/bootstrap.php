<?php

if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', realpath('../../../'));
    define('ARMADITO_ROOT', GLPI_ROOT . DIRECTORY_SEPARATOR . '/plugins/armadito');
    define('TESTS_ROOT', GLPI_ROOT . "/plugins/armadito/phpunit/");
    set_include_path(get_include_path() . PATH_SEPARATOR . GLPI_ROOT . PATH_SEPARATOR . TESTS_ROOT);
}

require_once(GLPI_ROOT . "/inc/autoload.function.php");

include_once("CommonTestCase.php");
include_once("RestoreDatabaseTestCase.php");
include_once("LogTest.php");
include_once("commonfunction.php");

?>
