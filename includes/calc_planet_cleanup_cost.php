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
// File: includes/calc_planet_cleanup_cost.php
function calc_planet_cleanup_cost($db,$colo = 0, $type = 1)
{
    global $planet_id, $planetinfo;
    global $colonist_limit, $max_spies_per_planet;
    global $spy_cleanup_planet_credits1;
    global $spy_cleanup_planet_credits2;
    global $spy_cleanup_planet_credits3;
  
    $spy_cleanup_planet_credits[1] = $spy_cleanup_planet_credits1;
    $spy_cleanup_planet_credits[2] = $spy_cleanup_planet_credits2;
    $spy_cleanup_planet_credits[3] = $spy_cleanup_planet_credits3;

    $col_lim = $colonist_limit / 1000000;
    $cred_lim = $spy_cleanup_planet_credits[$type] / 1000000;
    $colonists = $colo / 1000000;
  
    //// Constansts to create the S-curve function
    $c1 = 0.75 * $cred_lim;
    $c2 = 0.5 * $col_lim;
    $c3 = $c1 / pow($c2, 2);
    $c4 = pow($cred_lim - $c1, 2) / ($col_lim - $c2);
    $c5 = 0.1 * $col_lim;
    $c6 = 1/30 * $cred_lim;

    if ($colonists <= $c5)
    {
        $cl_cost = $c6;
    }
    elseif ($colonists > $c5 && $colonists <= $c2)
    {
        $cl_cost = $c3 * pow($colonists, 2);
    }
    else
    {
        $cl_cost = SQRT($c4 * ($colonists - $c2)) + $c1;
    }

    $cl_cost = ($cl_cost * 1000000);

    // Here we reduce the costs of scans by 9.9% per spy the owner has on the planet.
    $res66 = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=? AND owner_id=?", array($planet_id, $planetinfo['owner']));
    $spies_on_planet = $res66->RecordCount();
  
    $cl_cost = ($cl_cost - ($cl_cost * $spies_on_planet / $max_spies_per_planet * 99/100) );  
    
    // You must check for upper boundary. Otherwise the typecast can cause it to flip to negative amounts.
    if ($cl_cost > 2000000000 || $cl_cost < 0)
    {
        $cl_cost = 2000000000;
    }

    return $cl_cost;
}
?>
