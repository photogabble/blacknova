<?

function furangeetoship($ship_id)
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $attackerbeams;
  global $attackerfighters;
  global $attackershields;
  global $attackertorps;
  global $attackerarmor;
  global $attackertorpdamage;
  global $start_energy;
  global $playerinfo;
  global $rating_combat_factor;
  global $upgrade_cost;
  global $upgrade_factor;
  global $sector_max;
  global $furangeeisdead;
  global $db, $dbtables;

  // *********************************
  // *** LOOKUP TARGET DETAILS    ****
  // *********************************
  $db->Execute("LOCK TABLES $dbtables[players] WRITE, $dbtables[ships] WRITE, $dbtables[universe] WRITE, $dbtables[zones] READ, $dbtables[planets] READ, $dbtables[news] WRITE, $dbtables[logs] WRITE");
  $resultt = $db->Execute ("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE ship_id='$ship_id'");
  $targetinfo=$resultt->fields;

  // *********************************
  // ** VERIFY SECTOR ALLOWS ATTACK **
  // *********************************
  $sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$targetinfo[sector_id]'");
  $sectrow = $sectres->fields;
  $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
  $zonerow = $zoneres->fields;
  if ($zonerow[allow_attack]=="N")                        //*** DEST LINK MUST ALLOW ATTACKING ***
  {
    playerlog($playerinfo[player_id], LOG_RAW, "Attack failed, you are in a sector that prohibits attacks."); 
    return;
  }

  // *********************************
  // *** USE EMERGENCY WARP DEVICE ***
  // *********************************
  if ($targetinfo[dev_emerwarp]>0)
  {
    playerlog($targetinfo[player_id], LOG_ATTACK_EWD, "Furangee $playerinfo[character_name]");
    $dest_sector=rand(0,$sector_max);
    $result_warp = $db->Execute ("UPDATE $dbtables[ships] SET sector_id=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$targetinfo[ship_id]");
    return;
  }

  // *********************************
  // *** SETUP ATTACKER VARIABLES ****
  // *********************************
  $attackerbeams = NUM_BEAMS($playerinfo[beams]);
  if ($attackerbeams > $playerinfo[energy]) $attackerbeams = $playerinfo[energy];
  $playerinfo[energy] = $playerinfo[energy] - $attackerbeams;
  $attackershields = NUM_SHIELDS($playerinfo[shields]);
  if ($attackershields > $playerinfo[energy]) $attackershields = $playerinfo[energy];
  $playerinfo[energy] = $playerinfo[energy] - $attackershields;
  $attackertorps = round(mypw($level_factor, $playerinfo[torp_launchers])) * 2;
  if ($attackertorps > $playerinfo[torps]) $attackertorps = $playerinfo[torps];
  $playerinfo[torps] = $playerinfo[torps] - $attackertorps;
  $attackertorpdamage = $torp_dmg_rate * $attackertorps;
  $attackerarmor = $playerinfo[armour_pts];
  $attackerfighters = $playerinfo[fighters];
  $playerdestroyed = 0;

  // *********************************
  // **** SETUP TARGET VARIABLES *****
  // *********************************
  $targetbeams = NUM_BEAMS($targetinfo[beams]);
  if ($targetbeams>$targetinfo[energy]) $targetbeams=$targetinfo[energy];
  $targetinfo[energy]=$targetinfo[energy]-$targetbeams;
  $targetshields = NUM_SHIELDS($targetinfo[shields]);
  if ($targetshields>$targetinfo[energy]) $targetshields=$targetinfo[energy];
  $targetinfo[energy]=$targetinfo[energy]-$targetshields;
  $targettorpnum = round(mypw($level_factor,$targetinfo[torp_launchers]))*2;
  if ($targettorpnum > $targetinfo[torps]) $targettorpnum = $targetinfo[torps];
  $targetinfo[torps] = $targetinfo[torps] - $targettorpnum;
  $targettorpdmg = $torp_dmg_rate*$targettorpnum;
  $targetarmor = $targetinfo[armour_pts];
  $targetfighters = $targetinfo[fighters];
  $targetdestroyed = 0;

  // *********************************
  // **** BEGIN COMBAT PROCEDURES ****
  // *********************************
  if($attackerbeams > 0 && $targetfighters > 0)
  {                         //******** ATTACKER HAS BEAMS - TARGET HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($attackerbeams > round($targetfighters / 2))
    {                                  //****** ATTACKER BEAMS GT HALF TARGET FIGHTERS ******
      $lost = $targetfighters-(round($targetfighters/2));
      $targetfighters = $targetfighters-$lost;                 //**** T LOOSES HALF ALL FIGHTERS ****
      $attackerbeams = $attackerbeams-$lost;                   //**** A LOOSES BEAMS EQ TO HALF T FIGHTERS ****
    } else
    {                                  //****** ATTACKER BEAMS LE HALF TARGET FIGHTERS ******
      $targetfighters = $targetfighters-$attackerbeams;        //**** T LOOSES FIGHTERS EQ TO A BEAMS ****
      $attackerbeams = 0;                                      //**** A LOOSES ALL BEAMS ****
    }   
  }
  if($attackerfighters > 0 && $targetbeams > 0)
  {                         //******** TARGET HAS BEAMS - ATTACKER HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($targetbeams > round($attackerfighters / 2))
    {                                  //****** TARGET BEAMS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;               //**** A LOOSES HALF ALL FIGHTERS ****
      $targetbeams=$targetbeams-$lost;                         //**** T LOOSES BEAMS EQ TO HALF A FIGHTERS ****
    } else
    {                                  //****** TARGET BEAMS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targetbeams;        //**** A LOOSES FIGHTERS EQ TO T BEAMS **** 
      $targetbeams=0;                                          //**** T LOOSES ALL BEAMS ****
    }
  }
  if($attackerbeams > 0)
  {                         //******** ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($attackerbeams > $targetshields)
    {                                  //****** ATTACKER BEAMS GT TARGET SHIELDS ******
      $attackerbeams=$attackerbeams-$targetshields;            //**** A LOOSES BEAMS EQ TO T SHIELDS ****
      $targetshields=0;                                        //**** T LOOSES ALL SHIELDS ****
    } else
    {                                  //****** ATTACKER BEAMS LE TARGET SHIELDS ******
      $targetshields=$targetshields-$attackerbeams;            //**** T LOOSES SHIELDS EQ TO A BEAMS ****
      $attackerbeams=0;                                        //**** A LOOSES ALL BEAMS ****
    }
  }
  if($targetbeams > 0)
  {                         //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($targetbeams > $attackershields)
    {                                  //****** TARGET BEAMS GT ATTACKER SHIELDS ******
      $targetbeams=$targetbeams-$attackershields;              //**** T LOOSES BEAMS EQ TO A SHIELDS ****
      $attackershields=0;                                      //**** A LOOSES ALL SHIELDS ****
    } else
    {                                  //****** TARGET BEAMS LE ATTACKER SHIELDS ****** 
      $attackershields=$attackershields-$targetbeams;          //**** A LOOSES SHIELDS EQ TO T BEAMS ****
      $targetbeams=0;                                          //**** T LOOSES ALL BEAMS ****
    }
  }
  if($attackerbeams > 0)
  {                         //******** ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR ********
    if($attackerbeams > $targetarmor)
    {                                  //****** ATTACKER BEAMS GT TARGET ARMOR ******
      $attackerbeams=$attackerbeams-$targetarmor;              //**** A LOOSES BEAMS EQ TO T ARMOR ****
      $targetarmor=0;                                          //**** T LOOSES ALL ARMOR (T DESTROYED) ****
    } else
    {                                  //****** ATTACKER BEAMS LE TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackerbeams;                //**** T LOOSES ARMORS EQ TO A BEAMS ****
      $attackerbeams=0;                                        //**** A LOOSES ALL BEAMS ****
    } 
  }
  if($targetbeams > 0)
  {                        //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR ******** 
    if($targetbeams > $attackerarmor)
    {                                 //****** TARGET BEAMS GT ATTACKER ARMOR ******
      $targetbeams=$targetbeams-$attackerarmor;                //**** T LOOSES BEAMS EQ TO A ARMOR ****
      $attackerarmor=0;                                        //**** A LOOSES ALL ARMOR (A DESTROYED) ****
    } else
    {                                 //****** TARGET BEAMS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetbeams;              //**** A LOOSES ARMOR EQ TO T BEAMS ****
      $targetbeams=0;                                          //**** T LOOSES ALL BEAMS ****
    } 
  }
  if($targetfighters > 0 && $attackertorpdamage > 0)
  {                        //******** ATTACKER FIRES TORPS - TARGET HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($attackertorpdamage > round($targetfighters / 2))
    {                                 //****** ATTACKER FIRED TORPS GT HALF TARGET FIGHTERS ******
      $lost=$targetfighters-(round($targetfighters/2));
      $targetfighters=$targetfighters-$lost;                   //**** T LOOSES HALF ALL FIGHTERS ****
      $attackertorpdamage=$attackertorpdamage-$lost;           //**** A LOOSES FIRED TORPS EQ TO HALF T FIGHTERS ****
    } else
    {                                 //****** ATTACKER FIRED TORPS LE HALF TARGET FIGHTERS ******
      $targetfighters=$targetfighters-$attackertorpdamage;     //**** T LOOSES FIGHTERS EQ TO A TORPS FIRED ****
      $attackertorpdamage=0;                                   //**** A LOOSES ALL TORPS FIRED ****
    }
  }
  if($attackerfighters > 0 && $targettorpdmg > 0)
  {                        //******** TARGET FIRES TORPS - ATTACKER HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($targettorpdmg > round($attackerfighters / 2))
    {                                 //****** TARGET FIRED TORPS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;               //**** A LOOSES HALF ALL FIGHTERS ****
      $targettorpdmg=$targettorpdmg-$lost;                     //**** T LOOSES FIRED TORPS EQ TO HALF A FIGHTERS ****
    } else
    {                                 //****** TARGET FIRED TORPS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targettorpdmg;      //**** A LOOSES FIGHTERS EQ TO T TORPS FIRED ****
      $targettorpdmg=0;                                        //**** T LOOSES ALL TORPS FIRED ****
    }
  }
  if($attackertorpdamage > 0)
  {                        //******** ATTACKER FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR ********
    if($attackertorpdamage > $targetarmor)
    {                                 //****** ATTACKER FIRED TORPS GT HALF TARGET ARMOR ******
      $attackertorpdamage=$attackertorpdamage-$targetarmor;    //**** A LOOSES FIRED TORPS EQ TO T ARMOR ****
      $targetarmor=0;                                          //**** T LOOSES ALL ARMOR (T DESTROYED) ****
    } else
    {                                 //****** ATTACKER FIRED TORPS LE HALF TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackertorpdamage;           //**** T LOOSES ARMOR EQ TO A TORPS FIRED ****
      $attackertorpdamage=0;                                   //**** A LOOSES ALL TORPS FIRED ****
    } 
  }
  if($targettorpdmg > 0)
  {                        //******** TARGET FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR ********
    if($targettorpdmg > $attackerarmor)
    {                                 //****** TARGET FIRED TORPS GT HALF ATTACKER ARMOR ******
      $targettorpdmg=$targettorpdmg-$attackerarmor;            //**** T LOOSES FIRED TORPS EQ TO A ARMOR ****
      $attackerarmor=0;                                        //**** A LOOSES ALL ARMOR (A DESTROYED) ****
    } else
    {                                 //****** TARGET FIRED TORPS LE HALF ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targettorpdmg;            //**** A LOOSES ARMOR EQ TO T TORPS FIRED ****
      $targettorpdmg=0;                                        //**** T LOOSES ALL TORPS FIRED ****
    } 
  }
  if($attackerfighters > 0 && $targetfighters > 0)
  {                        //******** ATTACKER HAS FIGHTERS - TARGET HAS FIGHTERS - FIGHTERS VS FIGHTERS ********
    if($attackerfighters > $targetfighters)
    {                                 //****** ATTACKER FIGHTERS GT TARGET FIGHTERS ******
      $temptargfighters=0;                                     //**** T WILL LOOSE ALL FIGHTERS ****
    } else
    {                                 //****** ATTACKER FIGHTERS LE TARGET FIGHTERS ******
      $temptargfighters=$targetfighters-$attackerfighters;     //**** T WILL LOOSE FIGHTERS EQ TO A FIGHTERS ****
    }
    if($targetfighters > $attackerfighters)
    {                                 //****** TARGET FIGHTERS GT ATTACKER FIGHTERS ******
      $tempplayfighters=0;                                     //**** A WILL LOOSE ALL FIGHTERS ****
    } else
    {                                 //****** TARGET FIGHTERS LE ATTACKER FIGHTERS ******
      $tempplayfighters=$attackerfighters-$targetfighters;     //**** A WILL LOOSE FIGHTERS EQ TO T FIGHTERS ****
    }     
    $attackerfighters=$tempplayfighters;
    $targetfighters=$temptargfighters;
  }
  if($attackerfighters > 0)
  {                        //******** ATTACKER HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR ********
    if($attackerfighters > $targetarmor)
    {                                 //****** ATTACKER FIGHTERS GT TARGET ARMOR ******
      $targetarmor=0;                                          //**** T LOOSES ALL ARMOR (T DESTROYED) ****
    } else
    {                                 //****** ATTACKER FIGHTERS LE TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackerfighters;             //**** T LOOSES ARMOR EQ TO A FIGHTERS **** 
    }
  }
  if($targetfighters > 0)
  {                        //******** TARGET HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR ********
    if($targetfighters > $attackerarmor)
    {                                 //****** TARGET FIGHTERS GT ATTACKER ARMOR ******
      $attackerarmor=0;                                        //**** A LOOSES ALL ARMOR (A DESTROYED) ****
    } else
    {                                 //****** TARGET FIGHTERS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetfighters;           //**** A LOOSES ARMOR EQ TO T FIGHTERS ****
    }
  }

  // *********************************
  // **** FIX NEGATIVE VALUE VARS ****
  // *********************************
  if ($attackerfighters < 0) $attackerfighters = 0;
  if ($attackertorps    < 0) $attackertorps = 0;
  if ($attackershields  < 0) $attackershields = 0;
  if ($attackerbeams    < 0) $attackerbeams = 0;
  if ($attackerarmor    < 0) $attackerarmor = 0;
  if ($targetfighters   < 0) $targetfighters = 0;
  if ($targettorpnum    < 0) $targettorpnum = 0;
  if ($targetshields    < 0) $targetshields = 0;
  if ($targetbeams      < 0) $targetbeams = 0;
  if ($targetarmor      < 0) $targetarmor = 0;

  // *********************************
  // *** DEAL WITH DESTROYED SHIPS ***
  // *********************************

  // *********************************
  // *** TARGET SHIP WAS DESTROYED ***
  // *********************************
  if(!$targetarmor>0)
  {
    if($targetinfo[dev_escapepod] == "Y")
    // ****** TARGET HAD ESCAPE POD ******
    {
      $rating=round($targetinfo[rating]/2);
      $db->Execute("UPDATE $dbtables[ships] SET class=1, hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector_id=0, ore=0, organics=0, energy=1000, colonists=0, goods=0, fighters=100, on_planet='N', planet_id=0, dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, destroyed='N',dev_lssd='N' WHERE ship_id=$targetinfo[ship_id]");
      playerlog($targetinfo[player_id], LOG_ATTACK_LOSE, "Furangee $playerinfo[character_name]|Y"); 
    } else
    // ****** TARGET HAD NO POD ******
    {
      playerlog($targetinfo[player_id], LOG_ATTACK_LOSE, "Furangee $playerinfo[character_name]|N"); 
      db_kill_player($targetinfo['player_id']);
    }   
    if($attackerarmor>0)
    {
      // ****** ATTACKER STILL ALIVE TO SALVAGE TRAGET ******
      $rating_change=round($targetinfo[rating]*$rating_combat_factor);
      $free_ore = round($targetinfo[ore]/2);
      $free_organics = round($targetinfo[organics]/2);
      $free_goods = round($targetinfo[goods]/2);
      $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ore] - $playerinfo[organics] - $playerinfo[goods] - $playerinfo[colonists];
      if($free_holds > $free_goods) 
      {                                                        //****** FIGURE OUT WHAT WE CAN CARRY ******
        $salv_goods=$free_goods;
        $free_holds=$free_holds-$free_goods;
      } elseif($free_holds > 0)
      {
        $salv_goods=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_goods=0;
      }
      if($free_holds > $free_ore)
      {
        $salv_ore=$free_ore;
        $free_holds=$free_holds-$free_ore;
      } elseif($free_holds > 0)
      {
        $salv_ore=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_ore=0;
      }
      if($free_holds > $free_organics)
      {
        $salv_organics=$free_organics;
        $free_holds=$free_holds-$free_organics;
      } elseif($free_holds > 0)
      {
        $salv_organics=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_organics=0;
      }
      $ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $targetinfo[hull]))+round(mypw($upgrade_factor, $targetinfo[engines]))+round(mypw($upgrade_factor, $targetinfo[power]))+round(mypw($upgrade_factor, $targetinfo[computer]))+round(mypw($upgrade_factor, $targetinfo[sensors]))+round(mypw($upgrade_factor, $targetinfo[beams]))+round(mypw($upgrade_factor, $targetinfo[torp_launchers]))+round(mypw($upgrade_factor, $targetinfo[shields]))+round(mypw($upgrade_factor, $targetinfo[armor]))+round(mypw($upgrade_factor, $targetinfo[cloak])));
      $ship_salvage_rate=rand(10,20);
      $ship_salvage=$ship_value*$ship_salvage_rate/100;
      playerlog($playerinfo[player_id], LOG_RAW, "Attack successful, $targetinfo[character_name] was defeated and salvaged for $ship_salvage credits."); 
      $db->Execute ("UPDATE $dbtables[ships] SET ore=ore+$salv_ore, organics=organics+$salv_organics, goods=goods+$salv_goods WHERE ship_id=$playerinfo[ship_id]");
      $db->Execute("UPDATE $dbtables[players] SET credits=credits+$ship_salvage WHERE player_id=$playerinfo[player_id]");
      
      $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
      $fighters_lost = $playerinfo[fighters] - $attackerfighters;
      $energy=$playerinfo[energy];
      $db->Execute ("UPDATE $dbtables[ships] SET energy=$energy,fighters=fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost WHERE ship_id=$playerinfo[ship_id]");
    }
  }

  // *********************************
  // *** TARGET AND ATTACKER LIVE  ***
  // *********************************
  if($targetarmor>0 && $attackerarmor>0)
  {
    $rating_change=round($targetinfo[rating]*.1);
    $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
    $fighters_lost = $playerinfo[fighters] - $attackerfighters;
    $energy=$playerinfo[energy];
    $target_rating_change=round($targetinfo[rating]/2);
    $target_armor_lost = $targetinfo[armour_pts] - $targetarmor;
    $target_fighters_lost = $targetinfo[fighters] - $targetfighters;
    $target_energy=$targetinfo[energy];
    playerlog($playerinfo[player_id], LOG_RAW, "Attack failed, $targetinfo[character_name] survived."); 
    playerlog($targetinfo[player_id], LOG_ATTACK_WIN, "Furangee $playerinfo[character_name]|$target_armor_lost|$target_fighters_lost");
    $db->Execute ("UPDATE $dbtables[ships] SET energy=$energy,fighters=fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost WHERE ship_id=$playerinfo[ship_id]");
    $db->Execute ("UPDATE $dbtables[players] SET rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");
    $db->Execute ("UPDATE $dbtables[ships] SET energy=$target_energy,fighters=fighters-$target_fighters_lost, armour_pts=armour_pts-$target_armor_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
    $db->Execute ("UPDATE $dbtables[players] SET rating=rating-$target_rating_change WHERE player_id=$targetinfo[player_id]");
  }

  // *********************************
  // *** ATTACKER SHIP DESTROYED   ***
  // *********************************
  if(!$attackerarmor>0)
  {
    playerlog($playerinfo[player_id], LOG_RAW, "$targetinfo[character_name] destroyed your ship!"); 
    db_kill_player($playerinfo['player_id']);
    $furangeeisdead = 1;
    if($targetarmor>0)
    {
      // ****** TARGET STILL ALIVE TO SALVAGE ATTACKER ******
      $rating_change=round($playerinfo[rating]*$rating_combat_factor);
      $free_ore = round($playerinfo[ore]/2);
      $free_organics = round($playerinfo[organics]/2);
      $free_goods = round($playerinfo[goods]/2);
      $free_holds = NUM_HOLDS($targetinfo[hull]) - $targetinfo[ore] - $targetinfo[organics] - $targetinfo[goods] - $targetinfo[colonists];
      if($free_holds > $free_goods) 
      {                                                        //****** FIGURE OUT WHAT TARGET CAN CARRY ******
        $salv_goods=$free_goods;
        $free_holds=$free_holds-$free_goods;
      } elseif($free_holds > 0)
      {
        $salv_goods=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_goods=0;
      }
      if($free_holds > $free_ore)
      {
        $salv_ore=$free_ore;
        $free_holds=$free_holds-$free_ore;
      } elseif($free_holds > 0)
      {
        $salv_ore=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_ore=0;
      }
      if($free_holds > $free_organics)
      {
        $salv_organics=$free_organics;
        $free_holds=$free_holds-$free_organics;
      } elseif($free_holds > 0)
      {
        $salv_organics=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_organics=0;
      }
      $ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $playerinfo[hull]))+round(mypw($upgrade_factor, $playerinfo[engines]))+round(mypw($upgrade_factor, $playerinfo[power]))+round(mypw($upgrade_factor, $playerinfo[computer]))+round(mypw($upgrade_factor, $playerinfo[sensors]))+round(mypw($upgrade_factor, $playerinfo[beams]))+round(mypw($upgrade_factor, $playerinfo[torp_launchers]))+round(mypw($upgrade_factor, $playerinfo[shields]))+round(mypw($upgrade_factor, $playerinfo[armor]))+round(mypw($upgrade_factor, $playerinfo[cloak])));
      $ship_salvage_rate=rand(10,20);
      $ship_salvage=$ship_value*$ship_salvage_rate/100;
      playerlog($targetinfo[player_id], LOG_ATTACK_WIN, "Furangee $playerinfo[character_name]|$armor_lost|$fighters_lost");
      playerlog($targetinfo[player_id], LOG_RAW, "You destroyed the Furangee ship and salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.");
      $db->Execute ("UPDATE $dbtables[ships] SET ore=ore+$salv_ore, organics=organics+$salv_organics, goods=goods+$salv_goods WHERE ship_id=$targetinfo[ship_id]");
      $db->Execute ("UPDATE $dbtables[players] SET credits=credits+$ship_salvage, rating=rating-$rating_change WHERE player_id=$targetinfo[player_id]");
      $armor_lost = $targetinfo[armour_pts] - $targetarmor;
      $fighters_lost = $targetinfo[fighters] - $targetfighters;
      $energy=$targetinfo[energy];
      $db->Execute ("UPDATE $dbtables[ships] SET energy=$energy,fighters=fighters-$fighters_lost, torps=torps-$targettorpnum,armour_pts=armour_pts-$armor_lost WHERE ship_id=$targetinfo[ship_id]");
    }
  }

  // *********************************
  // *** END OF FURANGEETOSHIP SUB ***
  // *********************************
  $db->Execute("UNLOCK TABLES");
}

