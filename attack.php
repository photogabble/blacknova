<?php
// Blacknova Traders - A web-based massively multiplayer space combat
// and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: attack.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

$title = $langvars['l_att_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title);

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('attack', 'bounty', 'main',
                                'planet', 'common', 'global_includes',
                                'global_funcs', 'combat', 'footer', 'news'));
echo '<h1>' . $title . '</h1>';

$ship_id = null;
if (array_key_exists('ship_id', $_GET))
{
    $ship_id  = (int) filter_input(INPUT_GET, 'ship_id', FILTER_SANITIZE_NUMBER_INT);
}

// Kami Multi Browser Window Attack Fix
if (array_key_exists('ship_selected', $_SESSION) == false || $_SESSION['ship_selected'] != $ship_id)
{
    echo "You need to click on the ship first.<br><br>";
    Bnt\Text::gotoMain($db, $lang, $langvars);
    Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
    die();
}
unset ($_SESSION['ship_selected']);

// Need to also set a WRITE LOCK on {$db->prefix}adodb_logsql WRITE
// or it will fail to log the sql.
$result = $db->Execute(
    "LOCK TABLES {$db->prefix}adodb_logsql WRITE, {$db->prefix}languages READ, " .
    "{$db->prefix}ibank_accounts READ, {$db->prefix}sector_defence WRITE, " .
    "{$db->prefix}ships WRITE, {$db->prefix}universe WRITE, {$db->prefix}bounty WRITE, " .
    "{$db->prefix}zones READ, {$db->prefix}planets WRITE, " .
    "{$db->prefix}news WRITE, {$db->prefix}movement_log WRITE, {$db->prefix}logs WRITE;"
);
Bnt\Db::logDbErrors($db, $result, __LINE__, __FILE__);

