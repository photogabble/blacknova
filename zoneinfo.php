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
// File: zoneinfo.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'zoneinfo');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'modify_defenses');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'ship');
load_languages($db, $raw_prefix, 'attack');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_zi_title;
updatecookie($db);
include_once './header.php';

if ($zoneinfo['zone_id'] < 5)
{
    $zonevar = "l_zname_" . $zoneinfo['zone_id'];
    $zoneinfo['zone_name'] = $$zonevar;
}

if ($zoneinfo['zone_id'] == '1')
{
    $ownername = $l_zi_nobody;
}
elseif ($zoneinfo['zone_id'] == '2')
{
    $ownername = $l_zi_feds;
}
elseif ($zoneinfo['zone_id'] == '3')
{
    $ownername = $l_zi_traders;
}
elseif ($zoneinfo['zone_id'] == '4')
{
    $ownername = $l_zi_war;
}
else
{
    if ($zoneinfo['team_zone'] == 'N')
    {
        $result = $db->Execute("SELECT player_id, character_name FROM {$db->prefix}players WHERE player_id=?", array($zoneinfo['owner']));
        $ownerinfo = $result->fields;
        $ownername = $ownerinfo['character_name'];
    }
    else
    {
        $result = $db->Execute("SELECT team_name, creator, team_id FROM {$db->prefix}teams WHERE team_id=?", array($zoneinfo['owner']));
        $ownerinfo = $result->fields;
        $ownername = $ownerinfo['team_name'];
    }
}

if ($zoneinfo['allow_attack'] == 'Y')
{
    $attack = $l_zi_allow;
}
else
{
    $attack = $l_zi_notallow;
}

if ($zoneinfo['allow_defenses'] == 'Y')
{
    $defense = $l_zi_allow;
}
elseif ($zoneinfo['allow_defenses'] == 'N')
{
    $defense = $l_zi_notallow;
}
else
{
    $defense = $l_zi_limit;
}

if ($zoneinfo['allow_warpedit'] == 'Y')
{
    $warpedit = $l_zi_allow;
}
elseif ($zoneinfo['allow_warpedit'] == 'N')
{
    $warpedit = $l_zi_notallow;
}
else
{
    $warpedit = $l_zi_limit;
}

if ($zoneinfo['allow_planet'] == 'Y')
{
    $planet = $l_zi_allow;
}
elseif ($zoneinfo['allow_planet'] == 'N')
{
    $planet = $l_zi_notallow;
}
else
{
    $planet = $l_zi_limit;
}

if ($zoneinfo['allow_trade'] == 'Y')
{
    $trade = $l_zi_allow;
}
elseif ($zoneinfo['allow_trade'] == 'N')
{
    $trade = $l_zi_notallow;
}
else
{
    $trade = $l_zi_limit;
}

if ($zoneinfo['max_level'] == 0)
{
    $maxlevel = $l_zi_ul;
}
else
{
    $maxlevel = $zoneinfo['max_level'];
}

if (($zoneinfo['team_zone'] == 'N' && $zoneinfo['owner'] == $playerinfo['player_id']) || ($zoneinfo['team_zone'] == 'Y' && $zoneinfo['owner'] == $playerinfo['team'] && $playerinfo['player_id'] == $ownerinfo['creator']))
{
    $editable = TRUE;
}
else
{
    $editable = FALSE;
}

$template->assign("attack", $attack);
$template->assign("title", $title);
$template->assign("l_zi_control", $l_zi_control);
$template->assign("l_clickme", $l_clickme);
$template->assign("l_zi_tochange", $l_zi_tochange);
$template->assign("editable", $editable);
$template->assign("zoneinfo_zonename", $zoneinfo['zone_name']);
$template->assign("l_zi_owner", $l_zi_owner);
$template->assign("ownername", $ownername);
$template->assign("color_line1", $color_line1);
$template->assign("color_line2", $color_line2);
$template->assign("l_att_att", $l_att_att);
$template->assign("l_md_title", $l_md_title);
$template->assign("defense", $defense);
$template->assign("l_warpedit", $l_warpedit);
$template->assign("warpedit", $warpedit);
$template->assign("l_planet", $l_planet);
$template->assign("l_planets", $l_planets);
$template->assign("planet", $planet);
$template->assign("l_title_port", $l_title_port);
$template->assign("trade", $trade);
$template->assign("l_zi_maxhull", $l_zi_maxhull);
$template->assign("maxlevel", $maxlevel);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/zoneinfo.tpl");

include_once './footer.php';
?>
