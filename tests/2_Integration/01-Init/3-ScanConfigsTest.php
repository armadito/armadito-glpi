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

class ScanConfigsTest extends CommonTestCase
{
    /**
     * @test
     */
    public function FormAdd()
    {
        $this->NewScanConfig(0, "linux home scan" , 1, "/home", "");
        $this->NewScanConfig(0, "linux full scan" , 1, "/", "--no-archive");
        $this->NewScanConfig(0, "linux fast scan" , 1, "/tmp", "--no-archive");

        $this->UpdateScanConfig(3, "linux fast scan" , 1, "/tmp", "");
    }

    protected function NewScanConfig($id, $scan_name, $antivirusid, $scan_path, $scan_options)
    {
        $scanConfig = $this->initScanConfig($id, $scan_name, $antivirusid, $scan_path, $scan_options);
        $scanConfig->insertScanConfigInDB();
    }

    protected function UpdateScanConfig($id, $scan_name, $antivirusid, $scan_path, $scan_options)
    {
        $scanConfig = $this->initScanConfig($id, $scan_name, $antivirusid, $scan_path, $scan_options);
        $scanConfig->updateScanConfigInDB();
    }

    protected function initScanConfig($id, $scan_name, $antivirusid, $scan_path, $scan_options)
    {
        $data = array(
            "id" => $id,
            "scan_name" => $scan_name,
            "antivirus_id" => $antivirusid,
            "scan_path" => $scan_path,
            "scan_options" => $scan_options
        );

        $scanConfig = new PluginArmaditoScanConfig();
        $scanConfig->initFromForm($data);
        return $scanConfig;
    }
}

