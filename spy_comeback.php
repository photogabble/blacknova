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
// File: spy_comeback.php

include_once ("./global_includes.php");

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "linecolor.php");

// Load language variables
load_languages($db, $raw_prefix, 'spy');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'main');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_spy_title;
echo "<h1>" . $title. "</h1>\n";
updatecookie($db);

seed_mt_rand();

if (!$spy_success_factor)
{
    echo "<strong>$l_spy_disabled</strong><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if (!isset($_POST['doit']))
{
    $_POST['doit'] = '';
}
else
{
    $doit = $_POST['doit'];
}

if (!isset($_GET['doit']))
{
    $_GET['doit'] = '';
}
else
{
    $doit = $_GET['doit'];
}

if (!isset($_GET['command']))
{
    $_GET['command'] = '';
}
else
{
    $command = $_GET['command'];
}

if (!isset($_POST['command']))
{
    $_POST['command'] = '';
}
else
{
    $command = $_POST['command'];
}

if (!isset($by))
{
    $by = '';
}

if (!isset($by1))
{
    $by1 = '';
}

if (!isset($by2))
{
    $by2 = '';
}

if (!isset($by3))
{
    $by3 = '';
}

if (!isset($_POST['planet_id']))
{
    $_POST['planet_id'] = '';
}
else
{
    $planet_id = $_POST['planet_id'];
}

if (!isset($_GET['planet_id']))
{
    $_GET['planet_id'] = '';
}
else
{
    $planet_id = $_GET['planet_id'];
}

if (!isset($planet_id))
{
    $planet_id = '-1';
}

if (!isset($spy_id))
{
    $spy_id = '-1';
}

if (!isset($dismiss))
{
    $dismiss = '';
}

$line_color = $color_line2;

// Getting your spy back from enemy planet

if ($playerinfo['turns'] < 1)
{
    echo "$l_spy_noturn2<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
  
$res = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id));
$planetinfo = $res->fields;
if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
{
    echo "$l_planet_none<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
  
$res = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE owner_id =? AND spy_id=?  AND active = 'Y' AND planet_id=?", array($playerinfo['player_id'], $spy_id, $planetinfo['planet_id']));
if ($res->RecordCount())
{
    if (empty($doit))
    {
        $spy = $res->fields;
        $l_spy_confirm = str_replace("[spyid]", "$spy[spy_id]", $l_spy_confirm);
        echo "<strong>$l_spy_confirm</strong><br>";
        echo "<br><table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
        echo "<tr bgcolor=\"$color_header\">";
        echo "<td><font color=white><strong>$l_spy_codenumber</strong></font></td>";
        echo "<td><font color=white><strong>$l_spy_job</strong></font></td>";
        echo "<td><font color=white><strong>$l_spy_percent</strong></font></td>";
        echo "<td><font color=white><strong>$l_spy_move</strong></font></td>";
        echo "<td><font color=white><strong>$l_spy_action</strong></font></td>";
        echo "</tr>";

        if ($spy['job_id'] == 0)
        {
            $job = "$l_spy_jobs_0";
        }
        else
        {
            $spyjobvar = "l_spy_jobs_" . $spy['job_id'];
            global $$spyjobvar;
            $new_spy_job = $$spyjobvar;

            $job = "<a href=spy_change.php&spy_id=$spy[spy_id]&planet_id=$planet_id>$new_spy_job</a>";
        }

        $temp = $spy['move_type'];
        $l_movetype = 'l_spy_moves_' . $temp;
        $move = $$l_movetype;

        if ($spy['spy_percent'] == 0)
        {
            $spy['spy_percent'] = "-";
        }
        else
        {
            $spy['spy_percent'] = number_format(100*$spy['spy_percent'], 5, $local_number_dec_point, $local_number_thousands_sep);
        }

        echo "<tr bgcolor=" . linecolor() ."><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_id]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$job</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_percent]</font></td><td><font style=\"font-size: 0.8em;\"><a href=spy_change.php&spy_id=$spy[spy_id]&planet_id=$planet_id>$move</a></font></td><td><font style=\"font-size: 0.8em;\"><a href=spy_comeback.php&spy_id=$spy[spy_id]&planet_id=$planet_id&doit=1>$l_yes</a><br><br><a href=planet.php?planet_id=$planet_id>$l_no</a></font></td></tr></table><br>";
    }
    else
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET planet_id='0', job_id='0', spy_percent='0.0', ship_id=?, active='N', try_sabot='Y', try_inter='Y', try_birth='Y', try_steal='Y', try_torps='Y', try_fits='Y', try_capture='Y' WHERE spy_id=? ", array($shipinfo['ship_id'], $spy_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=?", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        echo "$l_spy_backonship<br>";
    }
}
else
{
    echo "$l_spy_backfailed<br><br>";
}

echo "<a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu";
global $l_global_mmenu;
$template->assign("title", $title);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/spy.tpl");

include_once ("./footer.php");
?>
