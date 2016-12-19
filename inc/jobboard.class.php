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

class PluginArmaditoJobBoard extends PluginArmaditoBoard
{

    function __construct()
    {
    }

    function displayBoard()
    {
        echo "<table align='center'>";
        echo "<tr height='420'>";
        $this->showJobStatusChart();
        $this->showLongestJobRunsChart();
        echo "</tr>";
        echo "</table>";
    }

    function showLongestJobRunsChart()
    {
        $data = $this->getLongestJobsData();

        $colortbox = new PluginArmaditoColorToolbox();
        $palette   = $colortbox->getPalette(12);

        $bchart = new PluginArmaditoChartHorizontalBar();
        $bchart->init('longest_job_runs', __('Longest job runs', 'armadito'), $data);
        $bchart->setPalette($palette);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
    }

    function showJobStatusChart()
    {
        $data  = $this->getJobStatusData();
        $chart = new PluginArmaditoChartHalfDonut();
        $chart->init('jobstatus', "Job(s) statuses", $data);

        echo "<td width='380'>";
        $chart->showChart();
        echo "</td>";
    }

    function countJobStatus($status)
    {
        return countElementsInTableForMyEntities('glpi_plugin_armadito_jobs', "`job_status`='" . $status . "'");
    }

    function getJobStatusData()
    {
        $statuses = PluginArmaditoJob::getAvailableStatuses();

        $data = array();
        foreach ($statuses as $name => $color) {
            $n_status = $this->countJobStatus($name);
            $data[]   = array(
                'key' => __($name, 'armadito'),
                'value' => $n_status,
                'color' => $color
            );
        }

        return $data;
    }

    function getLongestJobsData()
    {
        global $DB;

        $query = "SELECT id, job_type, duration FROM `glpi_plugin_armadito_jobs` WHERE `is_deleted`='0'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getLongestJobsData : %s', $DB->error()));
        }

        $longest_jobs = $this->initLongestJobsArray(10);

        if ($DB->numrows($ret) > 0)
        {
             while ($job = $DB->fetch_assoc($ret))
             {
                $longest_jobs = $this->addToLongestJobsIfNeeded($longest_jobs, 10, $job);
             }
        }

        return $this->toChartArray($longest_jobs, "longest_jobs", 10);
    }

    function initLongestJobsArray($size)
    {
        $longest_jobs = array();
        for($i = 0; $i < $size; $i++){
            $longest_jobs[$i]['value'] = 0;
            $longest_jobs[$i]['label'] = '';
        }

        return $longest_jobs;
    }

    function addToLongestJobsIfNeeded($longest_jobs, $size, $job)
    {
        $job_secs_duration = PluginArmaditoToolbox::DurationToSeconds($job['duration']);

        for($i = 0; $i < $size; $i++) {
            if($job_secs_duration >= $longest_jobs[$i]['value']) {
                $longest_jobs = $this->rotateLongestJobsArray($longest_jobs, $size, $i);

                $longest_jobs[$i]['value'] = $job_secs_duration;
                $longest_jobs[$i]['label'] = 'Job '.$job['id'].' ('.$job['job_type'].')';
                break;
            }
        }

        return $longest_jobs;
    }

    function rotateLongestJobsArray($longest_jobs, $size, $i_to_beinserted)
    {
         $rotated_array = array();
         for($i = 0; $i < $size; $i++)
         {
            if($i > $i_to_beinserted && $i > 0) {
                $rotated_array[$i] = $longest_jobs[$i-1];
            }
            else{
                $rotated_array[$i] = $longest_jobs[$i];
            }
         }

         return $rotated_array;
    }

    function toChartArray($jobs, $key, $size)
    {
        $data = array();
        $data['key'] = $key;
        for($i = 0; $i < $size; $i++) {

            $data['values'][] = array(
                'label' => $jobs[$i]['label'],
                'value' => $jobs[$i]['value']
            );
        }

        return $data;
    }
}
?>
