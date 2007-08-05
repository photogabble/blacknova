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
// File: includes/spy_sneak_to_ship.php

function spy_sneak_to_ship($db,$planet_id, $ship_id)
{
    // Dynamic functions
    dynamic_loader ($db, "seed_mt_rand.php");
    dynamic_loader ($db, "playerlog.php");

    global $sneak_toship_success;

    seed_mt_rand();
    $i=0;
    $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}planets.name, {$db->prefix}planets.sector_id, {$db->prefix}players.character_name, {$db->prefix}ships.name AS ship_name FROM {$db->prefix}players INNER JOIN {$db->prefix}ships ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id INNER JOIN {$db->prefix}planets ON {$db->prefix}players.player_id = {$db->prefix}planets.owner INNER JOIN {$db->prefix}spies ON {$db->prefix}planets.planet_id = {$db->prefix}spies.planet_id WHERE {$db->prefix}spies.planet_id = $planet_id AND {$db->prefix}spies.active = 'Y' AND {$db->prefix}spies.job_id = '0' AND {$db->prefix}spies.move_type != 'none' "); 
    while (!$res->EOF)
    {
        $spy = $res->fields;
        $flag=1;
        for ($j=1; $j<=$i; $j++)
        {
            if ($spy['owner_id'] == $changers[$j])
            {
                $flag = 0;
            }
        }
    
        if ($flag)
        {
            $k = mt_rand(1,100);
            if ($k <= $sneak_toship_success)
            {
                $res2 = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE ship_id = '$ship_id' AND active = 'Y' AND owner_id = $spy[owner_id] "); 
                if ($res2->EOF) // No spies on ship
                {
                    $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET planet_id = '0', ship_id = '$ship_id', job_id = '0', spy_percent = '0' WHERE spy_id = $spy[spy_id] "); 
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    playerlog($db,$spy['owner_id'], "LOG_SPY_TOSHIP", "$spy[spy_id]|$spy[name]|$spy[sector_id]|$spy[character_name]|$spy[ship_name]");
                    $i++;
                    $changers[$i] = $spy['owner_id'];
                }
            }
        }
    $res->MoveNext();
    }
}
?>
