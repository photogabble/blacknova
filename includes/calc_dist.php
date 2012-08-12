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
// File: includes/calc_dist.php

// Calculate the distance between two sectors.
function calc_dist($db, $src, $dst)
{
    $results = $db->Execute("SELECT x,y,z FROM {$db->prefix}universe WHERE " .
                            "sector_id=? OR sector_id=?", array($src, $dst));
    db_op_result($db,$results,__LINE__,__FILE__);

    // Make sure you check for this when calling this function.
    if (!$results)
    {
        die("Unspecified error in calc_dist: src is " . $src . ", dst is " . $dst);
        // This is a bit harsh, but I want to ensure that we hear about it if this occurs.
        // If not, we can reduce this check.
    }

    $x = $results->fields['x'];
    $y = $results->fields['y'];
    $z = $results->fields['z'];

    $results->MoveNext();

    $x -= $results->fields['x'];
    $y -= $results->fields['y'];
    $z -= $results->fields['z'];

    $x = sqrt(($x*$x) + ($y*$y) + ($z*$z));

    // Make sure it's never less than 1.
    if ($x < 1)
    {
        $x = 1;
    }

    return round($x);
}
?>
