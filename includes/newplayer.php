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
// File: inclues/newplayer.php

function newplayer($db,$email, $char, $pass, $c_code, $ship_name, $acl)
{
    global $raw_prefix;
    global $start_credits, $start_turns, $preset_limit;
    global $start_armor, $start_energy, $start_fighters, $max_turns;
    global $start_pod, $start_scoop, $zone_name;
    global $default_template;

    $stamp = date("Y-m-d H:i:s");

    // Get the new player's account id - if it exists yet
    $res = $db->Execute("SELECT account_id FROM {$raw_prefix}users WHERE email=?", array($email));
    db_op_result($db,$res,__LINE__,__FILE__);
    $account_id = $res->fields['account_id'];

    if (!$account_id)
    {
        // Create account, since it doesn't exist yet.
        $debug_query = $db->Execute("INSERT INTO {$raw_prefix}users (email, password, c_code, active) VALUES(" .
                                    "?," .  // email
                                    "?," .  // password - sha256 hashed.
                                    "?," .  // c_code
                                    "'N'" . // active
                                    ")", array($email, sha256::hash($pass), $c_code));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        // Now that we've created the new account, get the new player's account id
        $res = $db->Execute("SELECT account_id FROM {$raw_prefix}users WHERE email=?", array($email));
        db_op_result($db,$res,__LINE__,__FILE__);
        $account_id = $res->fields['account_id'];
    }

    // Create player
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}players (currentship, ".
                                "character_name, credits, turns, ".
                                "turns_used, last_login, last_update, times_dead, rating, ".
                                "score, team, team_invite, ip_address, ".
                                "trade_colonists, trade_fighters, ".
                                "trade_torps, trade_energy, template, account_id, acl) VALUES(" .
                                "0," .                              // currentship
                                "?," .                              // character_name
                                "?," .                              // credits
                                "?," .                              // turns
                                "0," .                              // turns_used
                                "?," .                              // last_login
                                "?," .                              // last_update
                                "0," .                              // times_dead
                                "0," .                              // rating
                                "0," .                              // score
                                "0," .                              // team
                                "0," .                              // team_invite
                                "'". getenv("REMOTE_ADDR") ."'," .  // ip_address
                                "'Y'," .                            // trade_colonists
                                "'N'," .                            // trade_fighters
                                "'N'," .                            // trade_torps
                                "'Y'," .                            // trade_energy
                                "?," .                              // template
                                "?," .                              // account_id
                                "?" .                               // ACL
                                ")", array($char, $start_credits, $start_turns, $stamp, $stamp, $default_template, $account_id, $acl));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Get the new player's id
    $res = $db->Execute("SELECT player_id FROM {$db->prefix}players WHERE account_id=?", array($account_id));
    db_op_result($db,$res,__LINE__,__FILE__);
    $player_id = $res->fields['player_id'];

    // Set up an empty set of presets, so if there arent any, the user can still edit them. :)
    $x = $preset_limit + 1;
    for ($y=1; $y<$x; $y++)
    {
        $debug_query = $db->Execute("INSERT INTO {$db->prefix}presets (player_id, preset) VALUES " . 
                                    "(?, '1')", array($player_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    // Add presets for player
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}presets (player_id, preset) VALUES " . 
                                "(?, '1')", array($player_id));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Create player's ship
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ships (player_id, ".
                                "class, name, destroyed, hull, engines, pengines, ".
                                "power, computer, sensors, beams, ".
                                "torp_launchers, torps, shields, armor, ".
                                "armor_pts, cloak, sector_id, ore, ".
                                "organics, goods, energy, colonists, ".
                                "fighters, on_planet, dev_warpedit, ".
                                "dev_genesis, dev_emerwarp, ".
                                "dev_escapepod, dev_fuelscoop, ".
                                "dev_minedeflector, planet_id, ".
                                "cleared_defenses) VALUES(" .
//                               "''," . // ship_id     -  not needed.
                                "?," .   // player_id
                                "'1'," . // class
                                "?," .   // name
                                "'N'," . // destroyed
                                "0," .   // hull
                                "0," .   // engines
                                "0," .   // pengines
                                "0," .   // power
                                "0," .   // computer
                                "0," .   // sensors
                                "0," .   // beams
                                "0," .   // torp_launchers
                                "0," .   // torps
                                "0," .   // shields
                                "0," .   // armor
                                "?," .   // armor_pts
                                "0," .   // cloak
                                "1," .   // sector_id
                                "0," .   // ore
                                "0," .   // organics
                                "0," .   // goods
                                "?," .   // energy
                                "0," .   // colonists
                                "?," .   // fighters
                                "'N'," . // on_planet
                                "0," .   // dev_warpedit
                                "0," .   // dev_genesis
                                "0," .   // dev_emerwarp
                                "?," .   // dev_escapepod
                                "?," .   // dev_fuelscoop
                                "0," .   // dev_minedeflector
                                "0," .   // planet_id
                                "''" .   // cleared_defenses
                                ")", array($player_id, $ship_name, $start_armor, $start_energy, $start_fighters, $start_pod, $start_scoop));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Get the new ship's id
    $res = $db->Execute("SELECT ship_id FROM {$db->prefix}ships WHERE player_id=?", array($player_id));
    db_op_result($db,$res,__LINE__,__FILE__);
    $ship_id = $res->fields['ship_id'];

    // Insert current ship in players table
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET currentship=? WHERE player_id=?", array($ship_id, $player_id));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $zone_name = $char . "&#39;s Territory";

    // Create player's zone
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}zones (zone_name, ".
                                "owner, team_zone, allow_attack, ".
                                "allow_planetattack, allow_warpedit, ".
                                "allow_planet, allow_trade, allow_defenses, ".
                                "max_level) VALUES(" .
                                "?," .      // zone_name
                                "?," .      // owner
                                "'N'," .               // team_zone
                                "'Y'," .               // allow_attack
                                "'Y'," .               // allow_planetattack
                                "'Y'," .               // allow_warpedit
                                "'Y'," .               // allow_planet
                                "'Y'," .               // allow_trade
                                "'Y'," .               // allow_defenses
                                "0" .                  // max_level
                                ")", array($zone_name, $player_id));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $stamp = date("Y-m-d H:i:s");
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ibank_accounts (player_id,balance,loan,loantime) VALUES ('$player_id',0,0,'$stamp')");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    return $player_id;
}
?>
