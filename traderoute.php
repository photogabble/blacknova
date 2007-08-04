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
// File: traderoute.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "t_port.php"); 
dynamic_loader ($db, "num_level.php"); 
dynamic_loader ($db, "traderoute_delete.php"); 
dynamic_loader ($db, "adminlog.php"); 
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'traderoute');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'teams');
load_languages($db, $raw_prefix, 'move');
load_languages($db, $raw_prefix, 'bounty');
load_languages($db, $raw_prefix, 'ports');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_tdr_title;
updatecookie($db);

include_once ("./header.php");
//-------------------------------------------------------------------------------------------------

echo "<h1>" . $title. "</h1>\n";

$result = $db->Execute("SELECT * FROM {$db->prefix}traderoutes WHERE owner=$playerinfo[player_id]");
$num_traderoutes = $result->RecordCount();

$i = 0;
while (!$result->EOF)
{
    $traderoutes[$i] = $result->fields;
    $i++;
    $result->MoveNext();
}

// Added by request and thanks to Hiliadan
$l_tdr_numroutes = str_replace("[number]", $num_traderoutes, $l_tdr_numroutes);
if ($num_traderoutes > 0)
{
    echo "<br><br>" . $l_tdr_numroutes . "<br><br>";
}

$freeholds = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
$maxholds = num_level($shipinfo['hull'], $level_factor, $level_magnitude);
$maxenergy = (5 * num_level($shipinfo['power'], $level_factor, $level_magnitude));

