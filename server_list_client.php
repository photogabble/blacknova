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
// File: server_list_client.php

/*
$pos = (strpos($_SERVER['PHP_SELF'], "/server_list_client.php"));
if ($pos !== false)
{
    include_once './config/config.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo "You can not access this file directly!";
    include_once './footer.php';
    die();
}
*/

$_POST['gamenum'] = '1';
include_once './global_includes.php';
$server_list_url = "http://www.kabal-invasion.com/msl/";

echo "sched ports " . $sched_ports;
// PUBLIC SERVER LIST UPDATE
echo "\n<BR><STRONG>SERVER LIST UPDATE</STRONG>\n<BR>";

$gm_url = $_SERVER['HTTP_HOST'];
$gm_admin_mail = $admin_mail;
$gm_speed = $sched_ports + $sched_planets + $sched_igb;
$gm_speed_turns = $sched_turns;
$gm_size_sc = $sector_max;
$gm_size_un = $galaxy_size;

$gm_size_pl = 5; // hardcoded for now
//$gm_size_pl = $max_planets_sector;

$gm_money_igb = $ibank_interest;
$gm_money_pl = round($interest_rate - 1,4);
$gm_port_limit = $ore_limit + $organics_limit + $goods_limit + $energy_limit;
$gm_port_rate = $ore_rate + $organics_rate + $goods_rate + $energy_rate;
$gm_port_delta = $ore_delta + $organics_delta + $goods_delta + $energy_delta;
$gm_sofa_on = $sofa_on;
if ($sofa_on != true)
{
    $gm_sofa_on = 0;
}

$gm_all = "gm_speed=" . $gm_speed .
          "&gm_speed_turns=" . $gm_speed_turns .
          "&gm_size_sc=" . $gm_size_sc .
          "&gm_size_un=" . $gm_size_un .
          "&gm_size_pl=" . $gm_size_pl .
          "&gm_money_igb=" . $gm_money_igb .
          "&gm_money_pl=" . $gm_money_pl .
          "&gm_port_limit=" . $gm_port_limit .
          "&gm_port_rate=" . $gm_port_rate .
          "&gm_port_delta=" . $gm_port_delta .
          "&gm_sofa_on=" . $gm_sofa_on .
          "&gm_url=" . rawurlencode($gm_url) .
          "&gm_admin_mail=" . rawurlencode($gm_admin_mail) .
          "&gm_name=" . rawurlencode($game_name);

$res = $db->Execute("SELECT COUNT({$db->prefix}ships.ship_id) AS x FROM {$db->prefix}ships, {$db->prefix}players LEFT JOIN {$raw_prefix}users " .
                    "ON {$raw_prefix}users.account_id = {$db->prefix}players.account_id ".
                    "WHERE {$db->prefix}ships.player_id = {$db->prefix}players.player_id AND {$db->prefix}ships.destroyed='N' " .
                    "AND {$raw_prefix}users.email NOT LIKE '%@aiplayer' AND {$db->prefix}players.turns_used > 0");
$row = $res->fields;
$dyn_players = $row['x'];

$res = $db->Execute("SELECT score, character_name FROM {$db->prefix}ships, {$db->prefix}players LEFT JOIN {$raw_prefix}users " .
                    "ON {$raw_prefix}users.account_id = {$db->prefix}players.account_id ".
                    "WHERE {$db->prefix}ships.player_id = {$db->prefix}players.player_id AND {$db->prefix}ships.destroyed='N' AND " .
                    "{$raw_prefix}users.email NOT LIKE '%@aiplayer' ORDER BY score DESC");
$row = $res->fields;
$dyn_top_score = $row['score'];
$dyn_top_player = $row['character_name'];

$res = $db->Execute("SELECT COUNT({$db->prefix}ships.ship_id) AS x FROM {$db->prefix}ships,{$db->prefix}players LEFT JOIN {$raw_prefix}users " .
                    "ON {$raw_prefix}users.account_id = {$db->prefix}players.account_id ".
                    "WHERE {$db->prefix}ships.player_id = {$db->prefix}players.player_id AND {$db->prefix}ships.destroyed='N' AND " .
                    "{$raw_prefix}users.email LIKE '%@aiplayer'");
$row = $res->fields;
$dyn_aiplayer = $row['x'];

$res = $db->Execute("SELECT AVG(hull) AS a1 , AVG(engines) AS a2 , AVG(power) AS a3 , AVG(computer) AS a4 , " .
                    "AVG(sensors) AS a5 , AVG(beams) AS a6 , AVG(torp_launchers) AS a7 , AVG(shields) AS a8 , " .
                    "AVG(armour) AS a9 , AVG(cloak) AS a10 FROM {$db->prefix}ships,{$db->prefix}players LEFT JOIN {$raw_prefix}users " .
                    "ON {$raw_prefix}users.account_id = {$db->prefix}players.account_id ".
                    "WHERE {$db->prefix}ships.player_id = {$db->prefix}players.player_id AND destroyed='N' AND email LIKE '%@aiplayer'");
$row = $res->fields;
$dyn_aiplayer_lvl = $row['a1'] + $row['a2'] + $row['a3'] + $row['a4'] + $row['a5'] + $row['a6'] + $row['a7'] + $row['a8'] + $row['a9'] + $row['a10'];
$dyn_aiplayer_lvl = $dyn_aiplayer_lvl / 10;

$dyn_all = "&dyn_players=" . $dyn_players .
           "&dyn_aiplayer=" . $dyn_aiplayer .
           "&dyn_aiplayer_lvl=" . $dyn_aiplayer_lvl .
           "&dyn_top_score=" . $dyn_top_score .
           "&dyn_top_player=" . rawurlencode($dyn_top_player) .
           "&dyn_key=" . rawurlencode($server_list_key);

echo "\n\n<!-- Debug of values sent\n";
echo str_replace("&", "\n", $gm_all);
echo "\n";
echo str_replace("&", "\n", $dyn_all);
echo "\n\n-->\n\n";

$url = $server_list_url . "bnt_ls_server.php?" . $gm_all . $dyn_all;
if (isset($creating))
{
    $url = $url . "&gm_reset=1";
}

//echo "\n\n<!--" . $url . "-->\n\n";
echo "\n\n<br>" . $url . "<br>\n\n";

$i = @file($url);
$result = @fopen($url,'r');

if ($result)
{
    echo "<font color=green>Updated public server list successfully.</font><br>\n";
}
else
{
    echo "<font color=red>Failed to update public server list!</font><br>\n";
}

?>
