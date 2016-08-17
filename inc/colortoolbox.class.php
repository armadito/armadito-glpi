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

/**
 * Toolbox of various utility methods to manipulate colors
 **/
class PluginArmaditoColorToolbox {
   protected $h;

    function __construct() {
         $this->h = -1;
    }

   /**
   *  Get nice random colors using golden ratio
   *  Reference : http://martin.ankerl.com/2009/12/09/how-to-create-random-colors-programmatically/
   **/
   function getRandomPalette ( $nb_colors ) {
      $this->h = mt_rand() / mt_getrandmax();
      $palette = array();
      for($i = 0; $i < $nb_colors; $i++) {
         $palette[] = $this->getGoldenColor();
      }
      return $palette;
   }

   function getGoldenColor() {
         $golden_ratio_conjugate = 0.618033988749895;
         $this->h = $this->h + $golden_ratio_conjugate;
         $this->h = fmod($this->h, 1);
         return "#".$this->HSVtoHexa($this->h, 0.5, 0.95);
   }

   function HSVtoHexa($h, $s, $v) {
        $h_i = intval($h*6);
        $f = $h*6 - $h_i;
        $p = $v * (1 - $s);
        $q = $v * (1 - $f*$s);
        $t = $v * (1 - (1 - $f) * $s);

        switch($h_i) {
           case 0:
              $r = $v;
              $g = $t;
              $b = $p;
              break;
           case 1:
              $r = $q;
              $g = $v;
              $b = $p;
              break;
           case 2:
              $r = $p;
              $g = $v;
              $b = $t;
              break;
           case 3:
              $r = $p;
              $g = $q;
              $b = $v;
              break;
           case 4:
              $r = $t;
              $g = $p;
              $b = $v;
              break;
           case 5:
              $r = $v;
              $g = $p;
              $b = $q;
              break;
        }

        // echo "r = ".round($r*255).", g =".round($g*255).", b=".round($b*255)."<br>";
        $color = dechex( (round($r*255) << 16) + (round($g*255) << 8) + round($b*255) );
        $color = str_repeat('0', 6 - strlen($color)) . $color;
        return $color;
    }

    static function getRainbowColors () {
         $colors = array(
            "violet" => "#8B00FF",
            "indigo" => "#4B0082",
            "blue" => "#0000FF",
            "green" => "#00FF00",
            "yellow" => "#FFFF00",
            "orange" => "#FF7F00",
            "red" => "#FF0000"
         );
         return $colors;
     }
}

?>
