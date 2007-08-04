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
// File: defense_report.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "load_languages.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'planet_report');
load_languages($db, $raw_prefix, 'defense_report');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'device');
load_languages($db, $raw_prefix, 'modify_defenses');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_sdf_title;
include_once ("./header.php");
updatecookie($db);

$query = "SELECT * FROM {$db->prefix}sector_defense WHERE player_id=$playerinfo[player_id]";

if (!empty($sort))
{
    $query .= " ORDER BY";
    if ($sort == "quantity")
    {
        $query .= " quantity ASC";
    }
    elseif ($sort == "mode")
    {
        $query .= " defense_type ASC, fm_setting ASC";
    }
    elseif ($sort == "type")
    {
        $query .= " defense_type ASC";
   }
   else
   {
       $query .= " sector_id ASC";
   }
}

$res = $db->Execute($query);

echo "<h1>" . $title. "</h1>\n";

$i = 0;
if ($res)
{
    while (!$res->EOF)
    {
        $sector[$i] = $res->fields;
        $i++;
        $res->MoveNext();
    }
}

// Easter egg comment - I saw a werewolf drinking a Pina Colada at Trader Vics - and his hair was PERFECT!

$num_sectors = $i;
if ($num_sectors < 1)
{
    echo "<br>$l_sdf_none";
}
else
{
    echo "$l_pr_clicktosort<br><br>";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
    echo "<tr bgcolor=\"$color_header\">";
    echo "<td><strong><a href=\"defense_report.php?sort=sector\">$l_sector</a></strong></td>";
    echo "<td><strong><a href=\"defense_report.php?sort=quantity\">$l_qty</a></strong></td>";
    echo "<td><strong><a href=\"defense_report.php?sort=type\">$l_sdf_type</a></strong></td>";
    echo "<td><strong><a href=\"defense_report.php?sort=mode\">$l_sdf_mode</a></strong></td>";
    echo "</tr>";
    $color = $color_line1;
    for ($i=0; $i<$num_sectors; $i++)
    {
        echo "<tr bgcolor=\"$color\">";
        echo "<td><a href=\"move.php?move_method=real&amp;engage=1&amp;destination=". $sector[$i]['sector_id'] . "\">". $sector[$i]['sector_id'] ."</a></td>";
        echo "<td>" . number_format($sector[$i]['quantity'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        $defense_type = $sector[$i]['defense_type'] == 'F' ? $l_fighters : $l_mines;
        echo "<td> ". $defense_type ." </td>";
        $mode = $sector[$i]['defense_type'] == 'F' ? $sector[$i]['fm_setting'] : "-";
        if ($mode == 'attack')
        {
            $mode = $l_md_attack;
        }
        elseif ($mode == 'toll')
        {
            $mode = $l_md_toll;
        }

        echo "<td> $mode </td>";
        echo "</tr>";

        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }
    }

    echo "</table>";
}
echo "<br><br>";

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");

?>
