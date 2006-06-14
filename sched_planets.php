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
// File: sched_planets.php
dynamic_loader ($db, "playerlog.php");

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_planets.php"));
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

// Dynamic functions
dynamic_loader ($db, "col_count_news.php");

global $db;
echo "colonist repro rate" . $colonist_reproduction_rate;
echo "multiplier" . $multiplier;

$expoprod = pow($colonist_reproduction_rate + 1, $multiplier);
echo "<br><br>expoprod is " . $expoprod;
$expoprod *= $multiplier;

$expocreds = pow($interest_rate, $multiplier);
$expostarvation_death_rate = 1 - pow((1 - $starvation_death_rate ), $multiplier);  

$line = "-1";

if ($spy_success_factor)
{
    $debug_query = $db->Execute("SELECT DISTINCT planet_id from {$db->prefix}spies WHERE spy_percent > '0.0' AND job_id > '0' ");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    while (!$debug_query->EOF)
    {
        $line .= ", " . $debug_query->fields['planet_id'];
        $debug_query->MoveNext();
    }

    $line = str_replace("-1, ", "", $line);
}
echo "<strong>PLANETS</strong><br>\n";

// Planets without 'working' spies

// If organics plus org production minus org consumption is less then zero then there is starvation
// So set organics to zero and kill off some colonists

$debug_query = sql_log_starvation(); // See includes/dbtype-common.php 
while (!$debug_query->EOF)
{
    $info = $debug_query->fields;
    if ($info['st_value']>0)
    {
        playerlog($db,$info['owner'], "LOG_STARVATION", "$info[sector_id]|" . number_format($info['st_value'], 0, $local_number_dec_point, $local_number_thousands_sep));
    }
    $debug_query->MoveNext();
}

echo "got to update starv";
sql_update_starvation(); // See includes/dbtype-common.php

sql_production_update(); // See includes/dbtype-common.php
echo "got to prod update";

sql_defense_update(); // See includes/dbtype-common.php
echo "got past defense";
// Planets with 'working' spies
// We have to update them one by one, because production etc is different on different planets, depending on the spies activity

$debug_query1 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id IN ($line)");
db_op_result($db,$debug_query1,__LINE__,__FILE__);

// echo "Other planets: ".$debug_query->RecordCount(). "<br>";

while (!$debug_query1->EOF)
{
    $row = $debug_query1->fields;
   
    if ($spy_success_factor)
    {
        $result_s = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=$row[planet_id] AND job_id='1' AND active='Y' "); // Saboteurs
        $sum = 0.0;
        while (!$result_s->EOF)
        {
            $spy = $result_s->fields;
            $sum += $spy['spy_percent'];
            $result_s->MoveNext();
        }
    }
    else
    {
        $sum = 0.0;
    }

    $production = min($row['colonists'], $colonist_limit) * (pow(($colonist_production_rate - $sum + 1), $multiplier) - 1);
    $org_cons = min($row['colonists'], $colonist_limit) * (pow(($colonist_production_rate + 1), $multiplier) - 1) * $organics_consumption;
    $organics_production = ($production * $organics_prate * $row['prod_organics'] / 100.0) - $org_cons;

    if ($row['organics'] + $organics_production < 0)
    {
        $organics_production = -$row['organics'];
        $starvation = round($row['colonists'] * $expostarvation_death_rate);
        if ($row['owner'] && $starvation >= 1)
        {
            playerlog($db,$row['owner'], "LOG_STARVATION", "$row[sector_id]|" . number_format($starvation, 0, $local_number_dec_point, $local_number_thousands_sep) );
        }
    }
    else
    {
        $starvation = 0;
    }

    $ore_production = $production * $ore_prate * $row['prod_ore'] / 100.0;
    $goods_production = $production * $goods_prate * $row['prod_goods'] / 100.0;
    $energy_production = $production * $energy_prate * $row['prod_energy'] / 100.0;

    if ($spy_success_factor)
    {
        $result_b = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE job_id='3' AND planet_id=$row[planet_id] AND active='Y' "); // Birth decreasers  (or however we call them)
        $sum2 = 0.0;
        while (!$result_b->EOF)
        {
            $spy = $result_b->fields;
            $sum2 += $spy['spy_percent'];
            $result_b->MoveNext();
        }  
    }
    else
    {
        $sum2 = 0.0;
    }

    $reproduction = round(($row['colonists'] - $starvation) * (pow(($colonist_reproduction_rate - $sum2 + 1),$multiplier) - 1));

    if (($row['colonists'] + $reproduction - $starvation) > $colonist_limit)
    {
        $reproduction = $colonist_limit - $row['colonists'] ;
    }

    $total_percent = $row['prod_organics'] + $row['prod_ore'] + $row['prod_goods'] + $row['prod_energy'];
    if ($row['owner'])
    {
        $fighter_production = $production * $fighter_prate * $row['prod_fighters'] / 100.0;
        $torp_production = $production * $torpedo_prate * $row['prod_torp'] / 100.0;
        $total_percent += $row['prod_fighters'] + $row['prod_torp'];
    }
    else
    {
        $fighter_production = 0;
        $torp_production = 0;
    }

    $credits_production = $production * $credits_prate * (100.0 - $total_percent) / 100.0;

    if ($spy_success_factor)
    {
        $result_i = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE job_id='2' AND planet_id=$row[planet_id] AND active='Y' "); // Interest Stealers
        $intr = 0.0;
        while (!$result_i->EOF)
        {
            $spy = $result_i->fields;
            $intr += $spy['spy_percent'];
            if ($allow_ibank)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=balance+$row[credits]*" .(pow(($spy['spy_percent'] + 1),$multiplier) - 1). " WHERE player_id=$spy[owner_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            else
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits+$row[credits]*" .(pow(($spy['spy_percent'] + 1),$multiplier) - 1). " WHERE player_id=$spy[owner_id]");
            }

            $result_i->MoveNext();
        }
    }
    else
    {
        $intr = 0.0;
    }

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET organics=organics+$organics_production, " .
                                "ore=ore+$ore_production, goods=goods+$goods_production, energy=energy+$energy_production, " .
                                "colonists=colonists+$reproduction-$starvation, torps=torps+$torp_production, " .
                                "fighters=fighters+$fighter_production, credits=credits*" . 
                                pow(($interest_rate - $intr), $multiplier) .
                                "+$credits_production WHERE planet_id=$row[planet_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $debug_query1->MoveNext();

    col_count_news($db, $row['owner']);
}

sql_planet_zero();

echo "Planets updated ($multiplier times).<br><br>\n";
$multiplier = 0;

?>
