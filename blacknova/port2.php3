<?

include("config.php3");
updatecookie();

$title="Trading at Port";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$result     = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$result2    = mysql_query("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = mysql_fetch_array($result2);

$res = mysql_query("SELECT * FROM zones WHERE zone_id=$sectorinfo[zone_id]");
$zoneinfo = mysql_fetch_array($res);

if($zoneinfo[allow_trade] == 'N')
{
  $title="Trade forbidden";
  bigtitle();
  echo "Trading at this port is not allowed<p>";
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}
elseif($zoneinfo[allow_trade] == 'L')
{
  if($zoneinfo[corp_zone] == 'N')
  {
    $res = mysql_query("SELECT team FROM ships WHERE ship_id=$zoneinfo[owner]");
    $ownerinfo = mysql_fetch_array($res);

    if($playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
    {
      $title="Trade forbidden";
      bigtitle();
      echo "Trading at this port is not allowed for outsiders<p>";
      TEXT_GOTOMAIN();
      include("footer.php3");
      die();
    }
  }
  else
  {
    if($playerinfo[team] != $zoneinfo[owner])
    {
      $title="Trade forbidden";
      bigtitle();
      echo "Trading at this port is not allowed for outsiders<p>";
      TEXT_GOTOMAIN();
      include("footer.php3");
      die();
    }
  }
}

bigtitle();

$color_red     = "red";
$color_green   = "#00FF00"; //light green
$trade_deficit = "Cost : ";
$trade_benefit = "Profit : ";


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

if($playerinfo[turns] < 1)
{
  echo "You need at least one turn to trade at a port.<BR><BR>";
}
else
{
  $trade_ore      = round(abs($trade_ore));
  $trade_organics = round(abs($trade_organics));
  $trade_goods    = round(abs($trade_goods));
  $trade_energy   = round(abs($trade_energy));

  if($sectorinfo[port_type] == "special")
  {
    /* the code for a special port will go here! */
    if($hull_upgrade)
    {
      $hull_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[hull]));
    }
    if($engine_upgrade)
    {
      $engine_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[engines]));
    }
    if($power_upgrade)
    {
      $power_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[power]));
    }
    if($computer_upgrade)
    {
      $computer_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[computer]));
    }
    if($sensors_upgrade)
    {
      $sensors_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[sensors]));
    }
    if($beams_upgrade)
    {
      $beams_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[beams]));
    }
    if($armour_upgrade)
    {
      $armour_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[armour]));
    }
    if($cloak_upgrade)
    {
      $cloak_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[cloak]));
    }
    if($torp_launchers_upgrade)
    {
      $torp_launchers_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[torp_launchers]));
    }
    if($shields_upgrade)
    {
      $shields_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[shields]));
    }
    
    
    $fighter_number  = round(abs($fighter_number));
    $fighter_max     = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[ship_fighters];
    if($fighter_max < 0)
    {
      $fighter_max = 0;
    }
    if($fighter_number > $fighter_max)
    {
      $fighter_number = $fighter_max;
    }
    $fighter_cost    = $fighter_number * $fighter_price;
    $torpedo_number  = round(abs($torpedo_number));
    $torpedo_max     = NUM_TORPEDOES($playerinfo[torp_launchers]) - $playerinfo[torps];
    if($torpedo_max < 0)
    {
      $torpedo_max = 0;
    }
    if($torpedo_number > $torpedo_max)
    {
      $torpedo_number = $torpedo_max;
    }
    $torpedo_cost = $torpedo_number * $torpedo_price;
    $armour_number = round(abs($armour_number));
    $armour_max = NUM_ARMOUR($playerinfo[armour]) - $playerinfo[armour_pts];
    if($armour_max < 0)
    {
      $armour_max = 0;
    }
    if($armour_number > $armour_max)
    {
      $armour_number = $armour_max;
    }
    $armour_cost     = $armour_number * $armour_price;
    $colonist_number = round(abs($colonist_number));
    $colonist_max    = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - 
      $playerinfo[ship_goods] - $playerinfo[ship_colonists];

    if($colonist_number > $colonist_max)
    {
      $colonist_number = $colonist_max;
    }
    
    $colonist_cost            = $colonist_number * $colonist_price;    
    $dev_genesis_number       = round(abs($dev_genesis_number));
    $dev_genesis_cost         = $dev_genesis_number * $dev_genesis_price;
    $dev_beacon_number        = round(abs($dev_beacon_number));
    $dev_beacon_cost          = $dev_beacon_number * $dev_beacon_price;
    $dev_emerwarp_number      = min(round(abs($dev_emerwarp_number)), $max_emerwarp - $playerinfo[dev_emerwarp]);
    $dev_emerwarp_cost        = $dev_emerwarp_number * $dev_emerwarp_price;
    $dev_warpedit_number      = round(abs($dev_warpedit_number));
    $dev_warpedit_cost        = $dev_warpedit_number * $dev_warpedit_price;
    $dev_minedeflector_number = round(abs($dev_minedeflector_number));
    $dev_minedeflector_cost   = $dev_minedeflector_number * $dev_minedeflector_price;
    
    if($escapepod_purchase)
    {
      $dev_escapepod_cost = $dev_escapepod_price;
    }
    if($fuelscoop_purchase)
    {
      $dev_fuelscoop_cost = $dev_fuelscoop_price;
    }
    $total_cost = $hull_upgrade_cost + $engine_upgrade_cost + $power_upgrade_cost + $computer_upgrade_cost +
      $sensors_upgrade_cost + $beams_upgrade_cost + $armour_upgrade_cost + $cloak_upgrade_cost + 
      $torp_launchers_upgrade_cost + $fighter_cost + $torpedo_cost + $armour_cost + $colonist_cost + 
      $dev_genesis_cost + $dev_beacon_cost + $dev_emerwarp_cost + $dev_warpedit_cost + $dev_minedeflector_cost +
      $dev_escapepod_cost + $dev_fuelscoop_cost + $shields_upgrade_cost;
    if($total_cost > $playerinfo[credits])
    {
      echo "You do not have enough credits for this transaction.  The total cost is " . NUMBER($total_cost) . " credits and you only have " . NUMBER($playerinfo[credits]) . " credits.<BR><BR>Click <A HREF=port.php3>here</A> to return to the supply depot.<BR><BR>";
    }
    else
    {
      
     $trade_credits = NUMBER(abs($total_cost));
      echo "<TABLE BORDER=2 CELLSPACING=2 CELLPADDING=2 BGCOLOR=#400040 WIDTH=600 ALIGN=CENTER>
         <TR>
            <TD colspan=99 align=center bgcolor=#300030><font size=3 color=white><b>Results for this trade</b></font></TD>
         </TR>
         <TR>
            <TD colspan=99 align=center><b><font color=red>Cost : " . $trade_credits . " credits</font></b></TD>
         </TR>";
         
       //  Total cost is " . NUMBER(abs($total_cost)) . " credits.<BR><BR>";
      $query = "UPDATE ships SET credits=credits-$total_cost";
      if($hull_upgrade)
      {
        $query = $query . ", hull=hull+1";
        BuildOneCol("Hull upgraded");
      }
      if($engine_upgrade) 
      {
        $query = $query . ", engines=engines+1";
        BuildOneCol("Engines upgraded");
      }
      if ($power_upgrade) 
      {
        $query = $query . ", power=power+1";
        BuildOneCol("Power upgraded");
      }
      if($computer_upgrade) 
      {
        $query = $query . ", computer=computer+1";
        BuildOneCol("Computer upgraded");
      }
      if($sensors_upgrade) 
      {
        $query = $query . ", sensors=sensors+1";
        BuildOneCol("Sensors upgraded");
      }
      if($beams_upgrade) 
      {
        $query = $query . ", beams=beams+1";
        BuildOneCol("Beam Weapons upgraded");
      }
      if($armour_upgrade) 
      {
        $query = $query . ", armour=armour+1";
        BuildOneCol("Armour Weapons upgraded");
      }
      if($cloak_upgrade) 
      {
        $query = $query . ", cloak=cloak+1";
        BuildOneCol("Cloak upgraded");
      }
      if($torp_launchers_upgrade) 
      {
        $query = $query . ", torp_launchers=torp_launchers+1";
        BuildOneCol("Torpedo Launchers upgraded");
      }
      if($shields_upgrade) 
      {
        $query = $query . ", shields=shields+1";
        BuildOneCol("Shields upgraded");
      }
      if($fighter_number) 
      {
        $query = $query . ", ship_fighters=ship_fighters+$fighter_number";
      BuildTwoCol("Fighters added:", $fighter_number, "left", "right" );

      }
      if($torpedo_number) 
      {
        $query = $query . ", torps=torps+$torpedo_number";
      BuildTwoCol("Torpedoes added:", $torpedo_number, "left", "right" );
      }
      if($armour_number) 
      {
        $query = $query . ", armour_pts=armour_pts+$armour_number";
      BuildTwoCol("Points of armour added:", $armour_number, "left", "right" );
      }
      if($colonist_number) 
      {
        $query = $query . ", ship_colonists=ship_colonists+$colonist_number";
      BuildTwoCol("Colonists added:", $colonist_number, "left", "right" );
      }
      if($dev_genesis_number) 
      {
        $query = $query . ", dev_genesis=dev_genesis+$dev_genesis_number";
      BuildTwoCol("Genesis Devices added:", $dev_genesis_number, "left", "right" );
      }
      if($dev_beacon_number) 
      {
        $query = $query . ", dev_beacon=dev_beacon+$dev_beacon_number";
      BuildTwoCol("Space Beacons added:", $dev_beacon_number , "left", "right" );
      }
      if($dev_emerwarp_number) 
      {
        $query = $query . ", dev_emerwarp=dev_emerwarp+$dev_emerwarp_number";
      BuildTwoCol("Emergency Warp Devices added:", $dev_emerwarp_number , "left", "right" );
      }
      if($dev_warpedit_number) 
      {
        $query = $query . ", dev_warpedit=dev_warpedit+$dev_warpedit_number";
      BuildTwoCol("Warp Editors added:", $dev_warpedit_number , "left", "right" );        
      }
      if($dev_minedeflector_number) 
      {
        $query = $query . ", dev_minedeflector=dev_minedeflector+$dev_minedeflector_number";
      BuildTwoCol("Mine Deflectors added:", $dev_minedeflector_number , "left", "right" );        
      }
      if($escapepod_purchase) 
      {
        $query = $query . ", dev_escapepod='Y'";
        BuildOneCol("Escape Pod installed");
      }
      if($fuelscoop_purchase) 
      {
        $query = $query . ", dev_fuelscoop='Y'";
        BuildOneCol("Fuel Scoop installed");
      }
      $query = $query . ", turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]";
      $purchase = mysql_query("$query");
      echo "
      </table>
      ";
    }
  }
  elseif($sectorinfo[port_type] != "none")
  {
    /* 
      Here is the TRADE fonction to strip out some "spaghetti code"
      That fonction save about 60 lines of code, hope this will be 
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
  
   $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - 
      $playerinfo[ship_goods] - $playerinfo[ship_colonists];
   $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
   $total_cost = $trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price + 
      $trade_energy * $energy_price;   
      
   /* debug info
   echo "$trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price + $trade_energy * $energy_price = $total_cost";
   */
   
   if($free_holds < $cargo_exchanged)
   {
   echo "You do not have enough free cargo holds for the commodities you wish to purchase.  Click <A HREF=port.php3>here</A> to return to the port menu.<BR><BR>";
   }
   elseif($trade_energy > $free_power)
   {
   echo "You do not have enough free power storage for the energy you wish to purchase.  Click <A HREF=port.php3>here</A> to return to the port menu.<BR><BR>";
   }
   elseif($playerinfo[turns] < 1)
   {
   echo "You do not have enough turns to complete the transaction.<BR><BR>";
   }
   elseif($playerinfo[credits] < $total_cost)
   {
   echo "You do not have enough credits to complete the transaction. <BR><BR>";  
   }
   elseif($trade_ore < 0 && abs($playerinfo[ship_ore]) < abs($trade_ore))
   {
   echo "You do not have enough ore to complete the transaction. ";
   }
   elseif($trade_organics < 0 && abs($playerinfo[ship_organics]) < abs($trade_organics))
   {
   echo "You do not have enough organics to complete the transaction. ";
   }
   elseif($trade_goods < 0 && abs($playerinfo[ship_goods]) < abs($trade_goods))
   {
   echo "You do not have enough goods to complete the transaction. ";
   }
   elseif($trade_energy < 0 && abs($playerinfo[ship_energy]) < abs($trade_energy))
   {
   echo "You do not have enough energy to complete the transaction. ";
   }
   elseif(abs($trade_organics) > $sectorinfo[port_organics])
   {
   echo "Number of organics exceeds the supply/demand.  ";
   }
   elseif(abs($trade_ore) > $sectorinfo[port_ore])
   {
   echo "Number of ore exceeds the supply/demand.  ";
   }
   elseif(abs($trade_goods) > $sectorinfo[port_goods])
   {
   echo "Number of goods exceeds the supply/demand.  ";
   }
   elseif(abs($trade_energy) > $sectorinfo[port_energy])
   {
   echo "Number of energy exceeds the supply/demand.  ";
   }
   else
   {

      if ($total_cost == 0 ) 
      {
         $trade_color   = "white";
         $trade_result  = "Cost : ";
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
            <TD colspan=99 align=center><font size=3 color=white><b>Results for this trade</b></font></TD>
         </TR>
         <TR>
            <TD colspan=99 align=center><b><font color=\"". $trade_color . "\">". $trade_result ." " . NUMBER(abs($total_cost)) . " credits</font></b></TD>
         </TR>
         <TR bgcolor=$color_line1>
            <TD><b><font size=2 color=white>Traded Ore: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_ore) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line2>
            <TD><b><font size=2 color=white>Traded Organics: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_organics) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line1>
            <TD><b><font size=2 color=white>Traded Goods: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_goods) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line2>
            <TD><b><font size=2 color=white>Traded Energy: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_energy) . "</font></b></TD>
         </TR>
      </TABLE>
      ";

      /* Update ship cargo, credits and turns */
      $trade_result     = mysql_query("UPDATE ships SET turns=turns-1, turns_used=turns_used+1, rating=rating+1, credits=credits-$total_cost, ship_ore=ship_ore+$trade_ore, ship_organics=ship_organics+$trade_organics, ship_goods=ship_goods+$trade_goods, ship_energy=ship_energy+$trade_energy where ship_id=$playerinfo[ship_id]");

      /* Make all trades positive to change port values*/
      $trade_ore        = round(abs($trade_ore));
      $trade_organics   = round(abs($trade_organics));
      $trade_goods      = round(abs($trade_goods));
      $trade_energy     = round(abs($trade_energy));

      
      /* Decrease supply and demand on port */
      $trade_result2    = mysql_query("UPDATE universe SET port_ore=port_ore-$trade_ore, port_organics=port_organics-$trade_organics, port_goods=port_goods-$trade_goods, port_energy=port_energy-$trade_energy where sector_id=$sectorinfo[sector_id]");
      echo "Trade completed.<BR><BR>";
    }
  }
}


//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?>
