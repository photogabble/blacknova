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
// File: planet_report_menu.php

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

global $playerinfo;
global $l_pr_teamlink, $ship_based_combat;
global $l_pr_planetstatus, $l_pr_changeprods, $l_pr_comm_disp, $l_pr_prod_disp1, $l_pr_prod_disp2, $l_pr_baserequired, $l_pr_team_disp;

echo "<h1>" . $title. "</h1>\n";

echo "<strong><a href=\"standard_report.php\" name=\"Planet Status\">$l_pr_planetstatus</a></strong><br>" .
     "$l_pr_comm_disp<br>" .
     "<br>";

if (!$ship_based_combat)
{
    echo "<strong><a href=\"planet_defense_report.php\" name=\"Planet Defense\">Planet Defenses</a></strong><br>" .
         "Display the defense levels of your planets.<br>" .
         "<br>";
}

echo "<strong><a href=\"planet_production_change.php\" name=\"Planet Status\">$l_pr_changeprods</a></strong> &nbsp;&nbsp; $l_pr_baserequired<br>" .
     $l_pr_prod_disp1 . "<br>" . $l_pr_prod_disp2 . "<br>";

if ($playerinfo['team']>0)
{
    echo "<br>" .
         "<strong><a href=team_planets.php>$l_pr_teamlink</a></strong><br> " .
         $l_pr_team_disp .
         "<br>";
    echo "<br>" .
         "<strong><a href=team_defenses.php>Show Team Defenses</a></strong><br> Show the Defense Levels of all planets on your team.<br>";
}

echo "<br><br>";

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");
?>