function furangeetosecdef()
{
  // **********************************
  // *** FURANGEE TO SECTOR DEFENCE ***
  // **********************************

  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;
  global $targetlink;

  global $l_sf_sendlog;
  global $l_sf_sendlog2;
  global $l_chm_hehitminesinsector;
  global $l_chm_hewasdestroyedbyyourmines;

  global $furangeeisdead;
  global $db, $dbtables;

  // *********************************
  // *** CHECK FOR SECTOR DEFENCE ****
  // *********************************
  if ($targetlink>0)
  {
    $resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
    $i = 0;
    $total_sector_fighters = 0;
    if($resultf > 0)
    {
      while(!$resultf->EOF)
      {
        $defences[$i] = $resultf->fields;
        $total_sector_fighters += $defences[$i]['quantity'];
        $i++;
        $resultf->MoveNext();
      }
    }
    $resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='M'");
    $i = 0;
    $total_sector_mines = 0;
    if($resultm > 0)
    {
      while(!$resultm->EOF)
      {
        $defences[$i] = $resultm->fields;
        $total_sector_mines += $defences[$i]['quantity'];
        $i++;
        $resultm->MoveNext();
      }
    }
    if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
    //*** DEST LINK HAS DEFENCES SO LETS ATTACK THEM***
    {
      playerlog($playerinfo[player_id], LOG_RAW, "ATTACKING SECTOR DEFENCES $total_sector_fighters fighters and $total_sector_mines mines."); 
      // ************************************
      // *** LETS GATHER COMBAT VARIABLES ***
      // ************************************
      $targetfighters = $total_sector_fighters;
      $playerbeams = NUM_BEAMS($playerinfo[beams]);
      if($playerbeams>$playerinfo[energy]) {
        $playerbeams=$playerinfo[energy];
      }
      $playerinfo[energy]=$playerinfo[energy]-$playerbeams;
      $playershields = NUM_SHIELDS($playerinfo[shields]);
      if($playershields>$playerinfo[energy]) {
        $playershields=$playerinfo[energy];
      }
      $playertorpnum = round(mypw($level_factor,$playerinfo[torp_launchers]))*2;
      if($playertorpnum > $playerinfo[torps]) {
        $playertorpnum = $playerinfo[torps];
      }
      $playertorpdmg = $torp_dmg_rate*$playertorpnum;
      $playerarmour = $playerinfo[armour_pts];
      $playerfighters = $playerinfo[fighters];
      $totalmines = $total_sector_mines;
      if ($totalmines>1) {
        $roll = rand(1,$totalmines);
      } else {
        $roll = 1;
      }
      $totalmines = $totalmines - $roll;
      $playerminedeflect = $playerinfo[fighters]; // *** Furangee keep as many deflectors as fighters ***

      // *****************************
      // *** LETS DO SOME COMBAT ! ***
      // *****************************
      // *** BEAMS VS FIGHTERS ***
      if($targetfighters > 0 && $playerbeams > 0) {
        if($playerbeams > round($targetfighters / 2))
        {
          $temp = round($targetfighters/2);
          $targetfighters = $temp;
          $playerbeams = $playerbeams-$temp;
        } else {
          $targetfighters = $targetfighters-$playerbeams;
          $playerbeams = 0;
        }   
      }
      // *** TORPS VS FIGHTERS ***
      if($targetfighters > 0 && $playertorpdmg > 0) {
        if($playertorpdmg > round($targetfighters / 2)) {
          $temp=round($targetfighters/2);
          $targetfighters=$temp;
          $playertorpdmg=$playertorpdmg-$temp;
        } else {
          $targetfighters=$targetfighters-$playertorpdmg;
          $playertorpdmg=0;
        }
      }
      // *** FIGHTERS VS FIGHTERS ***
      if($playerfighters > 0 && $targetfighters > 0) {
       if($playerfighters > $targetfighters) {
         echo $l_sf_destfightall;
         $temptargfighters=0;
        } else {
          $temptargfighters=$targetfighters-$playerfighters;
        }
        if($targetfighters > $playerfighters) {
          $tempplayfighters=0;
        } else {
          $tempplayfighters=$playerfighters-$targetfighters;
        }     
        $playerfighters=$tempplayfighters;
        $targetfighters=$temptargfighters;
      }
      // *** OH NO THERE ARE STILL FIGHTERS **
      // *** ARMOUR VS FIGHTERS ***
      if($targetfighters > 0) {
        if($targetfighters > $playerarmour) {
          $playerarmour=0;
        } else {
          $playerarmour=$playerarmour-$targetfighters;
        } 
      }
      // *** GET RID OF THE SECTOR FIGHTERS THAT DIED ***
      $fighterslost = $total_sector_fighters - $targetfighters;
      destroy_fighters($targetlink,$fighterslost);

      // *** LETS LET DEFENCE OWNER KNOW WHAT HAPPENED *** 
      $l_sf_sendlog = str_replace("[player]", "Furangee $playerinfo[character_name]", $l_sf_sendlog);
      $l_sf_sendlog = str_replace("[lost]", $fighterslost, $l_sf_sendlog);
      $l_sf_sendlog = str_replace("[sector]", $targetlink, $l_sf_sendlog);
      message_defence_owner($targetlink,$l_sf_sendlog);

      // *** UPDATE FURANGEE AFTER COMBAT ***
      $armour_lost=$playerinfo[armour_pts]-$playerarmour;
      $fighters_lost=$playerinfo[fighters]-$playerfighters;
      $energy=$playerinfo[energy];
      $update1 = $db->Execute ("UPDATE $dbtables[ships] SET energy=$energy,fighters=fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$playertorpnum WHERE ship_id=$playerinfo[ship_id]");

      // *** CHECK TO SEE IF FURANGEE IS DEAD ***
      if($playerarmour < 1) {
        $l_sf_sendlog2 = str_replace("[player]", "Furangee " . $playerinfo[character_name], $l_sf_sendlog2);
        $l_sf_sendlog2 = str_replace("[sector]", $targetlink, $l_sf_sendlog2);
        message_defence_owner($targetlink,$l_sf_sendlog2);
        cancel_bounty($playerinfo[player_id]);
        db_kill_player($playerinfo['player_id']);
        $furangeeisdead = 1;
        return;
      }

      // *** OK FURANGEE MUST STILL BE ALIVE ***

      // *** NOW WE HIT THE MINES ***

      // *** LETS LOG THE FACT THAT WE HIT THE MINES ***
      $l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", "Furangee " . $playerinfo[character_name], $l_chm_hehitminesinsector);
      $l_chm_hehitminesinsector = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector);
      $l_chm_hehitminesinsector = str_replace("[chm_sector]", $targetlink, $l_chm_hehitminesinsector);
      message_defence_owner($targetlink,"$l_chm_hehitminesinsector");

      // *** DEFLECTORS VS MINES ***
      if($playerminedeflect >= $roll) {
        // Took no mine damage due to virtual mine deflectors
      } else {
        $mines_left = $roll - $playerminedeflect;

        // *** SHIELDS VS MINES ***
        if($playershields >= $mines_left) {
          $update2 = $db->Execute("UPDATE $dbtables[ships] set energy=energy-$mines_left WHERE ship_id=$playerinfo[ship_id]");
        } else {
          $mines_left = $mines_left - $playershields;

          // *** ARMOUR VS MINES ***
          if($playerarmour >= $mines_left)
          {
            $update2 = $db->Execute("UPDATE $dbtables[ships] SET armour_pts=armour_pts-$mines_left,energy=0 WHERE ship_id=$playerinfo[ship_id]");
          } else {
            // *** OH NO WE DIED ***
            // *** LETS LOG THE FACT THAT WE DIED *** 
            $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]", "Furangee " . $playerinfo[character_name], $l_chm_hewasdestroyedbyyourmines);
            $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", $targetlink, $l_chm_hewasdestroyedbyyourmines);
            message_defence_owner($targetlink,"$l_chm_hewasdestroyedbyyourmines");
            // *** LETS ACTUALLY KILL THE FURANGEE NOW ***
            cancel_bounty($playerinfo[player_id]);
            db_kill_player($playerinfo['player_id']);
            $furangeeisdead = 1;
            // *** LETS GET RID OF THE MINES NOW AND RETURN OUT OF THIS FUNCTION ***
            explode_mines($targetlink,$roll);
            return;
          }
        }
      }
      // *** LETS GET RID OF THE MINES NOW ***
      explode_mines($targetlink,$roll);
    } else {
      //*** FOR SOME REASON THIS WAS CALLED WITHOUT ANY SECTOR DEFENCES TO ATTACK ***
      return;
    }
  }
}


