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
// File: genesis.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "planetcount_news.php");
dynamic_loader ($db, "planet_log.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'genesis');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_gns_title;
updatecookie($db);
include_once ("./header.php");

$result3 = $db->Execute("SELECT planet_id FROM {$db->prefix}planets WHERE sector_id='$shipinfo[sector_id]'");
$num_planets = $result3->RecordCount();

$res = $db->Execute("SELECT {$db->prefix}universe.zone_id, {$db->prefix}zones.allow_planet, {$db->prefix}zones.team_zone, " .
                    "{$db->prefix}zones.owner FROM {$db->prefix}zones, {$db->prefix}universe WHERE " .
                    "{$db->prefix}zones.zone_id=$sectorinfo[zone_id] AND {$db->prefix}universe.sector_id = $shipinfo[sector_id]");
$query97 = $res->fields;

$res = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=$query97[owner]");
$ownerinfo = $res->fields;

$gen_failed = TRUE;

if ($playerinfo['turns'] < 1)
{
    $gen_reason = $l_gns_turn;
}
elseif ($shipinfo['on_planet'] == 'Y')
{
    $gen_reason = $l_gns_onplanet;
}
elseif ($num_planets >= $sectorinfo['star_size'])
{
    $gen_reason = $l_gns_full;
}
elseif ($shipinfo['dev_genesis'] < 1)
{
    $gen_reason = $l_gns_nogenesis;
}
elseif ($query97['allow_planet'] == 'N')
{
    $gen_reason = $l_gns_forbid;
}
elseif (($query97['allow_planet'] == 'L') && ($ownerinfo['team'] != $playerinfo['team']))
{
    $gen_reason = $l_gns_forbid;
}
else
{
    $gen_failed = FALSE;
    seed_mt_rand();
    $planetname = chr(mt_rand(65,90)) . chr(mt_rand(65,90)) . "-" . $shipinfo['sector_id'] . "-" . ($num_planets + 1);
    // Rednova version:
    // $planetname = substr($playerinfo['character_name'],0,1) . substr($shipinfo['name'],0,1) . "-" . $shipinfo['sector_id'] . "-" . ($num_planets + 1);

    $query1 = "INSERT INTO {$db->prefix}planets (planet_id, sector_id, name, organics, ore, goods, energy, colonists, " .
              "credits, computer, sensors, beams, torp_launchers, torps, shields, armor, armor_pts, cloak, fighters, owner, ".
              "team, base, sells, defeated, prod_organics, prod_ore, prod_goods, prod_energy, prod_fighters, prod_torp) ".
              " VALUES('', $shipinfo[sector_id], '" . $planetname . "', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, " .
              "0, 0, 0, 0, 0, $playerinfo[player_id], 0, 'N', 'N', 'N', $default_prod_organics, $default_prod_ore, " .
              "$default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp)";
    $debug_query = $db->Execute($query1);
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $query2 = "UPDATE {$db->prefix}players SET turns_used=turns_used+1, turns=turns-1 " .
              "WHERE player_id=$playerinfo[player_id]";
    $debug_query = $db->Execute($query2);
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $query3 = "UPDATE {$db->prefix}ships SET dev_genesis=dev_genesis-1 WHERE ship_id=$shipinfo[ship_id]";
    $debug_query = $db->Execute($query3);
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $logres = $db->Execute("SELECT MAX(planet_id) AS planet_id FROM {$db->prefix}planets WHERE " .
                           "owner = $playerinfo[player_id]");

    // Planet log constants
    planet_log($db, $logres->fields['planet_id'],$playerinfo['player_id'],$playerinfo['player_id'],1);

    planetcount_news($db, $playerinfo['player_id']);
}

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
$smarty->assign("gen_failed", $gen_failed);
$smarty->assign("gen_reason", $gen_reason);
$smarty->assign("l_gns_pcreate", $l_gns_pcreate);
$smarty->assign("l_global_mmenu", $l_global_mmenu);
$smarty->display("$templateset/genesis.tpl");

include_once ("./footer.php");
?>
