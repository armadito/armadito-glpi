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

class PluginArmaditoEnrollmentKey extends PluginArmaditoCommonDBTM
{

    protected $id;
    protected $value;
    protected $use_counter;
    protected $creation_date;
    protected $expiration_date;

    function initFromForm($POST)
    {
        if(isset($POST["id"])) {
            $this->id = $POST["id"];
        }

        PluginArmaditoToolbox::validateInt($POST["use_counter"]);
        PluginArmaditoToolbox::validateDate($POST["creation_date"].":00");
        PluginArmaditoToolbox::validateDate($POST["expiration_date"].":00");

        $this->creation_date   = $POST["creation_date"].":00";
        $this->expiration_date = $POST["expiration_date"].":00";
        $this->use_counter     = $POST["use_counter"];
        $this->value           = $this->generateRandomKey();
    }

    function initFromDB($id)
    {
        if (!$this->getFromDB($id)) {
            throw new PluginArmaditoDbException('EnrollmentKey initFromDB failed.');
        }

        $this->id              = $id;
        $this->value           = $this->fields["value"];
        $this->use_counter     = $this->fields["use_counter"];
        $this->creation_date   = $this->fields["creation_date"];
        $this->expiration_date = $this->fields["expiration_date"];
    }

    function initFromJson($jobj)
    {
        if(!$this->isEnrollmentKeyInDB($jobj->key)){
            throw new InvalidArgumentException(sprintf('Enrollment key not found in database'));
        }

        $this->initFromDB($this->id);
    }

    function isEnrollmentKeyInDB($key_value)
    {
        global $DB;

        PluginArmaditoToolbox::validateKey($key_value);

        $query = "SELECT id FROM `glpi_plugin_armadito_enrollmentkeys`
                WHERE `value`='" . $key_value . "' AND `is_deleted`=0";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error isEnrollmentKeyInDB : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0) {
            $data     = $DB->fetch_assoc($ret);
            $this->id = PluginArmaditoToolbox::validateInt($data["id"]);
            return true;
        }

        return false;
    }

    function insertEnrollmentKeyInDB()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();

        $query = "NewEnrollmentKey";
        $dbmanager->addQuery($query, "INSERT", $this->getTable(), $params);
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->executeQuery($query);

        $this->id = PluginArmaditoDbToolbox::getLastInsertedId();
        $this->logNewItem();
    }

    function updateEnrollmentKeyInDB()
    {
        $dbmanager = new PluginArmaditoDbManager();
        $params = $this->setCommonQueryParams();
        $params["id"]["type"] = "i";

        $query = "UpdateEnrollmentKey";
        $dbmanager->addQuery($query, "UPDATE", $this->getTable(), $params, "id");
        $dbmanager->prepareQuery($query);
        $dbmanager->bindQuery($query);

        $dbmanager = $this->setCommonQueryValues($dbmanager, $query);
        $dbmanager->setQueryValue($query, "id", $this->id);
        $dbmanager->executeQuery($query);
    }

    function setCommonQueryParams()
    {
        $params["value"]["type"]           = "s";
        $params["use_counter"]["type"]     = "i";
        $params["creation_date"]["type"]   = "s";
        $params["expiration_date"]["type"] = "s";
        return $params;
    }

    function setCommonQueryValues( $dbmanager, $query )
    {
        $dbmanager->setQueryValue($query, "value", $this->value);
        $dbmanager->setQueryValue($query, "use_counter", $this->use_counter);
        $dbmanager->setQueryValue($query, "creation_date", $this->creation_date);
        $dbmanager->setQueryValue($query, "expiration_date", $this->expiration_date);
        return $dbmanager;
    }

    static function getTypeName($nb = 0)
    {

        return __('Enrollment Key', 'armadito');
    }

    function getSearchOptions()
    {
        $search_options = new PluginArmaditoSearchoptions('EnrollmentKey');

        $items['Key Id']          = new PluginArmaditoSearchitemlink('id', $this->getTable(), 'PluginArmaditoEnrollmentKey');
        $items['Key Value']       = new PluginArmaditoSearchtext('value', $this->getTable());
        $items['Creation Date']   = new PluginArmaditoSearchtext('creation_date', $this->getTable());
        $items['Expiration Date'] = new PluginArmaditoSearchtext('expiration_date', $this->getTable());
        $items['Usage Counter']   = new PluginArmaditoSearchtext('use_counter', $this->getTable());

        return $search_options->get($items);
    }

    function showForm($id, $options = array())
    {
        $this->initForm($id, $options);
        $this->showFormHeader($options);

        $now = date("Y-m-d H:i", time());

        echo "<input type='hidden' name='id' value='" . htmlspecialchars($this->fields["id"]) . "'/>";
        echo "<input type='hidden' name='creation_date' value='" . $now . "'/>";

        echo "<tr class='tab_bg_2' align='center' ><td>".__('Expiration date')."</td><td>";
        $rand_begin = Html::showDateTimeField("expiration_date",
                                            array('value'      => $now,
                                                  'timestep'   => -1,
                                                  'maybeempty' => false,
                                                  'canedit'    => true,
                                                  'mindate'    => '',
                                                  'maxdate'    => '',
                                                  'mintime'    => "00:00:00",
                                                  'maxtime'    => "22:00:00"));
        echo "</td></tr>\n";

        echo "<tr class='tab_bg_2' align='center' ><td>".__('Usage Counter')."</td><td>";
        Dropdown::showNumber("use_counter", array(
             'value' => $this->fields["use_counter"],
             'min' => 1,
             'max' => 100000)
        );
        echo "</td></tr>\n";

        $this->showFormButtons($options);
        return true;
    }

    function defineTabs($options = array())
    {
        $ong = array();
        $this->addDefaultFormTab($ong);
        $this->addStandardTab('Log', $ong, $options);
        return $ong;
    }

    function generateRandomKey()
    {
        static $max = 60466175; // ZZZZZZ in decimal

        return strtoupper(sprintf(
            "%05s-%05s-%05s-%05s-%05s",
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36)
        ));
    }

    function checkKeyExpiration()
    {
        $now_ts = time();
        $expiration_ts = PluginArmaditoToolbox::MySQLDateTime_to_Timestamp($this->expiration_date);

        if($now_ts > $expiration_ts) {
            throw new InvalidArgumentException(sprintf('Your enrollment key has expired.'));
        }
    }

    function checkKeyUsability()
    {
        if($this->use_counter <= 0) {
            throw new InvalidArgumentException(sprintf('Your enrollment key reached its maximum usage count.'));
        }
    }

    function decrementUseCounter()
    {
        $input                = array();
        $input['id']          = $this->id;
        $input['use_counter'] = $this->use_counter-1;

        if($this->use_counter <= 0){
           $input['use_counter'] = 0;
        }

        if (!$this->update($input)) {
            throw new PluginArmaditoDbException("Error when decrementing EnrollmentKey use counter.");
        }
    }
}
?>
