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
        $this->showAverageDurationsChart();
        echo "</tr>";
        echo "</table>";
    }

    function showJobStatusChart()
    {
        $params = array(
            "svgname" => 'jobstatus',
            "title"   => "Job(s) statuses",
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getJobStatusData()
        );

        $chart = new PluginArmaditoChart("DonutChart", $params);

        echo "<td width='380'>";
        $chart->showChart();
        echo "</td>";
    }

    function showLongestJobRunsChart()
    {
        $colortbox = new PluginArmaditoColorToolbox();

        $params = array(
            "svgname" => 'longest_job_runs',
            "title"   => __('Longest job runs', 'armadito'),
            "palette" => $colortbox->getPalette(12),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getLongestJobsData()
        );

        $bchart = new PluginArmaditoChart("HorizontalBarChart", $params);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
    }

    function showAverageDurationsChart()
    {
        $colortbox = new PluginArmaditoColorToolbox();

        $params = array(
            "svgname" => 'average_job_durations',
            "title"   => __('Average job durations', 'armadito'),
            "palette" => $colortbox->getPalette(12),
            "width"   => 370,
            "height"  => 400,
            "data"    => $this->getJobAverageDurationsData()
        );

        $bchart = new PluginArmaditoChart("VerticalBarChart", $params);

        echo "<td width='400'>";
        $bchart->showChart();
        echo "</td>";
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

    function countJobStatus($status)
    {
        return countElementsInTableForMyEntities('glpi_plugin_armadito_jobs', "`job_status`='" . $status . "'");
    }

    function getLongestJobsData()
    {
        global $DB;

        $query = "SELECT id, job_type, duration FROM `glpi_plugin_armadito_jobs` WHERE `is_deleted`='0'";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getLongestJobsData : %s', $DB->error()));
        }

        $arrayTop = new PluginArmaditoArrayTopChart(10);
        $arrayTop->init();

        if ($DB->numrows($ret) > 0)
        {
             while ($job = $DB->fetch_assoc($ret))
             {
                $value = PluginArmaditoToolbox::DurationToSeconds($job['duration']);
                $label = 'Job '.$job['id'].' ('.$job['job_type'].')';

                $arrayTop->insertIfRelevant($value, $label);
             }
        }

        return $arrayTop->toChartArray("job durations (secs)");
    }

    function getJobAverageDurationsData()
    {
        $data = array();
        $data['key'] = "Average Job durations";
        $job_types = $this->getJobTypesList();

        foreach ($job_types as $job_type)
        {
            $data['values'][] = array(
                'label' => $job_type,
                'value' => $this->getAverageDurationForJobType($job_type)
            );
        }

        return $data;
    }

    function getJobTypesList()
    {
        global $DB;

        $job_types = array();

        $query  = "SELECT DISTINCT `job_type` FROM `glpi_plugin_armadito_jobs`";
        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getJobTypesList : %s', $DB->error()));
        }

        if ($DB->numrows($ret) > 0)
        {
             while ($job = $DB->fetch_assoc($ret))
             {
                $job_types[] = $job['job_type'];
             }
        }

        return $job_types;
    }

    function getAverageDurationForJobType($job_type)
    {
        global $DB;

        $ret = $this->getJobDurationsFromDB($job_type);
        $average_duration = 0;

        if ($DB->numrows($ret) > 0)
        {
             $i = 0;
             while ($job = $DB->fetch_assoc($ret))
             {
                $average_duration += PluginArmaditoToolbox::DurationToSeconds($job['duration']);
                $i++;
             }

             if($i > 0) {
                $average_duration = $average_duration/$i;
             }
        }

        return $average_duration;
    }

    function getJobDurationsFromDB($job_type)
    {
        global $DB;

        $query  = "SELECT id, duration FROM `glpi_plugin_armadito_jobs` WHERE `is_deleted`='0' AND `job_status`='successful'";

        $ret   = $DB->query($query);

        if (!$ret) {
            throw new InvalidArgumentException(sprintf('Error getJobDurations : %s', $DB->error()));
        }

        return $ret;
    }
}
?>
