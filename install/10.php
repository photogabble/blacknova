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
// File: install/10.php

// Used for file integrity check
if (!function_exists("file_get_contents"))
{
    // Dynamic functions
    dynamic_loader ($db, "file_get_contents.php");
}

echo "<h2>Testing file integrity</h2>";
$md5list = file_get_contents("md5sum_list", $use_include_path = 0);

$listpart = explode("\n", $md5list);
$k = 1;
for ($i = 0; $i < count($listpart) ; $i++)
{
    $part = $listpart[$i];
    $md5word = explode("  ", $listpart[$i]);
    for ($j = 0; $j < count($md5word) ; $j++)
    {
        $kimble[$k] = $md5word[$j];
        $k++;
    }
}

$j = 0;
for ($temp = 1; $temp< $k-1; $temp=$temp+2)  
{
    if (md5_file($kimble[$temp+1]) == $kimble[$temp])
    {
        $j++;
        $badlist[$j] = $kimble[$temp+1];
    }
}

if ($j > 0)
{
    echo "<p><font color=red>The following files DO NOT match the checksums they shipped with, and may be corrupted!</font><br>";
    echo "<font color=yellow>You may want to try redownloading them.</font></p>";
    echo '<div id="wrap" style="width:500px;">';
    echo '<div id="left" style="float:left; width:200px;">';
    for ($x=1; $x <= $j; $x=$x+2)
    {
            echo $badlist[$x];
            echo "<br>";
    }
    echo '</div>';
    echo '<div id="right" style="float:right; width:200px;">';
    for ($x=2; $x <= $j; $x=$x+2)
    {
            echo $badlist[$x];
            echo "<br>";
    }
    echo '</div>';
    echo '</div>';
    echo '<div style="clear:both;"><br>';
    echo "<p><font color=red>Continuing to install with files that do not match their checksums could";
    echo " cause bugs, errors, or even data loss.</font></p></div>";
}
else
{
    echo "<font color=lightgreen>";
    echo "<p>" . count($listpart) ." files tested and confirmed to match the checksums they shipped with.</p>";
    echo "<p>You are clear to proceed with install.</p></font>";
}

echo "<br><br>";
?>
