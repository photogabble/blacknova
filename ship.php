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
// File: ship.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'ship');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_ship_title;
updatecookie($db);

if (!isset($_GET['ship_id']))
{
    $_GET['ship_id'] = '';
}

$debug_query = $db->Execute("SELECT {$db->prefix}ships.player_id, name, character_name, sector_id FROM {$db->prefix}ships " .
                            "LEFT JOIN {$db->prefix}players ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id " .
                            "WHERE ship_id=?", array($_GET['ship_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);

$otherplayer = $debug_query->fields;

global $l_global_mmenu;
$template->assign("title", $title);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->assign("player_id", $otherplayer['player_id']);
$template->assign("ship_id", $_GET['ship_id']);
$template->assign("l_planet_att_link", $l_planet_att_link);
$template->assign("l_planet_scn_link", $l_planet_scn_link);
$template->assign("l_ship_perform", $l_ship_perform);
$template->assign("l_ship_owned", $l_ship_owned);
$template->assign("l_send_msg", $l_send_msg);
$template->assign("l_ship_youc", $l_ship_youc);
$template->assign("l_ship_the", $l_ship_the);
$template->assign("l_ship_nolonger", $l_ship_nolonger);
$template->assign("otherplayer_character_name", $otherplayer['character_name']);
$template->assign("otherplayer_name", $otherplayer['name']);
$template->assign("otherplayer_sector_id", $otherplayer['sector_id']);
$template->assign("shipinfo_sector_id", $shipinfo['sector_id']);
$template->display("$templateset/ship.tpl");
include_once './footer.php';
?>
