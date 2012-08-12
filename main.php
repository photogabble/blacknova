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
// File: main.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "player_insignia_name.php");
dynamic_loader ($db, "scan_success.php");
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "calc_ownership.php");
dynamic_loader ($db, "get_player.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'admin');
load_languages($db, $raw_prefix, 'login');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'modify_defenses');
load_languages($db, $raw_prefix, 'spy');

// Define global variables (BAD!)
global $local_number_dec_point, $local_number_thousands_sep;

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_main_title;
include_once './header.php';
updatecookie($db);

if (!isset($_GET['command']))
{
    $_GET['command'] = '';
}

if ($score_link)
{
    if ($_GET['command'] == "score")
    {
        $current_score = gen_score($db,$playerinfo['player_id']);

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}players WHERE player_id=?",1,-1,array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $playerinfo = $debug_query->fields;
    }
    $template->assign("score_link", $score_link);
}

//-------------------------------------------------------------------------------------------------

if ($shipinfo['cleared_defenses'] > ' ')
{
    $template->assign("l_incompletemove", $l_incompletemove);
    $template->assign("l_clicktocontinue", $l_clicktocontinue);
    $template->assign("shipinfo_cleared_defenses", $shipinfo['cleared_defenses']);
    $template->display("$templateset/main-def.tpl");

    include_once './footer.php';
    die();
}

$planetgone = FALSE;
if ($shipinfo['on_planet'] == "Y")
{
    $res2 = $db->Execute("SELECT planet_id, owner FROM {$db->prefix}planets WHERE planet_id=?", array($shipinfo['planet_id']));
    if ($res2->RecordCount() != 0)
    {
        if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
        {
            $add_slash_to_url = '/';
        }

        $server_port = '';
        if ($_SERVER['SERVER_PORT'] != '80')
        {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }
        // No click/refresh - seems smoother.
        header("Location: " . $server_type . "://" . $_SERVER['SERVER_NAME'] . $server_port . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "planet.php?planet_id=$shipinfo[planet_id]&amp;id=" . $playerinfo['player_id']);
        die();
    }
    else
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET on_planet='N' WHERE player_id=? AND ship_id=?", array($playerinfo['player_id'], $playerinfo['currentship']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $planetgone = TRUE;
    }
}

$res = $db->Execute("SELECT DISTINCT link_dest FROM {$db->prefix}links WHERE link_start=? ORDER BY link_dest ASC", array($shipinfo['sector_id']));

$i = 0;
if (is_object($res))
{
    while (!$res->EOF)
    {
        $links[$i]['dest'] = $res->fields['link_dest'];
        $tempvar = $links[$i]['dest'];
        $rez = $db->SelectLimit("SELECT * FROM {$db->prefix}links WHERE link_start=? AND " .
                                "link_dest=?",1,-1,array($tempvar, $shipinfo['sector_id']));
        if (!$rez->EOF)
        {
            $links[$i]['ways'] = '='; // Two-way link because it found one
        }
        else
        {
            $links[$i]['ways'] = '-'; // One-way link
        }

        $rez2 = $db->SelectLimit("SELECT sector_id FROM {$db->prefix}scan_log WHERE sector_id=? AND player_id =?",1,-1,array($links[$i]['dest'], $playerinfo['player_id']));
        $rez3 = $db->SelectLimit("SELECT source FROM {$db->prefix}movement_log WHERE source=? AND player_id =?",1,-1, array($links[$i]['dest'], $playerinfo['player_id']));
        $rez4 = $db->SelectLimit("SELECT destination FROM {$db->prefix}movement_log WHERE destination=? AND player_id =?",1,-1,array($links[$i]['dest'], $playerinfo['player_id']));

        if (!$rez2->EOF || !$rez3->EOF || !$rez4->EOF)
        {
            $links[$i]['known'] = '';
        }
        else
        {
            $links[$i]['known'] = '[U]';
        }

        $i++;
        $res->MoveNext();
    }
}

if (!isset($links))
{
    $links = '';
}

