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
// File: log.php

include_once ("./global_includes.php");
include_once ("./backends/sha256/shaclass.php"); // Include the sha256 backend

dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");

// Load language variables
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'log');
load_languages($db, $raw_prefix, 'lrscan');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'planet');
load_languages($db, $raw_prefix, 'spy');

$title = $l_log_titlet;
include_once ("./header.php");

$sha256adminpass = sha256::hash($adminpass);

$l_log_months_temp = '';
$l_log_months_short_temp = '';
if (isset($_GET['sha256swordfish']))
{
    $sha256swordfish = $_GET['sha256swordfish'];
}

if (isset($_POST['sha256swordfish']))
{
    $sha256swordfish = $_POST['sha256swordfish'];
}

if (!isset($sha256swordfish))
{
    $sha256swordfish = '';
}

if (isset($_GET['player']))
{
    $player = $_GET['player'];
}

if (isset($_POST['player']))
{
    $player = $_POST['player'];
}

if (!isset($player))
{
    $player = '';
}

if (!isset($nonext))
{
    $nonext = '';
}

if ($sha256adminpass != $sha256swordfish)
{
    checklogin($db);
    get_info($db);
//    checkdead($db);
}

if (isset($_POST['startdate']))
{
    $startdate = $_POST['startdate'];
}
elseif (isset($_GET['startdate']))
{
    $startdate = $_GET['startdate'];
}

if ($sha256swordfish == $sha256adminpass) // Check if called by admin script
{
    $playerinfo['player_id'] = $player;

    if ($playerinfo['player_id'] == 0)
    {
        $playerinfo['character_name'] = 'Administrator';
//        $playerinfo['player_id'] = 1;
    }
    else
    {
        $res = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=?", array($player));
        $targetname = $res->fields;
        $playerinfo['character_name'] = $targetname['character_name'];
    }
}

// Dynamic functions
dynamic_loader ($db, "simple_date.php");
dynamic_loader ($db, "log_parse.php");

$yres = 390;

echo '<div style="text-align:center;">';
echo "<table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
$logline = str_replace("[player]", "$playerinfo[character_name]", $l_log_log);

echo "<tr><td><td width=\"100%\"><td></tr>";
echo "<tr><td><td height=\"20\" style=\"background-image: url(templates/$templateset/images/top_panel.png); background-repeat:no-repeat\">";
echo "<font style=\"font-size: 0.8em;\" color=\"#040658\"><strong>&nbsp;&nbsp;&nbsp;" . $logline . "</strong></font>";
echo "</td><td><td>&nbsp;</tr>";
echo "<tr><td valign=\"bottom\">";

echo "<td colspan=\"2\"><table border=\"1\" width=\"100%\"><tr><td  bgcolor=\"#63639C\">";

if (empty($startdate))
{
    $startdate = date("Y-m-d");
}

$res = $db->Execute("SELECT * FROM {$db->prefix}logs WHERE player_id=? AND log_time LIKE ? ORDER BY log_time DESC, type DESC", array($playerinfo['player_id'], $startdate . '%'));
while (!$res->EOF)
{
    $logs[] = $res->fields;
    $res->MoveNext();
}

$log_months_temp = "l_log_months_" . (substr($startdate, 5, 2) - 1);
$log_months_short_temp = "l_log_months_short_" . (substr($startdate, 5, 2) - 1);
$entry = simple_date($local_logdate_med_format, substr($startdate, 0, 4), $$log_months_temp, $$log_months_short_temp, substr($startdate, 8, 2), 0, 0 ) ;

echo "<div id=\"divScroller1\">" .
     "\n<div id=\"dynPage0\" class=\"dynPage\">" .
     '<div style="text-align:center;">' .
     "<br>" .
     "<font style=\"font-size: 0.8em;\" color=\"#DEDEEF\"><strong>$l_log_start $entry</strong></font>" .
     "<hr width=\"80%\" size=\"1\" noshade=\"noshade\" style=\"color: #040658\">" .
     "</div>";

if (!empty($logs))
{
    foreach($logs as $log)
    {
        $event = log_parse($log, $l_log_pod, $l_log_nopod, $space_plague_kills);

        $log_months_temp = "l_log_months_" . (substr($log['log_time'], 5, 2) - 1);
        $log_months_short_temp = "l_log_months_short_" . (substr($log['log_time'], 5, 2) - 1);
        $time = simple_date($local_logdate_full_format, substr($log['log_time'], 0, 4), $$log_months_temp, $$log_months_short_temp, substr($log['log_time'], 8, 2), substr($log['log_time'], 11, 2), substr($log['log_time'], 14, 2) );

        echo "<table border=\"0\" cellspacing=\"5\" width=\"100%\">" .
             "<tr>" .
             "<td><font style=\"font-size: 0.8em;\" color=\"#040658\"><strong>$event[title]</strong></font></td>" .
             "<td align=\"right\"><font style=\"font-size: 0.8em;\" color=\"#040658\"><strong>$time</strong></font></td>" .
             "<tr><td colspan=\"2\">" .
             "<font style=\"font-size: 0.8em;\" color=\"#DEDEEF\">" .
             "$event[text]" .
             "</font></td></tr>" .
             "</table>" .
             "<div style=\"text-align:center;\"><hr width=\"80%\" size=\"1\" noshade=\"noshade\" style=\"color: #040658\"></div>";
    }
}

