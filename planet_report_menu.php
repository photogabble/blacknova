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
// File: planet_report_menu.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'planet_report');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_pr_title;
updatecookie($db);
include_once ("./header.php");

global $playerinfo, $ship_based_combat;

$template->assign("title", $title);
$template->assign("ship_based_combat", $ship_based_combat);
$template->assign("playerinfo_team", $playerinfo['team']);
$template->assign("l_pr_planetstatus", $l_pr_planetstatus);
$template->assign("l_pr_comm_disp", $l_pr_comm_disp);
$template->assign("l_pr_changeprods", $l_pr_changeprods);
$template->assign("l_pr_baserequired", $l_pr_baserequired);
$template->assign("l_pr_prod_disp1", $l_pr_prod_disp1);
$template->assign("l_pr_prod_disp2", $l_pr_prod_disp2);
$template->assign("l_pr_team_disp", $l_pr_team_disp);
$template->assign("l_pr_teamlink", $l_pr_teamlink);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/planet_report_menu.tpl");
include_once ("./footer.php");
?>
