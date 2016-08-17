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
      $this->h = rand();
      $palette = array();
      for($i = 0; $i < $nb_colors; $i++) {
         $palette[] = getGoldenColor();
      }
      return $palette;
   }

   function getGoldenColor() {
         $golden_ratio_conjugate = 0.618033988749895;
         $this->h += $golden_ratio_conjugate;
         $this->h %= 1;
         return "#" + HSVtoHexa(h, 0.5, 0.95);
   }

    /*
    **  Converts HSV to RGB values
    ** –––––––––––––––––––––––––––––––––––––––––––––––––––––
    **  References : http://en.wikipedia.org/wiki/HSL_and_HSV
    **             https://gist.github.com/Jadzia626/2323023
    **  Purpose:   Useful for generating colours with
    **             same hue-value for web designs.
    **  Input:     Hue        (H) Integer 0-360
    **             Saturation (S) Integer 0-100
    **             Lightness  (V) Integer 0-100
    **  Output:    String "R,G,B"
    **             Suitable for CSS function RGB().
    */
    function HSVtoHexa($iH, $iS, $iV) {
        if($iH < 0)   $iH = 0;   // Hue:
        if($iH > 360) $iH = 360; //   0-360
        if($iS < 0)   $iS = 0;   // Saturation:
        if($iS > 100) $iS = 100; //   0-100
        if($iV < 0)   $iV = 0;   // Lightness:
        if($iV > 100) $iV = 100; //   0-100
        $dS = $iS/100.0; // Saturation: 0.0-1.0
        $dV = $iV/100.0; // Lightness:  0.0-1.0
        $dC = $dV*$dS;   // Chroma:     0.0-1.0
        $dH = $iH/60.0;  // H-Prime:    0.0-6.0
        $dT = $dH;       // Temp variable
        while($dT >= 2.0) $dT -= 2.0; // php modulus does not work with float
        $dX = $dC*(1-abs($dT-1));     // as used in the Wikipedia link
        switch(floor($dH)) {
            case 0:
                $dR = $dC; $dG = $dX; $dB = 0.0; break;
            case 1:
                $dR = $dX; $dG = $dC; $dB = 0.0; break;
            case 2:
                $dR = 0.0; $dG = $dC; $dB = $dX; break;
            case 3:
                $dR = 0.0; $dG = $dX; $dB = $dC; break;
            case 4:
                $dR = $dX; $dG = 0.0; $dB = $dC; break;
            case 5:
                $dR = $dC; $dG = 0.0; $dB = $dX; break;
            default:
                $dR = 0.0; $dG = 0.0; $dB = 0.0; break;
        }
        $dM  = $dV - $dC;
        $dR += $dM; $dG += $dM; $dB += $dM;
        $dR *= 255; $dG *= 255; $dB *= 255;
        $dR = str_pad(dechex(round($dR)), 2, "0", STR_PAD_LEFT);
        $dG = str_pad(dechex(round($dG)), 2, "0", STR_PAD_LEFT);
        $dB = str_pad(dechex(round($dB)), 2, "0", STR_PAD_LEFT);
        return $dR.$dG.$dB;
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
