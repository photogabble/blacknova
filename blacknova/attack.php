<?
  
include("config.php");
updatecookie();
include("languages/$lang");

connectdb();

$title=$l_att_title;
include("header.php");

if(checklogin())
{
  die();
}
//-------------------------------------------------------------------------------------------------
 $db->Execute("LOCK TABLES $dbtables[players] WRITE, $dbtables[universe] WRITE, $dbtables[bounty] WRITE $dbtables[zones] READ, $dbtables[planets] WRITE, $dbtables[news] WRITE, $dbtables[logs] WRITE");
$result = $db->Execute ("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo=$result->fields;
$ship_id = stripnum($ship_id);
$player_id = stripnum($player_id);

$result2 = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id='$player_id'");
$targetinfo=$result2->fields;

$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $result->fields;

$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE ship_id=$ship_id");
$targetship = $result->fields;

$playerscore = gen_score($playerinfo[player_id]);
$targetscore = gen_score($targetinfo[player_id]);

$playerscore = $playerscore * $playerscore;
$targetscore = $targetscore * $targetscore;

bigtitle();

srand((double)microtime()*1000000);

/* check to ensure target is in the same sector as player */
if($targetship[sector_id] != $shipinfo[sector_id] || $targetship[on_planet] == "Y")
{
  echo "$l_att_notarg<BR><BR>";
}
elseif($playerinfo[turns] < 1)
{
  echo "$l_att_noturn<BR><BR>";
}
else
{
  /* determine percent chance of success in detecting target ship - based on player's sensors and opponent's cloak */
  $success = (10 - $targetship[cloak] + $shipinfo[sensors]) * 5;
  if($success < 5)
  {
    $success = 5;
  }
  if($success > 95)
  {
    $success = 95;
  }
  $flee = (10 - $targetship[engines] + $shipinfo[engines]) * 5;
  $roll = rand(1, 100);
  $roll2 = rand(1, 100);

  $res = $db->Execute("SELECT allow_attack,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE sector_id='$targetship[sector_id]' AND $dbtables[zones].zone_id=$dbtables[universe].zone_id");
  $zoneinfo = $res->fields;
  if($zoneinfo[allow_attack] == 'N')
  {
    echo "$l_att_noatt<BR><BR>";
  }
  elseif($flee < $roll2)
  {
    echo "$l_att_flee<BR><BR>";
    $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
    playerlog($targetinfo[player_id], LOG_ATTACK_OUTMAN, "$playerinfo[character_name]");
  }
  elseif($roll > $success)
  {
    /* if scan fails - inform both player and target. */
    echo "$l_planet_noscan<BR><BR>";
    $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
    playerlog($targetinfo[player_id], LOG_ATTACK_OUTSCAN, "$playerinfo[character_name]");
  }
  else
  {
    /* if scan succeeds, show results and inform target. */
    $shipavg = $targetship[hull] + $targetship[engines] + $targetship[computer] + $targetship[beams] + $targetship[torp_launchers] + $targetship[shields] + $targetship[armour];
    $shipavg /= 7;
    if($shipavg > $ewd_maxhullsize)
    {
       $chance = ($shipavg - $ewd_maxhullsize) * 10;
    }
    else
    {
       $chance = 0;
    }
    $random_value = rand(1,100);
    if($targetship[dev_emerwarp] > 0 && $random_value > $chance)
    {
      /* need to change warp destination to random sector in universe */
      $rating_change=round($targetinfo[rating]*.1);
      $dest_sector=rand(1,$sector_max);
      $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1,rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");
      $l_att_ewdlog=str_replace("[name]",$playerinfo[character_name],$l_att_ewdlog);
      $l_att_ewdlog=str_replace("[sector]",$shipinfo[sector_id],$l_att_ewdlog);
      playerlog($targetinfo[player_id], LOG_ATTACK_EWD, "$playerinfo[character_name]");
      $result_warp = $db->Execute ("UPDATE $dbtables[ships] SET sector_id=$dest_sector, dev_emerwarp=dev_emerwarp-1,cleared_defences=' ' WHERE ship_id=$ship_id");
      log_move($targetship[ship_id],$dest_sector);
      echo "$l_att_ewd<BR><BR>";
    }
    else
    {
      if($playerscore == 0) $playerscore=1;
      if(($targetscore / $playerscore < $bounty_ratio || $targetinfo[turns_used] < $bounty_minturns) &&
        !("furangee" == substr($targetinfo[email], -8) ) )
      {
         // Check to see if there is Federation bounty on the player. If there is, people can attack regardless.
         $btyamount = 0;
         $hasbounty = $db->Execute("SELECT SUM(amount) AS btytotal FROM $dbtables[bounty] WHERE bounty_on = $targetinfo[player_id] AND placed_by = 0");
         if($hasbounty)
         {
            $resx = $hasbounty->fields;
            $btyamount = $resx[btytotal];
         }
         if($btyamount <= 0) 
         {
            $bounty = ROUND($playerscore * $bounty_maxvalue);
            $insert = $db->Execute("INSERT INTO $dbtables[bounty] (bounty_on,placed_by,amount) values ($playerinfo[player_id], 0 ,$bounty)");      
            playerlog($playerinfo[player_id],LOG_BOUNTY_FEDBOUNTY,"$bounty");
            echo $l_by_fedbounty2 . "<BR><BR>";
         }
      }
      if($targetship[dev_emerwarp] > 0)
      {
        playerlog($targetinfo[player_id], LOG_ATTACK_EWDFAIL, $playerinfo[character_name]);
      }
      
      $targetbeams = NUM_BEAMS($targetship[beams]);
      if($targetbeams>$targetship[energy])
      {
        $targetbeams=$targetship[energy];
      }
      $targetship[energy]=$targetship[energy]-$targetbeams;
      
      $playerbeams = NUM_BEAMS($shipinfo[beams]);
      if($playerbeams>$shipinfo[energy])
      {
        $playerbeams=$shipinfo[energy];
      }
      $shipinfo[energy]=$shipinfo[energy]-$playerbeams;
      
      $playershields = NUM_SHIELDS($shipinfo[shields]);
      if($playershields>$shipinfo[energy])
      {
        $playershields=$shipinfo[energy];
      }
      $shipinfo[energy]=$shipinfo[energy]-$playershields;
      
      $targetshields = NUM_SHIELDS($targetship[shields]);
      if($targetshields>$targetship[energy])
      {
        $targetshields=$targetship[energy];
      }
      $targetship[energy]=$targetship[energy]-$targetshields;

      $playertorpnum = round(mypw($level_factor,$shipinfo[torp_launchers]))*10;
      if($playertorpnum > $shipinfo[torps])
      {
        $playertorpnum = $shipinfo[torps];
      }
      
      $targettorpnum = round(mypw($level_factor,$targetship[torp_launchers]))*10;
      if($targettorpnum > $targetship[torps])
      {
        $targettorpnum = $targetship[torps];
      }

      $playertorpdmg = $torp_dmg_rate*$playertorpnum;
      $targettorpdmg = $torp_dmg_rate*$targettorpnum;
      $playerarmour = $shipinfo[armour_pts];
      $targetarmour = $targetship[armour_pts];
      $playerfighters = $shipinfo[ship_fighters];
      $targetfighters = $targetship[ship_fighters];
      $targetdestroyed = 0;
      $playerdestroyed = 0;

      echo "$l_att_att $targetinfo[character_name] $l_abord $targetship[name]:<BR><BR>";
      echo "$l_att_beams<BR>";
      if($targetfighters > 0 && $playerbeams > 0)
      {
        if($playerbeams > round($targetfighters / 2))
        {
          $temp = round($targetfighters/2);
          $lost = $targetfighters-$temp;
          echo "$targetinfo[character_name] $l_att_lost $lost $l_fighters<BR>";
          $targetfighters = $temp;
          $playerbeams = $playerbeams-$lost;
        }
        else
        {
          $targetfighters = $targetfighters-$playerbeams;
          echo "$targetinfo[character_name] $l_att_lost $playerbeams $l_fighters<BR>";
          $playerbeams = 0;
        }
      }

      if($playerfighters > 0 && $targetbeams > 0)
      {
        if($targetbeams > round($playerfighters / 2))
        {
          $temp=round($playerfighters/2);
          $lost=$playerfighters-$temp;
          echo "$l_att_ylost $lost $l_fighters<BR>";
          $playerfighters=$temp;
          $targetbeams=$targetbeams-$lost;
        }
        else
        {
          $playerfighters=$playerfighters-$targetbeams;
          echo "$l_att_ylost $targetbeams $l_fighters<BR>";
          $targetbeams=0;
        }
      }

      if($playerbeams > 0)
      {
        if($playerbeams > $targetshields)
        {
          $playerbeams=$playerbeams-$targetshields;
          $targetshields=0;
          echo "$targetinfo[character_name]". $l_att_sdown ."<BR>";
        }
        else
        {
          echo "$targetinfo[character_name]" . $l_att_shits ." $playerbeams $l_att_dmg.<BR>";
          $targetshields=$targetshields-$playerbeams;
          $playerbeams=0;
        }
      }

      if($targetbeams > 0)
      {
        if($targetbeams > $playershields)
        {
          $targetbeams=$targetbeams-$playershields;
          $playershields=0;
          echo "$l_att_ydown<BR>";
        }
        else
        {
          echo "$l_att_yhits $targetbeams $l_att_dmg.<BR>";
          $playershields=$playershields-$targetbeams;
          $targetbeams=0;
        }
      }

      if($playerbeams > 0)
      {
        if($playerbeams > $targetarmour)
        {
          $targetarmour=0;
          echo "$targetinfo[character_name] " .$l_att_sarm ."<BR>";
        }
        else
        {
          $targetarmour=$targetarmour-$playerbeams;
          echo "$targetinfo[character_name]". $l_att_ashit ." $playerbeams $l_att_dmg.<BR>";
        }
      }

      if($targetbeams > 0)
      {
        if($targetbeams > $playerarmour)
        {
          $playerarmour=0;
          echo "$l_att_yarm<BR>";
        }
        else
        {
          $playerarmour=$playerarmour-$targetbeams;
          echo "$l_att_ayhit $targetbeams $l_att_dmg.<BR>";
        }
      }

      echo "<BR>$l_att_torps<BR>";
      if($targetfighters > 0 && $playertorpdmg > 0)
      {
        if($playertorpdmg > round($targetfighters / 2))
        {
          $temp=round($targetfighters/2);
          $lost=$targetfighters-$temp;
          echo "$targetinfo[character_name] $l_att_lost $lost $l_fighters<BR>";
          $targetfighters=$temp;
          $playertorpdmg=$playertorpdmg-$lost;
        }
        else
        {
          $targetfighters=$targetfighters-$playertorpdmg;
          echo "$targetinfo[character_name] $l_att_lost $playertorpdmg $l_fighters<BR>";
          $playertorpdmg=0;
        }
      }

      if($playerfighters > 0 && $targettorpdmg > 0)
      {
        if($targettorpdmg > round($playerfighters / 2))
        {
          $temp=round($playerfighters/2);
          $lost=$playerfighters-$temp;
          echo "$l_att_ylost $lost $l_fighters<BR>";
          echo "$temp - $playerfighters - $targettorpdmg";
          $playerfighters=$temp;
          $targettorpdmg=$targettorpdmg-$lost;
        }
        else
        {
          $playerfighters=$playerfighters-$targettorpdmg;
          echo "$l_att_ylost $targettorpdmg $l_fighters<BR>";
          $targettorpdmg=0;
        }
      }
      if($playertorpdmg > 0)
      {
        if($playertorpdmg > $targetarmour)
        {
          $targetarmour=0;
          echo "$targetinfo[character_name]" . $l_att_sarm ."<BR>";
        }
        else
        {
          $targetarmour=$targetarmour-$playertorpdmg;
          echo "$targetinfo[character_name]" . $l_att_ashit . " $playertorpdmg $l_att_dmg.<BR>";
        }
      }

      if($targettorpdmg > 0)
      {
        if($targettorpdmg > $playerarmour)
        {
          $playerarmour=0;
          echo "$l_att_yarm<BR>";
        }
        else
        {
          $playerarmour=$playerarmour-$targettorpdmg;
          echo "$l_att_ayhit $targettorpdmg $l_att_dmg.<BR>";
        }
      }

      echo "<BR>$l_att_fighters<BR>";
      if($playerfighters > 0 && $targetfighters > 0)
      {
        if($playerfighters > $targetfighters)
        {
          echo "$targetinfo[character_name] $l_att_lostf<BR>";
          $temptargfighters=0;
        }
        else
        {
          echo "$targetinfo[character_name] $l_att_lost $playerfighters $l_fighters.<BR>";
          $temptargfighters=$targetfighters-$playerfighters;
        }
        if($targetfighters > $playerfighters)
        {
          echo "$l_att_ylostf<BR>";
          $tempplayfighters=0;
        }
        else
        {
          echo "$l_att_ylost $targetfighters $l_fighters.<BR>";
          $tempplayfighters=$playerfighters-$targetfighters;
        }
        $playerfighters=$tempplayfighters;
        $targetfighters=$temptargfighters;
      }

      if($playerfighters > 0)
      {
        if($playerfighters > $targetarmour)
        {
          $targetarmour=0;
          echo "$targetinfo[character_name]". $l_att_sarm . "<BR>";
        }
        else
        {
          $targetarmour=$targetarmour-$playerfighters;
          echo "$targetinfo[character_name]" . $l_att_ashit ." $playerfighters $l_att_dmg.<BR>";
        }
      }

      if($targetfighters > 0)
      {
        if($targetfighters > $playerarmour)
        {
          $playerarmour=0;
          echo "$l_att_yarm<BR>";
        }
        else
        {
          $playerarmour=$playerarmour-$targetfighters;
          echo "$l_att_ayhit $targetfighters $l_att_dmg.<BR>";
        }
      }

      if($targetarmour < 1)
      {
        echo "<BR>$targetinfo[character_name]". $l_att_sdest ."<BR>";
        if($targetship[dev_escapepod] == "Y")
        {
          $rating=round($targetinfo[rating]/2);
          echo "$l_att_espod<BR><BR>";
          $db->Execute("UPDATE $dbtables[ships] SET class=1, hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector_id=0,organics=0,ore=0,goods=0,energy=$start_energy,colonists=0,fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='Y',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',cleared_defences=' ',dev_lssd='N' WHERE ship_id=$ship_id");
          $db->Execute("UPDATE $dbtables[players] SET rating='$rating' WHERE player_id=$targetinfo[player_id]");
          playerlog($targetinfo[player_id], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
          collect_bounty($playerinfo[player_id],$targetinfo[player_id]);
        }
        else
        {
          playerlog($targetinfo[player_id], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
          db_kill_player($targetinfo['player_id']);
          collect_bounty($playerinfo[player_id],$targetinfo[player_id]);
        }

        if($playerarmour > 0)
        {
          $rating_change=round($targetinfo[rating]*$rating_combat_factor);
          $free_ore = round($targetship[ore]/2);
          $free_organics = round($targetship[organics]/2);
          $free_goods = round($targetship[goods]/2);
          $free_holds = NUM_HOLDS($shipinfo[hull]) - $shipinfo[ore] - $shipinfo[organics] - $shipinfo[goods] - $shipinfo[colonists];
          if($free_holds > $free_goods)
          {
            $salv_goods=$free_goods;
            $free_holds=$free_holds-$free_goods;
          }
          elseif($free_holds > 0)
          {
            $salv_goods=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_goods=0;
          }
          if($free_holds > $free_ore)
          {
            $salv_ore=$free_ore;
            $free_holds=$free_holds-$free_ore;
          }
          elseif($free_holds > 0)
          {
            $salv_ore=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_ore=0;
          }
          if($free_holds > $free_organics)
          {
            $salv_organics=$free_organics;
            $free_holds=$free_holds-$free_organics;
          }
          elseif($free_holds > 0)
          {
            $salv_organics=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_organics=0;
          }
          
          $ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $targetship[hull]))+round(mypw($upgrade_factor, $targetship[engines]))+round(mypw($upgrade_factor, $targetship[power]))+round(mypw($upgrade_factor, $targetship[computer]))+round(mypw($upgrade_factor, $targetship[sensors]))+round(mypw($upgrade_factor, $targetship[beams]))+round(mypw($upgrade_factor, $targetship[torp_launchers]))+round(mypw($upgrade_factor, $targetship[shields]))+round(mypw($upgrade_factor, $targetship[armour]))+round(mypw($upgrade_factor, $targetinfo[cloak])));
          $ship_salvage_rate=rand(10,20);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;

          $l_att_ysalv=str_replace("[salv_ore]",$salv_ore,$l_att_ysalv);
          $l_att_ysalv=str_replace("[salv_organics]",$salv_organics,$l_att_ysalv);
          $l_att_ysalv=str_replace("[salv_goods]",$salv_goods,$l_att_ysalv);
          $l_att_ysalv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_ysalv);
          $l_att_ysalv=str_replace("[ship_salvage]",$ship_salvage,$l_att_ysalv);
          $l_att_ysalv=str_replace("[rating_change]",NUMBER(abs($rating_change)),$l_att_ysalv);

          $armour_lost=$playerinfo[armour_pts]-$playerarmour;
          $fighters_lost=$playerinfo[ship_fighters]-$playerfighters;
          $energy=$shipinfo[energy];

          echo $l_att_ysalv;
          $update3 = $db->Execute ("UPDATE $dbtables[ships] SET ore=ore+$salv_ore, organics=organics+$salv_organics, goods=goods+$salv_goods,energy=$energy, fighters=fighters-$fighters_lost,armour_pts=armour_pts-$armour_lost,torps=torps-$playertorpnum WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
          $update3 = $db->Execute ("UPDATE $dbtables[players] SET credits=credits+$ship_salvage, turns=turns-1,turns_used=turns_used+1,rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");

          echo "$l_att_ylost $armour_lost $l_armourpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<BR><BR>";
        }
      }
      else
      {
       $l_att_stilship=str_replace("[name]",$targetinfo[character_name],$l_att_stilship);
        echo "$l_att_stilship<BR>";

        $rating_change=round($targetinfo[rating]*.1);
        $armour_lost=$targetship[armour_pts]-$targetarmour;
        $fighters_lost=$targetship[fighters]-$targetfighters;
        $energy=$targetship[energy];

        playerlog($targetinfo[player_id], LOG_ATTACKED_WIN, "$playerinfo[character_name]|$armour_lost|$fighters_lost");
        $update4 = $db->Execute ("UPDATE $dbtables[ships] SET energy=$energy,fighters=fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$targettorpnum WHERE ship_id=$ship_id");
        
        $armour_lost=$shipinfo[armour_pts]-$playerarmour;
        $fighters_lost=$shipinfo[fighters]-$playerfighters;
        $energy=$shipinfo[ship_energy];

        $update4b = $db->Execute ("UPDATE $dbtables[ships] SET energy=$energy,fighters=fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$playertorpnum WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
        $update4b = $db->Execute ("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1, rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");

        echo "$l_att_ylost $armour_lost $l_armourpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<BR><BR>";
      }
      if($playerarmour < 1)
      {
        echo "$l_att_yshiplost<BR><BR>";
        if($shipinfo[dev_escapepod] == "Y")
        {
          $rating=round($playerinfo[rating]/2);
          echo "$l_att_loosepod<BR><BR>";
          $db->Execute("UPDATE $dbtables[ships] SET class=1, hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector_id=0,organics=0,ore=0,goods=0,energy=$start_energy,colonists=0,fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',dev_lssd='N' WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
          $db->Execute("UPDATE $dbtables[players] SET rating='$rating' WHERE player_id=$playerinfo[player_id]");
          collect_bounty($targetinfo[player_id],$playerinfo[player_id]);
        }
        else
        {
          db_kill_player($playerinfo['player_id']);
          collect_bounty($targetinfo[player_id],$playerinfo[player_id]);
        }
        if($targetarmour > 0)
        {
          $free_ore = round($shipinfo[ore]/2);
          $free_organics = round($shipinfo[organics]/2);
          $free_goods = round($shipinfo[goods]/2);
          $free_holds = NUM_HOLDS($targetship[hull]) - $targetship[ore] - $targetship[organics] - $targetship[goods] - $targetship[colonists];
          if($free_holds > $free_goods)
          {
            $salv_goods=$free_goods;
            $free_holds=$free_holds-$free_goods;
          }
          elseif($free_holds > 0)
          {
            $salv_goods=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_goods=0;
          }
          if($free_holds > $free_ore)
          {
            $salv_ore=$free_ore;
            $free_holds=$free_holds-$free_ore;
          }
          elseif($free_holds > 0)
          {
            $salv_ore=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_ore=0;
          }
          if($free_holds > $free_organics)
          {
            $salv_organics=$free_organics;
            $free_holds=$free_holds-$free_organics;
          }
          elseif($free_holds > 0)
          {
            $salv_organics=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_organics=0;
          }

          $ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $shipinfo[hull]))+round(mypw($upgrade_factor, $shipinfo[engines]))+round(mypw($upgrade_factor, $shipinfo[power]))+round(mypw($upgrade_factor, $shipinfo[computer]))+round(mypw($upgrade_factor, $shipinfo[sensors]))+round(mypw($upgrade_factor, $shipinfo[beams]))+round(mypw($upgrade_factor, $shipinfo[torp_launchers]))+round(mypw($upgrade_factor, $shipinfo[shields]))+round(mypw($upgrade_factor, $shipinfo[armour]))+round(mypw($upgrade_factor, $shipinfo[cloak])));
          $ship_salvage_rate=rand(10,20);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;

          $l_att_salv=str_replace("[salv_ore]",$salv_ore,$l_att_salv);
          $l_att_salv=str_replace("[salv_organics]",$salv_organics,$l_att_salv);
          $l_att_salv=str_replace("[salv_goods]",$salv_goods,$l_att_salv);
          $l_att_salv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_salv);
          $l_att_salv=str_replace("[ship_salvage]",$ship_salvage,$l_att_salv);
          $l_att_salv=str_replace("[name]",$targetinfo[character_name],$l_att_salv);

          echo "$l_att_salv<BR>";

          $armour_lost=$targetship[armour_pts]-$targetarmour;
          $fighters_lost=$targetship[fighters]-$targetfighters;
          $energy=$targetinfo[energy];
                    
          $update6 = $db->Execute ("UPDATE $dbtables[players] SET credits=credits+$ship_salvage WHERE player_id=$targetinfo[player_id]");
          $update6b = $db->Execute ("UPDATE $dbtables[ships] SET energy=$energy,fighters=fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$targettorpnum, ore=ore+$salv_ore, organics=organics+$salv_organics, goods=goods+$salv_goods WHERE ship_id=$ship_id");
        }
      }
    }
  }
}
$db->Execute("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php");

?>