function furangeemove()
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;
  global $sector_max;
  global $targetlink;
  global $furangeeisdead;
  global $db, $dbtables;

  // *********************************
  // ***** OBTAIN A TARGET LINK ******
  // *********************************
  if ($targetlink==$playerinfo[sector_id]) $targetlink=0;
  $linkres = $db->Execute ("SELECT * FROM $dbtables[links] WHERE link_start='$playerinfo[sector_id]'");
  if ($linkres>0)
  {
    while (!$linkres->EOF)
    {
      $row = $linkres->fields;
      // *** OBTAIN SECTOR INFORMATION ***
      $sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$row[link_dest]'");
      $sectrow = $sectres->fields;
      $zoneres = $db->Execute("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
      $zonerow = $zoneres->fields;
      if ($zonerow[allow_attack]=="Y")                        //*** DEST LINK MUST ALLOW ATTACKING ***
      {
        $setlink=rand(0,2);                        //*** 33% CHANCE OF REPLACING DEST LINK WITH THIS ONE ***
        if ($setlink==0 || !$targetlink>0)          //*** UNLESS THERE IS NO DEST LINK, CHHOSE THIS ONE ***
        {
          $targetlink=$row[link_dest];
        }
      }
      $linkres->MoveNext();
    }
  }

  // *********************************
  // ***** IF NO ACCEPTABLE LINK *****
  // *********************************
  // **** TIME TO USE A WORM HOLE ****
  // *********************************
  if (!$targetlink>0)
  {
    // *** GENERATE A RANDOM SECTOR NUMBER ***
    $wormto=rand(1,($sector_max-15));
    $limitloop=1;                        // *** LIMIT THE NUMBER OF LOOPS ***
    while (!$targetlink>0 && $limitloop<15)
    {
      // *** OBTAIN SECTOR INFORMATION ***
      $sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$wormto'");
      $sectrow = $sectres->fields;
      $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
      $zonerow = $zoneres->fields;
      if ($zonerow[allow_attack]=="Y")
      {
        $targetlink=$wormto;
        playerlog($playerinfo[player_id], LOG_RAW, "Used a wormhole to warp to a zone where attacks are allowed."); 
      }
      $wormto++;
      $wormto++;
      $limitloop++;
    }
  } 

  // *********************************
  // *** CHECK FOR SECTOR DEFENCE ****
  // *********************************
  if ($targetlink>0)
  {
    $resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
    $i = 0;
    $total_sector_fighters = 0;
    if($resultf > 0)
    {
      while(!$resultf->EOF)
      {
        $defences[$i] = $resultf->fields;
        $total_sector_fighters += $defences[$i]['quantity'];
        $i++;
        $resultf->MoveNext();
      }
    }
    $resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='M'");
    $i = 0;
    $total_sector_mines = 0;
    if($resultm > 0)
    {
      while(!$resultm->EOF)
      {
        $defences[$i] = $resultm->fields;
        $total_sector_mines += $defences[$i]['quantity'];
        $i++;
        $resultm->MoveNext();
      }
    }
    if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
    // ********************************
    // **** DEST LINK HAS DEFENCES ****
    // ********************************
    {
      if ($playerinfo[aggression] == 2 || $playerinfo[aggression] == 1) {
        // *********************************
        // **** DO MOVE TO TARGET LINK *****
        // *********************************
        $stamp = date("Y-m-d H-i-s");
        $query="UPDATE $dbtables[players] SET last_login='$stamp', turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]";
        $move_result = $db->Execute ("$query");
        $db->Execute("UPDATE $dbtables[ships] SET sector_id=$targetlink WHERE ship_id=$playerinfo[ship_id]");
        if (!$move_result)
        {
          $error = $db->ErrorMsg();
          playerlog($playerinfo[player_id], LOG_RAW, "Move failed with error: $error "); 
          $targetlink = $playerinfo[sector_id];         //*** RESET TARGET LINK SO IT IS NOT ZERO ***
          return;
        }
        // ********************************
        // **** ATTACK SECTOR DEFENCES ****
        // ********************************
        furangeetosecdef();
        return;
      } else {
        playerlog($playerinfo[player_id], LOG_RAW, "Move failed, the sector is defended by $total_sector_fighters fighters and $total_sector_mines mines."); 
        return;
      }
    } else
    // ********************************
    // **** DEST LINK IS UNDEFENDED ***
    // ********************************
    {
      // *********************************
      // **** DO MOVE TO TARGET LINK *****
      // *********************************
      $stamp = date("Y-m-d H-i-s");
      $query="UPDATE $dbtables[players] SET last_login='$stamp', turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]";
      $move_result = $db->Execute ("$query");
      $db->Execute("UPDATE $dbtables[ships] SET sector_id=$targetlink WHERE ship_id=$playerinfo[ship_id]");
      if (!$move_result)
      {
        $error = $db->ErrorMsg();
        playerlog($playerinfo[player_id], LOG_RAW, "Move failed with error: $error "); 
        $targetlink = $playerinfo[sector_id];         //*** RESET TARGET LINK SO IT IS NOT ZERO ***
        return;
      } else
      {
        // playerlog($playerinfo[player_id], LOG_RAW, "Moved to $targetlink without incident."); 
      }
    }
  } else
  {                                            //*** WE HAVE NO TARGET LINK FOR SOME REASON ***
    playerlog($playerinfo[player_id], LOG_RAW, "Move failed due to lack of target link.");
    $targetlink = $playerinfo[sector_id];         //*** RESET TARGET LINK SO IT IS NOT ZERO ***
  }
}

