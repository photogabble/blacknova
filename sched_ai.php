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
// File: sched_ai.php

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_ai.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

//  AI turn updates
echo "<strong>" . $ai_name . " turns</strong><br>\n";

//  include functions
include_once ("./ai_functions.php");

// Dynamic functions
dynamic_loader ($db, "ai_move.php");
dynamic_loader ($db, "ai_regen.php");
dynamic_loader ($db, "ai_trade.php");
dynamic_loader ($db, "ai_hunter.php");
dynamic_loader ($db, "ai_toplanet.php");
dynamic_loader ($db, "ai_toship.php");
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "load_languages.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
// load_languages($db, $raw_prefix, 'ai'); //  No idea which lang file to include.. ?

global $targetlink;
global $ai_isdead;

//  make ai player selection
$ai_count = $ai_count0 = $ai_count0a = $ai_count1 = $ai_count1a = $ai_count2 = $ai_count2a = $ai_count3 = $ai_count3a = $ai_count3h = 0;
$res = $db->Execute("SELECT * FROM {$db->prefix}players WHERE email like '%aiplayer' and active='Y'");
while(!$res->EOF && $res)
{
    $ai_isdead = 0;
    $playerinfo = $res->fields;

    //  REGENERATE/BUY STATS 
    ai_regen();

    //  RUN THROUGH ORDERS 
    $ai_count++;
    if (mt_rand(1,5) > 1)                                 //  20% CHANCE OF NOT MOVING AT ALL 
    {
        //  ORDERS = 0 SENTINEL 
        if ($playerinfo['orders'] == 0)
        {
            $ai_count0++;
            //  FIND A TARGET 
            //  IN MY SECTOR, NOT MYSELF, NOT ON A PLANET 
            $reso0 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE sector=$playerinfo[sector] and email!='$playerinfo[email]' and planet_id=0 and ship_id > 1");
            if (!$reso0->EOF)
            {
                $rowo0 = $reso0->fields;
                if ($playerinfo['aggression'] == 0)            //  O = 0 & AGRESSION = 0 PEACEFUL 
                {
                    // This Guy Does Nothing But Sit As A Target Himself
                }
                elseif ($playerinfo[aggression] == 1)        //  O = 0 & AGRESSION = 1 ATTACK SOMETIMES 
                {
                    // ai players's only compare number of fighters when determining if they have an attack advantage
                    if ($playerinfo[ship_fighters] > $rowo0[ship_fighters])
                    {
                        $ai_count0a++;
                        playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo0[character_name]");
                        ai_toship($rowo0[ship_id]);
                        if ($ai_isdead>0)
                        {
                            $res->MoveNext();
                            continue;
                        }
                    }
                }
                elseif ($playerinfo[aggression] == 2)        //  O = 0 & AGRESSION = 2 ATTACK ALLWAYS 
                {
                    $ai_count0a++;
                    playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo0[character_name]");
                    ai_toship($rowo0[ship_id]);
                    if ($ai_isdead>0)
                    {
                        $res->MoveNext();
                        continue;
                    }
                }
            }
        }
        elseif ($playerinfo[orders] == 1) //  ORDERS = 1 ROAM
        {
            $ai_count1++;
            //  ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE 
            $targetlink = $playerinfo[sector];
            ai_move();
            if ($ai_isdead>0)
            {
                $res->MoveNext();
                continue;
            }

            //  FIND A TARGET 
            //  IN MY SECTOR, NOT MYSELF 
            $reso1 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE sector=$targetlink and email!='$playerinfo[email]' and ship_id > 1");
            if (!$reso1->EOF)
            {
                $rowo1 = $reso1->fields;
                if ($playerinfo[aggression] == 0)            //  O = 1 & AGRESSION = 0 PEACEFUL 
                {
                    // This Guy Does Nothing But Roam Around As A Target Himself
                }
                elseif ($playerinfo[aggression] == 1)        //  O = 1 & AGRESSION = 1 ATTACK SOMETIMES 
                {
                    // AI player's only compare number of fighters when determining if they have an attack advantage
                    if ($playerinfo[ship_fighters] > $rowo1[ship_fighters] && $rowo1[planet_id] == 0)
                    {
                        $ai_count1a++;
                        playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo1[character_name]");
                        ai_toship($rowo1[ship_id]);
                        if ($ai_isdead>0)
                        {
                            $res->MoveNext();
                            continue;
                        }
                    }
                }
                elseif ($playerinfo[aggression] == 2)        //  O = 1 & AGRESSION = 2 ATTACK ALLWAYS 
                {
                    $ai_count1a++;
                    playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo1[character_name]");
                    if (!$rowo1[planet_id] == 0)  //  IS ON PLANET
                    {
                        ai_toplanet($rowo1[planet_id],$rowo1[character_name]);
                    }
                    else
                    {
                        ai_toship($rowo1[ship_id]);
                    }

                    if ($ai_isdead>0)
                    {
                        $res->MoveNext();
                        continue;
                    }
                }
            }
        }
        elseif ($playerinfo[orders] == 2) //  ORDERS = 2 ROAM AND TRADE
        {
            $ai_count2++;
            //  ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE 
            $targetlink = $playerinfo[sector];
            ai_move();
            if ($ai_isdead > 0)
            {
                $res->MoveNext();
                continue;
            }

            //  NOW TRADE BEFORE WE DO ANY AGGRESSION CHECKS 
            ai_trade();

            //  FIND A TARGET 
            //  IN MY SECTOR, NOT MYSELF 
            $reso2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE sector=$targetlink and email!='$playerinfo[email]' and ship_id > 1");
            if (!$reso2->EOF)
            {
                $rowo2=$reso2->fields;
                if ($playerinfo[aggression] == 0)            //  O = 2 & AGRESSION = 0 PEACEFUL 
                {
                    // This Guy Does Nothing But Roam And Trade
                }
                elseif ($playerinfo[aggression] == 1)        //  O = 2 & AGRESSION = 1 ATTACK SOMETIMES 
                {
                    // AI player's only compare number of fighters when determining if they have an attack advantage
                    if ($playerinfo[ship_fighters] > $rowo2[ship_fighters] && $rowo2[planet_id] == 0)
                    {
                        $ai_count2a++;
                        playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo2[character_name]");
                        ai_toship($rowo2[ship_id]);
                        if ($ai_isdead>0)
                        {
                            $res->MoveNext();
                            continue;
                        }
                    }
                }
                elseif ($playerinfo[aggression] == 2)        //  O = 2 & AGRESSION = 2 ATTACK ALLWAYS 
                {
                    $ai_count2a++;
                    playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo2[character_name]");
                    if (!$rowo2[planet_id] == 0) // IS ON PLANET
                    {
                        ai_toplanet($rowo2[planet_id],$rowo2[character_name]);
                    }
                    else
                    {
                        ai_toship($rowo2[ship_id]);
                    }

                    if ($ai_isdead>0)
                    {
                        $res->MoveNext();
                        continue;
                    }
                }
            }
        }
        // ORDERS = 3 ROAM AND HUNT  *
        elseif ($playerinfo[orders] == 3)
        {
            $ai_count3++;

            //  LET SEE IF WE GO HUNTING THIS ROUND BEFORE WE DO ANYTHING ELSE 
            $hunt = mt_rand(0,3);                               // 25% CHANCE OF HUNTING *

            // Uncomment below for Debugging
            //$hunt=0;
            if ($hunt==0)
            {
                $ai_count3h++;
                ai_hunter();
                if ($ai_isdead>0)
                {
                    $res->MoveNext();
                    continue;
                }
            }
            else
            {
                //  ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE 
                ai_move();
                if ($ai_isdead>0)
                {
                    $res->MoveNext();
                    continue;
                }

                //  FIND A TARGET 
                //  IN MY SECTOR, NOT MYSELF 
                $reso3 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE sector=$playerinfo[sector] and email!='$playerinfo[email]' and ship_id > 1");
                if (!$reso3->EOF)
                {
                    $rowo3=$reso3->fields;
                    if ($playerinfo[aggression] == 0)            //  O = 3 & AGRESSION = 0 PEACEFUL 
                    {
                        // This Guy Does Nothing But Roam Around As A Target Himself
                    }
                    elseif ($playerinfo[aggression] == 1)        //  O = 3 & AGRESSION = 1 ATTACK SOMETIMES 
                    {
                        // AI player's only compare number of fighters when determining if they have an attack advantage
                        if ($playerinfo[ship_fighters] > $rowo3[ship_fighters] && $rowo3[planet_id] == 0)
                        {
                            $ai_count3a++;
                            playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo3[character_name]");
                            ai_toship($rowo3[ship_id]);
                            if ($ai_isdead>0)
                            {
                                $res->MoveNext();
                                continue;
                            }
                        }
                    }
                    elseif ($playerinfo[aggression] == 2)        //  O = 3 & AGRESSION = 2 ATTACK ALLWAYS 
                    {
                        $ai_count3a++;
                        playerlog($db,$playerinfo[ship_id], "LOG_AI_ATTACK", "$rowo3[character_name]");
                        if (!$rowo3[planet_id] == 0) // IS ON PLANET
                        {
                            ai_toplanet($rowo3[planet_id],$rowo3[character_name]);
                        }
                        else
                        {
                            ai_toship($rowo3[ship_id]);
                        }

                        if ($ai_isdead>0)
                        {
                            $res->MoveNext();
                            continue;
                        }
                    }
                }
            }
        }
    }
    $res->MoveNext();
}

