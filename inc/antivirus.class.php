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

include_once("toolbox.class.php");

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginArmaditoAntivirus extends CommonDBTM {

    protected $id;
    protected $fullname;
    protected $name;
    protected $version;

   function __construct() {
      //
   }

   function initFromJson($jobj_) {
      $this->name = $jobj_->task->antivirus->name;
      $this->version = $jobj_->task->antivirus->version;
	  $this->fullname = $jobj_->task->antivirus->name."+".$jobj_->task->antivirus->version;
   }

   function isAntivirusInDB() {
	  global $DB;

      $query = "SELECT id FROM `glpi_plugin_armadito_antiviruses`
                 WHERE `fullname`='".$this->fullname."'";
      $ret = $DB->query($query);

      if(!$ret){
         throw new Exception(sprintf('Error isAlreadyEnrolled : %s', $DB->error()));
      }

      if($DB->numrows($ret) > 0){
         $data = $DB->fetch_assoc($ret);
		 $this->id = PluginArmaditoToolbox::validateInt($data["id"]);
         return true;
      }
      return false;
   }

   function run() {

		if(!$this->isAntivirusInDB()){
			return $this->insertAntivirusInDB();
		}
		else{
			return $this->updateAntivirusInDB();
		}
   }

   function insertAntivirusInDB() {
		 $error = new PluginArmaditoError();
		 $dbmanager = new PluginArmaditoDbManager();
		 $dbmanager->init();

		 $params["name"]["type"] = "s";
		 $params["version"]["type"] = "s";
		 $params["fullname"]["type"] = "s";

		 $dbmanager->addQuery("NewAntivirus", "INSERT", $this->getTable(), $params );

		 if(!$dbmanager->prepareQuery("NewAntivirus")){
			return $dbmanager->getLastError();
		 }

		 if(!$dbmanager->bindQuery("NewAntivirus")){
			return $dbmanager->getLastError();
		 }

		 $dbmanager->setQueryValue("NewAntivirus", "name", $this->name);
		 $dbmanager->setQueryValue("NewAntivirus", "version", $this->version);
		 $dbmanager->setQueryValue("NewAntivirus", "fullname", $this->fullname);

		 if(!$dbmanager->executeQuery("NewAntivirus")){
			return $dbmanager->getLastError();
		 }

		 $dbmanager->closeQuery("NewAntivirus");

		 $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
		 if($this->id > 0){
			PluginArmaditoToolbox::validateInt($this->id);
			$error->setMessage(0, 'New Antivirus successfully added in database.');
		 }
         else{
			$error->setMessage(1, 'Unable to get new Antivirus Id');
		 }
         return $error;
   }

   function updateAntivirusInDB() {
		 $error = new PluginArmaditoError();
		 $dbmanager = new PluginArmaditoDbManager();
		 $dbmanager->init();

		 $params["name"]["type"] = "s";
		 $params["version"]["type"] = "s";
		 $params["fullname"]["type"] = "s";
		 $params["id"]["type"] = "i";

		 $dbmanager->addQuery("UpdateAntivirus", "UPDATE", $this->getTable(), $params, "id");

		 if(!$dbmanager->prepareQuery("UpdateAntivirus")){
			return $dbmanager->getLastError();
		 }

		 if(!$dbmanager->bindQuery("UpdateAntivirus")){
			return $dbmanager->getLastError();
		 }

		 $dbmanager->setQueryValue("UpdateAntivirus", "name", $this->name);
		 $dbmanager->setQueryValue("UpdateAntivirus", "version", $this->version);
		 $dbmanager->setQueryValue("UpdateAntivirus", "fullname", $this->fullname);
		 $dbmanager->setQueryValue("UpdateAntivirus", "id", $this->id); # WHERE

		 if(!$dbmanager->executeQuery("UpdateAntivirus")){
			return $dbmanager->getLastError();
		 }

		 $dbmanager->closeQuery("UpdateAntivirus");
		 $error->setMessage(0, 'Antivirus successfully updated in database.');
         return $error;
   }

      /**
      *  Get list of all Antiviruses managed
      **/
      static function getAntivirusList () {
         global $DB;

         $AVs = array();
         $query = "SELECT DISTINCT full_name FROM `glpi_plugin_armadito_antiviruses`";
         $ret = $DB->query($query);

         if(!$ret){
            throw new Exception(sprintf('Error getAntivirusList : %s', $DB->error()));
         }

         if($DB->numrows($ret) > 0){
            while ($data = $DB->fetch_assoc($ret)) {
                $AVs[] =  $data['full_name'];
            }
         }
         return $AVs;
      }
}
?>
