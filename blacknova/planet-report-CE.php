<?

// var_dump($HTTP_POST_VARS);

include("config.php");
updatecookie();
include("languages/$lang");

$title=$l_pr_title;

include("header.php");

connectdb();

if(checklogin())
{
  die();
}

bigtitle();

echo "<BR>";
echo "Click <A HREF=planet-report.php>here</A> to return to report menu<br>";


if(isset($HTTP_POST_VARS["TPCreds"]))
  collect_credits($HTTP_POST_VARS["TPCreds"]);
elseif(isset($buildp) AND isset($builds))
  go_build_base($buildp, $builds);
else
 change_planet_production($HTTP_POST_VARS);


echo "<BR><BR>";
TEXT_GOTOMAIN();


function go_build_base($planet_id, $sector_id)
{
  global $db;
  global $dbtables;
  global $base_ore, $base_organics, $base_goods, $base_credits;
  global $l_planet_bbuild;
  global $username;

  echo "<BR>Click <A HREF=planet-report.php?PRepType=1>here</A> to return to the Planet Status Report<BR><BR>";

  $result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
  $playerinfo=$result->fields;

  $result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$playerinfo[sector]");
  $sectorinfo=$result2->fields;

  $result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
  if($result3)
    $planetinfo=$result3->fields;


  Real_Space_Move($sector_id);


  echo "<BR>Click <A HREF=planet.php?planet_id=$planet_id>here</A> to go to the Planet Menu<BR><BR>";


  // build a base
  if($planetinfo[ore] >= $base_ore && $planetinfo[organics] >= $base_organics && $planetinfo[goods] >= $base_goods && $planetinfo[credits] >= $base_credits)
  {
    // ** Create The Base
    $update1 = $db->Execute("UPDATE $dbtables[planets] SET base='Y', ore=$planetinfo[ore]-$base_ore, organics=$planetinfo[organics]-$base_organics, goods=$planetinfo[goods]-$base_goods, credits=$planetinfo[credits]-$base_credits WHERE planet_id=$planet_id");
    // ** Update User Turns
    $update1b = $db->Execute("UPDATE $dbtables[ships] SET turns=turns-1, turns_used=turns_used-1 where ship_ip=$playerinfo[ship_id]");
    // ** Refresh Plant Info
    $result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
    $planetinfo=$result3->fields;
    // ** Notify User Of Base Results
    echo "$l_planet_bbuild<BR><BR>";
    // ** Calc Ownership and Notify User Of Results
    $ownership = calc_ownership($playerinfo[sector]);
    if(!empty($ownership))
    {
      echo "$ownership<p>";
    }
  }
}

function collect_credits($planetarray)
{
  global $db, $dbtables, $username;

  $CS = "GO"; // Current State

  // create an array of sector -> planet pairs
  for($i = 0; $i < count($planetarray); $i++)
  {
    $res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planetarray[$i]");
    $s_p_pair[$i]= array($res->fields["sector_id"], $planetarray[$i]);
  }

  // Sort the array so that it is in order of sectors, lowest number first, not closest
  sort($s_p_pair);
  reset($s_p_pair);

  // run through the list of sector planet pairs realspace moving to each sector and then performing the transfer. 
  // Based on the way realspace works we don't need a sub loop -- might add a subloop to clean things up later.


  for($i=0; $i < count($planetarray) && $CS == "GO"; $i++)
  {
    echo "<BR>";
    $CS = real_space_move($s_p_pair[$i][0]);

    if($CS == "GO")
      $CS = Take_Credits($s_p_pair[$i][0], $s_p_pair[$i][1]);
    else
     echo "<BR> NOT ENOUGH TURNS TO TAKE CREDITS<BR>";

    echo "<BR>";
  }

  if($CS != "GO")
  {
    echo "<BR>Not enough turns to complete credit collection<BR>";
  }

  echo "<BR>";
  echo "Click <A HREF=planet-report.php?PRepType=1>here</A> to return to the Planet Status Report<br>";
}

function change_planet_production($prodpercentarray)
{
  global $db, $dbtables;

  echo "Click <A HREF=planet-report.php?PRepType=2>here</A> to return to the Change Planet Production Report<br><br>";

  while(list($commod_type, $valarray) = each($prodpercentarray))
  {
    while(list($planet_id, $prodpercent) = each($valarray))
    {  
      if($commod_type == "prod_ore")
      {
        $db->Execute("UPDATE $dbtables[planets] SET $commod_type=$prodpercent WHERE planet_id=$planet_id");
        $db->Execute("UPDATE $dbtables[planets] SET sells='N' WHERE planet_id=$planet_id");
      }
      elseif($commod_type == "sells")
      {
        $db->Execute("UPDATE $dbtables[planets] SET sells='Y' WHERE planet_id=$prodpercent");
      }
      else
      {
        $db->Execute("UPDATE $dbtables[planets] SET $commod_type=$prodpercent WHERE planet_id=$planet_id");
      }
    }
  }

  echo "Production Percentages Updated<BR>";

}

