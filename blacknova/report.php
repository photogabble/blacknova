<?

include("config.php");

updatecookie();


include("languages/$lang");
$title=$l_report_title;

connectdb();
include("header.php");

if(checklogin())
{
  die();
}

$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo=$result->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ship_types] WHERE type_id=$shipinfo[class]");
$classinfo = $res->fields;

$holds_used = $shipinfo[ore] + $shipinfo[organics] + $shipinfo[goods] + $shipinfo[colonists];
$holds_max = NUM_HOLDS($shipinfo[hull]);

$armour_pts_max = NUM_ARMOUR($shipinfo[armour]);
$ship_fighters_max = NUM_FIGHTERS($shipinfo[computer]);
$torps_max = NUM_TORPEDOES($shipinfo[torp_launchers]);
$energy_max = NUM_ENERGY($shipinfo[power]);

$hull_bars = MakeBars($shipinfo[hull], $classinfo[maxhull]);
$engines_bars = MakeBars($shipinfo[engines], $classinfo[maxengines]);
$power_bars = MakeBars($shipinfo[power], $classinfo[maxpower]);
$computer_bars = MakeBars($shipinfo[computer], $classinfo[maxcomputer]);
$sensors_bars = MakeBars($shipinfo[sensors], $classinfo[maxsensors]);
$armour_bars = MakeBars($shipinfo[armour], $classinfo[maxarmour]);
$shields_bars = MakeBars($shipinfo[shields], $classinfo[maxshields]);
$beams_bars = MakeBars($shipinfo[beams], $classinfo[maxbeams]);
$torp_launchers_bars = MakeBars($shipinfo[torp_launchers], $classinfo[maxtorp_launchers]);
$cloak_bars = MakeBars($shipinfo[cloak], $classinfo[maxcloak]);

echo "

<center>

<table border=0 cellspacing=0 cellpadding=0 width=90%>
  <tr>
    <td width=50%>
    <font size=5 color=white><b>$shipinfo[name]<br>
    <font size=3>
    $classinfo[name]</b></font></font><p>
    <font size=2><b>
    $classinfo[description]
    <br><br>
    </b></font>
    </td>
    <td width=50% align=center valign=center>
    <img src=/images/$classinfo[image]></td>
  </tr>
</table>

<table border=0 cellspacing=0 cellpadding=0 width=90%>
  <tr>
    <td width=50%>
    <font size=3 color=white><b>$l_ship_levels</b></font>
    <br><br>
    </td>
  </tr>

  <tr>
    <td>

    <table border=0 cellspacing=0 cellpadding=3>
    <tr>
      <td>
      <font size=2><b>
      $l_hull&nbsp;&nbsp;&nbsp;
      </b></font>
      <td valign=bottom>
      $hull_bars
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_engines&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $engines_bars
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_power&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $power_bars
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_computer&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $computer_bars
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_sensors&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $sensors_bars
      </td>
    </tr>

  </table>

  </td>

  <td width=50%>

  <table border=0 cellspacing=0 cellpadding=3>
    <tr>
      <td>
      <font size=2><b>
      $l_armour&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $armour_bars;
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_shields&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $shields_bars
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_beams&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $beams_bars
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_torp_launch&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $torp_launchers_bars
      </td>
    </tr>

    <tr>
      <td>
      <font size=2><b>
      $l_cloak&nbsp;&nbsp;&nbsp;
      </b></font><td valign=bottom>
      $cloak_bars
      </td>
    </tr>

  </table>

  </td>
</tr>
</table>

<p>

<table border=0 cellspacing=0 cellpadding=0 width=90%>
  <tr>
    <td width=33%>
    <font size=3 color=white><b>$l_holds</b></font>
    <br><br>
    </td>
    <td width=33%>
    <font size=3 color=white><b>$l_arm_weap</b></font>
    <br><br></td>
    </td>
    <td width=33%>
    <font size=3 color=white><b>$l_devices</b></font>
    <br><br></td>
    </td>
  </tr>

  <tr>
    <td valign=top>

    <table border=0 cellspacing=0 cellpadding=3>
      <tr>
        <td>
        <font size=2><b>
        $l_total_cargo&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $holds_used / $holds_max
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/ore.gif>&nbsp;$l_ore&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[ore]
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/organics.gif>&nbsp;$l_organics&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[organics]
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/goods.gif>&nbsp;$l_goods&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[goods]
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/colonists.gif>&nbsp;$l_colonists&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[colonists]
        </b></font>
        </td>
      </tr>

    </table>

  </td><td valign=top>

    <table border=0 cellspacing=0 cellpadding=3>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/energy.gif>&nbsp;$l_energy&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[energy] / $energy_max
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/tfighter.gif>&nbsp;$l_fighters&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[fighters] / $ship_fighters_max
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/torp.gif>&nbsp;$l_torps&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[torps] / $torps_max
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        &nbsp;<img src=images/armour.gif>&nbsp;$l_armourpts&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[armour_pts] / $armour_pts_max
        </b></font>
        </td>
      </tr>

    </table>

  <td valign=top>

    <table border=0 cellspacing=0 cellpadding=3>

      <tr>
        <td>
        <font size=2><b>
        $l_beacons&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[dev_beacon]
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        $l_warpedit&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[dev_warpedit]
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        $l_genesis&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[dev_genesis]
        </b></font>
        </td>
      </tr>

      <tr>
        <td>
        <font size=2><b>
        $l_deflect&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=white><b>
        $shipinfo[dev_minedeflector]
        </b></font>
        </td>
      </tr>
";

if($shipinfo[dev_escapepod] == 'Y')
  echo "
      <tr>
        <td>
        <font size=2><b>
        $l_escape_pod&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=#00FF00><b>
        $l_installed
        </b></font>
        </td>
      </tr>
  ";

if($shipinfo[dev_fuelscoop] == 'Y')
  echo "
      <tr>
        <td>
        <font size=2><b>
        $l_fuel_scoop&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=#00FF00><b>
        $l_installed
        </b></font>
        </td>
      </tr>
  ";

if($shipinfo[dev_lssd] == 'Y')
  echo "
      <tr>
        <td>
        <font size=2><b>
        $l_lssd&nbsp;&nbsp;&nbsp;
        </b></font>
        <td>
        <font color=#00FF00><b>
        $l_installed
        </b></font>
        </td>
      </tr>
  ";

echo "

    </table>

  </td></tr>

</table>
<p>

</center>
";

function MakeBars($level, $max)
{
  global $l_n_a;
  
  $diff = $max - $level;
  $img = "";

  for ($i=0;$i<$level;$i++)
  {
    $bright = floor($i / 5) + 1;
    if($bright > 5)
      $bright = 5;
    $img .= "<img src=/images/dialon$bright.gif>&nbsp;";
  }

  for ($i=0;$i<$diff;$i++)
  {
    $img .= "<img src=/images/dialoff.gif>&nbsp;";
  }

  if($img == "")
    $img = "<font size=2><b>$l_n_a</b></font>";

  return $img;
}

TEXT_GOTOMAIN();

include("footer.php");

?>

