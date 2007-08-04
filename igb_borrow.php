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
// File: igb_borrow.php
include_once ("./global_includes.php");

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "gen_score.php");

// Load language variables
load_languages($db, $raw_prefix, 'igb');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'igb_borrow');

checklogin($db);
get_info($db);
checkdead($db);
$title = $l_igb_title;
include_once ("./header.php");

if (!$allow_ibank)
{
    include_once ("./igb_error.php");
}

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND owner=?", array($playerinfo['player_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$planetinfo = $debug_query->RecordCount();

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND team=?", array($playerinfo['team']));
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

$result = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=?", array($playerinfo['player_id']));
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

global $playerinfo, $account, $amount, $ibank_loanlimit, $ibank_loanfactor;
global $l_igb_invalidamount,$l_igb_notwoloans, $l_igb_loantoobig;
global $l_igb_takenaloan, $l_igb_loancongrats, $l_igb_loantransferred;
global $l_igb_loanfee, $l_igb_amountowned, $igb_lrate, $l_igb_loanreminder1;
global $db, $l_igb_back, $l_igb_logout;

$amount = preg_replace('/[^0-9]/','',$_POST['amount']);
if (($amount * 1) != $amount)
{
    $backlink = "igb_loans.php";
    $igb_errmsg = $l_igb_invalidamount;
    include_once ("./igb_error.php");
}

if ($amount <= 0)
{
    $backlink = "igb_loans.php";
    $igb_errmsg = $l_igb_invalidamount;
    include_once ("./igb_error.php");
}

if ($account['loan'] != 0)
{
    $backlink = "igb_loans.php";
    $igb_errmsg = $l_igb_notwoloans;
    include_once ("./igb_error.php");
}

$score = gen_score($db,$playerinfo['player_id']);
$maxtrans = $score * $score * $ibank_loanlimit;

if ($amount > $maxtrans)
{
    $backlink = "igb_loans.php";
    $igb_errmsg = $l_igb_loantoobig;
    include_once ("./igb_error.php");
}

$amount2 = $amount * $ibank_loanfactor;
$amount3 = $amount + $amount2;
$hours = $igb_lrate / 60;
$mins = $igb_lrate % 60;
$l_igb_loanreminder1 = str_replace("[hours]", $hours, $l_igb_loanreminder1);
$l_igb_loanreminder1 = str_replace("[mins]", $mins, $l_igb_loanreminder1);
$l_igb_loanreminder1 = $l_igb_loanreminder1 . "<br><br>" . $l_igb_loanreminder2;

$stamp = date("Y-m-d H:i:s");
$debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET loan=?, loantime=? WHERE " .
                            "player_id=?", array($amount3, $stamp, $playerinfo['player_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);

$debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits+? WHERE " .
                            "player_id=?", array($amount, $playerinfo['player_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);

$template->assign("l_igb_takenaloan", $l_igb_takenaloan);
$template->assign("l_igb_loancongrats", $l_igb_loancongrats);
$template->assign("l_igb_loantransferred", $l_igb_loantransferred);
$template->assign("amount", number_format($amount, 0, $local_number_dec_point, $local_number_thousands_sep));
$template->assign("l_igb_loanfee", $l_igb_loanfee);
$template->assign("amount2", number_format($amount2, 0, $local_number_dec_point, $local_number_thousands_sep));
$template->assign("l_igb_amountowned", $l_igb_amountowned);
$template->assign("amount3", number_format($amount3, 0, $local_number_dec_point, $local_number_thousands_sep));
$template->assign("l_igb_loanreminder1", $l_igb_loanreminder1);
$template->assign("l_igb_back", $l_igb_back);
$template->assign("l_igb_logout", $l_igb_logout);
$template->display("$templateset/igb_borrow.tpl");
echo "<img alt=\"\" src=\"templates/$templateset/images/div2.png\">";
echo "</div>";

include_once ("./footer.php");
?>
