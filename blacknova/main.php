<?
include("config.php");
include("languages/$lang");

updatecookie();

$basefontsize = 0;
$stylefontsize = "8Pt";
$picsperrow = 5;

if($screenres == 640)
   $picsperrow = 3;

if($screenres >= 1024)
{
   $basefontsize = 1;
   $stylefontsize = "12Pt";
   $picsperrow = 7;
}

$title=$l_main_title;
include("header.php");
include("topbar.php");

srand((double)microtime() * 1000000);
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

if($sched_type==1)
{ 
   $lastrun = time(); 
   $res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] LIMIT 1"); 
   $result = $res->fields;
   $sched_counter = floor((TIME()-$result[last_run])/ ($sched_ticks*60)); 
   if ($sched_counter < 0)
      $sched_counter = 0; 
   if ($sched_counter > 5)
      $sched_counter = 5; 
   if ($sched_counter > 0) 
   { 
      $secs = $sched_counter * $sched_ticks * 60; 
      $db->Execute("UPDATE $dbtables[scheduler] SET last_run=last_run+$secs"); 
      $sched_temp=$swordfish; 
      $swordfish=$adminpass; 
      for (; $sched_counter > 0; $sched_counter--) 
      { 
         include("scheduler.php"); 
      } 
      $swordfish=$sched_temp; 
      unset($sched_temp); 
   }
} 

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;
if($playerinfo['cleared_defences'] > ' ')
{
   echo "$l_incompletemove <BR>";
   echo "<a href=$playerinfo[cleared_defences]>$l_clicktocontinue</a>";
   die();
}
if($playerinfo[on_planet] == "Y")
{
   $res2 = $db->Execute("SELECT planet_id, owner FROM $dbtables[planets] WHERE planet_id=$playerinfo[planet_id]");
   if($res2->RecordCount() != 0)
   {
      echo "<A HREF=planet.php?planet_id=$playerinfo[planet_id]>$l_clickme</A> $l_toplanetmenu    <BR>";
      echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php?planet_id=$playerinfo[planet_id]&id=".$playerinfo[ship_id]."\">";
      die();
   }
   else
   {
      $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
      echo "<BR>$l_nonexistant_pl<BR><BR>";
   }
}

//-------------------------------------------------------------------------------------------------

$res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = $res->fields;

