<?

include("config.php3");
updatecookie();

$title="Main Menu";

$basefontsize = 0;
$stylefontsize = "8Pt";
$picsperrow = 6;

if($res == 640)
  $picsperrow = 4;

if($res >= 1024)
{
  $basefontsize = 1;
  $stylefontsize = "12Pt";
  $picsperrow = 7;
}

include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships READ, universe READ, links READ, zones READ");

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

$res = mysql_query("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = mysql_fetch_array($res);
mysql_free_result($res);

$res = mysql_query("SELECT * FROM links WHERE link_start='$playerinfo[sector]' ORDER BY link_dest ASC");

//bigtitle();

srand((double)microtime() * 1000000);

if($playerinfo[on_planet] == "Y")
{
  if($sectorinfo[planet] == "Y")
  {
    echo "Click <A HREF=planet.php3>here</A> to go to the planet menu.<BR>"; 
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php3?id=".$playerinfo[ship_id]."\">";
    mysql_query("UNLOCK TABLES");
    //-------------------------------------------------------------------------------------------------
    die();
  }
  else
  {
    mysql_query("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
    echo "<BR>On a non-existent planet???<BR><BR>";
  }
}

$i = 0;
if($res > 0)
{
  while($row = mysql_fetch_array($res))
  {
    $links[$i] = $row[link_dest];
    $i++;
  }
  mysql_free_result($res);
}
$num_links = $i;

$res = mysql_query("SELECT zone_id,zone_name FROM zones WHERE zone_id=$sectorinfo[zone_id]");
$zoneinfo = mysql_fetch_array($res);
mysql_free_result($res);

$shiptypes[0]= "tinyship.gif";
$shiptypes[1]= "smallship.gif";
$shiptypes[2]= "mediumship.gif";
$shiptypes[3]= "largeship.gif";
$shiptypes[4]= "hugeship.gif";

$planettypes[0]= "tinyplanet.gif";
$planettypes[1]= "smallplanet.gif";
$planettypes[2]= "mediumplanet.gif";
$planettypes[3]= "largeplanet.gif";
$planettypes[4]= "hugeplanet.gif";

?>

<table border=2 cellspacing=2 cellpadding=2 bgcolor="#400040" width="75%" align=center>
<tr><td align="center" colspan=3><font color=silver size=<? echo $basefontsize + 2; ?> face="arial">Player <b><font color=white><? echo $playerinfo[character_name];?></font></b>, aboard the <b><font color=white><a href="report.php3"><? echo $playerinfo[ship_name] ?></a></font></b>
</td></tr>
</table>

<table width=75% cellpadding=0 cellspacing=1 border=0 align=center>
<tr><td>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;Turns available: </font><font color=white><b><? echo NUMBER($playerinfo[turns]) ?></b></font>
</td>
<td align=center>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">Turns used: </font><font color=white><b><? echo NUMBER($playerinfo[turns_used]); ?></b></font>
</td>
<td align=right>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">Score: </font><font color=white><b><? echo NUMBER($playerinfo[score])?>&nbsp;</b></font>
</td>
<tr><td>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;Sector: </font><font color=white><b><? echo $playerinfo[sector]; ?></b></font>
</td><td align=center>

<?
if(!empty($sectorinfo[beacon]))
{
  echo "<font color=white size=", $basefontsize + 2," face=\"arial\"><b>", $sectorinfo[beacon], "</b></font>";
}
?>
</td><td align=right>
<a href="<? echo "zoneinfo.php3?zone=$zoneinfo[zone_id]"; ?>"><b><? echo $zoneinfo[zone_name]; ?></b></font></a>&nbsp;
</td></tr>
</table>

<table width=100% border=0 align=center cellpadding=0 cellspacing=0">

<tr>

<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Commands
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href="device.php3">Devices</a>&nbsp;<br>
&nbsp;<a class=mnu href="planet-report.php3">Planets</a>&nbsp;<br>
&nbsp;<a class=mnu href="log.php3">Log</a>&nbsp;<br>
&nbsp;<a class=mnu href="mailto2.php3">Send Message</a>&nbsp;<br>
&nbsp;<a class=mnu href="ranking.php3">Rankings</a>&nbsp;<br>
&nbsp;<a class=mnu href="lastusers.php3">Last Users</a>&nbsp;<br>
&nbsp;<a class=mnu href="self-destruct.php3">Self-Destruct</a>&nbsp;<br>
&nbsp;<a class=mnu href="options.php3">Options</a>&nbsp;<br>
&nbsp;<a class=mnu href="navcomp.php3">Nav Computer</a>&nbsp;<br>
</div>
</td></tr>
<tr><td nowrap>
<div class=mnu>
&nbsp;<a class=mnu href="help.php3">Help</a>&nbsp;<br>
&nbsp;<a class=mnu href="feedback.php3">Feedback</a>&nbsp;<br>
<?
if(!empty($link_forums))
{
    echo "&nbsp;<a class=mnu href=$link_forums TARGET=\'_blank\'>Forums</a>&nbsp;<br>";
}
?>
</div>
</td></tr>
<tr><td nowrap>
&nbsp;<a class=mnu href="logout.php3">Logout</a>&nbsp;<br>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Warp to
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>

<?

if(!$num_links)
{
  echo "&nbsp;<a class=dis>No warp links</a>&nbsp;<br>";
  $link_bnthelper_string="<!--links:N:-->";
}
else
{
  $link_bnthelper_string="<!--links:Y";
  for($i=0; $i<$num_links;$i++)
  {
     echo "&nbsp;<a class=mnu href=move.php3?sector=$links[$i]>=&gt;&nbsp;$links[$i]</a>&nbsp;<a class=dis href=lrscan.php3?sector=$links[$i]>[scan]</a>&nbsp;<br>";
     $link_bnthelper_string=$link_bnthelper_string . ":" . $links[$i];
  }
}
$link_bnthelper_string=$link_bnthelper_string . ":-->";
echo "</div>";
echo "</td></tr>";
echo "<tr><td nowrap align=center>";
echo "<div class=mnu>";
echo "&nbsp;<a class=dis href=lrscan.php3?sector=*>[Full scan]</a>&nbsp;<br>";
?>

</div>
</td></tr>
</table>

</td>

<td valign=top>
&nbsp;<br>

<center><font size=<? echo $basefontsize+2; ?> face="arial" color=white><b>Trading port:&nbsp;

<?
if($sectorinfo[port_type] != "none")
{
  echo "<a href=port.php3>", ucfirst($sectorinfo[port_type]), "</a>";
  $port_bnthelper_string="<!--port:" . $sectorinfo[port_type] . ":" . $sectorinfo[port_ore] . ":" . $sectorinfo[port_organics] . ":" . $sectorinfo[port_goods] . ":" . $sectorinfo[port_energy] . ":-->";
}
else
{
  echo "</b><font size=", $basefontsize+2,">None</font><b>";
  $port_bnthelper_string="<!--port:none:0:0:0:0:-->";
}
?>

</b></font></center>
<br>

<center><b><font size=2 face="arial" color=white>Planets in sector <? echo $sectorinfo[sector_id];?>:</font></b></center>
<table border=0 width=100%>
<tr>
<td align=center valign=top>

<?
if($sectorinfo[planet] == "Y" && $sectorinfo[sector_id] != 0)
{
  if($sectorinfo[planet_owner] != "")
  {
    $result5 = mysql_query("SELECT * FROM ships WHERE ship_id=$sectorinfo[planet_owner]");
    $planet_owner = mysql_fetch_array($result5);

    $planetavg = $planet_owner[hull] + $planet_owner[engines] + $planet_owner[computer] + $planet_owner[beams] + $planet_owner[torp_launchers] + $planet_owner[shields] + $planet_owner[armour];
    $planetavg /= 7;
  
    if($planetavg < 8)
      $planetlevel = 0;
    else if ($planetavg < 12)
      $planetlevel = 1;
    else if ($planetavg < 16)
      $planetlevel = 2;
    else if ($planetavg < 20)
      $planetlevel = 3;
    else
      $planetlevel = 4;
  }
  else
    $planetlevel=0;

  echo "<A HREF=planet.php3>";
  echo "<img src=\"images/$planettypes[$planetlevel]\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
  if(empty($sectorinfo[planet_name]))
  {
    echo "Unnamed";
    $planet_bnthelper_string="<!--planet:Y:Unnamed:";
  }
  else
  {
    echo "$sectorinfo[planet_name]";
    $planet_bnthelper_string="<!--planet:Y:" . $sectorinfo[planet_name] . ":";
  }

  if($sectorinfo[planet_owner] == "")
  {
    echo "<br>(Unowned)";
    $planet_bnthelper_string=$planet_bnthelper_string . "Unowned:-->";
  }
  else
  {
    echo "<br>($planet_owner[character_name])";
    $planet_bnthelper_string=$planet_bnthelper_string . $planet_owner[character_name] . ":-->";
  }
  echo "</font></td>";
}
else
{
  echo "<br><font color=white size=", $basefontsize +2, ">None</font><br><br>";
  $planet_bnthelper_string="<!--planet:N:::-->";
}
?>

</td>
</tr>
</table>

<b><center><font size=2 face="arial" color=white>Other ships in sector <? echo $sectorinfo[sector_id];?>:</font><br></center></b>
<table border=0 width=100%>
<tr>

<?

if($playerinfo[sector] != 0)
{
  $result4 = mysql_query("SELECT * FROM ships WHERE ship_id<>$playerinfo[ship_id] AND sector=$playerinfo[sector] AND on_planet='N' ORDER BY ship_name ASC");
  $totalcount=0;
  
  if($result4 > 0)
  {
    $curcount=0;
    while($row = mysql_fetch_array($result4))
    {
      $success = SCAN_SUCCESS($playerinfo[sensors], $row[cloak]);
      if($success < 5)
      {
        $success = 5;
      }
      if($success > 95)
      {
        $success = 95;
      }
      $roll = rand(1, 100);

      if($roll < $success)
      {
        $shipavg = $row[hull] + $row[engines] + $row[computer] + $row[beams] + $row[torp_launchers] + $row[shields] + $row[armour];
        $shipavg /= 7;

        if($shipavg < 8)
          $shiplevel = 0;
        else if ($shipavg < 12)
          $shiplevel = 1;
        else if ($shipavg < 16)
          $shiplevel = 2;
        else if ($shipavg < 20)
          $shiplevel = 3;
        else
          $shiplevel = 4;

        echo "<td align=center valign=top>";
        echo "<a href=ship.php3?ship_id=$row[ship_id]><img src=\"images/", $shiptypes[$shiplevel],"\" border=0></a><BR><font size=", $basefontsize +1, " color=#ffffff face=\"arial\">$row[ship_name]<br>($row[character_name])</font></td>";
        echo "</td>";
        $totalcount++;
        if($curcount == $picsperrow - 1)
        {
          echo "</tr><tr>";
          $curcount=0;
        }
        else
          $curcount++;
      }
    }
  }
  if($result4 == 0 || $totalcount == 0)
  {
    echo "<td align=center valign=top>";
    echo "<br><font color=white>None</font><br><br>";
    echo "</td>";
  }
}
else
{
    echo "<td align=center valign=top>";
    echo "<br><font color=white>There is so much traffic in Sol (Sector 0) that you cannot even isolate other ships!</font><br><br>";
    echo "</td>";
}
?>

</tr>
</table>

<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Cargo
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<a class=dis>
&nbsp;Ore&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_ore]); ?>&nbsp</div>
&nbsp;Organics&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_organics]); ?>&nbsp</div>
&nbsp;Goods&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_goods]); ?>&nbsp</div>
&nbsp;Energy&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_energy]); ?>&nbsp</div>
&nbsp;Colonists&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_colonists]); ?>&nbsp</div>
&nbsp;Credits&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[credits]); ?>&nbsp</div>
</a>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Trade Routes
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href=traderoute.php3?phase=2&destination=<? echo $playerinfo[preset1] ?>>=&gt;&nbsp;<? echo $playerinfo[preset1] ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=traderoute.php3?phase=2&destination=<? echo $playerinfo[preset2] ?>>=&gt;&nbsp;<? echo $playerinfo[preset2] ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=traderoute.php3?phase=2&destination=<? echo $playerinfo[preset3] ?>>=&gt;&nbsp;<? echo $playerinfo[preset3] ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=traderoute.php3>=&gt;&nbsp;Other</a>&nbsp;<br>
</div>
</a>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Realspace
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="7" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href=rsmove.php3?engage=1&destination=<? echo $playerinfo[preset1]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset1]; ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.php3?engage=1&destination=<? echo $playerinfo[preset2]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset2]; ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.php3?engage=1&destination=<? echo $playerinfo[preset3]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset3]; ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.php3>=&gt;&nbsp;Other</a>&nbsp;<br>
</div>
</a>
</td></tr>
</table>