function Take_Credits($sector_id, $planet_id)
{
  global $db, $dbtables, $username;

  // Get basic Database information (ship and planet)
  $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
  $playerinfo = $res->fields;
  $res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
  $planetinfo = $res->fields;

  // Set the name for unamed planets to be "unnamed"
  if(empty($planetinfo[name]))
  {
    $planet[name] = $l_unnamed;
  }

  //verify player is still in same sector as the planet
  if($playerinfo[sector] == $planetinfo[sector_id])
  {
    if($playerinfo[turns] >= 1)
    {
      // verify player owns the planet to take credits from
      if($planetinfo[owner] == $playerinfo[ship_id])
      {
        // get number of credits from the planet and current number player has on ship
        $CreditsTaken = $planetinfo[credits];
        $CreditsOnShip = $playerinfo[credits];
        $NewShipCredits = $CreditsTaken + $CreditsOnShip;

        // update the planet record for credits
        $res = $db->Execute("UPDATE $dbtables[planets] SET credits=0 WHERE planet_id=$planetinfo[planet_id]");

        // update the player record
        // credits
        $res = $db->Execute("UPDATE $dbtables[ships] SET credits=$NewShipCredits WHERE email='$username'");
        // turns
        $res = $db->Execute("UPDATE $dbtables[ships] SET turns=turns-1 WHERE email='$username'");

        echo "Took " . NUMBER($CreditsTaken) . " Credits from planet $planetinfo[name]. <BR>";
        echo "Your ship - " . $playerinfo[ship_name] . " - now has " . NUMBER($NewShipCredits) . " onboard. <BR>";

        $retval = "GO";
      }
      else
      {
        echo "<BR><BR>You do not own planet $planetinfo[name]<BR><BR>";

        $retval = "GO";
      }
    }
    else
    {
      echo "<BR><BR>You do not have enough turns to take credits from $planetinfo[name] in sector $planetinfo[sector_id]<BR><BR>";

      $retval = "BREAK-TURNS";
    }
  }
  else
  {
    echo "<BR><BR>You must be in the same sector as the planet to transfer to/from the planet<BR><BR>";

    $retval = "BREAK-SECTORS";
  }

  return($retval);
}

function Real_Space_Move($destination)
{
  global $db;
  global $dbtables;
  global $level_factor;
  global $username;
  global $lang;

  $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
  $playerinfo = $res->fields;

  $result2 = $db->Execute("SELECT angle1,angle2,distance FROM $dbtables[universe] WHERE sector_id=$playerinfo[sector]");
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
  $distance = round(sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2)));
  $shipspeed = pow($level_factor, $playerinfo[engines]);
  $triptime = round($distance / $shipspeed);

  if($triptime == 0 && $destination != $playerinfo[sector])
  {
    $triptime = 1;
  }


  if($playerinfo[dev_fuelscoop] == "Y")
  {
    $energyscooped = $distance * 100;
  }
  else
  {
    $energyscooped = 0;
  }

 
  if($playerinfo[dev_fuelscoop] == "Y" && $energyscooped == 0 && $triptime == 1)
  {
    $energyscooped = 100;
  }
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];

  // amount of energy that can be stored is less than amount scooped amount scooped is set to what can be stored
  if($free_power < $energyscooped)
  {
    $energyscooped = $free_power;
  }

  // make sure energyscooped is not null
  if(!isset($energyscooped))
  {
    $energyscooped = "0";
  }

  // make sure energyscooped not negative, or decimal
  if($energyscooped < 1)
  {
    $energyscooped = 0;
  }

  // check to see if already in that sector
  if($destination == $playerinfo[sector])
  {
    $triptime = 0;
    $energyscooped = 0;
  }

  if($triptime > $playerinfo[turns])
  {
    $l_rs_movetime=str_replace("[triptime]",NUMBER($triptime),$l_rs_movetime);
    echo "$l_rs_movetime<BR><BR>";
    echo "$l_rs_noturns";
    $db->Execute("UPDATE $dbtables[ships] SET cleared_defences=' ' where ship_id=$playerinfo[ship_id]");

    $retval = "BREAK-TURNS";
  }
  else
  {
    $ok=1;
    $sector = $destination;
    $calledfrom = "planet-report.php";
    include("check_fighters.php");
    if($ok>0)
    {
       $stamp = date("Y-m-d H-i-s");
       $update = $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',sector=$destination,ship_energy=ship_energy+$energyscooped,turns=turns-$triptime,turns_used=turns_used+$triptime WHERE ship_id=$playerinfo[ship_id]");
       $l_rs_ready=str_replace("[sector]",$destination,$l_rs_ready);
       $l_rs_ready=str_replace("[triptime]",NUMBER($triptime),$l_rs_ready);
       $l_rs_ready=str_replace("[energy]",NUMBER($energyscooped),$l_rs_ready);
       echo "$l_rs_ready<BR>";
       include("check_mines.php");
    }

    $retval = "GO";
  }

  return($retval);
}

include("footer.php");

?>