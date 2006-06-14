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
// File: report.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "makebars.php"); 
dynamic_loader ($db, "num_level.php"); 
dynamic_loader ($db, "updatecookie.php");

load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'spy');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_report_title;
updatecookie($db);
include_once ("./header.php");

global $local_number_dec_point, $local_number_thousands_sep;

if (isset($_GET['sid']))  // Called from the Spy menu
{
    $debug_query = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE owner_id=$playerinfo[player_id] and ship_id='$_GET[sid]'");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $ok = $debug_query->RecordCount();

    if ($ok)  // Player has a spy on the target ship. Let's change the info-s.
    {
        $thisplayerinfo = $playerinfo;
        $thisshipinfo = $shipinfo;
        $thisclassinfo = $classinfo;
        $thissectorinfo = $sectorinfo;
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}players WHERE currentship='$_GET[sid]'",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $playerinfo = $debug_query->fields;
    
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ships WHERE player_id=$playerinfo[player_id] " .
                                        "AND ship_id=$playerinfo[currentship]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $shipinfo = $debug_query->fields;
    
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ship_types WHERE type_id=$shipinfo[class]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $classinfo = $debug_query->fields;
    
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}universe WHERE sector_id=$shipinfo[sector_id]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $sectorinfo = $debug_query->fields;
    }
    else
    {
        global $l_global_mmenu;
        $template->assign("l_global_mmenu", $l_global_mmenu);
        $template->display("$templateset/report-cheat.tpl");
        include_once ("./footer.php");
        die();
    }

}
else
{
    unset($thisplayerinfo);
}

$holds_used = $shipinfo['ore'] + $shipinfo['organics'] + $shipinfo['goods'] + $shipinfo['colonists'];

$holds_max = num_level($shipinfo['hull'], $level_factor, $level_magnitude);
$armor_pts_max = num_level($shipinfo['armor'], $level_factor, $level_magnitude);
$ship_fighters_max = num_level($shipinfo['computer'], $level_factor, $level_magnitude);
$torps_max = num_level($shipinfo['torp_launchers'], $level_factor, $level_magnitude);
$energy_max = 5* num_level($shipinfo['power'], $level_factor, $level_magnitude);

$average_stats = (($shipinfo['computer'] + $shipinfo['beams'] + $shipinfo['torp_launchers']) / 3 );
$average_stats_max = (($classinfo['maxcomputer'] + $classinfo['maxbeams'] + $classinfo['maxtorp_launchers']) / 3 );

$hull_bars = MakeBars($shipinfo['hull'], $classinfo['maxhull']);
$engines_bars = MakeBars($shipinfo['engines'], $classinfo['maxengines']);

if ($plasma_engines)
{
$pengines_bars = MakeBars($shipinfo['pengines'], $classinfo['maxpengines']);
}

$power_bars = MakeBars($shipinfo['power'], $classinfo['maxpower']);
$computer_bars = MakeBars($shipinfo['computer'], $classinfo['maxcomputer']);
$sensors_bars = MakeBars($shipinfo['sensors'], $classinfo['maxsensors']);
$armor_bars = MakeBars($shipinfo['armor'], $classinfo['maxarmor']);
$shields_bars = MakeBars($shipinfo['shields'], $classinfo['maxshields']);
$beams_bars = MakeBars($shipinfo['beams'], $classinfo['maxbeams']);
$torp_launchers_bars = MakeBars($shipinfo['torp_launchers'], $classinfo['maxtorp_launchers']);
$cloak_bars = MakeBars($shipinfo['cloak'], $classinfo['maxcloak']);
$average_bars = MakeBars($average_stats, $average_stats_max);

global $l_global_mmenu;

