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
// File: includes/simple_date.php

// Recognizes only some (d, j, F, M, Y, H, i) format string components!
function simple_date($frmtstr, $full_year, $month_full, $month_short, $day, $hour, $min)
{
    $retvalue="";
    $temp_sizeof = strlen($frmtstr);
    for ($cntr=0; $cntr<$temp_sizeof; $cntr++)
    {
        switch (substr($frmtstr,$cntr,1))
        {
            case "d":
                if (strlen($day)==1)
                {
                    $retvalue .= "0$day";
                }
                else
                {
                    $retvalue .= $day;
                }
            break;

            case "j":
                $retvalue .= number_format($day, 0, $local_number_dec_point, $local_number_thousands_sep);
            break;

            case "F":
                $retvalue .= $month_full;
            break;

            case "M":
                $retvalue .= $month_short;
            break;

            case "Y":
                $retvalue .= $full_year;
            break;

            case "H":
                if (strlen($hour)==1)
                {
                    $retvalue .= "0$hour";
                }
                else
                {
                    $retvalue .= $hour;
                }
            break;

            case "i":
                if (strlen($min)==1)
                {
                    $retvalue .= "0$min";
                }
                else
                {
                    $retvalue .= $min;
                }
            break;

            default:
                $retvalue .= substr($frmtstr,$cntr,1);
            break;
        }
    }
    return $retvalue;
}
?>
