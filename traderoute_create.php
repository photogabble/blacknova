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
// File: traderoute_create.php

include_once './global_includes.php';
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Dynamic functions
dynamic_loader ($db, "traderoute_die.php");
dynamic_loader ($db, "traderoute_check_compatible.php");

global $num_traderoutes, $max_traderoutes_player;
//global $port_id1, $port_id2;
global $planet_id1, $planet_id2;
global $team_planet_id1, $team_planet_id2;
global $move_type, $circuit_type, $editing;
global $l_tdr_maxtdr, $l_tdr_errnotvalidport, $l_tdr_errnoport, $l_tdr_errnosrc, $l_tdr_errnotownnotsell;
global $l_tdr_errnotvaliddestport, $l_tdr_errnoport2, $l_tdr_errnodestplanet, $l_tdr_errnotownnotsell2;
global $l_tdr_newtdrcreated, $l_tdr_tdrmodified, $l_tdr_returnmenu;
global $l_tdr_explorefirst;
global $db;

if ($num_traderoutes >= $max_traderoutes_player && empty($_POST['editing']))
{
    traderoute_die($l_tdr_maxtdr);
}

if (isset($_GET['ptype1']))
{
    $ptype1 = $_GET['ptype1'];
}
elseif (isset($_POST['ptype1']))
{
    $ptype1 = $_POST['ptype1'];
}
else
{
    $ptype1 = '';
}

if (isset($_GET['ptype2']))
{
    $ptype2 = $_GET['ptype2'];
}
elseif (isset($_POST['ptype2']))
{
    $ptype2 = $_POST['ptype2'];
}
else
{
    $ptype2 = '';
}

// DB sanity check for source
if ($ptype1 == 'port')
{
    $query = $db->Execute("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($_POST['port_id1']));
    if (!$query || $query->EOF)
    {
        $l_tdr_errnotvalidport = str_replace("[tdr_port_id]", $_POST['port_id1'], $l_tdr_errnotvalidport);
        traderoute_die($l_tdr_errnotvalidport);
    }

    $source = $query->fields;
    if ($source['port_type'] == 'none')
    {
        $l_tdr_errnoport = str_replace("[tdr_port_id]", $_POST['port_id1'], $l_tdr_errnoport);
        traderoute_die($l_tdr_errnoport);
    }

    // ensure that they have been in the sector for the first port, but only if its a valid port type.
    $res1 = $db->Execute("SELECT * from {$db->prefix}movement_log WHERE player_id=? AND " .
                         "source=?", array($playerinfo['player_id'], $_POST['port_id1']));
    if (!$res1 || $res1->EOF)
    {
        $res11 = $db->Execute("SELECT * from {$db->prefix}scan_log WHERE player_id=? AND " .
                              "sector_id=?", array($playerinfo['player_id'], $_POST['port_id1']));
        if (!$res11 || $res11->EOF)
        {
            traderoute_die($l_tdr_explorefirst);
        }
    }
}
else
{
    $query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id1));
    if (!$query || $query->EOF)
    {
        traderoute_die($l_tdr_errnosrc);
    }

    $source = $query->fields;

    // Hum, that thing was tricky
    if ($source['owner'] != $playerinfo['player_id'])
    {
        if (($playerinfo['team'] == 0 || $playerinfo['team'] != $source['team']) && $source['sells'] == 'N')
        {
            $l_tdr_errnotownnotsell = str_replace("[tdr_source_name]", $source[name], $l_tdr_errnotownnotsell);
            $l_tdr_errnotownnotsell = str_replace("[tdr_source_sector_id]", $source[sector_id], $l_tdr_errnotownnotsell);
            traderoute_die($l_tdr_errnotownnotsell);
        }
    }
}

if ($ptype2 == 'port')
{
    $query = $db->Execute("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($_POST['port_id2']));
    $destination = $query->fields;
}
else
{
    $query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id2));
    $destination = $query->fields;
}