if ($spy_success_factor)
{
    $debug_query = $db->Execute("SELECT * from {$db->prefix}spies WHERE owner_id = $playerinfo[player_id] AND " .
                                "ship_id = $shipinfo[ship_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $ship_spies = $debug_query->RecordCount();
  
    $template->assign("ship_spies", $ship_spies);
    $template->assign("l_spy", $l_spy);
}

$template->assign("spy_success_factor", $spy_success_factor);
$template->assign("shipname", $shipinfo['name']);
$template->assign("classname", $classinfo['name']);
$template->assign("classdescription", $classinfo['description']);
$template->assign("classimage", $classinfo['image']);
$template->assign("l_ship_levels", $l_ship_levels);
$template->assign("l_hull", $l_hull);
$template->assign("shipinfo_hull", $shipinfo['hull']);
$template->assign("classinfo_maxhull", $classinfo['maxhull']);
$template->assign("hull_bars", $hull_bars);
$template->assign("l_engines", $l_engines);
$template->assign("plasma_engines", $plasma_engines);

if ($plasma_engines)
{
$template->assign("l_pengines", $l_pengines);
$template->assign("shipinfo_pengines", $shipinfo['pengines']);
$template->assign("classinfo_maxpengines", $classinfo['maxpengines']);
$template->assign("pengines_bars", $pengines_bars);
}

$template->assign("shipinfo_engines", $shipinfo['engines']);
$template->assign("classinfo_maxengines", $classinfo['maxengines']);
$template->assign("engines_bars", $engines_bars);
$template->assign("l_power", $l_power);
$template->assign("shipinfo_power", $shipinfo['power']);
$template->assign("classinfo_maxpower", $classinfo['maxpower']);
$template->assign("power_bars", $power_bars);
$template->assign("l_computer", $l_computer);
$template->assign("shipinfo_computer", $shipinfo['computer']);
$template->assign("classinfo_maxcomputer", $classinfo['maxcomputer']);
$template->assign("computer_bars", $computer_bars);
$template->assign("l_sensors", $l_sensors);
$template->assign("shipinfo_sensors", $shipinfo['sensors']);
$template->assign("classinfo_maxsensors", $classinfo['maxsensors']);
$template->assign("sensors_bars", $sensors_bars);
$template->assign("average_stats", number_format($average_stats, 1, $local_number_dec_point, $local_number_thousands_sep));
$template->assign("average_stats_max", number_format($average_stats_max, 1, $local_number_dec_point, $local_number_thousands_sep));
$template->assign("average_bars", $average_bars);
$template->assign("l_armor", $l_armor);
$template->assign("shipinfo_armor", $shipinfo['armor']);
$template->assign("classinfo_maxarmor", $classinfo['maxarmor']);
$template->assign("armor_bars", $armor_bars);
$template->assign("l_shields", $l_shields);
$template->assign("shipinfo_shields", $shipinfo['shields']);
$template->assign("classinfo_maxshields", $classinfo['maxshields']);
$template->assign("shields_bars", $shields_bars);
$template->assign("l_beams", $l_beams);
$template->assign("shipinfo_beams", $shipinfo['beams']);
$template->assign("classinfo_maxbeams", $classinfo['maxbeams']);
$template->assign("beams_bars", $beams_bars);
$template->assign("l_torp_launch", $l_torp_launch);
$template->assign("shipinfo_torp_launchers", $shipinfo['torp_launchers']);
$template->assign("classinfo_maxtorp_launchers", $classinfo['maxtorp_launchers']);
$template->assign("torp_launchers_bars", $torp_launchers_bars);
$template->assign("l_cloak", $l_cloak);
$template->assign("shipinfo_cloak", $shipinfo['cloak']);
$template->assign("classinfo_maxcloak", $classinfo['maxcloak']);
$template->assign("cloak_bars", $cloak_bars);
$template->assign("l_holds", $l_holds);
$template->assign("l_arm_weap", $l_arm_weap);
$template->assign("l_devices", $l_devices);
$template->assign("l_total_cargo", $l_total_cargo);
$template->assign("holds_used", number_format($holds_used), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("holds_max", number_format($holds_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_ore", $l_ore);
$template->assign("shipinfo_ore", number_format($shipinfo['ore']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_organics", $l_organics);
$template->assign("shipinfo_organics", number_format($shipinfo['organics']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_goods", $l_goods);
$template->assign("shipinfo_goods", number_format($shipinfo['goods']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_colonists", $l_colonists);
$template->assign("shipinfo_colonists", number_format($shipinfo['colonists']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_energy", $l_energy);
$template->assign("shipinfo_energy", number_format($shipinfo['energy']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("energy_max", number_format($energy_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_fighters", $l_fighters);
$template->assign("shipinfo_fighters", number_format($shipinfo['fighters']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("ship_fighters_max", number_format($ship_fighters_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_mines", $l_mines);
$template->assign("l_torps", $l_torps);
$template->assign("shipinfo_torps", number_format($shipinfo['torps']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("torps_max", number_format($torps_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_armorpts", $l_armorpts);
$template->assign("shipinfo_armor_pts", number_format($shipinfo['armor_pts']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("armor_pts_max", number_format($armor_pts_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_warpedit", $l_warpedit);
$template->assign("shipinfo_dev_warpedit", number_format($shipinfo['dev_warpedit']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_genesis", $l_genesis);
$template->assign("shipinfo_dev_genesis", number_format($shipinfo['dev_genesis']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_deflect", $l_deflect);
$template->assign("shipinfo_dev_minedeflector", number_format($shipinfo['dev_minedeflector']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("l_escape_pod", $l_escape_pod);
$template->assign("shipinfo_dev_escapepod", $shipinfo['dev_escapepod']);
$template->assign("l_installed", $l_installed);
$template->assign("l_not_installed", $l_not_installed);
$template->assign("l_fuel_scoop", $l_fuel_scoop);
$template->assign("shipinfo_dev_fuelscoop", $shipinfo['dev_fuelscoop']);
$template->assign("l_ewd", $l_ewd);
$template->assign("l_avg_stats", $l_avg_stats);
$template->assign("shipinfo_dev_emerwarp", $shipinfo['dev_emerwarp']);
$template->assign("l_credits", $l_credits);
$template->assign("shipinfo_credits", number_format($playerinfo['credits']), 0, $local_number_dec_point, $local_number_thousands_sep);
$template->assign("templateset", $templateset);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->assign("l_spy_linkback", $l_spy_linkback);
$template->assign("l_clickme", $l_clickme);
$template->assign("sid_isset", (isset($_GET['sid'])));
$template->display("$templateset/report.tpl");

if (isset($thisplayerinfo))
{
    $playerinfo = $thisplayerinfo;
    $shipinfo = $thisshipinfo;
    $classinfo = $thisclassinfo;
    $sectorinfo = $thissectorinfo;
}

include_once ("./footer.php");
?>
