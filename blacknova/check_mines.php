<?
    if (preg_match("/check_mines.php/i", $PHP_SELF)) {
        echo "You can not access this file directly!";
        die();
    }

    include("languages/$lang");

    $result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
    //Put the sector information into the array "sectorinfo"
    $sectorinfo=$result2->fields;
    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='M'");
    //Put the defence information into the array "defenceinfo"
    $i = 0;
    $total_sector_mines = 0;
    $owner = true;
    if($result3 > 0)
    {
       while(!$result3->EOF)
       {
          $row=$result3->fields;
          $defences[$i] = $row;
           $total_sector_mines += $defences[$i]['quantity'];
          if($defences[$i][player_id] != $playerinfo[player_id])
          {
             $owner = false;
          }
          $i++;
          $result3->MoveNext();
       }
    }
    $num_defences = $i;
    $shipavg = $shipinfo[hull] + $shipinfo[engines] + $shipinfo[computer] + $shipinfo[beams] + $shipinfo[torp_launchers] + $shipinfo[shields] + $shipinfo[armour];
    $shipavg /= 7;

    if ($num_defences > 0 && $total_sector_mines > 0 && !$owner && $shipavg > $mine_hullsize)
    {
        $fm_owner = $defences[0][player_id];
        $result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fm_owner");
        $mine_owner = $result2->fields;
        if ($mine_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
        // find out if the mine owner and player are on the same team
        {
	   // Lets blow up some mines!
           bigtitle();
           $ok=0;
           $totalmines = $total_sector_mines;
           if ($totalmines>1)
           {
              $roll = rand(1,$totalmines);
           }
           else
           {
              $roll = 1;
           }
           $totalmines = $totalmines - $roll;
           $l_chm_youhitsomemines = str_replace("[chm_roll]", $roll, $l_chm_youhitsomemines);
           echo "$l_chm_youhitsomemines<BR>";
           playerlog($playerinfo[player_id], LOG_HIT_MINES, "$roll|$sector");

           $l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", $playerinfo[character_name], $l_chm_hehitminesinsector);
           $l_chm_hehitminesinsector = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector);
           $l_chm_hehitminesinsector = str_replace("[chm_sector]", $sector, $l_chm_hehitminesinsector);
           message_defence_owner($sector,"$l_chm_hehitminesinsector");

           if($shipinfo[dev_minedeflector] >= $roll)
           {
              $l_chm_youlostminedeflectors = str_replace("[chm_roll]", $roll, $l_chm_youlostminedeflectors);
              echo "$l_chm_youlostminedeflectors<BR>";
              $result2 = $db->Execute("UPDATE $dbtables[ships] set dev_minedeflector=dev_minedeflector-$roll where ship_id=$shipinfo[ship_id]");
           }
           else
           {
              if($shipinfo[dev_minedeflector] > 0)
              {
                 echo "$l_chm_youlostallminedeflectors<BR>";
              }
              else
              {
                 echo "$l_chm_youhadnominedeflectors<BR>";
              }

              $mines_left = $roll - $shipinfo[dev_minedeflector];
              $playershields = NUM_SHIELDS($shipinfo[shields]);
              
              if($playershields > $shipinfo[energy])
              {
                 $playershields=$shipinfo[energy];
              }

              if($playershields >= $mines_left)
              {
                 $l_chm_yourshieldshitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourshieldshitforminesdmg);
                 echo "$l_chm_yourshieldshitforminesdmg<BR>";
                 $result2 = $db->Execute("UPDATE $dbtables[ships] set energy=energy-$mines_left, dev_minedeflector=0 WHERE ship_id=$shipinfo[ship_id]");
                 if($playershields == $mines_left) echo "$l_chm_yourshieldsaredown<BR>";
              }
              else
              {
                 echo "$l_chm_youlostallyourshields<BR>";
                 $mines_left = $mines_left - $playershields;
                 if($shipinfo[armour_pts] >= $mines_left)
                 {
                    $l_chm_yourarmorhitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourarmorhitforminesdmg);
                    echo "$l_chm_yourarmorhitforminesdmg<BR>";
                    $result2 = $db->Execute("UPDATE $dbtables[ships] SET armour_pts=armour_pts-$mines_left,energy=0,dev_minedeflector=0 WHERE ship_id=$shipinfo[ship_id]");
                    if($shipinfo[armour_pts] == $mines_left) echo "$l_chm_yourhullisbreached<BR>";
                 }
                 else
                 {
                    $pod = $shipinfo[dev_escapepod];
                    playerlog($playerinfo[player_id], LOG_SHIP_DESTROYED_MINES, "$sector|$pod");
                    
                    $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]", $playerinfo[character_name], $l_chm_hewasdestroyedbyyourmines);
                    $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", $sector, $l_chm_hewasdestroyedbyyourmines);
                    message_defence_owner($sector,"$l_chm_hewasdestroyedbyyourmines");
                    echo "$l_chm_yourshiphasbeendestroyed<BR><BR>";
                    
                    if($shipinfo[dev_escapepod] == "Y")
                    {
                       $rating=round($playerinfo[rating]/2);
                       echo "$l_chm_luckescapepod<BR><BR>";
                       $db->Execute("UPDATE $dbtables[ships] SET class=1, hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector_id=0,organics=0,ore=0,goods=0,energy=$start_energy,colonists=0,fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',cleared_defences=' ',dev_lssd='N' WHERE ship_id=$shipinfo[ship_id]");
                       $db->Execute("UPDATE $dbtables[players] SET rating='$rating' WHERE player_id=$playerinfo[player_id]");
                       cancel_bounty($playerinfo[player_id]);
                    }
                    else
                    {
                       cancel_bounty($playerinfo[player_id]);
                       db_kill_player($playerinfo['player_id']);
                    }
                 }
              }
           }
           explode_mines($sector,$roll);
        }
    }

?>

