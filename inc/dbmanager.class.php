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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginArmaditoDbManager
{
    protected $statements = array();
    protected $queries = array();

    function __construct()
    {
    }

    function setQueryValue($name, $property_name, $property_value)
    {
        $this->queries[$name]["params"][$property_name]["value"] = $property_value;
    }

    function addQuery($name, $type, $table, $params, $where_params = array())
    {
        $query = array();

        switch ($type) {
            case "INSERT":
                $query["query"] = $this->addInsertQuery($table, $params);
                break;
            case "UPDATE":
                $query["query"] = $this->addUpdateQuery($table, $params, $where_params);
                break;
            default:
                return 1;
        }

        $query["type"]        = $type;
        $query["params"]      = $params;
        $this->queries[$name] = $query;
        return 0;
    }

    function addInsertQuery($table, $params)
    {
        $query = "INSERT INTO `" . $table . "` (";
        foreach ($params as $property_name => $property_type) {
            $query .= "`" . $property_name . "`,";
        }

        $query = rtrim($query, ",");
        $query .= ") VALUES (";

        for ($i = 0; $i < count($params); $i++) {
            $query .= "?,";
        }

        $query = rtrim($query, ",");
        $query .= ")";

        PluginArmaditoToolbox::logE($query);

        return $query;
    }

    function addUpdateQuery($table, $params, $where_params)
    {

        if (!is_array($where_params)) {
            $tmp             = $where_params;
            $where_params    = array();
            $where_params[0] = $tmp;
        }

        $query = "UPDATE `" . $table . "` SET";
        foreach ($params as $property_name => $property_type) {
            if (!in_array($property_name, $where_params)) {
                $query .= " `" . $property_name . "`=?,";
            }
        }

        $query = rtrim($query, ",");
        $query .= " WHERE";

        $i = 0;
        foreach ($where_params as $where_param) {
            if ($i > 0) {
                $query .= " AND";
            }
            $query .= " `" . $where_param . "`=?";
            $i++;
        }

        PluginArmaditoToolbox::logE($query);

        return $query;
    }

    function prepareQuery($name)
    {
        global $DB;
        $this->statements[$name] = $DB->prepare($this->queries[$name]["query"]);
        if (!$this->statements[$name]) {
            throw new PluginArmaditoDbException('prepareQuery ' . $name . ' failed.');
        }
    }

    function bindQuery($name)
    {
        $ref    = new ReflectionClass('mysqli_stmt');
        $method = $ref->getMethod("bind_param");
        if (!$method->invokeArgs($this->statements[$name], $this->getbindQueryArgs($name))) {
            $this->closeQuery($name);
            throw new PluginArmaditoDbException('bindQuery ' . $name . ' failed.');
        }
    }

    function getbindQueryArgs($name)
    {
        $bindargs = array("");
        foreach ($this->queries[$name]["params"] as $property_name => $property_array) {
            $bindargs[0] .= $property_array["type"];
            $bindargs[] =& $this->queries[$name]["params"][$property_name]["value"];
        }
        return $bindargs;
    }

    function executeQuery($name)
    {
        if (!$this->statements[$name]->execute()) {
            $lasterror = $this->statements[$name]->error;
            $this->closeQuery($name);
            throw new PluginArmaditoDbException('executeQuery ' . $name . ' failed. ' . $lasterror);
        }

        $this->closeQuery($name);
    }

    function closeQuery($name)
    {
        return $this->statements[$name]->close();
    }
}
?>
