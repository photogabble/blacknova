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
// File: lrscan.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "get_shipclassname.php"); 
dynamic_loader ($db, "display_this_planet.php"); 
dynamic_loader ($db, "log_scan.php"); 
dynamic_loader ($db, "t_port.php"); 
dynamic_loader ($db, "scan_success.php"); 
dynamic_loader ($db, "get_player.php"); 
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'lrscan');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');

checklogin($db);
get_info($db);
checkdead($db);

if (!isset($_GET['sector']))
{
    $_GET['sector'] = '';
}

if ($_GET['sector'] == "*")
{
    $title = $l_full_title;
}
else
{
    $title = $l_lrs_title;
}

updatecookie($db);
include_once ("./header.php");

seed_mt_rand();

//-------------------------------------------------------------------------------------------------


if ($_GET['sector'] == "*")
{
    $title = $l_full_title;
    echo "<h1>" . $title. "</h1>\n";

    if (!$allow_fullscan)
    {
        echo "$l_lrs_nofull<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    if ($playerinfo['turns'] < $fullscan_cost)
    {
        $l_lrs_noturns=str_replace("[turns]",$fullscan_cost,$l_lrs_noturns);
        echo "$l_lrs_noturns<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    echo $l_lrs_used . " " . number_format($fullscan_cost, 0, $local_number_dec_point, $local_number_thousands_sep) . " " . $l_lrs_turns . " " . number_format($playerinfo['turns'] - $fullscan_cost, 0, $local_number_dec_point, $local_number_thousands_sep) . $l_lrs_left. "<br><br>";

    // deduct the appropriate number of turns
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-?, turns_used=turns_used+? WHERE player_id=?", array($fullscan_cost, $fullscan_cost, $playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // user requested a full long range scan
    $l_lrs_reach=str_replace("[sector]",$shipinfo['sector_id'],$l_lrs_reach);
    echo $l_lrs_reach . "<br><br>";

    // get sectors which can be reached from the player's current sector
    $result = $db->Execute("SELECT distinct(link_dest), link_start FROM {$db->prefix}links WHERE link_start=? ORDER BY link_dest", array($shipinfo['sector_id']));
    echo "<table border=0 cellspacing=0 cellpadding=0 width=\"100%\">\n";
    echo " <tr bgcolor=\"$color_header\">\n<td><strong>$l_sector</strong><td>\n</td><td><strong>$l_lrs_links</strong></td><td><strong>$l_lrs_ships</strong></td><td colspan=2><strong>$l_port</strong></td><td><strong>$l_planets</strong></td><td><strong>$l_mines</strong></td><td><strong>$l_fighters</strong></td><td><strong>$l_lss</strong></td></tr>";

    $color = $color_line1;
    while (!$result->EOF)
    {
        $row = $result->fields;
        // get number of sectors which can be reached from scanned sector
        $result2 = $db->Execute("SELECT * FROM {$db->prefix}links WHERE link_start=?", array($row['link_dest']));
        $num_links = $result2->RecordCount();

        // get number of ships in scanned sector
        $result2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE sector_id=? AND on_planet='N' and destroyed='N'", array($row['link_dest']));
        
        $num_ships = 0;
        while (!$result2->EOF)
        {
            $shiprow = $result2->fields;
            $success = scan_success($shipinfo['sensors'], $shiprow['cloak']);
            if ($success < 5)
            {
                $success = 5;
            }

            if ($success > 95)
            {
                $success = 95;
            }

            $roll = mt_rand(1, 100);

            if ($roll < $success)
            {
                $num_ships++;
            }
            $result2->MoveNext();
        }
        
        // get port type and discover the presence of a planet in scanned sector
        $result2 = $db->SelectLimit("SELECT port_type FROM {$db->prefix}ports WHERE sector_id=?",1,-1,array($row['link_dest']));
        $resultSDa = $db->Execute("SELECT SUM(quantity) as mines from {$db->prefix}sector_defense WHERE sector_id=? and defense_type='M'", array($row['link_dest']));
        $resultSDb = $db->Execute("SELECT SUM(quantity) as fighters from {$db->prefix}sector_defense WHERE sector_id=? and defense_type='F'", array($row['link_dest']));

        $query96 = $result2->fields;
        $defM = $resultSDa->fields;
        $defF = $resultSDb->fields;
        $port_type = $query96['port_type'];
        $has_mines = number_format($defM['mines'], 0, $local_number_dec_point, $local_number_thousands_sep);
        $has_fighters = number_format($defF['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep);
        $has_planets = 0;
        $i='';

        $result3 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE sector_id=?", array($row['link_dest']));
        while (!$result3->EOF)
        {
            $uber = 0;
            $success = 0;
            $hiding_planet[$i] = $result3->fields;

            if ($hiding_planet[$i]['owner'] == $playerinfo['player_id'])
            {
                $uber = 1;
            }

            if ($hiding_planet[$i]['team'] != 0)
            {
                if ($hiding_planet[$i]['team'] == $playerinfo['team'])
                {
                    $uber = 1;
                }
            }

            if ($shipinfo['sensors'] >= $hiding_planet[$i]['cloak'])
            {
                $uber = 1;
            }

            if ($uber == 0) // Not yet 'visible'
            {
                $success = scan_success($shipinfo['sensors'], $hiding_planet[$i]['cloak']);
                if ($success < 5)
                {
                    $success = 5;
                }
    
                if ($success > 95)
                {
                    $success = 95;
                }
    
                $roll = mt_rand(1, 100);
                if ($roll <= $success) // If able to see the planet
                {
                    $uber = 1; // Confirmed working
                }
    
             ///
                if ($uber == 0 && $spy_success_factor)  // Still not yet 'visible'
                {
                    $res_s = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=? AND owner_id=?", array($hiding_planet[$i]['planet_id'], $playerinfo['player_id']));
                    if ($res_s->RecordCount())
                    $uber = 1;
                }
            }

            if ($uber == 1)
            {
                $planets[$i] = $result3->fields;
                $has_planets++;
            }
            $i++;
            $result3->MoveNext();
        }

        if ($port_type != "none") 
        {
            $icon_alt_text = t_port($db, $port_type);
            $icon_port_type_name = $port_type . ".png";
            $image_string = "<img align=middle height=12 width=12 alt=\"$icon_alt_text\" src=\"templates/$templateset/images/$icon_port_type_name\">&nbsp;";
        } 
        else 
        {
            $image_string = "&nbsp;";
        }

        log_scan($db, $playerinfo['player_id'], $row['link_dest']);
        
        if ($num_ships < 1)
        {
            $num_ships = '';
        }

        if ($has_planets < 1)
        {
            $has_planets = '';
        }

        echo "<tr bgcolor=\"$color\"><td><a href=\"move.php?move_method=warp&amp;destination=$row[link_dest]\">$row[link_dest]</a></td><td><a href=\"lrscan.php?sector=$row[link_dest]\">$l_scan</a></td><td>$num_links</td><td>$num_ships</td><td width=12>$image_string</td><td>" . t_port($db, $port_type) . "</td><td>$has_planets</td><td>$has_mines</td><td>$has_fighters</td>";
        if ($row['link_dest'] != '1')
        {
            $resx = $db->SelectLimit("SELECT * from {$db->prefix}movement_log WHERE player_id!=? AND source=? ORDER BY time DESC",1,-1,array($playerinfo['player_id'], $row['link_dest']));
            db_op_result($db,$resx,__LINE__,__FILE__);
            $myrow = $resx->fields;
            if (!$myrow)
            {
                echo "<td>$l_none</td>";
            }
            else
            {
                if ($shipinfo['sensors'] >= $lssd_level_three)
                {
                    echo "<td>Player " . get_player($db, $myrow['player_id']) . " on board a " . get_shipclassname($db, $myrow['ship_class']) . " class ship traveled to sector " . $myrow['destination'] . "</td>";
                }
                elseif ($shipinfo['sensors'] >= $lssd_level_two)
                {
                    echo "<td>Player " . get_player($db, $myrow['player_id']) . " on board a " . get_shipclassname($db, $myrow['ship_class']) . " class ship. </td>";
                }
                else
                {
                    echo "<td>An unknown " . get_shipclassname($db, $myrow['ship_class']) . " class ship. </td>";
                }
            }
        }
        else
        {
            echo "<td>-</td>";
        }

        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }

        $result->MoveNext();

    }
    echo "</table>";

    if ($num_links == 0)
    {
        echo "$l_none.";
    }
    else
    {
        echo "<br>$l_lrs_click";
    }
}
else // user requested a single sector (standard) long range scan
{
    $planettypes[0] = $planettypes0;
    $planettypes[1] = $planettypes1;
    $planettypes[2] = $planettypes2;
    $planettypes[3] = $planettypes3;
    $planettypes[4] = $planettypes4;
    $title = $l_lrs_title;
    echo "<h1>" . $title. "</h1>";

    if ($playerinfo['turns'] < $lrscan_cost)
    {
        $l_lrs_noturns=str_replace("[turns]",$fullscan_cost,$l_lrs_noturns);
        echo "$l_lrs_noturns<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    echo $l_lrs_used . " " . number_format($lrscan_cost, 0, $local_number_dec_point, $local_number_thousands_sep) . " " . $l_lrs_turns . " " . number_format($playerinfo['turns'] - $lrscan_cost, 0, $local_number_dec_point, $local_number_thousands_sep) . " " . $l_lrs_left . "<br><br>";

    // deduct the appropriate number of turns
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-?, turns_used=turns_used+? WHERE player_id=?", array($lrscan_cost, $lrscan_cost, $playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // get scanned sector information
    $debug_query = $db->Execute("SELECT sector_name,x,y,z FROM {$db->prefix}universe WHERE sector_id=?", array($_GET['sector']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $sector_scan_info = $debug_query->fields;

    $debug_query = $db->Execute("SELECT sector_id,port_type FROM {$db->prefix}ports WHERE sector_id=?", array($_GET['sector']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $port_sector_info = $debug_query->fields;

    // get sectors which can be reached from the player's current sector
    $debug_query = $db->Execute("SELECT link_dest FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array($shipinfo['sector_id'], $_GET['sector']));
    $scan_link_check = $debug_query->RecordCount();
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    if ($scan_link_check == 0)
    {
        echo "$l_lrs_cantscan<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }
 
    log_scan($db, $playerinfo['player_id'], $_GET['sector']);

    echo "<table border=0 cellspacing=0 cellpadding=0 width=\"100%\">";
    echo "<tr bgcolor=\"$color_header\"><td><strong>$l_sector $_GET[sector]";
    if ($sector_scan_info['sector_name'] != "")
    {
        echo " ($sector_scan_info[sector_name])";
    }

    echo "</strong></td></tr>";
    echo "</table><br>";

    echo "<table border=0 cellspacing=0 cellpadding=0 width=\"100%\">";
    echo "<tr bgcolor=\"$color_line2\"><td><strong>$l_links</strong></td></tr>";
    echo "<tr><td>";

    // get sectors which can be reached through scanned sector
    $result3 = $db->Execute("SELECT link_dest FROM {$db->prefix}links WHERE link_start=? ORDER BY link_dest ASC", array($_GET['sector']));

    if ($result3 > 0)
    {
        while (!$result3->EOF)
        {
            echo $result3->fields['link_dest'];
            if ($result3->MoveNext())
            {
                echo ",";
            }
            else
            {
            }
        }
    }
    else
    {
        echo "$l_none";
    }

    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><strong>$l_ships</strong></td></tr>";
    echo "<tr><td>";

    if ($_GET['sector'] != 0)
    {
        // get ships located in the scanned sector
        $result4 = $db->Execute("SELECT {$db->prefix}players.player_id,name,character_name,cloak FROM {$db->prefix}ships " .
                                "LEFT JOIN {$db->prefix}players ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id " .
                                "WHERE sector_id=? AND on_planet='N'", array($_GET['sector']));
        if ($result4->EOF)
        {
            echo "$l_none";
        }
        else
        {
            $num_detected = 0;
            while (!$result4->EOF)
            {
                $row = $result4->fields;
                // display other ships in sector - unless they are successfully cloaked
                $success = scan_success($shipinfo['sensors'], $row['cloak']);
                if ($success < 5)
                {
                    $success = 5;
                }

                if ($success > 95)
                {
                    $success = 95;
                }

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $num_detected++;
                    echo $row['name'] . "(" . $row['character_name'] . ")<br>";
                }
                $result4->MoveNext();
            }
            if (!$num_detected)
            {
                echo "$l_none";
            }
        }
    }
    else
    {
        echo "$l_lrs_zero";
    }

    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><strong>$l_port</strong></td></tr>";
    echo "<tr><td>";
  
    if ($port_sector_info['port_type'] == "none")
    {
        echo "$l_none";
    }
    else
    {
        if ($port_sector_info['port_type'] != "none") 
        {
            $port_type = $port_sector_info['port_type'];
            $icon_alt_text = ucfirst(t_port($db, $port_type));
            $icon_port_type_name = $port_type . ".png";
            $image_string = "<img align=middle height=12 width=12 alt=\"$icon_alt_text\" src=\"templates/$templateset/images/$icon_port_type_name\">";
        }
        echo "$image_string " . t_port($db, $port_sector_info['port_type']);
    }

    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><strong>$l_planets</strong></td></tr>";
    echo "<tr><td>";

    $res = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE sector_id=?", array($port_sector_info['sector_id']));

    // since we don't know if we are going to show any planets yet,
    // just walk though them and keep track.
    // if there are none or we don't show any (cause of cloak) then
    // we do the same thing below (cause $num_shown will == 0).
    $num_shown = 0;
    $i = 0;
    while (!$res->EOF)
    {
        $uber = 0;
        $success = 0;
        $hiding_planet[$i] = $res->fields;

        if ($hiding_planet[$i]['owner'] == $playerinfo['player_id'])
        {
            $uber = 1;
        }

        if ($hiding_planet[$i]['team'] != 0)
        {
            if ($hiding_planet[$i]['team'] == $playerinfo['team'])
            {
                $uber = 1;
            }
        }

        if ($shipinfo['sensors'] >= $hiding_planet[$i]['cloak'])
        {
            $uber = 1;
        }

        if ($uber == 0) // Not yet 'visible'
        {
            $success = scan_success($shipinfo['sensors'], $hiding_planet[$i]['cloak']);
            if ($success < 5)
            {
                $success = 5;
            }
    
            if ($success > 95)
            {
                $success = 95;
            }
    
            $roll = mt_rand(1, 100);
            if ($roll <= $success) // If able to see the planet
            {
                $uber = 1; // Confirmed working
            }
    
            if ($uber == 0 && $spy_success_factor)  // Still not yet 'visible'
            {
                $res_s = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=? AND owner_id=?", array($hiding_planet[$i]['planet_id'], $playerinfo['player_id']));
                if ($res_s->RecordCount())
                {
                    $uber = 1;
                }
            }
        }
    
        global $basefontsize;
        if ($uber == 1)
        {
            $planets[$i] = $res->fields;
            if ($num_shown == 0)
            {
                // before we show the first planet, setup a sub-table
                // to make display_this_planet() code work...
                echo "<table><tr>";
            }
            $num_shown++;
            echo display_this_planet($db, $planets[$i], $planettypes, $basefontsize, $l_unowned, $l_unnamed, $colonist_limit);
        }
        $i++;
        $res->MoveNext();
    }
    if ($num_shown == 0)
    {
        echo $l_none;
    }
    else
    {
        // if we showed at least 1 planet, then finish up the sub table...
        echo "</tr></table>";
    }

    $resultSDa = $db->Execute("SELECT SUM(quantity) as mines from {$db->prefix}sector_defense WHERE sector_id=? and defense_type='M'", array($_GET['sector']));
    $resultSDb = $db->Execute("SELECT SUM(quantity) as fighters from {$db->prefix}sector_defense WHERE sector_id=? and defense_type='F'", array($_GET['sector']));
    $defM = $resultSDa->fields;
    $defF = $resultSDb->fields;

    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line1\"><td><strong>$l_mines</strong></td></tr>";
    $has_mines = number_format($defM['mines'], 0, $local_number_dec_point, $local_number_thousands_sep) ;

    echo "<tr><td>" . $has_mines;
    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><strong>$l_fighters</strong></td></tr>";
    $has_fighters = number_format($defF['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep) ;

    echo "<tr><td>" . $has_fighters;
    echo "</td></tr>";
    if ($_GET['sector'] != '1')
    {
        echo "<tr bgcolor=\"$color_line2\"><td><strong>$l_lss</strong></td></tr>";
        echo "<tr><td>";

        $resx = $db->SelectLimit("SELECT * from {$db->prefix}movement_log WHERE player_id!=? AND source=? ORDER BY time DESC",1,-1,array($playerinfo['player_id'], $_GET['sector']));
        db_op_result($db,$resx,__LINE__,__FILE__);
        $myrow = $resx->fields;
        if (!$myrow)
        {
            echo "$l_none<br><br></tr>";
        }
        else
        {
            if ($shipinfo['sensors'] >= $lssd_level_three)
            {
                echo "Player " . get_player($db, $myrow['player_id']) . " on board a " . get_shipclassname($db, $myrow['ship_class']) . " class ship traveled to sector " . $myrow['destination'] . "<br><br></td></tr></td>";
            }
            elseif ($shipinfo['sensors'] >= $lssd_level_two)
            {
                echo "Player " . get_player($db, $myrow['player_id']) . " on board a " . get_shipclassname($db, $myrow['ship_class']) . " class ship. <br><br></td></tr></td>";
            }
            else
            {
                echo "An unknown " . get_shipclassname($db, $myrow['ship_class']) . " class ship. <br><br></td></tr></td>";
            }
        }
    }
    echo "</table><br>";
    echo "<a href=\"move.php?move_method=warp&amp;destination=$_GET[sector]\">$l_clickme</a> $l_lrs_moveto $_GET[sector].";
}

//-------------------------------------------------------------------------------------------------
echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