echo '<div style="text-align:center;">' .
     "<br>" .
     "<font style=\"font-size: 0.8em;\" color=\"#DEDEEF\"><strong>$l_log_end $entry</strong></font>" .
     "</div>" .
     "</div>" .
     "</div>\n";

$month = substr($startdate, 5, 2);
$day = substr($startdate, 8, 2) - 1;
$year = substr($startdate, 0, 4);

$yesterday = adodb_mktime (0,0,0,$month,$day,$year);
$yesterday = date("Y-m-d", $yesterday);

$day = substr($startdate, 8, 2) - 2;

$yesterday2 = adodb_mktime (0,0,0,$month,$day,$year);
$yesterday2 = date("Y-m-d", $yesterday2);

echo "</td></tr></table>";
//echo "</div>";

$log_months_temp = "l_log_months_" . (substr($startdate, 5, 2) - 1);
$log_months_short_temp = "l_log_months_short_" . (substr($startdate, 5, 2) - 1);
$date1 = simple_date($local_logdate_short_format, 0, $$log_months_temp, $$log_months_short_temp, substr($startdate, 8, 2), 0, 0);

$log_months_temp = "l_log_months_" . (substr($yesterday, 5, 2) - 1);
$log_months_short_temp = "l_log_months_short_" . (substr($yesterday, 5, 2) - 1);
$date2 = simple_date($local_logdate_short_format, 0, $$log_months_temp, $$log_months_short_temp, substr($yesterday, 8, 2), 0, 0);

$log_months_temp = "l_log_months_" . (substr($yesterday2, 5, 2) - 1);
$log_months_short_temp = "l_log_months_short_" . (substr($yesterday2, 5, 2) - 1);
$date3 = simple_date($local_logdate_short_format, 0, $$log_months_temp, $$log_months_short_temp, substr($yesterday2, 8, 2), 0, 0);

$month = substr($startdate, 5, 2);
$day = substr($startdate, 8, 2) - 3;
$year = substr($startdate, 0, 4);

$backlink = adodb_mktime (0,0,0,$month,$day,$year);
$backlink = date("Y-m-d", $backlink);

$day = substr($startdate, 8, 2) + 3;

$nextlink = adodb_mktime (0,0,0,$month,$day,$year);
if ($nextlink > time())
{
    $nextlink = time();
}

$nextlink = date("Y-m-d", $nextlink);

if ($startdate == date("Y-m-d"))
{
    $nonext = 1;
}

if ($sha256swordfish == $sha256adminpass) // Fix for admin log view
{
    $postlink =  "&player=$player&sha256swordfish=" . urlencode($sha256swordfish);
}
else
{
    $postlink = "";
}

echo "<tr><td><td align=right>" .
     "<a href=\"log.php?startdate=${backlink}$postlink\"><font color=\"white\"><strong>&lt;&lt;&lt;</strong></font></a>&nbsp;&nbsp;&nbsp;" .
     "<a href=\"log.php?startdate=${yesterday2}$postlink\"><font color=\"white\"><strong>$date3</strong></font></a>" .
     "&nbsp;|&nbsp;" .
     "<a href=\"log.php?startdate=${yesterday}$postlink\"><font color=\"white\"><strong>$date2</strong></font></a>" .
     " | " .
     "<a href=\"log.php?startdate=${startdate}$postlink\"><font color=\"white\"><strong>$date1</strong></font></a>";

if ($nonext != 1)
{
    echo "&nbsp;&nbsp;&nbsp;<a href=log.php?startdate=${nextlink}$postlink><font color=white><strong>&gt;&gt;&gt;</strong></font></a>";
}

echo "&nbsp;&nbsp;&nbsp;";

if ($sha256swordfish == $sha256adminpass)
{
    echo "<tr><td><td>" .
         '<form name="bntform" action="admin.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">' .
         "<input type=hidden name=sha256swordfish value=\"$sha256swordfish\">" .
         "<input type=hidden name=menu value=logview>" .
         "<input type=submit name=submit_button value=\"Return to Admin\"></td></tr>";
}
else
{
    echo "<tr><td><td><p><font style=\"font-size: 0.8em;\" face=arial><a href=\"main.php\">" . $l_global_mmenu . "</a></font></td></tr>";
}

echo "</table>" . "</div>";

include_once ("./footer.php");
?>
