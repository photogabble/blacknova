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
// File: includes/spyrand.php

function spyrand($lower, $upper, $distribution_const = 1)
{
    $max_random = mt_getrandmax();

    if ($distribution_const == 1)
    {
        return floor($lower + ($upper-$lower+1)*mt_rand(0,$max_random)/($max_random+1));
    }
    elseif ($distribution_const > 1)
    {
        return floor($lower + ($upper-$lower+1)*pow(mt_rand(0,$max_random)/($max_random+1),$distribution_const));
    }
    else
    {
        return floor($lower + ($upper-$lower+1)*pow(mt_rand(1,$max_random)/($max_random+1),$distribution_const));
        // It could be 0..$max_random, but for example, POW(0, 0.8) returns error
    }
}
?>
