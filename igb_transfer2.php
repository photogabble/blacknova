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
// File: igb_transfer2.php

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

global $playerinfo;
global $account;
global $player_id;
global $splanet_id;
global $dplanet_id;
global $igb_min_turns;
global $igb_svalue;
global $ibank_paymentfee;
global $igb_trate;
global $l_igb_sendyourself, $l_igb_unknowntargetship, $l_igb_min_turns, $l_igb_min_turns2;
global $l_igb_mustwait, $l_igb_shiptransfer, $l_igb_igbaccount, $l_igb_maxtransfer;
global $l_igb_unlimited, $l_igb_maxtransferpercent, $l_igb_transferrate, $l_igb_recipient;
global $l_igb_seltransferamount, $l_igb_transfer, $l_igb_back, $l_igb_logout, $l_igb_in;
global $l_igb_errplanetsrcanddest, $l_igb_errunknownplanet, $l_igb_unnamed;
global $l_igb_errnotyourplanet, $l_igb_planettransfer, $l_igb_srcplanet, $l_igb_destplanet;
global $l_igb_transferrate2, $l_igb_seltransferamount, $l_igb_errnobase;
global $db;

if ($_POST['splanet_id'] != $_POST['dplanet_id'])
{
    $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id=?", array($_POST['splanet_id']));
    if (!$res || $res->EOF)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errunknownplanet;
        include_once ("./igb_error.php");
    }

    $source = $res->fields;

    if (empty($source['name']))
    {
        $source[name] = $l_igb_unnamed;
    }

    $res = $db->Execute("SELECT name, credits, owner, sector_id, base FROM {$db->prefix}planets WHERE planet_id=?", array($_POST[dplanet_id]));
    if (!$res || $res->EOF)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errunknownplanet;
        include_once ("./igb_error.php");
    }

    $dest = $res->fields;

    if (empty($dest['name']))
    {
        $dest[name] = $l_igb_unnamed;
    }

    if ($dest['base'] == 'N')
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errnobase;
        include_once ("./igb_error.php");
    }

    if ($source['owner'] != $playerinfo['player_id'] || $dest['owner'] != $playerinfo['player_id'])
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errnotyourplanet;
        include_once ("./igb_error.php");
    }

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate2 = str_replace("[igb_num_percent]", number_format($percent, 0, $local_number_dec_point, $local_number_thousands_sep), $l_igb_transferrate2);

    echo "\n<form action=\"igb_transfer.php3\"  method=\"post\" accept-charset=\"utf-8\">";
    echo "\n<table width=\"600\" height=\"350\" border=\"0\">";
    echo "\n  <tr>";
    echo "\n    <td align=\"center\" background=\"templates/{$templateset}/images/igbscreen.png\">";
    echo "\n      <table width=\"520\" height=\"300\" border=\"0\">";

    echo "<tr><td colspan=2 align=center valign=top><font color=#00FF00>$l_igb_planettransfer<br>---------------------------------</font></td></tr>" .
         "<tr valign=top>" .
         "<td><font color=#00FF00>$l_igb_srcplanet $source[name] $l_igb_in $source[sector_id] :" .
         "<td align=right><font color=#00FF00>" . number_format($source['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C" .
         "<tr valign=top>" .
         "<td><font color=#00FF00>$l_igb_destplanet $dest[name] $l_igb_in $dest[sector_id] :" .
         "<td align=right><font color=#00FF00>" . number_format($dest['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C" .
         "<form action=\"igb_transfer.php3\" method=\"post\" accept-charset=\"utf-8\">" .
         "<tr valign=top>" .
         "<td><br><font color=#00FF00>$l_igb_seltransferamount :</td>" .
         "<td align=right><br><input class=term type=text size=15 maxlength=50 name=amount value=0><br>" .
         "<br><input class=term type=submit value=$l_igb_transfer></td>" .
         "<input type=hidden name=splanet_id value=$_POST[splanet_id]>" .
         "<input type=hidden name=dplanet_id value=$_POST[dplanet_id]>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font color=#00FF00>" .
         "$l_igb_transferrate2" .
         "<tr valign=bottom>" .
         "<td><font color=#00FF00><a href=igb_transfer.php>$l_igb_back</a></td><td align=right><font color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></font></td>" .
         "</tr>";
    echo "</table></td></tr></table></form>";

}
else // Ship transfer
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=?", array($_POST['player_id']));

    if ($playerinfo['player_id'] == $_POST['player_id'])
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_sendyourself;
        include_once ("./igb_error.php");
    }

    if (!$res || $res->EOF)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_unknowntargetship;
        include_once ("./igb_error.php");
    }

    $target = $res->fields;

    if ($target['turns_used'] < $igb_min_turns)
    {
        $l_igb_min_turns = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns);
        $l_igb_min_turns = str_replace("[igb_target_char_name]", $target['character_name'], $l_igb_min_turns);
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_min_turns;
        include_once ("./igb_error.php");
    }

    if ($playerinfo['turns_used'] < $igb_min_turns)
    {
        $l_igb_min_turns2 = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns2);
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_min_turns2;
        include_once ("./igb_error.php");
    }

    if ($igb_trate > 0)
    {
        $curtime = time();
        $curtime -= $igb_trate * 60;
        $res = $db->Execute("SELECT UNIX_TIMESTAMP(transfer_time) as time FROM {$db->prefix}ibank_transfers WHERE UNIX_TIMESTAMP(transfer_time) > ? AND source_id=? AND dest_id=?", array($curtime, $playerinfo['player_id'], $target['player_id']));
        if (!$res->EOF)
        {
            $time = $res->fields;
            $difftime = ($time['time'] - $curtime) / 60;
            $l_igb_mustwait = str_replace("[igb_target_char_name]", $target['character_name'], $l_igb_mustwait);
            $l_igb_mustwait = str_replace("[igb_trate]", number_format($igb_trate, 0, $local_number_dec_point, $local_number_thousands_sep), $l_igb_mustwait);
            $l_igb_mustwait = str_replace("[igb_difftime]", number_format($difftime, 0, $local_number_dec_point, $local_number_thousands_sep), $l_igb_mustwait);
            $backlink = "igb_transfer.php";
            $igb_errmsg = $l_igb_mustwait;
            include_once ("./igb_error.php");
        }
    }

    echo "\n<form action=\"igb_transfer.php2\" method=\"post\" accept-charset=\"utf-8\">";
    echo "\n<table width=\"600\" height=\"350\" border=\"0\">";
    echo "\n  <tr>";
    echo "\n    <td align=\"center\" background=\"templates/{$templateset}/images/igbscreen.png\">";
    echo "\n      <table width=\"520\" height=\"300\" border=\"0\">";

    echo "<tr><td colspan=2 align=center valign=top><font color=#00FF00>$l_igb_shiptransfer<br>---------------------------------</font></td></tr>" .
         "<tr valign=top><td><font color=#00FF00>$l_igb_igbaccount :</td><td align=right><font color=#00FF00>" . number_format($account['balance'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C</td></tr>";

    if ($igb_svalue == 0)
    {
        echo "<tr valign=top><td><font color=#00FF00>$l_igb_maxtransfer :</td><td align=right><font color=#00FF00>$l_igb_unlimited</td></tr>";
    }
    else
    {
        $percent = $igb_svalue * 100;
        $score = gen_score($db,$playerinfo['player_id']);
        $maxtrans = $score * $score * $igb_svalue;
        $l_igb_maxtransferpercent = str_replace("[igb_percent]", $percent, $l_igb_maxtransferpercent);
        echo "<tr valign=top><td nowrap=\"nowrap\"><font color=#00FF00>$l_igb_maxtransferpercent :</td><td align=right><font color=#00FF00>" . number_format($maxtrans, 0, $local_number_dec_point, $local_number_thousands_sep) . " C</td></tr>";
    }

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate = str_replace("[igb_num_percent]", number_format($percent, 0, $local_number_dec_point, $local_number_thousands_sep), $l_igb_transferrate);
    echo "<tr valign=top><td><font color=#00FF00>$l_igb_recipient :</td><td align=right><font color=#00FF00>$target[character_name]&nbsp;&nbsp;</td></tr>" .
         "<form action=\"igb_transfer.php3\" method=\"post\" accept-charset=\"utf-8\">" .
         "<tr valign=top>" .
         "<td><br><font color=#00FF00>$l_igb_seltransferamount :</td>" .
         "<td align=right><br><input class=term type=text size=15 maxlength=50 name=amount value=0><br>" .
         "<br><input class=term type=submit value=$l_igb_transfer></td>" .
         "<input type=hidden name=player_id value=$player_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font color=#00FF00>" .
         "$l_igb_transferrate" .
         "<tr valign=bottom>" .
         "<td><font color=#00FF00><a href=igb_transfer.php>$l_igb_back</a></td><td align=right><font color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
         "</tr>";
    echo "</table></td></tr></table></form>";
}

echo "<img alt=\"\" src=\"templates/$templateset/images/div2.png\">";
echo "</div>";

include_once ("./footer.php");
?>
