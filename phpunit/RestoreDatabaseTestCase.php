<?php
include_once('bootstrap.php');
include_once('commonfunction.php');

include_once(GLPI_ROOT . "/config/based_config.php");
include_once(GLPI_ROOT . "/inc/dbmysql.class.php");
include_once(GLPI_CONFIG_DIR . "/config_db.php");


/*
 * Helper class to restore database from some SQL restore point file
 */

abstract class RestoreDatabaseTestCase extends CommonTestCase
{

    public static function setUpBeforeClass()
    {
        restore_database();
    }
}
?>
