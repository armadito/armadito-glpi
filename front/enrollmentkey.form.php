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

function getFormdata()
{
    $data = $_POST;
    unset($data['add']);
    unset($data['_glpi_csrf_token']);

    return $data;
}

Session::checkRight('plugin_armadito_enrollmentkeys', READ);

Html::header(__('Features', 'armadito'), $_SERVER["PHP_SELF"], "plugins", "pluginarmaditomenu", "enrollmentkey");

PluginArmaditoMenu::displayHeader();
PluginArmaditoMenu::displayMenu("mini");

$enrollmentkey = new PluginArmaditoEnrollmentKey();

if (isset($_POST['add']))
{
    try {
        $data = getFormdata();
        $enrollmentkey->initFromForm($data);
        $enrollmentkey->insertEnrollmentKeyInDB();

        Html::redirect(Toolbox::getItemTypeSearchURL('PluginArmaditoEnrollmentKey'));
    }
    catch (Exception $e) {
        echo "<span style=\"color:red\"> ". htmlspecialchars($e->getMessage()) ."</span><br>";
    }
}
else if (isset ($_POST["update"]))
{
    Session::checkRight('plugin_armadito_enrollmentkeys', UPDATE);

    try {
        $data = getFormdata();
        $enrollmentkey->initFromForm($data);
        $enrollmentkey->updateEnrollmentKeyInDB();
    }
    catch (Exception $e) {
        echo "<span style=\"color:red\"> ". htmlspecialchars($e->getMessage()) ."</span><br>";
    }

    Html::back();
}

if (isset($_GET["id"])) {
    $enrollmentkey->display(array(
        "id" => $_GET["id"]
    ));
} else {
    $enrollmentkey->display(array(
        "id" => 0
    ));
}

Html::footer();
?>
