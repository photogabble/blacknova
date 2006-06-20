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
// File: igb_consolidate2.php

include_once ("./global_includes.php");

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'igb');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);
$title = $l_igb_title;
include_once ("./header.php");

if (!$allow_ibank)
{
    include_once ("./igb_error.php");
}

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND owner='?'", array($playerinfo['player_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$planetinfo = $debug_query->RecordCount();

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND team='?'", array($playerinfo['team']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$teamplanetinfo = $debug_query->RecordCount();

if ($portinfo['port_type'] != 'shipyard' && $portinfo['port_type'] != 'upgrades' && $portinfo['port_type'] != 'devices' && $planetinfo < 1 && $teamplanetinfo < 1)
{
    echo $l_noport . "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
else
{
    $no_body = 2;
}

updatecookie($db);

$result = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id='?'", array($playerinfo['player_id']));
$account = $result->fields;

//echo "<body bgcolor=\"#666\" text=\"#FFFFFF\" link=\"#00FF00\" vlink=\"#00FF00\" alink=\"#FF0000\">";

echo "<style type=\"text/css\">";
echo "    input.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}";
echo "    select.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}";
echo "</style>";

echo "\n<div style=\"text-align:center;\">";
echo "\n<img alt=\"\" src=\"templates/$templateset/images/div1.png\">";
//echo "\n<table width=\"600\" height=\"350\" border=\"0\">";
//echo "\n<tr><td align=\"center\" background=\"templates/$templateset/images/igbscreen.png\">";

global $playerinfo, $account;
global $db;
global $dplanet_id, $minimum, $maximum, $igb_tconsolidate, $ibank_paymentfee;
global $l_igb_planetconsolidate, $l_igb_back, $l_igb_logout;
global $l_igb_errunknownplanet, $l_igb_unnamed, $l_igb_errnotyourplanet;
global $l_igb_currentpl, $l_igb_in, $l_igb_transferamount, $l_igb_plaffected;
global $l_igb_transferfee, $l_igb_turncost, $l_igb_amounttransferred;
global $l_igb_consolidate;

$res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id='?'", array($dplanet_id));
if (!$res || $res->EOF)
{
    $backlink = "igb_transfer.php";
    $igb_errmsg = $l_igb_errunknownplanet;
    include_once ("./igb_error.php");
}

$dest = $res->fields;

if (empty($dest['name']))
{
    $dest['name'] = $l_igb_unnamed;
}

if ($dest['owner'] != $playerinfo['player_id'])
{
    $backlink = "igb_transfer.php";
    $igb_errmsg = $l_igb_errnotyourplanet;
    include_once ("./igb_error.php");
}

$minimum = preg_replace('/[^0-9]/','',$minimum);
$maximum = preg_replace('/[^0-9]/','',$maximum);

$query = "SELECT SUM(credits) as total, COUNT(*) as count from {$db->prefix}planets WHERE owner='?' " .
         "AND credits != 0";

if ($minimum != 0)
{
    $query .= " AND credits >= $minimum";
}

if ($maximum != 0)
{
    $query .= " AND credits <= $maximum";
}

$query .= " AND planet_id !='?'";

// DB NOT CLEANED!
$res = $db->Execute($query, array($playerinfo['player_id'], $dplanet_id));
$amount = $res->fields;

$fee = $ibank_paymentfee * $amount['total'];

$tcost = ceil($amount['count'] / $igb_tconsolidate);
$transfer = $amount['total'] - $fee;

$template->assign("l_igb_planetconsolidate", $l_igb_planetconsolidate);
$template->assign("l_igb_currentpl", $l_igb_currentpl);
$template->assign("dest_name", $dest['name']);
$template->assign("l_igb_in", $l_igb_in);
$template->assign("dest_sector_id", $dest['sector_id']);
$template->assign("dest_credits", number_format($dest['credits']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_igb_transferamount", $l_igb_transferamount);
$template->assign("total", number_format($amount['total']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_igb_transferfee", $l_igb_transferfee);
$template->assign("fee", number_format($fee), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_igb_plaffected", $l_igb_plaffected);
$template->assign("count", number_format($amount['count']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_igb_turncost", $l_igb_turncost);
$template->assign("tcost", number_format($tcost), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_igb_amounttransferred", $l_igb_amounttransferred);
$template->assign("transfer", number_format($transfer), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("minimum", $minimum);
$template->assign("maximum", $maximum);
$template->assign("dplanet_id", $dplanet_id);
$template->assign("l_igb_consolidate", $l_igb_consolidate);
$template->assign("l_igb_back", $l_igb_back);
$template->assign("l_igb_logout", $l_igb_logout);
$template->display("$templateset/igb_consolidate2.tpl");

echo "<img alt=\"\" src=\"templates/$templateset/images/div2.png\">";
echo "</div>";

include_once ("./footer.php");
?>
