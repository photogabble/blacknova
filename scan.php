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
// File: scan.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "scan_error.php");
dynamic_loader ($db, "scan_success.php");
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'scan');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'bounty');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_scan_title;
updatecookie($db);

if (!isset($_GET['player_id']))
{
    $player_id = '';
}
else
{
    $player_id = $_GET['player_id'];
}

$debug_query = $db->Execute ("SELECT * FROM {$db->prefix}players WHERE player_id=?", array($player_id));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$targetinfo = $debug_query->fields;

$debug_query = $db->Execute ("SELECT * FROM {$raw_prefix}users WHERE account_id=?", array($targetinfo['account_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$targetaccountinfo = $debug_query->fields;

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array($targetinfo['currentship']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$targetshipinfo = $debug_query->fields;

$playerscore = gen_score($db,$playerinfo['player_id']);
$targetscore = gen_score($db,$targetinfo['player_id']);

$playerscore = $playerscore * $playerscore;
$targetscore = $targetscore * $targetscore;

echo "<h1>" . $title. "</h1>\n";

seed_mt_rand();

// check to ensure target is in the same sector as player
if (($targetshipinfo['sector_id'] != $shipinfo['sector_id']) || ($targetshipinfo['sector_id'] == 1) || ($targetshipinfo['on_planet'] != 'N'))
{
    echo $l_planet_noscan;
}
else
{
    if ($playerinfo['turns'] < 1)
    {
        echo $l_scan_turn;
    }
    else
    {
        // determine per cent chance of success in scanning target ship - 
        // Based on player's sensors and opponent's cloak

        $success = scan_success($shipinfo['sensors'], $targetshipinfo['cloak']);
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
            echo $l_planet_noscan;
            playerlog($db,$targetinfo['player_id'], "LOG_SHIP_SCAN_FAIL", "$playerinfo[character_name]");
        }
        else
        {
            // if scan succeeds, show results and inform target.
            // cramble results by scan error factor.
            // Get total bounty on this player, if any
            $btyamount = 0;
            $debug_query = $db->Execute("SELECT SUM(amount) AS btytotal FROM {$db->prefix}bounty WHERE bounty_on=?", array($targetinfo['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            if ($debug_query)
            {
                $resx = $debug_query->fields;
                if ($resx['btytotal'] > 0)
                {
                    $btyamount = number_format($resx['btytotal'], 0, $local_number_dec_point, $local_number_thousands_sep);
                    $l_scan_bounty = str_replace("[amount]",$btyamount,$l_scan_bounty);
                    echo $l_scan_bounty . "<br>";
                    $btyamount = 0;
                    // Check for Federation bounty
                    $hasfedbounty = $db->Execute("SELECT SUM(amount) AS btytotal FROM {$db->prefix}bounty WHERE bounty_on=? AND placed_by = 0", array($targetinfo['player_id']));
                    db_op_result($db,$hasfedbounty,__LINE__,__FILE__);
                    if ($hasfedbounty)
                    {
                        $resy = $hasfedbounty->fields;
                        if ($resy['btytotal'] > 0)
                        {
                            $btyamount = $resy['btytotal'];
                            echo $l_scan_fedbounty . "<br>";
                        }
                    }
                }
            }

            // Player will get a Federation Bounty on themselves if they attack a player who's score is less than 
            // bounty_ratio of themselves. If the target has a Federation Bounty, they can attack without attracting a bounty on themselves.

            if (($btyamount == 0 && ((($targetscore / $playerscore) < $bounty_ratio) || $targetinfo['turns_used'] < $bounty_minturns)) && !("aiplayer" == substr($targetaccountinfo['email'], -8)))
            {
                echo $l_by_fedbounty . "<br><br>";
            }
            else
            {
                echo $l_by_nofedbounty . "<br><br>";
            }

            $sc_error = scan_error($shipinfo['sensors'], $targetshipinfo['cloak'], $scan_error_factor);
            echo "$l_scan_ron $targetshipinfo[name], $l_scan_capt  $targetinfo[character_name]<br><br>";
            echo "<strong>$l_ship_levels:</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>$l_hull:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_hull = round($targetshipinfo['hull'] * $sc_error / 100);
                echo "<td>$sc_hull</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_engines:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_engines = round($targetshipinfo['engines'] * $sc_error / 100);
                echo "<td>$sc_engines</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            if ($plasma_engines)
            {
                echo "<tr><td>$l_pengines:</td>";
                $roll = mt_rand(1,100);
                if ($roll < $success)
                {
                    $sc_pengines = round($targetshipinfo['pengines'] * $sc_error / 100);
                    echo "<td>$sc_pengines</td></tr>";
                }
                else 
                {
                    echo "<td>???</td></tr>";
                }
            }

            echo "<tr><td>$l_power:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_power = round($targetshipinfo['power'] * $sc_error / 100);
                echo "<td>$sc_power</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_computer:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_computer = round($targetshipinfo['computer'] * $sc_error / 100);
                echo "<td>$sc_computer</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_sensors:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_sensors = round($targetshipinfo['sensors'] * $sc_error / 100);
                echo "<td>$sc_sensors</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_beams:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_beams = round($targetshipinfo['beams'] * $sc_error / 100);
                echo "<td>$sc_beams</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_torp_launch:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_torp_launchers = round($targetshipinfo['torp_launchers'] * $sc_error / 100);
                echo "<td>$sc_torp_launchers</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_armor:</td>";
            $roll = mt_rand(1,100);
            if ($roll  <$success)
            {
                $sc_armor = round($targetshipinfo['armor'] * $sc_error / 100);
                echo "<td>$sc_armor</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_shields:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_shields = round($targetshipinfo['shields'] * $sc_error / 100);
                echo "<td>$sc_shields</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }
            echo "<tr><td>$l_cloak:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_cloak = round($targetshipinfo['cloak'] * $sc_error / 100);
                echo "<td>$sc_cloak</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            echo "<strong>$l_scan_arma</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>$l_armorpts:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_armor_pts=round($targetshipinfo['armor_pts'] * $sc_error / 100);
                echo "<td>$sc_armor_pts</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_fighters:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_ship_fighters = round($targetshipinfo['fighters'] * $sc_error / 100);
                echo "<td>$sc_ship_fighters</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_torps:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_torps = round($targetshipinfo['torps'] * $sc_error / 100);
                echo "<td>$sc_torps</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            echo "<strong>$l_scan_carry</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>$l_credits:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_credits = round($targetinfo['credits'] * $sc_error / 100);
                echo "<td>$sc_credits</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_colonists:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_ship_colonists = round($targetshipinfo['colonists'] * $sc_error / 100);
                echo "<td>$sc_ship_colonists</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_energy:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_ship_energy = round($targetshipinfo['energy'] * $sc_error / 100);
                echo "<td>$sc_ship_energy</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_ore:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_ship_ore = round($targetshipinfo['ore'] * $sc_error / 100);
                echo "<td>$sc_ship_ore</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_organics:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_ship_organics = round($targetshipinfo['organics'] * $sc_error / 100);
                echo "<td>$sc_ship_organics</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_goods:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_ship_goods = round($targetshipinfo['goods'] * $sc_error / 100);
                echo "<td>$sc_ship_goods</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            echo "<strong>$l_devices:</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>$l_warpedit:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_dev_warpedit = round($targetshipinfo['dev_warpedit'] * $sc_error / 100);
                echo "<td>$sc_dev_warpedit</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_genesis:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_dev_genesis = round($targetshipinfo['dev_genesis'] * $sc_error / 100);
                echo "<td>$sc_dev_genesis</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_deflect:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_dev_minedeflector = round($targetshipinfo['dev_minedeflector'] * $sc_error / 100);
                echo "<td>$sc_dev_minedeflector</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_ewd:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                $sc_dev_emerwarp = round($targetshipinfo['dev_emerwarp'] * $sc_error / 100);
                echo "<td>$sc_dev_emerwarp</td></tr>";
            }
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_escape_pod:</td>";
            $roll = mt_rand(1,100);
            if ($roll < $success)
            {
                echo "<td>$targetshipinfo[dev_escapepod]</td></tr>";
            } 
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>$l_fuel_scoop:</td>";
            $roll = mt_rand(1,100);

            if ($roll < $success)
            {
                echo "<td>$targetshipinfo[dev_fuelscoop]</td></tr>";
            } 
            else 
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            playerlog($db,$targetinfo['player_id'], "LOG_SHIP_SCAN", "$playerinfo[character_name]");
        }
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=?", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
}


echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");

?>
