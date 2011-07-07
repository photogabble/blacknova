<?php

  if (preg_match("/sched_furangee.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  // *********************************
  // ***** FURANGEE TURN UPDATES *****
  // *********************************
  echo "<BR><B>FURANGEE TURNS</B><BR><BR>";

  // *********************************
  // ******* INCLUDE FUNCTIONS *******
  // *********************************
  include_once("furangee_funcs.php");
  include_once("languages/$lang");
  global $targetlink;
  global $furangeeisdead;

  // *********************************
  // **** MAKE FURANGEE SELECTION ****
  // *********************************
  $furcount = $furcount0 = $furcount0a = $furcount1 = $furcount1a = $furcount2 = $furcount2a = $furcount3 = $furcount3a = $furcount3h = 0;
  $res = $db->Execute("SELECT * FROM $dbtables[players] JOIN $dbtables[furangee] LEFT JOIN $dbtables[ships] ON $dbtables[players].currentship=$dbtables[ships].ship_id WHERE email=furangee_id and active='Y' AND destroyed='N' ORDER BY sector_id");
  echo $db->ErrorMsg() . "<BR>";
  while(!$res->EOF)
  {
    $furangeeisdead = 0;
    $playerinfo = $res->fields;
    // *********************************
    // ****** REGENERATE/BUY STATS *****
    // *********************************
    furangeeregen();
    // *********************************
    // ****** RUN THROUGH ORDERS *******
    // *********************************
    $furcount++;
    if (rand(1,5) > 1)                                 // ****** 20% CHANCE OF NOT MOVING AT ALL ******
    {
      // *********************************
      // ****** ORDERS = 0 SENTINEL ******
      // *********************************
      if ($playerinfo[orders] == 0)
      {
        $furcount0++;
        $targetlink = $playerinfo[sector_id];
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF, NOT ON A PLANET ******
        $reso0 = $db->Execute("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE sector_id=$targetlink and email!='$playerinfo[email]' AND planet_id=0 AND $dbtables[players].player_id > 1");
        if (!$reso0->EOF)
        {
          $rowo0 = $reso0->fields;
          if ($playerinfo[aggression] == 0)            // ****** O = 0 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Sit As A Target Himself
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 0 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Furangee's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[fighters] > $rowo0[fighters])
            {
              $furcount0a++;
              playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo0[character_name]");
              furangeetoship($rowo0[ship_id]);
              if ($furangeeisdead>0) {
                $res->MoveNext();
                continue;
              }
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 0 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount0a++;
            playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo0[character_name]");
            furangeetoship($rowo0[ship_id]);
            if ($furangeeisdead>0) {
              $res->MoveNext();
              continue;
            }
          }
        }
      }
      // *********************************
      // ******** ORDERS = 1 ROAM ********
      // *********************************
      elseif ($playerinfo[orders] == 1)
      {
        $furcount1++;
        // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
        $targetlink = $playerinfo[sector_id];
        furangeemove();
        if ($furangeeisdead>0) {
          $res->MoveNext();
          continue;
        }
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF ******
        $reso1 = $db->Execute("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE sector_id=$targetlink and email!='$playerinfo[email]' and $dbtables[players].player_id > 1");
        if (!$reso1->EOF)
        {
          $rowo1= $reso1->fields;
          if ($playerinfo[aggression] == 0)            // ****** O = 1 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Roam Around As A Target Himself
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 1 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Furangee's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[fighters] > $rowo1[fighters] && $rowo1[planet_id] == 0)
            {
              $furcount1a++;
              playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo1[character_name]");
              furangeetoship($rowo1[ship_id]);
              if ($furangeeisdead>0) {
                $res->MoveNext();
                continue;
              }
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 1 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount1a++;
            playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo1[character_name]");
            if (!$rowo1[planet_id] == 0) {              // *** IS ON PLANET ***
              furangeetoplanet($rowo1[planet_id]);
            } else {
              furangeetoship($rowo1[ship_id]);
            }
            if ($furangeeisdead>0) {
              $res->MoveNext();
              continue;
            }
          }
        }
      }
      // *********************************
      // *** ORDERS = 2 ROAM AND TRADE ***
      // *********************************
      elseif ($playerinfo[orders] == 2)
      {
        $furcount2++;
        // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
        $targetlink = $playerinfo[sector_id];
        furangeemove();
        if ($furangeeisdead>0) {
          $res->MoveNext();
          continue;
        }
        // ****** NOW TRADE BEFORE WE DO ANY AGGRESSION CHECKS ******
        furangeetrade();
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF ******
        $reso2 = $db->Execute("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE sector_id=$targetlink and email!='$playerinfo[email]' and $dbtables[players].player_id > 1");
        if (!$reso2->EOF)
        {
          $rowo2=$reso2->fields;
          if ($playerinfo[aggression] == 0)            // ****** O = 2 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Roam And Trade
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 2 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Furangee's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[fighters] > $rowo2[fighters] && $rowo2[planet_id] == 0)
            {
              $furcount2a++;
              playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo2[character_name]");
              furangeetoship($rowo2[ship_id]);
              if ($furangeeisdead>0) {
                $res->MoveNext();
                continue;
              }
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 2 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount2a++;
            playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo2[character_name]");
            if (!$rowo2[planet_id] == 0) {              // *** IS ON PLANET ***
              furangeetoplanet($rowo2[planet_id]);
            } else {
              furangeetoship($rowo2[ship_id]);
            }
            if ($furangeeisdead>0) {
              $res->MoveNext();
              continue;
            }
          }
        }
      }
      // *********************************
      // *** ORDERS = 3 ROAM AND HUNT  ***
      // *********************************
      elseif ($playerinfo[orders] == 3)
      {
        $furcount3++;
        // ****** LET SEE IF WE GO HUNTING THIS ROUND BEFORE WE DO ANYTHING ELSE ******
        $hunt=rand(0,3);                               // *** 25% CHANCE OF HUNTING ***
        // Uncomment below for Debugging
        //$hunt=0;
        if ($hunt==0)
        {
        $furcount3h++;
        furangeehunter();
        if ($furangeeisdead>0) {
          $res->MoveNext();
          continue;
        }
        } else
        {
          // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
          $targetlink = $playerinfo[sector_id];
          furangeemove();
          if ($furangeeisdead>0) {
            $res->MoveNext();
            continue;
          }
          // ****** FIND A TARGET ******
          // ****** IN MY SECTOR, NOT MYSELF ******
          $reso3 = $db->Execute("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE sector_id=$targetlink and email!='$playerinfo[email]' and $dbtables[players].player_id > 1");
          if (!$reso3->EOF)
          {
            $rowo3=$reso3->fields;
            if ($playerinfo[aggression] == 0)            // ****** O = 3 & AGRESSION = 0 PEACEFUL ******
            {
              // This Guy Does Nothing But Roam Around As A Target Himself
            }
            elseif ($playerinfo[aggression] == 1)        // ****** O = 3 & AGRESSION = 1 ATTACK SOMETIMES ******
            {
              // Furangee's only compare number of fighters when determining if they have an attack advantage
              if ($playerinfo[fighters] > $rowo3[fighters] && $rowo3[planet_id] == 0)
              {
                $furcount3a++;
                playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo3[character_name]");
                furangeetoship($rowo3[ship_id]);
                if ($furangeeisdead>0) {
                  $res->MoveNext();
                  continue;
                }
              }
            }
            elseif ($playerinfo[aggression] == 2)        // ****** O = 3 & AGRESSION = 2 ATTACK ALLWAYS ******
            {
              $furcount3a++;
              playerlog($playerinfo[player_id], LOG_FURANGEE_ATTACK, "$rowo3[character_name]");
              if (!$rowo3[planet_id] == 0) {              // *** IS ON PLANET ***
                furangeetoplanet($rowo3[planet_id]);
              } else {
                furangeetoship($rowo3[ship_id]);
              }
              if ($furangeeisdead>0) {
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
  $res->_close();
  $furnonmove = $furcount - ($furcount0 + $furcount1 + $furcount2 + $furcount3);
  echo "Counted $furcount Furangee players that are ACTIVE with working ships.<BR>";
  echo "$furnonmove Furangee players did not do anything this round. <BR>";
  echo "$furcount0 Furangee players had SENTINEL orders of which $furcount0a launched attacks. <BR>";
  echo "$furcount1 Furangee players had ROAM orders of which $furcount1a launched attacks. <BR>";
  echo "$furcount2 Furangee players had ROAM AND TRADE orders of which $furcount2a launched attacks. <BR>";
  echo "$furcount3 Furangee players had ROAM AND HUNT orders of which $furcount3a launched attacks and $furcount3h went hunting. <BR>";
  echo "FURANGEE TURNS COMPLETE. <BR>";
  echo "<BR>";
  // *********************************
  // ***** END OF FURANGEE TURNS *****
  // *********************************

  $db->Execute("UNLOCK TABLES"); //some unsafe returns in funcs

?>
