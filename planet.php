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
// File: planet.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "spy_detect_planet.php");
dynamic_loader ($db, "spy_planet_destroyed.php");
dynamic_loader ($db, "planetcombat.php");
dynamic_loader ($db, "planetcount_news.php");
dynamic_loader ($db, "planet_log.php");
dynamic_loader ($db, "linecolor.php");
dynamic_loader ($db, "scan_error.php");
dynamic_loader ($db, "scan_success.php");
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "calc_ownership.php");
dynamic_loader ($db, "adminlog.php");
dynamic_loader ($db, "get_player.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'admin');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'planet');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'combat');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'bounty');
load_languages($db, $raw_prefix, 'shipyard');
load_languages($db, $raw_prefix, 'spy');
load_languages($db, $raw_prefix, 'ship');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_planet_title;
updatecookie($db);

// Planet log constants
include_once ("./header.php");

// Defines for Planet log
define('PLOG_GENESIS_CREATE',1);
define('PLOG_GENESIS_DESTROY',2);
define('PLOG_CAPTURE',3);
define('PLOG_ATTACKED',4);
define('PLOG_SCANNED',5);
define('PLOG_OWNER_DEAD',6);
define('PLOG_DEFEATED',7);
define('PLOG_SOFA',8);
define('PLOG_PLANET_DESTRUCT',9);

global  $local_number_dec_point, $local_number_thousands_sep;
$planet_id = '';
if (isset($_GET['planet_id']))
{
    $planet_id = $_GET['planet_id'];
}
elseif (isset($_POST['planet_id']))
{
    $planet_id = $_POST['planet_id'];
}

$line_color = $color_line2;

// Dynamic functions
dynamic_loader ($db, "base_string.php");

//-------------------------------------------------------------------------------------------------

$result3 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id));
if ($result3)
{
    $planetinfo = $result3->fields;
}

echo "<h1>" . $title. "</h1>\n";

if (isset($_GET['command']))
{
    $command = $_GET['command'];
}
elseif (isset($_POST['command']))
{
    $command = $_POST['command'];
}
else
{
    $command = '';
}

if (!isset($destroy))
{
    $destroy = '';
}

seed_mt_rand();