function furangeeregen()
{
  // *******************************
  // *** SETUP GENERAL VARIABLES ***
  // *******************************
  global $playerinfo;
  global $furangeeisdead;
  global $db, $dbtables;

  // *******************************
  // *** LETS REGENERATE ENERGY ****
  // *******************************
  $maxenergy = NUM_ENERGY($playerinfo[power]);
  if ($playerinfo[energy] <= ($maxenergy - 50))  // *** STOP REGEN WHEN WITHIN 50 OF MAX ***
  {                                                   // *** REGEN HALF OF REMAINING ENERGY ***
    $playerinfo[energy] = $playerinfo[energy] + round(($maxenergy - $playerinfo[energy])/2);
    $gene = "regenerated Energy to $playerinfo[energy] units,";
  }

  // *******************************
  // *** LETS REGENERATE ARMOUR ****
  // *******************************
  $maxarmour = NUM_ARMOUR($playerinfo[armour]);
  if ($playerinfo[armour_pts] <= ($maxarmour - 50))  // *** STOP REGEN WHEN WITHIN 50 OF MAX ***
  {                                                  // *** REGEN HALF OF REMAINING ARMOUR ***
    $playerinfo[armour_pts] = $playerinfo[armour_pts] + round(($maxarmour - $playerinfo[armour_pts])/2);
    $gena = "regenerated Armour to $playerinfo[armour_pts] points,";
  }

  // *******************************
  // *** LETS BUY FIGHTERS/TORPS ***
  // *******************************

  // *******************************
  // *** FURANGEE PAY 6/FIGHTER ****
  // *******************************
  $available_fighters = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[fighters];
  if (($playerinfo[credits]>5) && ($available_fighters>0))
  {
    if (round($playerinfo[credits]/6)>$available_fighters)
    {
      $purchase = ($available_fighters*6);
      $playerinfo[credits] = $playerinfo[credits] - $purchase;
      $playerinfo[fighters] = $playerinfo[fighters] + $available_fighters;
      $genf = "purchased $available_fighters fighters for $purchase credits,";
    }
    if (round($playerinfo[credits]/6)<=$available_fighters)
    {
      $purchase = (round($playerinfo[credits]/6));
      $playerinfo[fighters] = $playerinfo[fighters] + $purchase;
      $genf = "purchased $purchase fighters for $playerinfo[credits] credits,";
      $playerinfo[credits] = 0;
    }
  } 

  // *******************************
  // *** FURANGEE PAY 3/TORPEDO ****
  // *******************************
  $available_torpedoes = NUM_TORPEDOES($playerinfo[torp_launchers]) - $playerinfo[torps];
  if (($playerinfo[credits]>2) && ($available_torpedoes>0))
  {
    if (round($playerinfo[credits]/3)>$available_torpedoes)
    {
      $purchase = ($available_torpedoes*3);
      $playerinfo[credits] = $playerinfo[credits] - $purchase;
      $playerinfo[torps] = $playerinfo[torps] + $available_torpedoes;
      $gent = "purchased $available_torpedoes torpedoes for $purchase credits,";
    }
    if (round($playerinfo[credits]/3)<=$available_torpedoes)
    {
      $purchase = (round($playerinfo[credits]/3));
      $playerinfo[torps] = $playerinfo[torps] + $purchase;
      $gent = "purchased $purchase torpedoes for $playerinfo[credits] credits,";
      $playerinfo[credits] = 0;
    }
  } 

  // *********************************
  // *** UPDATE FURANGEE RECORD ******
  // *********************************
  $db->Execute ("UPDATE $dbtables[ships] SET energy=$playerinfo[energy], armour_pts=$playerinfo[armour_pts], fighters=$playerinfo[fighters], torps=$playerinfo[torps] WHERE ship_id=$playerinfo[ship_id]");
  $db->Execute ("UPDATE $dbtables[players] SET credits=$playerinfo[credits] WHERE player_id=$playerinfo[player_id]");
  if (!$gene=='' || !$gena=='' || !$genf=='' || !$gent=='')
  {
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee $gene $gena $genf $gent and has been updated."); 
  }

}