$result = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array($_SESSION['username']));
Bnt\Db::logDbErrors($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$result2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id = ?;", array($ship_id));
Bnt\Db::logDbErrors($db, $result2, __LINE__, __FILE__);
$targetinfo = $result2->fields;

$playerscore = Bnt\Score::updateScore($db, $playerinfo['ship_id'], $bntreg);
$targetscore = Bnt\Score::updateScore($db, $targetinfo['ship_id'], $bntreg);
$playerscore = $playerscore * $playerscore;
$targetscore = $targetscore * $targetscore;

// Check to ensure target is in the same sector as player
if ($targetinfo['sector'] != $playerinfo['sector'] || $targetinfo['on_planet'] == 'Y')
{
    echo $langvars['l_att_notarg'] . '<br><br>';
}
elseif ($playerinfo['turns'] < 1)
{
    echo $langvars['l_att_noturn'] . '<br><br>';
}
elseif (Bad\Team::sameTeam($playerinfo['team'], $targetinfo['team']))
{
    echo "<div style='color:#ff0;'>" . $langvars['l_team_noattack_members'] . "</div>\n";
}
elseif (isset($_SESSION['in_combat']) && $_SESSION['in_combat'] === true)
{
    echo "<div style='color:#ff0;'>" . $langvars['l_team_already_combat'] . "</div>\n";
    Bnt\AdminLog::writeLog($db, 13371337, "{$playerinfo['ship_id']}|{$targetinfo['ship_id']}|Detected multi attack.");
}
else
{
    // Set In Combat Flag
    $_SESSION['in_combat'] = (boolean) true;

    // Determine percent chance of success in detecting target ship - based on player's sensors and opponent's cloak
    $success = (10 - $targetinfo['cloak'] + $playerinfo['sensors']) * 5;
    if ($success < 5)
    {
        $success = 5;
    }

    if ($success > 95)
    {
        $success = 95;
    }
    $flee = (10 - $targetinfo['engines'] + $playerinfo['engines']) * 5;
    $roll = Bnt\Rand::betterRand(1, 100);
    $roll2 = Bnt\Rand::betterRand(1, 100);

    $res = $db->Execute(
        "SELECT allow_attack, {$db->prefix}universe.zone_id FROM {$db->prefix}zones, " .
        "{$db->prefix}universe WHERE sector_id = ? AND {$db->prefix}zones.zone_id = " .
        "{$db->prefix}universe.zone_id;",
        array($targetinfo['sector'])
    );
    Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
    $zoneinfo = $res->fields;

    if ($zoneinfo['allow_attack'] == 'N')
    {
        echo $langvars['l_att_noatt'] . "<br><br>";
    }
    elseif ($flee < $roll2)
    {
        echo $langvars['l_att_flee'] . "<br><br>";
        $resx = $db->Execute(
            "UPDATE {$db->prefix}ships SET turns = turns - 1, turns_used = turns_used + 1 WHERE " .
            "ship_id = ?;",
            array($playerinfo['ship_id'])
        );
        Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_OUTMAN, "$playerinfo[character_name]");
    }
    elseif ($roll > $success)
    {
        // If scan fails - inform both player and target.
        echo $langvars['l_planet_noscan'] . "<br><br>";
        $resx = $db->Execute(
            "UPDATE {$db->prefix}ships SET turns = turns - 1, turns_used = turns_used + 1 WHERE " .
            "ship_id = ?;",
            array($playerinfo['ship_id'])
        );
        Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_OUTSCAN, "$playerinfo[character_name]");
    }
    else
    {
        // If scan succeeds, show results and inform target.
        $shipavg = Bnt\CalcLevels::avgTech($targetinfo, "ship");

        if ($shipavg > $ewd_maxhullsize)
        {
            $chance = ($shipavg - $ewd_maxhullsize) * 10;
        }
        else
        {
            $chance = 0;
        }
        $random_value = Bnt\Rand::betterRand(1, 100);

        if ($targetinfo['dev_emerwarp'] > 0 && $random_value > $chance)
        {
            // Need to change warp destination to random sector in universe
            $rating_change = round($targetinfo['rating'] * .1);
            $dest_sector = Bnt\Rand::betterRand(1, $sector_max - 1);
            $resx = $db->Execute(
                "UPDATE {$db->prefix}ships SET turns = turns - 1, turns_used = turns_used + 1, " .
                "rating = rating - ? " .
                "WHERE ship_id = ?;",
                array($rating_change, $playerinfo['ship_id'])
            );
            Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
            Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_EWD, "$playerinfo[character_name]");
            $result_warp = $db->Execute(
                "UPDATE {$db->prefix}ships SET sector = $dest_sector, " .
                "dev_emerwarp = dev_emerwarp - 1, cleared_defences = ' ' " .
                "WHERE ship_id = ?;",
                array($targetinfo['ship_id'])
            );
            Bnt\Db::logDbErrors($db, $result_warp, __LINE__, __FILE__);
            Bnt\LogMove::writeLog($db, $targetinfo['ship_id'], $dest_sector);
            echo $langvars['l_att_ewd'] . "<br><br>";
        }
        else
        {
            // Bounty-free Xenobe attacking allowed.
            if (($targetscore / $playerscore < $bounty_ratio || $targetinfo['turns_used'] < $bounty_minturns) && ( preg_match("/(\@xenobe)$/", $targetinfo['email']) === 0))
            {
                // Changed xenobe check to a regexp cause a player could put
                // @xen or whatever in his email address
                // so (\@xenobe) is an exact match and the $ symbol means
                // "this is the *end* of the string
                // Our custom @xenobe names will match, nothing else will
                // Check to see if there is Federation bounty on the player.
                // If there is, people can attack regardless.
                $btyamount = 0;
                $hasbounty = $db->Execute(
                    "SELECT SUM(amount) AS btytotal FROM {$db->prefix}bounty WHERE " .
                    "bounty_on = ? AND placed_by = 0;",
                    array($targetinfo['ship_id'])
                );
                Bnt\Db::logDbErrors($db, $hasbounty, __LINE__, __FILE__);
                if ($hasbounty)
                {
                    $resx = $hasbounty->fields;
                    $btyamount = $resx['btytotal'];
                }

                if ($btyamount <= 0)
                {
                    $bounty = round($playerscore * $bounty_maxvalue);
                    $insert = $db->Execute(
                        "INSERT INTO {$db->prefix}bounty (bounty_on,placed_by,amount) values " .
                        "(?,?,?);",
                        array($playerinfo['ship_id'], 0 ,$bounty)
                    );
                    Bnt\Db::logDbErrors($db, $insert, __LINE__, __FILE__);
                    Bnt\PlayerLog::writeLog($db, $playerinfo['ship_id'], LOG_BOUNTY_FEDBOUNTY, "$bounty");
                    echo "<div style='color:#f00;'>" . $langvars['l_by_fedbounty2'] . "</div>\n";
                    echo "<br>\n";
                }
            }

            if ($targetinfo['dev_emerwarp'] > 0)
            {
                Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_EWDFAIL, $playerinfo['character_name']);
            }
            $targetenergy = $targetinfo['ship_energy'];
            $playerenergy = $playerinfo['ship_energy'];
            // I added these two so we can have a value for debugging and
            // reporting totals. If we use the variables in calcs below,
            // change the display of stats too

            $targetbeams = Bnt\CalcLevels::beams($targetinfo['beams'], $level_factor);
            if ($targetbeams > $targetinfo['ship_energy'])
            {
                $targetbeams = $targetinfo['ship_energy'];
            }
            $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetbeams;
            // Why dont we set targetinfo[ship_energy] to a variable instead?

            $playerbeams = Bnt\CalcLevels::beams($playerinfo['beams'], $level_factor);
            if ($playerbeams > $playerinfo['ship_energy'])
            {
                $playerbeams = $playerinfo['ship_energy'];
            }
            $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playerbeams;

            $playershields = Bnt\CalcLevels::shields($playerinfo['shields'], $level_factor);
            if ($playershields > $playerinfo['ship_energy'])
            {
                $playershields = $playerinfo['ship_energy'];
            }
            $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playershields;

            $targetshields = Bnt\CalcLevels::shields($targetinfo['shields'], $level_factor);
            if ($targetshields > $targetinfo['ship_energy'])
            {
                $targetshields = $targetinfo['ship_energy'];
            }
            $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetshields;

            $playertorpnum = round(pow($level_factor, $playerinfo['torp_launchers'])) * 10;
            if ($playertorpnum > $playerinfo['torps'])
            {
                $playertorpnum = $playerinfo['torps'];
            }

            $targettorpnum = round(pow($level_factor, $targetinfo['torp_launchers'])) * 10;
            if ($targettorpnum > $targetinfo['torps'])
            {
                $targettorpnum = $targetinfo['torps'];
            }
            $playertorpdmg = $torp_dmg_rate * $playertorpnum;
            $targettorpdmg = $torp_dmg_rate * $targettorpnum;
            $playerarmor = $playerinfo['armor_pts'];
            $targetarmor = $targetinfo['armor_pts'];
            $playerfighters = $playerinfo['ship_fighters'];
            $targetfighters = $targetinfo['ship_fighters'];
            $targetdestroyed = 0;
            $playerdestroyed = 0;

            echo $langvars['l_att_att'] . " " . $targetinfo['character_name'] . " " . $langvars['l_aboard'] . " " . $targetinfo['ship_name'] . ":<br><br>";

            $bcs_info = null;
            $bcs_info[] = array("Beams(lvl)", "{$playerbeams}({$playerinfo['beams']})", "{$targetbeams}({$targetinfo['beams']})");
            $bcs_info[] = array("Shields(lvl)", "{$playershields}({$playerinfo['shields']})", "{$targetshields}({$targetinfo['shields']})");
            $bcs_info[] = array("Energy(Start)", "{$playerinfo['ship_energy']}({$playerenergy})", "{$targetinfo['ship_energy']}({$targetenergy})");
            $bcs_info[] = array("Torps(lvl)", "{$playertorpnum}({$playerinfo['torp_launchers']})", "{$targettorpnum}({$targetinfo['torp_launchers']})");
            $bcs_info[] = array("TorpDmg", "{$playertorpdmg}", "{$targettorpdmg}");
            $bcs_info[] = array("Fighters", "{$playerfighters}", "{$targetfighters}");
            $bcs_info[] = array("Armor(lvl)", "{$playerarmor}({$playerinfo['armor']})", "{$targetarmor}({$targetinfo['beams']})");
            $bcs_info[] = array("Escape Pod", "{$playerinfo['dev_escapepod']}", "{$targetinfo['dev_escapepod']}");

            echo "<div style='width:800px; margin:auto; text-align:center; color:#fff;'>\n";

            echo "  <div style='text-align:center; font-size:24px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Blacknova Combat System. " .
                 "(<span style='color:#0f0;'>BETA</span>)</div>\n";
            echo "  <div style='height:1px;'></div>\n";

            echo "<table style='width:100%; border:none; background-color:#FFCC00;' cellpadding='0' cellspacing='1'>\n";

            echo "  <tr style='background-color:{$color_header}; font-size:16px;'>\n";
            echo "    <td style='text-align:center; font-weight:bold;background-color:{$color_header};'>Stats</td>\n";
            echo "    <td style='width:33%; text-align:center; font-weight:bold;background-color:{$color_header};'>You [<span style='color:#0f0; font-size:12px; font-weight:normal;'>" .
                 "{$playerinfo['character_name']}</span>]</td>\n";
            echo "    <td style='width:33%; text-align:center; font-weight:bold;background-color:{$color_header};'>Target [<span style='color:#0f0; font-size:12px; font-weight:normal;'>" .
                 "{$targetinfo['character_name']}</span>]</td>\n";
            echo "  </tr>\n";

            $color = $color_line1;

            $temp_count = count($bcs_info);
            for ($bcs_index = 0; $bcs_index < $temp_count; $bcs_index++)
            {
                echo "  <tr>\n";
                echo "    <td style='text-align:right; font-weight:bold; padding:4px;background-color:{$color};'>{$bcs_info[$bcs_index][0]}</td>\n";
                echo "    <td style='width:33%; text-align:right; padding:4px;background-color:{$color};'>{$bcs_info[$bcs_index][1]}</td>\n";
                echo "    <td style='width:33%; text-align:right; padding:4px;background-color:{$color};'>{$bcs_info[$bcs_index][2]}</td>\n";
                echo "  </tr>\n";

                if ($color == $color_line1)
                {
                    $color = $color_line2;
                }
                else
                {
                    $color = $color_line1;
                }
            }
            echo "</table>\n";
            echo "  <div style='height:4px;'></div>\n";
            echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Beams</div>\n";
            echo "  <div style='height:1px;'></div>\n";
            echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";

            $bcs_stats_info = false;

            if ($targetfighters > 0 && $playerbeams > 0)
            {
                $bcs_stats_info = true;
                if ($playerbeams > round($targetfighters / 2))
                {
                    $temp = round($targetfighters / 2);
                    $lost = $targetfighters - $temp;

                    // Maybe we should report on how many beams fired , etc for
                    // comparision/bugtracking
                    echo $targetinfo['character_name'] . " " . $langvars['l_att_lost'] . " " . $lost . " " . $langvars['l_fighters'] . "<br>";
                    $targetfighters = $temp;
                    $playerbeams = $playerbeams - $lost;
                }
                else
                {
                    $targetfighters = $targetfighters - $playerbeams;
                    echo $targetinfo['character_name'] . " " . $langvars['l_att_lost'] . " " . $playerbeams . " " . $langvars['l_fighters'] . "<br>";
                    $playerbeams = 0;
                }
            }

            if ($playerfighters > 0 && $targetbeams > 0)
            {
                $bcs_stats_info = true;
                if ($targetbeams > round($playerfighters / 2))
                {
                    $temp = round($playerfighters / 2);
                    $lost = $playerfighters - $temp;
                    echo $langvars['l_att_ylost'] . " " . $lost . " " . $langvars['l_fighters'] . "<br>";
                    $playerfighters = $temp;
                    $targetbeams = $targetbeams - $lost;
                }
                else
                {
                    $playerfighters = $playerfighters - $targetbeams;
                    echo $langvars['l_att_ylost'] . " " . $targetbeams . " " . $langvars['l_fighters'] . "<br>";
                    $targetbeams = 0;
                }
            }

            if ($playerbeams > 0)
            {
                $bcs_stats_info = true;
                if ($playerbeams > $targetshields)
                {
                    $playerbeams = $playerbeams - $targetshields;
                    $targetshields = 0;
                    echo "$targetinfo[character_name]" . $langvars['l_att_sdown'] . "<br>";
                }
                else
                {
                    echo "$targetinfo[character_name]" . $langvars['l_att_shits'] . " " . $playerbeams . " " . $langvars['l_att_dmg'] . ".<br>";
                    $targetshields = $targetshields - $playerbeams;
                    $playerbeams = 0;
                }
            }

            if ($targetbeams > 0)
            {
                $bcs_stats_info = true;
                if ($targetbeams > $playershields)
                {
                    $targetbeams = $targetbeams - $playershields;
                    $playershields = 0;
                    echo $langvars['l_att_ydown'] . "<br><br>";
                }
                else
                {
                    echo $langvars['l_att_yhits'] . " " . $targetbeams . " " . $langvars['l_att_dmg'] . ".<br>";
                    $playershields = $playershields - $targetbeams;
                    $targetbeams = 0;
                }
            }

            if ($playerbeams > 0)
            {
                $bcs_stats_info = true;
                if ($playerbeams > $targetarmor)
                {
                    $targetarmor = 0;
                    echo $targetinfo['character_name'] . $langvars['l_att_sarm']  . "<br>";
                }
                else
                {
                    $targetarmor = $targetarmor - $playerbeams;
                    echo $targetinfo['character_name'] . $langvars['l_att_ashit'] ." " . $playerbeams . " " . $langvars['l_att_dmg'] . ".<br>";
                }
            }

            if ($targetbeams > 0)
            {
                $bcs_stats_info = true;
                if ($targetbeams > $playerarmor)
                {
                    $playerarmor = 0;
                    echo $langvars['l_att_yarm'] . "<br>";
                }
                else
                {
                    $playerarmor = $playerarmor - $targetbeams;
                    echo $langvars['l_att_ayhit'] . " " . $targetbeams . " " . $langvars['l_att_dmg'] . ".<br>";
                }
            }

            if ($bcs_stats_info == false)
            {
                echo "No information available.<br>\n";
            }

            echo "  </div>\n";
            echo "  <div style='height:4px;'></div>\n";
            echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Torpedos</div>\n";
            echo "  <div style='height:1px;'></div>\n";
            echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";
            $bcs_stats_info = false;

            if ($targetfighters > 0 && $playertorpdmg > 0)
            {
                $bcs_stats_info = true;
                if ($playertorpdmg > round($targetfighters / 2))
                {
                    $temp = round($targetfighters / 2);
                    $lost = $targetfighters - $temp;
                    echo $targetinfo['character_name'] . " " . $langvars['l_att_lost'] . " " . $lost . " " . $langvars['l_fighters'] . "<br>";
                    $targetfighters = $temp;
                    $playertorpdmg = $playertorpdmg - $lost;
                }
                else
                {
                    $targetfighters = $targetfighters - $playertorpdmg;
                    echo $targetinfo['character_name'] . " " .  $langvars['l_att_lost'] . " " . $playertorpdmg . " " . $langvars['l_fighters'] . "<br>";
                    $playertorpdmg = 0;
                }
            }

            if ($playerfighters > 0 && $targettorpdmg > 0)
            {
                $bcs_stats_info = true;
                if ($targettorpdmg > round($playerfighters / 2))
                {
                    $temp = round($playerfighters / 2);
                    $lost = $playerfighters - $temp;
                    echo $langvars['l_att_ylost'] . " " . $lost . " " . $langvars['l_fighters'] . "<br>";
                    echo "$temp - $playerfighters - $targettorpdmg";
                    $playerfighters = $temp;
                    $targettorpdmg = $targettorpdmg - $lost;
                }
                else
                {
                    $playerfighters = $playerfighters - $targettorpdmg;
                    echo $langvars['l_att_ylost'] . " " . $targettorpdmg . " " . $langvars['l_fighters'] . "<br>";
                    $targettorpdmg = 0;
                }
            }

            if ($playertorpdmg > 0)
            {
                $bcs_stats_info = true;
                if ($playertorpdmg > $targetarmor)
                {
                    $targetarmor=0;
                    echo "$targetinfo[character_name]" . $langvars['l_att_sarm'] . "<br>";
                }
                else
                {
                    $targetarmor = $targetarmor - $playertorpdmg;
                    echo "$targetinfo[character_name]" . $langvars['l_att_ashit'] . " " . $playertorpdmg . " " . $langvars['l_att_dmg'] . ".<br>";
                }
            }

            if ($targettorpdmg > 0)
            {
                $bcs_stats_info = true;
                if ($targettorpdmg > $playerarmor)
                {
                    $playerarmor = 0;
                    echo $langvars['l_att_yarm'] . "<br>";
                }
                else
                {
                    $playerarmor = $playerarmor - $targettorpdmg;
                    echo $langvars['l_att_ayhit'] . " " . $targettorpdmg . " " . $langvars['l_att_dmg'] . ".<br>";
                }
            }

            if ($bcs_stats_info == false)
            {
                echo "No information available.<br>\n";
            }

            echo "  </div>\n";
            echo "  <div style='height:4px;'></div>\n";
            echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Fighters</div>\n";
            echo "  <div style='height:1px;'></div>\n";
            echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";
            $bcs_stats_info = false;

            if ($playerfighters > 0 && $targetfighters > 0)
            {
                $bcs_stats_info = true;
                if ($playerfighters > $targetfighters)
                {
                    echo $targetinfo['character_name'] . " " . $langvars['l_att_lostf'] . "<br>";
                    $temptargfighters = 0;
                }
                else
                {
                    echo $targetinfo['character_name'] . " " . $langvars['l_att_lost'] . " " . $playerfighters . " " . $langvars['l_fighters'] . ".<br>";
                    $temptargfighters = $targetfighters - $playerfighters;
                }

                if ($targetfighters > $playerfighters)
                {
                    echo $langvars['l_att_ylostf'] ."<br>";
                    $tempplayfighters = 0;
                }
                else
                {
                    echo $langvars['l_att_ylost'] . " " . $targetfighters . " " . $langvars['l_fighters'] . ".<br>";
                    $tempplayfighters = $playerfighters - $targetfighters;
                }
                $playerfighters = $tempplayfighters;
                $targetfighters = $temptargfighters;
            }

            if ($playerfighters > 0)
            {
                $bcs_stats_info = true;
                if ($playerfighters > $targetarmor)
                {
                    $targetarmor = 0;
                    echo $targetinfo['character_name'] . " " . $langvars['l_att_sarm'] . "<br>";
                }
                else
                {
                    $targetarmor = $targetarmor - $playerfighters;
                    echo $targetinfo['character_name'] . " " . $langvars['l_att_ashit'] ." " . $playerfighters . " " . $langvars['l_att_dmg'] . ".<br>";
                }
            }

            if ($targetfighters > 0)
            {
                $bcs_stats_info = true;
                if ($targetfighters > $playerarmor)
                {
                    $playerarmor = 0;
                    echo $langvars['l_att_yarm'] . "<br>";
                }
                else
                {
                    $playerarmor = $playerarmor - $targetfighters;
                    echo $langvars['l_att_ayhit'] . " " . $targetfighters . " " . $langvars['l_att_dmg'] . ".<br>";
                }
            }

            if ($bcs_stats_info == false)
            {
                echo "No information available.<br>\n";
            }
            echo "  </div>\n";
            echo "  <div style='height:4px;'></div>\n";
            echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Outcome</div>\n";
            echo "  <div style='height:1px;'></div>\n";
            echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";

            if ($targetarmor < 1)
            {
                echo $targetinfo['character_name'] . " " . $langvars['l_att_sdest'] . "<br>";
                if ($targetinfo['dev_escapepod'] == "Y")
                {
                    $rating = round($targetinfo['rating'] / 2);
                    echo $langvars['l_att_espod'] . " (<span style='color:#ff0;'>You destroyed their ship but they got away in their Escape Pod</span>)<br>";
                    $resx = $db->Execute(
                        "UPDATE {$db->prefix}ships SET hull = 0, engines = 0, power = 0, sensors = 0, computer = 0, beams = 0, torp_launchers = 0, " .
                        "torps = 0, armor = 0, armor_pts = 100, cloak = 0, shields = 0, sector = 0, ship_organics = 0, ship_ore = 0, ship_goods = 0, " .
                        "ship_energy = ?, ship_colonists = 0, ship_fighters = 100, dev_warpedit = 0, dev_genesis = 0, dev_beacon = 0, dev_emerwarp = 0, " .
                        "dev_escapepod = 'N', dev_fuelscoop = 'N', dev_minedeflector = 0, on_planet = 'N', rating = ?, cleared_defences = ' ', " .
                        "dev_lssd = 'N' WHERE ship_id = ?;",
                        array($bntreg->start_energy, $rating, $targetinfo['ship_id'])
                    );
                    Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
                    Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
                    Bnt\Bounty::collect($db, $langvars, $playerinfo['ship_id'], $targetinfo['ship_id']);
                    Bnt\AdminLog::writeLog($db, 950, "*|{$playerinfo['ship_id']}|{$targetinfo['ship_id']}|Just lost the Escape Pod.");
                }
                else
                {
                    Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
                    Bnt\Character::kill($db, $targetinfo['ship_id'], $langvars, $bntreg, false);
                    Bnt\Bounty::collect($db, $langvars, $playerinfo['ship_id'], $targetinfo['ship_id']);
                    Bnt\AdminLog::writeLog($db, 950, "*|{$playerinfo['ship_id']}|{$targetinfo['ship_id']}|Didn't have the Escape Pod.");
                }

                if ($playerarmor > 0)
                {
                    $rating_change = round($targetinfo['rating'] * $rating_combat_factor);
                    // Updating to always get a positive rating increase for
                    // xenobe and the credits they are carrying
                    $salv_credits = 0;

                    // Double Death Attack Bug Fix - Returns 0 for real
                    // players, 1 for Xenobe players
                    // He is a Xenobe
                    if (preg_match("/(\@xenobe)$/", $targetinfo['email']) !== 0)
                    {
                        $resx = $db->Execute("UPDATE {$db->prefix}xenobe SET active= N WHERE xenobe_id = ?;", array($targetinfo['email']));
                        Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);

                        Bnt\AdminLog::writeLog($db, 950, "*|{$playerinfo['ship_id']}|{$targetinfo['ship_id']}|Detected as AI.");

                        if ($rating_change > 0)
                        {
                            $rating_change = 0 - $rating_change;
                            Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
                            Bnt\Bounty::collect($db, $langvars, $playerinfo['ship_id'], $targetinfo['ship_id']);
                            Bnt\Character::kill($db, $targetinfo['ship_id'], $langvars, $bntreg, false);

                            Bnt\AdminLog::writeLog($db, 950, "*|{$playerinfo['ship_id']}|{$targetinfo['ship_id']}|Hope fully we only killed off the AI.");

                        }
                        $salv_credits = $targetinfo['credits'];
                    }

                    $free_ore = round($targetinfo['ship_ore'] / 2);
                    $free_organics = round($targetinfo['ship_organics'] / 2);
                    $free_goods = round($targetinfo['ship_goods'] / 2);
                    $free_holds = Bnt\CalcLevels::holds($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
                    if ($free_holds > $free_goods)
                    {
                        $salv_goods = $free_goods;
                        $free_holds = $free_holds - $free_goods;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_goods = $free_holds;
                        $free_holds = 0;
                    }
                    else
                    {
                        $salv_goods = 0;
                    }
                    if ($free_holds > $free_ore)
                    {
                        $salv_ore = $free_ore;
                        $free_holds = $free_holds - $free_ore;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_ore = $free_holds;
                        $free_holds = 0;
                    }
                    else
                    {
                        $salv_ore = 0;
                    }
                    if ($free_holds > $free_organics)
                    {
                        $salv_organics = $free_organics;
                        $free_holds = $free_holds - $free_organics;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_organics = $free_holds;
                        $free_holds = 0;
                    }
                    else
                    {
                        $salv_organics = 0;
                    }
                    $ship_value = $upgrade_cost * (round(pow($upgrade_factor, $targetinfo['hull'])) + round(pow($upgrade_factor, $targetinfo['engines'])) + round(pow($upgrade_factor, $targetinfo['power'])) + round(pow($upgrade_factor, $targetinfo['computer'])) + round(pow($upgrade_factor, $targetinfo['sensors'])) + round(pow($upgrade_factor, $targetinfo['beams'])) + round(pow($upgrade_factor, $targetinfo['torp_launchers'])) + round(pow($upgrade_factor, $targetinfo['shields'])) + round(pow($upgrade_factor, $targetinfo['armor'])) + round(pow($upgrade_factor, $targetinfo['cloak'])));
                    $ship_salvage_rate = Bnt\Rand::betterRand(10, 20);
                    $ship_salvage = $ship_value * $ship_salvage_rate / 100 + $salv_credits;  // Added credits for xenobe - 0 if normal player

                    $langvars['l_att_ysalv'] = str_replace("[salv_ore]", $salv_ore, $langvars['l_att_ysalv']);
                    $langvars['l_att_ysalv'] = str_replace("[salv_organics]", $salv_organics, $langvars['l_att_ysalv']);
                    $langvars['l_att_ysalv'] = str_replace("[salv_goods]", $salv_goods, $langvars['l_att_ysalv']);
                    $langvars['l_att_ysalv'] = str_replace("[ship_salvage_rate]", $ship_salvage_rate, $langvars['l_att_ysalv']);
                    $langvars['l_att_ysalv'] = str_replace("[ship_salvage]", $ship_salvage, $langvars['l_att_ysalv']);
                    $langvars['l_att_ysalv2'] = str_replace("[rating_change]", number_format(abs($rating_change), 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_att_ysalv2']);

                    echo $langvars['l_att_ysalv'] . "<br>" . $langvars['l_att_ysalv2'] . "<br>\n";
                    $update3 = $db->Execute(
                        "UPDATE {$db->prefix}ships SET ship_ore = ship_ore + ?, ship_organics = ship_organics + ?, ship_goods = ship_goods + ?, " .
                        "credits = credits + ? WHERE ship_id = ?;",
                        array($salv_ore, $salv_organics, $salv_goods, $ship_salvage, $playerinfo['ship_id'])
                    );
                    Bnt\Db::logDbErrors($db, $update3, __LINE__, __FILE__);
                    $armor_lost = $playerinfo['armor_pts'] - $playerarmor;
                    $fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
                    $energy = $playerinfo['ship_energy'];
                    $update3b = $db->Execute(
                        "UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, armor_pts = armor_pts - ?, torps = torps - ?, " .
                        "turns = turns - 1, turns_used = turns_used + 1, rating = rating - ? " .
                        "WHERE ship_id = ?;",
                        array($energy, $fighters_lost, $armor_lost, $playertorpnum, $rating_change, $playerinfo['ship_id'])
                    );
                    Bnt\Db::logDbErrors($db, $update3b, __LINE__, __FILE__);
                    echo $langvars['l_att_ylost'] . " " . $armor_lost . " " . $langvars['l_armorpts'], $fighters_lost . " " . $langvars['l_fighters'], $langvars['l_att_andused'] . " " . $playertorpnum . " " . $langvars['l_torps'] . ".<br>";
                }
            }
            else
            {
                $langvars['l_att_stilship'] = str_replace("[name]", $targetinfo['character_name'], $langvars['l_att_stilship']);
                echo $langvars['l_att_stilship'] . "<br>";

                $rating_change = round($targetinfo['rating'] * .1);
                $armor_lost = $targetinfo['armor_pts'] - $targetarmor;
                $fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
                $energy = $targetinfo['ship_energy'];

                Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACKED_WIN, "$playerinfo[character_name]|$armor_lost|$fighters_lost");
                $update4 = $db->Execute(
                    "UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, armor_pts = armor_pts - ?, " .
                    "torps = torps - ? WHERE ship_id = ?;",
                    array($energy, $fighters_lost, $armor_lost, $targettorpnum, $targetinfo['ship_id'])
                );
                Bnt\Db::logDbErrors($db, $update4, __LINE__, __FILE__);

                $armor_lost = $playerinfo['armor_pts'] - $playerarmor;
                $fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
                $energy = $playerinfo['ship_energy'];

                $update4b = $db->Execute(
                    "UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, armor_pts = armor_pts - ?, torps = torps - ?, " .
                    "turns = turns - 1, turns_used = turns_used + 1, rating = rating - ? " .
                    "WHERE ship_id = ?;",
                    array($energy, $fighters_lost, $armor_lost, $playertorpnum, $rating_change, $playerinfo['ship_id'])
                );
                Bnt\Db::logDbErrors($db, $update4b, __LINE__, __FILE__);
                echo $langvars['l_att_ylost'] . " " . $armor_lost . " " . $langvars['l_armorpts'], $fighters_lost . " " . $langvars['l_fighters'], $langvars['l_att_andused'] . " " . $playertorpnum . " " . $langvars['l_torps'] . ".<br><br>";
            }

            if ($playerarmor < 1)
            {
                echo $langvars['l_att_yshiplost'] . "<br><br>";
                if ($playerinfo['dev_escapepod'] == "Y")
                {
                    $rating = round($playerinfo['rating'] / 2);
                    echo $langvars['l_att_loosepod'] . "<br><br>";
                    $resx = $db->Execute(
                        "UPDATE {$db->prefix}ships SET hull = 0, engines = 0, power = 0, sensors = 0, computer = 0, beams = 0, torp_launchers = 0, torps = 0, " .
                        "armor = 0, armor_pts = 100, cloak = 0, shields = 0, sector = 0, ship_organics = 0, ship_ore = 0, ship_goods = 0, ship_energy = ?, " .
                        "ship_colonists = 0, ship_fighters = 100, dev_warpedit = 0, dev_genesis = 0, dev_beacon = 0, dev_emerwarp = 0, dev_escapepod = 'N', " .
                        "dev_fuelscoop = 'N', dev_minedeflector = 0, on_planet = 'N', rating = ?, dev_lssd = 'N' " .
                        "WHERE ship_id = ?",
                        array($bntreg->start_energy, $rating, $playerinfo['ship_id'])
                    );
                    Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
                    Bnt\Bounty::collect($db, $langvars, $targetinfo['ship_id'], $playerinfo['ship_id']);
                }
                else
                {
                    echo "Didnt have pod?! $playerinfo[dev_escapepod]<br>";
                    Bnt\Character::kill($db, $playerinfo['ship_id'], $langvars, $bntreg, false);
                    Bnt\Bounty::collect($db, $langvars, $targetinfo['ship_id'], $playerinfo['ship_id']);
                }

                if ($targetarmor > 0)
                {
                    $salv_credits = 0;

                    $free_ore = round($playerinfo['ship_ore'] / 2);
                    $free_organics = round($playerinfo['ship_organics']/2);
                    $free_goods = round($playerinfo['ship_goods']/2);
                    $free_holds = Bnt\CalcLevels::holds($targetinfo['hull'], $level_factor) - $targetinfo['ship_ore'] - $targetinfo['ship_organics'] - $targetinfo['ship_goods'] - $targetinfo['ship_colonists'];
                    if ($free_holds > $free_goods)
                    {
                        $salv_goods = $free_goods;
                        $free_holds = $free_holds - $free_goods;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_goods = $free_holds;
                        $free_holds = 0;
                    }
                    else
                    {
                        $salv_goods = 0;
                    }

                    if ($free_holds > $free_ore)
                    {
                        $salv_ore = $free_ore;
                        $free_holds = $free_holds - $free_ore;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_ore = $free_holds;
                        $free_holds = 0;
                    }
                    else
                    {
                        $salv_ore = 0;
                    }

                    if ($free_holds > $free_organics)
                    {
                        $salv_organics = $free_organics;
                        $free_holds = $free_holds - $free_organics;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_organics = $free_holds;
                        $free_holds = 0;
                    }
                    else
                    {
                        $salv_organics = 0;
                    }
                    $ship_value = $upgrade_cost * (round(pow($upgrade_factor, $playerinfo['hull'])) + round(pow($upgrade_factor, $playerinfo['engines'])) + round(pow($upgrade_factor, $playerinfo['power'])) + round(pow($upgrade_factor, $playerinfo['computer'])) + round(pow($upgrade_factor, $playerinfo['sensors'])) + round(pow($upgrade_factor, $playerinfo['beams'])) + round(pow($upgrade_factor, $playerinfo['torp_launchers'])) + round(pow($upgrade_factor, $playerinfo['shields'])) + round(pow($upgrade_factor, $playerinfo['armor'])) + round(pow($upgrade_factor, $playerinfo['cloak'])));
                    $ship_salvage_rate = Bnt\Rand::betterRand(10, 20);
                    $ship_salvage = $ship_value * $ship_salvage_rate / 100 + $salv_credits;  // Added credits for xenobe - 0 if normal player

                    $langvars['l_att_salv'] = str_replace("[salv_ore]", $salv_ore, $langvars['l_att_salv']);
                    $langvars['l_att_salv'] = str_replace("[salv_organics]", $salv_organics, $langvars['l_att_salv']);
                    $langvars['l_att_salv'] = str_replace("[salv_goods]", $salv_goods, $langvars['l_att_salv']);
                    $langvars['l_att_salv'] = str_replace("[ship_salvage_rate]", $ship_salvage_rate, $langvars['l_att_salv']);
                    $langvars['l_att_salv'] = str_replace("[ship_salvage]", $ship_salvage, $langvars['l_att_salv']);
                    $langvars['l_att_salv'] = str_replace("[name]", $targetinfo['character_name'], $langvars['l_att_salv']);

                    echo $langvars['l_att_salv'] . "<br>";
                    $update6 = $db->Execute(
                        "UPDATE {$db->prefix}ships SET credits = credits + ?, ship_ore = ship_ore + ?, ship_organics = ship_organics + ?, " .
                        "ship_goods = ship_goods + ? WHERE ship_id = ?;",
                        array($ship_salvage, $salv_ore, $salv_organics, $salv_goods, $targetinfo['ship_id'])
                    );
                    Bnt\Db::logDbErrors($db, $update6, __LINE__, __FILE__);
                    $armor_lost = $targetinfo['armor_pts'] - $targetarmor;
                    $fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
                    $energy = $targetinfo['ship_energy'];
                    $update6b = $db->Execute(
                        "UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, armor_pts = armor_pts - ?, torps = torps - ? " .
                        "WHERE ship_id = ?;",
                        array($energy, $fighters_lost, $armor_lost, $targettorpnum, $targetinfo['ship_id'])
                    );
                    Bnt\Db::logDbErrors($db, $update6b, __LINE__, __FILE__);
                }
            }

            echo "  </div>\n";
            echo "  <div style='height:1px;'></div>\n";
            echo "  <div style='text-align:right; font-size:10px; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Layout created by TheMightyDude</div>\n";
            echo "</div>\n";
        }
    }
}
$resx = $db->Execute('UNLOCK TABLES');
Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);

$_SESSION['in_combat'] = (boolean) false;

Bnt\Text::gotoMain($db, $lang, $langvars);
Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
