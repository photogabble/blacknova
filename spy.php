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
// File: spy.php

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

// Showing a summary table of all spies

    echo "<a href=\"spy_detect.php\">$l_clickme</a> $l_spy_messages<br><br>";
  
    if ($by1 == 'character_name')
    {
        $by11 = "character_name asc";
    }
    elseif ($by1 == 'ship_name')
    {
        $by11 = "ship_name asc";
    }
    elseif ($by1 == 'ship_type')
    {
        $by11 = "c_name asc";
    }
    elseif ($by1 == 'move_type')
    {
        $by11 = "move_type asc, spy_id asc";
    }
    else
    {
        $by11 = "spy_id asc";
    }

    if ($by2 == 'planet')
    {
        $by22 = "{$db->prefix}planets.name asc, {$db->prefix}planets.sector_id asc, spy_id asc";
    }
    elseif ($by2 == 'id')
    {
        $by22 = "spy_id asc";
    }
    elseif ($by2 == 'job_id')
    {
        $by22 = "job_id desc, spy_percent desc, spy_id asc";
    }
    elseif ($by2 == 'percent')
    {
        $by22 = "spy_percent desc, {$db->prefix}planets.sector_id asc, {$db->prefix}planets.name asc, spy_id asc";
    }
    elseif ($by2 == 'move_type')
    {
        $by22 = "move_type asc, {$db->prefix}planets.sector_id asc, {$db->prefix}planets.name asc, spy_id asc";
    }
    elseif ($by2 == 'owner')
    {
        $by22 = "{$db->prefix}ships.character_name asc, {$db->prefix}planets.sector_id asc, {$db->prefix}planets.name asc, spy_id asc";
    }
    else
    {
        $by22 = "{$db->prefix}planets.sector_id asc, {$db->prefix}planets.name asc, spy_id asc";
    }

    if ($by3 == 'id')
    {
        $by33 = "spy_id asc";
    }
    elseif ($by3 == 'sector')
    {
        $by33 = "{$db->prefix}planets.sector_id asc, {$db->prefix}planets.name asc, spy_id asc";
    }
    else
    {
        $by33 = "{$db->prefix}planets.name asc, {$db->prefix}planets.sector_id asc, spy_id asc";
    }

    $res = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE {$db->prefix}spies.owner_id=$playerinfo[player_id] ");
    if ($res->RecordCount())
    {
        ////1
        $line_color = $color_line2;
        $res = sql_spy_select();
        if ($res->RecordCount())
        {
            echo "<table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
            echo "<tr bgcolor=\"$color_header\"><td colspan=99 align=center><font color=white><strong>$l_spy_defaulttitle1</strong></font></td></tr>";
            echo "<tr bgcolor=\"$color_line2\">";
            echo "<td><strong><a href=\"spy.php?by2=$by2&by3=$by3\">$l_spy_codenumber</a></strong></td>";
            echo "<td><strong><a href=\"spy.php?by1=character_name&by2=$by2&by3=$by3\">$l_spy_shipowner</a></strong></td>";
            echo "<td><strong><a href=\"spy.php?by1=ship_name&by2=$by2&by3=$by3\">$l_spy_shipname</a></strong></td>";
            echo "<td><strong><a href=\"spy.php?by1=ship_type&by2=$by2&by3=$by3\">$l_spy_shiptype</a></strong></td>";
            echo "<td><font color=white><strong>$l_spy_shiplocation</strong></font></td>";         // Do not create a link here!
            echo "<td><strong><a href=\"spy.php?by1=move_type&by2=$by2&by3=$by3\">$l_spy_move</a></strong></td>";
            echo "</tr>";

            while (!$res->EOF)
            {
                $spy = $res->fields;
                if ( (time() - $spy['online'])/60 > 5 )
                {
                    $spy['sector_id'] = $l_spy_notknown;
                }
                else
                {
                    $spy['sector_id'] = "<a href=move.php?move_method=real&engage=1&destination=$spy[sector_id]>$spy[sector_id]</a>";
                }

                $temp = $spy['move_type'];
                $l_movetype = 'l_spy_moves_' . $temp;
                $move = $$l_movetype;
                echo "<tr bgcolor=" . linecolor() ."><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_id]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[character_name]</font></td><td><font style=\"font-size: 0.8em;\" color=white><a href=report.php?sid=$spy[ship_id]>$spy[ship_name]</a></font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[c_name]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[sector_id]</font></td><td><font style=\"font-size: 0.8em;\"><a href=spy_change.php&spy_id=$spy[spy_id]>$move</a></font></td></tr>";
                $res->MoveNext();
            }

            echo "</table><br><br>";
        }
        else
        {
            echo "<strong>$l_spy_no1</strong><br><br>";
        }

        ////2
        $line_color = $color_line2;
        $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}planets.name, {$db->prefix}planets.sector_id, {$db->prefix}players.character_name FROM {$db->prefix}spies INNER JOIN {$db->prefix}planets ON {$db->prefix}spies.planet_id={$db->prefix}planets.planet_id LEFT JOIN {$db->prefix}players ON {$db->prefix}players.player_id={$db->prefix}planets.owner WHERE {$db->prefix}spies.owner_id=$playerinfo[player_id] AND {$db->prefix}spies.owner_id!={$db->prefix}planets.owner ORDER BY $by22 ");
        if ($res->RecordCount())
        {
            echo "<table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
            echo "<tr bgcolor=\"$color_header\"><td colspan=99 align=center><font color=white><strong>$l_spy_defaulttitle2</strong></font></td></tr>";
            echo "<tr bgcolor=\"$color_line2\">";
            echo "<td><strong><a href=spy.php?by2=id&by1=$by1&by3=$by3>$l_spy_codenumber</a></strong></td>";
            echo "<td><strong><a href=spy.php?by2=owner&by1=$by1&by3=$by3>$l_spy_planetowner</a></strong></td>";
            echo "<td><strong><a href=spy.php?by1=$by1&by3=$by3>$l_spy_planetname</a></strong></td>";
            echo "<td><strong><a href=spy.php?by2=sector&by1=$by1&by3=$by3>$l_spy_sector</a></strong></td>";
            echo "<td><strong><a href=spy.php?by2=job_id&by1=$by1&by3=$by3>$l_spy_job</a></strong></td>";
            echo "<td><strong><a href=spy.php?by2=percent&by1=$by1&by3=$by3>$l_spy_percent</a></strong></td>";
            echo "<td><strong><a href=spy.php?by2=move_type&by1=$by1&by3=$by3>$l_spy_move</a></strong></td>";
            echo "</tr>";
    
            while (!$res->EOF)
            {
                $spy = $res->fields;

                if ($spy['job_id'] == 0)
                {
                    $job = "$l_spy_jobs_0";
                }
                else
                {
                    $spyjobvar = "l_spy_jobs_" . $spy['job_id'];
                    global $$spyjobvar;
                    $new_spy_job = $$spyjobvar;

                    $job = "<a href=spy_change.php&spy_id=$spy[spy_id]>$new_spy_job</a>";
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

                if (empty($spy['name']))
                {
                    $spy['name'] = $l_unnamed;
                }
 
                if (empty($spy['character_name']))
                {
                    $spy['character_name'] = $l_unowned;
                }
    
                echo "<tr bgcolor=" . linecolor() ."><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_id]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[character_name]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[name]</font></td><td><font style=\"font-size: 0.8em;\"><a href=move.php?move_method=real&engage=1&destination=$spy[sector_id]>$spy[sector_id]</a></font></td><td><font style=\"font-size: 0.8em;\" color=white>$job</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_percent]</font></td><td><font style=\"font-size: 0.8em;\"><a href=spy_change.php&spy_id=$spy[spy_id]>$move</a></font></td></tr>";
                $res->MoveNext();
            }
            echo "</table><br><br>";
        }
        else
        {
            echo "<strong>$l_spy_no2</strong><br><br>";
        }

        ////3
        $line_color = $color_line2;
        $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}planets.name, {$db->prefix}planets.sector_id FROM {$db->prefix}spies INNER JOIN {$db->prefix}planets ON {$db->prefix}spies.planet_id={$db->prefix}planets.planet_id WHERE {$db->prefix}spies.active='N' AND {$db->prefix}planets.owner=$playerinfo[player_id] AND {$db->prefix}spies.owner_id=$playerinfo[player_id] ORDER BY $by33 ");
        if ($res->RecordCount())
        {
            echo "<table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
            echo "<tr bgcolor=\"$color_header\"><td colspan=99 align=center><font color=white><strong>$l_spy_defaulttitle3</strong></font></td></tr>";
            echo "<tr bgcolor=\"$color_line2\">";
            echo "<td><strong><a href=spy.php?by3=id&by1=$by1&by2=$by2>$l_spy_codenumber</a></strong></td>";
            echo "<td><strong><a href=spy.php?by1=$by1&by2=$by2>$l_spy_planetname</a></strong></td>";
            echo "<td><strong><a href=spy.php?by3=sector&by1=$by1&by2=$by2>$l_spy_sector</a></strong></td>";
            echo "</tr>";
    
            while (!$res->EOF)
            {
                $spy = $res->fields;
        
                if (empty($spy['name']))
                {
                    $spy['name'] = $l_unnamed;
                }
    
                echo "<tr bgcolor=" . linecolor() ."><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_id]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[name]</font></td><td><font style=\"font-size: 0.8em;\"><a href=move.php?move_method=real&engage=1&destination=$spy[sector_id]>$spy[sector_id]</a></font></td></tr>";
                $res->MoveNext();
            }

            echo "</table><br><br>";
        }
        else
        {
            echo "<strong>$l_spy_no3</strong><br><br>";
        }

        ////4
        $line_color = $color_line2;
        $res = $db->Execute("SELECT spy_id FROM {$db->prefix}spies WHERE active='N' AND owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] AND planet_id='0' ORDER BY spy_id asc ");
        if ($res->RecordCount())
        {
            echo "<table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
            echo "<tr bgcolor=\"$color_header\"><td colspan=99 align=center><font color=white><strong>$l_spy_defaulttitle4</strong></font></td></tr>";
            echo "<tr bgcolor=\"$color_line2\">";
            echo "<td><strong><font color=white>$l_spy_codenumber</font></strong></td>";
            echo "</tr>";

            while (!$res->EOF)
            {
                $spy = $res->fields;
    
                echo "<tr bgcolor=" . linecolor() ."><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_id]</font></td></tr>";
                $res->MoveNext();
            }

            echo "</table><br>";
        }
        else
        {
            echo "<strong>$l_spy_no4</strong><br><br>";
        }
    }
    else
    {
        echo $l_spy_nospiesatall. "<br>";
    }

global $l_global_mmenu;
$smarty->assign("title", $title);
$smarty->assign("l_global_mmenu", $l_global_mmenu);
$smarty->display("$templateset/spy.tpl");

include_once ("./footer.php");
?>