calc_ownership($db,$shipinfo['sector_id']);
$num_links = $i;
$defenses = array(array());
$res = $db->Execute("SELECT * FROM {$db->prefix}sector_defense, {$db->prefix}players WHERE {$db->prefix}sector_defense.sector_id=?
                                                    AND {$db->prefix}players.player_id = {$db->prefix}sector_defense.player_id ", array($shipinfo['sector_id']));
$i = 0;
if (is_object($res))
{
    while (!$res->EOF)
    {
        $defenses[$i] = $res->fields;
        $i++;
        $res->MoveNext();
    }
}
$num_defenses = $i;

if ($no_stars)
{
    $template->assign("sectorinfo_star_size", 0);
}
else
{
    $template->assign("sectorinfo_star_size", $sectorinfo['star_size']);
}

if ($sectorinfo['star_size'] > 0 && !$no_stars)
{
    $startypes[0] = $startypes0;
    $startypes[1] = $startypes1;
    $startypes[2] = $startypes2;
    $startypes[3] = $startypes3;
    $startypes[4] = $startypes4;
    $startypes[5] = $startypes5;

    $star_image = $startypes[$sectorinfo['star_size']] . ".png";
    $star_alt = substr($star_image,0,-4);
    $template->assign("star_image", $star_image);
    $template->assign("star_alt", $star_alt);
}

$player_insignia = player_insignia_name($db,$accountinfo['email']);
if ($player_insignia['rank_icon'] == 0)
{
    $player_insignia['rank_icon'] = 1;
}

// First template begins here.

$result = $db->Execute("SELECT * FROM {$db->prefix}messages WHERE recp_id=? AND notified='N'", array($playerinfo['player_id']));
$gotmail = $result->RecordCount();
if ($gotmail > 0)
{
  $debug_query = $db->Execute("UPDATE {$db->prefix}messages SET notified='Y' WHERE recp_id=?", array($playerinfo['player_id']));
  db_op_result($db,$debug_query,__LINE__,__FILE__);
}

if ($zoneinfo['zone_id'] < 5)
{
    $zonevar = "l_zname_" . $zoneinfo['zone_id'];
    $zoneinfo['zone_name'] = $$zonevar;
}

$l_main_shipyard2=str_replace("[shipyard_link]",$l_main_shipyard1,$l_main_shipyard2);

$template->assign("gotmail", $gotmail);
$template->assign("l_youhave", $l_youhave);
$template->assign("l_messages_wait", $l_messages_wait);
$template->assign("l_nonexistant_pl", $l_nonexistant_pl);
$template->assign("planetgone", $planetgone);
$template->assign("l_turns_have", $l_turns_have);
$template->assign("ksm_allowed", $ksm_allowed);
$template->assign("l_map", $l_map);
$template->assign("l_spy", $l_spy);
$template->assign("spy_success_factor", $spy_success_factor);
$template->assign("playerinfo_acl", $playerinfo['acl']);
$template->assign("l_admin_title", $l_admin_title);
$template->assign("l_main_warpto", $l_main_warpto);
$template->assign("l_forums", $l_forums);
$template->assign("link_forums", $link_forums);
$template->assign("color_header", $color_header);
$template->assign("color_line1", $color_line1);
$template->assign("color_line2", $color_line2);
$template->assign("main_table_heading", $main_table_heading);
$template->assign("l_logout", $l_logout);
$template->assign("l_feedback", $l_feedback);
$template->assign("l_log", $l_log);
$template->assign("l_faq", $l_faq);
$template->assign("l_help", $l_help);
$template->assign("l_navcomp", $l_navcomp);
$template->assign("l_options", $l_options);
$template->assign("l_ohno", $l_ohno);
$template->assign("l_teams", $l_teams);
$template->assign("l_rankings", $l_rankings);
$template->assign("l_login_settings", $l_login_settings);
$template->assign("l_send_msg", $l_send_msg);
$template->assign("l_read_msg", $l_read_msg);
$template->assign("l_sector_def", $l_sector_def);
$template->assign("l_planets", $l_planets);
$template->assign("l_commands", $l_commands);
$template->assign("zoneinfo_zone_name", $zoneinfo['zone_name']);
$template->assign("sectorinfo_beacon", $sectorinfo['beacon']);
$template->assign("beaconhere", (!empty($sectorinfo['beacon'])));
$template->assign("shipinfo_sector_id", number_format($shipinfo['sector_id']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_sector", $l_sector);
$template->assign("l_score", $l_score);
$template->assign("playerinfo_score", number_format($playerinfo['score']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("playerinfo_turns", number_format($playerinfo['turns']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("playerinfo_turns_used", number_format($playerinfo['turns_used']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_turns_used", $l_turns_used);
$template->assign("general_highlight_color", $general_highlight_color);
$template->assign("general_text_color", $general_text_color);
$template->assign("shipinfo_name", $shipinfo['name']);
$template->assign("l_abord", $l_abord);
$template->assign("playerinfo_character_name", $playerinfo['character_name']);
$template->assign("player_insignia", $player_insignia['rank_icon']);
$template->assign("player_insignia_name", $player_insignia['rank_name']);
$template->assign("l_scan",$l_scan);
$template->assign("sectorinfo_sector_id",$sectorinfo['sector_id']);
$template->assign("links",$links);
$template->assign("num_links",$num_links);
$template->assign("l_no_warplink",$l_no_warplink);
$template->assign("l_fullscan",$l_fullscan);
$template->assign("l_tradingport",$l_tradingport);
$template->assign("l_none",$l_none);
$template->assign("portinfo_port_type",$portinfo['port_type']);
$template->assign("l_main_shipyard2", $l_main_shipyard2);
$template->assign("l_planet_in_sec", $l_planet_in_sec);
$template->assign("l_tradingport", $l_tradingport);

$res = '';
$res = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE sector_id=?", array($shipinfo['sector_id']));

$i = 0;
$successful_display = 0;

$planets = array(array());
$planettypes[0] = $planettypes0;
$planettypes[1] = $planettypes1;
$planettypes[2] = $planettypes2;
$planettypes[3] = $planettypes3;
$planettypes[4] = $planettypes4;

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

        seed_mt_rand();
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

    if ($uber == 1)
    {
        $planets[$i] = $hiding_planet[$i];
        global $basefontsize;

        global $general_highlight_color;
        $planetlevel = 0;
        $planetavg = 0;
        if ($planets[$i]['owner'] != 0)
        {
            $result5 = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=?", array($planets[$i]['owner']));
            $planet_owner = $result5->fields;

            $planetavg = $planets[$i]['computer'] + $planets[$i]['sensors'] + $planets[$i]['beams'] + $planets[$i]['torp_launchers'] + $planets[$i]['shields'] + $planets[$i]['cloak'] + ($planets[$i]['colonists'] / ($colonist_limit / 54));
            $planetavg = round($planetavg/94.5); // Divide by (54 levels * 7 categories / 4) to get 1-4.
            if ($planetavg > 4)
            {
                $planetavg = 4;
            }

            if ($planetavg < 0)
            {
                $planetavg = 0;
            }

            $planetlevel = $planetavg;
        }

        $planets[$i]['planet_image'] = $planettypes[$planetlevel]. ".png";

        if (empty($planets[$i]['name']))
        {
            $planets[$i]['name'] = $l_unnamed;
        }

        if ($planets[$i]['owner'] == 0)
        {
            $planets[$i]['owner_name'] = $l_unowned;
        }
        else
        {
            $planets[$i]['owner_name'] = $planet_owner['character_name'];
        }

        $successful_display++;
    }
    $i++;
    $res->MoveNext();
}

$template->assign("planets",$planets);
$template->assign("successful_display",$successful_display);
$template->assign("num_planets",$res->RecordCount());
$template->assign("l_ships_in_sec",$l_ships_in_sec);
$template->assign("sectorinfo_sector_id",$sectorinfo['sector_id']);
$template->assign("shipinfo_sector_id",$shipinfo['sector_id']);

$template->assign("l_sector_0", $l_sector_0);
$i = 0;
$visible_ship_array = array(array());
if ($shipinfo['sector_id'] != 1)
{
    $result4 = $db->Execute(" SELECT DISTINCT
                              {$db->prefix}ships.*,
                              {$db->prefix}players.*,
                              {$db->prefix}teams.team_name,
                              {$db->prefix}teams.team_id
                              FROM {$db->prefix}ships
                              LEFT JOIN {$db->prefix}players ON {$db->prefix}ships.player_id={$db->prefix}players.player_id
                              LEFT JOIN {$db->prefix}teams
                              ON {$db->prefix}players.team = {$db->prefix}teams.team_id
                              WHERE {$db->prefix}ships.player_id!=?
                              AND {$db->prefix}ships.sector_id=?
                              AND {$db->prefix}ships.on_planet='N'", array($playerinfo['player_id'], $shipinfo['sector_id']));

    $ships_present = $result4->RecordCount();
    $template->assign("ships_present", $ships_present);
    if ($ships_present > 0)
    {
        while (!$result4->EOF)
        {
            $row = $result4->fields;
            $success = scan_success($shipinfo['sensors'], $row['cloak']);
            if ($success < 5)
            {
                $success = 5;
            }

            if ($success > 95)
            {
                $success = 95;
            }

            seed_mt_rand();
            $roll = mt_rand(1, 100);

            if ($roll < $success)
            {
                $debug_query = $db->CacheSelectLimit("SELECT image,name FROM {$db->prefix}ship_types WHERE type_id=?",1,-1,array($row['class']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $shipimage = $debug_query->fields;
                $visible_ship_array[$i]['shipimage']      = $shipimage['image'];
                $visible_ship_array[$i]['shipimagename']  = $shipimage['name'];
                $visible_ship_array[$i]['player_id']      = $row['player_id'];
                $visible_ship_array[$i]['ship_id']        = $row['ship_id'];
                $visible_ship_array[$i]['name']           = $row['name'];
                $visible_ship_array[$i]['team_name']      = $row['team_name'];
                $visible_ship_array[$i]['character_name'] = $row['character_name'];
                $i++;
            }
            $result4->MoveNext();
        }
    }

    $template->assign("visible_count", $i);
    $template->assign("visible_ship_array", $visible_ship_array);
}
else
{
    $template->assign("visible_count", 0);
}

$shipseen = 0;
if ($shipinfo['sector_id'] != '1')
{
    $resx = $db->SelectLimit("SELECT player_id, ship_class, destination from {$db->prefix}movement_log WHERE player_id!=? AND source=? ORDER BY time DESC",1,-1,array($playerinfo['player_id'], $shipinfo['sector_id']));
    db_op_result($db,$resx,__LINE__,__FILE__);
    $shipseen = $resx->fields;
    if ($shipseen !== FALSE)
    {
        // Dynamic functions
        dynamic_loader ($db, "get_shipclassname.php");
        $template->assign("shipseen_playername", get_player($db, $shipseen['player_id']));
        $template->assign("shipseen_classname", get_shipclassname($db, $shipseen['ship_class']));
        $template->assign("shipseen_destination", $shipseen['destination']);
    }
}

$template->assign("general_highlight_color", $general_highlight_color);
$template->assign("l_lss", $l_lss);
$template->assign("shipinfo_sector_id", $shipinfo['sector_id']);
$template->assign("shipinfo_sensors", $shipinfo['sensors']);
$template->assign("lssd_level_two", $lssd_level_two);
$template->assign("lssd_level_three", $lssd_level_three);
$template->assign("shipseen", $shipseen);
$template->assign("num_defenses", $num_defenses);

if ($num_defenses > 0)
{
    $totalcount = 0;
    $curcount = 0;
    $i = 0;
    while ($i < $num_defenses)
    {
        $defense_id = $defenses[$i]['defense_id'];
        if ($defenses[$i]['defense_type'] == 'F')
        {
            $def_type = $l_fighters;
            $mode = $defenses[$i]['fm_setting'];
            if ($mode == 'attack')
            {
                $mode = $l_md_attack;
            }
            else
            {
                $mode = $l_md_toll;
            }

            $def_type .= $mode;
            $defenses[$i]['mode'] = $def_type;
        }
        elseif ($defenses[$i]['defense_type'] == 'M')
        {
            $def_type = $l_mines;
            $defenses[$i]['mode'] = $def_type;
        }

        $char_name = $defenses[$i]['character_name'];
        $qty = $defenses[$i]['quantity'];
        $totalcount++;

        if ($curcount == $picsperrow - 1)
        {
            $curcount = 0;
        }
        else
        {
            $curcount++;
        }
        $i++;
    }
    $template->assign("defenses", $defenses);
}
else
{
    $template->assign("defenses", '');
}

$i = 0;
$num_traderoutes = 0;

$traderoutes = array(array());
// Port query begin
$query = $db->Execute("SELECT * FROM {$db->prefix}traderoutes WHERE source_type='P' AND source_id=? AND owner=? ORDER BY dest_id ASC", array($shipinfo['sector_id'], $playerinfo['player_id']));
while (!$query->EOF)
{
    $traderoutes[$i] = $query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
}
// Port query end

// Sector Defense Trade route query begin
// This is still under developement
$query = $db->Execute("SELECT * FROM {$db->prefix}traderoutes WHERE source_type='D' AND source_id=? AND owner=? ORDER BY dest_id ASC", array($shipinfo['sector_id'], $playerinfo['player_id']));
while (!$query->EOF)
{
    $traderoutes[$i] = $query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
}
// Defense query end

// Personal planet traderoute type query begin
$query = $db->Execute("SELECT * FROM {$db->prefix}planets, {$db->prefix}traderoutes WHERE source_type='L' AND source_id={$db->prefix}planets.planet_id AND {$db->prefix}planets.sector_id=? AND {$db->prefix}traderoutes.owner=?", array($shipinfo['sector_id'], $playerinfo['player_id']));
while (!$query->EOF)
{
    $traderoutes[$i] = $query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
}
// Personal planet traderoute type query end

// team planet traderoute type query begin
$query = $db->Execute("SELECT * FROM {$db->prefix}planets, {$db->prefix}traderoutes WHERE source_type='C' AND source_id={$db->prefix}planets.planet_id AND {$db->prefix}planets.sector_id=? AND {$db->prefix}traderoutes.owner=?", array($shipinfo['sector_id'], $playerinfo['player_id']));
while (!$query->EOF)
{
    $traderoutes[$i] = $query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
}
// team planet traderoute type query end

if ($num_traderoutes != 0)
{
    $i = 0;
    while ($i < $num_traderoutes)
    {
        if ($traderoutes[$i]['source_type'] == 'P')
        {
            $traderoutes[$i]['description'] = "$l_port&nbsp;";
        }
        elseif ($traderoutes[$i]['source_type'] == 'D')
        {
            $traderoutes[$i]['description'] = "Def's ";
        }
        else
        {
            $query = $db->SelectLimit("SELECT name FROM {$db->prefix}planets WHERE planet_id=?",1,-1,array($traderoutes[$i]['source_id']));
            if (!$query || $query->RecordCount() == 0)
            {
                $traderoutes[$i]['description'] = $l_unknown;
            }
            else
            {
                $planet = $query->fields;
                if ($planet['name'] == "")
                {
                    $traderoutes[$i]['description'] = $l_unknown;
                }
                else
                {
                    $traderoutes[$i]['description'] = $planet['name'];
                }
            }
        }

        if ($traderoutes[$i]['dest_type'] == 'P')
        {
            $traderoutes[$i]['description2'] = $traderoutes[$i]['dest_id'];
        }
        elseif ($traderoutes[$i]['dest_type'] == 'D')
        {
            $traderoutes[$i]['description2'] = "Def's in " . $traderoutes[$i]['dest_id'];
        }
        else
        {
            $query = $db->Execute("SELECT name FROM {$db->prefix}planets WHERE planet_id=?", array($traderoutes[$i]['dest_id']));
            if (!$query || $query->RecordCount() == 0)
            {
                $traderoutes[$i]['description2'] = $l_unknown;
            }
            else
            {
                $planet = $query->fields;
                if ($planet['name'] == "")
                {
                    $traderoutes[$i]['description2'] = $l_unnamed;
                }
                else
                {
                    $traderoutes[$i]['description2'] = $planet['name'];
                }
            }
        }
    $i++;
    }
}
//-------------------------------------------------------------------------------------------------

$template->assign("traderoutes", $traderoutes);

// Pull the presets for the player from the db.
$debug_query = $db->Execute("SELECT preset FROM {$db->prefix}presets WHERE player_id=?", array($playerinfo['player_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$i = 0;
while (!$debug_query->EOF)
{
    $i++;
    $presetinfo[$i] = $debug_query->fields['preset'];
    $debug_query->MoveNext();
}

$template->assign("shipinfo_goods", number_format($shipinfo['goods']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("shipinfo_ore", number_format($shipinfo['ore']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("shipinfo_organics", number_format($shipinfo['organics']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("shipinfo_energy", number_format($shipinfo['energy']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("shipinfo_colonists", number_format($shipinfo['colonists']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("playerinfo_credits", number_format($playerinfo['credits']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("main_table_heading", $main_table_heading);
$template->assign("l_traderoutes", $l_traderoutes);
$template->assign("num_traderoutes", $num_traderoutes);
$template->assign("maindiv", 'yes');
$template->assign("l_trade_control", $l_trade_control);
$template->assign("main_table_heading", $main_table_heading);
$template->assign("l_realspace", $l_realspace);
$template->assign("l_plasma", $l_plasma);
$template->assign("rslink_sector_back", ($shipinfo['sector_id'] - 1));
$template->assign("rslink_sector_forward", ($shipinfo['sector_id'] + 1));
$template->assign("sector_max", $sector_max);
$template->assign("shipinfo_sector_id", $shipinfo['sector_id']);
$template->assign("l_scan", $l_scan);
$template->assign("presetinfo", $presetinfo);
$template->assign("l_colonists", $l_colonists);
$template->assign("l_credits", $l_credits);
$template->assign("l_cargo", $l_cargo);
$template->assign("l_goods", $l_goods);
$template->assign("l_energy", $l_energy);
$template->assign("l_ore", $l_ore);
$template->assign("l_organics", $l_organics);
$template->assign("l_none", $l_none);
$template->assign("l_set", $l_set);
$template->assign("l_main_noscript", $l_main_noscript);
$template->assign("templateset", $templateset);
$template->assign("classimage", $classinfo['image']);
$template->assign("gravatar_id", md5($accountinfo['email']));
$template->assign("use_gravatar", $playerinfo['use_gravatar']);
$template->assign("override_gravatar", $accountinfo['override_gravatar']);
$template->assign("plasma_engines", $plasma_engines);
$template->display("$templateset/main.tpl");
include_once './footer.php';
?>
