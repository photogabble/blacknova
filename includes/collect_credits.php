<?php
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
// File: inclues/collect_credits.php

function collect_credits($planetarray)
{
    global $db, $l_pr_notenoughturns, $l_pr_planetstatus;
    global $spy_success_factor, $planet_detect_success1, $shipinfo;

    $CS = "GO"; // Current State

    // create an array of sector -> planet pairs
    $max = count($planetarray);
    if ($max > 0)
    {
        for ($i = 0; $i < $max; $i++)
        {
            $res = $db->SelectLimit("SELECT * FROM {$db->prefix}planets WHERE planet_id=?",1,-1, array($planetarray[$i]));
            $s_p_pair[$i]= array($res->fields['sector_id'], $planetarray[$i]);
        }
    }

    // Sort the array so that it is in order of sectors, lowest number first, not closest
    sort($s_p_pair);
    reset($s_p_pair);

    // run through the list of sector planet pairs realspace moving to each sector and then performing the transfer.
    // Based on the way realspace works we don't need a sub loop -- might add a subloop to clean things up later.

    $temp_count = count($planetarray);
    for ($i=0; ($i < $temp_count && $CS == "GO"); $i++)
    {
        echo "<br>";

        $CS = real_space_move($s_p_pair[$i][0]);

        if ($CS == "HOSTILE")
        {
            $CS = "GO";
        }
        else if ($CS == "GO")
        {
            $CS = take_credits($s_p_pair[$i][0], $s_p_pair[$i][1]);
            if ($spy_success_factor)
            {
                spy_detect_planet($db,$shipinfo['ship_id'], $s_p_pair[$i][1], $planet_detect_success1);
            }
        }
        else
        {
            echo "<br> $l_pr_notenoughturns<br>";
        }

        echo "<br>";
    }

    if ($CS != "GO" && $CS != "HOSTILE")
    {
        echo "<br>$l_pr_notenoughturns<br>";
    }

    echo "<br>";
    echo "<a href=standard_report.php>$l_pr_planetstatus</a><br>";

echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
}
?>
