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

class PluginArmaditoProfileRights
{
    protected $type;
    protected $label;
    protected $field;

    function __construct($field, $type)
    {
        $this->field = $field;
        $this->type = $type;
    }

    function setLabel($label)
    {
        $this->label = $label;
    }

    function getRights()
    {
        return array(
            'rights' => array(
                READ => __('Read'),
                UPDATE => __('Update'),
                PURGE => __('Purge')
            ),
            'itemtype' => $this->type,
            'label' => __($this->label, 'armadito'),
            'field' => $this->field
        );
    }
}
?>
