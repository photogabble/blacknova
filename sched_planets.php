<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: sched_planets.php

if (preg_match("/sched_planets.php/i", $_SERVER['PHP_SELF']))
{
    echo "You can not access this file directly!";
    die();
}

echo "<B>PLANETS</B><p>";

$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE owner >0;");
// Using Planet Update Code from BNT version 0.36 due to code bugs.
// We are now using transactions to off load the SQL stuff in full to the Database Server.

$db->Execute("START TRANSACTION;");
while (!$res->EOF)
{
    $row = $res->fields;
    $production = floor(min($row['colonists'], $colonist_limit) * $colonist_production_rate);
    $organics_production = floor($production * $organics_prate * $row['prod_organics'] / 100.0);// - ($production * $organics_consumption);
    $organics_production -= floor($production * $organics_consumption);

    if ($row['organics'] + $organics_production < 0)
    {
        $organics_production = -$row['organics'];
        $starvation = floor($row['colonists'] * $starvation_death_rate);
        if ($row['owner'] && $starvation >= 1)
        {
            playerlog ($db, $dbtables, $row['owner'], LOG_STARVATION, "$row[sector_id]|$starvation");
        }
    }
    else
    {
        $starvation = 0;
    }

    $ore_production = floor($production * $ore_prate * $row['prod_ore'] / 100.0);
    $goods_production = floor($production * $goods_prate * $row['prod_goods'] / 100.0);
    $energy_production = floor($production * $energy_prate * $row['prod_energy'] / 100.0);
    $reproduction = floor(($row['colonists'] - $starvation) * $colonist_reproduction_rate);

    if (($row['colonists'] + $reproduction - $starvation) > $colonist_limit)
    {
        $reproduction = $colonist_limit - $row['colonists'];
    }

    $total_percent = $row['prod_organics'] + $row['prod_ore'] + $row['prod_goods'] + $row['prod_energy'];

    if ($row['owner'])
    {
        $fighter_production = floor($production * $fighter_prate * $row['prod_fighters'] / 100.0);
        $torp_production = floor($production * $torpedo_prate * $row['prod_torp'] / 100.0);
        $total_percent += $row['prod_fighters'] + $row['prod_torp'];
    }
    else
    {
        $fighter_production = 0;
        $torp_production = 0;
    }

    $credits_production = floor($production * $credits_prate * (100.0 - $total_percent) / 100.0);
    $SQL = "UPDATE $dbtables[planets] SET organics = organics + $organics_production, ore = ore + $ore_production, goods = goods + $goods_production, energy = energy + $energy_production, colonists = colonists + $reproduction-$starvation, torps = torps + $torp_production, fighters = fighters + $fighter_production, credits = credits * $interest_rate + $credits_production WHERE planet_id=$row[planet_id] LIMIT 1; ";
    $ret = $db->Execute($SQL);
    $res->MoveNext();
}

$ret = $db->Execute("COMMIT;");
global $sched_planet_valid_credits;
if ($sched_planet_valid_credits == true)
{
    $ret = $db->Execute("UPDATE $dbtables[planets] SET credits = $max_credits_without_base WHERE credits > $max_credits_without_base AND base = 'N'");
}

echo "Planets updated.<br><br>";
echo "<br>";

?>
