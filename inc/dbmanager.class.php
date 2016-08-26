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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginArmaditoDbManager {

   protected $statements = array();
   protected $queries = array();
   protected $error;

   function __construct() {
      //
   }

   function init() {
        $this->error = new PluginArmaditoError();
   }
	
   function getLastError() {
		return $this->error;
   }

   function setQueryValue( $name, $property_name, $property_value) {
		$this->queries[$name]["params"][$property_name]["value"] = $property_value;
   }

   function addQuery( $name, $type, $table, $params ) {

		$query = array();

		switch ($type) {
			case "INSERT":
				$query["query"] = $this->addInsertQuery($table, $params);
				break;
			case "UPDATE":
				$query["query"] = $this->addUpdateQuery($table, $params);
				break;
			default: return 1;
		}
		
		$query["params"] = $params;
		$this->queries[$name] = $query;
		return 0;
   }

   function addInsertQuery($table, $params){
    	$query = "INSERT INTO `".$table."` (";
		foreach ($params as $property_name => $property_type) {
			 	$query .= "`".$property_name."`,";
		}

		$query = rtrim($query, ",");		
		$query .= ") VALUES (";
		
		for($i = 0; $i < count($params); $i++){
			$query .= "?,";
		}

		$query = rtrim($query, ",");		
		$query .= ")";
		return $query;
   }

   function addUpdateQuery($table, $params){
		$query = "UPDATE...";
		return $query;
   }

   function prepareQuery ($name) {
   		 global $DB;
		 $this->statements[$name] = $DB->prepare($this->queries[$name]["query"]);
         if(!$this->statements[$name]) {
            $this->error->setMessage(1, 'prepareQuery '.$name.' failed.');
            $this->error->log();
            return 0;
         }
		 return 1;
   }

   function makeValuesReferenced($arr){
		$refs = array();
		foreach($arr as $key => $value)
		    $refs[$key] = &$arr[$key];
		return $refs;
   }

   function getbindQueryArgs($name) {
		$bindargs = array("");
		foreach ($this->queries[$name]["params"] as $property_name => $property_array) {
			$bindargs[0] .= $property_array["type"];
			array_push($bindargs, $property_name);
		}
		return $this->makeValuesReferenced($bindargs);
   }

   function bindQuery ($name) {
   		global $DB;
		$ref    = new ReflectionClass('mysqli_stmt'); 
		$method = $ref->getMethod("bind_param");

  		if(!$method->invokeArgs($this->statements[$name], $this->getbindQueryArgs($name))) {
		   $this->error->setMessage(1, 'bindQuery '.$name.' failed.');
           $this->error->log();
           $this->statements[$name]->close();
		   return 0;
		}
		return 1;
   }

   function executeQuery ($name) {
   		 global $DB;
		 if(! $this->statements[$name]->execute()){
            $this->error->setMessage(1, 'executeQuery '.$name.' failed.');
            $this->error->log();
            $this->statements[$name]->close();
            return 0;
         }
		 return 1;
   }

   function closeQuery ($name) {
		 return $this->statements[$name]->close();
   }

}

?>