// Destination Check for combat levels
$debug_query = $db->Execute("SELECT zone_id FROM {$db->prefix}universe WHERE sector_id=?", array($destination['sector_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$dest_zone = $debug_query->fields['zone_id'];

$debug_query = $db->Execute("SELECT max_level FROM {$db->prefix}zones WHERE zone_id=?", array($dest_zone));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$dest_max_level = $debug_query->fields['max_level'];

$combat_levels = round(($shipinfo['computer'] + $shipinfo['torp_launchers'] + $shipinfo['beams']) / 3);
if ($combat_levels > $dest_max_level && $dest_max_level > 0)
{
    traderoute_die($l_tdr_max_level);
}

// DB sanity check for dest
if ($ptype2 == 'port')
{
    $query = $db->Execute("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($_POST['port_id2']));
    if (!$query || $query->EOF)
    {
        $l_tdr_errnotvaliddestport = str_replace("[tdr_port_id]", $_POST['port_id2'], $l_tdr_errnotvaliddestport);
        traderoute_die($l_tdr_errnotvaliddestport);
    }

    $destination = $query->fields;
    if ($destination['port_type'] == 'none')
    {
        $l_tdr_errnoport2 = str_replace("[tdr_port_id]", $_POST['port_id2'], $l_tdr_errnoport2);
        traderoute_die($l_tdr_errnoport2);
    }

    if ($destination['port_type'] == 'devices' || $destination['port_type'] == 'upgrades')
    {
        $l_tdr_errnoport2 = str_replace("[tdr_port_id]", $_POST['port_id2'], $l_tdr_errnoport2);
        traderoute_die($l_tdr_errnoport2);
    }

    // ensure that they have been in the sector for the second port, but only if its a valid port type.
    $res1 = $db->Execute("SELECT * from {$db->prefix}movement_log WHERE player_id=? AND source=?", array($playerinfo['player_id'], $_POST['port_id2']));
    if (!$res1 || $res1->EOF)
    {
        $res11 = $db->Execute("SELECT * from {$db->prefix}scan_log WHERE player_id=? AND sector_id=?", array($playerinfo['player_id'], $_POST['port_id2']));
        if (!$res11 || $res11->EOF)
        {
            traderoute_die($l_tdr_explorefirst);
        }
    }
}
else
{
    $query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id2));
    if (!$query || $query->EOF)
    {
        traderoute_die($l_tdr_errnodestplanet);
    }

    $destination = $query->fields;

    if ($destination['owner'] != $playerinfo['player_id'] && $destination['sells'] == 'N')
    {
        $l_tdr_errnotownnotsell2 = str_replace("[tdr_dest_name]", $destination['name'], $l_tdr_errnotownnotsell2);
        $l_tdr_errnotownnotsell2 = str_replace("[tdr_dest_sector_id]", $destination['sector_id'], $l_tdr_errnotownnotsell2);
        traderoute_die($l_tdr_errnotownnotsell2);
    }
}

// Check traderoute for src => dest
traderoute_check_compatible($ptype1, $ptype2, $move_type, $_POST['circuit_type'], $source , $destination);

if ($ptype1 == 'port')
{
    $src_id = $_POST['port_id1'];
}
elseif ($ptype1 == 'planet')
{
    $src_id = $planet_id1;
}
elseif ($ptype1 == 'team_planet')
{
    $src_id = $team_planet_id1;
}

if ($ptype2 == 'port')
{
    $dest_id = $_POST['port_id2'];
}
elseif ($ptype2 == 'planet')
{
    $dest_id = $planet_id2;
}
elseif ($ptype2 == 'team_planet')
{
    $dest_id = $team_planet_id2;
}

if ($ptype1 == 'port')
{
    $src_type = 'P';
}
elseif ($ptype1 == 'planet')
{
    $src_type = 'L';
}
elseif ($ptype1 == 'team_planet')
{
    $src_type = 'C';
}

if ($ptype2 == 'port')
{
    $dest_type = 'P';
}
elseif ($ptype2 == 'planet')
{
    $dest_type = 'L';
}
elseif ($ptype2 == 'team_planet')
{
    $dest_type = 'C';
}

if ($_POST['move_type'] == 'realspace')
{
    $mtype = 'R';
}
else
{
    $mtype = 'W';
}

if (empty($_POST['editing']))
{
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}traderoutes (source_id, dest_id, source_type, dest_type, move_type, owner, circuit) " .
                                "VALUES (?,?,?,?,?,?,?)", array($src_id, $dest_id, $src_type, $dest_type, $mtype, $playerinfo['player_id'], $_POST['circuit_type']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}
else
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}traderoutes SET source_id=?, dest_id=?, source_type=?, dest_type=?, move_type=?, owner=?, circuit=? WHERE traderoute_id=?", array($src_id, $dest_id, $src_type, $dest_type, $mtype, $playerinfo['player_id'], $_POST['circuit_type'], $_POST['editing']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}

$template->assign("empty_editing", empty($_POST['editing']));
$template->assign("l_tdr_newtdrcreated", $l_tdr_newtdrcreated);
$template->assign("l_tdr_tdrmodified", $l_tdr_tdrmodified);
$template->assign("l_tdr_returnmenu", $l_tdr_returnmenu);
$template->display("$templateset/traderoute_create.tpl");

traderoute_die("");
?>
