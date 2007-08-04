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
// File: galaxy2.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'shipyard');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'ship');
load_languages($db, $raw_prefix, 'galaxy2');

checklogin($db);

if (!$ksm_allowed)
{
    global $l_error_occured, $l_galaxy2_disabled;
    $title = $l_error_occured;
    echo "<h1>" . $title. "</h1>\n";
    echo $l_galaxy2_disabled;
    echo "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$title = $l_map;
updatecookie($db);
include_once ("./header.php");

echo "<h1>" . $title. "</h1>\n";
$tile['shipyard'] = "shipyard.png";
$tile['upgrades'] = "upgrades.png";
$tile['devices']  = "devices.png";
$tile['ore']      = "ore.png";
$tile['organics'] = "organics.png";
$tile['energy']   = "energy.png";
$tile['goods']    = "goods.png";
$tile['none']     = "space.png";
$tile['unknown']  = "uspace.png";
    
$text['ore']      = $l_ore;
$text['goods']    = $l_goods;
$text['organics'] = $l_organics;
$text['energy']   = $l_energy;
$text['upgrades'] = $l_upgrade_ports;
$text['devices']  = $l_device_ports;
$text['shipyard']  = $l_shipyard_title;
$text['none']     = $l_none;

global $shipinfo;
//Map pagination developed by Ibon Lopez and Brian Gustin

$range= 400; //sets range of sectors per segment.. making this variable based on sensors later, perhaps.
$max_segment = intval($sector_max / $range); // sets the max number of segments (anti-exploit).

if (isset($_POST['location'])) // if a location was input then use that to set map display
{
    $location = $_POST['location'];
}
elseif (isset($_POST['lastinput']))
{
    $location = $_POST['lastinput'];
}
elseif (isset($_POST['nextinput']))
{
    $location = $_POST['nextinput'];
}
else //set ship's  location to set map display
{
    $location = $shipinfo['sector_id'];
}
 
// checking input data for  forbidden values; happens in 3 cases: prev , next buttons (hoped for locations near 0 or sector_max), and if player uses bad input

if ( $location < 0 )
{
    $location = 0;
}
else
{
    if ( $location > $sector_max )
    {
        $location = $sector_max;
    }
}

// previous and next
echo "<table align=\"center\" border=\"0\"><tr><td>";
if ($location == 0)
{
//    echo "$l_last_range";
}
else
{
    echo "<form name=\"form_prev\" action=\"galaxy2.php\" method=\"post\" accept-charset=\"utf-8\">".
         "<input name=\"lastinput\" type=\"hidden\" value=\"".($location-$range)."\">".
         "<input name=\"last\" type=\"submit\" value=\"$l_last_range\">".
         "</form>";
}

echo "</td><td>";
if ($location == $sector_max)
{
//    echo "$l_next_range";
}
else
{
    echo "<form name=\"form_next\" action=\"galaxy2.php\" method=\"post\" accept-charset=\"utf-8\">".
         "<input name=\"nextinput\" type=\"hidden\" value=\"".($location+$range)."\">".
         "<input name=\"next\" type=\"submit\" value=\"$l_next_range\">".
         "</form>";
}

echo "</td></tr></table>";

echo "<div style=\"text-align:center;\">$l_view_this_sector";

echo "<form name=\"form_textbox\" action=\"galaxy2.php\" method=\"post\" accept-charset=\"utf-8\">".
     "<input name=\"location\" type=\"text\" size=\"5\" maxlength=\"5\">".
     "<input name=\"view\" type=\"submit\" value=\"View\">".
     "</form>";
echo "</div>";

if ( $location - intval($range / 2) < 0) // then range would take negative sectors!!adjust it
{
    $range_bottom = 0;
    $range_top = $range-1;
}
elseif ($location + intval($range / 2) > $sector_max) // same as  last, but over sector_max
{
    $range_bottom = $sector_max-$range;
    $range_top = $sector_max;
}
else // general case
{
    if ($range % 2 == 0)
    {
        $addunit = 1;
    }
    else
    {
        $addunit = 0;
    }

    $range_bottom = $location - intval($range / 2) + $addunit;
    $range_top = $location + intval($range / 2);
}

$result = $db->Execute ("SELECT sector_id, port_type FROM {$db->prefix}ports WHERE sector_id BETWEEN ? AND " .
                        "? ORDER BY sector_id ASC", array($range_bottom, $range_top));
$result2 = $db->Execute("SELECT distinct source FROM {$db->prefix}movement_log WHERE player_id = ? AND source".
                        " BETWEEN ? AND ? ORDER BY source ASC", array($playerinfo['player_id'], $range_bottom, $range_top));
$result3 = $db->Execute("SELECT distinct sector_id FROM {$db->prefix}scan_log WHERE player_id = ? AND sector_id".
                        " BETWEEN ? AND ? ORDER BY sector_id ASC", array($playerinfo['player_id'], $range_bottom, $range_top));
$result4 = $db->Execute("SELECT distinct destination FROM {$db->prefix}movement_log WHERE player_id = ? AND destination".
                        " BETWEEN ? AND ? ORDER BY destination ASC", array($playerinfo['player_id'], $range_bottom, $range_top));

echo "<table border=0 cellpadding=2 cellspacing=1 >\n";

if (!$result2->EOF) 
{
    $row2 = $result2->fields;
}

if (!$result3->EOF) 
{
    $row3 = $result3->fields;
}

if (!$result4->EOF) 
{
    $row4 = $result4->fields;
}

$ind = 0;
$sectorcount = $result->RecordCount();    
while (!$result->EOF)
{
    $row   = $result->fields;
    $break = ($ind + 1) % 25;
    if ($break == 1)
    {
        echo "<tr><td>$row[sector_id]</td>\n";
    }

    if ( (!$result2->EOF) || (!$result3->EOF) || (!$result4->EOF) )
    {
        $port= "unknown";
        $alt = "$row[sector_id] - $l_unknown";
     
        if ( (!$result2->EOF) && ($row2['source'] == $row['sector_id']) )
        {
            $port = $row['port_type'];
            $alt  = "$row[sector_id] - $text[$port]";
            $result2->Movenext();
            $row2 = $result2->fields;
        }
     
        if ( (!$result3->EOF) && ($row3['sector_id'] == $row['sector_id']) )
        {
            $port = $row['port_type'];
            $alt  = "$row[sector_id] - $text[$port]";
            $result3->Movenext();
            $row3 = $result3->fields;
        }

        if ( (!$result4->EOF) && ($row4['destination'] == $row['sector_id']) )
        {
            $port = $row['port_type'];
            $alt  = "$row[sector_id] - $text[$port]";
            $result4->Movenext();
            $row4 = $result4->fields;
        }
    }
    else
    {
        $port = "unknown";
        $alt = "$row[sector_id] -  $l_unknown";
    }

    echo "<td><a href=\"move.php?move_method=real&amp;engage=1&amp;destination=$row[sector_id]\"><img src=\"templates/$templateset/images/" . $tile[$port] . "\" alt=\"$alt\" width=\"25\" height=\"25\" /></a></td>\n";

    if ($break == 0 || $ind == $sectorcount-1)
    {
        echo "<td>$row[sector_id]</td></tr>\n";
    }

    $result->Movenext();
    $ind++;
}
    
echo "</table>\n";

// Easter egg comment - We get to say wikki-wikki-wikki again

echo "<table border=0><tr>";
echo "<td><img src=\"templates/$templateset/images/" . $tile['shipyard'] . "\" alt='shipyard' /> - $l_shipyard_title</td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['upgrades'] . "\" alt='upgrades' /> - $l_upgrade_ports</td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['devices'] . "\" alt='devices' /> - $l_device_ports</td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['ore'] . "\" alt='ore' /> - $l_ore</td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['organics'] . "\" alt='organics' /> - $l_organics</td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['energy'] . "\" alt='energy' /> - $l_energy</td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['goods'] . "\" alt='goods' /> - $l_goods</td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['none'] . "\" alt='none' /> - $l_none<br></td>\n";
echo "<td><img src=\"templates/$templateset/images/" . $tile['unknown'] . "\" alt='unknown' /> - $l_unknown</td>\n";
echo "</tr></table>";

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
