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
// File: includes/ai_tosecdef.php
<?php
function ai_tosecdef()
{
    // Dynamic functions
    dynamic_loader ($db, "seed_mt_rand.php");
    dynamic_loader ($db, "playerlog.php");

    //  AI TO SECTOR DEFENSE 

    //  SETUP GENERAL VARIABLES
    global $playerinfo, $targetlink;
    global $l_sf_sendlog, $l_sf_sendlog2, $l_chm_hehitminesinsector, $l_chm_hewasdestroyedbyyourmines;
    global $ai_isdead, $db, $db_prefix;

    seed_mt_rand();
    //  CHECK FOR SECTOR DEFENSE
    if ($targetlink>0)
    {
        // echo "The target link for $playerinfo[character_name] is $targetlink<br>";
        $resultf = $db->Execute ("SELECT * FROM {$db_prefix}sector_defense WHERE sector_id=? and defense_type ='F' ORDER BY quantity DESC", array($targetlink));
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

        $resultm = $db->Execute ("SELECT * FROM {$db_prefix}sector_defense WHERE sector_id=? and defense_type ='M'",array($targetlink));
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

        if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
        // DEST LINK HAS DEFENSES SO LETS ATTACK THEM
        {
            playerlog($playerinfo['ship_id'], "LOG_RAW", "ATTACKING SECTOR DEFENSES $total_sector_fighters fighters and $total_sector_mines mines."); 
            //  LETS GATHER COMBAT VARIABLES 
            $targetfighters = $total_sector_fighters;
            $playerbeams = num_beams($playerinfo['beams']);
            if ($playerbeams>$playerinfo['ship_energy'])
            {
                $playerbeams= $playerinfo['ship_energy'];
            }

            $playerinfo['ship_energy']= $playerinfo['ship_energy']-$playerbeams;
            $playershields = num_shields($playerinfo['shields']);
            if ($playershields>$playerinfo['ship_energy'])
            {
                $playershields= $playerinfo['ship_energy'];
            }

            $playertorpnum = round(pow($level_factor,$playerinfo['torp_launchers']))*2;
            if ($playertorpnum > $playerinfo['torps'])
            {
                $playertorpnum = $playerinfo['torps'];
            }

            $playertorpdmg = $torp_dmg_rate*$playertorpnum;
            $playerarmor = $playerinfo['armor_pts'];
            $playerfighters = $playerinfo['ship_fighters'];
            $totalmines = $total_sector_mines;
            if ($totalmines>1)
            {
                $roll = mt_rand(1,$totalmines);
            }
            else
            {
                $roll = 1;
            }

            $totalmines = $totalmines - $roll;
            //$playerminedeflect = $playerinfo['ship_fighters']; //  AI keep as many deflectors as fighters 
            $playerminedeflect = $playerinfo['dev_minedeflector'];

            //  LETS DO SOME COMBAT ! 
            //  BEAMS VS FIGHTERS 
            if ($targetfighters > 0 && $playerbeams > 0)
            {
                if ($playerbeams > round($targetfighters / 2))
                {
                    $temp = round($targetfighters/2);
                    $targetfighters = $temp;
                    $playerbeams = $playerbeams-$temp;
                }
                else
                {
                    $targetfighters = $targetfighters-$playerbeams;
                    $playerbeams = 0;
                }   
            }

            //  TORPS VS FIGHTERS 
            if ($targetfighters > 0 && $playertorpdmg > 0)
            {
                if ($playertorpdmg > round($targetfighters / 2))
                {
                    $temp=round($targetfighters/2);
                    $targetfighters= $temp;
                    $playertorpdmg= $playertorpdmg-$temp;
                }
                else
                {
                    $targetfighters= $targetfighters-$playertorpdmg;
                    $playertorpdmg = 0;
                }
            }

            //  FIGHTERS VS FIGHTERS 
            if ($playerfighters > 0 && $targetfighters > 0)
            {
                if ($playerfighters > $targetfighters)
                {
                    echo $l_sf_destfightall;
                    $temptargfighters = 0;
                }
                else
                {
                    $temptargfighters = $targetfighters-$playerfighters;
                }

                if ($targetfighters > $playerfighters)
                {
                    $tempplayfighters = 0;
                }
                else
                {
                    $tempplayfighters= $playerfighters-$targetfighters;
                }

                $playerfighters= $tempplayfighters;
                $targetfighters= $temptargfighters;
            }

            //  OH NO THERE ARE STILL FIGHTERS 
            //  armor VS FIGHTERS 
            if ($targetfighters > 0)
            {
                if ($targetfighters > $playerarmor)
                {
                    $playerarmor = 0;
                }
                else
                {
                    $playerarmor= $playerarmor-$targetfighters;
                } 
            }

            //  GET RID OF THE SECTOR FIGHTERS THAT DIED 
            $fighterslost = $total_sector_fighters - $targetfighters;
            destroy_fighters($targetlink,$fighterslost);

            //  LETS LET DEFENSE OWNER KNOW WHAT HAPPENED  
            $l_sf_sendlog1 = str_replace("[player]", "kabal $playerinfo[character_name]", $l_sf_sendlog);
            $l_sf_sendlog2 = str_replace("[lost]", $fighterslost, $l_sf_sendlog1);
            $l_sf_sendlog3 = str_replace("[sector]", $targetlink, $l_sf_sendlog2);
            message_defense_owner($targetlink,$l_sf_sendlog3);

            //  UPDATE AI AFTER COMBAT 
            $armor_lost= $playerinfo['armor_pts']-$playerarmor;
            $fighters_lost= $playerinfo['ship_fighters']-$playerfighters;
            $energy = $playerinfo['ship_energy'];
            $update1 = $db->Execute ("UPDATE {$db_prefix}ships SET ship_energy=?, ship_fighters=ship_fighters-?, armor_pts=armor_pts-?, torps=torps-? WHERE ship_id=?", array($energy, $fighters_lost, $armor_lost, $playertorpnum, $playerinfo['ship_id']));

            //  CHECK TO SEE IF AI IS DEAD 
            if ($playerarmor < 1)
            {
                $l_sf_sendlog2 = str_replace("[player]", "kabal " . $playerinfo['character_name'], $l_sf_sendlog2);
                $l_sf_sendlog2 = str_replace("[sector]", $targetlink, $l_sf_sendlog2);
                message_defense_owner($targetlink,$l_sf_sendlog2);
                cancel_bounty($playerinfo['ship_id']);
                // Dynamic functions
                dynamic_loader ($db, "db_kill_player.php");
                db_kill_player($playerinfo['ship_id']);
                $ai_isdead = 1;
                return;
            }

            //  OK AI MUST STILL BE ALIVE 
            //  NOW WE HIT THE MINES 
            //  LETS LOG THE FACT THAT WE HIT THE MINES 
            // echo "before - $playerinfo[character_name] hit $roll mines in sector $targetlink<br>";
            $l_chm_hehitminesinsector1 = str_replace("[chm_playerinfo_character_name]", "kabal " . $playerinfo['character_name'], $l_chm_hehitminesinsector);
            $l_chm_hehitminesinsector2 = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector1);
            $l_chm_hehitminesinsector3 = str_replace("[chm_sector]", $targetlink, $l_chm_hehitminesinsector2);

            // echo "after - $l_chm_hehitminesinsector3<br>";
            message_defense_owner($targetlink,"$l_chm_hehitminesinsector3");

            //  DEFLECTORS VS MINES 
            if ($playerminedeflect >= $roll)
            {
                $playerminedeflect = $playerminedeflect - $roll;
                $update2 = $db->Execute("UPDATE {$db_prefix}ships set dev_minedeflector=? WHERE ship_id=?", array($playerminedeflect, $playerinfo['ship_id']));
            }
            else
            {
                $mines_left = $roll - $playerminedeflect;
                if ($mines_left < 0)
                {
                    $mines_left = 0;
                }

                //  SHIELDS VS MINES 
                if ($playershields >= $mines_left)
                {
                    $update2 = $db->Execute("UPDATE {$db_prefix}ships set ship_energy=ship_energy-? WHERE ship_id=?", array($mines_left, $playerinfo['ship_id']));
                }
                else
                {
                    $mines_left = $mines_left - $playershields;

                    //  armor VS MINES 
                    if ($playerarmor >= $mines_left)
                    {
                        $update2 = $db->Execute("UPDATE {$db_prefix}ships set armor_pts=armor_pts-?,ship_energy=0 WHERE ship_id=?", array($mines_left, $playerinfo['ship_id']));
                    }
                    else
                    {
                        //  OH NO WE DIED 
                        //  LETS LOG THE FACT THAT WE DIED  
                        $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]", "kabal " . $playerinfo['character_name'], $l_chm_hewasdestroyedbyyourmines);
                        $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", $targetlink, $l_chm_hewasdestroyedbyyourmines);
                        message_defense_owner($targetlink,"$l_chm_hewasdestroyedbyyourmines");
                        //  LETS ACTUALLY KILL THE AI NOW 
                        cancel_bounty($playerinfo['ship_id']);
                        // Dynamic functions
                        dynamic_loader ($db, "db_kill_player.php");
                        db_kill_player($playerinfo['ship_id']);
                        $ai_isdead = 1;
                        //  LETS GET RID OF THE MINES NOW AND RETURN OUT OF THIS FUNCTION 
                        explode_mines($targetlink,$roll);
                        return;
                    }
                }
            }

        //  LETS GET RID OF THE MINES NOW 
        explode_mines($targetlink,$roll);
        }
        else
        {
            // FOR SOME REASON THIS WAS CALLED WITHOUT ANY SECTOR DEFENSES TO ATTACK 
            return;
        }
    }
}
?>
