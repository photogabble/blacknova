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
// File: inclues/change_planet_production.php

function change_planet_production($prodpercentarray)
{
//  NOTES on what this function does and how
//  Declares some global variables so they are accessable
//    $db, and default production values from the global_includes file
//
//  We need to track what the player_id is and what team they belong to if they belong to a team,
//    these two values are not passed in as arrays
//    player_id = the owner of the planet          ($player_id = $prodpercentarray[player_id])
//    team_id = the teameration creators player_id ($team_id = $prodpercentarray[team_id])
//
//  First we generate a list of values based on the commodity
//    (ore, organics, goods, energy, fighters, torps, team, team, sells)
//
//  Second we generate a second list of values based on the planet_id
//  Because team and player_id are not arrays we do not pass them through the second list command.
//  When we write the ore production percent we also clear the selling and team values out of the db
//  When we pass through the team array we set the value to $team we grabbed out of the array.
//  in the sells and team the prodpercent = the planet_id.
//
//  We run through the database checking to see if any planet production is greater than 100, or possibly negative
//    if so we set the planet to the default values and report it to the player.
//
//  There has got to be a better way, but at this time I am not sure how to do it.
//  Off the top of my head if we could sort the data passed in, in order of planets we could check before we do the writes
//    This would save us from having to run through the database a second time checking our work.
//

    global $db;
    global $default_prod_ore, $default_prod_organics, $default_prod_goods, $default_prod_energy, $default_prod_fighters;
    global $default_prod_torp;
    global $l_unnamed, $l_pr_changeprods, $l_pr_ppupdated, $l_pr_prexeedcheck, $l_pr_prexeeds;
    global $playerinfo;

    $player_id = $playerinfo['player_id'];
    $team_id = $prodpercentarray['team_id']; // This needs to come from the DB, not the player url!!!

    echo "<br><a href=planet_production_change.php>$l_pr_changeprods</a><br><br>";

    while (list($commod_type, $valarray) = each($prodpercentarray))
    {
        if ($commod_type != "team_id" && $commod_type != "player_id")
        {
            while (list($planet_id, $prodpercent) = each($valarray))
            {
                $debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=$planet_id");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $planetinfo = $debug_query->fields;

                if ($planetinfo['owner'] == $playerinfo['player_id'])
                {
                    if ($commod_type == "prod_ore" || $commod_type == "prod_organics" || $commod_type=="prod_goods" || $commod_type=="prod_energy" || $commod_type=="prod_torp" || $commod_type =="prod_fighters")
                    {
                        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET $commod_type=$prodpercent WHERE planet_id=$planet_id");
                        db_op_result($db,$debug_query,__LINE__,__FILE__);
                    }
                    elseif ($commod_type == "sells")
                    {
                        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET sells='Y' WHERE planet_id=$prodpercent");
                        db_op_result($db,$debug_query,__LINE__,__FILE__);
                    }
                    elseif ($commod_type == "team")
                    {
                        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team=$team_id WHERE planet_id=$prodpercent");
                        db_op_result($db,$debug_query,__LINE__,__FILE__);
                    }
                }
                else
                {
                    // TODO: LOG player attempted cheat
                }
            }
        }
    }

    echo "<br>";
    echo "$l_pr_ppupdated <br><br>";
    echo "$l_pr_prexeedcheck <br><br>";

    $res = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE owner=$player_id ORDER BY sector_id");
    $i = 0;
    if ($res)
    {
        while (!$res->EOF)
        {
            $planets[$i] = $res->fields;
            $i++;
            $res->MoveNext();
        }
    }

    if ($i > 0)
    {
        foreach($planets as $planet)
        {
            if (empty($planet['name']))
            {
                $planet['name'] = $l_unnamed;
            }

            if ($planet['prod_ore'] < 0)
            {
                $planet['prod_ore'] = 110;
            }

            if ($planet['prod_organics'] < 0)
            {
                $planet['prod_organics'] = 110;
            }

            if ($planet['prod_goods'] < 0)
            {
                $planet['prod_goods'] = 110;
            }

            if ($planet['prod_energy'] < 0)
            {
                $planet['prod_energy'] = 110;
            }

            if ($planet['prod_fighters'] < 0)
            {
                $planet['prod_fighters'] = 110;
            }

            if ($planet['prod_torp'] < 0)
            {
                $planet['prod_torp'] = 110;
            }

            if ($planet['prod_ore'] + $planet['prod_organics'] + $planet['prod_goods'] + $planet['prod_energy'] + $planet['prod_fighters'] + $planet['prod_torp'] > 100)
            {
                $l_pr_prexeeds2 = str_replace("[name]", $planet['name'], $l_pr_prexeeds);
                $l_pr_prexeeds2 = str_replace("[sector_id]", $planet['sector_id'], $l_pr_prexeeds2);

                echo "$l_pr_prexeeds2<br>";
                $debug_query =$db->Execute("UPDATE {$db->prefix}planets SET prod_ore=$default_prod_ore " .
                                           "WHERE planet_id=$planet[planet_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET prod_organics=$default_prod_organics " .
                                            "WHERE planet_id=$planet[planet_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET prod_goods=$default_prod_goods " .
                                            "WHERE planet_id=$planet[planet_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET prod_energy=$default_prod_energy " .
                                            "WHERE planet_id=$planet[planet_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET prod_fighters=$default_prod_fighters " .
                                            "WHERE planet_id=$planet[planet_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET prod_torp=$default_prod_torp " .
                                            "WHERE planet_id=$planet[planet_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }
    }

    echo "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
}
?>
