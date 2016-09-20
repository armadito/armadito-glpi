<?php

/**
   Copyright (C) 2010-2016 by the FusionInventory Development Team.
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

class PluginArmaditoConfig extends CommonDBTM {
   public $displaylist = FALSE;


   static $rightname = 'plugin_armadito_configuration';

   CONST ACTION_CLEAN = 0;
   CONST ACTION_STATUS = 1;

   /**
    * Display name of itemtype
    *
    * @return value name of this itemtype
    **/
   static function getTypeName($nb=0) {

      return __('General setup');

   }

   /**
   * Display form for config
   *
   * @return bool TRUE if form is ok
   *
   **/
   function showForm($options=array()) {
      global $CFG_GLPI;

      $this->showFormHeader($options);
   }

   /**
   * Initialize config values of armadito plugin
   *
   * @return nothing
   *
   **/
   function initConfigModule($getOnly=FALSE) {

      $input = array();
      $input['version'] = PLUGIN_ARMADITO_VERSION;
      $input['extradebug'] = '1';

      if ($getOnly) {
         return $input;
      }

      $this->addValues($input);
   }

   /**
    * add multiple configuration values
    *
    * @param $values array of configuration values, indexed by name
    *
    * @return nothing
    **/
   function addValues($values, $update=TRUE) {

      foreach ($values as $type=>$value) {
         if ($this->getValue($type) === NULL) {
            $this->addValue($type, $value);
         } else if ($update == TRUE){
            $this->updateValue($type, $value);
         }
      }
   }

   /**
    * Update configuration value
    *
    * @param $name field name
    * @param $value field value
    *
    * @return boolean : TRUE on success
    **/
   function updateValue($name, $value) {
      $config = current($this->find("`type`='".$name."'"));
      if (isset($config['id'])) {
         return $this->update(array('id'=> $config['id'], 'value'=>$value));
      } else {
         return $this->add(array('type' => $name, 'value' => $value));
      }
   }

   /**
    * Add configuration value, if not already present
    *
    * @param $name field name
    * @param $value field value
    *
    * @return integer the new id of the added item (or FALSE if fail)
    **/
   function addValue($name, $value) {
      $existing_value = $this->getValue($name);
      if (!is_null($existing_value)) {
         return $existing_value;
      } else {
         return $this->add(array('type'       => $name,
                                 'value'      => $value));
      }
   }

   /**
   * Get configuration value
   *
   * @param $name field name
   *
   * @return field value for an existing field, FALSE otherwise
   **/
   function getValue($name) {
      global $PF_CONFIG;

      if (isset($PF_CONFIG[$name])) {
         return $PF_CONFIG[$name];
      }

      $config = current($this->find("`type`='".$name."'"));
      if (isset($config['value'])) {
         return $config['value'];
      }
      return NULL;
   }

   /**
   * give state of a config field for an armadito plugin
   *
   * @param $name field name
   *
   * @return TRUE for an existing field, FALSE otherwise
   **/
   function isActive($name) {
      if (!($this->getValue($name))) {
         return FALSE;
      } else {
         return TRUE;
      }
   }

   static function loadCache() {
      global $DB, $PF_CONFIG;

      //Test if table exists before loading cache
      //The only case where table doesn't exists is when you click on
      //uninstall the plugin and it's already uninstalled
      if (TableExists('glpi_plugin_armadito_configs')) {
         $PF_CONFIG = array();
         foreach ($DB->request('glpi_plugin_armadito_configs') as $data) {
            $PF_CONFIG[$data['type']] = $data['value'];
         }
      }
   }
}

?>