// attempting to make a player generator to regenerate players after death and keep the number of AI players
// at the ai_max value in the the config file.
    
$needed_ai_ = $ai__max - $ai_count;
if ($needed_ai_ >= 0)
{
    echo "creating $needed_ai_ ai_.<br>";
     
    // The created ai_ will be set to the average player hull + or - 7

    $res = $db->Execute("SELECT round(AVG(hull)) AS hull, round(AVG(power)) AS power FROM {$db->prefix}ships JOIN {$db->prefix}players WHERE destroyed='N' AND acl != '0' AND hull > 3"); // ACL = 0 means AI player.
    $row = $res->fields;
 
    while ($needed_ai_ > 0)
    {
        $furlevel = (($row['hull'] + $row['power'])/2) + mt_rand(-7,9);
        if ($furlevel <= 0)
        {
            $furlevel = 3;
        }

        if ($furlevel >= 100)
        {
            $furlevel = 100; 
            echo "<br>Lowering AI average too big !<br>"; } // New code to limit AI size, stops undeafetable AI's being created - GunSlinger
        }

        $fur_cloak = round($furlevel/2);  // Making cloak half the size of the AI player level so users see more of them.  I know this affects other calcs, but not against players - rjordan
        $fpf = $furlevel * 1000000;
        echo "creating level $furlevel " . $ai_name . ".<br>";

        // Create player name
        $Sylable1 = array("Ak","Al","Ar","B","Br","D","F","Fr","G","Gr","K","Kr","N","Ol","Om","P","Qu","R","S","Z");
        $Sylable2 = array("a","ar","aka","aza","e","el","i","in","int","ili","ish","ido","ir","o","oi","or","os","ov","u","un");
        $Sylable3 = array("ag","al","ak","ba","dar","g","ga","k","ka","kar","kil","l","n","nt","ol","r","s","ta","til","x");
        $sy1roll = mt_rand(0,19);
        $sy2roll = mt_rand(0,19);
        $sy3roll = mt_rand(0,19);
        $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];
        $resultnm = $db->Execute ("SELECT character_name FROM {$db->prefix}players WHERE character_name='$character'");
        $namecheck = $resultnm->fields;
        $nametry = 1;

        // If Name Exists Try Again - Up To Nine Times
        while (($namecheck[0]) and ($nametry <= 9))
        {
            $sy1roll = mt_rand(0,19);
            $sy2roll = mt_rand(0,19);
            $sy3roll = mt_rand(0,19);
            $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];
            $resultnm = $db->Execute ("SELECT character_name FROM {$db->prefix}players WHERE character_name='$character'");
            $namecheck = $resultnm->fields;
            $nametry++;
        }

        // Create Ship Name
        $shipname = $ai_name . "-" . $character; 

        // Select Random Sector
        $sector = mt_rand(1,$sector_max);
          
        // Select Orders
        $orders = mt_rand(0,2);

        // Select Aggression
        $aggression = mt_rand(1,100);
        if ($aggression <= $ai__aggression)
        {
            $aggression = 1; //I do this to create more peacefull than aggressive.  This creates 33% aggressive.  I will make configurable later
        }
        else
        {
            $aggression = 0;
        }

        if ($aggression == 1)
        {
            $orders = mt_rand(1,2);
        }

        // update database
        $_active = empty($active) ? "N" : "Y";
        $errflag=0;
        if ( $character=='' || $shipname=='' )
        {
            echo "Ship name, and character name may not be blank.<br>"; 
            $errflag=1;
        }

        // Change Spaces to Underscores in shipname
        $shipname = str_replace(" ","_",$shipname);

        // Create emailname from character
        $emailname = str_replace(" ","_",$character) . "@aiplayer";
        $result = $db->Execute ("SELECT email, character_name, ship_name FROM {$db->prefix}players WHERE email='$emailname' OR character_name='$character' OR ship_name='$shipname'");
        if ($result>0)
        {
            while (!$result->EOF)
            {
                $row= $result->fields;
                if ($row[0]==$emailname)
                {
                    echo "ERROR: E-mail address $emailname, is already in use.  "; 
                    $errflag=1;
                }

                if ($row[1]==$character)
                {
                    echo "ERROR: Character name $character, is already in use.<br>"; 
                    $errflag=1;
                }

                if ($row[2]==$shipname)
                {
                    echo "ERROR: Ship name $shipname, is already in use.<br>"; 
                    $errflag=1;
                }

                $result->MoveNext();
            }
        }

        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($errflag==0)
        {
            $makepass="";
            $syllables="er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
            $syllable_array=explode(",", $syllables);
            seed_mt_rand();
            for ($count=1;$count<=4;$count++)
            {
                if (mt_rand()%10 == 1)
                {
                    $makepass .= sprintf("%0.0f",(mt_rand()%50)+1);
                }
                else
                {
                    $makepass .= sprintf("%s",$syllable_array[mt_rand()%62]);
                }
            }

            if ($furlevel=='')
            {
                $furlevel=0;
            }

            $maxenergy = 5* num_level($furlevel, $level_factor, $level_magnitude);
            $maxarmor = num_level($furlevel, $level_factor, $level_magnitude);
            $maxfighters = num_level($furlevel, $level_factor, $level_magnitude);
            $maxtorps = num_level($furlevel, $level_factor, $level_magnitude);
            $stamp=date("Y-m-d H:i:s");

            $ai_c_code = md5(mt_mt_rand(0,9999));
            // Add AI player record to accounts table
            $resultaccount = $db->Execute("INSERT INTO {$raw_prefix}users (email, password, c_code, active) VALUES(" .
                                          "'$emailname','$makepass', '$ai_c_code', 'Y')");

            // Get the new player's account id
            $res = $db->Execute("SELECT account_id FROM {$raw_prefix}users WHERE email='$emailname'");
            db_op_result($db,$res,__LINE__,__FILE__);
            $account_id = $res->fields['account_id'];

            // ADD AI Player RECORD TO player TABLE ... MODIFY IF ships SCHEMA CHANGES *
            $result2 = $db->Execute("INSERT INTO {$db->prefix}players (currentship, character_name, email, credits, " .
                                    "turns, turns_used, last_login, times_dead, rating, score, team, team_invite, " .
                                    "ip_address, trade_colonists, trade_fighters, trade_torps, trade_energy, " .
                                    "password, c_code, active, account_id) ".
                                    "VALUES ('3', '$character', '$emailname'," .
                                    "'$ai_start_credits', '$start_turns', '0', '$stamp', '0', '0', '0', '0', '0', ".
                                    "'127.0.0.1', 'Y', 'N', 'N', 'Y', '$makepass', '$ai_c_code', 'Y', '$account_id')");

            $shipclass = $furlevel / 20;
            if ($shipclass < 1)
            {
                $shipclass = 1;
            }

            // Create player's ship
            //    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ships (player_id, ". // We will need it to do player_id as well, but I dunno how yet
            $debug_query = $db->Execute("INSERT INTO {$db->prefix}ships (".
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
//                                "'$player_id'," .     // player_id
                                        "'$shipclass'," .              // class
                                        "'$shipname'," .     // name
                                        "'N'," .              // destroyed
                                        "'$furlevel'," .                // hull
                                        "'$furlevel'," .                // engines
                                        "'$furlevel'," .                // pengines
                                        "'$furlevel'," .                // power
                                        "'$furlevel'," .                // computer
                                        "'$furlevel'," .                // sensors
                                        "'$furlevel'," .                // beams
                                        "'$furlevel'," .                // torp_launchers
                                        "'$furlevel'," .                // torps
                                        "'$furlevel'," .                // shields
                                        "'$furlevel'," .                // armor
                                        "'$maxarmor'," .    // armor_pts
                                        "'$furlevel'," .                // cloak
                                        "'$sector'," .                // sector_id
                                        "0," .                // ore
                                        "0," .                // organics
                                        "0," .                // goods
                                        "$maxenergy," .    // energy
                                        "0," .                // colonists
                                        "$maxfighters," .  // fighters
                                        "'N'," .              // on_planet
                                        "0," .                // dev_warpedit
                                        "0," .                // dev_genesis
                                        "0," .                // dev_emerwarp
                                        "'$start_pod'," .     // dev_escapepod
                                        "'$start_scoop'," .   // dev_fuelscoop
                                        "0," .                // dev_minedeflector
                                        "0," .                // planet_id
                                        "''" .                // cleared_defenses
                                        ")");

            if (!$result2)
            {
                echo $db->ErrorMsg() . "<br>";
            }
            else
            {
                echo $ai_name . " has been created.<br><br>";
                echo "Password has been set.<br><br>";
                echo "Ship Records have been updated.<br><br>";
            }

            $result3 = $db->Execute("INSERT INTO {$db->prefix}ai (ai_id,active,aggression,orders) VALUES('$emailname','Y','$aggression','$orders')");
            if (!$result3)
            {
                echo $db->ErrorMsg() . "<br>";
            }
            else
            {
                echo $ai_name . " Records have been updated.<br><br>";
            }

            // AI player has a 5% chance of getting a planet - rjordan01
            $getsplanet = mt_rand(1,100);
            if ($getsplanet <= $ai__planets)
            // if ($getsplanet<=100)
            {
                echo "The " . $ai_name . " has been selected to get a planet<br><br>";
                $result4 = $db->Execute("SELECT player_id FROM {$db->prefix}players WHERE character_name = '$character'");
                $furshipid = $result4->fields;

                $resep = $db->SelectLimit("SELECT planet_id FROM {$db->prefix}planets WHERE owner = 0 and credits > 0 order by credits desc",1);
                $ep = $resep->fields;
                if ($ep['planet_id'])
                {
                    $result7 = $db->Execute("UPDATE {$db->prefix}planets SET organics='$fpf', ore='$fpf', goods='$fpf', energy='$fpf', colonists='$fpf', credits='$fpf', fighters='$fpf', torps='$fpf', owner='$furshipid[player_id]', base='Y', sells='N', prod_organics='15.00',prod_ore='5.00',prod_goods='5.00',prod_energy='10.00',prod_fighters='20.00',prod_torp='10.00' WHERE planet_id=$ep[planet_id]");
                    if (!$result7) 
                    {
                        echo $db->ErrorMsg() . "<br>";
                    }
                    else 
                    {
                        echo $ai_name . " captured an abanded planet. $ep[planet_id]<br><br>";
                    }
                }
                else
                {
                    // checking sector
                    $fps_query = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id = '$sector' and zone_id = '1'");
                    $fpsector = $fps_query->fields;

                    if ($fpsector['sector_id'] != $sector)
                    {
                        echo "Not allowed to create a planet in sector $sector.<br>";
                    }
                    else
                    {
                        echo "sector is $sector<br>";
                        $maxp = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE sector_id = '$sector'");
                        $num_res = $maxp->numRows(); 
                        if ($num_res >= $max_star_size)
                        {
                            echo "There are too many planets in sector $sector. <br>";
                        }
                        else
                        {
                            // Create the planet
                            $num_res += 1;
                            $pname = "FP-$sector-$num_res";
                            echo "The planet name is $pname<br>";

                            $result5 = $db->Execute("INSERT INTO {$db->prefix}planets (sector_id, name, organics, ore, goods, energy, colonists, " .
                                                    "credits, computer, sensors, beams, torp_launchers, torps, shields, armor, armor_pts, cloak, fighters, owner, ".
                                                    "team, base, sells, prod_organics, prod_ore, prod_goods, prod_energy, prod_fighters, prod_torp, defeated) ".
                                                    "VALUES (" .
                                                    "'$sector', " .                // sector_id
                                                    "'$pname', " .          // name
                                                    "10000000, " .         // organics
                                                    "'$fpf'," .                 // ore
                                                    "'$fpf', " .                // goods
                                                    "'$fpf', " .         // energy
                                                    "10000000, " .         // colonists
                                                    "'$fpf', " .                // credits
                                                    "20, " .               // computer
                                                    "20, " .               // sensors
                                                    "20, " .               // beams
                                                    "20, " .               // torp_launchers
                                                    "20, " .               // torps
                                                    "20, " .               // shields
                                                    "20, " .               // armor
                                                    "20, " .               // armout_pts
                                                    "20, " .                // cloak
                                                    "10000000, " .         // fighters
                                                    "'$furshipid[player_id]', " .                // owner
                                                    "0, " .                // team
                                                    "1, " .                // base
                                                    "0, " .                // sells
                                                    "25, " .               // prod_organics
                                                    "0, " .                // prod_ore
                                                    "0, " .                // prod_goods
                                                    "25, " .               // prod_energy
                                                    "25, " .               // prod_fighters
                                                    "25, " .               // prod_torp
                                                    "'N'" .                // defeated
                                                    ")");
                            if (!$result5)
                            {
                                echo $db->ErrorMsg() . "<br>";
                            }
                            else
                            {
                                echo $ai_name . " Planet has been created.<br><br>";
                            }
                        }
                    }
                }
            }
        }
        $needed_ai_--;
    }
}

//$res->_close();
$furnonmove = $ai_count - ($ai_count0 + $ai_count1 + $ai_count2 + $ai_count3);
echo "Counted $ai_count " . $ai_name . " players that are ACTIVE with working ships.<br>";
echo "$furnonmove " . $ai_name . " players did not do anything this round. <br>";
echo "$ai_count0 " . $ai_name . " players had SENTINEL orders of which $ai_count0a launched attacks. <br>";
echo "$ai_count1 " . $ai_name . " players had ROAM orders of which $ai_count1a launched attacks. <br>";
echo "$ai_count2 " . $ai_name . " players had ROAM AND TRADE orders of which $ai_count2a launched attacks. <br>";
echo "$ai_count3 " . $ai_name . " players had ROAM AND HUNT orders of which $ai_count3a launched attacks and $ai_count3h went hunting. <br>";
echo $ai_name . " TURNS COMPLETE. <br>";
echo "<br>";
// END OF AI PLAYER TURNS

?>
