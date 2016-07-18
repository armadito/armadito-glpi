<?php

/**
   Copyright (C) 2010-2016 by the FusionInventory Development Team
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

include_once("toolbox.class.php");

class PluginArmaditoProfile extends Profile {

   // ProfileRight::deleteProfileRights(array('armadito:read'));

   function getRightsGeneral() {

      $rights = array(
          array('rights'    => array(READ => __('Read')),
                'label'     => __('Menu', 'armadito'),
                'field'     => 'plugin_armadito_menu'),
          array('rights'    => array(READ => __('Read'), UPDATE => __('Update')),
                'itemtype'  => 'PluginArmaditoConfig',
                'label'     => __('Configuration', 'armadito')),
          array('itemtype'  => 'PluginArmaditoAgent',
                'label'     => __('Agents', 'armadito'),
                'field'     => 'plugin_armadito_agent')
      );

      return $rights;
  }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      return self::createTabEntry('Armadito');
   }

  function getAllRights() {
      $a_rights = array();
      $a_rights = array_merge($a_rights, $this->getRightsGeneral());
      return $a_rights;
   }

   static function uninstallProfile() {
      $pfProfile = new self();
      $a_rights = $pfProfile->getAllRights();
      foreach ($a_rights as $data) {
         ProfileRight::deleteProfileRights(array($data['field']));
      }
   }

}
?>
