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
// File: planetteam.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "planetcount_news.php");
dynamic_loader ($db, "calc_ownership.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'team');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_teamm_title;
updatecookie($db);

if (!isset($planet_id))
{
    $planet_id = '';
}

if (!isset($planetinfo))
{
    $planetinfo = '';
}

$planet_id = preg_replace('/[^0-9]/','',$planet_id);

$result2 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id));
if ($result2)
{
    $planetinfo=$result2->fields;
}

if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0))
{
    echo "<h1>" . $title. "</h1>\n";
    if ($command == "planetteam")
    {
        echo ("$l_teamm_toteam<br>");
        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team=?, owner=? " .
                                    "WHERE planet_id=?", array($playerinfo['team'], $playerinfo['player_id'], $planet_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $ownership = calc_ownership($db,$shipinfo['sector_id']);
        planetcount_news($db, $playerinfo['player_id']);

        if (!empty($ownership))
        {
            echo "<p>$ownership<p>";
        }
    }
    if ($command == "planetpersonal" && $planetinfo['team'] == $playerinfo['team'])
    {
        echo ("$l_teamm_topersonal<br>");
        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team='0', owner=? WHERE " .
                                    "planet_id=?", array($playerinfo['player_id'], $planet_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $ownership = calc_ownership($db,$shipinfo['sector_id']);
        planetcount_news($db, $playerinfo['player_id']);

        // Kick other players off the planet
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET on_planet='N' WHERE on_planet='Y' AND planet_id=? " .
                                    "AND player_id!=?", array($planet_id, $playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        if (!empty($ownership))
        {
            echo "<p>$ownership<p>";
        }

    }

    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
}
else
{
    echo ("<br>$l_teamm_exploit<br>");
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
}

include_once ("./footer.php");
?>
