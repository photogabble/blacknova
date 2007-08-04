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
// File: shipyard2.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "spy_buy_new_ship.php"); 
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'shipyard');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_shipyard2_title;
updatecookie($db);

echo "<h1>" . $title. "</h1>\n";

$res = $db->Execute("SELECT * FROM {$db->prefix}ship_types WHERE buyable = 'Y'");
while (!$res->EOF)
{
    $ships[] = $res->fields;
    $res->MoveNext();
}

if (isset($_POST['stype']))
{
    $lastship = end($ships);
    if ($_POST['stype'] < 1 || $_POST['stype'] > $lastship['type_id'])
    {
        shipyard_die("Wrong ship class specified");
    }
}
else
{
    shipyard_die("Wrong ship class specified");
}

foreach($ships as $testship)
{
    if ($testship['type_id'] == $_POST['stype'])
    {
        $sship = $testship;
        break;
    }
}

if (!isset($_POST['confirm'])) // Display info only
{
    $calc_hull = round(pow($upgrade_factor,$shipinfo['hull']));
    $calc_engines = round(pow($upgrade_factor,$shipinfo['engines']));
    $calc_pengines = round(pow($upgrade_factor,$shipinfo['pengines']));
    $calc_power = round(pow($upgrade_factor,$shipinfo['power']));
    $calc_computer = round(pow($upgrade_factor,$shipinfo['computer']));
    $calc_sensors = round(pow($upgrade_factor,$shipinfo['sensors']));
    $calc_beams = round(pow($upgrade_factor,$shipinfo['beams']));
    $calc_torp_launchers = round(pow($upgrade_factor,$shipinfo['torp_launchers']));
    $calc_shields = round(pow($upgrade_factor,$shipinfo['shields']));
    $calc_armor = round(pow($upgrade_factor,$shipinfo['armor']));
    $calc_cloak = round(pow($upgrade_factor,$shipinfo['cloak']));
    $shipvalue = ($calc_hull+$calc_engines+$calc_pengines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armor+$calc_cloak) * $upgrade_cost;

    $calc_nhull = round(pow($upgrade_factor,$sship['minhull']));
    $calc_nengines = round(pow($upgrade_factor,$sship['minengines']));
    $calc_npengines = round(pow($upgrade_factor,$sship['minpengines']));
    $calc_npower = round(pow($upgrade_factor,$sship['minpower']));
    $calc_ncomputer = round(pow($upgrade_factor,$sship['mincomputer']));
    $calc_nsensors = round(pow($upgrade_factor,$sship['minsensors']));
    $calc_nbeams = round(pow($upgrade_factor,$sship['minbeams']));
    $calc_ntorp_launchers = round(pow($upgrade_factor,$sship['mintorp_launchers']));
    $calc_nshields = round(pow($upgrade_factor,$sship['minshields']));
    $calc_narmor = round(pow($upgrade_factor,$sship['minarmor']));
    $calc_ncloak = round(pow($upgrade_factor,$sship['mincloak']));
    $newshipvalue = ($calc_nhull+$calc_nengines+$calc_npengines+$calc_npower+$calc_ncomputer+$calc_nsensors+$calc_nbeams+$calc_ntorp_launchers+$calc_nshields+$calc_narmor+$calc_ncloak) * $upgrade_cost;
    $totalcost = $newshipvalue - $shipvalue; 

    if ($totalcost <= 1)
    {
        $totalcost = $newshipvalue;
    }

    echo "
    <font style=\"font-size: 1.2em;\" color=white><strong>You are buying:</strong></font><p>
    <table border=0 cellpadding=5>
    <tr><td align=center><font color=white style=\"font-size: 1.2em;\"><strong>$sship[name]</strong><br><img src=templates/$templateset/images/$sship[image]></font></td>
    <td><font style=\"font-size: 0.8em;\"><strong>$sship[description]</strong></font>
    </table>
    <p>
    <table border=0>
    <tr><td>
    <font style=\"font-size: 1.2em;\">Current Ship Value:&nbsp;&nbsp;&nbsp;</font></td>
    <td align=right><font style=\"font-size: 1.2em;\" color=#00FF00><strong>" . number_format($shipvalue, 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong></font></td></tr>
    <tr><td>
    <font style=\"font-size: 1.2em;\">New Ship Value:&nbsp;&nbsp;&nbsp;</font></td>
    <td align=right><font style=\"font-size: 1.2em;\" color=#FF0000><strong>" . number_format($newshipvalue, 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong></font></td></tr>
    <tr><td><td><hr></td></tr>
    <tr><td>
    <font style=\"font-size: 1.2em;\">Total Cost:&nbsp;&nbsp;&nbsp;</font></td>
    <td align=right><font style=\"font-size: 1.2em;\" color=#FF0000><strong>" . number_format($totalcost, 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong></font></td></tr></table>
    <p>
    ";

    if ($totalcost > $playerinfo['credits'])
    {
        echo "<br><font color=white><strong>&nbsp;You do not have enough credits to buy this ship.</strong></font><p><br>";
    }
    else
    {
        echo '<form name="bntform" action="shipyard2.php" method="post" accept-charset="utf-8" onsubmit="document.bntform.submit_button.disabled=true;">' .
             "<input type=hidden name=stype value=$_POST[stype]>" .
             "<input type=hidden name=confirm value=yes>" .
             "<input type=submit name=submit_button value=\"Purchase\">".
             "</form><p>";
    }
}
else // Now we really do buy the ship
{
    $calc_hull = round(pow($upgrade_factor,$shipinfo['hull']));
    $calc_engines = round(pow($upgrade_factor,$shipinfo['engines']));
    $calc_pengines = round(pow($upgrade_factor,$shipinfo['pengines']));
    $calc_power = round(pow($upgrade_factor,$shipinfo['power']));
    $calc_computer = round(pow($upgrade_factor,$shipinfo['computer']));
    $calc_sensors = round(pow($upgrade_factor,$shipinfo['sensors']));
    $calc_beams = round(pow($upgrade_factor,$shipinfo['beams']));
    $calc_torp_launchers = round(pow($upgrade_factor,$shipinfo['torp_launchers']));
    $calc_shields = round(pow($upgrade_factor,$shipinfo['shields']));
    $calc_armor = round(pow($upgrade_factor,$shipinfo['armor']));
    $calc_cloak = round(pow($upgrade_factor,$shipinfo['cloak']));
    $shipvalue = ($calc_hull+$calc_engines+$calc_pengines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armor+$calc_cloak) * $upgrade_cost;
    $shipvalue = $shipvalue * $trade_in_value;

    $calc_nhull = round(pow($upgrade_factor,$sship['minhull']));
    $calc_nengines = round(pow($upgrade_factor,$sship['minengines']));
    $calc_npengines = round(pow($upgrade_factor,$sship['minpengines']));
    $calc_npower = round(pow($upgrade_factor,$sship['minpower']));
    $calc_ncomputer = round(pow($upgrade_factor,$sship['mincomputer']));
    $calc_nsensors = round(pow($upgrade_factor,$sship['minsensors']));
    $calc_nbeams = round(pow($upgrade_factor,$sship['minbeams']));
    $calc_ntorp_launchers = round(pow($upgrade_factor,$sship['mintorp_launchers']));
    $calc_nshields = round(pow($upgrade_factor,$sship['minshields']));
    $calc_narmor = round(pow($upgrade_factor,$sship['minarmor']));
    $calc_ncloak = round(pow($upgrade_factor,$sship['mincloak']));
    $newshipvalue = ($calc_nhull+$calc_nengines+$calc_npengines+$calc_npower+$calc_ncomputer+$calc_nsensors+$calc_nbeams+$calc_ntorp_launchers+$calc_nshields+$calc_narmor+$calc_ncloak) * $upgrade_cost;

    $totalcost = $newshipvalue - $shipvalue;

    // Let's do the regular sanity checks first

    if ($totalcost <= 1)
    {
        $totalcost = $newshipvalue;
    }

    if ($playerinfo['turns'] < $sship['turnstobuild'])
    {
        shipyard_die("You need at least $sship[turnstobuild] turns to perform this action");
    }

    if (!isset($sship))
    {
        shipyard_die("Internal error. Cannot find ship class.");
    }

    if ($sship['type_id'] == $shipinfo['class'])
    {
        shipyard_die("You already own this model of ship.");
    }

    if ($playerinfo['credits'] < $totalcost)
    {
        shipyard_die("You do not have enough credits to complete this transaction.");
    }

    if (!get_magic_quotes_gpc())
    {
        $shipname = mysql_escape_string($shipinfo['name']);
    }

    // Time to create the new ship and assign it as current

    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ships (ship_id, player_id, class, name, destroyed, hull, engines, pengines, power, computer, sensors, beams," .
                                "torp_launchers, torps, shields, armor, armor_pts, cloak, sector_id, ore, organics, goods, energy, colonists, fighters, on_planet," .
                                "dev_warpedit, dev_genesis, dev_emerwarp, dev_escapepod, dev_fuelscoop, dev_minedeflector, planet_id, cleared_defenses)" .
                                " VALUES(" .
                                "''," .                             // ship_id
                                "$playerinfo[player_id]," .         // player_id
                                "'$_POST[stype]'," .                       // class
                                "'$shipinfo[name]'," .                    // name
                                "'N'," .                            // destroyed
                                "$sship[minhull]," .                // hull
                                "$sship[minengines]," .             // engines
                                "$sship[minpengines]," .             // pengines
                                "$sship[minpower]," .               // power
                                "$sship[mincomputer]," .            // computer
                                "$sship[minsensors]," .             // sensors
                                "$sship[minbeams]," .               // beams
                                "$sship[mintorp_launchers]," .      // torp_launchers
                                "0," .                              // torps
                                "$sship[minshields]," .             // shields
                                "$sship[minarmor]," .              // armor
                                "$start_armor," .                  // armor_pts
                                "$sship[mincloak]," .               // cloak
                                "$shipinfo[sector_id]," .           // sector_id
                                "0," .                              // ore
                                "0," .                              // organics
                                "0," .                              // goods
                                "$start_energy," .                  // energy
                                "0," .                              // colonists
                                "$start_fighters," .                // fighters
                                "'N'," .                            // on_planet
                                "$shipinfo[dev_warpedit]," .        // dev_warpedit
                                "$shipinfo[dev_genesis]," .         // dev_genesis
                                "$shipinfo[dev_emerwarp]," .        // dev_emerwarp
                                "'$shipinfo[dev_escapepod]'," .     // dev_escapepod
                                "'$shipinfo[dev_fuelscoop]'," .     // dev_fuelscoop
                                "$shipinfo[dev_minedeflector]," .   // dev_minedeflector
                                "0," .                              // planet_id
                                "'' " .                             // cleared_defenses
                                ")");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Get new ship_id
    $debug_query = $db->Execute("SELECT MAX(ship_id) as ship_id from {$db->prefix}ships WHERE player_id=$playerinfo[player_id]" .
                        " AND class='$_POST[stype]'");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $ship_id = $debug_query->fields['ship_id'];

    // Insert current ship in players table
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET currentship=$ship_id " . 
                                "WHERE player_id=$playerinfo[player_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    if ($spy_success_factor)
    {
        spy_buy_new_ship($db,$shipinfo['ship_id']);
    }
    $debug_query = $db->Execute("DELETE FROM {$db->prefix}ships WHERE ship_id=$shipinfo[ship_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Now update player credits & turns
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-$sship[turnstobuild], turns_used=turns_used+$sship[turnstobuild], credits=credits-$totalcost WHERE player_id=$playerinfo[player_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    gen_score($db,$playerinfo['player_id']);

    echo "<p>You have just bought a new ship!<p>";
}

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

// Dynamic functions
dynamic_loader ($db, "shipyard_die.php");
?>
