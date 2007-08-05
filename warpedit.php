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
// File: warpedit.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'warpedit1');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_warp_title;
updatecookie($db);
include_once ("./header.php");

echo "<h1>" . $title. "</h1>\n";

if ($playerinfo['turns'] < 1)
{
    echo "$l_warp_turn<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($shipinfo['dev_warpedit'] < 1)
{
    echo "$l_warp_none<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($zoneinfo['allow_warpedit'] == 'N')
{
    echo "$l_warp_forbid<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($zoneinfo['allow_warpedit'] == 'L')
{
    $result5 = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($zoneinfo['owner']));
    $zoneteam = $result5->fields;

    if ($zoneinfo[owner] != $playerinfo[player_id])
    {
        if (($zoneteam[team] != $playerinfo[team]) || ($playerinfo[team] == 0))
        {
            echo "$l_warp_forbid<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }
    }
}

$result2 = $db->Execute("SELECT * FROM {$db->prefix}links WHERE link_start=? ORDER BY link_dest ASC", array($shipinfo['sector_id']));
if ($result2 < 1)
{
    echo "$l_warp_nolink<br><br>";
}
else
{
    echo "$l_warp_linkto ";
    while (!$result2->EOF)
    {
        echo $result2->fields['link_dest'] . " ";
        $result2->MoveNext();
    }

    echo "<br><br>";
}

echo "<form action=\"warpedit2.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>$l_warp_query</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>$l_warp_oneway?</td><td><input type=\"checkbox\" name=\"oneway\" value=\"oneway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"$l_reset\">";
echo "</form>";
echo "<br><br>$l_warp_dest<br><br>";
echo "<form action=\"warpedit3.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>$l_warp_destquery</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>$l_warp_bothway?</td><td><input type=\"checkbox\" name=\"bothway\" value=\"bothway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"$l_reset\">";
echo "</form>";

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
