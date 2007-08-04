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
global $l_sector, $l_name, $l_unnamed, $l_ore, $l_organics, $l_goods;
global $l_energy, $l_colonists, $l_credits, $l_fighters, $l_torps, $l_base;
global $l_selling, $l_pr_totals, $l_yes, $l_no, $l_team;
global $l_pr_noplanet, $l_reset, $l_pr_changeprods, $l_pr_baserequired; 
global $l_pr_takecreds, $l_pr_collectcreds, $l_pr_warning1, $l_pr_warning2;
global $l_pr_menulink;
global $planet_detect_success2, $spy_success_factor, $shipinfo, $title;

echo "<h1>" . $title. "</h1>\n";

echo "<strong><a href=\"planet_report_menu.php\">$l_pr_menulink</a></strong><br>" .
     "<br>" .
     "<strong><a href=\"planet_production_change.php\">$l_pr_changeprods</a></strong> &nbsp;&nbsp; $l_pr_baserequired<br>";

if ($playerinfo['team']>0)
{
    echo "<br>" .
         "<strong><a href=team_planets.php>$l_pr_teamlink</a></strong><br> " .
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
    elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" || $sort == "colonists" || $sort == "credits" || $sort == "fighters")
    {
        $query .= " $sort DESC, sector_id ASC";
    }
    elseif ($sort == "torp")
    {
        $query .= " torps DESC, sector_id ASC";
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
    while (!$res->EOF)
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
    echo "<br>";
    echo '<form name="bntform" action="planet_report_ce.php" method="post" accept-charset="utf-8" onsubmit="document.bntform.submit_button.disabled=true;">';

    // ------ next block of echo's creates the header of the table
    echo "$l_pr_clicktosort<br><br>";
    echo "<strong>" . $l_pr_warning1 . "</strong>" . $l_pr_warning2 . "<br><br>";
    echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
    echo "<tr bgcolor=\"$color_header\" valign=bottom>";
    echo "<td><strong><a href=\"standard_report.php&amp;sort=sector_id\">$l_sector</a></strong></td>";
    echo "<td><strong><a href=\"standard_report.php&amp;sort=name\">$l_name</a></strong></td>";
    echo "<td><strong><a href=\"standard_report.php&amp;sort=ore\">$l_ore</a></strong></td>";
    echo "<td><strong><a href=\"standard_report.php&amp;sort=organics\">$l_organics</a></strong></td>";
    echo "<td><strong><a href=\"standard_report.php&amp;sort=goods\">$l_goods</a></strong></td>";
    echo "<td><strong><a href=\"standard_report.php&amp;sort=energy\">$l_energy</a></strong></td>";
    echo "<td align=center><strong><a href=\"standard_report.php&amp;sort=colonists\">$l_colonists</a></strong></td>";
    echo "<td align=center><strong><a href=\"standard_report.php&amp;sort=credits\">$l_credits</a></strong></td>";
    echo "<td align=center><strong>$l_pr_takecreds?</strong></td>";
    echo "<td align=center><strong><a href=\"standard_report.php&amp;sort=fighters\">$l_fighters</a></strong></td>";
    echo "<td align=center><strong><a href=\"standard_report.php&amp;sort=torp\">$l_torps</a></strong></td>";
    echo "<td align=right><strong>$l_base?</strong></td>";
    if ($playerinfo['team'] > 0)
    {
        echo "<td align=right><strong>$l_team?</strong></td>";
    }
    echo "<td align=right><strong>$l_selling?</strong></td>";

    // ------ next block of echo's fils the table and calculates the totals of all the commoditites as well as counting the bases and selling planets
    echo "</tr>";
    $total_organics = 0;
    $total_ore = 0;
    $total_goods = 0;
    $total_energy = 0;
    $total_colonists = 0;
    $total_credits = 0;
    $total_fighters = 0;
    $total_torp = 0;
    $total_base = 0;
    $total_team = 0;
    $total_selling = 0;
    $color = $color_line1;
    for ($i=0; $i<$num_planets; $i++)
    {
        $total_organics += $planet[$i]['organics'];
        $total_ore += $planet[$i]['ore'];
        $total_goods += $planet[$i]['goods'];
        $total_energy += $planet[$i]['energy'];
        $total_colonists += $planet[$i]['colonists'];
        $total_credits += $planet[$i]['credits'];
        $total_fighters += $planet[$i]['fighters'];
        $total_torp += $planet[$i]['torps'];
        if ($planet[$i]['base'] == "Y")
        {
            $total_base += 1;
        }

        if ($planet[$i]['team'] > 0)
        {
            $total_team += 1;
        }

        if ($planet[$i]['sells'] == "Y")
        {
            $total_selling += 1;
        }

        if (empty($planet[$i]['name']))
        {
            $planet[$i]['name'] = $l_unnamed;
        }

        echo "<tr bgcolor=\"$color\">";
        echo "<td><a href=\"move.php?move_method=real&amp;engage=1&amp;destination=". $planet[$i]['sector_id'] . "\">". $planet[$i]['sector_id'] ."</a></td>";
        echo "<td>" . $planet[$i]['name'] . "</td>";
        echo "<td>" . number_format($planet[$i]['ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planet[$i]['energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td align=right>" . number_format($planet[$i]['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td align=right>" . number_format($planet[$i]['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td align=center>" . "<input type=\"checkbox\" name=\"TPCreds[]\" value=\"" . $planet[$i]['planet_id'] . "\">" . "</td>";
        echo "<td align=right>"  . number_format($planet[$i]['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td align=right>"  . number_format($planet[$i]['torps'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td align=center>" . base_build_check($planet, $i) . "</td>";
        if ($playerinfo['team'] > 0)
        {
            echo "<td align=center>" . ($planet[$i]['team'] > 0  ? "$l_yes" : "$l_no") . "</td>";
        }

        echo "<td align=center>" . ($planet[$i]['sells'] == 'Y' ? "$l_yes" : "$l_no") . "</td>";
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

    // the next block displays the totals
    echo "<tr bgcolor=\"$color\">";
    echo "<td colspan=2 align=center>$l_pr_totals</td>";
    echo "<td>" . number_format($total_ore, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format($total_organics, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format($total_goods, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format($total_energy, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td align=right>" . number_format($total_colonists, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td align=right>" . number_format($total_credits, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td></td>";
    echo "<td align=right>"  . number_format($total_fighters, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td align=right>"  . number_format($total_torp, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td align=center>" . number_format($total_base, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    if ($playerinfo['team'] > 0)
    {
        echo "<td align=center>" . number_format($total_team, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    }

    echo "<td align=center>" . number_format($total_selling, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "</tr>";
    echo "</table>";

    echo "<script type=\"text/javascript\" defer=\"defer\">";
    echo "function checkAll(elm, name)";
    echo "    {";
    echo "    for (var i = 0; i < elm.form.elements.length; i++)";
    echo "    {";
    echo "        if (elm.form.elements[i].name.indexOf(name) == 0)";
    echo "        {";
    echo "            elm.form.elements[i].checked = elm.checked;";
    echo "        }";
    echo "    }";
    echo "}";
    echo "</script> .";
    echo "<br> <input type=checkbox onClick=\"checkAll(this,'TPCreds')\"> Select All <br>. "; 
    echo '<input type="submit" name="submit_button" value="$l_pr_collectcreds">  <input type="reset" value="$l_reset">';
    echo "</form>";
}

echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");
?>
