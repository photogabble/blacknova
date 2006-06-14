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
// File: team_report.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'teams');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'team_report');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_team_report_title;
updatecookie($db);
include_once ("./header.php");

$main_table_heading = "#ffffff"; // Used for table headings for tables on main

if (!isset($orderby))
{
    $orderby = 'p.character_name';
}

if (!isset($direction))
{
    $direction = '';
}

if (!isset($whichteam))
{
    $whichteam = '';
}

// Get user info
$result = $db->Execute("SELECT {$db->prefix}players.*, {$db->prefix}teams.team_name, {$db->prefix}teams.description, " .
                       "{$db->prefix}teams.creator, {$db->prefix}teams.team_id FROM {$db->prefix}players LEFT JOIN {$db->prefix}teams " .
                       "ON {$db->prefix}players.team = {$db->prefix}teams.team_id LEFT JOIN {$raw_prefix}users ON {$raw_prefix}users.account_id = {$db->prefix}players.account_id " .
                       "WHERE {$raw_prefix}users.email='$_SESSION[email]'");
$playerinfo = $result->fields;

// Get Team Info

$whichteam = preg_replace('/[^0-9]/','',$whichteam);
if ($whichteam)
{
    $debug_query   = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=$whichteam");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $team          = $debug_query->fields;
}
else
{
    $result_team   = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=$playerinfo[team]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $team          = $debug_query->fields;
}

// Dynamic functions
dynamic_loader ($db, "showinfo.php");

if ($playerinfo['team'] != 0)
{
    $result = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=$playerinfo[team]");
//    $whichteam = $result->fields;
    $team = $result->fields;
    $isowner = ($playerinfo['player_id'] == $team['creator']);
    showinfo($db,$playerinfo['team'],$isowner);
}

global $l_global_mmenu;

$template->assign("title", $title);
$template->assign("playerinfo_team", $playerinfo['team']);
$template->assign("l_team_notmember", $l_team_notmember);
$template->assign("l_clickme", $l_clickme);
$template->assign("l_team_menu", $l_team_menu);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/team_report.tpl");

include_once ("./footer.php");
?>
