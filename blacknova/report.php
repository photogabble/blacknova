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

$shiptypes[0]= "tinyship.gif";
$shiptypes[1]= "smallship.gif";
$shiptypes[2]= "mediumship.gif";
$shiptypes[3]= "largeship.gif";
$shiptypes[4]= "hugeship.gif";

$shipavg = $shipinfo[hull] + $shipinfo[engines] + $shipinfo[computer] + $shipinfo[beams] + $shipinfo[torp_launchers] + $shipinfo[shields] + $shipinfo[armour];
$shipavg /= 7;
if($shipavg < 8)
   $shiplevel = 0;
elseif($shipavg < 12)
   $shiplevel = 1;
elseif($shipavg < 16)
   $shiplevel = 2;
elseif($shipavg < 20)
   $shiplevel = 3;
else
   $shiplevel = 4;

bigtitle();

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_player: $playerinfo[character_name]</B></TD><TD ALIGN=CENTER><B>$l_ship: $shipinfo[name]</B></TD><TD ALIGN=RIGHT><B>$l_credits: " . NUMBER($playerinfo[credits]) . "</B></TD></TR>";
echo "</TABLE>";
echo "<BR>";

echo "<TABLE BORDER=0 CELLSPACING=5 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR><TD>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_ship_levels</B></TD><TD></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_hull</TD><TD>$l_level $shipinfo[hull]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_engines</TD><TD>$l_level $shipinfo[engines]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_power</TD><TD>$l_level $shipinfo[power]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_computer</TD><TD>$l_level $shipinfo[computer]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_sensors</TD><TD>$l_level $shipinfo[sensors]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_armour</TD><TD>$l_level $shipinfo[armour]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_shields</TD><TD>$l_level $shipinfo[shields]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_beams</TD><TD>$l_level $shipinfo[beams]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_torp_launch</TD><TD>$l_level $shipinfo[torp_launchers]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_cloak</TD><TD>$l_level $shipinfo[cloak]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD><i>$l_shipavg</i></TD><TD>$l_level " . NUMBER($shipavg, 2) . "</TD></TR>";

echo "</TABLE>";
echo "</TD><TD VALIGN=TOP>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
$holds_used = $shipinfo[ore] + $shipinfo[organics] + $shipinfo[goods] + $shipinfo[colonists];
$holds_max = NUM_HOLDS($shipinfo[hull]);
echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_holds</B></TD><TD ALIGN=RIGHT><B>" . NUMBER($holds_used) . " / " . NUMBER($holds_max) . "</B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_ore</TD><TD ALIGN=RIGHT>" . NUMBER($shipinfo[ore]) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_organics</TD><TD ALIGN=RIGHT>" . NUMBER($shipinfo[organics]) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_goods</TD><TD ALIGN=RIGHT>" . NUMBER($shipinfo[goods]) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_colonists</TD><TD ALIGN=RIGHT>" . NUMBER($shipinfo[colonists]) . "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";
$armour_pts_max = NUM_ARMOUR($shipinfo[armour]);
$ship_fighters_max = NUM_FIGHTERS($shipinfo[computer]);
$torps_max = NUM_TORPEDOES($shipinfo[torp_launchers]);
echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_arm_weap</B></TD><TD></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_armourpts</TD><TD ALIGN=RIGHT>" . NUMBER($shipinfo[armour_pts]) . " / " . NUMBER($armour_pts_max) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_fighters</TD><TD ALIGN=RIGHT>" . NUMBER($shipinfo[fighters]) . " / " . NUMBER($ship_fighters_max) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_torps</TD><TD ALIGN=RIGHT>" . NUMBER($shipinfo[torps]) . " / " . NUMBER($torps_max) . "</TD></TR>";
echo "</TABLE>";
echo "</TD><TD VALIGN=TOP>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
$energy_max = NUM_ENERGY($shipinfo[power]);
echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_energy</B></TD><TD ALIGN=RIGHT><B>" . NUMBER($shipinfo[energy]) . " / " . NUMBER($energy_max) . "</B></TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_devices</B></TD><TD></B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_beacons</TD><TD ALIGN=RIGHT>$shipinfo[dev_beacon]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_warpedit</TD><TD ALIGN=RIGHT>$shipinfo[dev_warpedit]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_genesis</TD><TD ALIGN=RIGHT>$shipinfo[dev_genesis]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_deflect</TD><TD ALIGN=RIGHT>$shipinfo[dev_minedeflector]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_ewd</TD><TD ALIGN=RIGHT>$shipinfo[dev_emerwarp]</TD></TR>";
$escape_pod = ($shipinfo[dev_escapepod] == 'Y') ? $l_yes : $l_no;
$fuel_scoop = ($shipinfo[dev_fuelscoop] == 'Y') ? $l_yes : $l_no;
$lssd = ($shipinfo[dev_lssd] == 'Y') ? $l_yes : $l_no;
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_escape_pod</TD><TD ALIGN=RIGHT>$escape_pod</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_fuel_scoop</TD><TD ALIGN=RIGHT>$fuel_scoop</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_lssd</TD><TD ALIGN=RIGHT>$lssd</TD></TR>";
echo "</TABLE>";

echo "</TD></TR>";
echo "</TABLE>";

echo "<p align=center>";
echo "<img src=\"images/$shiptypes[$shiplevel]\" border=0></p>";

TEXT_GOTOMAIN();

include("footer.php");

?>