</td>
</tr>

</table>

<?

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

$player_bnthelper_string="<!--player info:" . $playerinfo[hull] . ":" .  $playerinfo[engines] . ":"  .  $playerinfo[power] . ":" .  $playerinfo[computer] . ":" . $playerinfo[sensors] . ":" .  $playerinfo[beams] . ":" . $playerinfo[torp_launchers] . ":" .  $playerinfo[torps] . ":" . $playerinfo[shields] . ":" .  $playerinfo[armour] . ":" . $playerinfo[armour_pts] . ":" .  $playerinfo[cloak] . ":" . $playerinfo[credits] . ":" .  $playerinfo[sector] . ":" . $playerinfo[ship_ore] . ":" .  $playerinfo[ship_organics] . ":" . $playerinfo[ship_goods] . ":" .  $playerinfo[ship_energy] . ":" . $playerinfo[ship_colonists] . ":" .  $playerinfo[ship_fighters] . ":" . $playerinfo[turns] . ":" .  $playerinfo[on_planet] . ":" . $playerinfo[dev_warpedit] . ":" .  $playerinfo[dev_genesis] . ":" . $playerinfo[dev_beacon] . ":" .  $playerinfo[dev_emerwarp] . ":" . $playerinfo[dev_escapepod] . ":" .  $playerinfo[dev_fuelscoop] . ":" . $playerinfo[dev_minedeflector] . ":-->";
$rspace_bnthelper_string="<!--rspace:" . $sectorinfo[distance] . ":" . $sectorinfo[angle1] . ":" . $sectorinfo[angle2] . ":-->";
echo $player_bnthelper_string;
echo $link_bnthelper_string;
echo $port_bnthelper_string;
echo $planet_bnthelper_string;
echo $rspace_bnthelper_string;

include("footer.php3");

?> 
