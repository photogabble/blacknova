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
// File: includes/spy_sneak_to_planet.php

function spy_sneak_to_planet($db,$planet_id, $ship_id)
{
    // Dynamic functions
    dynamic_loader ($db, "seed_mt_rand.php");
    dynamic_loader ($db, "playerlog.php");

    global $max_spies_per_planet;
    global $sneak_toplanet_success;

    seed_mt_rand();
    $res = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE ship_id=? AND active='Y' AND move_type='toplanet' ", array($ship_id));
    while (!$res->EOF)
    {
        $spy = $res->fields;

        $i = mt_rand(1,100);
        if ($i <= $sneak_toplanet_success)
        {
            $res2 = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=? AND owner_id=?", array($planet_id, $spy['owner_id']));
            if ($res2->RecordCount() < $max_spies_per_planet)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET planet_id=?, ship_id = '0', job_id = '0', spy_percent = '0', move_type='none' WHERE spy_id=?", array($planet_id, $spy['spy_id'])); 
                db_op_result($db,$debug_query,__LINE__,__FILE__);
  
                $debug_query = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}planets.name, {$db->prefix}planets.sector_id, {$db->prefix}players.character_name, {$db->prefix}ships.name as ship_name FROM {$db->prefix}spies INNER JOIN {$db->prefix}planets ON {$db->prefix}spies.planet_id = {$db->prefix}planets.planet_id INNER JOIN {$db->prefix}players ON {$db->prefix}planets.owner = {$db->prefix}players.player_id INNER JOIN {$db->prefix}ships ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id WHERE {$db->prefix}spies.spy_id=?", array($spy['spy_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
        
                $info = $debug_query->fields;
                playerlog($db,$info['owner_id'], "LOG_SPY_TOPLANET", "$info[spy_id]|$info[name]|$info[sector_id]|$info[character_name]|$info[ship_name]");
            }
        }
    $res->MoveNext();
    }
}
?>
