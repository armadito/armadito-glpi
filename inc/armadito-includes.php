<?php 

/**
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

// Define some plugins useful classes

class Alog {

   static function logE($text) {

      $ok = error_log(date("Y-m-d H:i:s")." ".$text."\n", 3, GLPI_LOG_DIR."/armadito.log"); 
      return $ok;
   }

}

class DBtools {
 
   static function ExecQuery ($query){
      global $DB;
 
      if($DB->query($query)){
         return true;
      }
      else{
         Alog::logE("Error $query :".$DB->error());
         return false;
      }
   }
}


?>
