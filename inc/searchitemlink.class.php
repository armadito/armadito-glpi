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

class PluginArmaditoSearchitemlink
{
    protected $table;
    protected $field;
    protected $name;
    protected $type;

    function __construct($field, $table, $type)
    {
        $this->field = $field;
        $this->table = $table;
        $this->type = $type;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function getOptions($tab, $i)
    {
        $tab = [];
        $tab['id']            = $i;
        $tab['table']         = $this->table;
        $tab['field']         = $this->field;
        $tab['name']          = __($this->name, 'armadito');
        $tab['datatype']      = 'itemlink';
        $tab['itemlink_type'] = $this->type;
        $tab['massiveaction'] = FALSE;
        return $tab;
    }
}
