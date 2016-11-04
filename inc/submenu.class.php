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


class PluginArmaditoSubMenu
{
    protected $name;
    protected $type;
    protected $width_status;
    protected $entries;

    function __construct($name, $type, $width_status)
    {
        $this->name = $name;
        $this->type = $type;
        $this->width_status = $width_status;
        $this->entries = array();
    }

    function addEntries($menu_entries)
    {
        foreach( $menu_entries as $entry )
        {
            $this->addEntry($entry);
        }
    }

    function addEntry($menu_entry)
    {
        array_push($this->entries, $menu_entry->get());
    }

    function display()
    {
        if (!empty($this->entries)) {
            $width_status = PluginArmaditoMenu::htmlMenu(__('General', 'armadito'), $this->entries, $this->type, $this->width_status);
        }
    }
}
