<?
include("config.php");
updatecookie();

include("languages/$lang");

$title=$l_title_port;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$result     = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $result->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

$result2    = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$shipinfo[sector_id]'");
$sectorinfo = $result2->fields;

$res = $db->Execute("SELECT * FROM $dbtables[zones] WHERE zone_id=$sectorinfo[zone_id]");
$zoneinfo = $res->fields;

if($zoneinfo[allow_trade] == 'N')
{
  $title=$l_no_trade;
  bigtitle();
  echo "$l_no_trade_info<p>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}
elseif($zoneinfo[allow_trade] == 'L')
{
  if($zoneinfo[corp_zone] == 'N')
  {
    $res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$zoneinfo[owner]");
    $ownerinfo = $res->fields;

    if($playerinfo[player_id] != $zoneinfo[owner] && $playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
    {
      $title=$l_no_trade;
      bigtitle();
      echo "$l_no_trade_out<p>";
      TEXT_GOTOMAIN();
      include("footer.php");
      die();
    }
  }
  else
  {
    if($playerinfo[team] != $zoneinfo[owner])
    {
      $title=$l_no_trade;
      bigtitle();
      echo "$l_no_trade_out<p>";
      TEXT_GOTOMAIN();
      include("footer.php");
      die();
    }
  }
}

bigtitle();

$color_red     = "red";
$color_green   = "#00FF00"; //light green
$trade_deficit = "$l_cost : ";
$trade_benefit = "$l_profit : ";


function BuildOneCol( $text = "&nbsp;", $align = "left" ) {
   echo"
   <TR>
      <TD colspan=99 align=".$align.">".$text.".</TD>
   </TR>
   ";
}

function BuildTwoCol( $text_col1 = "&nbsp;", $text_col2 = "&nbsp;", $align_col1 = "left", $align_col2 = "left" ) {
   echo"
   <TR>
      <TD align=".$align_col1.">".$text_col1."</TD>
      <TD align=".$align_col2.">".$text_col2."</TD>
   </TR>";
}


function phpTrueDelta($futurevalue,$shipvalue)
{
$tempval = $futurevalue - $shipvalue;
return $tempval;
}

function phpChangeDelta($desiredvalue,$currentvalue)
{
global $upgrade_cost;

  $Delta=0; $DeltaCost=0;
  $Delta = $desiredvalue - $currentvalue;

    while($Delta>0) 
    {
     $DeltaCost=$DeltaCost + mypw(2,$desiredvalue-$Delta); 
     $Delta=$Delta-1;
    }
    $DeltaCost=$DeltaCost * $upgrade_cost;
    
  return $DeltaCost;
}

if($playerinfo[turns] < 1)
{
  echo "$l_trade_turnneed<BR><BR>";
}
else
{
  $trade_ore      = round(abs($trade_ore));
  $trade_organics = round(abs($trade_organics));
  $trade_goods    = round(abs($trade_goods));
  $trade_energy   = round(abs($trade_energy));

  if($sectorinfo[port_type] == "special")
  {

    if(isLoanPending($playerinfo[player_id]))
    {
      echo "$l_port_loannotrade<p>";
      echo "<A HREF=IGB.php>$l_igb_term</a><p>";
      TEXT_GOTOMAIN();
      include("footer.php");
      die();
    }

    $hull_upgrade_cost = 0;
    if($hull_upgrade > $shipinfo[hull])
    {
      $hull_upgrade_cost = phpChangeDelta($hull_upgrade, $shipinfo[hull]);
    }
    $engine_upgrade_cost = 0;
    if($engine_upgrade > $shipinfo[engines])
    {
      $engine_upgrade_cost = phpChangeDelta($engine_upgrade, $shipinfo[engines]);
    }
    $power_upgrade_cost = 0;
    if($power_upgrade > $shipinfo[power])
    {
      $power_upgrade_cost = phpChangeDelta($power_upgrade, $shipinfo[power]);
    }
    $computer_upgrade_cost = 0;
    if($computer_upgrade > $shipinfo[computer])
    {
      $computer_upgrade_cost = phpChangeDelta($computer_upgrade, $shipinfo[computer]);
    }
    $sensor_upgrade_cost = 0;
    if($sensors_upgrade > $shipinfo[sensors])
    {
      $sensors_upgrade_cost = phpChangeDelta($sensors_upgrade, $shipinfo[sensors]);
    }
    $beams_upgrade_cost = 0;
    if($beams_upgrade > $shipinfo[beams])
    {
      $beams_upgrade_cost = phpChangeDelta($beams_upgrade, $shipinfo[beams]);
    }
    $armour_upgrade_cost = 0;
    if($armour_upgrade > $shipinfo[armour])
    {
      $armour_upgrade_cost = phpChangeDelta($armour_upgrade, $shipinfo[armour]);
    }
    $cloak_upgrade_cost = 0;
    if($cloak_upgrade > $shipinfo[cloak])
    {
      $cloak_upgrade_cost = phpChangeDelta($cloak_upgrade, $shipinfo[cloak]);
    }
    $torp_launchers_upgrade_cost = 0;
    if($torp_launchers_upgrade > $shipinfo[torp_launchers])
    {
      $torp_launchers_upgrade_cost = phpChangeDelta($torp_launchers_upgrade, $shipinfo[torp_launchers]);
    }
    $shields_upgrade_cost = 0;
    if($shields_upgrade > $shipinfo[shields])
    {
      $shields_upgrade_cost = phpChangeDelta($shields_upgrade, $shipinfo[shields]);
    }


    if($fighter_number < 0)
       $fighter_number = 0;
    $fighter_number  = round(abs($fighter_number));
    $fighter_max     = NUM_FIGHTERS($shipinfo[computer]) - $shipinfo[fighters];
    if($fighter_max < 0)
    {
      $fighter_max = 0;
    }
    if($fighter_number > $fighter_max)
    {
      $fighter_number = $fighter_max;
    }
    $fighter_cost    = $fighter_number * $fighter_price;
    if($torpedo_number < 0)
       $torpedo_number = 0;
    $torpedo_number  = round(abs($torpedo_number));
    $torpedo_max     = NUM_TORPEDOES($shipinfo[torp_launchers]) - $shipinfo[torps];
    if($torpedo_max < 0)
    {
      $torpedo_max = 0;
    }
    if($torpedo_number > $torpedo_max)
    {
      $torpedo_number = $torpedo_max;
    }
    $torpedo_cost = $torpedo_number * $torpedo_price;
    if($armour_number < 0)
       $armour_number = 0;
    $armour_number = round(abs($armour_number));
    $armour_max = NUM_ARMOUR($shipinfo[armour]) - $shipinfo[armour_pts];
    if($armour_max < 0)
    {
      $armour_max = 0;
    }
    if($armour_number > $armour_max)
    {
      $armour_number = $armour_max;
    }
    $armour_cost     = $armour_number * $armour_price;
    if($colonist_number < 0)
       $colonist_number = 0;
    $colonist_number = round(abs($colonist_number));
    $colonist_max    = NUM_HOLDS($shipinfo[hull]) - $shipinfo[ore] - $shipinfo[organics] -
      $shipinfo[goods] - $shipinfo[colonists];

    if($colonist_number > $colonist_max)
    {
      $colonist_number = $colonist_max;
    }

    $colonist_cost            = $colonist_number * $colonist_price;
    $dev_genesis_number       = round(abs($dev_genesis_number));
    $dev_genesis_cost         = $dev_genesis_number * $dev_genesis_price;
    $dev_beacon_number        = round(abs($dev_beacon_number));
    $dev_beacon_cost          = $dev_beacon_number * $dev_beacon_price;
    $dev_emerwarp_number      = min(round(abs($dev_emerwarp_number)), $max_emerwarp - $shipinfo[dev_emerwarp]);
    $dev_emerwarp_cost        = $dev_emerwarp_number * $dev_emerwarp_price;
    $dev_warpedit_number      = round(abs($dev_warpedit_number));
    $dev_warpedit_cost        = $dev_warpedit_number * $dev_warpedit_price;
    $dev_minedeflector_number = round(abs($dev_minedeflector_number));
    $dev_minedeflector_cost   = $dev_minedeflector_number * $dev_minedeflector_price;

    $dev_escapepod_cost = 0;
    $dev_fuelscoop_cost = 0;
    $dev_lssd_cost = 0;
    if(($escapepod_purchase) && ($shipinfo[dev_escapepod] != 'Y'))
//    if($escapepod_purchase)
    {
      $dev_escapepod_cost = $dev_escapepod_price;
    }
    if(($fuelscoop_purchase) && ($shipinfo[dev_fuelscoop] != 'Y'))
    {
      $dev_fuelscoop_cost = $dev_fuelscoop_price;
    }
    if(($lssd_purchase) && ($shipinfo[dev_lssd] != 'Y'))
    {
      $dev_lssd_cost = $dev_lssd_price;
    }
    $total_cost = $hull_upgrade_cost + $engine_upgrade_cost + $power_upgrade_cost + $computer_upgrade_cost +
      $sensors_upgrade_cost + $beams_upgrade_cost + $armour_upgrade_cost + $cloak_upgrade_cost +
      $torp_launchers_upgrade_cost + $fighter_cost + $torpedo_cost + $armour_cost + $colonist_cost +
      $dev_genesis_cost + $dev_beacon_cost + $dev_emerwarp_cost + $dev_warpedit_cost + $dev_minedeflector_cost +
      $dev_escapepod_cost + $dev_fuelscoop_cost + $dev_lssd_cost + $shields_upgrade_cost;
    if($total_cost > $playerinfo[credits])
    {
      echo "You do not have enough credits for this transaction.  The total cost is " . NUMBER($total_cost) . " credits and you only have " . NUMBER($playerinfo[credits]) . " credits.";
    }
    else
    {
     $trade_credits = NUMBER(abs($total_cost));
      echo "<TABLE BORDER=2 CELLSPACING=2 CELLPADDING=2 BGCOLOR=#400040 WIDTH=600 ALIGN=CENTER>
         <TR>
            <TD colspan=99 align=center bgcolor=#300030><font size=3 color=white><b>$l_trade_result</b></font></TD>
         </TR>
         <TR>
            <TD colspan=99 align=center><b><font color=red>$l_cost : " . $trade_credits . " $l_credits</font></b></TD>
         </TR>";

       //  Total cost is " . NUMBER(abs($total_cost)) . " credits.<BR><BR>";
      $db->Execute("UPDATE $dbtables[players] SET credits=credits-$total_cost,turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
      $query = "UPDATE $dbtables[ships] SET class=$shipinfo[class] ";
      if($hull_upgrade > $shipinfo[hull])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($hull_upgrade, $shipinfo[hull]);
        $query = $query . ", hull=hull+$tempvar";
        BuildOneCol("$l_hull $l_trade_upgraded $hull_upgrade");
      }
      if($engine_upgrade > $shipinfo[engines])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($engine_upgrade, $shipinfo[engines]);
        $query = $query . ", engines=engines+$tempvar";
        BuildOneCol("$l_engines $l_trade_upgraded $engine_upgrade");
      }
      if ($power_upgrade > $shipinfo[power])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($power_upgrade, $shipinfo[power]);
        $query = $query . ", power=power+$tempvar";
        BuildOneCol("$l_power $l_trade_upgraded $power_upgrade");
      }
      if($computer_upgrade > $shipinfo[computer])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($computer_upgrade, $shipinfo[computer]);
        $query = $query . ", computer=computer+$tempvar";
        BuildOneCol("$l_computer $l_trade_upgraded $computer_upgrade");
      }
      if($sensors_upgrade > $shipinfo[sensors])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($sensors_upgrade, $shipinfo[sensors]);
        $query = $query . ", sensors=sensors+$tempvar";
        BuildOneCol("$l_sensors $l_trade_upgraded $sensors_upgrade");
      }
      if($beams_upgrade > $shipinfo[beams])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($beams_upgrade, $shipinfo[beams]);
        $query = $query . ", beams=beams+$tempvar";
        BuildOneCol("$l_beams $l_trade_upgraded $beams_upgrade");
      }
      if($armour_upgrade > $shipinfo[armour])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($armour_upgrade, $shipinfo[armour]);
        $query = $query . ", armour=armour+$tempvar";
        BuildOneCol("$l_armour $l_trade_upgraded $armour_upgrade");
      }
      if($cloak_upgrade > $shipinfo[cloak])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($cloak_upgrade, $shipinfo[cloak]);
        $query = $query . ", cloak=cloak+$tempvar";
        BuildOneCol("$l_cloak $l_trade_upgraded $cloak_upgrade");
      }
      if($torp_launchers_upgrade > $shipinfo[torp_launchers])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($torp_launchers_upgrade, $shipinfo[torp_launchers]);
        $query = $query . ", torp_launchers=torp_launchers+$tempvar";
        BuildOneCol("$l_torp_launch $l_trade_upgraded $torp_launchers_upgrade");
      }
      if($shields_upgrade > $shipinfo[shields])
      {
        $tempvar = 0; $tempvar=phpTrueDelta($shields_upgrade, $shipinfo[shields]);
        $query = $query . ", shields=shields+$tempvar";
        BuildOneCol("$l_shields $l_trade_upgraded $shields_upgrade");
      }
      if($fighter_number)
      {
        $query = $query . ", fighters=fighters+$fighter_number";
      BuildTwoCol("$l_fighters $l_trade_added:", $fighter_number, "left", "right" );

      }
      if($torpedo_number)
      {
        $query = $query . ", torps=torps+$torpedo_number";
      BuildTwoCol("$l_torps $l_trade_added:", $torpedo_number, "left", "right" );
      }
      if($armour_number)
      {
        $query = $query . ", armour_pts=armour_pts+$armour_number";
      BuildTwoCol("$l_armourpts $l_trade_added:", $armour_number, "left", "right" );
      }
      if($colonist_number)
      {
        $query = $query . ", colonists=colonists+$colonist_number";
      BuildTwoCol("$l_colonists $l_trade_added:", $colonist_number, "left", "right" );
      }
      if($dev_genesis_number)
      {
        $query = $query . ", dev_genesis=dev_genesis+$dev_genesis_number";
      BuildTwoCol("$l_genesis $l_trade_added:", $dev_genesis_number, "left", "right" );
      }
      if($dev_beacon_number)
      {
        $query = $query . ", dev_beacon=dev_beacon+$dev_beacon_number";
      BuildTwoCol("$l_beacons $l_trade_added:", $dev_beacon_number , "left", "right" );
      }
      if($dev_emerwarp_number)
      {
        $query = $query . ", dev_emerwarp=dev_emerwarp+$dev_emerwarp_number";
      BuildTwoCol("$l_ewd $l_trade_added:", $dev_emerwarp_number , "left", "right" );
      }
      if($dev_warpedit_number)
      {
        $query = $query . ", dev_warpedit=dev_warpedit+$dev_warpedit_number";
      BuildTwoCol("$l_warpedit $l_trade_added:", $dev_warpedit_number , "left", "right" );
      }
      if($dev_minedeflector_number)
      {
        $query = $query . ", dev_minedeflector=dev_minedeflector+$dev_minedeflector_number";
      BuildTwoCol("$l_deflect $l_trade_added:", $dev_minedeflector_number , "left", "right" );
      }
      if(($escapepod_purchase) && ($shipinfo[dev_escapepod] != 'Y'))
      {
        $query = $query . ", dev_escapepod='Y'";
        BuildOneCol("$l_escape_pod $l_trade_installed");
      }
      if(($fuelscoop_purchase) && ($shipinfo[dev_fuelscoop] != 'Y'))
      {
        $query = $query . ", dev_fuelscoop='Y'";
        BuildOneCol("$l_fuel_scoop $l_trade_installed");
      }
      if(($lssd_purchase) && ($shipinfo[dev_lssd] != 'Y'))
      {
        $query = $query . ", dev_lssd='Y'";
        BuildOneCol("$l_lssd $l_trade_installed");
      }
      $query = $query . " WHERE ship_id=$shipinfo[ship_id]";
      $purchase = $db->Execute("$query");
      $hull_upgrade=0;
      echo "
      </table>
      ";
    }
  }
  elseif($sectorinfo[port_type] != "none")
  {
    /*
      Here is the TRADE fonction to strip out some "spaghetti code".
      The function saves about 60 lines of code, I hope it will be
      easier to modify/add something in this part.
                                                           --Fant0m
    */
   $price_array = array();

   function TRADE($price, $delta, $max, $limit, $factor, $port_type, $origin)
   {
      global $trade_color, $trade_deficit, $trade_result, $trade_benefit, $sectorinfo, $color_green, $color_red, $price_array;

      if($sectorinfo[port_type] ==  $port_type )
      {
        $price_array[$port_type] = $price - $delta * $max / $limit * $factor;
      }
      else
      {
        $price_array[$port_type] = $price + $delta * $max / $limit * $factor;
        $origin                  = -$origin;
      }
      /* debug info
      print "$origin*$price_array[$port_type]=";
      print $origin*$price_array[$port_type]."<br>";
      */
      return $origin;
   }


   $trade_ore       =  TRADE($ore_price,        $ore_delta,       $sectorinfo[port_ore],        $ore_limit,       $inventory_factor, "ore",        $trade_ore);
   $trade_organics  =  TRADE($organics_price,   $organics_delta,  $sectorinfo[port_organics],   $organics_limit,  $inventory_factor, "organics",   $trade_organics );
   $trade_goods     =  TRADE($goods_price,      $goods_delta,     $sectorinfo[port_goods],      $goods_limit,     $inventory_factor, "goods",      $trade_goods);
   $trade_energy    =  TRADE($energy_price,     $energy_delta,    $sectorinfo[port_energy],     $energy_limit,    $inventory_factor, "energy",     $trade_energy);

   $ore_price       =  $price_array['ore'];
   $organics_price  =  $price_array['organics'];
   $goods_price     =  $price_array['goods'];
   $energy_price    =  $price_array['energy'];

   $cargo_exchanged = $trade_ore + $trade_organics + $trade_goods;

   $free_holds = NUM_HOLDS($shipinfo[hull]) - $shipinfo[ore] - $shipinfo[organics] -
      $shipinfo[goods] - $shipinfo[colonists];
   $free_power = NUM_ENERGY($shipinfo[power]) - $shipinfo[energy];
   $total_cost = $trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price +
      $trade_energy * $energy_price;

   /* debug info
   echo "$trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price + $trade_energy * $energy_price = $total_cost";
   */

   if($free_holds < $cargo_exchanged)
   {
   echo "$l_notenough_cargo  $l_returnto_port<BR><BR>";
   }
   elseif($trade_energy > $free_power)
   {
   echo "$l_notenough_power  $l_returnto_port<BR><BR>";
   }
   elseif($playerinfo[turns] < 1)
   {
   echo "$l_notenough_turns.<BR><BR>";
   }
   elseif($playerinfo[credits] < $total_cost)
   {
   echo "$l_notenough_credits <BR><BR>";
   }
   elseif($trade_ore < 0 && abs($shipinfo[ore]) < abs($trade_ore))
   {
   echo "$l_notenough_ore ";
   }
   elseif($trade_organics < 0 && abs($shipinfo[organics]) < abs($trade_organics))
   {
   echo "$l_notenough_organics ";
   }
   elseif($trade_goods < 0 && abs($shipinfo[goods]) < abs($trade_goods))
   {
   echo "$l_notenough_goods ";
   }
   elseif($trade_energy < 0 && abs($shipinfo[energy]) < abs($trade_energy))
   {
   echo "$l_notenough_energy ";
   }
   elseif(abs($trade_organics) > $sectorinfo[port_organics])
   {
   echo $l_exceed_organics;
   }
   elseif(abs($trade_ore) > $sectorinfo[port_ore])
   {
   echo $l_exceed_ore;
   }
   elseif(abs($trade_goods) > $sectorinfo[port_goods])
   {
   echo $l_exceed_goods;
   }
   elseif(abs($trade_energy) > $sectorinfo[port_energy])
   {
   echo $l_exceed_energy;
   }
   else
   {

      if ($total_cost == 0 )
      {
         $trade_color   = "white";
         $trade_result  = "$l_cost : ";
      }
      elseif ($total_cost < 0 )
      {
         $trade_color   = $color_green;
         $trade_result  = $trade_benefit;
      }
      else
      {
         $trade_color   = $color_red;
         $trade_result  = $trade_deficit;
      }

      echo "
      <TABLE BORDER=2 CELLSPACING=2 CELLPADDING=2 BGCOLOR=#400040 WIDTH=600 ALIGN=CENTER>
         <TR>
            <TD colspan=99 align=center><font size=3 color=white><b>$l_trade_result</b></font></TD>
         </TR>
         <TR>
            <TD colspan=99 align=center><b><font color=\"". $trade_color . "\">". $trade_result ." " . NUMBER(abs($total_cost)) . " $l_credits</font></b></TD>
         </TR>
         <TR bgcolor=$color_line1>
            <TD><b><font size=2 color=white>$l_traded_ore: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_ore) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line2>
            <TD><b><font size=2 color=white>$l_traded_organics: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_organics) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line1>
            <TD><b><font size=2 color=white>$l_traded_goods: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_goods) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line2>
            <TD><b><font size=2 color=white>$l_traded_energy: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_energy) . "</font></b></TD>
         </TR>
      </TABLE>
      ";

      /* Update ship cargo, credits and turns */
      $trade_result     = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1, rating=rating+1, credits=credits-$total_cost, ship_ore=ship_ore+$trade_ore, ship_organics=ship_organics+$trade_organics, ship_goods=ship_goods+$trade_goods, ship_energy=ship_energy+$trade_energy where player_id=$playerinfo[player_id]");

      /* Make all trades positive to change port values*/
      $trade_ore        = round(abs($trade_ore));
      $trade_organics   = round(abs($trade_organics));
      $trade_goods      = round(abs($trade_goods));
      $trade_energy     = round(abs($trade_energy));


      /* Decrease supply and demand on port */
      $trade_result2    = $db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore-$trade_ore, port_organics=port_organics-$trade_organics, port_goods=port_goods-$trade_goods, port_energy=port_energy-$trade_energy where sector_id=$sectorinfo[sector_id]");
      echo "$l_trade_complete.<BR><BR>";
    }
  }
}


//-------------------------------------------------------------------------------------------------

if($sectorinfo[port_type] == "special")
{
  echo "<BR><BR>Click <A HREF=port.php>here</A> to return to the supply depot.";
}

echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php");

?>
