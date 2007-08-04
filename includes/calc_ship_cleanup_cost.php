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
// File: calc_ship_cleanup_cost.php
function calc_ship_cleanup_cost($level_avg = 0, $type = 1)
{
    global $level_factor, $upgrade_cost;
  
    if ($type==1)
    {
        $c=1;
    }
    elseif ($type==2)
    {
        $c=2;
    }
    else
    {
        $c=4;
    }

    // You must check for upper boundary. Otherwise the typecast can cause it to flip to negative amounts.
    $cl_cost = (pow($level_factor, ($level_avg * 1.1)) * 80 * $upgrade_cost * $c);

    if ($cl_cost > 2000000000 || $cl_cost < 0)
    {
        $cl_cost = 2000000000;
    }
  
    return $cl_cost;
}
?>
