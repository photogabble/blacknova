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
// File: sched_degrade.php

dynamic_loader ($db, "playerlog.php");

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_degrade.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php"); 
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

echo "<strong>DEGRADE</strong><br>\n";
echo "Degrading Sector Fighters with no friendly base<br>";

for ($j = 0; $j<$multiplier; $j++)
{
    $res = $db->Execute("SELECT player_id, defense_id, sector_id, quantity FROM {$db->prefix}sector_defense WHERE defense_type = 'F'");

    while (!$res->EOF)
    {
        $row = $res->fields;
        $res3 = $db->SelectLimit("SELECT team FROM {$db->prefix}players WHERE player_id=?",1,-1,array($row['player_id']));
        $sched_playerinfo = $res3->fields;

        $res2 = $db->Execute("SELECT energy, planet_id FROM {$db->prefix}planets WHERE ". 
                             "(owner = $row[player_id] || (team = $sched_playerinfo[team] && $sched_playerinfo[team] != 0)) && " .
                             "sector_id = $row[sector_id] and energy > 0"); 
        $owned_planets = $res2->fields;

        if ($res2->EOF) // If there are fighters in a sector, and the owner doesnt have planets in the sector with energy
        {     
            if ($row['defense_id'] != "")
            {
                sql_sched_degrade_defenses($row['defense_id']);
                $degrade_rate = $defense_degrade_rate * 100;
                playerlog($db,$row['player_id'], "LOG_DEFENSE_DEGRADE", "$row[sector_id]|$degrade_rate");
            }
        }
        else // Else, if the owner of the fighters HAS planets in the sector select the planets he has that have enough energy.
        {
            $energy_required = ROUND($row['quantity'] * $energy_per_fighter);
            $energy_available = $owned_planets['energy'];
            echo "available $energy_available, required $energy_required.<br>\n";

            if ($energy_available > $energy_required)
            {
                while (!$res2->EOF) // While there is a planet that can support it, remove the energy from it.
                {
                    $degrade_row = $res2->fields;
                    sql_sched_degrade_energy($degrade_row['planet_id']);
                    $res2->MoveNext();
                }
            }
            else // no planet has enough energy to support the fighters.
            {
                sql_sched_degrade_defenses($row['defense_id']);
                $degrade_rate = $defense_degrade_rate * 100;
                playerlog($db,$row['player_id'], "LOG_DEFENSE_DEGRADE", "$row[sector_id]|$degrade_rate");  
            }
        }
        $res->MoveNext();
    }
    $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE quantity < 1");
}

echo "Sector defense degradation completed<br>\n";
echo "<br>";
?>
