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

class EnrollmentKeyTest extends CommonTestCase
{
    /**
     * @test
     */
    public function FormAdd()
    {
        $this->NewEnrollmentKey(1, "2017-01-12 11:06","2025-01-12 13:06", 10, "0RV2V-2RYTX-NX4OW-7LARZ-KZQAJ");
        $this->NewEnrollmentKey(2, "2017-01-12 11:06","2019-01-12 11:16", 15, "9LTKF-WTDYW-MFU83-XKYEN-P4VEO");
    }

    protected function NewEnrollmentKey($id, $creation_date, $expiration_date , $use_counter, $key)
    {
        $enrollmentkey = $this->initEnrollmentKey($id, $creation_date, $expiration_date , $use_counter);
        $enrollmentkey->setValue($key);
        $enrollmentkey->insertEnrollmentKeyInDB();
    }

    protected function initEnrollmentKey($id, $creation_date, $expiration_date, $use_counter)
    {
        $data = array(
            "id" => $id,
            "creation_date" => $creation_date,
            "expiration_date" => $expiration_date,
            "use_counter" => $use_counter
        );

        $enrollmentkey = new PluginArmaditoEnrollmentKey();
        $enrollmentkey->initFromForm($data);
        return $enrollmentkey;
    }
}

