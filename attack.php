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
// File: attack.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "playerdeath.php"); 
dynamic_loader ($db, "log_move.php"); 
dynamic_loader ($db, "num_level.php"); 
dynamic_loader ($db, "gen_score.php"); 
dynamic_loader ($db, "seed_mt_rand.php"); 
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'attack');
load_languages($db, $raw_prefix, 'bounty');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_att_title;
updatecookie($db);

if (!isset($player_id))
{
    $player_id = '';
}

if (!isset($ship_id))
{
    $ship_id = '';
}

//-------------------------------------------------------------------------------------------------

//$debug_query = $db->Execute("LOCK TABLES {$db->prefix}players WRITE, {$db->prefix}ships WRITE, {$db->prefix}universe WRITE, {$db->prefix}bounty WRITE, {$db->prefix}zones READ, {$db->prefix}planets WRITE, {$db->prefix}news WRITE, {$db->prefix}spies WRITE, {$db->prefix}sessions WRITE, {$db->prefix}logs WRITE, {$db->prefix}movement_log WRITE, {$db->prefix}ibank_accounts WRITE, {$db->prefix}planet_log WRITE, {$db->prefix}sector_defense WRITE");
//db_op_result($db,$debug_query,__LINE__,__FILE__);

$player_id = preg_replace('/[^0-9]/','',$player_id);
$ship_id = preg_replace('/[^0-9]/','',$ship_id);

$result2 = $db->Execute ("SELECT * FROM {$db->prefix}players WHERE player_id='?'", array($player_id));
$targetinfo = $result2->fields;

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id='?'", array($ship_id));
$targetship = $result->fields;

$playerscore = gen_score($db,$playerinfo['player_id']);
$targetscore = gen_score($db,$targetinfo['player_id']);

$playerscore = $playerscore * $playerscore;
$targetscore = $targetscore * $targetscore;

echo "<h1>" . $title. "</h1>\n";
seed_mt_rand();

