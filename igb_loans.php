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
// File: igb_loans.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

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

global $playerinfo, $account;
global $ibank_loanlimit, $ibank_loanfactor, $ibank_loaninterest;
global $l_igb_loanstatus,$l_igb_shipaccount, $l_igb_currentloan, $l_igb_repay;
global $l_igb_maxloanpercent, $l_igb_loanamount, $l_igb_borrow, $l_igb_loanrates;
global $l_igb_back, $l_igb_logout, $igb_lrate, $l_igb_loantimeleft, $l_igb_loanlate, $l_igb_repayamount;
global $db;

echo "\n";
if ($account['loan'] != 0)
{
    echo '\n<form name="bntform" action="igb_repay.php" method="post" accept-charset="utf-8" onsubmit="document.bntform.submit_button.disabled=true;">';
}
else
{
    echo '\n<form name="bntform" action="igb_borrow.php" method="post" accept-charset="utf-8" onsubmit="document.bntform.submit_button.disabled=true;">';
}

echo "\n  <table width=\"600\" height=\"350\" border=\"0\">";
echo "\n    <tr>";
echo "\n      <td align=\"center\" background=\"templates/$templateset/images/igbscreen.png\">";
echo "\n        <table width=\"520\" height=\"300\" border=\"0\">";
echo "\n          <tr><td colspan=2 align=center valign=top><font color=\"00FF00\">$l_igb_loanstatus<br>---------------------------------</font></td></tr>";
echo "\n          <tr valign=top><td><font color=\"00FF00\">$l_igb_shipaccount :</font></td><td align=right><font color=\"00FF00\">" . number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C</font></td></tr>";
echo "\n          <tr valign=top><td><font color=\"00FF00\">$l_igb_currentloan :</font></td><td align=right><font color=\"00FF00\">" . number_format($account['loan'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C</font></td></tr>";

if ($account['loan'] != 0)
{
    $curtime = time();
    $res = $db->Execute("SELECT UNIX_TIMESTAMP(loantime) as time FROM {$db->prefix}ibank_accounts WHERE " .
                        "player_id=?", array($playerinfo['player_id']));
    if (!$res->EOF)
    {
        $time = $res->fields;
    }

    $difftime = ($curtime - $time['time']) / 60;
    echo "\n\n          <tr valign=top><td nowrap=\"nowrap\"><font color=\"00FF00\">$l_igb_loantimeleft :</td>";

    if ($difftime > $igb_lrate)
    {
        echo "\n            <td align=right><font color=\"00FF00\">$l_igb_loanlate</td></tr>";
    }
    else
    {
        $difftime=$igb_lrate - $difftime;
        $hours = floor($difftime / 60);
        $mins = $difftime % 60;
        echo "\n<td align=right><font color=\"00FF00\">${hours}h ${mins}m</td></tr>";
    }

    $factor = $ibank_loanfactor *=100;
    $interest = $ibank_loaninterest *=100;

    $l_igb_loanrates = str_replace("[factor]", $factor, $l_igb_loanrates);
    $l_igb_loanrates = str_replace("[interest]", $interest, $l_igb_loanrates);

    echo "\n<tr valign=top>" .
         "\n<td><br><font color=\"00FF00\">$l_igb_repayamount :</font></td>" .
         "\n<td align=right><br><input class=term type=text size=15 maxlength=50 name=amount value='". MIN($playerinfo['credits'],$account['loan'])."'><br>" .
         "<br><input class=term name=submit_button type=submit value=$l_igb_repay></td>" .
         "\n<tr><td colspan=2 align=center><font color=\"00FF00\">" .
         "$l_igb_loanrates";
}
else
{
    $percent = $ibank_loanlimit * 100;
    $score = gen_score($db,$playerinfo['player_id']);
    $maxloan = $score * $score * $ibank_loanlimit;
    $l_igb_maxloanpercent = str_replace("[igb_percent]", $percent, $l_igb_maxloanpercent);
    echo "\n          <tr valign=top><td nowrap=\"nowrap\"><font color=\"00FF00\">$l_igb_maxloanpercent :</font></td><td align=right><font color=\"00FF00\">" . number_format($maxloan, 0, $local_number_dec_point, $local_number_thousands_sep) . " C</font></td></tr>";
    $factor = $ibank_loanfactor *=100;
    $interest = $ibank_loaninterest *=100;
    $l_igb_loanrates = str_replace("[factor]", $factor, $l_igb_loanrates);
    $l_igb_loanrates = str_replace("[interest]", $interest, $l_igb_loanrates);

    echo "\n          <tr valign=top>" .
         "\n            <td><br><font color=\"00FF00\">$l_igb_loanamount :</font></td>" .
         "\n            <td align=right><br><input class=term type=text size=15 maxlength=50 name=amount value=0><br>" .
         "<br><input class=term name=submit_button type=submit value=$l_igb_borrow></td>" .
         "\n</tr><tr><td colspan=2 align=center><font color=\"00FF00\">" .
         "$l_igb_loanrates</font></td></tr>";
}

echo "\n<tr valign=bottom>" .
      "\n<td><font color=\"00FF00\"><a href=\"igb_login.php\">$l_igb_back</a></font></td>" .
      "\n<td align=right><font color=\"00FF00\">&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></font></td>" .
      "\n</tr>";
echo "</table></td></table></form>";
echo "<img alt=\"\" src=\"templates/$templateset/images/div2.png\">";
echo "</div>";

include_once ("./footer.php");
?>
