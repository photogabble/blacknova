<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: makebars.php
function MakeBars($level, $max)
{
    global $l_n_a, $templateset;

    if ($max == 0)
    {
        $max = 1;
    }
    $heath = ($level / $max);
    $heath_bars = round($heath * 10);

    $image = '';

    for ($i=0; $i<$heath_bars; $i++)
    {
        $bright = floor($i / 2) + 1;
        if ($bright > 5)
        {
            $bright = 5;
        }

        $image .= "<img src=\"templates/$templateset/images/dialon$bright.png\" alt=\"\">";
    }

    for ($i=0; $i<(10-$heath_bars); $i++)
    {
        $image .= "<img src=\"templates/$templateset/images/dialoff.png\" alt=\"\">";
    }

    if ($image == '')
    {
        $image = "<font style=\"font-size: 0.8em;\"><strong>$l_n_a</strong></font>";
    }

    return $image;
}
?>
