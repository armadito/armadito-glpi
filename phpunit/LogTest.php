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

class GLPIlogs extends PHPUnit_Framework_TestCase
{
    
    public function testSQLlogs()
    {
        
        $filecontent = file_get_contents(GLPI_ROOT . "/files/_log/sql-errors.log");
        
        $this->assertEquals('', $filecontent, 'sql-errors.log not empty');
        
        file_put_contents(GLPI_ROOT . "/files/_log/sql-errors.log", '');
    }
    
    
    
    public function testPHPlogs()
    {
        
        $filecontent = file_get_contents(GLPI_ROOT . "/files/_log/php-errors.log");
        
        $this->assertEquals('', $filecontent, 'php-errors.log not empty');
        
        file_put_contents(GLPI_ROOT . "/files/_log/php-errors.log", '');
    }
    
}



class GLPIlogs_AllTests
{
    
    public static function suite()
    {
        
        $suite = new PHPUnit_Framework_TestSuite('GLPIlogs');
        return $suite;
    }
}

?>
