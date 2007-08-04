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
// File: team_defenses.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "spy_detect_planet.php"); 
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'team_planets');
load_languages($db, $raw_prefix, 'planet_report');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_teamplanet_title;
updatecookie($db);

if ($playerinfo['team'] == 0)
{
    echo "<br>$l_teamplanet_notally";
    echo "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

    include_once ("./footer.php");
    return;
}

$query = "SELECT * FROM {$db->prefix}planets WHERE team=$playerinfo[team]";

if (!empty($sort))
{
    $query .= " ORDER BY";
    if ($sort == "name")
    {
        $query .= " $sort ASC";
    }
    elseif ($sort == "computer" || $sort == "sensors" || $sort == "beams" || $sort == "torp_launchers" || $sort == "shields" || $sort == "cloak" || $sort == "owner" || $sort == "base")
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
echo "<h1>" . $title. "</h1>\n";

echo "<br>";
echo "<strong><a href=\"planet_report_menu.php\">$l_teamplanet_personal</a></strong>";
echo "<br>";
echo "<br>";

$i = 0;
if ($res)
{
    while (!$res->EOF)
    {
        $planet[$i] = $res->fields;
        if ($spy_success_factor)
        {
            spy_detect_planet($db,$shipinfo['ship_id'], $planet[$i]['planet_id'], $planet_detect_success2);
        }

        $i++;
        $res->Movenext();
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
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
    echo "<tr bgcolor=\"$color_header\" valign=\"bottom\">";
    echo "<td><strong><a href=\"team_defenses.php?sort=sector_id\">Sector</a></strong></td>";
    echo "<td><strong><a href=\"team_defenses.php?sort=name\">$l_name</a></strong></td>";
    echo "<td width=\"6%\"><strong><a href=\"team_defenses.php?sort=computer\">$l_computer</a></strong></td>";
    echo "<td width=\"6%\"><strong><a href=\"team_defenses.php?sort=sensors\">$l_sensors</a></strong></td>";
    echo "<td width=\"6%\"><strong><a href=\"team_defenses.php?sort=beams\">$l_beams</a></strong></td>";
    echo "<td width=\"6%\"><strong><a href=\"team_defenses.php?sort=torp_launchers\">$l_torp_launch</a></strong></td>";
    echo "<td width=\"6%\"><strong><a href=\"team_defenses.php?sort=shields\">$l_shields</a></strong></td>";
    echo "<td width=\"6%\"><strong><a href=\"team_defenses.php?sort=cloak\">$l_cloak</a></strong></td>";
    echo "<td width=\"6%\"><strong><a href=\"team_defenses.php?sort=base\">$l_base</a></strong></td>";
    echo "<td><strong><a href=\"team_defenses.php?sort=owner\">Owner</a></strong></td>";

    // ------ next block of echo's fils the table and calculates the totals of all the commoditites as well as counting the bases and selling planets
    echo "</tr>";
    $color = $color_line1;
    for ($i=0; $i<$num_planets; $i++)
    {
        if (empty($planet[$i]['name']))
        {
            $planet[$i]['name'] = $l_unnamed;
        }

        $owner = $planet[$i]['owner'];
        $res = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=$owner");
        $player = $res->fields['character_name'];

        echo "<tr bgcolor=\"$color\">";
        echo "<td><a href=\"move.php?engage=1&destination=". $planet[$i]['sector_id'] . "\">". $planet[$i]['sector_id'] ."</a></td>";
        echo "<td>" . $planet[$i]['name'] . "</td>";
        echo "<td>" . number_format($planet[$i]['computer'], 0, $local_number_dec_point, $local_number_thousands_sep). "</td>";
        echo "<td>" . number_format($planet[$i]['sensors'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['beams'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['torp_launchers'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['shields'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['cloak'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . ($planet[$i]['base'] == 'Y' ? "$l_yes" : "$l_no") . "</td>";
        echo "<td>" . $player . "</td>";
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
    echo "<td colspan=\"10\" align=\"center\">$l_pr_totals: $total_base</td>";
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
