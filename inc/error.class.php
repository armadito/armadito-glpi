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

class PluginArmaditoError {
   protected $message;
   protected $code;

   function __construct() {
      $this->message = "Unknown error.";
      $this->code = -1;
   }

   function setMessage($code_, $message_) {
      $this->code = $code_;
      $this->message = $message_;
   }

   function getCode() {
      return $this->code;
   }

   function toJson() {
      return '{ "code": '.$this->code.', "message": "'.$this->message.'", "plugin_version": "'.PLUGIN_ARMADITO_VERSION.'" }';
   }

   function log() {
      if($this->code > 0){
         PluginArmaditoToolbox::logE("Error (".$this->code.") : ".$this->message);
      }
   }
}
?>
