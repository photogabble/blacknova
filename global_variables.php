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
// File: global_variables.php

$pos = (strpos($_SERVER['PHP_SELF'], "/global_variables.php"));
if ($pos !== false)
{
    include_once './global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once './footer.php';
    die();
}

// These spy values are most array based, and thus difficult to implement in the config_values db.
$spy_cleanup_ship_turns[1] = 2;
$spy_cleanup_ship_turns[2] = 4;
$spy_cleanup_ship_turns[3] = 6;
$spy_cleanup_planet_turns[1] = 1;
$spy_cleanup_planet_turns[2] = 2;
$spy_cleanup_planet_turns[3] = 3;
$spy_cleanup_planet_credits[1] = 2000000; //Max values. Actual values depend on number of colonists on the planet
$spy_cleanup_planet_credits[2] = 4000000;
$spy_cleanup_planet_credits[3] = 8000000;

$planettypes[0] = "tinyplanet";
$planettypes[1] = "smallplanet";
$planettypes[2] = "mediumplanet";
$planettypes[3] = "largeplanet";
$planettypes[4] = "hugeplanet";

$startypes[0] = "";
$startypes[1] = "redstar";
$startypes[2] = "orangestar";
$startypes[3] = "yellowstar";
$startypes[4] = "greenstar";
$startypes[5] = "bluestar";
?>
