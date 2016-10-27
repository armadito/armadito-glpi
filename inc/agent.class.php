<?php

/*
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

include_once("toolbox.class.php");

class PluginArmaditoAgent extends CommonDBTM
{
    protected $id;
    protected $jobj;
    protected $antivirus;
    protected $computerid;

    function __construct()
    {
    }

    function initFromJson($jobj)
    {
        $this->id        = PluginArmaditoToolbox::validateInt($jobj->agent_id);
        $this->jobj      = $jobj;
        $this->antivirus = new PluginArmaditoAntivirus();
        $this->antivirus->initFromJson($jobj);
        $this->computerid = PluginArmaditoAgent::getComputerIdForUUID($this->jobj->uuid);
    }

    function initFromDB($agent_id)
    {
        if ($this->getFromDB($agent_id)) {
            $this->id        = $this->fields["id"];
            $this->antivirus = new PluginArmaditoAntivirus();
            $this->antivirus->initFromDB($this->fields["plugin_armadito_antiviruses_id"]);
        } else {
            PluginArmaditoToolbox::logE("Unable to get Agent DB fields");
        }
    }

    function updateLastAlert($alert)
    {
        $paAgent = new self();

        $input                = array();
        $input['id']          = $alert->getAgentId();
        $input['last_alert']  = $alert->getDetectionTime();
        if ($paAgent->update($input)) {
            return true;
        }
        return false;
    }

    function getAntivirusId()
    {
        return $this->antivirus->getId();
    }

    function getAntivirus()
    {
        return $this->antivirus;
    }

    function toJson()
    {
        return '{"agent_id": ' . $this->id . '}';
    }

    static function getAgentIdForComputerId($computers_id)
    {
        global $DB;

        PluginArmaditoToolbox::validateInt($computers_id);

        $query = "SELECT id FROM `glpi_plugin_armadito_agents`
                WHERE `computers_id`='" . $computers_id . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getAgentIdForComputerId : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return PluginArmaditoToolbox::validateInt($data["id"]);
        }

        return -1;
    }

    static function getAgentIdForUUID($uuid)
    {
        global $DB;

        PluginArmaditoToolbox::validateUUID($uuid);

        $query = "SELECT id FROM `glpi_plugin_armadito_agents`
                WHERE `uuid`='" . $uuid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getAgentIdForUUID : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return PluginArmaditoToolbox::validateInt($data["id"]);
        }

        return -1;
    }

    static function getComputerIdForUUID($uuid)
    {
        global $DB;

        PluginArmaditoToolbox::validateUUID($uuid);

        $query = "SELECT id FROM `glpi_computers`
                WHERE `uuid`='". $uuid . "'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getComputerIdForUUID : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            return PluginArmaditoToolbox::validateInt($data["id"]);
        }

        return -1;
    }

    static function FusionInventoryHook($params = array())
    {
        if (!empty($params)
         && isset($params['inventory_data'])
         && !empty($params['inventory_data'])) {

             $uuid = $params['inventory_data']['Computer']['uuid'];
             $agent_id = PluginArmaditoAgent::getAgentIdForUUID($uuid);

             if($agent_id > 0)
             {
                $computers_id = PluginArmaditoToolbox::validateInt($params['computers_id']);
                PluginArmaditoAgent::associateAgentWithComputer($agent_id, $computers_id);
             }
        }
    }

    static function associateAgentWithComputer( $agent_id, $computers_id )
    {
            $pfAgent = new PluginArmaditoAgent();

            if ($pfAgent->getFromDB($agent_id))
            {
                $input                = array();
                $input['id']          = $agent_id;
                $input['computers_id'] = $computers_id;

                return $pfAgent->update($input);
            }
    }

    function run()
    {
        $error = $this->antivirus->run();
        if ($error->getCode() != 0) {
            return $error;
        }

        if ($this->isAgentInDB()) {
            $error = $this->updateAgentInDB();
        } else {
            $error = $this->insertAgentInDB();
        }

        return $error;
    }

    function isAgentInDB()
    {
        global $DB;

        PluginArmaditoToolbox::validateUUID($this->jobj->uuid);

        $query = "SELECT id FROM `glpi_plugin_armadito_agents`
                WHERE `uuid`='" . $this->jobj->uuid . "'";
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

    function updateAgentInDB()
    {
        $error     = new PluginArmaditoError();
        $dbmanager = new PluginArmaditoDbManager();
        $dbmanager->init();

        $params["entities_id"]["type"]                      = "i";
        $params["computers_id"]["type"]                     = "i";
        $params["agent_version"]["type"]                    = "s";
        $params["plugin_armadito_antiviruses_id"]["type"]   = "i";
        $params["last_contact"]["type"]                     = "s";
        $params["last_alert"]["type"]                       = "s";
        $params["id"]["type"]                               = "i";

        $query_name = "UpdateAgent";
        $dbmanager->addQuery($query_name, "UPDATE", $this->getTable(), $params, "id");

        if (!$dbmanager->prepareQuery($query_name) || !$dbmanager->bindQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->setQueryValue($query_name, "entities_id", 0);
        $dbmanager->setQueryValue($query_name, "computers_id", $this->computerid);
        $dbmanager->setQueryValue($query_name, "agent_version", $this->jobj->agent_version);
        $dbmanager->setQueryValue($query_name, "plugin_armadito_antiviruses_id", $this->antivirus->getId());
        $dbmanager->setQueryValue($query_name, "last_contact", date("Y-m-d H:i:s", time()));
        $dbmanager->setQueryValue($query_name, "last_alert", '1970-01-01 00:00:00');
        $dbmanager->setQueryValue($query_name, "id", $this->id);

        if (!$dbmanager->executeQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->closeQuery($query_name);
        PluginArmaditoToolbox::logIfExtradebug('pluginArmadito-Agent', 'ReEnroll Device with id ' . $this->id);

        $error->setMessage(0, 'New device successfully re-enrolled.');
        return $error;


    }

    function insertAgentInDB()
    {
        $error     = new PluginArmaditoError();
        $dbmanager = new PluginArmaditoDbManager();
        $dbmanager->init();

        $params["entities_id"]["type"]                      = "i";
        $params["computers_id"]["type"]                     = "i";
        $params["agent_version"]["type"]                    = "s";
        $params["plugin_armadito_antiviruses_id"]["type"]   = "i";
        $params["last_contact"]["type"]                     = "s";
        $params["last_alert"]["type"]                       = "s";
        $params["uuid"]["type"]                      = "s";

        $query_name = "NewAgent";
        $dbmanager->addQuery($query_name, "INSERT", $this->getTable(), $params);

        if (!$dbmanager->prepareQuery($query_name) || !$dbmanager->bindQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->setQueryValue($query_name, "entities_id", 0);
        $dbmanager->setQueryValue($query_name, "computers_id", $this->computerid);
        $dbmanager->setQueryValue($query_name, "agent_version", $this->jobj->agent_version);
        $dbmanager->setQueryValue($query_name, "plugin_armadito_antiviruses_id", $this->antivirus->getId());
        $dbmanager->setQueryValue($query_name, "last_contact", date("Y-m-d H:i:s", time()));
        $dbmanager->setQueryValue($query_name, "last_alert", '1970-01-01 00:00:00');
        $dbmanager->setQueryValue($query_name, "uuid", $this->jobj->uuid);

        if (!$dbmanager->executeQuery($query_name)) {
            return $dbmanager->getLastError();
        }

        $dbmanager->closeQuery($query_name);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        if ($this->id > 0) {
            PluginArmaditoToolbox::validateInt($this->id);
            $error->setMessage(0, 'New Agent successfully added in database.');
        } else {
            $error->setMessage(1, 'Unable to get new Antivirus Id');
        }
        return $error;
    }


    static function getTypeName($nb = 0)
    {
        return __('Agent', 'armadito');
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

    static function getMenuName()
    {
        return __('Armadito');
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        $tabname = '';

        if (!$withtemplate) {
            switch ($item->getType()) {
                case 'Profile':
                    if ($item->getField('central')) {
                        $tabname = __("Armadito", 'armadito');
                    }
                    break;
                case 'Computer':
                    $tabname = array( 1 => __("Armadito AV", 'armadito'));
                    break;
                case 'Notification':
                    $tabname = array( 1 => __("Armadito Plugin", 'armadito'));
                    break;
                default :
                    break;
            }
        }

        return $tabname;
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        return true;
    }

    function getSearchOptions()
    {
        $tab           = array();
        $tab['common'] = __('Agent', 'armadito');

        $i = 1;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'id';
        $tab[$i]['name']          = __('Agent Id', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoAgent';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'agent_version';
        $tab[$i]['name']          = __('Agent Version', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_computers';
        $tab[$i]['field']         = 'name';
        $tab[$i]['name']          = __('Computer', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'Computer';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'uuid';
        $tab[$i]['name']          = __('UUID', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = 'glpi_plugin_armadito_antiviruses';
        $tab[$i]['field']         = 'fullname';
        $tab[$i]['name']          = __('Antivirus', 'armadito');
        $tab[$i]['datatype']      = 'itemlink';
        $tab[$i]['itemlink_type'] = 'PluginArmaditoAntivirus';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'last_contact';
        $tab[$i]['name']          = __('Last Contact', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        $i++;

        $tab[$i]['table']         = $this->getTable();
        $tab[$i]['field']         = 'last_alert';
        $tab[$i]['name']          = __('Last Alert', 'armadito');
        $tab[$i]['datatype']      = 'text';
        $tab[$i]['massiveaction'] = FALSE;

        return $tab;
    }

    function getSpecificMassiveActions($checkitem = NULL)
    {

        $actions = array();
        if (Session::haveRight("plugin_armadito_jobs", UPDATE)) {
            $actions[__CLASS__ . MassiveAction::CLASS_ACTION_SEPARATOR . 'newscan'] = __('Scan', 'armadito');
        }

        if (Session::haveRight("plugin_armadito_agents", UPDATE)) {
            $actions[__CLASS__ . MassiveAction::CLASS_ACTION_SEPARATOR . 'transfert'] = __('Transfer');
        }

        return $actions;
    }

    static function processMassiveActionNewScan(MassiveAction $ma, CommonDBTM $item, $key)
    {
        $agent_id = $key;

        if($item->getType() == "Computer") {
            $agent_id = PluginArmaditoAgent::getAgentIdForComputerId($key);
        }

        $job = new PluginArmaditoJob();
        $job->initFromForm($agent_id, "Scan", $_POST);

        if ($job->addJob()) {
            $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
        } else {
            $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
        }
    }

    static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item, array $ids)
    {
        switch ($ma->getAction()) {
            case 'newscan':
                foreach ($ids as $key) {
                    PluginArmaditoAgent::processMassiveActionNewScan($ma, $item, $key);
                }
                return;
        }
        return;
    }

    /**
     * @since version 0.85
     *
     * @see CommonDBTM::showMassiveActionsSubForm()
     **/
    static function showMassiveActionsSubForm(MassiveAction $ma)
    {
        switch ($ma->getAction()) {
            case 'newscan':
                PluginArmaditoAgent::showNewScanForm();
                return true;
        }

        return parent::showMassiveActionsSubForm($ma);
    }

    static function showNewScanForm()
    {
        $configs = PluginArmaditoScanConfig::getScanConfigsList();
        if (empty($configs)) {
            PluginArmaditoScanConfig::showNoScanConfigForm();
            return;
        }

        echo "<b> Scan Parameters </b><br>";
        echo "Configuration: ";
        Dropdown::showFromArray("scanconfig_id", $configs);
        # PluginArmaditoToolbox::showHours('beginhours', array('step' => 15));

        echo "<br><br><b> Job Parameters </b><br>";
        echo "Priority ";
        $array    = array();
        $array[0] = "Low";
        $array[1] = "Medium";
        $array[2] = "High";
        $array[3] = "Urgent";
        Dropdown::showFromArray("job_priority", $array);
        echo "<br><br>" . Html::submit(__('Post'), array(
            'name' => 'massiveaction'
        ));
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
        echo "<td>" . __('Agent Id', 'armadito') . "&nbsp;:</td>";
        echo "<td align='center'>";
        echo "<b>" . htmlspecialchars($this->fields["id"]) . "</b>";
        echo "</td>";
        echo "</tr>";
    }
}

?>
