<?
if (preg_match("/check_mines.php/i", $PHP_SELF)) {
   die("You can not access this file directly!");
}

include("languages/$lang");

//Put the sector information into the array "sectorinfo"

$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");

$sectorinfo=$result2->fields;

//Put the defence information into the array "defenceinfo"  

$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] 
WHERE sector_id='$sector' and defence_type ='M'");

$num_defences = 0;
$total_sector_mines = 0;
$owner = true;
if($result3 > 0)
{
   while(!$result3->EOF)
   {
      $row=$result3->fields;
      $defences[$num_defences] = $row;
      $total_sector_mines += $defences[$i]['quantity'];
      if($defences[$i][ship_id] != $playerinfo[ship_id])
      {
         $owner = false;
      }
      $num_defences++;
      $result3->MoveNext();
   }
}

// Compute the ship average...if its too low then the ship will not hit mines...     

$shipavg = ($playerinfo[hull] + $playerinfo[engines] + $playerinfo[computer] + $playerinfo[beams] + $playerinfo[torp_launchers] + $playerinfo[shields] + $playerinfo[armour]) / 7;

// The mines will attack if 4 conditions are met
//    1) There is at least 1 group of mines in the sector
//    2) There is at least 1 mine in the sector 
//    3) You are not the owner or on the team of the owner - team 0 dosent count
//    4) You ship is at least $mine_hullsize (setable in config.php) big

if ($num_defences > 0 && $total_sector_mines > 0 && !$owner && $shipavg > $mine_hullsize)
{
   // find out if the mine owner and player are on the same team
   $fm_owner = $defences[0][ship_id];
   $result2 = $db->Execute("SELECT * from $dbtables[ships] where ship_id=$fm_owner");

   $mine_owner = $result2->fields;
   if ($mine_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
   {
      // Well...you hit mines, shame...
      bigtitle();
      
      $ok=0;


      // New Behaivor
      // Before we had a issue where if there where a lot of mines in the sector the result will go -
      // I changed the behaivor so that rand will chose a % of mines to attack will
      // (it will always be at least 5% of the mines or at the very least 1 mine);
      // and if you are very unlucky they all will hit you
      $pren = (rand(5, 100)/100);
      $roll = round( $pren * $total_sector_mines - 1) + 1;
      $totalmines = $totalmines - $roll;

      // Red Alert: You are hit sir!!! Tell the player and put it in the log

      $l_chm_youhitsomemines = str_replace("[chm_roll]", $roll, $l_chm_youhitsomemines);
      echo "$l_chm_youhitsomemines<BR>";
      playerlog($playerinfo[ship_id], LOG_HIT_MINES, "$roll|$sector");

      // Tell the owner that his mines where hit - It might help to speak the same language though
		if($mine_owner[lang] != $playerinfo[lang])
		{
			include("languages/".$mine_owner['lang'].".inc");
		}
			echo "you dont speak the same language";
      $l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", $playerinfo[character_name], $l_chm_hehitminesinsector);
      $l_chm_hehitminesinsector = str_replace("[chm_roll]", "$roll", $l_chm_hehitminesinsector);
      $l_chm_hehitminesinsector = str_replace("[chm_sector]", $sector, $l_chm_hehitminesinsector);

      message_defence_owner($sector,"$l_chm_hehitminesinsector");

		if($mine_owner[lang] != $playerinfo[lang])
		{
			include("languages/".$playerinfo['lang'].".inc");
		}

      // If the player has enough mine deflectors then subtract the ammount and continue
      if($playerinfo[dev_minedeflector] >= $roll)
      {

         $l_chm_youlostminedeflectors = str_replace("[chm_roll]", $roll, $l_chm_youlostminedeflectors);
         echo "$l_chm_youlostminedeflectors<BR>";
         $result2 = $db->Execute("UPDATE $dbtables[ships] set dev_minedeflector=dev_minedeflector-$roll where ship_id=$playerinfo[ship_id]");

      } else {
            if($playerinfo[dev_minedeflector] > 0)
            {
               echo "$l_chm_youlostallminedeflectors<BR>";
            }
            else
            {
               echo "$l_chm_youhadnominedeflectors<BR>";
            }
         
            // Shields up sir!
            $mines_left = $roll - $playerinfo[dev_minedeflector];
            $playershields = NUM_SHIELDS($playerinfo[shields]);
            if($playershields > $playerinfo[ship_energy])
            {
               $playershields=$playerinfo[ship_energy];
            }
            if($playershields >= $mines_left)
            {
               $l_chm_yourshieldshitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourshieldshitforminesdmg);
               echo "$l_chm_yourshieldshitforminesdmg<BR>";

               $result2 = $db->Execute("UPDATE $dbtables[ships] set ship_energy=ship_energy-$mines_left, dev_minedeflector=0 where ship_id=$playerinfo[ship_id]");
               if($playershields == $mines_left)
                  echo "$l_chm_yourshieldsaredown<BR>";
            }
            else
            {
               // Direct hit sir
               echo "$l_chm_youlostallyourshields<BR>";
               $mines_left = $mines_left - $playershields;
               if($playerinfo[armour_pts] >= $mines_left)
               {
                  $l_chm_yourarmorhitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourarmorhitforminesdmg);
                  echo "$l_chm_yourarmorhitforminesdmg<BR>";
                  $result2 = $db->Execute("UPDATE $dbtables[ships] set armour_pts=armour_pts-$mines_left,ship_energy=0,dev_minedeflector=0 where ship_id=$playerinfo[ship_id]");
                  if($playerinfo[armour_pts] == $mines_left)
                     echo "$l_chm_yourhullisbreached<BR>";
               }
               else
               {
                  // BOOM
                  $pod = $playerinfo[dev_escapepod];

                  playerlog($playerinfo[ship_id], LOG_SHIP_DESTROYED_MINES, "$sector|$pod");
                  $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]", $playerinfo[character_name], $l_chm_hewasdestroyedbyyourmines);
                  $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", $sector, $l_chm_hewasdestroyedbyyourmines);
                  
                  message_defence_owner($sector,"$l_chm_hewasdestroyedbyyourmines");

                  echo "$l_chm_yourshiphasbeendestroyed<BR><BR>";

                  // Live...

                  if($playerinfo[dev_escapepod] == "Y")

                  {
                     $rating=round($playerinfo[rating]/2);
                     echo "$l_chm_luckescapepod<BR><BR>";
                     $db->Execute("UPDATE $dbtables[ships] SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating',cleared_defences=' ',dev_lssd='N' WHERE ship_id=$playerinfo[ship_id]");
                     cancel_bounty($playerinfo[ship_id]);
                  }
                  else
                  {
                     // or die!
                     cancel_bounty($playerinfo[ship_id]);
                     db_kill_player($playerinfo['ship_id']);
                  }
               }
            }
         }
      explode_mines($sector,$roll);
      }
   }
?>



