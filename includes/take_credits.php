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
// File: includes/take_credits.php

function take_credits($sector_id, $planet_id)
{
    global $db, $shipinfo, $playerinfo, $l_unnamed;
    global $l_pr_credstaken, $l_pr_credsonboard, $l_planet2_notowner, $l_planet2_sector, $l_pr_notturns;

    // Dynamic functions 
    include_once ("./get_info.php");

    // Get basic Database information (ship, player and planet)
    get_info($db);

    $res = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=$planet_id");
    $planetinfo = $res->fields;

    // Set the name for unamed planets to be "unnamed"
    if (empty($planetinfo['name']))
    {
        $planet['name'] = $l_unnamed;
    }

    // Verify player is still in same sector as the planet
    if ($shipinfo['sector_id'] == $planetinfo['sector_id'])
    {
        if ($playerinfo['turns'] >= 1)
        {
            // verify player owns the planet to take credits from
            if ($planetinfo['owner'] == $playerinfo['player_id'])
            {
                // get number of credits from the planet and current number player has on ship
                $CreditsTaken = $planetinfo['credits'];
                $credits_on_ship = $playerinfo['credits'];
//                $NewShipCredits = $CreditsTaken + $credits_on_ship;

                // update the planet record for credits -- dont set to zero, as an update may have occured during.
                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits-$CreditsTaken WHERE planet_id=$planetinfo[planet_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                // update the player record
                // credits & turns
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits+$CreditsTaken, turns=turns-1 WHERE player_id='$playerinfo[player_id]'");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                get_info($db);
                $l_pr_credstaken2 = str_replace("[CreditsTaken]", number_format($CreditsTaken, 0, $local_number_dec_point, $local_number_thousands_sep), $l_pr_credstaken);
                $l_pr_credstaken2 = str_replace("[name]", $planetinfo['name'], $l_pr_credstaken2);
        
                $l_pr_credsonboard2 = str_replace("[name]", "<strong>" . $shipinfo['name'] . "</strong>", $l_pr_credsonboard);
                $l_pr_credsonboard2 = str_replace("[NewShipCredits]", number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep), $l_pr_credsonboard2);
        
                echo "$l_pr_credstaken2 <br>";
                echo "$l_pr_credsonboard2 <br>";

                $retval = "GO";
            }
            else
            {
                echo "<br><br>$l_planet2_notowner $planetinfo[name]<br><br>";
                $retval = "GO";
            }
        }
        else
        {
            $l_pr_notturns2 = str_replace("[name]", $planetinfo[name], $l_pr_notturns);
            $l_pr_notturns2 = str_replace("[sector_id]", $planetinfo['sector_id'], $l_pr_notturns2);
            echo "<br><br>$l_pr_notturns2<br><br>";
            $retval = "BREAK-TURNS";
        }
    }
    else
    {
        echo "<br><br>$l_planet2_sector<br><br>";
        $retval = "BREAK-SECTORS";
    }

    return $retval;
}
?>
