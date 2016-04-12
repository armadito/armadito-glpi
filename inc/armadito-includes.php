<?php 

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