$res = $db->Execute("SELECT zone_id,zone_name FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
$zoneinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$playerinfo[sector]' ORDER BY link_dest ASC");
if($res > 0)
{
  while(!$res->EOF)
  {
    $links[] = $res->fields[link_dest];
    $res->MoveNext();
  }
}
$num_links = count($links);

$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$playerinfo[sector]'");
if($res > 0)
{
  while(!$res->EOF)
  {
    $planets[] = $res->fields;
    $res->MoveNext();
  }
}
$num_planets = count($planets);

$res = $db->Execute("SELECT * FROM $dbtables[sector_defence],$dbtables[ships] WHERE $dbtables[sector_defence].sector_id='$playerinfo[sector]' AND $dbtables[ships].ship_id = $dbtables[sector_defence].ship_id ");
if($res > 0)
{
  while(!$res->EOF)
  {
    $defences[] = $res->fields;
    $res->MoveNext();
  }
}
$num_defences = count($defences);

//-------------------------------------------------------------------------------------------------

function tabletop($tbltitle)
{
?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
<tr valign="top">
   <td>
      <table border="0" cellpadding="0" cellspacing="0">
      <tr>
         <td><img src="images/lcorner.gif" width="8" height="11" border="0" alt=""></td>
      </tr>
      <tr>
         <td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td>
      </tr>
      </table>
   </td>
   <td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b><? echo $tbltitle ?></b></font></td>
   <td align="right">
      <table border="0" cellpadding="0" cellspacing="0">
      <tr>
         <td><img src="images/rcorner.gif" width="8" height="11" border="0" alt=""></td>
      </tr>
      <tr>
         <td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td>
      </tr>
      </table>
   </td>
</tr>
</table>
<?
}

function tableentry($file, $text, $enabled = true)
{
   if (!$enabled) { return; }
   echo "&nbsp;<a class=mnu href=\"$file\">$text</a>&nbsp;<br>";
}

?>
<table width="100%" border=0 align=center cellpadding=0 cellspacing=0>
<tr>
   <td valign=top>

<? tabletop($l_commands); ?>
<TABLE BORDER=1 CELLSPACING=0 CELLPADDING=0 BGCOLOR="#500050" align="center">
<TR>
   <TD NOWRAP>
<div class=mnu>
<?
tableentry("device.php", $l_devices);
tableentry("planet-report.php", $l_planets);
tableentry("log.php", $l_log);
tableentry("defence-report.php", $l_sector_def);
tableentry("readmail.php", $l_read_msg);
tableentry("mailto2.php", $l_send_msg);
tableentry("ranking.php", $l_rankings);
tableentry("settings.php", $l_login_settings);
tableentry("teams.php", $l_teams);
tableentry("self-destruct.php", $l_ohno);
tableentry("options.php", $l_options);
tableentry("navcomp.php", $l_navcomp);
tableentry("galaxy2.php", $l_map, $ksm_allowed);
?>
</div>
   </td>
</tr>
<tr>
   <td nowrap>
<div class=mnu>
<?
tableentry("help.php", $l_help, false);
tableentry("faq.php", $l_faq);
tableentry("feedback.php", $l_feedback);
tableentry("link_forums.php", $l_forums, !empty($link_forums));
?>
</div>
   </td>
</tr>
<tr>
   <td nowrap>
<?
tableentry("logout.php", $l_logout);
?>
   </td>
</tr>
</table>

<br>

<? tabletop($l_main_warpto); ?>
<TABLE BORDER=1 CELLSPACING=0 CELLPADDING=0 BGCOLOR="#500050" align="center">
<TR>
   <TD NOWRAP>
<div class=mnu>
<?
if(!$num_links)
{
  echo "&nbsp;<a class=dis>$l_no_warplink</a>&nbsp;<br>";
  $link_bnthelper_string = "<!--links:N";
}
else
{
  $link_bnthelper_string = "<!--links:Y";
  for($i=0; $i<$num_links;$i++)
  {
     echo "&nbsp;<a class=\"mnu\" href=\"move.php?sector=$links[$i]\">=&gt;&nbsp;$links[$i]</a>&nbsp;<a class=dis href=\"lrscan.php?sector=$links[$i]\">[$l_scan]</a>&nbsp;<br>";
     $link_bnthelper_string=$link_bnthelper_string . ":" . $links[$i];
  }
}
$link_bnthelper_string = $link_bnthelper_string . ":-->";
?>
</div>
   </td>
</tr>
<tr>
   <td nowrap align=center>
   <div class=mnu>
   &nbsp;<a class=dis href=\"lrscan.php?sector=*\">[<? echo $l_fullscan ?>]</a>&nbsp;<br>
   </div>
   </td>
</tr>
</table>
<?
//----------------------------------------------------------------------------------------------
echo "</TD><TD valign=top>";
// BEGINING OF THE MAIN BOX
//----------------------------------------------------------------------------------------------

$fontsize = $basefontsize+2;

//********************************
//  PORT
//********************************
echo "<br>\n";
echo "<center>\n";
echo "<font size=$fontsize face=\"arial\" color=white><b>$l_tradingport:&nbsp;\n";
if($sectorinfo[port_type] != "none")
{
   echo "<a href=port.php>", ucfirst(t_port($sectorinfo[port_type])), "</a>\n";
   $port_bnthelper_string = "<!--port:" . $sectorinfo[port_type] . ":" . $sectorinfo[port_ore] . ":" . $sectorinfo[port_organics] . ":" . $sectorinfo[port_goods] . ":" . $sectorinfo[port_energy] . ":-->";
}
else
{
   echo "</b><font size=$fontsize>$l_none</font><b>\n";
   $port_bnthelper_string = "<!--port:none:0:0:0:0:-->";
}
echo "</b></font>\n";
echo "<br>\n";
//********************************
//  PLANET
//********************************
echo "<br>\n";
echo "<b><font size=$fontsize face=\"arial\" color=white>$l_planet_in_sec$sectorinfo[sector_id]:</font></b>\n";
if($num_planets > 0)
{
   echo "<table border=0 width=\"100%\">\n";
   echo "<tr>\n";
   
   $totalcount=0;
   $curcount=0;
   for($i=0; $i < $num_planets; $i++)
   {
      if($planets[$i][owner] != 0)
      {
         $result5 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=" . $planets[$i][owner]);
         $planet_owner = $result5->fields;
         
         $planetavg = ($planet_owner[hull] + $planet_owner[engines] + $planet_owner[computer] + $planet_owner[beams] + $planet_owner[torp_launchers] + $planet_owner[shields] + $planet_owner[armour])/7;         
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
      {
         $planetlevel = 0;
      }

      echo "  <TD align=center valign=top>\n";
      echo "      <A HREF=planet.php?planet_id=".$planets[$i][planet_id].">\n";
      echo "      <img src=\"images/".$planettypes[$planetlevel]."\" border=0>\n";
      echo "      </A><BR>\n";
      echo "      <font size=\"$basefontsize+1\" color=#ffffff face=\"arial\">";
      
      if(empty($planets[$i][name]))
      {
         echo $l_unnamed."<BR>";
         $planet_bnthelper_string="<!--planet:Y:Unnamed:";
      }
      else
      {
         echo $planets[$i][name]."<BR>";
         $planet_bnthelper_string="<!--planet:Y:" . $planets[$i][name] . ":";
      }
      if($planets[$i][owner] == 0)
      {
         echo "($l_unowned)";
         $planet_bnthelper_string=$planet_bnthelper_string . "Unowned:-->";
      }
      else
      {
         echo "($planet_owner[character_name])";
         $planet_bnthelper_string=$planet_bnthelper_string . $planet_owner[character_name] . ":N:-->";
      }
      echo "      </font>";
      echo "   </TD>";

      $totalcount++;
      if($curcount == $picsperrow - 1)
      {
         echo "</tr>\n<tr>";
         $curcount=0;
      }
      else
      {
      $curcount++;
      }
   }
   echo "   </td>\n";
   echo "</tr>\n";
   echo "</table>\n";
}
else
{
  echo " <br><font color=white size=\"fontsize\">$l_none</font><br><br>";
  $planet_bnthelper_string="<!--planet:N:::-->";
}

echo "<center><b><font size=$fontsize face=\"arial\" color=white>$l_ships_in_sec $sectorinfo[sector_id]:</font></b></center><br>\n";
<table border=0 width="100%">
<tr>

<?

if($playerinfo[sector] != 0)
{
  $result4 = $db->Execute(" SELECT
                              $dbtables[ships].*,
                              $dbtables[teams].team_name,
                              $dbtables[teams].id
                           FROM $dbtables[ships]
                              LEFT OUTER JOIN $dbtables[teams]
                              ON $dbtables[ships].team = $dbtables[teams].id
                           WHERE $dbtables[ships].ship_id<>$playerinfo[ship_id]
                           AND $dbtables[ships].sector=$playerinfo[sector]
                           AND $dbtables[ships].on_planet='N'");
   $totalcount=0;

   if($result4 > 0)
   {
      $curcount=0;
      echo "<td align=center colspan=99 valign=top>
      <table width=\"100%\" border=0>
         <tr>";
      while(!$result4->EOF)
      {
         $row=$result4->fields;
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

            if ($row[team_name]) {
               echo "<a href=ship.php?ship_id=$row[ship_id]><img src=\"images/", $shiptypes[$shiplevel],"\" border=0></a><BR><font size=", $basefontsize +1, " color=#ffffff face=\"arial\">$row[ship_name]<br>($row[character_name])&nbsp;(<font color=#33ff00>$row[team_name]</font>)</font>";
            }
            else
            {
               echo "<a href=ship.php?ship_id=$row[ship_id]><img src=\"images/", $shiptypes[$shiplevel],"\" border=0></a><BR><font size=", $basefontsize +1, " color=#ffffff face=\"arial\">$row[ship_name]<br>($row[character_name])</font>";
            }

            echo "</td>";

            $totalcount++;

            if($curcount == $picsperrow - 1)
            {
               echo "</tr><tr>";
               $curcount=0;
            }
            else
            {
               $curcount++;
            }
         }
         if($result4 == 0 || $totalcount == 0)
         {
            echo "<td align=center>";
            echo "<br><font size=2 color=white>$l_none</font><br><br>";
            echo "</td>";
            $displayed=true;
            break;
         }
         $result4->MoveNext();
      }
   echo "    </tr>
           </table>
         </td>";
}
   if($result4 == 0 || $totalcount == 0 && $displayed != true)
   {
      echo "<tr><td align=center>";
      echo "<br><font size=2 color=white>$l_none</font><br><br>";
      echo "</td></tr>";
   }
}
else
{
    echo "<td align=center valign=top>";
    echo "<br><font size=2 color=white>$l_sector_0</font><br><br>";
}
?>
</td>
</tr>
</table>
<?
if($num_defences>0) echo "<b><center><font face=\"arial\" color=white>$l_sector_def</font><br></center></b>";
?>
<table border=0 width="100%">
<tr>
<?
if($num_defences > 0)
{
  $totalcount=0;
  $curcount=0;
  $i=0;
  while($i < $num_defences)
  {

    $defence_id = $defences[$i]['defence_id'];
    echo "<td align=center valign=top>";
    if($defences[$i]['defence_type'] == 'F')
    {
       echo "<a href=modify-defences.php?defence_id=$defence_id><img src=\"images/fighters.gif\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
       $def_type = $l_fighters;
       $mode = $defences[$i]['fm_setting'];
       if($mode == 'attack')
         $mode = $l_md_attack;
       else
        $mode = $l_md_toll;
       $def_type .= $mode;
    }
    elseif($defences[$i]['defence_type'] == 'M')
    {
       echo "<a href=modify-defences.php?defence_id=$defence_id><img src=\"images/mines.gif\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
       $def_type = $l_mines;
    }
    $char_name = $defences[$i]['character_name'];
    $qty = $defences[$i]['quantity'];
    echo "$char_name ( $qty $def_type )";
    echo "</font></td>";

    $totalcount++;
    if($curcount == $picsperrow - 1)
    {
      echo "</tr><tr>";
      $curcount=0;
    }
    else
      $curcount++;
    $i++;
  }
  echo "</td></tr></table>";
}
else
{
  echo "<td align=center valign=top>";
//  echo "<br><font color=white size=", $basefontsize +2, ">None</font><br><br>";
  echo "</td></tr></table>";
}
?>
<br>

<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0" alt=""></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<? echo $l_cargo ?>
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0" alt=""></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td></tr>
</table></td>
</tr></table>

<table BORDER=1 CELLPADDING=0 CELLSPACING=0 BGCOLOR="#500050" align="center"> 
<tr><td>
<table BORDER=0 CELLPADDING=1 CELLSPACING=0 BGCOLOR="#500050" align="center" class=dis> 
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<? echo $l_ore ?>" src="images/ore.gif">&nbsp;<? echo $l_ore ?>&nbsp;</td></tr> 
 <tr><td nowrap align='right'><span class=mnu>&nbsp;<? echo NUMBER($playerinfo[ship_ore]); ?>&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<? echo $l_organics ?>" src="images/organics.gif">&nbsp;<? echo $l_organics ?>&nbsp;</td></tr> 
 <tr><td nowrap align='right'><span class=mnu>&nbsp;<? echo NUMBER($playerinfo[ship_organics]); ?>&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<? echo $l_goods ?>" src="images/goods.gif">&nbsp;<? echo $l_goods ?>&nbsp;</td></tr> 
 <tr><td nowrap align='right'><span class=mnu>&nbsp;<? echo NUMBER($playerinfo[ship_goods]); ?>&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<? echo $l_energy ?>" src="images/energy.gif">&nbsp;<? echo $l_energy ?>&nbsp;</td></tr> 
 <tr><td nowrap align='right'><span class=mnu>&nbsp;<? echo NUMBER($playerinfo[ship_energy]); ?>&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<? echo $l_colonists ?>" src="images/colonists.gif">&nbsp;<? echo $l_colonists ?>&nbsp;</td></tr> 
 <tr><td nowrap align='right'><span class=mnu>&nbsp;<? echo NUMBER($playerinfo[ship_colonists]); ?>&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<? echo $l_credits ?>" src="images/credits.gif">&nbsp;<? echo $l_credits ?>&nbsp;</td></tr> 
 <tr><td nowrap align='right'><span class=mnu>&nbsp;<? echo NUMBER($playerinfo[credits]); ?>&nbsp;</span></td></tr>
</table>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0" alt=""></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<? echo $l_traderoutes ?>
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0" alt=""></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>

<?

  $i=0;
  $num_traderoutes = 0;

/********* Port querry ************************************ begin *********/
  $query = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE source_type='P' AND source_id=$playerinfo[sector] AND owner=$playerinfo[ship_id] ORDER BY dest_id ASC");
  while(!$query->EOF)
  {
    $traderoutes[$i]=$query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
  }
/********* Port querry ************************************ end **********/

/********* Sector Defense Trade route query *************** begin ********/
/********* this is still under developement ***/
  $query = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE source_type='D' AND source_id=$playerinfo[sector] AND owner=$playerinfo[ship_id] ORDER BY dest_id ASC");
  while(!$query->EOF)
  {
    $traderoutes[$i]=$query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
  }
/********* Defense querry ********************************* end **********/
/********* Personal planet traderoute type query ********** begin ********/
  $query = $db->Execute("SELECT * FROM $dbtables[planets], $dbtables[traderoutes] WHERE source_type='L' AND source_id=$dbtables[planets].planet_id AND $dbtables[planets].sector_id=$playerinfo[sector] AND $dbtables[traderoutes].owner=$playerinfo[ship_id]");
  while(!$query->EOF)
  {
    $traderoutes[$i]=$query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
  }
/********* Personal planet traderoute type query ********* end **********/
/********* Corperate planet traderoute type query ******** begin ********/
  $query = $db->Execute("SELECT * FROM $dbtables[planets], $dbtables[traderoutes] WHERE source_type='C' AND source_id=$dbtables[planets].planet_id AND $dbtables[planets].sector_id=$playerinfo[sector] AND $dbtables[traderoutes].owner=$playerinfo[ship_id]");
  while(!$query->EOF)
  {
    $traderoutes[$i]=$query->fields;
    $i++;
    $num_traderoutes++;
    $query->MoveNext();
  }
/********* Corperate planet traderoute type query ******** end **********/

  if($num_traderoutes == 0)
    echo "<center><a class=dis>&nbsp;$l_none &nbsp;</a></center>";
  else
  {
    $i=0;
    while($i<$num_traderoutes)
    {
      echo "&nbsp;<a class=mnu href=traderoute.php?engage=" . $traderoutes[$i][traderoute_id] . ">";
      if($traderoutes[$i][source_type] == 'P')
        echo "$l_port&nbsp;";
      elseif($traderoutes[$i][source_type] == 'D')
        echo "Def's ";
      else
      {
        $query = $db->Execute("SELECT name FROM $dbtables[planets] WHERE planet_id=" . $traderoutes[$i][source_id]);
        if(!$query || $query->RecordCount() == 0)
          echo $l_unknown;
        else
        {
          $planet = $query->fields;
          if($planet[name] == "")
            echo "$l_unnamed ";
          else
            echo "$planet[name] ";
        }
      }

      if($traderoutes[$i][circuit] == '1')
        echo "=&gt;&nbsp;";
      else
        echo "&lt;=&gt;&nbsp;";

      if($traderoutes[$i][dest_type] == 'P')
        echo $traderoutes[$i][dest_id];
      elseif($traderoutes[$i][dest_type] == 'D')
        echo "Def's in " .  $traderoutes[$i][dest_id] . "";
      else
      {
        $query = $db->Execute("SELECT name FROM $dbtables[planets] WHERE planet_id=" . $traderoutes[$i][dest_id]);
        if(!$query || $query->RecordCount() == 0)
          echo $l_unknown;
        else
        {
          $planet = $query->fields;
          if($planet[name] == "")
            echo $l_unnamed;
          else
            echo $planet[name];
        }
      }
      echo "</a>&nbsp;<br>";
      $i++;
    }
  }
  $previous_sector = $sectorinfo[sector_id] - 1; 
  $next_sector = $sectorinfo[sector_id] + 1; 
  if($previous_sector <0) $previous_sector = $sector_max; 
  if($next_sector > $sector_max) $next_sector = 0; 

?>

</div>
</td></tr>
<tr><td nowrap>
<div class=mnu>
&nbsp;<a class=mnu href=traderoute.php><? echo $l_trade_control ?></a>&nbsp;<br>
</div>
</table>

<br>
<FORM ACTION=rsmove.php METHOD=POST>
<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0" alt=""></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<? echo $l_realspace ?>
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0" alt=""></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0" alt=""></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=1 CELLPADDING=1 CELLSPACING=0 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href="rsmove.php?engage=1&amp;destination=<? echo $previous_sector; ?>">&nbsp;<? echo "&lt&lt"; ?></a>&nbsp;&nbsp;<a class=mnu href="rsmove.php?engage=1&amp;destination=<? echo $next_sector; ?>">&nbsp;<? echo "&gt&gt"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="rsmove.php?engage=1&amp;destination=<? echo $playerinfo[preset1]; ?>">=&gt;&nbsp;<? echo $playerinfo[preset1]; ?></a>&nbsp;<a class=dis href=preset.php>[<? echo $l_set?>]</a>&nbsp;<br>
&nbsp;<a class=mnu href="rsmove.php?engage=1&amp;destination=<? echo $playerinfo[preset2]; ?>">=&gt;&nbsp;<? echo $playerinfo[preset2]; ?></a>&nbsp;<a class=dis href=preset.php>[<? echo $l_set?>]</a>&nbsp;<br>
&nbsp;<a class=mnu href="rsmove.php?engage=1&amp;destination=<? echo $playerinfo[preset3]; ?>">=&gt;&nbsp;<? echo $playerinfo[preset3]; ?></a>&nbsp;<a class=dis href=preset.php>[<? echo $l_set?>]</a>&nbsp;<br>
Direct:<BR><INPUT TYPE=TEXT NAME=destination SIZE=10 MAXLENGTH=10><BR><INPUT TYPE=SUBMIT VALUE=<? echo $l_rs_submit; ?>>
</div>
</td></tr>
</table>

</td>
</tr>

</table>
</FORM>

<?


//-------------------------------------------------------------------------------------------------

if ($allowbnthelper)
{
   $player_bnthelper_string="<!--player info:" . $playerinfo[hull] . ":" .  $playerinfo[engines] . ":"  .  $playerinfo[power] . ":" .  $playerinfo[computer] . ":" . $playerinfo[sensors] . ":" .  $playerinfo[beams] . ":" . $playerinfo[torp_launchers] . ":" .  $playerinfo[torps] . ":" . $playerinfo[shields] . ":" .  $playerinfo[armour] . ":" . $playerinfo[armour_pts] . ":" .  $playerinfo[cloak] . ":" . $playerinfo[credits] . ":" .  $playerinfo[sector] . ":" . $playerinfo[ship_ore] . ":" .  $playerinfo[ship_organics] . ":" . $playerinfo[ship_goods] . ":" .  $playerinfo[ship_energy] . ":" . $playerinfo[ship_colonists] . ":" .  $playerinfo[ship_fighters] . ":" . $playerinfo[turns] . ":" .  $playerinfo[on_planet] . ":" . $playerinfo[dev_warpedit] . ":" .  $playerinfo[dev_genesis] . ":" . $playerinfo[dev_beacon] . ":" .  $playerinfo[dev_emerwarp] . ":" . $playerinfo[dev_escapepod] . ":" .  $playerinfo[dev_fuelscoop] . ":" . $playerinfo[dev_minedeflector] . ":-->";
   $rspace_bnthelper_string="<!--rspace:" . $sectorinfo[distance] . ":" . $sectorinfo[angle1] . ":" . $sectorinfo[angle2] . ":-->";
   echo $player_bnthelper_string;
   echo $link_bnthelper_string;
   echo $port_bnthelper_string;
   echo $planet_bnthelper_string;
   echo $rspace_bnthelper_string;
   echo "\n";
}
?>
<TABLE WIDTH="500" ALIGN=CENTER BORDER=1 CELLSPACING=0 CELLPADDING=1 BGCOLOR="black">
<TR>

<TD ID=IEfad1 align="center" width="490" class="faderlines">
</TD>

</TR>
</TABLE>

<?
include("fader.php");
include("footer.php");
?>