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
// File: planet3.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "spy_detect_planet.php"); 
dynamic_loader ($db, "num_level.php"); 
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'main');

$planet_id = '';
if (isset($_GET['planet_id']))
{
    $planet_id = $_GET['planet_id'];
}
elseif (isset($_POST['planet_id']))
{
    $planet_id = $_POST['planet_id'];
}

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_planet3_title;
updatecookie($db);

$result2 = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE planet_id=$planet_id");
if ($result2)
{
    $planetinfo = $result2->fields;
}

echo "<h1>" . $title. "</h1>\n";

if ($playerinfo['turns']<1)
{
    echo $l_trade_turnneed. "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($planetinfo['sector_id'] != $shipinfo['sector_id'])
{
    echo $l_planet2_sector. "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if (empty($planetinfo))
{
    echo $l_planet_none. "<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($spy_success_factor)
{
    spy_detect_planet($db,$shipinfo['ship_id'], $planetinfo['planet_id'], $planet_detect_success1);
}

$trade_ore = preg_replace('/[^0-9]/','',$_POST['trade_ore']);
$trade_organics = preg_replace('/[^0-9]/','',$_POST['trade_organics']);
$trade_goods = preg_replace('/[^0-9]/','',$_POST['trade_goods']);
$trade_energy = preg_replace('/[^0-9]/','',$_POST['trade_energy']);
$ore_price = ($ore_price + $ore_delta/4);
$organics_price = ($organics_price + $organics_delta/4);
$goods_price = ($goods_price + $goods_delta/4);
$energy_price = ($energy_price + $energy_delta/4);

if ($planetinfo['sells']=='Y')
{
    $cargo_exchanged = $trade_ore + $trade_organics + $trade_goods;

    $free_holds = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
    $free_power = (5 * num_level($shipinfo['power'], $level_factor, $level_magnitude)) - $shipinfo['energy'];
    $total_cost = ($trade_ore*$ore_price) + ($trade_organics*$organics_price) + ($trade_goods*$goods_price) + ($trade_energy*$energy_price);

    if ($free_holds < $cargo_exchanged)
    {
        echo "$l_notenough_cargo  <a href=\"planet.php?planet_id=$planet_id\">$l_clickme</a> $l_toplanetmenu<br><br>";
    } 
    elseif ($trade_energy > $free_power) 
    {
        echo "$l_notenough_power <a href=\"planet.php?planet_id=$planet_id\">$l_clickme</a> $l_toplanetmenu<br><br>";
    } 
    elseif ($playerinfo['turns']<1) 
    {
        echo "$l_notenough_turns<br><br>";
    } 
    elseif ($playerinfo['credits']<$total_cost) 
    {
        echo "$l_notenough_credits<br><br>";
    } 
    elseif ($trade_organics > $planetinfo['organics'])
    {
        echo "$l_exceed_organics  ";
    } 
    elseif ($trade_ore > $planetinfo['ore'])
    {
        echo "$l_exceed_ore  ";
    } 
    elseif ($trade_goods > $planetinfo['goods'])
    {
        echo "$l_exceed_goods  ";
    } 
    elseif ($trade_energy > $planetinfo['energy'])
    {
        echo "$l_exceed_energy  ";
    } 
    else 
    {
        echo "$l_totalcost: $total_cost<br>$l_traded_ore: $trade_ore<br>$l_traded_organics: $trade_organics<br>$l_traded_goods: $trade_goods<br>$l_traded_energy: $trade_energy<br><br>";

        // Update ship cargo, credits and turns
        $debug_query = $db->Execute ("UPDATE {$db->prefix}players SET turns=turns-1, " .
                                     "turns_used=turns_used+1, credits=credits-$total_cost WHERE player_id=$playerinfo[player_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET ore=ore+$trade_ore, organics=organics+$trade_organics, " .
                                     "goods=goods+$trade_goods, energy=energy+$trade_energy WHERE ship_id=$shipinfo[ship_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute ("UPDATE {$db->prefix}planets SET ore=ore-$trade_ore, organics=organics-$trade_organics, " .
                                     "goods=goods-$trade_goods, energy=energy-$trade_energy, credits=credits+$total_cost " .
                                     "WHERE planet_id=$planet_id");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        echo "$l_trade_complete<br><br>";
    }
}

gen_score($db,$planetinfo['owner']);
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");

?>
