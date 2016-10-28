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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

include_once("toolbox.class.php");

class PluginArmaditoProfile extends Profile
{

    function getRightsGeneral()
    {
        return array(
            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update')
                ),
                'label' => __('Menu', 'armadito'),
                'field' => 'plugin_armadito_menu'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update'),
                    PURGE => __('Purge')
                ),
                'itemtype' => 'PluginArmaditoJob',
                'label' => __('Jobs', 'armadito'),
                'field' => 'plugin_armadito_jobs'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update')
                ),
                'itemtype' => 'PluginArmaditoState',
                'label' => __('States', 'armadito'),
                'field' => 'plugin_armadito_states'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update')
                ),
                'itemtype' => 'PluginArmaditoStateDetail',
                'label' => __('Statedetails', 'armadito'),
                'field' => 'plugin_armadito_statedetail'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update'),
                    PURGE => __('Purge')
                ),
                'itemtype' => 'PluginArmaditoAlert',
                'label' => __('Alerts', 'armadito'),
                'field' => 'plugin_armadito_alerts'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update'),
                    PURGE => __('Purge')
                ),
                'itemtype' => 'PluginArmaditoScan',
                'label' => __('Scans', 'armadito'),
                'field' => 'plugin_armadito_scans'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update')
                ),
                'itemtype' => 'PluginArmaditoAntivirus',
                'label' => __('Antiviruses', 'armadito'),
                'field' => 'plugin_armadito_antiviruses'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update'),
                    PURGE => __('Purge')
                ),
                'itemtype' => 'PluginArmaditoScanConfig',
                'label' => __('Scan configuration', 'armadito'),
                'field' => 'plugin_armadito_scanconfigs'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update')
                ),
                'itemtype' => 'PluginArmaditoConfig',
                'label' => __('Configuration', 'armadito'),
                'field' => 'plugin_armadito_configuration'
            ),

            array(
                'rights' => array(
                    READ => __('Read'),
                    UPDATE => __('Update')
                ),
                'itemtype' => 'PluginArmaditoAgent',
                'label' => __('Agents', 'armadito'),
                'field' => 'plugin_armadito_agents'
            )
        );
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        return createTabEntry('Armadito');
    }

    function getAllRights()
    {
        return $this->getRightsGeneral();
    }

    static function uninstallProfile()
    {
        $paProfile = new self();
        $a_rights  = $paProfile->getAllRights();

        foreach ($a_rights as $data) {
            ProfileRight::deleteProfileRights(array(
                $data['field']
            ));
        }
    }

    static function initProfile()
    {
        $paProfile = new self();
        $a_rights  = $paProfile->getAllRights();

        foreach ($a_rights as $data) {
            if (countElementsInTable("glpi_profilerights", "`name` = '" . $data['field'] . "'") == 0) {
                ProfileRight::addProfileRights(array(
                    $data['field']
                ));
                $_SESSION['glpiactiveprofile'][$data['field']] = 0;
            }
        }

        if (isset($_SESSION['glpiactiveprofile'])) {
           $paProfile->addRightsToActiveProfile($a_rights);
        }
    }

    function addRightsToActiveProfile($a_rights)
    {
        $dataprofile       = array();
        $dataprofile['id'] = $_SESSION['glpiactiveprofile']['id'];

        $profile   = new Profile();
        $profile->getFromDB($dataprofile['id']);

        foreach ($a_rights as $info) {
            $dataprofile = $this->addRightsToProfile($info, $dataprofile);
        }

        $profile->update($dataprofile);
    }

    function addRightsToProfile($info, $dataprofile)
    {
       if (is_array($info)
            && ((!empty($info['itemtype'])) || (!empty($info['rights'])))
             && (!empty($info['label'])) && (!empty($info['field']))) {

            $rights = $this->getRightsForItem($info);

            foreach ($rights as $right => $label)
            {
                $dataprofile['_' . $info['field']][$right]     = 1;
                $_SESSION['glpiactiveprofile'][$info['field']] = $right;
            }
        }

        return $dataprofile;
    }

    function getRightsForItem($info)
    {
        if (isset($info['rights'])) {
            return $info['rights'];
        } else {
            return $this->getRightsFor($info['itemtype']);
        }
    }
}
?>
