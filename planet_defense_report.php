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
// File: planet_report.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "team_planet_checkboxes.php");
dynamic_loader ($db, "selling_checkboxes.php");
dynamic_loader ($db, "base_build_check.php");

// Load language variables
load_languages($db, $raw_prefix, 'planet_report');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'teams');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_pr_title;
updatecookie($db);
include_once ("./header.php");

global $db;
global $res;
global $playerinfo;
global $sort;
global $query;
global $color_header, $color, $color_line1, $color_line2;
global $l_pr_teamlink, $l_pr_clicktosort;
global $l_sector, $l_name, $l_unnamed, $l_computer, $l_sensors, $l_beams, $l_torp_launch, $l_shields, $l_cloak, $l_fighters, $l_torps, $l_base, $l_selling, $l_pr_totals, $l_yes, $l_no, $l_team;
global $l_pr_noplanet, $l_reset, $l_pr_changeprods, $l_pr_baserequired, $l_pr_takecreds, $l_pr_collectcreds, $l_pr_menulink;
global $planet_detect_success2, $spy_success_factor, $shipinfo, $title;

echo "<h1>" . $title. "</h1>\n";

echo "<strong><a href=\"planet_report_menu.php\">$l_pr_menulink</a></strong><br>" .
     "<br>" .
     "<strong><a href=\"planet_production_change.php\">$l_pr_changeprods</a></strong> &nbsp;&nbsp; $l_pr_baserequired<br>";

if ($playerinfo['team']>0)
{
    echo "<br>" .
         "<strong><a href=\"team_planets.php\">$l_pr_teamlink</a></strong><br> " .
         "<br>";
}

$query = "SELECT * FROM {$db->prefix}planets WHERE owner=$playerinfo[player_id]";

if (!empty($sort))
{
    $query .= " ORDER BY";
    if ($sort == "name")
    {
        $query .= " $sort ASC";
    }
    elseif ($sort == "computer" || $sort == "sensors" || $sort == "beams" || $sort == "torp_launchers" || $sort == "shields" || $sort == "cloak" || $sort == "base")
    {
        $query .= " $sort DESC, sector_id ASC";
    }
    else
    {
        $query .= " sector_id ASC";
    }
}
else
{
    $query .= " ORDER BY sector_id ASC";
}
 
$res = $db->Execute($query);

$i = 0;
if ($res)
{
    while(!$res->EOF)
    {
        $planet[$i] = $res->fields;
        if ($spy_success_factor)
        {
            spy_detect_planet($db,$shipinfo['ship_id'], $planet[$i]['planet_id'], $planet_detect_success2);
        }
      
        $i++;
        $res->MoveNext();
    }
}

$num_planets = $i;
if ($num_planets < 1)
{
    echo "<br>$l_pr_noplanet";
}
else
{
    $total_base = 0;

    echo "<br>";
    // ------ next block of echo's creates the header of the table
    echo "$l_pr_clicktosort<br><br>";
    echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
    echo "<tr bgcolor=\"$color_header\" valign=bottom>";
    echo "<td><strong><a href=\"planet_defense_report.php&amp;sort=sector_id\">Sector</a></strong></td>";
    echo "<td><strong><a href=\"planet_defense_report.php&amp;sort=name\">$l_name</a></strong></td>";
    echo "<td width=\"10%\"><strong><a href=\"planet_defense_report.php&amp;sort=computer\">$l_computer</a></strong></td>";
    echo "<td width=\"10%\"><strong><a href=\"planet_defense_report.php&amp;sort=sensors\">$l_sensors</a></strong></td>";
    echo "<td width=\"10%\"><strong><a href=\"planet_defense_report.php&amp;sort=beams\">$l_beams</a></strong></td>";
    echo "<td width=\"10%\"><strong><a href=\"planet_defense_report.php&amp;sort=torp_launchers\">$l_torp_launch</a></strong></td>";
    echo "<td width=\"10%\"><strong><a href=\"planet_defense_report.php&amp;sort=shields\">$l_shields</a></strong></td>";
    echo "<td width=\"10%\"><strong><a href=\"planet_defense_report.php&amp;sort=cloak\">$l_cloak</a></strong></td>";
    echo "<td width=\"10%\"><strong><a href=\"planet_defense_report.php&amp;sort=base\">$l_base</a></strong></td>";

    // ------ next block of echo's fils the table and calculates the totals of all the commoditites as well as counting the bases and selling planets
    echo "</tr>";
    $color = $color_line1;
    for ($i=0; $i<$num_planets; $i++)
    {
        if (empty($planet[$i]['name']))
        {
            $planet[$i]['name'] = $l_unnamed;
        }
        echo "<tr bgcolor=\"$color\">";
        echo "<td><a href=\"move.php?move_method=real&amp;engage=1&amp;destination=". $planet[$i]['sector_id'] . "\">". $planet[$i]['sector_id'] ."</a></td>";
        echo "<td>" . $planet[$i]['name'] . "</td>";
        echo "<td>" . number_format($planet[$i]['computer'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['sensors'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['beams'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['torp_launchers'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['shields'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['cloak'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . base_build_check($planet, $i) . "</td>";
        echo "</tr>";

        $total_base += 1;

        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }
    }

    echo "<tr bgcolor=\"$color\">";
    echo "<td colspan=9 align=center>$l_pr_totals: $total_base</td>";
    echo "</tr>";
    if ($color == $color_line1)
    {
        $color = $color_line2;
    }
    else
    {
        $color = $color_line1;
    }

    echo "</table>";
}

echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");
?>
