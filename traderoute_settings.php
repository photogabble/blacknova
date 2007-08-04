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
// File: traderoute_settings.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Dynamic functions
dynamic_loader ($db, "traderoute_die.php");

global $playerinfo;
global $l_tdr_globalset, $l_tdr_tdrsportsrc, $l_tdr_colonists, $l_tdr_fighters, $l_tdr_torps, $l_tdr_trade;
global $l_tdr_tdrescooped, $l_tdr_keep, $l_tdr_save, $l_tdr_returnmenu;
global $db;

$smarty->assign("l_tdr_globalset", $l_tdr_globalset);
$smarty->assign("l_tdr_tdrsportsrc", $l_tdr_tdrsportsrc);
$smarty->assign("l_tdr_colonists", $l_tdr_colonists);
$smarty->assign("playerinfo_trade_colonists", $playerinfo['trade_colonists']);
$smarty->assign("l_tdr_fighters", $l_tdr_fighters);
$smarty->assign("playerinfo_trade_fighters", $playerinfo['trade_fighters']);
$smarty->assign("l_tdr_torps", $l_tdr_torps);
$smarty->assign("playerinfo_trade_torps", $playerinfo['trade_torps']);
$smarty->assign("l_tdr_tdrescooped", $l_tdr_tdrescooped);
$smarty->assign("l_tdr_trade", $l_tdr_trade);
$smarty->assign("l_tdr_keep", $l_tdr_keep);
$smarty->assign("playerinfo_trade_energy", $playerinfo['trade_energy']);
$smarty->assign("l_tdr_save", $l_tdr_save);
$smarty->assign("l_tdr_returnmenu", $l_tdr_returnmenu);
$smarty->display("$templateset/traderoute_settings.tpl");
traderoute_die("");
?>
