<?php
include_once('./conf/bootstrap.php');
include_once('mysql.php');

include_once(GLPI_ROOT . "/inc/based_config.php");
include_once(GLPI_ROOT . "/inc/dbmysql.class.php");
include_once(GLPI_CONFIG_DIR . "/config_db.php");


abstract class CommonTestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        global $DB, $CFG_GLPI;
        $DB = new DB();

        // Force profile in session to SuperAdmin
        $_SESSION['glpiprofiles'] = array(
            '4' => array(
                'entities' => 0
            )
        );
        $_SESSION['glpi_plugin_armadito_profile']['unmanaged'] = 'w';
        $_SESSION["glpi_plugin_armadito_profile"]['armadito'] = 'w';
        $_SESSION['glpiactiveentities'] = array(
            0,
            1
        );
        $_SESSION['glpi_use_mode'] = Session::NORMAL_MODE;


        require(GLPI_ROOT . "/inc/includes.php");
        $plugin = new Plugin();
        $DB->connect();
        $plugin->getFromDBbyDir("armadito");
        $plugin->activate($plugin->fields['id']);

        file_put_contents(GLPI_ROOT . "/files/_log/sql-errors.log", '');
        file_put_contents(GLPI_ROOT . "/files/_log/php-errors.log", '');

        $dir = GLPI_ROOT . "/files/_files/_plugins/armadito";
        if (file_exists($dir)) {
            $objects = scandir($dir);

            foreach ($objects as $object)
            {
                if ($object != "."
                 && $object != ".."
                 && filetype($dir . "/" . $object) != "dir")
                {
                    unlink($dir . "/" . $object);
                }
            }
        }

        include_once(GLPI_ROOT . "/inc/timer.class.php");
        $_SERVER['PHP_SELF'] = Html::cleanParametersURL($_SERVER['PHP_SELF']);

        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "0");
    }

    protected function tearDown()
    {
        $GLPIlog = new GLPIlogs();
        $GLPIlog->testSQLlogs();
        $GLPIlog->testPHPlogs();
    }
}
?>
