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
// File: preset.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'presets');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_pre_title;
updatecookie($db);
include_once ("./header.php");

$preset_limit = 5;
echo "<h1>" . $title. "</h1>\n";
$i = 0;

// Pull the presets for the player from the db.
$debug_query = $db->Execute("SELECT * FROM {$db->prefix}presets WHERE player_id=?", array($playerinfo['player_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
while (!$debug_query->EOF)
{
    $i++;
    $presetinfo[$i] = $debug_query->fields;
    $debug_query->MoveNext();
}

if (!isset($_POST['change']))
{
    echo '<form name="bntform" action="preset.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
    $x = $preset_limit + 1;
    for ($y=1; $y<$x; $y++)
    {
        echo "Preset " . $y . ": <input type=\"text\" name=\"preset" . $y. "\" size=\"6\" maxlength=\"6\" value=\"" . $presetinfo[$y]['preset'] . "\"><br>";
    }

    echo "<input type=\"hidden\" name=\"change\" value=\"1\">";
    echo "<br><input name=submit_button type=\"submit\" value=\"$l_pre_save\"><br><br>";
    echo "</form>";
}
else
{
    $x = $preset_limit + 1;
    for ($y=1; $y<$x; $y++)
    {
        $index = "preset" . $y;
        preg_replace('/[^0-9]/','',$_POST[$index]);
        if ($_POST[$index] > $sector_max)
        {
            $l_pre_exceed = str_replace("[preset]", $y, $l_pre_exceed);
            $l_pre_exceed = str_replace("[sector_max]", $sector_max, $l_pre_exceed);
            echo $l_pre_exceed . "<br>";
        }
        elseif ($_POST[$index] != $presetinfo[$y]['preset'])
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}presets SET preset=? WHERE player_id=? AND ". 
                                        "preset_id=?", array($_POST[$index], $playerinfo['player_id'], $presetinfo[$y]['preset_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $output = str_replace("[number]", $y, $l_pre_set);
            $output = str_replace("[preset]", "<a href=\"move.php?move_method=real&engage=1&destination=$_POST[$index]\">$_POST[$index]</a>", $output);
            echo $output . "<br>";
        }
        else
        {
            // Within limits, but not different than before, so it can stay.
        }
    }
}

echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");
?>
