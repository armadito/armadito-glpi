<?php

/*
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

class PluginArmaditoConfig extends CommonDBTM
{
    public $displaylist = FALSE;
    static $rightname = 'plugin_armadito_configuration';

    CONST ACTION_CLEAN = 0;
    CONST ACTION_STATUS = 1;

    static function getTypeName($nb = 0)
    {
        return __('General setup');
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if ($item->getType() == __CLASS__) {

         $array_ret = array();
         $array_ret[0] = __('General setup');
         $array_ret[1] = __('States', 'armadito');
         $array_ret[2] = __('Alerts', 'armadito');
         $array_ret[3] = __('Scans', 'armadito');
         $array_ret[4] = __('Jobs', 'armadito');

         return $array_ret;
      }
      return '';
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

       switch ($tabnum) {
          case 0:
             $item->showForm();
             break;
          case 1:
             $item->showStatesForm();
             break;
          case 2:
             $item->showAlertsForm();
             break;
          case 3:
             $item->showScansForm();
             break;
          case 4:
             $item->showJobsForm();
             break;
          default:
             break;
       }

       return TRUE;
    }

    function defineTabs($options=array())
    {
        $ong = array();
        $this->addStandardTab("PluginArmaditoConfig", $ong, $options);

        return $ong;
    }

    function initConfigModule($getOnly = FALSE)
    {

        $input               = array();
        $input['version']    = PLUGIN_ARMADITO_VERSION;
        $input['extradebug'] = '1';
        $input['getjobs_limit'] = 10;

        if ($getOnly) {
            return $input;
        }

        $this->addValues($input);
    }

    function showForm($options = array())
    {
        $this->showFormHeader($options);
        $options['candel'] = FALSE;
        $this->showFormButtons($options);
        return TRUE;
    }

    function initConfigForm($options, $title)
    {
        $paConfig = new PluginArmaditoConfig();
        $paConfig->fields['id'] = 1;
        $paConfig->showFormHeader($options);

        echo "<tr>";
        echo "<th colspan='4'>";
        echo $title;
        echo "</th>";
        echo "</tr>";

        return $paConfig;
    }

    function showStatesForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('States configuration', 'armadito'));
        $options['candel'] = FALSE;
        $paConfig->showFormButtons($options);
        return TRUE;
    }

    function showAlertsForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Alerts configuration', 'armadito'));

        $options['candel'] = FALSE;
        $paConfig->showFormButtons($options);
        return TRUE;
    }

    function showScansForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Scans configuration', 'armadito'));

        $options['candel'] = FALSE;
        $paConfig->showFormButtons($options);
        return TRUE;
    }

    function showJobsForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Jobs configuration', 'armadito'));

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Maximum jobs agents can get by round', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        Dropdown::showNumber("getjobs_limit", array(
             'value' => $this->getValue('getjobs_limit'),
             'min' => 1,
             'max' => 100)
         );
        echo "</td>";
        echo "</tr>";

        $options['candel'] = FALSE;
        $paConfig->showFormButtons($options);
        return TRUE;
    }

    function addValues($values, $update = TRUE)
    {
        foreach ($values as $type => $value) {
            if ($this->getValue($type) === NULL) {
                $this->addValue($type, $value);
            } else if ($update) {
                $this->updateValue($type, $value);
            }
        }
    }

    function updateValue($name, $value)
    {
        $config = current($this->find("`type`='" . $DB->escape($name) . "'"));
        if (isset($config['id'])) {
            return $this->update(array(
                'id' => $config['id'],
                'value' => $value
            ));
        } else {
            return $this->add(array(
                'type' => $name,
                'value' => $value
            ));
        }
    }

    function addValue($name, $value)
    {
        $existing_value = $this->getValue($name);
        if (!is_null($existing_value)) {
            return $existing_value;
        } else {
            return $this->add(array(
                'type' => $name,
                'value' => $value
            ));
        }
    }

    function getValue($name)
    {
        global $DB, $PF_CONFIG;

        if (isset($PF_CONFIG[$name])) {
            return $PF_CONFIG[$name];
        }

        $config = current($this->find("`type`='" . $DB->escape($name) . "'"));
        if (isset($config['value'])) {
            return $config['value'];
        }

        return NULL;
    }

    function isActive($name)
    {
        $value = $this->getValue($name);
        if($value === NULL){
           return FALSE;
        }

        return $value;
    }

    static function loadCache()
    {
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
