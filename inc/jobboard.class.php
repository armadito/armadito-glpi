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

class PluginArmaditoJobBoard extends PluginArmaditoBoard {

    function __construct() {
         //
    }

   /**
    * Display a board in HTML with JS libs (nvd3)
    *
    *@return nothing
    **/
   function displayBoard() {

      $restrict_entity = getEntitiesRestrictRequest(" AND", 'comp');
      $data = $this->getJobStatusData($restrict_entity);

      echo "<table align='center'>";
      echo "<tr height='420'>";
      $this->showJobStatusChart($data);
      echo "</tr>";
      echo "</table>";
   }

   /**
    * Get data and display Jobs' statuses (half donut)
    *
    *@return nothing
    **/
  function showJobStatusChart($data) {

      $chart = new PluginArmaditoChartHalfDonut();
      $chart->init('jobstatus', "Job statuses" , $data);

      echo "<td width='380'>";
      $chart->showChart();
      echo "</td>";
   }

   /**
    * Get data
    *
    *@return nothing
    **/
   function getJobStatusData ($restrict_entity) {
      global $DB;

      $data = array();
      $data[] = array(
          'key' => __('Successful', 'armadito').' : 20',
          'y'   => 20,
          'color' => '#3dff7d'
      );
      $data[] = array(
          'key' => __('Downloaded', 'armadito').' : 48',
          'y'   => 45,
          'color' => "#dedede"
      );

      return $data;
   }
}
?>
