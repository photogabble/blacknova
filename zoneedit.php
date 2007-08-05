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
// File: zoneedit.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'zoneinfo');
load_languages($db, $raw_prefix, 'zoneedit');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'main');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_ze_title;
updatecookie($db);
include_once ("./header.php");

global $zoneinfo;
if (!isset($_GET['command']))
{
    $_GET['command'] = '';
}

if (!isset($yattack))
{
    $yattack = '';
}

if (!isset($nattack))
{
    $nattack = '';
}

if (!isset($ywarpedit))
{
    $ywarpedit = '';
}

if (!isset($nwarpedit))
{
    $nwarpedit = '';
}

if (!isset($lwarpedit))
{
    $lwarpedit = '';
}

if (!isset($ydefense))
{
    $ydefense = '';
}

if (!isset($ndefense))
{
    $ndefense = '';
}

if (!isset($ldefense))
{
    $ldefense = '';
}

if (!isset($nplanet))
{
    $nplanet = '';
}

if (!isset($lplanet))
{
    $lplanet = '';
}

if (!isset($ytrade))
{
    $ytrade = '';
}

if (!isset($ntrade))
{
    $ntrade = '';
}

if (!isset($ltrade))
{
    $ltrade = '';
}

if (!isset($yplanet))
{
    $yplanet = '';
}

if ($zoneinfo['team_zone'] == 'N')
{
    $result = $db->Execute("SELECT account_id FROM {$raw_prefix}users WHERE email=?", array($_SESSION['email'])); // Accounts are in the root tables.
    $account_id = $result->fields['account_id'];

    $result = $db->Execute("SELECT player_id FROM {$db->prefix}players WHERE account_id=?", array($account_id));
    $ownerinfo = $result->fields;
}
else
{
    $result = $db->Execute("SELECT creator, team_id FROM {$db->prefix}teams WHERE creator=?", array($zoneinfo['owner']));
    $ownerinfo = $result->fields;
}

if (($zoneinfo['team_zone'] == 'N' && $zoneinfo['owner'] != $ownerinfo['player_id']) || ($zoneinfo['team_zone'] == 'Y' && $zoneinfo['owner'] != $ownerinfo['id'] && $row[owner] == $ownerinfo['creator']))
{
    $template->assign("title", $title);
    $template->assign("l_ze_notowner", $l_ze_notowner);
    $template->assign("l_global_mmenu", $l_global_mmenu);
    $template->display("$templateset/zoneedit-no.tpl");

    include_once ("./footer.php");
    die();
}

if ($_GET['command'] == 'change')
{
    global $zoneinfo;
    global $l_clickme, $l_ze_saved, $l_ze_return;
    global $db;

    if (!get_magic_quotes_gpc())
    {
        $name = mysql_escape_string($name);
    }

    $debug_query = $db->Execute("UPDATE {$db->prefix}zones SET allow_attack=?, allow_warpedit=?, allow_planet=?, allow_trade=?, allow_defenses=? WHERE zone_id=?", array($_POST['attacks'], $_POST['warpedits'], $_POST['planets'], $_POST['trades'], $_POST['defenses'], $zoneinfo['zone_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    global $l_global_mmenu;

    $template->assign("title", $title);
    $template->assign("l_ze_saved", $l_ze_saved);
    $template->assign("l_clickme", $l_clickme);
    $template->assign("l_ze_return", $l_ze_return);
    $template->assign("l_global_mmenu", $l_global_mmenu);
    $template->display("$templateset/zoneedit-change.tpl");

    include_once ("./footer.php");
    die();
}

if ($zoneinfo['allow_attack'] == 'Y')
{
    $yattack = "checked";
}
else
{
    $nattack = "checked";
}

if ($zoneinfo['allow_warpedit'] == 'Y')
{
    $ywarpedit = "checked";
}
elseif ($zoneinfo['allow_warpedit'] == 'N')
{
    $nwarpedit = "checked";
}
else
{
    $lwarpedit = "checked";
}

if ($zoneinfo['allow_planet'] == 'Y')
{
    $yplanet = "checked";
}
elseif ($zoneinfo['allow_planet'] == 'N')
{
    $nplanet = "checked";
}
else
{
    $lplanet = "checked";
}

if ($zoneinfo['allow_trade'] == 'Y')
{
    $ytrade = "checked";
}
elseif ($zoneinfo['allow_trade'] == 'N')
{
    $ntrade = "checked";
}
else
{
    $ltrade = "checked";
}

if ($zoneinfo['allow_defenses'] == 'Y')
{
    $ydefense = "checked";
}
elseif ($zoneinfo['allow_defenses'] == 'N')
{
    $ndefense = "checked";
}
else
{
    $ldefense = "checked";
}

global $l_global_mmenu;

$template->assign("title", $title);
$template->assign("zoneinfo_zone_name", $zoneinfo['zone_name']);
$template->assign("l_ze_name", $l_ze_name);
$template->assign("l_title_port", $l_title_port);
$template->assign("l_warpedit", $l_warpedit);
$template->assign("l_sector_def", $l_sector_def);
$template->assign("l_ze_genesis", $l_ze_genesis);
$template->assign("l_ze_allow", $l_ze_allow);
$template->assign("l_ze_attacks", $l_ze_attacks);
$template->assign("l_yes", $l_yes);
$template->assign("l_no", $l_no);
$template->assign("yattack", $yattack);
$template->assign("nattack", $nattack);
$template->assign("ywarpedit", $ywarpedit);
$template->assign("lwarpedit", $lwarpedit);
$template->assign("nwarpedit", $nwarpedit);
$template->assign("ydefense", $ydefense);
$template->assign("ldefense", $ldefense);
$template->assign("ndefense", $ndefense);
$template->assign("yplanet", $yplanet);
$template->assign("lplanet", $lplanet);
$template->assign("nplanet", $nplanet);
$template->assign("ytrade", $ytrade);
$template->assign("ltrade", $ltrade);
$template->assign("ntrade", $ntrade);
$template->assign("l_zi_limit", $l_zi_limit);
$template->assign("l_submit", $l_submit);
$template->assign("l_ze_return", $l_ze_return);
$template->assign("l_clickme", $l_clickme);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/zoneedit.tpl");
include_once ("./footer.php");

// Dynamic functions
dynamic_loader ($db, "zoneedit_die.php");

?>
