<?
include("config.php");

updatecookie();

include("languages/$lang");

$title=$l_rs_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

bigtitle();

$deg = pi() / 180;

if(isset($destination))
{
  $destination = round(abs($destination));
}

if(!isset($destination))
{
  echo "<FORM ACTION=rsmove.php METHOD=POST>";
  $l_rs_insector=str_replace("[sector]",$shipinfo[sector_id],$l_rs_insector);
  $l_rs_insector=str_replace("[sector_max]",$sector_max,$l_rs_insector);
  echo "$l_rs_insector<BR><BR>";
  echo "$l_rs_whichsector:  <INPUT TYPE=TEXT NAME=destination SIZE=10 MAXLENGTH=10><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=$l_rs_submit><BR><BR>";
  echo "</FORM>";
}
elseif($destination <= $sector_max && empty($engage))
{
/* Throw this old stuff away, the new distance calcs use cartesian coordinates
  $result2 = $db->Execute("SELECT angle1,angle2,distance FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
  $start = $result2->fields;
  $result3 = $db->Execute("SELECT angle1,angle2,distance FROM $dbtables[universe] WHERE sector_id=$destination");
  $finish = $result3->fields;
  $sa1 = $start[angle1] * $deg;
  $sa2 = $start[angle2] * $deg;
  $fa1 = $finish[angle1] * $deg;
  $fa2 = $finish[angle2] * $deg;
  $x = ($start[distance] * sin($sa1) * cos($sa2)) - ($finish[distance] * sin($fa1) * cos($fa2));
  $y = ($start[distance] * sin($sa1) * sin($sa2)) - ($finish[distance] * sin($fa1) * sin($fa2));
  $z = ($start[distance] * cos($sa1)) - ($finish[distance] * cos($fa1));
  $distance = round(sqrt(mypw($x, 2) + mypw($y, 2) + mypw($z, 2)));
*/
  $distance=calc_dist($shipinfo['sector_id'],$destination);
  if($distance<1) {
    // TODO: The query failed. What now?
  }


  $shipspeed = mypw($level_factor, $shipinfo[engines]);
  $triptime = round($distance / $shipspeed);
  if($triptime == 0 && $destination != $shipinfo[sector_id])
  {
    $triptime = 1;
  }
  if($shipinfo[dev_fuelscoop] == "Y")
  {
    $energyscooped = $distance * 100;
  }
  else
  {
    $energyscooped = 0;
  }
  if($shipinfo[dev_fuelscoop] == "Y" && $energyscooped == 0 && $triptime == 1)
  {
    $energyscooped = 100;
  }
  $free_power = NUM_ENERGY($shipinfo[power]) - $shipinfo[energy];
  if($free_power < $energyscooped)
  {
    $energyscooped = $free_power;
  }
  if($energyscooped < 1)
  {
    $energyscooped = 0;
  }
  if($destination == $shipinfo[sector_id])
  {
    $triptime = 0;
    $energyscooped = 0;
  }
 $l_rs_movetime=str_replace("[triptime]",NUMBER($triptime),$l_rs_movetime);
 $l_rs_energy=str_replace("[energy]",NUMBER($energyscooped),$l_rs_energy);
  echo "$l_rs_movetime $l_rs_energy<BR><BR>";
  if($triptime > $playerinfo[turns])
  {
    echo "$l_rs_noturns";
  }
  else
  {
    $l_rs_engage_link= "<A HREF=rsmove.php?engage=1&destination=$destination>" . $l_rs_engage_link . "</A>";
    $l_rs_engage=str_replace("[turns]",NUMBER($playerinfo[turns]),$l_rs_engage);
    $l_rs_engage=str_replace("[engage]",$l_rs_engage_link,$l_rs_engage);
    echo "$l_rs_engage<BR><BR>";
  }
}
elseif($destination <= $sector_max && $engage == 1)
{
/*  Throw this old stuff away, the new distance calcs use cartesian coordinates
  $result2 = $db->Execute("SELECT angle1,angle2,distance FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
  $start = $result2->fields;
  $result3 = $db->Execute("SELECT angle1,angle2,distance FROM $dbtables[universe] WHERE sector_id=$destination");
  $finish = $result3->fields;
  $sa1 = $start[angle1] * $deg;
  $sa2 = $start[angle2] * $deg;
  $fa1 = $finish[angle1] * $deg;
  $fa2 = $finish[angle2] * $deg;
  $x = ($start[distance] * sin($sa1) * cos($sa2)) - ($finish[distance] * sin($fa1) * cos($fa2));
  $y = ($start[distance] * sin($sa1) * sin($sa2)) - ($finish[distance] * sin($fa1) * sin($fa2));
  $z = ($start[distance] * cos($sa1)) - ($finish[distance] * cos($fa1));
  $distance = round(sqrt(mypw($x, 2) + mypw($y, 2) + mypw($z, 2)));
*/
  $distance=calc_dist($shipinfo['sector_id'],$destination);
  if($distance<1) {
    // TODO: The query failed. What now?
  }


  $shipspeed = mypw($level_factor, $shipinfo[engines]);
  $triptime = round($distance / $shipspeed);
  if($triptime == 0 && $destination != $shipinfo[sector_id])
  {
    $triptime = 1;
  }
  if($shipinfo[dev_fuelscoop] == "Y")
  {
    $energyscooped = $distance * 100;
  }
  else
  {
    $energyscooped = 0;
  }
  if($shipinfo[dev_fuelscoop] == "Y" && $energyscooped == 0 && $triptime == 1)
  {
    $energyscooped = 100;
  }
  $free_power = NUM_ENERGY($shipinfo[power]) - $shipinfo[energy];
  if($free_power < $energyscooped)
  {
    $energyscooped = $free_power;
  }
  if(!isset($energyscooped))
  {
    $energyscooped = "0";
  }
  if($energyscooped < 1)
  {
    $energyscooped = 0;
  }
  if($destination == $shipinfo[sector_id])
  {
    $triptime = 0;
    $energyscooped = 0;
  }
  if($triptime > $playerinfo[turns])
  {
   $l_rs_movetime=str_replace("[triptime]",NUMBER($triptime),$l_rs_movetime);
    echo "$l_rs_movetime<BR><BR>";
    echo "$l_rs_noturns";
    $db->Execute("UPDATE $dbtables[ships] SET cleared_defences=' ' WHERE ship_id=$shipinfo[ship_id]");
  }
  else
  {
    $ok=1;
    $sector = $destination;
    $calledfrom = "rsmove.php";
    include("check_fighters.php");
    if($ok>0)
    {
       $stamp = date("Y-m-d H-i-s");
       $update = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp', turns=turns-$triptime,turns_used=turns_used+$triptime WHERE player_id=$playerinfo[player_id]");
       $update = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$destination,energy=energy+$energyscooped WHERE ship_id=$shipinfo[ship_id]");
       log_move($shipinfo[ship_id],$destination);
       $l_rs_ready=str_replace("[sector]",$destination,$l_rs_ready);
       $l_rs_ready=str_replace("[triptime]",NUMBER($triptime),$l_rs_ready);
       $l_rs_ready=str_replace("[energy]",NUMBER($energyscooped),$l_rs_ready);
       echo "$l_rs_ready<BR><BR>";
       include("check_mines.php");
    }
  }
}
else
{
  echo "$l_rs_invalid.<BR><BR>";
  $db->Execute("UPDATE $dbtables[ships] SET cleared_defences=' ' WHERE ship_id=$shipinfo[ship_id]");
}


//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php");

?>
