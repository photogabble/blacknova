<?php
function ai_move()
{
    // Dynamic functions
    dynamic_loader ($db, "seed_mt_rand.php");
    dynamic_loader ($db, "playerlog.php");

    //  SETUP GENERAL VARIABLES
    global $playerinfo, $sector_max, $targetlink, $ai_isdead;
    global $db, $db_prefix;
    seed_mt_rand();

    //  OBTAIN A TARGET LINK 
    if ($targetlink = $playerinfo['sector'])
    {
        $targetlink = 0;
    }

    $linkres = $db->Execute ("SELECT * FROM {$db_prefix}links WHERE link_start='$playerinfo[sector]'");
  
    if (mt_rand(1,100)<=50)
    {
        // Generate a random sector number
        $wormto=mt_rand(1,($sector_max-15));
        $limitloop=1;                        //  Limit the number of loops
        while (!$targetlink>0 && $limitloop<15)
        {
            //  OBTAIN SECTOR INFORMATION 
            $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db_prefix}universe WHERE sector_id='$wormto'");
            $sectrow = $sectres->fields;
            $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM {$db_prefix}zones WHERE zone_id=$sectrow[zone_id]");
            $zonerow = $zoneres->fields;
            if ($zonerow['allow_attack']== "Y")
            {
                $targetlink= $wormto;
                playerlog($playerinfo['ship_id'], "LOG_RAW", "Used a wormhole to warp to a zone where attacks are allowed."); 
            }

            $wormto++;
            $wormto++;
            $limitloop++;
        }
    }
    elseif ($linkres>0)
    {
        while (!$linkres->EOF)
        {
            $row = $linkres->fields;
            //  OBTAIN SECTOR INFORMATION 
            $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db_prefix}universe WHERE sector_id='$row[link_dest]'");
            $sectrow = $sectres->fields;
            $zoneres = $db->Execute("SELECT zone_id,allow_attack FROM {$db_prefix}zones WHERE zone_id=$sectrow[zone_id]");
            $zonerow = $zoneres->fields;
            if ($zonerow['allow_attack']== "Y")                        // DEST LINK MUST ALLOW ATTACKING 
            {
                $setlink=mt_rand(0,2);                        // 33% CHANCE OF REPLACING DEST LINK WITH THIS ONE 
                if ($setlink== 0 || !$targetlink>0)          // UNLESS THERE IS NO DEST LINK, CHHOSE THIS ONE 
                {
                    $targetlink= $row['link_dest'];
                }
            }

            $linkres->MoveNext();
        }
    }

    // IF NO ACCEPTABLE LINK, USE A WORM HOLE
    if (!$targetlink>0)
    {
        // Generate a random sector number
        $wormto=mt_rand(1,($sector_max-15));
        $limitloop=1;                        //  Limit the number of loops
        while (!$targetlink>0 && $limitloop<15)
        {
            //  OBTAIN SECTOR INFORMATION 
            $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db_prefix}universe WHERE sector_id='$wormto'");
            $sectrow = $sectres->fields;
            $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM {$db_prefix}zones WHERE zone_id=$sectrow[zone_id]");
            $zonerow = $zoneres->fields;
            if ($zonerow['allow_attack']== "Y")
            {
                $targetlink= $wormto;
                playerlog($playerinfo['ship_id'], "LOG_RAW", "Used a wormhole to warp to a zone where attacks are allowed."); 
            }

            $wormto++;
            $wormto++;
            $limitloop++;
        }
    } 

    //  CHECK FOR SECTOR DEFENSE
    if ($targetlink > 0)
    {
        $resultf = $db->Execute ("SELECT * FROM {$db_prefix}sector_defense WHERE sector_id='$targetlink' and defense_type ='F' ORDER BY quantity DESC");
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

        $resultm = $db->Execute ("SELECT * FROM {$db_prefix}sector_defense WHERE sector_id='$targetlink' and defense_type ='M'");
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
        // DEST LINK HAS DEFENSES 
        {
            if ($playerinfo['aggression'] == 2 || $playerinfo['aggression'] == 1 || !(strstr($playerinfo['character_name'], 'Flag')))
            {
                //  ATTACK SECTOR DEFENSES 
                // Dynamic functions
                dynamic_loader ($db, "ai_tosecdef.php");
                ai_tosecdef();
                return;
            }
            else
            {
                playerlog($playerinfo['ship_id'], "LOG_RAW", "Move failed, the sector is defended by $total_sector_fighters fighters and $total_sector_mines mines."); 
                return;
            }
        }
    }

    // DO MOVE TO TARGET LINK 
    if ($targetlink > 0)
    {
        $stamp = date("Y-m-d H-i-s");
        $query = "UPDATE {$db_prefix}ships SET last_login='$stamp', turns_used=turns_used+1, turns=turns-1, sector=$targetlink WHERE ship_id=$playerinfo[ship_id]";
        $move_result = $db->Execute ("$query");
        if (!$move_result)
        {
            $error = $db->ErrorMsg();
            playerlog($playerinfo['ship_id'], "LOG_RAW", "Move failed with error: $error "); 
        }
        else
        {
            // playerlog($playerinfo['ship_id'], "LOG_RAW", "Moved to $targetlink without incident."); 
        }
    }
    else
    {                                            // WE HAVE NO TARGET LINK FOR SOME REASON 
        playerlog($playerinfo['ship_id'], "LOG_RAW", "Move failed due to lack of target link.");
        $targetlink = $playerinfo['sector'];         // RESET TARGET LINK SO IT IS NOT ZERO 
    }
}
?>