function furangeetrade()
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;
  global $inventory_factor;
  global $ore_price;
  global $ore_delta;
  global $ore_limit;
  global $goods_price;
  global $goods_delta;
  global $goods_limit;
  global $organics_price;
  global $organics_delta;
  global $organics_limit;
  global $furangeeisdead;
  global $db, $dbtables;

  // *********************************
  // *** OBTAIN SECTOR INFORMATION ***
  // *********************************
  $sectres = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector_id]'");
  $sectorinfo = $sectres->fields;

  // *********************************
  // **** OBTAIN ZONE INFORMATION ****
  // *********************************
  $zoneres = $db->Execute ("SELECT zone_id,allow_attack,allow_trade FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
  $zonerow = $zoneres->fields;

  // Debug info
  //playerlog($playerinfo[player_id], LOG_RAW, "PORT $sectorinfo[port_type] ALLOW_TRADE $zonerow[allow_trade] PORE $sectorinfo[port_ore] PORG $sectorinfo[port_organics] PGOO $sectorinfo[port_goods] ORE $playerinfo[ore] ORG $playerinfo[organics] GOO $playerinfo[goods] CREDITS $playerinfo[credits] "); 

  // *********************************
  // ** MAKE SURE WE CAN TRADE HERE **
  // *********************************
  if ($zonerow[allow_trade]=="N") return;

  // *********************************
  // ** CHECK FOR A PORT WE CAN USE **
  // *********************************
  if($sectorinfo[port_type] == "none") return;
  // *** FURANGEE DO NOT TRADE AT SPECIAL PORTS ***
  if($sectorinfo[port_type] == "special") return;
  // *** FURANGEE DO NOT TRADE AT ENERGY PORTS SINCE THEY REGEN ENERGY ***
  if($sectorinfo[port_type] == "energy") return;

  // *********************************
  // ** CHECK FOR NEG CREDIT/CARGO ***
  // *********************************
  if($playerinfo[ore] <= 0) $playerinfo[ore]=$shipore=0;
  if($playerinfo[organics] <= 0) $playerinfo[organics]=$shiporganics=0;
  if($playerinfo[goods] <= 0) $playerinfo[goods]=$shipgoods=0;
  if($playerinfo[credits] <= 0) $playerinfo[credits]=$shipcredits=0;
  if($sectorinfo[port_ore] <= 0) return;
  if($sectorinfo[port_organics] <= 0) return;
  if($sectorinfo[port_goods] <= 0) return;

  // *********************************
  // ** CHECK FURANGEE CREDIT/CARGO **
  // *********************************
  if($playerinfo[ore]>0) $shipore=$playerinfo[ore];
  if($playerinfo[organics]>0) $shiporganics=$playerinfo[organics];
  if($playerinfo[goods]>0) $shipgoods=$playerinfo[goods];
  if($playerinfo[credits]>0) $shipcredits=$playerinfo[credits];
  // *** MAKE SURE WE HAVE CARGO OR CREDITS ***
  if(!$playerinfo[credits]>0 && !$playerinfo[ore]>0 && !$playerinfo[goods]>0 && !$playerinfo[organics]>0) return;

  // *********************************
  // ** MAKE SURE CARGOS COMPATABLE **
  // *********************************
  if($sectorinfo[port_type]=="ore" && $shipore>0) return;
  if($sectorinfo[port_type]=="organics" && $shiporganics>0) return;
  if($sectorinfo[port_type]=="goods" && $shipgoods>0) return;

  // *********************************
  // ***** LETS TRADE SOME CARGO *****
  // *********************************
  if($sectorinfo[port_type]=="ore")
  // *********************
  // ***** PORT ORE ******
  // *********************
  {
    // ************************
    // **** SET THE PRICES ****
    // ************************
    $ore_price1 = $ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $organics_price1 = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $goods_price1 = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    // ************************
    // ** SET CARGO BUY/SELL **
    // ************************
    $amount_organics = $playerinfo[organics];
    $amount_goods = $playerinfo[goods];
    // *** SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT *** 
    $amount_ore = NUM_HOLDS($playerinfo[hull]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL ***
    $amount_ore = min($amount_ore, $sectorinfo[port_ore]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY ***
    $amount_ore = min($amount_ore, floor(($playerinfo[credits] + $amount_organics * $organics_price1 + $amount_goods * $goods_price1) / $ore_price1));
    // ************************
    // **** BUY/SELL CARGO ****
    // ************************
    $total_cost = round(($amount_ore * $ore_price1) - ($amount_organics * $organics_price1 + $amount_goods * $goods_price1));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = $playerinfo[ore]+$amount_ore;
    $neworganics = max(0,$playerinfo[organics]-$amount_organics);
    $newgoods = max(0,$playerinfo[goods]-$amount_goods);
    if ($newore < 0 || $neworganics < 0 || $newgoods < 0) {
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee Trade Negative ERROR: Port $sectorinfo[port_type] ORE player $newore port $sectorinfo[port_ore] price $ore_price1 delta $ore_delta ORG player $neworganics port $sectorinfo[port_organics] price $organics_price1 delta $organics_delta GOOD player $newgoods port $sectorinfo[port_goods] price $goods_price1 delta $goods_delta"); 
    }
    $trade_result = $db->Execute("UPDATE $dbtables[players] SET rating=rating+1, credits=$newcredits WHERE player_id=$playerinfo[player_id]");
    $trade_result = $db->Execute("UPDATE $dbtables[ships] SET ore=$newore, organics=$neworganics, goods=$newgoods WHERE ship_id=$playerinfo[ship_id]");
    $trade_result2 = $db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore-$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods+$amount_goods WHERE sector_id=$sectorinfo[sector_id]");
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee Trade Results: Sold $amount_organics Organics Sold $amount_goods Goods Bought $amount_ore Ore Cost $total_cost"); 
  }
  if($sectorinfo[port_type]=="organics")
  // *********************
  // *** PORT ORGANICS ***
  // *********************
  {
    // ************************
    // **** SET THE PRICES ****
    // ************************
    $organics_price1 = $organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $ore_price1 = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $goods_price1 = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    // ************************
    // ** SET CARGO BUY/SELL **
    // ************************
    $amount_ore = $playerinfo[ore];
    $amount_goods = $playerinfo[goods];
    // *** SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT *** 
    $amount_organics = NUM_HOLDS($playerinfo[hull]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL ***
    $amount_organics = min($amount_organics, $sectorinfo[port_organics]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY ***
    $amount_organics = min($amount_organics, floor(($playerinfo[credits] + $amount_ore * $ore_price1 + $amount_goods * $goods_price1) / $organics_price1));
    // ************************
    // **** BUY/SELL CARGO ****
    // ************************
    $total_cost = round(($amount_organics * $organics_price1) - ($amount_ore * $ore_price1 + $amount_goods * $goods_price1));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = max(0,$playerinfo[ore]-$amount_ore);
    $neworganics = $playerinfo[organics]+$amount_organics;
    $newgoods = max(0,$playerinfo[goods]-$amount_goods);
    if ($newore < 0 || $neworganics < 0 || $newgoods < 0) {
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee Trade Negative ERROR: Port $sectorinfo[port_type] ORE player $newore port $sectorinfo[port_ore] price $ore_price1 delta $ore_delta ORG player $neworganics port $sectorinfo[port_organics] price $organics_price1 delta $organics_delta GOOD player $newgoods port $sectorinfo[port_goods] price $goods_price1 delta $goods_delta"); 
    }
    $trade_result = $db->Execute("UPDATE $dbtables[players] SET rating=rating+1, credits=$newcredits WHERE player_id=$playerinfo[player_id]");
    $trace_result = $db->Execute("UPDATE $dbtables[ships] SET ore=$newore, organics=$neworganics, goods=$newgoods WHERE ship_id=$playerinfo[ship_id]");
    $trade_result2 = $db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore+$amount_ore, port_organics=port_organics-$amount_organics, port_goods=port_goods+$amount_goods where sector_id=$sectorinfo[sector_id]");
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee Trade Results: Sold $amount_goods Goods Sold $amount_ore Ore Bought $amount_organics Organics Cost $total_cost"); 
  }
  if($sectorinfo[port_type]=="goods")
  // *********************
  // **** PORT GOODS *****
  // *********************
  {
    // ************************
    // **** SET THE PRICES ****
    // ************************
    $goods_price1 = $goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $ore_price1 = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $organics_price1 = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    // ************************
    // ** SET CARGO BUY/SELL **
    // ************************
    $amount_ore = $playerinfo[ore];
    $amount_organics = $playerinfo[organics];
    // *** SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT *** 
    $amount_goods = NUM_HOLDS($playerinfo[hull]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL ***
    $amount_goods = min($amount_goods, $sectorinfo[port_goods]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY ***
    $amount_goods = min($amount_goods, floor(($playerinfo[credits] + $amount_ore * $ore_price1 + $amount_organics * $organics_price1) / $goods_price1));
    // ************************
    // **** BUY/SELL CARGO ****
    // ************************
    $total_cost = round(($amount_goods * $goods_price1) - ($amount_organics * $organics_price1 + $amount_ore * $ore_price1));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = max(0,$playerinfo[ore]-$amount_ore);
    $neworganics = max(0,$playerinfo[organics]-$amount_organics);
    $newgoods = $playerinfo[goods]+$amount_goods;
    if ($newore < 0 || $neworganics < 0 || $newgoods < 0) {
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee Trade Negative ERROR: Port $sectorinfo[port_type] ORE player $newore port $sectorinfo[port_ore] price $ore_price1 delta $ore_delta ORG player $neworganics port $sectorinfo[port_organics] price $organics_price1 delta $organics_delta GOOD player $newgoods port $sectorinfo[port_goods] price $goods_price1 delta $goods_delta"); 
    }
    $trade_result = $db->Execute("UPDATE $dbtables[players] SET rating=rating+1, credits=$newcredits WHERE player_id=$playerinfo[player_id]");
    $trade_result = $db->Execute("UPDATE $dbtables[ships] SET ore=$newore, organics=$neworganics, goods=$newgoods WHERE ship_id=$playerinfo[ship_id]");
    $trade_result2 = $db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore+$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods-$amount_goods where sector_id=$sectorinfo[sector_id]");
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee Trade Results: Sold $amount_ore Ore Sold $amount_organics Organics Bought $amount_goods Goods Cost $total_cost"); 
  }

}

function furangeehunter()
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;
  global $targetlink;
  global $furangeeisdead;
  global $db, $dbtables;

  $rescount = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE destroyed='N' and email NOT LIKE '%@furangee' and $dbtables[players].player_id > 1");
  $rowcount = $rescount->fields;
  $topnum = min(10,$rowcount[num_players]);

  // *** IF WE HAVE KILLED ALL THE PLAYERS IN THE GAME THEN THERE IS LITTLE POINT IN PROCEEDING ***
  if ($topnum<1) return;

  $res = $db->Execute("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING (player_id) WHERE destroyed='N' and email NOT LIKE '%@furangee' AND $dbtables[players].player_id > 1 ORDER BY score DESC LIMIT $topnum");

  // *** LETS CHOOSE A TARGET FROM THE TOP PLAYER LIST ***
  $i=1;
  $targetnum=rand(1,$topnum);
  while (!$res->EOF)
  {
    if ($i==$targetnum)
    { 
    $targetinfo=$res->fields;
    }
    $i++;
    $res->MoveNext();
  }

  // *** Make sure we have a target ***
  if (!$targetinfo)
  {
    playerlog($playerinfo[player_id], LOG_RAW, "Hunt Failed: No Target ");
    return;
  }

  // *********************************
  // *** WORM HOLE TO TARGET SECTOR **
  // *********************************
  $sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$targetinfo[sector_id]'");
  $sectrow = $sectres->fields;
  $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
  $zonerow = $zoneres->fields;
  // *** ONLY WORM HOLM TO TARGET IF WE CAN ATTACK IN TARGET SECTOR ***
  if ($zonerow[allow_attack]=="Y")
  {
    $stamp = date("Y-m-d H-i-s");
    $query="UPDATE $dbtables[players] SET last_login='$stamp', turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]";
    $move_result = $db->Execute ("$query");
    $db->Execute("UPDATE $dbtables[ships] SET sector_id=$targetinfo[sector_id] WHERE ship_id=$playerinfo[ship_id]");
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee used a wormhole to warp to sector $targetinfo[sector_id] where he is hunting player $targetinfo[character_name]."); 
    if (!$move_result)
    {
      $error = $db->ErrorMsg();
      playerlog($playerinfo[player_id], LOG_RAW, "Move failed with error: $error "); 
      return;
    }
  // *********************************
  // *** CHECK FOR SECTOR DEFENCE ****
  // *********************************
    $resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id=$targetinfo[sector_id] and defence_type ='F' ORDER BY quantity DESC");
    $i = 0;
    $total_sector_fighters = 0;
    if($resultf > 0)
    {
      while(!$resultf->EOF)
      {
        $defences[$i] = $resultf->fields;
        $total_sector_fighters += $defences[$i]['quantity'];
        $i++;
        $resultf->MoveNext();
      }
    }
    $resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id=$targetinfo[sector_id] and defence_type ='M'");
    $i = 0;
    $total_sector_mines = 0;
    if($resultm > 0)
    {
      while(!$resultm->EOF)
      {
        $defences[$i] = $resultm->fields;
        $total_sector_mines += $defences[$i]['quantity'];
        $i++;
        $resultm->MoveNext();
      }
    }

    if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
    //*** DEST LINK HAS DEFENCES ***
    {
      // *** ATTACK SECTOR DEFENCES ***
      $targetlink = $targetinfo[sector_id];
      furangeetosecdef();
    }
    if ($furangeeisdead>0) {
      // *** SECTOR DEFENSES KILLED US ***
      return;
    }

    // *** TIME TO ATTACK THE TARGET ***
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee launching an attack on $targetinfo[character_name]."); 

    // *** SEE IF TARGET IS ON A PLANET ***
    if ($targetinfo[on_planet]=='Y') {
      // *** ON A PLANET ***
      furangeetoplanet($targetinfo[planet_id]);
    } else {
      // *** NOT ON A PLANET ***
      furangeetoship($targetinfo[ship_id]);
    }
  } else
  {
    playerlog($playerinfo[player_id], LOG_RAW, "Furangee hunt failed, target $targetinfo[character_name] was in a no attack zone (sector $targetinfo[sector_id]).");
  }
}

function furangeetoplanet($planet_id)
{
  // ***********************************
  // *** Furangee Planet Attack Code ***
  // ***********************************

  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;
  global $planetinfo;

  global $torp_dmg_rate;
  global $level_factor;
  global $rating_combat_factor;
  global $upgrade_cost;
  global $upgrade_factor;
  global $sector_max;
  global $furangeeisdead;
  global $db, $dbtables;

  // *** LOCKING TABLES ****
  $db->Execute("LOCK TABLES $dbtables[players] WRITE, $dbtables[ships] WRITE, $dbtables[universe] WRITE, $dbtables[planets] WRITE, $dbtables[news] WRITE, $dbtables[logs] WRITE");

  // ********************************
  // *** LOOKUP PLANET DETAILS   ****
  // ********************************
  $resultp = $db->Execute ("SELECT * FROM $dbtables[planets] WHERE planet_id='$planet_id'");
  $planetinfo=$resultp->fields;

  // ********************************
  // *** LOOKUP OWNER DETAILS    ****
  // ********************************
  $resulto = $db->Execute ("SELECT * FROM $dbtables[players] LEFT JOIN $dbtables[ships] USING(player_id) WHERE $dbtables[players].player_id='$planetinfo[owner]' AND ship_id=$dbtables[players].currentship");
  $ownerinfo=$resulto->fields;

  // **********************************
  // *** SETUP PLANETARY VARIABLES ****
  // **********************************
  $base_factor = ($planetinfo[base] == 'Y') ? $basedefense : 0;

  // *** PLANET BEAMS ***
  $targetbeams = NUM_BEAMS($ownerinfo[beams] + $base_factor);
  if ($targetbeams > $planetinfo[energy]) $targetbeams = $planetinfo[energy];
  $planetinfo[energy] -= $targetbeams;
    
  // *** PLANET SHIELDS ***
  $targetshields = NUM_SHIELDS($ownerinfo[shields] + $base_factor);
  if ($targetshields > $planetinfo[energy]) $targetshields = $planetinfo[energy];
  $planetinfo[energy] -= $targetshields;
    
  // *** PLANET TORPS ***
  $torp_launchers = round(mypw($level_factor, ($ownerinfo[torp_launchers])+ $base_factor)) * 10;
  $torps = $planetinfo[torps];
  $targettorps = $torp_launchers;
  if ($torp_launchers > $torps) $targettorps = $torps;
  $planetinfo[torps] -= $targettorps;
  $targettorpdmg = $torp_dmg_rate * $targettorps;

  // *** PLANET FIGHTERS ***
  $targetfighters = $planetinfo[fighters];

  // *********************************
  // *** SETUP ATTACKER VARIABLES ****
  // *********************************

  // *** ATTACKER BEAMS ***
  $attackerbeams = NUM_BEAMS($playerinfo[beams]);
  if ($attackerbeams > $playerinfo[energy]) $attackerbeams = $playerinfo[energy];
  $playerinfo[energy] -= $attackerbeams;

  // *** ATTACKER SHIELDS ***
  $attackershields = NUM_SHIELDS($playerinfo[shields]);
  if ($attackershields > $playerinfo[energy]) $attackershields = $playerinfo[energy];
  $playerinfo[energy] -= $attackershields;

  // *** ATTACKER TORPS ***
  $attackertorps = round(mypw($level_factor, $playerinfo[torp_launchers])) * 2;
  if ($attackertorps > $playerinfo[torps]) $attackertorps = $playerinfo[torps]; 
  $playerinfo[torps] -= $attackertorps;
  $attackertorpdamage = $torp_dmg_rate * $attackertorps;

  // *** ATTACKER FIGHTERS ***
  $attackerfighters = $playerinfo[fighters];

  // *** ATTACKER ARMOUR ***
  $attackerarmor = $playerinfo[armour_pts];

  // *********************************
  // **** BEGIN COMBAT PROCEDURES ****
  // *********************************
  if($attackerbeams > 0 && $targetfighters > 0)
  {                         //******** ATTACKER HAS BEAMS - TARGET HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($attackerbeams > $targetfighters)
    {                                  //****** ATTACKER BEAMS GT TARGET FIGHTERS ******
      $lost = $targetfighters;
      $targetfighters = 0;                                     //**** T LOOSES ALL FIGHTERS ****
      $attackerbeams = $attackerbeams-$lost;                   //**** A LOOSES BEAMS EQ TO T FIGHTERS ****
    } else
    {                                  //****** ATTACKER BEAMS LE TARGET FIGHTERS ******
      $targetfighters = $targetfighters-$attackerbeams;        //**** T LOOSES FIGHTERS EQ TO A BEAMS ****
      $attackerbeams = 0;                                      //**** A LOOSES ALL BEAMS ****
    }   
  }
  if($attackerfighters > 0 && $targetbeams > 0)
  {                         //******** TARGET HAS BEAMS - ATTACKER HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($targetbeams > round($attackerfighters / 2))
    {                                  //****** TARGET BEAMS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;               //**** A LOOSES HALF ALL FIGHTERS ****
      $targetbeams=$targetbeams-$lost;                         //**** T LOOSES BEAMS EQ TO HALF A FIGHTERS ****
    } else
    {                                  //****** TARGET BEAMS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targetbeams;        //**** A LOOSES FIGHTERS EQ TO T BEAMS **** 
      $targetbeams=0;                                          //**** T LOOSES ALL BEAMS ****
    }
  }
  if($attackerbeams > 0)
  {                         //******** ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($attackerbeams > $targetshields)
    {                                  //****** ATTACKER BEAMS GT TARGET SHIELDS ******
      $attackerbeams=$attackerbeams-$targetshields;            //**** A LOOSES BEAMS EQ TO T SHIELDS ****
      $targetshields=0;                                        //**** T LOOSES ALL SHIELDS ****
    } else
    {                                  //****** ATTACKER BEAMS LE TARGET SHIELDS ******
      $targetshields=$targetshields-$attackerbeams;            //**** T LOOSES SHIELDS EQ TO A BEAMS ****
      $attackerbeams=0;                                        //**** A LOOSES ALL BEAMS ****
    }
  }
  if($targetbeams > 0)
  {                         //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($targetbeams > $attackershields)
    {                                  //****** TARGET BEAMS GT ATTACKER SHIELDS ******
      $targetbeams=$targetbeams-$attackershields;              //**** T LOOSES BEAMS EQ TO A SHIELDS ****
      $attackershields=0;                                      //**** A LOOSES ALL SHIELDS ****
    } else
    {                                  //****** TARGET BEAMS LE ATTACKER SHIELDS ****** 
      $attackershields=$attackershields-$targetbeams;          //**** A LOOSES SHIELDS EQ TO T BEAMS ****
      $targetbeams=0;                                          //**** T LOOSES ALL BEAMS ****
    }
  }
  if($targetbeams > 0)
  {                        //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR ******** 
    if($targetbeams > $attackerarmor)
    {                                 //****** TARGET BEAMS GT ATTACKER ARMOR ******
      $targetbeams=$targetbeams-$attackerarmor;                //**** T LOOSES BEAMS EQ TO A ARMOR ****
      $attackerarmor=0;                                        //**** A LOOSES ALL ARMOR (A DESTROYED) ****
    } else
    {                                 //****** TARGET BEAMS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetbeams;              //**** A LOOSES ARMOR EQ TO T BEAMS ****
      $targetbeams=0;                                          //**** T LOOSES ALL BEAMS ****
    } 
  }
  if($targetfighters > 0 && $attackertorpdamage > 0)
  {                        //******** ATTACKER FIRES TORPS - TARGET HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($attackertorpdamage > $targetfighters)
    {                                 //****** ATTACKER FIRED TORPS GT TARGET FIGHTERS ******
      $lost=$targetfighters;
      $targetfighters=0;                                       //**** T LOOSES ALL FIGHTERS ****
      $attackertorpdamage=$attackertorpdamage-$lost;           //**** A LOOSES FIRED TORPS EQ TO T FIGHTERS ****
    } else
    {                                 //****** ATTACKER FIRED TORPS LE HALF TARGET FIGHTERS ******
      $targetfighters=$targetfighters-$attackertorpdamage;     //**** T LOOSES FIGHTERS EQ TO A TORPS FIRED ****
      $attackertorpdamage=0;                                   //**** A LOOSES ALL TORPS FIRED ****
    }
  }
  if($attackerfighters > 0 && $targettorpdmg > 0)
  {                        //******** TARGET FIRES TORPS - ATTACKER HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($targettorpdmg > round($attackerfighters / 2))
    {                                 //****** TARGET FIRED TORPS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;               //**** A LOOSES HALF ALL FIGHTERS ****
      $targettorpdmg=$targettorpdmg-$lost;                     //**** T LOOSES FIRED TORPS EQ TO HALF A FIGHTERS ****
    } else
    {                                 //****** TARGET FIRED TORPS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targettorpdmg;      //**** A LOOSES FIGHTERS EQ TO T TORPS FIRED ****
      $targettorpdmg=0;                                        //**** T LOOSES ALL TORPS FIRED ****
    }
  }
  if($targettorpdmg > 0)
  {                        //******** TARGET FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR ********
    if($targettorpdmg > $attackerarmor)
    {                                 //****** TARGET FIRED TORPS GT HALF ATTACKER ARMOR ******
      $targettorpdmg=$targettorpdmg-$attackerarmor;            //**** T LOOSES FIRED TORPS EQ TO A ARMOR ****
      $attackerarmor=0;                                        //**** A LOOSES ALL ARMOR (A DESTROYED) ****
    } else
    {                                 //****** TARGET FIRED TORPS LE HALF ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targettorpdmg;            //**** A LOOSES ARMOR EQ TO T TORPS FIRED ****
      $targettorpdmg=0;                                        //**** T LOOSES ALL TORPS FIRED ****
    } 
  }
  if($attackerfighters > 0 && $targetfighters > 0)
  {                        //******** ATTACKER HAS FIGHTERS - TARGET HAS FIGHTERS - FIGHTERS VS FIGHTERS ********
    if($attackerfighters > $targetfighters)
    {                                 //****** ATTACKER FIGHTERS GT TARGET FIGHTERS ******
      $temptargfighters=0;                                     //**** T WILL LOOSE ALL FIGHTERS ****
    } else
    {                                 //****** ATTACKER FIGHTERS LE TARGET FIGHTERS ******
      $temptargfighters=$targetfighters-$attackerfighters;     //**** T WILL LOOSE FIGHTERS EQ TO A FIGHTERS ****
    }
    if($targetfighters > $attackerfighters)
    {                                 //****** TARGET FIGHTERS GT ATTACKER FIGHTERS ******
      $tempplayfighters=0;                                     //**** A WILL LOOSE ALL FIGHTERS ****
    } else
    {                                 //****** TARGET FIGHTERS LE ATTACKER FIGHTERS ******
      $tempplayfighters=$attackerfighters-$targetfighters;     //**** A WILL LOOSE FIGHTERS EQ TO T FIGHTERS ****
    }     
    $attackerfighters=$tempplayfighters;
    $targetfighters=$temptargfighters;
  }
  if($targetfighters > 0)
  {                        //******** TARGET HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR ********
    if($targetfighters > $attackerarmor)
    {                                 //****** TARGET FIGHTERS GT ATTACKER ARMOR ******
      $attackerarmor=0;                                        //**** A LOOSES ALL ARMOR (A DESTROYED) ****
    } else
    {                                 //****** TARGET FIGHTERS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetfighters;           //**** A LOOSES ARMOR EQ TO T FIGHTERS ****
    }
  }

  // *********************************
  // **** FIX NEGATIVE VALUE VARS ****
  // *********************************
  if ($attackerfighters < 0) $attackerfighters = 0;
  if ($attackertorps    < 0) $attackertorps = 0;
  if ($attackershields  < 0) $attackershields = 0;
  if ($attackerbeams    < 0) $attackerbeams = 0;
  if ($attackerarmor    < 0) $attackerarmor = 0;
  if ($targetfighters   < 0) $targetfighters = 0;
  if ($targettorps      < 0) $targettorps = 0;
  if ($targetshields    < 0) $targetshields = 0;
  if ($targetbeams      < 0) $targetbeams = 0;

  // ******************************************
  // *** CHECK IF ATTACKER SHIP DESTROYED   ***
  // ******************************************
  if(!$attackerarmor>0)
  {
    playerlog($playerinfo[player_id], LOG_RAW, "Ship destroyed by planetary defenses on planet $planetinfo[name]");
    $furangeeisdead = 1;

    $free_ore = round($playerinfo[ore]/2);
    $free_organics = round($playerinfo[organics]/2);
    $free_goods = round($playerinfo[goods]/2);
    $ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $playerinfo[hull]))+round(mypw($upgrade_factor, $playerinfo[engines]))+round(mypw($upgrade_factor, $playerinfo[power]))+round(mypw($upgrade_factor, $playerinfo[computer]))+round(mypw($upgrade_factor, $playerinfo[sensors]))+round(mypw($upgrade_factor, $playerinfo[beams]))+round(mypw($upgrade_factor, $playerinfo[torp_launchers]))+round(mypw($upgrade_factor, $playerinfo[shields]))+round(mypw($upgrade_factor, $playerinfo[armor]))+round(mypw($upgrade_factor, $playerinfo[cloak])));
    $ship_salvage_rate=rand(10,20);
    $ship_salvage=$ship_value*$ship_salvage_rate/100;
    $fighters_lost = $planetinfo[fighters] - $targetfighters;

    db_kill_player($playerinfo['player_id']);

    // *** LOG ATTACK TO PLANET OWNER ***
    playerlog($planetinfo[owner], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector_id]|Furangee $playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$ship_salvage");

    // *** UPDATE PLANET ***
    $db->Execute("UPDATE $dbtables[planets] SET energy=$planetinfo[energy],fighters=fighters-$fighters_lost, torps=torps-$targettorps, ore=ore+$free_ore, goods=goods+$free_goods, organics=organics+$free_organics, credits=credits+$ship_salvage WHERE planet_id=$planetinfo[planet_id]");
  
  }
  // **********************************************
  // *** MUST HAVE MADE IT PAST PLANET DEFENSES ***
  // **********************************************
  else
  {
    $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
    $fighters_lost = $playerinfo[fighters] - $attackerfighters;
    $target_fighters_lost = $planetinfo[fighters] - $targetfighters;
    playerlog($playerinfo[player_id], LOG_RAW, "Made it past defenses on planet $planetinfo[name]");

    // *** UPDATE ATTACKER ***
    $db->Execute ("UPDATE $dbtables[ships] SET energy=$playerinfo[energy],fighters=fighters-$fighters_lost, torps=torps-$attackertorps, armour_pts=armour_pts-$armor_lost WHERE ship_id=$playerinfo[ship_id]");
    $playerinfo[fighters] = $attackerfighters;
    $playerinfo[torps] = $attackertorps;
    $playerinfo[armour_pts] = $attackerarmor;


    // *** UPDATE PLANET ***
    $db->Execute ("UPDATE $dbtables[planets] SET energy=$planetinfo[energy], fighters=$targetfighters, torps=torps-$targettorps WHERE planet_id=$planetinfo[planet_id]");
    $planetinfo[fighters] = $targetfighters;
    $planetinfo[torps] = $targettorps;

    // *** NOW WE MUST ATTACK ALL SHIPS ON THE PLANET ONE BY ONE ***
    $resultps = $db->Execute("SELECT ship_id,name FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
    $shipsonplanet = $resultps->RecordCount();
    if ($shipsonplanet > 0)
    {
      while (!$resultps->EOF && $furangeeisdead < 1)
      {
        $onplanet = $resultps->fields;
        furangeetoship($onplanet[ship_id]);
        $resultps->MoveNext();
      }
    }
    $resultps = $db->Execute("SELECT ship_id,name FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
    $shipsonplanet = $resultps->RecordCount();
    if ($shipsonplanet == 0 && $furangeeisdead < 1)
    {
      // *** MUST HAVE KILLED ALL SHIPS ON PLANET ***
      playerlog($playerinfo[player_id], LOG_RAW, "Defeated all ships on planet $planetinfo[name]");
      // *** LOG ATTACK TO PLANET OWNER ***
      playerlog($planetinfo[owner], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector_id]|$playerinfo[character_name]");

      // *** UPDATE PLANET ***
      $db->Execute("UPDATE $dbtables[planets] SET fighters=0, torps=0, base='N', owner=0, corp=0 WHERE planet_id=$planetinfo[planet_id]"); 
      calc_ownership($planetinfo[sector_id]);

    } else {
      // *** MUST HAVE DIED TRYING ***
      playerlog($playerinfo[player_id], LOG_RAW, "We were KILLED by ships defending planet $planetinfo[name]");
      // *** LOG ATTACK TO PLANET OWNER ***
      playerlog($planetinfo[owner], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector_id]|Furangee $playerinfo[character_name]|0|0|0|0|0");

      // *** NO SALVAGE FOR PLANET BECAUSE WENT TO SHIP WHO WON **
    }

  }


  // *** END OF FURANGEE PLANET ATTACK CODE ***
  $db->Execute("UNLOCK TABLES");

}


?>
