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

class PluginArmaditoConfig extends PluginArmaditoEAVCommonDBTM
{
    public $displaylist = FALSE;
    protected $default_conf;

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
             $boardconf = new PluginArmaditoBoardConf();
             $boardconf->showForm();
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

        $this->default_conf = array();
        $this->default_conf['version']    = PLUGIN_ARMADITO_VERSION;
        $this->default_conf['debug_minlevel'] = 1;
        $this->default_conf['getjobs_limit'] = 10;
        $this->default_conf['armaditoscheduler'] = '0';
        $this->default_conf['colorpalette_h'] = 0.07;
        $this->default_conf['colorpalette_s'] = 0.3;
        $this->default_conf['colorpalette_v'] = 0.99;

        if ($getOnly) {
            return $this->default_conf;
        }

        $this->addValues($this->default_conf);
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

    function showForm($options = array())
    {
        $paConfig = $this->initConfigForm($options, __('Global configuration', 'armadito'));

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Debug level (minimum)', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";

        $array    = array();
        $array[0] = "Verbose";
        $array[1] = "Debug";
        $array[2] = "Info";
        $array[3] = "Warning";
        $array[4] = "Error";
        Dropdown::showFromArray("debug_minlevel", $array,  array('value' =>  $this->getValue('debug_minlevel')));
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Use Armadito Scheduler (experimental)', 'armadito')."&nbsp;:</td>";
        echo "<td width='20%'>";
        Dropdown::showYesNo("armaditoscheduler", $this->getValue('armaditoscheduler'));
        echo "</td>";
        echo "</tr>";

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