if ($shipinfo['colonists'] < 0 || $shipinfo['ore'] < 0 || $shipinfo['organics'] < 0 || $shipinfo['goods'] < 0 || $shipinfo['energy'] < 0 || $freeholds < 0)
{
    if ($shipinfo['colonists'] < 0 || $shipinfo['colonists'] > $maxholds)
    {
        adminlog($db, "LOG_ADMIN_ILLEGVALUE", "$playerinfo[character_name]|$shipinfo[colonists]|colonists|$maxholds");
        $shipinfo['colonists'] = 0;
    }

    if ($shipinfo['ore'] < 0 || $shipinfo['ore'] > $maxholds)
    {
        adminlog($db, "LOG_ADMIN_ILLEGVALUE", "$playerinfo[character_name]|$shipinfo[ore]|ore|$maxholds");
        $shipinfo['ore'] = 0;
    }

    if ($shipinfo['organics'] < 0 || $shipinfo['organics'] > $maxholds)
    {
        adminlog($db, "LOG_ADMIN_ILLEGVALUE", "$playerinfo[character_name]|$shipinfo[organics]|organics|$maxholds");
        $shipinfo['organics'] = 0;
    }

    if ($shipinfo['goods'] < 0 || $shipinfo['goods'] > $maxholds)
    {
        adminlog($db, "LOG_ADMIN_ILLEGVALUE", "$playerinfo[character_name]|$shipinfo[goods]|goods|$maxholds");
        $shipinfo['goods'] = 0;
    }

    if ($shipinfo['energy'] < 0 || $shipinfo['energy'] > $maxenergy)
    {
        adminlog($db, "LOG_ADMIN_ILLEGVALUE", "$playerinfo[character_name]|$shipinfo[energy]|energy|$maxenergy");
        $shipinfo['energy'] = 0;
    }

    if ($freeholds < 0)
    {
        $freeholds = 0;
    }

    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET ore=$shipinfo[ore], organics=$shipinfo[organics], goods=$shipinfo[goods], energy=$shipinfo[energy], colonists=$shipinfo[colonists] WHERE ship_id=$shipinfo[ship_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}

if (!isset($_GET['command']))
{
    $_GET['command']='';
}

if (isset($_GET['tr_repeat']))
{
    $tr_repeat = $_GET['tr_repeat'];
}
elseif (isset($_POST['tr_repeat']))
{
    $tr_repeat = $_POST['tr_repeat'];
}
else
{
    $tr_repeat = 1;
}

if (!isset($tr_repeat) || $tr_repeat <= 0)
{
    $tr_repeat = 1;
}

$tr_repeat = preg_replace('/[^0-9]/','',$tr_repeat);

if ($_GET['command'] == 'new')   // Displays new trade route form
{
    include_once ("./traderoute_new.php");
}
elseif ($_GET['command'] == 'create')    // Enters new route in db
{
    include_once ("./traderoute_create.php");
}
elseif ($_GET['command'] == 'edit')    // Displays new trade route form, edit
{
    include_once ("./traderoute_new.php");
}
elseif ($_GET['command'] == 'settings')  // Global traderoute settings form
{
    include_once ("./traderoute_settings.php");
}
elseif ($_GET['command'] == 'setsettings') // Enters settings in db
{
    include_once ("./traderoute_setsettings.php");
}
elseif (isset($_GET['engage'])) // Performs trade route
{
    dynamic_loader ($db, "traderoute_engage.php");
    for ($i = $tr_repeat; $i > 0 ; $i--)
    {
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ships WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $shipinfo = $debug_query->fields;
        traderoute_engage($i);
    }
}
else
{
    //-----------------------------------------------------------------
    if ($_GET['command'] != 'delete')
    {
        echo "<p><a href=\"traderoute.php?command=new\">" . $l_tdr_newtdr . "</a></p>";
        echo "<p><a href=\"traderoute.php?command=settings\">" . $l_tdr_modtdrset . "</a></p>";
    }
    else 
    {
        traderoute_delete();
        echo "<a href=\"traderoute.php?command=delete&amp;confirm=yes&amp;traderoute_id=" . $_GET['traderoute_id'] . "\">";
        echo $l_tdr_confdel_href . "</a> " . $l_tdr_confdel;
    }

    if ($num_traderoutes == 0)
    {
        echo "$l_tdr_noactive<p>";
    }
    else
    {
        echo "<table border=1 cellspacing=1 cellpadding=2 width=\"100%\" align=center>";
        echo "<tr bgcolor=\"" . $color_line2 . "\"><td align=\"center\" colspan=7>";

        if ($_GET['command'] != 'delete')
        {
            echo "<p><strong>$l_tdr_curtdr</strong></p>";
        }
        else
        {
            echo "<p><strong>$l_tdr_deltdr</strong></p>";
        }

        echo "</td>\n</tr>\n" .
             "<tr align=center bgcolor=\"$color_line2\">" .
             "<td><strong>$l_tdr_src</strong></td>" .
             "<td><strong>$l_tdr_srctype</strong></td>" .
             "<td><strong>$l_tdr_dest</strong></td>" .
             "<td><strong>$l_tdr_desttype</strong></td>" .
             "<td><strong>$l_tdr_move</strong></td>" .
             "<td><strong>$l_tdr_circuit</strong></td>" .
             "<td><strong>$l_tdr_change</strong></td>" .
             "</tr>";

        $i = 0;
        $curcolor = $color_line1;
        while ($i < $num_traderoutes)
        {
            echo "<tr bgcolor=\"$curcolor\">";
            if ($curcolor == $color_line1)
            {
                $curcolor = $color_line2;
            }
            else
            {
                $curcolor = $color_line1;
            }

            echo "<td>";
            if ($traderoutes[$i]['source_type'] == 'P')
            {
                echo "&nbsp;$l_tdr_portin <a href=\"move.php?move_method=real&amp;engage=1&amp;destination=" . $traderoutes[$i]['source_id'] . "\">" . $traderoutes[$i]['source_id'] . "</a></td>";
            }
            else
            {
                $result = $db->Execute("SELECT name, sector_id FROM {$db->prefix}planets WHERE planet_id=" . $traderoutes[$i]['source_id']);
                if ($result)
                {
                    $planet1 = $result->fields;
                    echo "&nbsp;$l_tdr_planet <strong>$planet1[name]</strong>$l_tdr_within<a href=\"move.php?move_method=real&amp;engage=1&amp;destination=$planet1[sector_id]\">$planet1[sector_id]</a></td>";
                }
                else
                {
                    echo "&nbsp;$l_tdr_nonexistance</td>";
                }
            }

            echo "<td align=center>";
            if ($traderoutes[$i]['source_type'] == 'P')
            {
                $result = $db->Execute("SELECT sector_id, port_type FROM {$db->prefix}ports WHERE sector_id=" . $traderoutes[$i]['source_id']);
                $port1 = $result->fields;
                echo "&nbsp;" . t_port($db, $port1['port_type']) . "</td>";
            }
            else
            {
                if (empty($planet1))
                {
                    echo "&nbsp;$l_tdr_na</td>";
                }
                else
                {
                    echo "&nbsp;$l_tdr_cargo</td>";
                }
            }

            echo "<td>";
            if ($traderoutes[$i]['dest_type'] == 'P')
            {
                echo "&nbsp;$l_tdr_portin <a href=\"move.php?move_method=real&amp;engage=1&amp;destination=" . $traderoutes[$i]['dest_id'] . "\">". $traderoutes[$i]['dest_id'] . "</a></td>";
            }
            else
            {
                $result = $db->Execute("SELECT name, sector_id FROM {$db->prefix}planets WHERE planet_id=" . $traderoutes[$i]['dest_id']);

                if ($result)
                {
                    $planet2 = $result->fields;
                    echo "&nbsp;$l_tdr_planet <strong>$planet2[name]</strong>$l_tdr_within<a href=\"move.php?move_method=real&amp;engage=1&amp;destination=$planet2[sector_id]\">$planet2[sector_id]</a></td>";
                }
                else
                {
                    echo "&nbsp;$l_tdr_nonexistance</td>";
                }
            }

            echo "<td align=center>";
            if ($traderoutes[$i]['dest_type'] == 'P')
            {
                $result = $db->Execute("SELECT sector_id, port_type FROM {$db->prefix}ports WHERE sector_id=" . $traderoutes[$i]['dest_id']);
                $port2 = $result->fields;
                echo "&nbsp;" . t_port($db, $port2['port_type']) . "</td>";
            }
            else
            {
                if (empty($planet2))
                {
                    echo "&nbsp;$l_tdr_na</td>";
                }
                else
                {
                    echo "&nbsp;";
                    if ($playerinfo['trade_colonists'] == 'N' && $playerinfo['trade_fighters'] == 'N' && $playerinfo['trade_torps'] == 'N')
                    {
                        echo $l_tdr_none;
                    }
                    else
                    {
                        if ($playerinfo['trade_colonists'] == 'Y')
                        {
                            echo $l_tdr_colonists;
                        }
        
                        if ($playerinfo['trade_fighters'] == 'Y')
                        {
                            if ($playerinfo['trade_colonists'] == 'Y')
                            {
                                echo ", ";
                            }
                            echo $l_tdr_fighters;
                        }
        
                       if ($playerinfo['trade_torps'] == 'Y')
                       {
                           echo "<br>$l_tdr_torps";
                       }
                    }
                    echo "</td>";
                }
            }
    
            echo "<td align=center>";
            if ($traderoutes[$i]['move_type'] == 'R')
            {
                echo "&nbsp;RS, ";
                if ($traderoutes[$i]['source_type'] == 'P')
                {
                    $src = $port1;
                }
                else
                {
                    $src = $planet1;
                }
    
                if ($traderoutes[$i]['dest_type'] == 'P')
                {
                    $dst = $port2;
                }
                else
                {  
                    $dst = $planet2;
                }
    
                $dist = traderoute_distance($traderoutes[$i]['source_type'], $traderoutes[$i]['dest_type'], $src['sector_id'], $dst['sector_id'], $traderoutes[$i]['circuit'], $playerinfo['trade_energy']);
    
                $l_tdr_escooped2 = $l_tdr_escooped;
                $l_tdr_escooped2 = str_replace("[tdr_dist_triptime]", $dist['triptime'], $l_tdr_escooped2);
                $l_tdr_escooped2 = str_replace("[tdr_dist_scooped]", "<br>" . $dist['scooped'], $l_tdr_escooped2);
                echo $l_tdr_escooped2;
                echo "</td>";
            }
            else
            {
                echo "&nbsp;$l_tdr_warp";
                if ($traderoutes[$i]['circuit'] == '1')
                {
                    echo ", 2 $l_tdr_turns";
                }
                else
                {
                    echo ", 4 $l_tdr_turns";
                }
    
                echo "</td>";
            }
    
            echo "<td align=center>";
            if ($traderoutes[$i]['circuit'] == '1')
            {
                echo "&nbsp;1 $l_tdr_way</td>";
            }
            else
            {
                echo "&nbsp;2 $l_tdr_ways</td>";
            }
    
            echo "<td align=center>";
            echo "<a href=\"traderoute.php?command=edit&amp;traderoute_id=" . $traderoutes[$i]['traderoute_id'] . "\">";
            echo "$l_tdr_edit</a><br><a href=\"traderoute.php?command=delete&amp;traderoute_id=" . $traderoutes[$i]['traderoute_id'] . "\">";
            echo "$l_tdr_del</a></td></tr>";
            $i++;
        }
    
        echo "</table><p>";
    }
}

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");

// Dynamic functions
dynamic_loader ($db, "traderoute_die.php");
dynamic_loader ($db, "traderoute_delete.php");
dynamic_loader ($db, "traderoute_distance.php");
dynamic_loader ($db, "traderoute_check_compatible.php");
?>
