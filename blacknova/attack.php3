<?
  
include("config.php3");
updatecookie();
include_once($gameroot . "/languages/$lang");

connectdb();

$title=$l_att_title;
include("header.php3");

if(checklogin())
{
  die();
}
//-------------------------------------------------------------------------------------------------
 mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ, planets WRITE, bn_news WRITE, logs WRITE");
$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query ("SELECT * FROM ships WHERE ship_id='$ship_id'");
$targetinfo=mysql_fetch_array($result2);

bigtitle();

srand((double)microtime()*1000000);

/* check to ensure target is in the same sector as player */
if($targetinfo[sector] != $playerinfo[sector] || $targetinfo[on_planet] == "Y")
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
  $success = (10 - $targetinfo[cloak] + $playerinfo[sensors]) * 5;
  if($success < 5)
  {
    $success = 5;
  }
  if($success > 95)
  {
    $success = 95;
  }
  $flee = (10 - $targetinfo[engines] + $playerinfo[engines]) * 5;
  $roll = rand(1, 100);
  $roll2 = rand(1, 100);

  $res = mysql_query("SELECT allow_attack,universe.zone_id FROM zones,universe WHERE sector_id='$targetinfo[sector]' AND zones.zone_id=universe.zone_id");
  $zoneinfo = mysql_fetch_array($res);
  if($zoneinfo[allow_attack] == 'N')
  {
    echo "$l_att_noatt<BR><BR>";
  }
  elseif($flee < $roll2)
  {
    echo "$l_att_flee<BR><BR>";
    mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    playerlog($targetinfo[ship_id], LOG_ATTACK_OUTMAN, "$playerinfo[character_name]");
  }
  elseif($roll > $success)
  {
    /* if scan fails - inform both player and target. */
    echo "$l_planet_noscan<BR><BR>";
    mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    playerlog($targetinfo[ship_id], LOG_ATTACK_OUTSCAN, "$playerinfo[character_name]");
  }
  else
  {
    /* if scan succeeds, show results and inform target. */
    if($targetinfo[hull] > $ewd_maxhullsize)
    {
       $chance = ($targetinfo[hull] - $ewd_maxhullsize) * 10;
    }
    else
    {
       $chance = 0;
    }
    $random_value = rand(1,100);
    if($targetinfo[dev_emerwarp] > 0 && $random_value > $chance)
    {
      /* need to change warp destination to random sector in universe */
      $rating_change=round($targetinfo[rating]*.1);
      $dest_sector=rand(1,$sector_max);
      mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1,rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
      $l_att_ewdlog=str_replace("[name]",$playerinfo[character_name],$l_att_ewdlog);
      $l_att_ewdlog=str_replace("[sector]",$playerinfo[sector],$l_att_ewdlog);
      playerlog($targetinfo[ship_id], LOG_ATTACK_EWD, "$playerinfo[character_name]");
      $result_warp = mysql_query ("UPDATE ships SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1,cleared_defences=' ' WHERE ship_id=$targetinfo[ship_id]");
      echo "$l_att_ewd<BR><BR>";
    }
    else
    {
      if($targetinfo[dev_emerwarp] > 0)
      {
        playerlog($targetinfo[ship_id], LOG_ATTACK_EWDFAIL, $playerinfo[character_name]);
      }
      $targetbeams = NUM_BEAMS($targetinfo[beams]);
      if($targetbeams>$targetinfo[ship_energy])
      {
        $targetbeams=$targetinfo[ship_energy];
      }
      $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetbeams;
      $playerbeams = NUM_BEAMS($playerinfo[beams]);
      if($playerbeams>$playerinfo[ship_energy])
      {
        $playerbeams=$playerinfo[ship_energy];
      }
      $playerinfo[ship_energy]=$playerinfo[ship_energy]-$playerbeams;
      $playershields = NUM_SHIELDS($playerinfo[shields]);
      if($playershields>$playerinfo[ship_energy])
      {
        $playershields=$playerinfo[ship_energy];
      }
      $playerinfo[ship_energy]=$playerinfo[ship_energy]-$playershields;
      $targetshields = NUM_SHIELDS($targetinfo[shields]);
      if($targetshields>$targetinfo[ship_energy])
      {
        $targetshields=$targetinfo[ship_energy];
      }
      $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetshields;

      $playertorpnum = round(pow($level_factor,$playerinfo[torp_launchers]))*2;
      if($playertorpnum > $playerinfo[torps])
      {
        $playertorpnum = $playerinfo[torps];
      }
      $targettorpnum = round(pow($level_factor,$targetinfo[torp_launchers]))*2;
      if($targettorpnum > $targetinfo[torps])
      {
        $targettorpnum = $targetinfo[torps];
      }
      $playertorpdmg = $torp_dmg_rate*$playertorpnum;
      $targettorpdmg = $torp_dmg_rate*$targettorpnum;
      $playerarmour = $playerinfo[armour_pts];
      $targetarmour = $targetinfo[armour_pts];
      $playerfighters = $playerinfo[ship_fighters];
      $targetfighters = $targetinfo[ship_fighters];
      $targetdestroyed = 0;
      $playerdestroyed = 0;
      echo "$l_att_att $targetinfo[character_name] $l_abord $targetinfo[ship_name]:<BR><BR>";
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
        if($targetinfo[dev_escapepod] == "Y")
        {
          $rating=round($targetinfo[rating]/2);
          echo "$l_att_espod<BR><BR>";
          mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating',cleared_defences=' ' WHERE ship_id=$targetinfo[ship_id]");
          playerlog($targetinfo[ship_id], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
        }
        else
        {
          playerlog($targetinfo[ship_id], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
          db_kill_player($targetinfo['ship_id']);
        }

        if($playerarmour > 0)
        {
          $rating_change=round($targetinfo[rating]*$rating_combat_factor);
          $free_ore = round($targetinfo[ship_ore]/2);
          $free_organics = round($targetinfo[ship_organics]/2);
          $free_goods = round($targetinfo[ship_goods]/2);
          $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
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
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $targetinfo[hull]))+round(pow($upgrade_factor, $targetinfo[engines]))+round(pow($upgrade_factor, $targetinfo[power]))+round(pow($upgrade_factor, $targetinfo[computer]))+round(pow($upgrade_factor, $targetinfo[sensors]))+round(pow($upgrade_factor, $targetinfo[beams]))+round(pow($upgrade_factor, $targetinfo[torp_launchers]))+round(pow($upgrade_factor, $targetinfo[shields]))+round(pow($upgrade_factor, $targetinfo[armour]))+round(pow($upgrade_factor, $targetinfo[cloak])));
          $ship_salvage_rate=rand(10,20);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;

          $l_att_ysalv=str_replace("[salv_ore]",$salv_ore,$l_att_ysalv);
          $l_att_ysalv=str_replace("[salv_organics]",$salv_organics,$l_att_ysalv);
          $l_att_ysalv=str_replace("[salv_goods]",$salv_goods,$l_att_ysalv);
          $l_att_ysalv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_ysalv);
          $l_att_ysalv=str_replace("[ship_salvage]",$ship_salvage,$l_att_ysalv);
          $l_att_ysalv=str_replace("[rating_change]",NUMBER(abs($rating_change)),$l_att_ysalv);

          echo $l_att_ysalv;
          $update3 = mysql_query ("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
          $armour_lost=$playerinfo[armour_pts]-$playerarmour;
          $fighters_lost=$playerinfo[ship_fighters]-$playerfighters;
          $energy=$playerinfo[ship_energy];
          $update3b = mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$playertorpnum, turns=turns-1, turns_used=turns_used+1, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
          echo "$l_att_ylost $armour_lost $l_armourpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<BR><BR>";
        }
      }
      else
      {
       $l_att_stilship=str_replace("[name]",$targetinfo[character_name],$l_att_stilship);
        echo "$l_att_stilship<BR>";
        $rating_change=round($targetinfo[rating]*.1);
        $armour_lost=$targetinfo[armour_pts]-$targetarmour;
        $fighters_lost=$targetinfo[ship_fighters]-$targetfighters;
        $energy=$targetinfo[ship_energy];
        playerlog($targetinfo[ship_id], LOG_ATTACKED_WIN, "$playerinfo[character_name]|$armour_lost|$fighters_lost");
        $update4 = mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
        $armour_lost=$playerinfo[armour_pts]-$playerarmour;
        $fighters_lost=$playerinfo[ship_fighters]-$playerfighters;
        $energy=$playerinfo[ship_energy];
        $update4b = mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$playertorpnum, turns=turns-1, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
        echo "$l_att_ylost $armour_lost $l_armourpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<BR><BR>";
      }
      if($playerarmour < 1)
      {
        echo "$l_att_yshiplost<BR><BR>";
        if($playerinfo[dev_escapepod] == "Y")
        {
          $rating=round($playerinfo[rating]/2);
          echo "$l_att_loosepod<BR><BR>";
          mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating' WHERE ship_id=$playerinfo[ship_id]");
        }
        else
        {
          db_kill_player($playerinfo['ship_id']);
        }
        if($targetarmour > 0)
        {
          $free_ore = round($playerinfo[ship_ore]/2);
          $free_organics = round($playerinfo[ship_organics]/2);
          $free_goods = round($playerinfo[ship_goods]/2);
          $free_holds = NUM_HOLDS($targetinfo[hull]) - $targetinfo[ship_ore] - $targetinfo[ship_organics] - $targetinfo[ship_goods] - $targetinfo[ship_colonists];
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
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor, $playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armour]))+round(pow($upgrade_factor, $playerinfo[cloak])));
          $ship_salvage_rate=rand(10,20);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;

          $l_att_salv=str_replace("[salv_ore]",$salv_ore,$l_att_salv);
          $l_att_salv=str_replace("[salv_organics]",$salv_organics,$l_att_salv);
          $l_att_salv=str_replace("[salv_goods]",$salv_goods,$l_att_salv);
          $l_att_salv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_salv);
          $l_att_salv=str_replace("[ship_salvage]",$ship_salvage,$l_att_salv);
          $l_att_salv=str_replace("[name]",$targetinfo[character_name],$l_att_salv);

          echo "$l_att_salv<BR>";
          $update6 = mysql_query ("UPDATE ships SET credits=credits+$ship_salvage, ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods WHERE ship_id=$targetinfo[ship_id]");
          $armour_lost=$targetinfo[armour_pts]-$targetarmour;
          $fighters_lost=$targetinfo[ship_fighters]-$targetfighters;
          $energy=$targetinfo[ship_energy];
          $update6b = mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
        }
      }
    }
  }
}
mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?>
