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
// File: igb_deposit2.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

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

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND owner=$playerinfo[player_id]");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$planetinfo = $debug_query->RecordCount();

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND team=$playerinfo[team]");
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

$result = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=$playerinfo[player_id]");
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

global $playerinfo, $igbinfo;
global $amount;
global $account;
global $l_igb_invaliddepositinput, $l_igb_nozeroamount2, $l_igb_notenoughcredits, $l_igb_accounts, $l_igb_logout;
global $l_igb_operationsuccessful, $l_igb_creditstoyou, $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_back;
global $db;

$amount = preg_replace('/[^0-9]/','',$_POST['amount']);
if (($amount * 1) != $amount)
{
    $backlink = "igb_deposit.php";
    $igb_errmsg = $l_igb_invaliddepositinput;
    include_once ("./igb_error.php");
}

if ($amount == 0)
{
    $backlink = "igb_deposit.php";
    $igb_errmsg = $l_igb_nozeroamount2;
    include_once ("./igb_error.php");
}

if ($amount > $playerinfo['credits'])
{
    $backlink = "igb_deposit.php";
    $igb_errmsg = $l_igb_notenoughcredits;
    include_once ("./igb_error.php");
}

$debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=balance+$amount WHERE " .
                            "player_id=$playerinfo[player_id]");
db_op_result($db,$debug_query,__LINE__,__FILE__);

$debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits-$amount WHERE " .
                            "player_id=$playerinfo[player_id]");
db_op_result($db,$debug_query,__LINE__,__FILE__);

$template->assign("l_igb_operationsuccessful", $l_igb_operationsuccessful);
$template->assign("amount", number_format($amount), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_igb_creditstoyou", $l_igb_creditstoyou);
$template->assign("l_igb_accounts", $l_igb_accounts);
$template->assign("l_igb_shipaccount", $l_igb_shipaccount);
$template->assign("l_igb_igbaccount", $l_igb_igbaccount);
$template->assign("playerinfo_credits", number_format($playerinfo['credits']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("igbinfo_balance", number_format($igbinfo['balance']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_igb_back", $l_igb_back);
$template->assign("l_igb_logout", $l_igb_logout);
$template->display("$templateset/igb_deposit2.tpl");

echo "<img alt=\"\" src=\"templates/$templateset/images/div2.png\">";
echo "</div>";

include_once ("./footer.php");
?>
