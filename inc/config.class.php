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
             $generalconf = new PluginArmaditoGeneralConf();
             $generalconf->showForm();
             break;
          case 1:
             $boardconf = new PluginArmaditoBoardConf();
             $boardconf->showForm();
             break;
          case 2:
             $stateconf = new PluginArmaditoStateConf();
             $stateconf->showForm();
             break;
          case 3:
             $alertconf = new PluginArmaditoAlertConf();
             $alertconf->showForm();
             break;
          case 4:
             $scanconf = new PluginArmaditoScanConf();
             $scanconf->showForm();
             break;
          case 5:
             $jobconf = new PluginArmaditoJobConf();
             $jobconf->showForm();
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

    function showFormFooter($options = array())
    {
        $options['candel'] = FALSE;
        $options['colspan'] = 4;
        $this->showFormButtons($options);
    }

    static function loadCache()
    {
        global $DB, $PF_CONFIG;

        //Test if table exists before loading cache
        //The only case where table doesn't exists is when you click on
        //uninstall the plugin and it's already uninstalled
        if ($DB->tableExists('glpi_plugin_armadito_configs')) {
            $PF_CONFIG = array();
            foreach ($DB->request('glpi_plugin_armadito_configs') as $data) {
                $PF_CONFIG[$data['type']] = $data['value'];
            }
        }
    }
}

?>
