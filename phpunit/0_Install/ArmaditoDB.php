<?php

/*
Copyright (C) 2010-2016 by the FusionInventory Development Team.
Copytight (C) 2016 by Teclib'

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

*/

class ArmaditoDB extends PHPUnit_Framework_Assert
{
    protected $file_sql_tables;
    protected $db_sql_tables;
    protected $when;

    public function checkInstall($pluginname = '', $when = '')
    {
        if ($pluginname == '') {
            return;
        }

        $this->when = $when;
        $this->file_sql_tables  = array();
        $this->db_sql_tables  = array();

        $file_name = "plugin_" . $pluginname . "-empty.sql";
        $file_path = GLPI_ROOT . "/plugins/" . $pluginname . "/install/mysql/" . $file_name;

        $this->getTablesFromFile($file_path);
        $this->getTablesFromDatabase();

        $this->checkForMissingTables();
        $this->checkForUnexpectedTables();

        foreach ($this->db_sql_tables as $table => $fields)
        {
            $this->checkForMissingFields($table, $fields);
            $this->checkForUnexpectedFields($table, $fields);
        }
    }

    protected function formatArrayForDiff ( $a_tables_ref )
    {
        $a_tables_ref_tableonly = array();

        foreach ($a_tables_ref as $table => $data)
        {
            $a_tables_ref_tableonly[] = $table;
        }

        return $a_tables_ref_tableonly;
    }

    protected function getTablesFromFile($file_path)
    {
        $file_content  = file_get_contents($file_path);
        $a_lines       = explode("\n", $file_content);

        foreach ($a_lines as $line)
        {
            if (strstr($line, "CREATE TABLE ") || strstr($line, "CREATE VIEW"))
            {
                $table_name = $this->parseTableName($line);
            }
            else if (preg_match("/^`/", trim($line)))
            {
                $this->parseFileField($line, $table_name);
            }
        }
    }

    protected function getTablesFromDatabase()
    {
        global $DB;

        $a_tables    = array();
        $query       = "SHOW TABLES";
        $result      = $DB->query($query);

        while ($data = $DB->fetch_array($result)) {
            if (strstr($data[0], 'armadito')) {
                $data[0]    = str_replace(" COLLATE utf8_unicode_ci", "", $data[0]);
                $data[0]    = str_replace("( ", "(", $data[0]);
                $data[0]    = str_replace(" )", ")", $data[0]);

                $this->getTableFromDatabase($data[0]);
            }
        }

        return $a_tables;
    }

    protected function getTableFromDatabase($table)
    {
        global $DB;

        $query  = "SHOW CREATE TABLE " . $table;
        $result = $DB->query($query);
        while ($data = $DB->fetch_array($result))
        {
            $a_lines = explode("\n", $data['Create Table']);
            foreach ($a_lines as $line)
            {
                if (strstr($line, "CREATE TABLE ") || strstr($line, "CREATE VIEW"))
                {
                    $table_name = $this->parseTableName($line);
                }
                else if (preg_match("/^`/", trim($line)))
                {
                    $this->parseDatabaseField($line, $table_name);
                }
            }
        }
    }

    protected function parseTableName($line)
    {
        $matches = array();
        preg_match("/`(.*)`/", $line, $matches);
        return $matches[1];
    }

    protected function parseFileField($line, $table_name)
    {
        $s_line    = explode("`", $line);

        $field_name = $s_line[1];
        $field_type = $this->parseFieldType($line);

        $this->file_sql_tables[$table_name][$field_name] = $field_type;
    }

    protected function parseDatabaseField($line, $table_name)
    {
        $s_line    = explode("`", $line);

        $field_name = $s_line[1];
        $field_type = $this->parseFieldType($line);

        if (trim($field_type) == 'text' || trim($field_type) == 'longtext') {
            $field_type .= ' DEFAULT NULL';
        }

        $this->db_sql_tables[$table_name][$field_name] = $field_type;
    }

    protected function parseFieldType($line)
    {
        $s_type    = explode("COMMENT", $line);
        $s_type[0] = trim($s_type[0]);
        $s_type[0] = str_replace(" COLLATE utf8_unicode_ci", "", $s_type[0]);
        $s_type[0] = str_replace(" CHARACTER SET utf8", "", $s_type[0]);
        $s_type[0] = str_replace(",", "", $s_type[0]);

        return $s_type[0];
    }

    protected function checkForMissingTables()
    {
        $file_sql_tables = $this->formatArrayForDiff($this->file_sql_tables);
        $db_sql_tables = $this->formatArrayForDiff($this->db_sql_tables);

        $tables_toadd    = array_diff($file_sql_tables, $db_sql_tables);

        $this->assertEquals(count($tables_toadd), 0, 'Tables missing ' . $this->when . ' ' . print_r($tables_toadd, TRUE));
    }

    protected function checkForUnexpectedTables()
    {
        $file_sql_tables = $this->formatArrayForDiff($this->file_sql_tables);
        $db_sql_tables = $this->formatArrayForDiff($this->db_sql_tables);

        $tables_toremove = array_diff($db_sql_tables, $file_sql_tables);

        $this->assertEquals(count($tables_toremove), 0, 'Tables unexpected ' . $this->when . ' ' . print_r($tables_toremove, TRUE));
    }

    protected function checkForMissingFields($table, $dbfields)
    {
        if (isset($this->file_sql_tables[$table]))
        {
            $missing_fields    = array_diff_assoc($this->file_sql_tables[$table], $dbfields);

            $diff = $this->getFieldsDiff($table, $dbfields, $this->file_sql_tables[$table]);
            $assert_message = 'Fields missing/not good in ' . $this->when . ' ' . $table . ' ' . print_r($missing_fields, TRUE) . " into " . $diff;

            $this->assertEquals(count($missing_fields), 0, $assert_message);
        }
    }

    protected function checkForUnexpectedFields($table, $dbfields)
    {
        if (isset($this->file_sql_tables[$table]))
        {
            $unexpected_fields = array_diff_assoc($dbfields, $this->file_sql_tables[$table]);

            $diff = $this->getFieldsDiff($table, $dbfields, $this->file_sql_tables[$table]);
            $assert_message = 'Unexpected fields in ' . $this->when . ' ' . $table . ' ' . print_r($unexpected_fields, TRUE) . " into " . $diff;

            $this->assertEquals(count($unexpected_fields), 0, $assert_message);
        }
    }

    protected function getFieldsDiff($table, $dbfields, $filefields)
    {
            $diff  = "======= DB ============== Ref =======> " . $table . "\n";
            $diff .= print_r($dbfields, TRUE);
            $diff .= print_r($this->file_sql_tables[$table], TRUE);
            return $diff;
    }
}
?>
