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
// File: includes/ai_hunter.php
//
// Description: The function handling AI hunters (attackers).

$pos = (strpos($_SERVER['PHP_SELF'], "/ai_toship.php"));
if ($pos !== false)
{
    include_once 'global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once 'footer.php';
    die();
}

function ai_hunter()
{
    // Dynamic functions
    dynamic_loader ($db, "seed_mt_rand.php");
    dynamic_loader ($db, "playerlog.php");

    // Setup general variables
    global $playerinfo, $targetlink, $ai_isdead;
    global $db;
    seed_mt_rand();

    $rescount = $db->Execute("SELECT COUNT(*) AS num_players FROM {$db->prefix}players " .
                             "LEFT JOIN {$raw_prefix}users on {$db->prefix}players.account_id = {$raw_prefix}users.account_id " .
                             "LEFT JOIN {$db->prefix}ships on {$db->prefix}ships.player_id = {$db->prefix}players.player_id " .
                             "WHERE destroyed='N' AND " .
                             "email NOT LIKE '%@aiplayer' AND ship_id > 1");
    $rowcount = $rescount->fields;
    $topnum = min(10,$rowcount['num_players']);

    // If we have killed all the players in the game then there is no point in continuing
    if ($topnum < 1)
    {
        return;
    }

    $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_destroyed='N' and email NOT LIKE '%@aiplayer' and " .
                        "ship_id > 1 ORDER BY score DESC LIMIT ?", array($topnum));

    // Lets choose a target from the top player list
    $i=1;
    $targetnum = mt_rand(1,$topnum);
    while (!$res->EOF)
    {
        if ($i == $targetnum)
        {
            $targetinfo = $res->fields;
        }

        $i++;
        $res->MoveNext();
    }

    //  Make sure we have a target
    if (!$targetinfo)
    {
        playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "Hunt Failed: No Target ");
        return;
    }

    // Take a wormhole to target sector
    $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db->prefix}universe WHERE sector_id=?", array($targetinfo['sector']));
    $sectrow = $sectres->fields;
    $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM {$db->prefix}zones WHERE zone_id=?", array($sectrow['zone_id']));
    $zonerow = $zoneres->fields;

    // Only take a wormhole to the target sector if we can attack in the target sector
    if ($zonerow['allow_attack']== "Y")
    {
        $stamp = date("Y-m-d H-i-s");
        $query = $db->Execute("UPDATE {$db->prefix}ships SET last_login=?, turns_used=turns_used+1, sector=? " .
                 "WHERE ship_id=?", array($stamp, $targetinfo['sector'], $playerinfo['ship_id']));
        playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "Xenobe used a wormhole to warp to sector $targetinfo[sector] where he is hunting player $targetinfo[character_name].");
        if (!$move_result)
        {
            $error = $db->ErrorMsg();
            playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "Move failed with error: $error ");
            return;
        }

        // Check for sector defenses
        $resultf = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? and " .
                                 "defense_type ='F' ORDER BY quantity DESC", array($targetinfo['sector']));
        $i = 0;
        $total_sector_fighters = 0;
        if ($resultf > 0)
        {
            while(!$resultf->EOF)
            {
                $defenses[$i] = $resultf->fields;
                $total_sector_fighters += $defenses[$i]['quantity'];
                $i++;
                $resultf->MoveNext();
            }
        }

        $resultm = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? and " .
                                 "defense_type ='M'", array($targetinfo['sector']));
        $i = 0;
        $total_sector_mines = 0;
        if ($resultm > 0)
        {
            while(!$resultm->EOF)
            {
                $defenses[$i] = $resultm->fields;
                $total_sector_mines += $defenses[$i]['quantity'];
                $i++;
                $resultm->MoveNext();
            }
        }

        if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0)) // DEST LINK HAS DEFENSES
        {
            // Attack sector defenses
            $targetlink = $targetinfo['sector'];
            // Dynamic functions
            dynamic_loader ($db, "ai_tosecdef.php");
            ai_tosecdef();
        }

        if ($ai_isdead>0)
        {
            // Sector defenses killed us
            return;
        }

        // Time to attack the target
        playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "Xenobe launching an attack on $targetinfo[character_name].");

        // See if the target is on a planet
        if ($targetinfo['planet_id']>0)
        {
            // Dynamic functions
            dynamic_loader ($db, "ai_toplanet.php");
            ai_toplanet($targetinfo['planet_id'],$targetinfo['character_name']);
        }
        else
        {
            // Dynamic functions
            dynamic_loader ($db, "ai_toship.php");
            ai_toship($targetinfo['ship_id']);
        }
    }
    else
    {
        playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "Xenobe hunt failed, target $targetinfo[character_name] was in a no attack zone (sector $targetinfo[sector]).");
    }
}
?>
