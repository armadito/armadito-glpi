<?php

/*
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

class PluginArmaditoAntivirus extends CommonDBTM
{
    protected $id;
    protected $fullname;
    protected $name;
    protected $version;

    function __construct()
    {
    }

    function getId()
    {
        return $this->id;
    }

    function getAVName()
    {
        return $this->name;
    }

    function getAVVersion()
    {
        return $this->version;
    }

    function initFromJson($jobj_)
    {
        $this->name     = $jobj_->task->antivirus->name;
        $this->version  = $jobj_->task->antivirus->version;
        $this->fullname = $jobj_->task->antivirus->name . "+" . $jobj_->task->antivirus->version;
    }

    function initFromDB($av_id)
    {
        if ($this->getFromDB($av_id)) {
            $this->id       = $this->fields["id"];
            $this->name     = $this->fields["name"];
            $this->version  = $this->fields["version"];
            $this->fullname = $this->fields["fullname"];
        } else {
            PluginArmaditoToolbox::logE("Unable to get Antivirus DB fields");
        }
    }

    function isAntivirusInDB()
    {
        global $DB;

        $query = "SELECT id FROM `glpi_plugin_armadito_antiviruses`
                 WHERE `fullname`='" . $this->fullname . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error isAlreadyEnrolled : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            $this->id = PluginArmaditoToolbox::validateInt($data["id"]);
            return true;
        }
        return false;
    }

    function run()
    {
        if (!$this->isAntivirusInDB()) {
            $this->insertAntivirusInDB();
        } else {
            $this->updateAntivirusInDB();
        }
    }

    function insertAntivirusInDB()
    {
        $dbmanager = new PluginArmaditoDbManager();

        $params = $this->setCommonQueryParams();

        $query = "NewAntivirus";
        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        PluginArmaditoToolbox::validateInt($this->id);
    }

    function updateAntivirusInDB()
    {
        $dbmanager = new PluginArmaditoDbManager();

        $params = $this->setCommonQueryParams();
        $params["id"]["type"]       = "i";

        $query = "UpdateAntivirus";
        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, "id");
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->setQueryValue($query, "id", $this->id); # WHERE
        $dbmanager->executeQuery($query);
    }

    function setCommonQueryParams()
    {
        $params["name"]["type"]     = "s";
        $params["version"]["type"]  = "s";
        $params["fullname"]["type"] = "s";
        return $params;
    }

    function setCommonQueryValues($dbmanager, $query)
    {
        $dbmanager->setQueryValue($query, "name", $this->name);
        $dbmanager->setQueryValue($query, "version", $this->version);
        $dbmanager->setQueryValue($query, "fullname", $this->fullname);
        return $dbmanager;
    }

    static function getAntivirusList()
    {
        global $DB;

        $AVs   = array();
        $query = "SELECT id, fullname FROM `glpi_plugin_armadito_antiviruses`";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getAntivirusList : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            while ($data = $DB->fetch_assoc($ret)) {
                $AVs[$data["id"]] = $data['fullname'];
            }
        }
        return $AVs;
    }

    static function getTypeName($nb = 0)
    {
        return __('Antivirus', 'armadito');
    }

    static function canCreate()
    {
        if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
            return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w');
        }
        return false;
    }

    static function canView()
    {
        if (isset($_SESSION["glpi_plugin_armadito_profile"])) {
            return ($_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'w' || $_SESSION["glpi_plugin_armadito_profile"]['armadito'] == 'r');
        }
        return false;
    }

    function defineTabs($options = array())
    {
        $ong = array();
        $this->addDefaultFormTab($ong);
        $this->addStandardTab('Log', $ong, $options);

        return $ong;
    }

    function showForm($table_id, $options = array())
    {
        PluginArmaditoToolbox::validateInt($table_id);

        $this->initForm($table_id, $options);
        $this->showFormHeader($options);

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Id', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo "<b>" . htmlspecialchars($this->fields["id"]) . "</b>";
        echo "</td>";
        echo "<td></td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Name', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo htmlspecialchars($this->fields["name"]);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Version', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo htmlspecialchars($this->fields["version"]);
        echo "</td>";
        echo "<td></td>";
        echo "</tr>";
    }
}
?>
