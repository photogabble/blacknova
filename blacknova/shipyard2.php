<?
include("config.php");
updatecookie();

include("languages/$lang");
$title="Buying a new ship";
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

bigtitle();

$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $result->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ship_types] WHERE buyable = 'Y'");
while(!$res->EOF)
{
  $ships[] = $res->fields;
  $res->MoveNext();
}

if(isset($stype))
{
  $lastship = end($ships);
  if($stype < 1 || $stype > $lastship[type_id])
    shipyard_die("Wrong ship class specified");
}
else
{
  shipyard_die("Wrong ship class specified");
}

foreach($ships as $testship)
{
  if($testship[type_id] == $stype)
  {
    $sship = $testship;
    break;
  }
}

if(!isset($confirm)) //display info only
{
  $calc_hull = round(mypw($upgrade_factor,$shipinfo[hull]));
  $calc_engines = round(mypw($upgrade_factor,$shipinfo[engines]));
  $calc_power = round(mypw($upgrade_factor,$shipinfo[power]));
  $calc_computer = round(mypw($upgrade_factor,$shipinfo[computer]));
  $calc_sensors = round(mypw($upgrade_factor,$shipinfo[sensors]));
  $calc_beams = round(mypw($upgrade_factor,$shipinfo[beams]));
  $calc_torp_launchers = round(mypw($upgrade_factor,$shipinfo[torp_launchers]));
  $calc_shields = round(mypw($upgrade_factor,$shipinfo[shields]));
  $calc_armour = round(mypw($upgrade_factor,$shipinfo[armour]));
  $calc_cloak = round(mypw($upgrade_factor,$shipinfo[cloak]));
  $shipvalue = ($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak) * $upgrade_cost;
  $shipvalue /= 2;

  $totalcost = $sship[cost_credits] - $shipvalue;

  echo "
    <font size=4 color=white><b>You are buying:</b></font><p>
    <table border=0 cellpadding=5>
    <tr><td align=center><font color=white size=4><b>$sship[name]</b><br><img src=images/$sship[image]></font></td>
    <td><font size=2><b>$sship[description]</b></font>
    </table>
    <p>
    <table border=0>
    <tr><td>
    <font size=4>Current Ship Value:&nbsp;&nbsp;&nbsp;</font></td>
    <td align=right><font size=4 color=#00FF00><b>" . NUMBER($shipvalue) . "</b></font></td></tr>
    <tr><td>
    <font size=4>New Ship Value:&nbsp;&nbsp;&nbsp;</font></td>
    <td align=right><font size=4 color=#FF0000><b>" . NUMBER($sship[cost_credits]) . "</b></font></td></tr>
    <tr><td><td><hr></td></tr>
    <tr><td>
    <font size=4>Total Cost:&nbsp;&nbsp;&nbsp;</font></td>
    <td align=right><font size=4 color=#FF0000><b>" . NUMBER($totalcost) . "</b></font></td></tr></table>
    <p>
  ";

  if($totalcost > $playerinfo[credits])
  {
    echo "<br><font size=3 color=white><b>&nbsp;You do not have enough credits to buy this ship.</b></font><p><br>";
  }
  else
  {
    echo "<form action=shipyard2.php method=POST>" .
         "<input type=hidden name=stype value=$stype>" .
         "<input type=hidden name=confirm value=yes>" .
         "<font size=3><b>Name your new ship:&nbsp;&nbsp;&nbsp;</b></font><input type=text name=shipname size=20 maxlength=20 value=\"\"><p>" .
         "<input type=submit value=\"Purchase\">".
         "</form><p>";
  }
}
else //ok, now we buy the ship for true
{
}

function shipyard_die($error_msg)
{
  global $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $l_footer_one_player_on, $sched_ticks;
  echo "<p>$error_msg<p>";

  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}


TEXT_GOTOMAIN();

include("footer.php");

?>