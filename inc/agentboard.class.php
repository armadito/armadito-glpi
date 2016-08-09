<?php
/**

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


class PluginArmaditoAgentBoard extends CommonGLPI {

   /**
    * Display a board in HTML with JS libs (nvd3)
    *
    *@return nothing
    **/
   static function displayBoard() {

      $restrict_entity    = getEntitiesRestrictRequest(" AND", 'comp');

      echo "<table align='center'>";
      echo "<tr height='420'>";

      // Armadito Computers
      self::addComputersChart($restrict_entity);

      // Last Agent Connections
      self::addLastContactsChart($restrict_entity);

      echo "</tr>";
      echo "</table>";
   }

   /**
    * Get data and display last updates chart (bar)
    *
    *@return nothing
    **/
   static function addLastContactsChart($restrict_entity) {

      // Number of agent connections in last hour, 6 hours, 24 hours
      $data = PluginArmaditoLastContactStat::getLastHours();

      $title = __('Agent connections of last hours', 'armadito');
      $title = '';
      echo "<td width='400'>";
      PluginArmaditoBoard::showChartBar('nblastcontacts', $data, $title, 370);
      echo "</td>";

   }

   /**
    * Get data and display armadito computers chart (half donut)
    *
    *@return nothing
    **/
   static function addComputersChart($restrict_entity) {
      global $DB;

      $armaditoComputers    = 0;
      $query_ao_computers = "SELECT COUNT(comp.`id`) as nb_computers
                             FROM glpi_computers comp
                             LEFT JOIN glpi_plugin_armadito_agents ao_comp
                               ON ao_comp.`computers_id` = comp.`id`
                             WHERE comp.`is_deleted`  = '0'
                               AND comp.`is_template` = '0'
                               AND ao_comp.`id` IS NOT NULL
                               $restrict_entity";

      $res_ao_computers = $DB->query($query_ao_computers);
      if ($data_ao_computers = $DB->fetch_assoc($res_ao_computers)) {
         $armaditoComputers = $data_ao_computers['nb_computers'];
      }

      // All Computers
      $allComputers    = countElementsInTableForMyEntities('glpi_computers',
                                              "`is_deleted`='0' AND `is_template`='0'");

      $dataComputer = array();
      $dataComputer[] = array(
          'key' => __('Armadito computers', 'armadito').' : '.$armaditoComputers,
          'y'   => $armaditoComputers,
          'color' => '#3dff7d'
      );
      $dataComputer[] = array(
          'key' => __('Other computers', 'armadito').' : '.($allComputers - $armaditoComputers),
          'y'   => ($allComputers - $armaditoComputers),
          'color' => "#dedede"
      );

      echo "<td width='380'>";
      PluginArmaditoBoard::showChart('computers', $dataComputer);
      echo "</td>";
   }

}
?>
