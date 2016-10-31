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
         $array_ret[1] = __('Boards', 'armadito');
         $array_ret[2] = __('States', 'armadito');
         $array_ret[3] = __('Alerts', 'armadito');
         $array_ret[4] = __('Scans', 'armadito');
         $array_ret[5] = __('Jobs', 'armadito');

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
             $item->showBoardsForm();
             break;
          case 2:
             $item->showStatesForm();
             break;
          case 3:
             $item->showAlertsForm();
             break;
          case 4:
             $item->showScansForm();
             break;
          case 5:
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
        $input['colorpalette_h'] = 0.8;
        $input['colorpalette_s'] = 0.3;
        $input['colorpalette_v'] = 0.99;

        if ($getOnly) {
            return $input;
        }

        $this->addValues($input);
    }

    function initConfigForm($options, $title)
    {
        $paConfig = new PluginArmaditoConfig();
        $paConfig->fields['id'] = 1;
        $options['colspan'] = 4;
        $paConfig->showFormHeader($options);

        echo "<tr class='headerRow'>";
        echo "<th colspan='".$options['colspan']."'>";
        echo $title;
        echo "</th>";
        echo "<th colspan='".$options['colspan']."'></th>";
        echo "</tr>";

        return $paConfig;
    }

    function showFormFooter($options = array())
    {
        $options['candel'] = FALSE;
        $options['colspan'] = 4;
        $this->showFormButtons($options);
    }

    function showBoardExample ($h, $s, $v)
    {
        $board = new PluginArmaditoExampleBoard();
        $board->setHue($h);
        $board->setSaturation($s);
        $board->setValue($v);
        $board->displayBoard();
    }

    function showForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Global configuration', 'armadito'));
        $paConfig->showFormFooter();
        return TRUE;
    }

    function showBoardsForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Boards', 'armadito'));

        $h = htmlspecialchars($this->getValue('colorpalette_h'));
        $s = htmlspecialchars($this->getValue('colorpalette_s'));
        $v = htmlspecialchars($this->getValue('colorpalette_v'));

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Color Palette Hue', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        echo "<input type='text' name='colorpalette_h' value='" . $h . "'/>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Color Palette Saturation', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        echo "<input type='text' name='colorpalette_s' value='" . $s . "'/>";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Color Palette Value', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        echo "<input type='text' name='colorpalette_v' value='" . $v . "'/>";
        echo "</td>";
        echo "</tr>";

        $paConfig->showBoardExample($h, $s, $v);
        $paConfig->showFormFooter();
        return TRUE;
    }

    function showStatesForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('States configuration', 'armadito'));
        $paConfig->showFormFooter();
        return TRUE;
    }

    function showAlertsForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Alerts configuration', 'armadito'));
        $paConfig->showFormFooter();
        return TRUE;
    }

    function showScansForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Scans configuration', 'armadito'));
        $paConfig->showFormFooter();
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

        $paConfig->showFormFooter();
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
        global $DB;
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