if (!empty($planetinfo)) // if there is a planet in the sector show appropriate menu
{
    if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
    {
        if ($shipinfo['on_planet'] == 'Y')
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET on_planet='N' WHERE ship_id=?", array($shipinfo['ship_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }

        echo $l_planet_none . "<p>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    if (($planetinfo['owner'] == 0  || $planetinfo['defeated'] == 'Y') && $command != "capture")
    {
        if ($planetinfo['owner'] == 0)
        {
            echo $l_planet_unowned . "<br><br>";
        }

        $capture_link="<a href='planet.php?planet_id=$planet_id&amp;command=capture'>$l_planet_capture1</a>";
        $l_planet_capture2 = str_replace("[capture]",$capture_link,$l_planet_capture2);
        echo $l_planet_capture2 . "<br><br>";
        echo "<br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    if ($planetinfo['owner'] != 0)
    {
        if ($spy_success_factor)
        {
            spy_detect_planet($db,$shipinfo['ship_id'], $planetinfo['planet_id'],$planet_detect_success1);
        }

        $result3 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=?", array($planetinfo['owner']));
        $ownerinfo = $result3->fields;

        $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE player_id=? AND ship_id=?", array($planetinfo['owner'], $ownerinfo['currentship']));
        $ownershipinfo = $res->fields;
    }

    if (empty($command))
    {
        // ...if there is no planet command already
        if (empty($planetinfo['name']))
        {
            $l_planet_unnamed = str_replace("[name]",$ownerinfo['character_name'],$l_planet_unnamed);
            $l_planet_unnamed = str_replace("[sector]",$planetinfo['sector_id'],$l_planet_unnamed);
            echo $l_planet_unnamed . "<br><br>";
        }
        else
        {
            $l_planet_named = str_replace("[name]",$ownerinfo['character_name'],$l_planet_named);
            $l_planet_named = str_replace("[planetname]",$planetinfo['name'],$l_planet_named);
            $l_planet_named = str_replace("[sector]",$planetinfo['sector_id'],$l_planet_named);
            echo "$l_planet_named<br><br>";
        }

        if ($playerinfo['player_id'] == $planetinfo['owner'])
        {
            if ($destroy == 1 && $allow_genesis_destroy)
            {
                echo "<font color=red>$l_planet_confirm</font><br><a href='planet.php?planet_id=$planet_id&amp;destroy=2'>$l_yes</a><br>";
                echo "<a href='planet.php?planet_id=$planet_id'>$l_no!</a><br><br>";
            }
            elseif ($destroy == 2 && $allow_genesis_destroy)
            {
                if ($shipinfo['dev_genesis'] > 0  && $playerinfo['turns'] > 0)
                {
                    if ($spy_success_factor)
                    {
                        spy_planet_destroyed($db,$planet_id);
                    }

                    $debug_query = $db->Execute("DELETE FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns_used=turns_used+1, " .
                                                "turns=turns-1 WHERE player_id=?", array($playerinfo['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET dev_genesis=dev_genesis-1 " .
                                                "WHERE ship_id=?", array($shipinfo['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query=$db->Execute("UPDATE {$db->prefix}ships SET on_planet='N' WHERE planet_id=?", array($planet_id));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    calc_ownership($db,$shipinfo['sector_id']);

                    // No click/refresh - seems smoother.
                    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
                    {
                        $add_slash_to_url = '/';
                    }

                    $server_port = '';
                    if ($_SERVER['SERVER_PORT'] != '80')
                    {
                        $server_port = ':' . $_SERVER['SERVER_PORT'];
                    }
                    header("Location: " . $server_type . "://" . $_SERVER['SERVER_NAME'] . $server_port . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "main.php");
            }
            else
            {
                if ($shipinfo[dev_genesis] < 1)
                {
                    echo "$l_gns_nogenesis<br>";
                }

                if ($playerinfo[turns] < 1)
                {
                    echo "$l_gns_turn<br>";
                }
            }
        }
        elseif ($allow_genesis_destroy)
        {
            echo "<a onclick=\"javascript: alert ('$l_planet_warning');\" href='planet.php?planet_id=$planet_id&amp;destroy=1'>$l_planet_destroyplanet</a><br>";
        }
    }

    if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo['owner'] > 0))
    {
        // owner menu
        echo "$l_turns_have $playerinfo[turns]<p>";

        $l_planet_name_link = "<a href='planet.php?planet_id=$planet_id&amp;command=name'>" . $l_planet_name_link . "</a>";
        $l_planet_name = str_replace("[name]",$l_planet_name_link,$l_planet_name2);

        echo "$l_planet_name<br>";

        $l_planet_leave_link = "<a href='planet.php?planet_id=$planet_id&amp;command=leave'>" . $l_planet_leave_link . "</a>";
        $l_planet_leave = str_replace("[leave]",$l_planet_leave_link,$l_planet_leave);

        $l_planet_land_link = "<a href='planet.php?planet_id=$planet_id&amp;command=land'>" . $l_planet_land_link . "</a>";
        $l_planet_land = str_replace("[land]",$l_planet_land_link,$l_planet_land);

        if ($shipinfo['on_planet'] == 'Y' && $shipinfo['planet_id'] == $planet_id)
        {
            echo "$l_planet_onsurface<br>";
            echo "$l_planet_leave<br>";
            $logout_link ="<a href=logout.php>$l_planet_logout1</a>";
            $l_planet_logout2=str_replace("[logout]",$logout_link,$l_planet_logout2);
            echo "$l_planet_logout2<br>";
        }
        else
        {
            echo "$l_planet_orbit<br>";
            echo "$l_planet_land<br>";
        }

        $l_planet_transfer_link="<a href='planet.php?planet_id=$planet_id&amp;command=transfer'>" . $l_planet_transfer_link . "</a>";
        $l_planet_transfer=str_replace("[transfer]",$l_planet_transfer_link,$l_planet_transfer);
        echo "$l_planet_transfer<br>";
        if ($planetinfo['base'] == "Y" && !$ship_based_combat)
        {     
            echo "<a href='planet.php?planet_id=$planet_id&amp;command=defenses'>$l_planet_upgrade</a> " . $l_planetary_defense_levels . ".<br>";
        }

        if ($planetinfo['sells'] == "Y")
        {
            echo $l_planet_selling;
        }
        else
        {
            echo $l_planet_not_selling;
        }

        $l_planet_tsell_link="<a href='planet.php?planet_id=$planet_id&amp;command=sell'>" . $l_planet_tsell_link ."</a>";
        $l_planet_tsell=str_replace("[selling]",$l_planet_tsell_link,$l_planet_tsell);
        echo "$l_planet_tsell<br>";

        if ($planetinfo['base'] == "N")
        {
            $l_planet_bbase_link = "<a href='planet.php?planet_id=$planet_id&amp;command=base'>" . $l_planet_bbase_link . "</a>";
            $l_planet_bbase=str_replace("[build]",$l_planet_bbase_link,$l_planet_bbase);
            echo "$l_planet_bbase<br>";
        }
        else
        {
            echo "$l_planet_hasbase<br>";
        }

        if ($playerinfo['acl'] >= 128)
        {
            echo "<a href=\"admin.php\">{$l_planet_admin}</a><br>";
        }

        if ($spy_success_factor)
        {
            echo "<a href=\"spy_cleanup_planet.php&amp;planet_id=$planetinfo[planet_id]\">$l_clickme</a> $l_spy_cleanupplanet<br>";
        }

        // This is added at the request of multiple players.
        $l_planet_readlog_link = "<a href=log.php>" . $l_planet_readlog_link ."</a>";
        $l_planet_readlog = str_replace("[View]",$l_planet_readlog_link,$l_planet_readlog);
        echo "<br>$l_planet_readlog<br>";

        if ($playerinfo['player_id'] == $planetinfo['owner'])
        {
            if ($playerinfo['team'] != 0)
            {
                if ($planetinfo['team'] == 0)
                {
                    $l_planet_mteam_linkC = "<a href='planetteam.php?planet_id=$planet_id&amp;command=planetteam'>" . $l_planet_mteam_linkC . "</a>";
                    $l_planet_mteam=str_replace("[planet]",$l_planet_mteam_linkC,$l_planet_mteam);
                    echo "$l_planet_mteam<br>";
                }
                else
                {
                    $l_planet_mteam_linkP = "<a href='planetteam.php?planet_id=$planet_id&amp;command=planetpersonal'>" . $l_planet_mteam_linkP . "</a>";
                    $l_planet_mteam=str_replace("[planet]",$l_planet_mteam_linkP,$l_planet_mteam);
                    echo "$l_planet_mteam<br>";
                }
            }
        }

        // change production rates
        echo "<br>\n\n";
        echo "<form action=\"planet.php\" method=post>";
        echo "<input type=hidden name=planet_id value=$planet_id>";
        echo "<input type=hidden name=command value=productions><br>";
        if (!$ship_based_combat)
        {
            echo "<table border=0 cellspacing=0 cellpadding=2>";
            echo "<tr bgcolor=\"$color_header\"><td></td><td><strong>$l_planetary_computer</strong></td><td><strong>$l_planetary_sensors</strong></td><td><strong>$l_planetary_beams</strong></td><td><strong>$l_planetary_torp_launch</strong></td><td><strong>$l_planetary_shields</strong></td>";
    //      echo "<td><strong>armor</strong></td><td><strong>armor Points</strong></td>";
            echo "<td><strong>$l_planetary_cloak</strong></td></tr>";
            echo "<tr bgcolor=\"$color_line2\"><td>" . $l_planetary_defense_levels . "</td>";
            echo "<td>" . number_format($planetinfo['computer'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>" . number_format($planetinfo['sensors'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>" . number_format($planetinfo['beams'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>" . number_format($planetinfo['torp_launchers'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>" . number_format($planetinfo['shields'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    //      echo "<td>" . number_format($planetinfo['armor'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    //      echo "<td>" . number_format($planetinfo['armor_pts'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>" . number_format($planetinfo['cloak'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "</tr>";
            echo "</table><br><br>";
        }
        echo "<table border=0 cellspacing=0 cellpadding=2>";
        echo "<tr bgcolor=\"$color_header\"><td></td><td><strong>$l_ore</strong></td><td><strong>$l_organics</strong></td><td><strong>$l_goods</strong></td><td><strong>$l_energy</strong></td><td><strong>$l_colonists</strong></td><td><strong>$l_credits</strong></td><td><strong>$l_fighters</strong></td><td><strong>$l_torps</strong></td>";
        if ($spy_success_factor)
        {
            echo "<td><strong>$l_spy</strong></td>";
        }

        echo "</tr><tr bgcolor=\"$color_line1\">";
        echo "<td>$l_current_qty</td>";
        echo "<td>" . number_format($planetinfo['ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planetinfo['organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planetinfo['goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planetinfo['energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planetinfo['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planetinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planetinfo['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format($planetinfo['torps'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        if ($spy_success_factor)
        {
            $res = $db->execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=? AND owner_id=?", array($planet_id, $playerinfo['player_id']));
            $n = $res->RecordCount();
            echo "<td>$n</td>";
        }

        echo "</tr>";
        echo "<tr bgcolor=\"$color_line2\"><td>$l_planet_perc</td>";
        echo "<td><input type=text name=pore value=\"$planetinfo[prod_ore]\" size=3 maxlength=3></td>";
        echo "<td><input type=text name=porganics value=\"$planetinfo[prod_organics]\" size=3 maxlength=3></td>";
        echo "<td><input type=text name=pgoods value=\"$planetinfo[prod_goods]\" size=3 maxlength=3></td>";
        echo "<td><input type=text name=penergy value=\"$planetinfo[prod_energy]\" size=3 maxlength=3></td>";
        echo "<td>n/a</td><td>*</td>";
        echo "<td><input type=text name=pfighters value=\"$planetinfo[prod_fighters]\" size=3 maxlength=3></td>";
        echo "<td><input type=text name=ptorp value=\"$planetinfo[prod_torp]\" size=3 maxlength=3></td>";
        if ($spy_success_factor)
        {
            echo "<td>n/a</td>";
        }

        echo "</table>$l_planet_interest<br><br>";
        echo "<input type=submit value=$l_planet_update>";
        echo "</form>";
    }
    else
    {
        // visitor menu
        if ($planetinfo['sells'] == "Y")
        {
            $l_planet_buy_link = "<a href='planet.php?planet_id=$planet_id&amp;command=buy'>" . $l_planet_buy_link ."</a>";
            $l_planet_buy = str_replace("[buy]",$l_planet_buy_link,$l_planet_buy);
            echo "$l_planet_buy<br>";
        }
        else
        {
            echo "$l_planet_not_selling.<br>";
        }

        $debug_query = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($planetinfo['owner']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $sameteam = $debug_query->fields['team'];

        $l_planet_scn_link = "<a href='planet.php?planet_id=$planet_id&amp;command=scan'>" . $l_planet_scn_link ."</a>";
        $l_planet_scn = str_replace("[scan]",$l_planet_scn_link,$l_planet_scn);
        echo "$l_planet_scn<br>";

        if ($sameteam != $playerinfo['team'] || $playerinfo['team'] == 0)
        {
            $l_planet_att_link = "<a href='planet.php?planet_id=$planet_id&amp;command=attac'>" . $l_planet_att_link ."</a>";
            $l_planet_att = str_replace("[attack]",$l_planet_att_link,$l_planet_att);
            echo "$l_planet_att<br>";
        }

        if ($sofa_on)
        {
            echo "<a href='planet.php?planet_id=$planet_id&amp;command=bom'>$l_sofa</a><br>";
        }

        if ($spy_success_factor)
        {
            if (!isset($by))
            {
                $by = '';
            }

            if ($by == 'job_id')
            {
                $by = "job_id desc, spy_id asc";
            }
            elseif ($by == 'percent')
            {
                $by = "spy_percent desc, spy_id asc";
            }
            elseif ($by == 'move_type')
            {
                $by = "move_type asc, spy_id asc";
            }
            else
            {
                $by = "spy_id asc";
            }

            $r = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE active = 'Y' AND planet_id=? AND " .
                              "owner_id = ? ORDER BY ?", array($planet_id, $playerinfo['player_id'], $by));
            if ($numspies = $r->RecordCount())
            {            
                echo "<br><table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
                echo "<tr bgcolor=\"$color_header\"><td colspan=99 align=center><font color=white><strong>$l_spy_yourspies </font> ($numspies)";
                if ($numspies < $max_spies_per_planet)
                {
                    echo " <a href=spy_send.php&amp;planet_id=$planet_id>$l_spy_sendnew</a>";
                }

                echo "</strong></td></tr>";
                echo "<tr bgcolor=\"$color_line2\">";
                echo "<td><strong><a href=planet.php?planet_id=$planet_id>ID</a></strong></td>";
                echo "<td><strong><a href=planet.php?planet_id=$planet_id&amp;by=job_id>$l_spy_job</a></strong></td>";
                echo "<td><strong><a href=planet.php?planet_id=$planet_id&amp;by=percent>$l_spy_percent</a></strong></td>";
                echo "<td><strong><a href=planet.php?planet_id=$planet_id&amp;by=move_type>$l_spy_move</a></strong></td>";
                echo "<td><font color=white><strong>$l_spy_action</strong></font></td>";
                echo "</tr>";
        
                while (!$r->EOF)
                {
                    $spy = $r->fields;
                    if ($spy['job_id'] == 0)
                    {
                        $job = $l_spy_jobs_0;
                    }
                    else
                    {
                        $spyjobvar = "l_spy_job_" . $spy['job_id'];
                        global $$spyjobvar;
                        $new_spy_job = $$spyjobvar;

                        $job = "<a href=spy_change.php&amp;spy_id=$spy[spy_id]&amp;planet_id=$planet_id>$new_spy_job</a>";
                    }
            
                    $temp = $spy['move_type'];
                    $l_movetype = 'l_spy_moves_' . $temp;
                    $move = $$l_movetype;
           
                    if ($spy['spy_percent'] == 0)
                    {
                        $spy['spy_percent'] = "-";
                    }
                    else
                    {
                        $spy['spy_percent'] = number_format(100*$spy['spy_percent'], 5, $local_number_dec_point, $local_number_thousands_sep);
                    }
            
                    echo "<tr bgcolor=" . linecolor() ."><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_id]</font></td><td><font style=\"font-size: 0.8em;\" color=white>$job</font></td><td><font style=\"font-size: 0.8em;\" color=white>$spy[spy_percent]</font></td><td><font style=\"font-size: 0.8em;\"><a href=spy_change.php&amp;spy_id=$spy[spy_id]&amp;planet_id=$planet_id>$move</a></font></td><td><font style=\"font-size: 0.8em;\"><a href=spy_comeback.php&amp;spy_id=$spy[spy_id]&amp;planet_id=$planet_id>$l_spy_comeback</a></font></td></tr>";
                    $r->MoveNext();
                }

                echo "</table><br>";
                // Planet info, detected by a spy
                echo "<br><table border=0 cellspacing=0 cellpadding=2>";
                echo "<tr bgcolor=\"$color_header\"><td></td><td><strong>$l_base</strong></td><td><strong>$l_planetary_computer</strong></td><td><strong>$l_planetary_sensors</strong></td><td><strong>$l_planetary_beams</strong></td><td><strong>$l_planetary_torp_launch</strong></td><td><strong>$l_planetary_shields</strong></td>";
             // echo "<td><strong>armor</strong></td><td><strong>armor Points</strong></td>";
                echo "<td><strong>$l_planetary_cloak</strong></td></tr>";
                echo "<tr bgcolor=\"$color_line2\"><td>" . $l_planetary_defense_levels . "&nbsp;</td>";
                echo "<td>" . base_string($planetinfo['base'],$l_yes,$l_no) . "</td>";
                echo "<td>" . number_format($planetinfo['computer'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['sensors'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['beams'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['torp_launchers'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['shields'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            //  echo "<td>" . number_format($planetinfo['armor'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            //  echo "<td>" . number_format($planetinfo['armor_pts'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['cloak'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "</tr>";
                echo "</table><br><br>";
                echo "<table border=0 cellspacing=0 cellpadding=2>";
                echo "<tr bgcolor=\"$color_header\"><td></td><td><strong>$l_ore</strong></td><td><strong>$l_organics</strong></td><td><strong>$l_goods</strong></td><td><strong>$l_energy</strong></td><td><strong>$l_colonists</strong></td><td><strong>$l_credits</strong></td><td><strong>$l_fighters</strong></td><td><strong>$l_torps</strong></td>";
                echo "</tr><tr bgcolor=\"$color_line1\">";
                echo "<td>$l_current_qty</td>";
                echo "<td>" . number_format($planetinfo['ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "<td>" . number_format($planetinfo['torps'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
                echo "</tr>";
                echo "<tr bgcolor=\"$color_line2\"><td>$l_planet_perc</td>";
                echo "<td><input type=text value=\"$planetinfo[prod_ore]\" size=3 maxlength=3 disabled=disabled></td>";
                echo "<td><input type=text value=\"$planetinfo[prod_organics]\" size=3 maxlength=3 disabled=disabled></td>";
                echo "<td><input type=text value=\"$planetinfo[prod_goods]\" size=3 maxlength=3 disabled=disabled></td>";
                echo "<td><input type=text value=\"$planetinfo[prod_energy]\" size=3 maxlength=3 disabled=disabled></td>";
                echo "<td>n/a</td><td>*</td>";
                echo "<td><input type=text value=\"$planetinfo[prod_fighters]\" size=3 maxlength=3 disabled=disabled></td>";
                echo "<td><input type=text value=\"$planetinfo[prod_torp]\" size=3 maxlength=3 disabled=disabled></td>";
                echo "</table>$l_planet_interest<br><br>";
                // End of Planet info
            }
            else 
            {
                echo "$l_spy_nospieshere. ";
                echo "<a href=spy_send.php&amp;planet_id=$planet_id>$l_spy_sendnew</a><br>";
            }  
        }  
    }
}
elseif ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo[owner] > 0))
{
    // player owns planet and there is a command
    if ($command == "sell")
    {
        if ($planetinfo['sells'] == "Y")
        {
            // set planet to not sell
            echo "$l_planet_nownosell<br>";
            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET sells='N' WHERE planet_id=?", array($planet_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
        else
        {
            echo "$l_planet_nowsell<br>";
            $debug_query = $db->Execute ("UPDATE {$db->prefix}planets SET sells='Y' WHERE planet_id=?", array($planet_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
    elseif ($command == "name")
    {
        // name menu
        echo "<form action=\"planet.php\" method=\"post\">";
        echo "$l_planet_iname:  ";
        echo "<input type=hidden name=command value=cname>";
        echo "<input type=hidden name=planet_id value=$planet_id>";
        echo "<input type=\"text\" name=\"new_name\" size=\"20\" maxlength=\"20\" value=\"$planetinfo[name]\"><br><br>";
        echo "<input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"$l_reset\"><br><br>";
        echo "</form>";
    }
    elseif ($command == "cname")
    {
        // name2 menu
        $new_name = trim(strip_tags($_POST['new_name']));
        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET name=? WHERE planet_id=?", array($_POST['new_name'], $planet_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $new_name = stripslashes($new_name);
        echo "$l_planet_cname $new_name.";
    }
    elseif ($command == "land")
    {
        // land menu
        echo "$l_planet_landed<br><br>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET on_planet='Y', planet_id=? WHERE ship_id=?", array($planet_id, $shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
    elseif ($command == "leave")
    {
        // leave menu
        echo "$l_planet_left<br><br>";
        $destination = $sectorinfo['sector_id'];
        include ("./check_defenses.php"); //          Need to edit check_defenses a bit more, but it will cause def check when leaving planet.
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET on_planet='N' WHERE ship_id=?", array($shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
    elseif ($command == "transfer")
    {
        // transfer menu
        $free_holds = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
        $free_power = (5 * num_level($shipinfo['power'], $level_factor, $level_magnitude)) - $shipinfo['energy'];

        $l_planet_cinfo = str_replace("[cargo]",number_format($free_holds, 0, $local_number_dec_point, $local_number_thousands_sep),$l_planet_cinfo);
        $l_planet_cinfo = str_replace("[energy]",number_format($free_power, 0, $local_number_dec_point, $local_number_thousands_sep),$l_planet_cinfo);
        echo "$l_planet_cinfo<br><br>";

        echo "<form action='planet2.php' method=post>";
        echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=0>";
        echo"<tr bgcolor=\"$color_header\"><td><strong>$l_commodity</strong></td><td><strong>$l_planet</strong></td><td><strong>$l_ship</strong></td><td><strong>$l_planet_transfer_link</strong></td><td><strong>$l_planet_toplanet</strong></td><td><strong>$l_all?</strong></td></tr>";
        echo"<tr bgcolor=\"$color_line1\"><td>$l_ore</td><td>" . number_format($planetinfo['ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($shipinfo['ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_ore size=10 maxlength=50></td><td><input type=checkbox name=tpore value=-1></td><td><input type=checkbox name=allore value=-1></td></tr>";
        echo"<tr bgcolor=\"$color_line2\"><td>$l_organics</td><td>" . number_format($planetinfo['organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($shipinfo['organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_organics size=10 maxlength=50></td><td><input type=checkbox name=tporganics value=-1></td><td><input type=checkbox name=allorganics value=-1></td></tr>";
        echo"<tr bgcolor=\"$color_line1\"><td>$l_goods</td><td>" . number_format($planetinfo['goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($shipinfo['goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_goods size=10 maxlength=50></td><td><input type=checkbox name=tpgoods value=-1></td><td><input type=checkbox name=allgoods value=-1></td></tr>";
        echo"<tr bgcolor=\"$color_line2\"><td>$l_energy</td><td>" . number_format($planetinfo['energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($shipinfo['energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_energy size=10 maxlength=50></td><td><input type=checkbox name=tpenergy value=-1></td><td><input type=checkbox name=allenergy value=-1></td></tr>";
        echo"<tr bgcolor=\"$color_line1\"><td>$l_colonists</td><td>" . number_format($planetinfo['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($shipinfo['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_colonists size=10 maxlength=50></td><td><input type=checkbox name=tpcolonists value=-1></td><td><input type=checkbox name=allcolonists value=-1></td></tr>";
        echo"<tr bgcolor=\"$color_line2\"><td>$l_fighters</td><td>" . number_format($planetinfo['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($shipinfo['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_fighters size=10 maxlength=50></td><td><input type=checkbox name=tpfighters value=-1></td><td><input type=checkbox name=allfighters value=-1></td></tr>";
        echo"<tr bgcolor=\"$color_line1\"><td>$l_torps</td><td>" . number_format($planetinfo['torps'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($shipinfo['torps'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_torps size=10 maxlength=50></td><td><input type=checkbox name=tptorps value=-1></td><td><input type=checkbox name=alltorps value=-1></td></tr>";
        echo"<tr bgcolor=\"$color_line2\"><td>$l_credits</td><td>" . number_format($planetinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_credits size=10 maxlength=50></td><td><input type=checkbox name=tpcredits value=-1></td><td><input type=checkbox name=allcredits value=-1></td></tr>";
        if ($spy_success_factor)
        {
            $res = $db->execute("SELECT * FROM {$db->prefix}spies WHERE planet_id = ? AND " .
                                "owner_id = ? ", array($planet_id, $playerinfo['player_id']));
            $n_pl = $res->RecordCount();
            $res = $db->execute("SELECT * FROM {$db->prefix}spies WHERE ship_id=? AND " .
                                "owner_id=?", array($shipinfo['ship_id'], $playerinfo['player_id']));
            $n_sh = $res->RecordCount();

            echo"<tr bgcolor=\"$color_line1\"><td>$l_spy</td><td>" . number_format($n_pl, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>" . number_format($n_sh, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td><input type=text name=transfer_spies size=10 maxlength=50></td><td><input type=checkbox name=tpspies value=-1></td><td><input type=checkbox name=allspies value=-1></td></tr>";
        }

        echo "</table><br>";
        echo "<input type=submit value=$l_planet_transfer_link>&nbsp;<input type=reset value=Reset>";
        echo "<input type=hidden value=$planet_id name=planet_id>";
        echo "</form>";
    }
    elseif ($command == "defenses" && !$ship_based_combat)
    {
        if ($planetinfo['base'] == "N")
        {
            echo "You must have a base to manage the planet defense levels.";
        }
        else
        {
            // defenses menu
            echo "\n<script type=\"text/javascript\" defer=\"defer\">\n";
            echo "<!--\n";
            echo "function MakeMax(name, val)\n";
            echo "{\n";
            echo " if (document.forms[0].elements[name].value != val)\n";
            echo " {\n";
            echo "  if (val != 0)\n";
            echo "  {\n";
            echo "  document.forms[0].elements[name].value = val;\n";
            echo "  }\n";
            echo " }\n";
            echo "}\n";

            // changeDelta function //
            echo "function changeDelta(desiredvalue,currentvalue)\n";
            echo "{\n"; 
            echo "  Delta=0; DeltaCost=0;\n";
            echo "  Delta = desiredvalue - currentvalue;\n";
            echo "\n";
            echo "    while (Delta>0) \n";
            echo "    {\n";
            echo "     DeltaCost=DeltaCost + Math.pow($upgrade_factor,desiredvalue-Delta); \n";
            echo "     Delta=Delta-1;\n";
            echo "    }\n";
            echo "\n";
            echo "  DeltaCost=DeltaCost * $upgrade_cost\n";
            echo "  return DeltaCost;\n";
            echo "}\n";

            echo "function counttotal()\n";
            echo "{\n";
            echo "// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)\n";
            echo "// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.\n";
            echo "var form = document.forms[0];\n";
            echo "var i = form.elements.length;\n";
            echo "while (i > 0)\n";
            echo "  {\n";
            echo " if (form.elements[i-1].value == '')\n";
            echo "  {\n";
            echo "  form.elements[i-1].value ='0';\n";
            echo "  }\n";
            echo " i--;\n";
            echo "}\n";
            echo "// Pluses must be first, or if empty will produce a javascript error\n";
            echo "form.total_cost.value =\n";
            //  echo "+ changeDelta(form.power_upgrade.value,$planetinfo[power])\n";
            echo "changeDelta(form.computer_upgrade.value,$planetinfo[computer])\n";
            echo "+ changeDelta(form.sensors_upgrade.value,$planetinfo[sensors])\n";
            echo "+ changeDelta(form.beams_upgrade.value,$planetinfo[beams])\n";
            echo "+ changeDelta(form.cloak_upgrade.value,$planetinfo[cloak])\n";
            echo "+ changeDelta(form.torp_launchers_upgrade.value,$planetinfo[torp_launchers])\n";
            echo "+ changeDelta(form.shields_upgrade.value,$planetinfo[shields])\n";
            echo ";\n";
            echo "  if (form.total_cost.value > $playerinfo[credits])\n";
            echo "  {\n";
            //echo "    form.total_cost.value = '$l_no_credits';\n";
            //  echo "    form.total_cost.value = 'You are short '+(form.total_cost.value - $playerinfo[credits]) +' credits';\n";
            echo "    form.total_needed.value = form.total_cost.value - $playerinfo[credits];\n";
            echo "    form.total_cost.value = '$l_no_credits';\n";
            echo "  }\n";
            echo "  else\n";
            echo "  {\n";
            echo "    form.total_needed.value = '0';\n";
            echo "  }\n";
            echo "  form.total_cost.length = form.total_cost.value.length;\n";
            echo "\n";
            echo "form.computer_costper.value=changeDelta(form.computer_upgrade.value,$planetinfo[computer]);\n";
            echo "form.sensors_costper.value=changeDelta(form.sensors_upgrade.value,$planetinfo[sensors]);\n";
            echo "form.beams_costper.value=changeDelta(form.beams_upgrade.value,$planetinfo[beams]);\n";
            echo "form.cloak_costper.value=changeDelta(form.cloak_upgrade.value,$planetinfo[cloak]);\n";
            echo "form.torp_launchers_costper.value=changeDelta(form.torp_launchers_upgrade.value,$planetinfo[torp_launchers]);\n";
            echo "form.shields_costper.value=changeDelta(form.shields_upgrade.value,$planetinfo[shields]);\n";
            echo "}";
            echo "\n// -->\n";
            echo "</script>\n";
            echo "<noscript></noscript>";

            $onblur = "onblur=\"counttotal()\"";
            $onfocus =  "onfocus=\"counttotal()\"";
            $onchange =  "onchange=\"counttotal()\"";
            $onclick =  "onclick=\"counttotal()\"";
    
            // Dynamic functions
            dynamic_loader ($db, "dropdown.php");

            echo "<p>\n";
            $l_creds_to_spend = str_replace("[credits]",number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_creds_to_spend);
            echo "$l_creds_to_spend<br>\n";
            if ($allow_ibank && $planetinfo['base'] == "Y")
            {
                $igblink = "\n<a href=igb_login.php>$l_igb_term</a>";
                $l_ifyouneedmore = str_replace("[igb]",$igblink,$l_ifyouneedmore);
                echo "$l_ifyouneedmore<br>";
            }

            echo "<form action='planet2.php?planetupgrade=yes' method=post>";
            echo "<input type=hidden value=$planet_id name=planet_id>";
            echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=0>";
            echo"<tr bgcolor=\"$color_header\"><td><strong>" . $l_planetary_defense_levels . "</strong></td><td><strong>$l_cost</strong></td><td><strong>$l_current_level</strong></td><td><strong>$l_upgrade</strong></td></tr>";

            // computer
            echo "<tr bgcolor=\"$color_line1\"><td>$l_computer</td>";
            echo "<td><input type=text readonly=readonly class='portcosts1' name=computer_costper value='0' tabindex='-1' $onblur></td>";
            echo "<td>" . number_format($planetinfo['computer'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>";
            echo dropdown("computer_upgrade",$planetinfo['computer'], 54);
            echo "</td></tr>";

            // sensors
            echo "<tr bgcolor=\"$color_line2\"><td>$l_sensors</td>";
            echo "<td><input type=text readonly=readonly class='portcosts2' name=sensors_costper value='0' tabindex='-1' $onblur></td>";
            echo "<td>" . number_format($planetinfo['sensors'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>";
            echo dropdown("sensors_upgrade",$planetinfo['sensors'], 54);
            echo "</td></tr>";

            // Beams
            echo "<tr bgcolor=\"$color_line1\"><td>$l_beams</td>";
            echo "<td><input type=text readonly=readonly class='portcosts1' name=beams_costper value='0' tabindex='-1' $onblur></td>";
            echo "<td>" . number_format($planetinfo['beams'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>";
            echo dropdown("beams_upgrade",$planetinfo['beams'], 54);
            echo "</td></tr>";

            // Torp_launchers
            echo "<tr bgcolor=\"$color_line2\"><td>$l_torp_launch</td>";
            echo "<td><input type=text readonly=readonly class='portcosts2' name=torp_launchers_costper value='0' tabindex='-1' $onblur></td>";
            echo "<td>" . number_format($planetinfo['torp_launchers'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>";
            echo dropdown("torp_launchers_upgrade",$planetinfo['torp_launchers'], 54);
            echo "</td></tr>";

            // SHIELDS
            echo "<tr bgcolor=\"$color_line1\"><td>$l_shields</td>";
            echo "<td><input type=text readonly=readonly class='portcosts1' name=shields_costper value='0' tabindex='-1' $onblur></td>";
            echo "<td>" . number_format($planetinfo['shields'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>";
            echo dropdown("shields_upgrade",$planetinfo['shields'], 54);
            echo "</td></tr>";

            // CLOAKS
            echo "<tr bgcolor=\"$color_line2\"><td>$l_cloak</td>";
            echo "<td><input type=text readonly=readonly class='portcosts2' name=cloak_costper value='0' tabindex='-1' $onblur></td>";
            echo "<td>" . number_format($planetinfo['cloak'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
            echo "<td>";
            echo dropdown("cloak_upgrade",$planetinfo['cloak'], 54);
            echo "</td></tr>";
            echo "</table><br>";
            echo "    <td><input type=submit value=$l_buy $onclick></td>\n";
            echo "    <td align=right>$l_totalcost: <input type=text style=\"text-align:right\" name=total_cost size=30 value=0 $onfocus $onblur $onchange $onclick> &nbsp; ";
            echo "    Credits needed: <input type=text style=\"text-align:right\" name=total_needed size=25 value=0></td>\n";
         // echo "<input type=submit value=$l_planet_transfer_link>&nbsp;<input type=reset value=Reset>";
            echo "</form>";
        }
    }
    elseif ($command == "base")
    {
            // build a base
            if ($planetinfo['ore'] >= $base_ore && $planetinfo['organics'] >= $base_organics && $planetinfo['goods'] >= $base_goods && $planetinfo['credits'] >= $base_credits)
            {
                // Check to see if the player has less than one turn available
                // and if so return to the main menu
                if ($playerinfo['turns'] < 1)
                {
                    echo "$l_planet_basenoturn<br><br>";
                    global $l_global_mmenu;
                    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                    include_once ("./footer.php");
                    die();
                }

                // Create The Base
                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET base='Y', ore=?-?, " .
                                            "organics=?-?, " .
                                            "goods=?-?, credits=?-? " .
                                            "WHERE planet_id=?", array($planetinfo['ore'], $base_ore, $planetinfo['organics'], $base_organics, $planetinfo['goods'], $base_goods, $planetinfo['credits'], $base_credits, $planet_id));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                // Update User Turns
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, " .
                                            "turns_used=turns_used+1 WHERE player_id=?", array($playerinfo['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                // Refresh Plant Info
                $result3 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id));
                $planetinfo = $result3->fields;

                // Notify User Of Base Results
                echo "$l_planet_bbuild<br><br>";

                // Calc Ownership and Notify User Of Results
                $ownership = calc_ownership($db,$shipinfo['sector_id']);
                if (!empty($ownership))
                {
                    echo "$ownership<p>";
                }
            }
            else
            {
                $l_planet_baseinfo = str_replace("[base_credits]",$base_credits,$l_planet_baseinfo);
                $l_planet_baseinfo = str_replace("[base_ore]",$base_ore,$l_planet_baseinfo);
                $l_planet_baseinfo = str_replace("[base_organics]",$base_organics,$l_planet_baseinfo);
                $l_planet_baseinfo = str_replace("[base_goods]",$base_goods,$l_planet_baseinfo);
                echo "$l_planet_baseinfo<br><br>";
            }
        }
        elseif ($command == "productions")
        {
            // change production percentages
            $porganics = preg_replace('/[^0-9]/','',$_POST['porganics']);
            $pore = preg_replace('/[^0-9]/','',$_POST['pore']);
            $pgoods = preg_replace('/[^0-9]/','',$_POST['pgoods']);
            $penergy = preg_replace('/[^0-9]/','',$_POST['penergy']);
            $pfighters = preg_replace('/[^0-9]/','',$_POST['pfighters']);
            $ptorp = preg_replace('/[^0-9]/','',$_POST['ptorp']);
            if ($porganics < 0.0 || $pore < 0.0 || $pgoods < 0.0 || $penergy < 0.0 || $pfighters < 0.0 || $ptorp < 0.0)
            {
                echo "$l_planet_p_under<br><br>";
            }
            elseif (($porganics + $pore + $pgoods + $penergy + $pfighters + $ptorp) > 100.0)
            {
                echo "$l_planet_p_over<br><br>";
            }
            else
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET prod_ore=?, prod_organics=?, " .
                                            "prod_goods=?, prod_energy=?, prod_fighters=?, " .
                                            "prod_torp=? WHERE planet_id=?", array($pore, $porganics, $pgoods, $penergy, $pfighters, $ptorp, $planet_id));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                echo "$l_planet_p_changed<br><br>";
            }
        }
        elseif ($command == "capture" &&  $planetinfo['defeated'] == 'Y' )
        {
            echo "$l_planet_captured<br>";
            if ($spy_success_factor)
            {
                change_planet_ownership($db, $planet_id, 0, $playerinfo['player_id']);
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team=null, owner=?, " .
                                        "base='N', defeated='N' WHERE planet_id=?", array($playerinfo['player_id'], $planet_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            planetcount_news($db, $playerinfo['player_id']); // Tells us if he got a neat number of planets from the capture!

            $ownership = calc_ownership($db,$shipinfo['sector_id']);
            if (!empty($ownership))
            {
                echo "$ownership<p>";
            }

            $planetowner = $playerinfo['character_name'];
            planet_log($db, $planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_CAPTURE);
            playerlog($db,$playerinfo['player_id'], "LOG_PLANET_CAPTURED", number_format($planetinfo['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep)."|".number_format($planetinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . "|$planetowner");
        } 
        else
        {
            echo "$l_command_no<br>";
        }
    }
    else
    {
        // player doesn't own planet and there is a command
        if ($command == "buy")
        {
            if ($planetinfo['sells'] == "Y")
            {
                $ore_price = ($ore_price + $ore_delta / 4);
                $organics_price = ($organics_price + $organics_delta / 4);
                $goods_price = ($goods_price + $goods_delta / 4);
                $energy_price = ($energy_price + $energy_delta / 4);
                echo "<form action='planet3.php' method=post>";
                echo "<table>";
                echo "<tr><td>$l_commodity</td><td>$l_avail</td><td>$l_price</td><td>$l_buy</td><td>$l_cargo</td></tr>";
                echo "<tr><td>$l_ore</td><td>$planetinfo[ore]</td><td>$ore_price</td><td><input type=text name=trade_ore size=10 maxlength=50 value=0></td><td>$shipinfo[ore]</td></tr>";
                echo "<tr><td>$l_organics</td><td>$planetinfo[organics]</td><td>$organics_price</td><td><input type=text name=trade_organics size=10 maxlength=50 value=0></td><td>$shipinfo[organics]</td></tr>";
                echo "<tr><td>$l_goods</td><td>$planetinfo[goods]</td><td>$goods_price</td><td><input type=text name=trade_goods size=10 maxlength=50 value=0></td><td>$shipinfo[goods]</td></tr>";
                echo "<tr><td>$l_energy</td><td>$planetinfo[energy]</td><td>$energy_price</td><td><input type=text name=trade_energy size=10 maxlength=50 value=0></td><td>$shipinfo[energy]</td></tr>";
                echo "</table>";
                echo "<input type=hidden name=planet_id value=$planet_id>";
                echo "<input type=submit value=$l_submit><input type=reset value=$l_reset><br></form>";
            }
            else
            {
                echo "$l_planet_not_selling<br>";
            }
        }
        elseif ($command == "attac")
        {
            $debug_query = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($planetinfo['owner']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $sameteam = $debug_query->fields['team'];

            if ($sameteam == $playerinfo['team'] && $playerinfo['team'] != 0)
            {
                echo $l_planet_cantattackteam . "<br>";
            }
            else
            {
                // check to see if sure...
                if ($planetinfo['sells'] == "Y")
                {
                    $l_planet_buy_link = "<a href='planet.php?planet_id=$planet_id&amp;command=buy'>" . $l_planet_buy_link ."</a>";
                    $l_planet_buy = str_replace("[buy]",$l_planet_buy_link,$l_planet_buy);
                    echo "$l_planet_buy<br>";
                }
                else
                {
                    echo "$l_planet_not_selling<br>";
                }

                $l_planet_att_link = "<a href='planet.php?planet_id=$planet_id&amp;command=attack'>" . $l_planet_att_link ."</a>";
                $l_planet_att = str_replace("[attack]",$l_planet_att_link,$l_planet_att);
                $l_planet_scn_link = "<a href='planet.php?planet_id=$planet_id&amp;command=scan'>" . $l_planet_scn_link ."</a>";
                $l_planet_scn = str_replace("[scan]",$l_planet_scn_link,$l_planet_scn);
                echo "$l_planet_att <strong>$l_planet_att_sure</strong><br>";
                echo "$l_planet_scn<br>";

                if ($sofa_on)
                {
                    echo "<a href='planet.php?planet_id=$planet_id&amp;command=bom'>$l_sofa</a><br>";
                }
            }
        }
        elseif ($command == "attack")
        {
            $debug_query = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($playerinfo['owner']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $sameteam = $debug_query->fields['team'];

            if ($sameteam == $playerinfo['team'] && $playerinfo['team'] != 0)
            {
                echo $l_planet_cantattackteam . "<br>";
            }
            else
            {
                global $ship_based_combat;
                planetcombat();
            }
        }
        elseif ($command == "bom")
        {
            // Check to see if sure...
            if ($planetinfo[sells] == "Y" && $sofa_on)
            {
                $l_planet_buy_link = "<a href='planet.php?planet_id=$planet_id&amp;command=buy'>" . $l_planet_buy_link ."</a>";
                $l_planet_buy = str_replace("[buy]",$l_planet_buy_link,$l_planet_buy);
                echo "$l_planet_buy<br>";
            }
            else
            {
                echo "$l_planet_not_selling<br>";
            }

            $l_planet_att_link = "<a href='planet.php?planet_id=$planet_id&amp;command=attac'>" . $l_planet_att_link ."</a>";
            $l_planet_att = str_replace("[attack]",$l_planet_att_link,$l_planet_att);
            $l_planet_scn_link = "<a href='planet.php?planet_id=$planet_id&amp;command=scan'>" . $l_planet_scn_link ."</a>";
            $l_planet_scn = str_replace("[scan]",$l_planet_scn_link,$l_planet_scn);
            echo "$l_planet_att<br>";
            echo "$l_planet_scn<br>";
            echo "<a href='planet.php?planet_id=$planet_id&amp;command=bomb'>$l_sofa</a><strong>$l_planet_att_sure</strong><br>";
        }
        elseif ($command == "bomb" && $sofa_on)
        {
            // Dynamic functions
            dynamic_loader ($db, "planetbombing.php");
            planetbombing();
        }
        elseif ($command == "scan")
        {
            // scan menu
            if ($playerinfo['turns'] < 1)
            {
                echo "$l_plant_scn_turn<br><br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                include_once ("./footer.php");
                die();
            }

            planet_log($db, $planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_SCANNED);

            $playerscore = gen_score($db,$playerinfo['player_id']);
            $playerscore *= $playerscore;

            $planetscore = $planetinfo['organics'] * $organics_price + $planetinfo['ore'] * $ore_price + $planetinfo['goods'] * $goods_price +$planetinfo['energy'] * $energy_price + $planetinfo['fighters'] * $fighter_price + $planetinfo['torps'] * $torpedo_price + $planetinfo['colonists'] * $colonist_price + $planetinfo['credits'];
            $planetscore = $planetscore * $min_value_capture / 100;

            if ($playerscore > $planetscore)
            {
                echo "<br>" . $l_planet_min_value . "<br><br>";
            }

            // determine per cent chance of success in scanning target ship - based on player's sensors and opponent's planet's cloak
            $success = (10 - $planetinfo['cloak'] / 2 + $shipinfo['sensors']) * 5;
            if ($success < 5)
            {
                $success = 5;
            }

            if ($success > 95)
            {
                $success = 95;
            }

            $roll = mt_rand(1, 100);
            if ($roll > $success)
            {
                // if scan fails - inform both player and target.
                echo "$l_planet_noscan<br><br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                playerlog($db,$ownerinfo['player_id'], "LOG_PLANET_SCAN_FAIL", "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
                include_once ("./footer.php");
                die();
            }
            else
            {
                playerlog($db,$ownerinfo['player_id'], "LOG_PLANET_SCAN", "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
                // scramble results by scan error factor.
                $sc_error = scan_error($shipinfo['sensors'], $planetinfo['cloak'], $scan_error_factor);
                if (empty($planetinfo['name']))
                {
                    $planetinfo['name'] = $l_unnamed;
                }

                $l_planet_scn_report = str_replace("[name]",$planetinfo['name'],$l_planet_scn_report);
                $l_planet_scn_report = str_replace("[owner]",$ownerinfo['character_name'],$l_planet_scn_report);
                echo "$l_planet_scn_report<br><br>";
                echo "<table>";
                echo "<tr><td>$l_commodities:</td><td></td>";
                echo "<tr><td>$l_organics:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_organics = number_format(round($planetinfo['organics'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_organics</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_ore:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_ore = number_format(round($planetinfo['ore'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_ore</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_goods:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_goods = number_format(round($planetinfo['goods'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_goods</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_energy:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_energy = number_format(round($planetinfo['energy'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_energy</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_colonists:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_colonists = number_format(round($planetinfo['colonists'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_colonists</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_credits:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_credits = number_format(round($planetinfo['credits'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_credits</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }
        
                echo "<tr><td>$l_defense:</td><td></td>";
                echo "<tr><td>$l_base:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    echo "<td>" . base_string($planetinfo['base'],$l_yes,$l_no) . "</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_base $l_torps:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_base_torp = number_format(round($planetinfo['torps'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_base_torp</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_fighters:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_fighters = number_format(round($planetinfo['fighters'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_fighters</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_computer:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_planet_computer = number_format(round($planetinfo['computer'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_planet_computer</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_beams:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_beams = number_format(round($planetinfo['beams'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_beams</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_torp_launch:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_torp_launchers = number_format(round($planetinfo['torp_launchers'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_torp_launchers</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_sensors:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_sensors = number_format(round($planetinfo['sensors'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_sensors</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_cloak:</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_cloak = number_format(round($planetinfo['cloak'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_cloak</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_shields</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_shields = number_format(round($planetinfo['shields'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_shields</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_armor</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_armor = number_format(round($planetinfo['armor'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_armor</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "<tr><td>$l_armorpts</td>";

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    $sc_armor_pts = number_format(round($planetinfo['armor_pts'] * $sc_error / 100), 0, $local_number_dec_point, $local_number_thousands_sep);
                    echo "<td>$sc_armor_pts</td></tr>";
                }
                else
                {
                    echo "<td>???</td></tr>";
                }

                echo "</table><br>";

                $res = $db->Execute("SELECT {$db->prefix}ships.*, {$db->prefix}players.character_name FROM {$db->prefix}ships " .
                                    "LEFT JOIN {$db->prefix}players ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id " .
                                    "WHERE on_planet = 'Y' and planet_id=?", array($planet_id));
                while (!$res->EOF)
                {
                    $row = $res->fields;
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
                        echo "<strong>$row[character_name] $l_planet_ison</strong><br>";
                    }

                    $res->MoveNext();
                }
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, " .
                                        "turns_used=turns_used+1 WHERE player_id=?", array($playerinfo['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
        elseif ($command == "capture" &&  ($planetinfo['owner'] == 0 || $planetinfo['defeated'] == 'Y'))
        {
            echo "$l_planet_captured<br>";
            if ($spy_success_factor)
            {
                change_planet_ownership($db, $planet_id, 0, $playerinfo['player_id']);
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team=null, owner=?, base='N', " .
                                        "defeated='N' WHERE planet_id=?", array($playerinfo['player_id'], $planet_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            planetcount_news($db, $playerinfo['player_id']);
            $ownership = calc_ownership($db,$shipinfo['sector_id']);

            if (!empty($ownership))
            {
                echo "$ownership<p>";
            }

            if ($planetinfo['owner'] != 0)
            {
                gen_score($db,$planetinfo['owner']);
            }

            if ($planetinfo['owner'] != 0)
            {
                $res = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=?", array($planetinfo['owner']));
                $query = $res->fields;
                $planetowner = $query['character_name'];
                playerlog($db,$planetinfo['owner'], "LOG_PLANET_YOUR_CAPTURED","$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
            }
            else
            {
                $planetowner = $l_planet_noone;
            }  

            // DB NOT CLEANED!
            $debug_query = $db->Execute("SELECT time FROM {$db->prefix}planet_log WHERE " .
                                        "planet_id=".$planetinfo['planet_id']." AND (action=".PLOG_CAPTURE." OR " .
                                        "action=".PLOG_GENESIS_CREATE.")");
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $row = $debug_query->fields;
            $lasttime = $db->UnixTimeStamp($row['time']);

            if ($lasttime)
            {
                $curtime = TIME();
                $difftime = ($curtime - $lasttime) / 60;
                if ($difftime <= 10)
                {
                    adminlog($db, "LOG_RAW","<font color=yellow><strong>Rapid planet recapture:</strong></font><br>planet_id=<strong>".$planetinfo['planet_id']."</strong>, sector=<strong>".$shipinfo['sector_id']."</strong>, attacker: <strong>".get_player($db, $playerinfo['player_id'])."</strong>, owner: <strong>".get_player($db, $planetinfo['owner'])."</strong>. Time difference=<strong>".number_format($difftime, 1, $local_number_dec_point, $local_number_thousands_sep)."</strong> minutes. Money: <strong>".$planetinfo['credits']."</strong>, colonists: <strong>".$planetinfo['colonists']."</strong>.");
                }
            }

            planet_log($db, $planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_CAPTURE);
            playerlog($db,$playerinfo['player_id'], "LOG_PLANET_CAPTURED", number_format($planetinfo['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep)."|".number_format($planetinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep)."|$planetowner");

        }
        elseif ($command == "capture")
        {
            echo $l_planet_notdef . "<br>";
            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET defeated='N' WHERE planet_id=?", array($planetinfo['planet_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
        else
        {
            echo "$l_command_no<br>";
        }
    }
}
else
{
    echo "$l_planet_none<p>";
}

if ($command != "")
{
    echo "<br><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<br><br>";
}

if ($allow_ibank && $planetinfo['base'] == "Y")
{
    echo "$l_ifyouneedplan <a href=\"igb_login.php\">$l_igb_term</a>.<br><br>";
}

echo "<a href =\"bounty.php\">$l_by_placebounty</a><p>";

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");
?>
