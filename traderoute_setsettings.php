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
// File: traderoute_setsettings.php

include_once './global_includes.php';
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

global $playerinfo, $colonists;
global $fighters;
global $torps;
global $energy;
global $l_tdr_returnmenu, $l_tdr_globalsetsaved;
global $db;

empty($_POST['colonists']) ? $colonists = 'N' : $colonists = 'Y';
empty($_POST['fighters']) ? $fighters = 'N' : $fighters = 'Y';
empty($_POST['torps']) ? $torps = 'N' : $torps = 'Y';

$debug_query = $db->Execute("UPDATE {$db->prefix}players SET trade_colonists=?, trade_fighters=?, trade_torps=?, trade_energy=? WHERE player_id=?", array($colonists, $fighters, $torps, $_POST['energy'], $playerinfo['player_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);

$template->assign("l_tdr_returnmenu", $l_tdr_returnmenu);
$template->assign("l_tdr_globalsetsaved", $l_tdr_globalsetsaved);
$template->display("$templateset/traderoute_setsettings.tpl");
?>
