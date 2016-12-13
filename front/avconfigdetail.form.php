<?php

/*
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

include("../../../inc/includes.php");

// Session::checkRight('plugin_armadito_avconfigs', READ);

$avconfigs_details = new PluginArmaditoAVConfigDetail();

Html::header(__('Armadito', 'armadito'), $_SERVER["PHP_SELF"], "plugins", "pluginarmaditomenu", "avconfig");

PluginArmaditoMenu::displayMenu("mini");

if (isset($_GET["id"])) {
    $avconfigs_details->display(array(
        "id" => $_GET["id"]
    ));
} else {
    // Show error message
}

Html::footer();

?>
