<?php

/**
   Copyright (C) 2016 Teclib'
   Copyright (C) 2010-2016 by the FusionInventory Development Team.

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

include ("../../../inc/includes.php");

Session::checkRight('plugin_armadito_scanconfigs', READ);

Html::header(__('Features', 'armadito'), $_SERVER["PHP_SELF"],
             "plugins", "pluginarmaditomenu", "scanconfig");

PluginArmaditoMenu::displayHeader();
PluginArmaditoMenu::displayMenu("mini");

$pfConfig = new PluginArmaditoScanConfig();

if (isset($_POST['update'])) {
   $data = $_POST;
   unset($data['update']);
   unset($data['id']);
   unset($data['_glpi_csrf_token']);
   foreach ($data as $key=>$value) {
      $pfConfig->updateValue($key, $value);
   }
   Html::back();
}

$a_config = current($pfConfig->find("", "", 1));
$pfConfig->getFromDB($a_config['id']);
$pfConfig->showTabs(array());
$pfConfig->addDivForTabs();

Html::footer();

?>
