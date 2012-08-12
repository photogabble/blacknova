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
// File: spy_change.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");

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
    include_once './footer.php';
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

// Changing your spy settings on enemy planet

$res = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE owner_id=? AND spy_id=?", array($playerinfo['player_id'], $spy_id));
$spy = $res->fields;

if ($res->RecordCount())
{
    if (empty($doit))
    {
        $try_sabot   = ($spy['try_sabot'] == 'Y')   ? "checked" : "";
        $try_inter   = ($spy['try_inter'] == 'Y')   ? "checked" : "";
        $try_birth   = ($spy['try_birth'] == 'Y')   ? "checked" : "";
        $try_steal   = ($spy['try_steal'] == 'Y')   ? "checked" : "";
        $try_torps   = ($spy['try_torps'] == 'Y')   ? "checked" : "";
        $try_fits    = ($spy['try_fits'] == 'Y')    ? "checked" : "";
        $try_capture = ($spy['try_capture'] == 'Y') ? "checked" : "";

        if ($spy['move_type'] == 'none')
        {
            $set_1 = 'checked';
            $set_2 = '';
            $set_3 = '';
        }
        elseif ($spy['move_type'] == 'toship')
        {
            $set_1 = '';
            $set_2 = 'checked';
            $set_3 = '';
        }
        else
        {
            $set_1 = '';
            $set_2 = '';
            $set_3 = 'checked';
        }

        if ($spy['planet_id'] == '0')
        {
            $set_1 .= " DISABLED";
        }

        $l_spy_changetitle = str_replace("[spyid]", $spy_id, $l_spy_changetitle);
        echo "<strong>$l_spy_changetitle</strong><br>";
        echo '<form name="bntform" action="spy_change.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
        echo "<input type=hidden name=doit value=1>";
        echo "<input type=hidden name=spy_id value=$spy_id>";
        echo "<input type=hidden name=planet_id value=$planet_id>";
        echo "<input type=hidden name=planet_id value=$planet_id>";
        echo "<input type=radio name=mode value=none $set_1> $l_spy_type1<br>";
        echo "<input type=radio name=mode value=toship $set_2> $l_spy_type2<br>";
        echo "<input type=radio name=mode value=toplanet $set_3> $l_spy_type3<br><br>";

        if ($spy['job_id']>0)
        {
            $spyjobvar = "l_spy_jobs_" . $spy['job_id'];
            global $$spyjobvar;
            $job = $$spyjobvar;

            $temp = number_format(100*$spy['spy_percent'], 5, $local_number_dec_point, $local_number_thousands_sep);
            $l_spy_occupied = str_replace("[spyid]", "$spy_id", $l_spy_occupied);
            $l_spy_occupied = str_replace("[job]", $job, $l_spy_occupied);
            $l_spy_occupied = str_replace("[percent]", $temp, $l_spy_occupied);
            echo "$l_spy_occupied<br>";
            echo "<input type=checkbox name=dismiss> $l_spy_dismiss<br><br>";
        }

        echo $l_spy_trytitle . ":<br>";
        echo "<input type=checkbox name=try_sabot $try_sabot> $l_spy_try_sabot<br>";
        echo "<input type=checkbox name=try_inter $try_inter> $l_spy_try_inter<br>";
        echo "<input type=checkbox name=try_birth $try_birth> $l_spy_try_birth<br>";
        echo "<input type=checkbox name=try_steal $try_steal> $l_spy_try_steal<br>";
        echo "<input type=checkbox name=try_torps $try_torps> $l_spy_try_torps<br>";
        echo "<input type=checkbox name=try_fits $try_fits> $l_spy_try_fits<br>";
        if ($allow_spy_capture_planets)
        {
            echo "<input type=checkbox name=try_capture $try_capture> $l_spy_try_capture<br><br>";
        }

        echo "<input name=submit_button type=submit value=\"$l_spy_changebutton\">";
        echo "</form>";
        if ($planet_id == -1) // Not called from Planet Menu
        {
            echo "<a href=spy.php>$l_clickme</a> $l_spy_linkback";
        }
        else
        {
            echo "<a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu";
        }
    }
    else
    {
        $try_sabot   = isset($_POST['try_sabot'])   ? "Y" : "N";
        $try_inter   = isset($_POST['try_inter'])   ? "Y" : "N";
        $try_birth   = isset($_POST['try_birth'])   ? "Y" : "N";
        $try_steal   = isset($_POST['try_steal'])   ? "Y" : "N";
        $try_torps   = isset($_POST['try_torps'])   ? "Y" : "N";
        $try_fits    = isset($_POST['try_fits'])    ? "Y" : "N";
        $try_capture = isset($_POST['try_capture']) ? "Y" : "N";

        if ($mode!="toship" && $mode!="toplanet" && $mode!="none")
        {
            $mode = "toship";
        }

        if ($spy['planet_id']=='0' && $mode == "none")
        {
            $mode = "toship";
        }

        if ($dismiss)
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET move_type=?, job_id='0', spy_percent='0.0', " .
                                        "try_sabot=?, try_inter=?, try_birth=?, " .
                                        "try_steal=?, try_torps=?, try_fits=?, " .
                                        "try_capture=? WHERE spy_id=?", array($mode, $try_sabot, $try_inter, $try_birth, $try_steal, $try_torps, $try_fits, $try_capture, $spy_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            echo "$l_spy_changed2<br>";
        }
        else
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET move_type=?, try_sabot=?, try_inter=?, try_birth=?, try_steal=?, try_torps=?, try_fits=?, try_capture=? WHERE spy_id=?", array($mode, $try_sabot, $try_inter, $try_birth, $try_steal, $try_torps, $try_fits, $try_capture, $spy_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            echo "$l_spy_changed1<br>";
        }

        if ($planet_id == -1)
        {
            echo "<a href=spy.php>$l_clickme</a> $l_spy_linkback";
        }
        else
        {
            echo "<a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu";
        }
    }
}
else
{
    echo "$l_spy_changefailed<br>";
}

global $l_global_mmenu;
$template->assign("title", $title);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/spy.tpl");

include_once './footer.php';
?>