if ($targetship['sector_id'] != $shipinfo['sector_id'] || $targetship['on_planet'] == "Y")
{
    echo "$l_att_notarg<br><br>";
}
elseif ($playerinfo['turns'] < 1)
{
    echo "$l_att_noturn<br><br>";
}
else
{
    // determine percent chance of success in detecting target ship - based on player's sensors and opponent's cloak
    $success = (10 - $targetship['cloak'] + $shipinfo['sensors']) * 5;
    if ($success < 5)
    {
        $success = 5;
    }

    if ($success > 95)
    {
        $success = 95;
    }
    $flee = (10 - $targetship['engines'] + $shipinfo['engines']) * 5;
    $roll = mt_rand(1, 100);
    $roll2 = mt_rand(1, 100);

    $res = $db->Execute("SELECT allow_attack,{$db->prefix}universe.zone_id FROM {$db->prefix}zones,{$db->prefix}universe WHERE sector_id='?' AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id", array($targetship['sector_id']));
    $query97 = $res->fields;
    if ($query97['allow_attack'] == 'N')
    {
        echo "$l_att_noatt<br><br>";
    }
    elseif ($flee < $roll2)
    {
        echo "$l_att_flee<br><br>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1,turns_used=turns_used+1 WHERE player_id='?'", array($playerinfo['player_id']);
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        playerlog($db,$targetinfo['player_id'], "LOG_ATTACK_OUTMAN", "$playerinfo[character_name]");
    }
    elseif ($roll > $success)
    {
        // if scan fails - inform both player and target.
        echo "$l_planet_noscan<br><br>";

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1,turns_used=turns_used+1 WHERE player_id='?'", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        playerlog($db,$targetinfo['player_id'], "LOG_ATTACK_OUTSCAN", "$playerinfo[character_name]");
    }
    else
    {
        // if scan succeeds, show results and inform target.
        if ($plasma_engines)
        {
            $shipavg = $targetship['hull'] + $targetship['engines'] + $targetship['pengines'] + $targetship['power'] + $targetship['computer'] + $targetship['sensors'] + $targetship['armor'] + $targetship['shields'] + $targetship['beams'] + $targetship['torp_launchers'] + $targetship['cloak'];
        }
        else
        {
            $shipavg = $targetship['hull'] + $targetship['engines'] + $targetship['power'] + $targetship['computer'] + $targetship['sensors'] + $targetship['armor'] + $targetship['shields'] + $targetship['beams'] + $targetship['torp_launchers'] + $targetship['cloak'];
        }

        $shipavg /= 11;
        if ($shipavg > $ewd_maxavgtechlevel)
        {
            $chance = ($shipavg - $ewd_maxavgtechlevel) * 10;
        }
        else
        {
            $chance = 0;
        }

        $random_value = mt_rand(1,100);
        if ($targetship['dev_emerwarp'] > 0 && $random_value > $chance)
        {
            // need to change warp destination to random sector in universe
            $rating_change=round($targetinfo['rating']*.1);
            $source_sector = $shipinfo['sector_id'];
            $dest_sector = mt_rand(1,$sector_max);

            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1,turns_used=turns_used+1,rating=rating-$rating_change WHERE player_id='?'", array($playerinfo['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            playerlog($db,$targetinfo['player_id'], "LOG_ATTACK_EWD", "$playerinfo[character_name]");

            $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET sector_id='?', dev_emerwarp=dev_emerwarp-1,cleared_defenses=' ' WHERE ship_id='?'", array($dest_sector, $ship_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            log_move($db, $targetinfo['player_id'],$targetship['ship_id'],$source_sector,$dest_sector,$shipinfo['class'],$shipinfo['cloak']);
            echo "$l_att_ewd<br><br>";
        }
        else
        {
            if ($playerscore == 0) 
            {
                $playerscore = 1;
            }

            if ((($targetscore / $playerscore < $bounty_ratio) || $targetinfo['turns_used'] < $bounty_minturns) && !("aiplayer" == substr($targetinfo['email'], -8) ) )
            {
                // Check to see if there is Federation bounty on the player. If there is, people can attack regardless.
                $btyamount = 0;
                $hasbounty = $db->Execute("SELECT SUM(amount) AS btytotal FROM {$db->prefix}bounty WHERE bounty_on='?' AND placed_by = 0", array($targetinfo['player_id']));
                if ($hasbounty)
                {
                    $resx = $hasbounty->fields;
                    $btyamount = $resx['btytotal'];
                }

                if ($btyamount <= 0)
                {
                    $big_bounty = ROUND(sqrt($playerscore - $targetscore));
                    $bounty = ROUND($playerscore * $bounty_maxvalue);
                    if (($bounty < $big_bounty) && $enable_big_bounty)
                    {
                        $bounty = $big_bounty;
                    }

                    $debug_query = $db->Execute("INSERT INTO {$db->prefix}bounty (bounty_on, placed_by, amount) values " .
                                                "(?,?,?)", array($playerinfo['player_id'], 0 ,$bounty));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);                    

                    $bounty = number_format($bounty, 0, $local_number_dec_point, $local_number_thousands_sep);
                    playerlog($db,$playerinfo['player_id'], "LOG_BOUNTY_FEDBOUNTY","$bounty");
                    echo $l_by_fedbounty2 . "<br><br>";
                }
            }

            if ($targetship['dev_emerwarp'] > 0)
            {
                playerlog($db,$targetinfo['player_id'], "LOG_ATTACK_EWDFAIL", $playerinfo['character_name']);
            }

            $targetbeams = num_level($targetship['beams'], $level_factor, $level_magnitude);
            if ($targetbeams>$targetship['energy'])
            {
                $targetbeams=$targetship['energy'];
            }

            $targetship['energy']=$targetship['energy']-$targetbeams;
            $playerbeams = num_level($shipinfo['beams'], $level_factor, $level_magnitude);
            if ($playerbeams>$shipinfo['energy'])
            {
                $playerbeams=$shipinfo['energy'];
            }

            $shipinfo['energy']=$shipinfo['energy']-$playerbeams;
            $playershields = num_level($shipinfo['shields'], $level_factor, $level_magnitude);
            if ($playershields>$shipinfo['energy'])
            {
                $playershields=$shipinfo['energy'];
            }

            $shipinfo['energy']=$shipinfo['energy']-$playershields;
            $targetshields = num_level($targetship['shields'], $level_factor, $level_magnitude);
            if ($targetshields>$targetship['energy'])
            {
                $targetshields=$targetship['energy'];
            }

            $targetship['energy']=$targetship['energy']-$targetshields;
            $playertorpnum = round(pow($level_factor,$shipinfo['torp_launchers']))*10;
            if ($playertorpnum > $shipinfo['torps'])
            {
                $playertorpnum = $shipinfo['torps'];
            }

            $targettorpnum = round(pow($level_factor,$targetship['torp_launchers']))*10;
            if ($targettorpnum > $targetship['torps'])
            {
                $targettorpnum = $targetship['torps'];
            }

            $playertorpdmg = $torp_dmg_rate*$playertorpnum;
            $targettorpdmg = $torp_dmg_rate*$targettorpnum;
            $playerarmor = $shipinfo['armor_pts'];
            $targetarmor = $targetship['armor_pts'];
            $playerfighters = $shipinfo['fighters'];
            $targetfighters = $targetship['fighters'];
            $targetdestroyed = 0;
            $playerdestroyed = 0;

            echo "$l_att_att $targetinfo[character_name] $l_abord $targetship[name]:<br><br>";
            echo "$l_att_beams<br>";
            if ($targetfighters > 0 && $playerbeams > 0)
            {
                if ($playerbeams > round($targetfighters / 2))
                {
                    $temp = round($targetfighters/2);
                    $lost = $targetfighters-$temp;
                    echo "$targetinfo[character_name] $l_att_lost $lost $l_fighters<br>";
                    $targetfighters = $temp;
                    $playerbeams = $playerbeams-$lost;
                }
                else
                {
                    $targetfighters = $targetfighters-$playerbeams;
                    echo "$targetinfo[character_name] $l_att_lost $playerbeams $l_fighters<br>";
                    $playerbeams = 0;
                }
            }

            if ($playerfighters > 0 && $targetbeams > 0)
            {
                if ($targetbeams > round($playerfighters / 2))
                {
                    $temp=round($playerfighters/2);
                    $lost=$playerfighters-$temp;
                    echo "$l_att_ylost $lost $l_fighters<br>";
                    $playerfighters=$temp;
                    $targetbeams=$targetbeams-$lost;
                }
                else
                {
                    $playerfighters=$playerfighters-$targetbeams;
                    echo "$l_att_ylost $targetbeams $l_fighters<br>";
                    $targetbeams=0;
                }
            }

            if ($playerbeams > 0)
            {
                if ($playerbeams > $targetshields)
                {
                    $playerbeams=$playerbeams-$targetshields;
                    $targetshields=0;
                    echo "$targetinfo[character_name]". $l_att_sdown ."<br>";
                }
                else
                {
                    echo "$targetinfo[character_name]" . $l_att_shits ." $playerbeams $l_att_dmg.<br>";
                    $targetshields=$targetshields-$playerbeams;
                    $playerbeams=0;
                }
            }

            if ($targetbeams > 0)
            {
                if ($targetbeams > $playershields)
                {
                    $targetbeams=$targetbeams-$playershields;
                    $playershields=0;
                    echo "$l_att_ydown<br>";
                }
                else
                {
                    echo "$l_att_yhits $targetbeams $l_att_dmg.<br>";
                    $playershields=$playershields-$targetbeams;
                    $targetbeams=0;
                }
            }

            if ($playerbeams > 0)
            {
                if ($playerbeams > $targetarmor)
                {
                    $targetarmor=0;
                    echo "$targetinfo[character_name] " .$l_att_sarm ."<br>";
                }
                else
                {
                    $targetarmor=$targetarmor-$playerbeams;
                    echo "$targetinfo[character_name]". $l_att_ashit ." $playerbeams $l_att_dmg.<br>";
                }
            }

            if ($targetbeams > 0)
            {
                if ($targetbeams > $playerarmor)
                {
                    $playerarmor=0;
                    echo "$l_att_yarm<br>";
                }
                else
                {
                    $playerarmor=$playerarmor-$targetbeams;
                    echo "$l_att_ayhit $targetbeams $l_att_dmg.<br>";
                }
            }

            echo "<br>$l_att_torps<br>";
            if ($targetfighters > 0 && $playertorpdmg > 0)
            {
                if ($playertorpdmg > round($targetfighters / 2))
                {
                    $temp=round($targetfighters/2);
                    $lost=$targetfighters-$temp;
                    echo "$targetinfo[character_name] $l_att_lost $lost $l_fighters<br>";
                    $targetfighters=$temp;
                    $playertorpdmg=$playertorpdmg-$lost;
                }
                else
                {
                    $targetfighters=$targetfighters-$playertorpdmg;
                    echo "$targetinfo[character_name] $l_att_lost $playertorpdmg $l_fighters<br>";
                    $playertorpdmg=0;
                }
            }

            if ($playerfighters > 0 && $targettorpdmg > 0)
            {
                if ($targettorpdmg > round($playerfighters / 2))
                {
                    $temp=round($playerfighters/2);
                    $lost=$playerfighters-$temp;
                    echo "$l_att_ylost $lost $l_fighters<br>";
//                  echo "$temp - $playerfighters - $targettorpdmg"; Im not sure this is supposed to be commented out, but..
                    $playerfighters=$temp;
                    $targettorpdmg=$targettorpdmg-$lost;
                }
                else
                {
                    $playerfighters=$playerfighters-$targettorpdmg;
                    echo "$l_att_ylost $targettorpdmg $l_fighters<br>";
                    $targettorpdmg=0;
                }
            }

            if ($playertorpdmg > 0)
            {
                if ($playertorpdmg > $targetarmor)
                {
                    $targetarmor=0;
                    echo "$targetinfo[character_name]" . $l_att_sarm ."<br>";
                }
                else
                {
                    $targetarmor=$targetarmor-$playertorpdmg;
                    echo "$targetinfo[character_name]" . $l_att_ashit . " $playertorpdmg $l_att_dmg.<br>";
                }
            }

            if ($targettorpdmg > 0)
            {
                if ($targettorpdmg > $playerarmor)
                {
                    $playerarmor=0;
                    echo "$l_att_yarm<br>";
                }
                else
                {
                    $playerarmor=$playerarmor-$targettorpdmg;
                    echo "$l_att_ayhit $targettorpdmg $l_att_dmg.<br>";
                }
            }

            echo "<br>$l_att_fighters<br>";
            if ($playerfighters > 0 && $targetfighters > 0)
            {
                if ($playerfighters > $targetfighters)
                {
                    echo "$targetinfo[character_name] $l_att_lostf<br>";
                    $temptargfighters=0;
                }
                else
                {
                    echo "$targetinfo[character_name] $l_att_lost $playerfighters $l_fighters.<br>";
                    $temptargfighters=$targetfighters-$playerfighters;
                }

                if ($targetfighters > $playerfighters)
                {
                    echo "$l_att_ylostf<br>";
                    $tempplayfighters=0;
                }
                else
                {
                    echo "$l_att_ylost $targetfighters $l_fighters.<br>";
                    $tempplayfighters=$playerfighters-$targetfighters;
                }

                $playerfighters=$tempplayfighters;
                $targetfighters=$temptargfighters;
            }

            if ($playerfighters > 0)
            {
                if ($playerfighters > $targetarmor)
                {
                    $targetarmor=0;
                    echo "$targetinfo[character_name]". $l_att_sarm . "<br>";
                }
                else
                {
                    $targetarmor=$targetarmor-$playerfighters;
                    echo "$targetinfo[character_name]" . $l_att_ashit ." $playerfighters $l_att_dmg.<br>";
                }
            }

            if ($targetfighters > 0)
            {
                if ($targetfighters > $playerarmor)
                {
                    $playerarmor=0;
                    echo "$l_att_yarm<br>";
                }
                else
                {
                    $playerarmor=$playerarmor-$targetfighters;
                    echo "$l_att_ayhit $targetfighters $l_att_dmg.<br>";
                }
            }

            if ($targetarmor < 1) // FOO - VICDIE
            {
                echo "<br>$targetinfo[character_name]". $l_att_sdest ."<br>";
                playerdeath($db,$targetinfo['player_id'], "LOG_ATTACK_LOSE", 0, 1, $playerinfo['player_id'], $targetship['ship_id']);

                if ($playerarmor > 0)
                {
                    $rating_change = round($targetinfo['rating']*$rating_combat_factor);

                    //Updating to always get a positive rating increase for furangee and the credits they are carrying - rjordan
                    $salv_credits = 0;
                    if ("aiplayer" == substr($targetinfo['email'], -8))                       // He's an AI player
                    {
                        $db->Execute("UPDATE {$db->prefix}ai SET active='N' WHERE ai_id='?'", array($targetinfo['email']));
                        if ($rating_change > 0)
                        {
                            $rating_change = 0 - $rating_change;
                        }

                        $salv_credits = $targetinfo['credits'];
                    }

                    $free_ore = round($targetship['ore']/2);
                    $free_organics = round($targetship['organics']/2);
                    $free_goods = round($targetship['goods']/2);
                    $free_holds = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
                    if ($free_holds > $free_goods)
                    {
                        $salv_goods=$free_goods;
                        $free_holds=$free_holds-$free_goods;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_goods=$free_holds;
                        $free_holds=0;
                    }
                    else
                    {
                        $salv_goods=0;
                    }

                    if ($free_holds > $free_ore)
                    {
                        $salv_ore=$free_ore;
                        $free_holds=$free_holds-$free_ore;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_ore=$free_holds;
                        $free_holds=0;
                    }
                    else
                    {
                        $salv_ore=0;
                    }

                    if ($free_holds > $free_organics)
                    {
                        $salv_organics=$free_organics;
                        $free_holds=$free_holds-$free_organics;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_organics=$free_holds;
                        $free_holds=0;
                    }
                    else
                    {
                        $salv_organics=0;
                    }

                    if ($plasma_engines)
                    {
                        $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $targetship['hull']))+round(pow($upgrade_factor, $targetship['engines']))+round(pow($upgrade_factor, $targetship['pengines']))+round(pow($upgrade_factor, $targetship['power']))+round(pow($upgrade_factor, $targetship['computer']))+round(pow($upgrade_factor, $targetship['sensors']))+round(pow($upgrade_factor, $targetship['beams']))+round(pow($upgrade_factor, $targetship['torp_launchers']))+round(pow($upgrade_factor, $targetship['shields']))+round(pow($upgrade_factor, $targetship['armor']))+round(pow($upgrade_factor, $targetship['cloak'])));
                    }
                    else
                    {
                        $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $targetship['hull']))+round(pow($upgrade_factor, $targetship['engines']))+round(pow($upgrade_factor, $targetship['power']))+round(pow($upgrade_factor, $targetship['computer']))+round(pow($upgrade_factor, $targetship['sensors']))+round(pow($upgrade_factor, $targetship['beams']))+round(pow($upgrade_factor, $targetship['torp_launchers']))+round(pow($upgrade_factor, $targetship['shields']))+round(pow($upgrade_factor, $targetship['armor']))+round(pow($upgrade_factor, $targetship['cloak'])));
                    }

                    $ship_salvage_rate = mt_rand(10,20);
                    $ship_salvage=$ship_value*$ship_salvage_rate/100+$salv_credits; // Added credits for AI - salv_credits = 0 if normal player

                    $l_att_ysalv=str_replace("[salv_ore]",$salv_ore,$l_att_ysalv);
                    $l_att_ysalv=str_replace("[salv_organics]",$salv_organics,$l_att_ysalv);
                    $l_att_ysalv=str_replace("[salv_goods]",$salv_goods,$l_att_ysalv);
                    $l_att_ysalv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_ysalv);
                    $l_att_ysalv=str_replace("[ship_salvage]",$ship_salvage,$l_att_ysalv);
                    $l_att_ysalv2=str_replace("[rating_change]", number_format(abs($rating_change), 0, $local_number_dec_point, $local_number_thousands_sep),$l_att_ysalv2);

                    $armor_lost=$shipinfo['armor_pts']-$playerarmor;
                    $fighters_lost=$shipinfo['fighters']-$playerfighters;
                    $energy=$shipinfo['energy'];

                    echo $l_att_ysalv . "<br>" . $l_att_ysalv2;
                    $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET ore=ore+'?', organics=organics+'?', goods=goods+'?', energy='?', fighters=fighters-'?', armor_pts=armor_pts-'?', torps=torps-'?' WHERE player_id='?' AND ship_id=$playerinfo[currentship]", array($salv_ore, $salv_organics, $salv_goods, $energy, $fighters_lost, $armor_lost, $playertorpnum, $playerinfo['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute ("UPDATE {$db->prefix}players SET credits=credits+'?', turns=turns-1, turns_used=turns_used+1, rating=rating-'?' WHERE player_id='?'", array($ship_salvage, $rating_change, $playerinfo['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    echo "$l_att_ylost $armor_lost $l_armorpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<br><br>";
                }
            }
            else
            {
                $l_att_stilship=str_replace("[name]",$targetinfo['character_name'],$l_att_stilship);
                echo "$l_att_stilship<br>";

                $rating_change=round($targetinfo['rating']*.1);
                $armor_lost=$targetship['armor_pts']-$targetarmor;
                $fighters_lost=$targetship['fighters']-$targetfighters;
                $energy=$targetship['energy'];

                playerlog($db,$targetinfo['player_id'], "LOG_ATTACKED_WIN", "$playerinfo[character_name]|$armor_lost|$fighters_lost");
                $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET energy='?', fighters=fighters-'?', armor_pts=armor_pts-'?', torps=torps-'?' WHERE ship_id='?'", array($energy, $fighters_lost, $armor_lost, $targettorpnum, $ship_id));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $armor_lost=$shipinfo['armor_pts']-$playerarmor;
                $fighters_lost=$shipinfo['fighters']-$playerfighters;
                $energy=$shipinfo['energy'];
//              FOO - ENERGY BUG HERE?
                $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET energy='?', fighters=fighters-'?', armor_pts=armor_pts-'?', torps=torps-'?' WHERE player_id='?' AND ship_id='?'", array($energy, $fighters_lost, $armor_lost, $playertorpnum, $playerinfo['player_id'], $playerinfo['currentship']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute ("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1, rating=rating-'?' WHERE player_id='?'", array($rating_change, $playerinfo['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "$l_att_ylost $armor_lost $l_armorpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<br><br>";
            }

            if ($playerarmor < 1) // ATTDIE
            {
                echo "$l_att_yshiplost<br><br>";
//              playerdeath($db,$playerinfo['player_id'], "LOG_ATTACK_LOSE", "$playerinfo[character_name]|N", 1, $targetinfo['player_id'], $shipinfo['ship_id']);
                playerdeath($db,$playerinfo['player_id'], "LOG_DEFEND", "$targetinfo[character_name]");

                if ($targetarmor > 0)
                {
                    $free_ore = round($shipinfo['ore']/2);
                    $free_organics = round($shipinfo['organics']/2);
                    $free_goods = round($shipinfo['goods']/2);
                    $free_holds = num_level($targetship['hull'], $level_factor, $level_magnitude) - $targetship['ore'] - $targetship['organics'] - $targetship['goods'] - $targetship['colonists'];
                    if ($free_holds > $free_goods)
                    {
                        $salv_goods=$free_goods;
                        $free_holds=$free_holds-$free_goods;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_goods=$free_holds;
                        $free_holds=0;
                    }
                    else
                    {
                        $salv_goods=0;
                    }

                    if ($free_holds > $free_ore)
                    {
                        $salv_ore=$free_ore;
                        $free_holds=$free_holds-$free_ore;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_ore=$free_holds;
                        $free_holds=0;
                    }
                    else
                    {
                        $salv_ore=0;
                    }

                    if ($free_holds > $free_organics)
                    {
                        $salv_organics=$free_organics;
                        $free_holds=$free_holds-$free_organics;
                    }
                    elseif ($free_holds > 0)
                    {
                        $salv_organics=$free_holds;
                        $free_holds=0;
                    }
                    else
                    {
                        $salv_organics=0;
                    }

                    if ($plasma_engines)
                    {
                        $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $shipinfo['hull']))+round(pow($upgrade_factor, $shipinfo['engines']))+round(pow($upgrade_factor, $shipinfo['pengines']))+round(pow($upgrade_factor, $shipinfo['power']))+round(pow($upgrade_factor, $shipinfo['computer']))+round(pow($upgrade_factor, $shipinfo['sensors']))+round(pow($upgrade_factor, $shipinfo['beams']))+round(pow($upgrade_factor, $shipinfo['torp_launchers']))+round(pow($upgrade_factor, $shipinfo['shields']))+round(pow($upgrade_factor, $shipinfo['armor']))+round(pow($upgrade_factor, $shipinfo['cloak'])));
                    }
                    else
                    {
                        $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $shipinfo['hull']))+round(pow($upgrade_factor, $shipinfo['engines']))+round(pow($upgrade_factor, $shipinfo['power']))+round(pow($upgrade_factor, $shipinfo['computer']))+round(pow($upgrade_factor, $shipinfo['sensors']))+round(pow($upgrade_factor, $shipinfo['beams']))+round(pow($upgrade_factor, $shipinfo['torp_launchers']))+round(pow($upgrade_factor, $shipinfo['shields']))+round(pow($upgrade_factor, $shipinfo['armor']))+round(pow($upgrade_factor, $shipinfo['cloak'])));
                    }

                    $ship_salvage_rate = mt_rand(10,20);
                    $ship_salvage=$ship_value*$ship_salvage_rate/100;

                    $l_att_salv=str_replace("[salv_ore]",$salv_ore,$l_att_salv);
                    $l_att_salv=str_replace("[salv_organics]",$salv_organics,$l_att_salv);
                    $l_att_salv=str_replace("[salv_goods]",$salv_goods,$l_att_salv);
                    $l_att_salv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_salv);
                    $l_att_salv=str_replace("[ship_salvage]",$ship_salvage,$l_att_salv);
                    $l_att_salv=str_replace("[name]",$targetinfo['character_name'],$l_att_salv);

                    echo "$l_att_salv<br>";

                    $armor_lost=$targetship['armor_pts']-$targetarmor;
                    $fighters_lost=$targetship['fighters']-$targetfighters;
                    $energy=$targetship['energy'];

                    $debug_query = $db->Execute ("UPDATE {$db->prefix}players SET credits=credits+'?' WHERE player_id='?'", array($ship_salvage, $targetinfo['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET energy=$energy, fighters=fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$targettorpnum, ore=ore+$salv_ore, organics=organics+$salv_organics, goods=goods+$salv_goods WHERE ship_id=$ship_id", array($energy, $fighters_lost, $armor_lost, $targettorpnum, $salv_ore, $salv_organics, $salv_goods, $ship_id));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                }
            }
        }
    }
}
//$debug_query = $db->Execute("UNLOCK TABLES");
//db_op_result($db,$debug_query,__LINE__,__FILE__);


//-------------------------------------------------------------------------------------------------


global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");
?>
