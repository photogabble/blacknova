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
// File: spy_detect.php

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

// Detected data

    if (!empty($info_id))
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}detect WHERE {$db->prefix}detect.owner_id=? AND det_id=?", array($playerinfo['player_id'], $info_id));
        if ($res->RecordCount())
        {
            echo "<font color=red style=\"font-size: 1.5em;\">$l_spy_infodeleted<br><br></font>";
            $res = $db->Execute("DELETE FROM {$db->prefix}detect WHERE det_id=?", array($info_id));
        }
        else 
        {
            echo "<strong>$l_spy_infonotyours</strong><br><br>";
        }
    }
  
  
    if ($by == "time")
    {
        $by2 = "det_type asc, det_time desc";
    }
    elseif ($by=="time")
    {
        $by2 = "detect_data asc, det_time desc";
    }
    else
    {
        $by2 = "det_time desc";
    }
  
    $res = $db->Execute("SELECT * FROM {$db->prefix}detect WHERE {$db->prefix}detect.owner_id=? ORDER BY ?", array($playerinfo['player_id'], $by2));
    if (!$res->RecordCount())
    {
        echo "$l_spy_noinfo<br><br>";
        echo "<a href=spy.php>$l_clickme</a> $l_spy_linkback<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }
  
    echo "<a href=spy.php>$l_clickme</a> $l_spy_linkback<br>";
  
    echo "<br><table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
    echo "<tr bgcolor=\"$color_header\"><td colspan=99 align=center><font color=white><strong>$l_spy_infotitle</strong></font></td></tr>";
    echo "<tr bgcolor=\"$color_line2\">";
  
    echo "<td><strong><a href=\"spy_detect.php\">$l_spy_time</a></strong></td>";
    echo "<td><strong><a href=\"spy_detect.php&by=type\">$l_spy_type</a></strong></td>";
    echo "<td><strong><a href=\"spy_detect.php&by=detect_data\">$l_spy_info</a></strong></td>";
    echo "<td><strong><font color=white>$l_spy_action</font></strong></td>";
    echo "</tr>";
  
    while (!$res->EOF)
    {
        $info = $res->fields;

        switch ($info['det_type'])
        {
            case 0:
                list($sector, $owner, $planet)= explode ("\|", $info['detect_data']);
                $l_spy_datatextF = str_replace("[sector]", "<a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a>", $l_spy_datatext_1);
                $l_spy_datatextF = str_replace("[player]", "<font color=white><strong>$owner</strong></font>", $l_spy_datatextF);
                $l_spy_datatextF = str_replace("[planet]", "<font color=white><strong>$planet</strong></font>", $l_spy_datatextF);
                $detect_data=$l_spy_datatextF;
                $data_type=$l_spy_datatype_1;
            break;
      
            case 1:
                list($inf, $sender, $receiver,$type)= explode ("\>", $info['detect_data']); // I use that symbol, because a letter may include '|' symbols, but cannot include '>' symbols
                if ($type == 'alliance')
                {
                    $l_spy_datatextF = str_replace("[sender]", "<font color=white><strong>$sender</strong></font>", $l_spy_datatext[2]);
                    $l_spy_datatextF = str_replace("[receiver]", "<font color=white><strong>$receiver</strong></font>", $l_spy_datatextF);
                    $l_spy_datatextF = str_replace("[letter]", "<font color=white><strong>$inf</strong></font>", $l_spy_datatextF);
                    $detect_data = $l_spy_datatextF;
                }
                else
                {
                    $l_spy_datatextF = str_replace("[sender]", "<font color=white><strong>$sender</strong></font>", $l_spy_datatext[3]);
                    $l_spy_datatextF = str_replace("[receiver]", "<font color=white><strong>$receiver</strong></font>", $l_spy_datatextF);
                    $l_spy_datatextF = str_replace("[letter]", "<font color=white><strong>$inf</strong></font>", $l_spy_datatextF);
                    $detect_data=$l_spy_datatextF;
                }
        
                $data_type=$l_spy_datatype[2];
            break;
        }
    
        echo "<tr bgcolor=" . linecolor() ."><td><font style=\"font-size: 0.8em;\" color=white>$info[det_time]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$data_type</font></td><td><font style=\"font-size: 0.8em;\">$detect_data</font></td><td><font style=\"font-size: 0.8em;\"><a href=\"spy_detect.php&info_id=$info[det_id]&by=$by\">$l_spy_delete</a></font></td></tr>";
   
        $res->MoveNext();
    }

    echo "</table><br>";
global $l_global_mmenu;
$template->assign("title", $title);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/spy.tpl");

include_once ("./footer.php");
?>
