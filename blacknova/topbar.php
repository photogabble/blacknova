<?
   connectdb();
   $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
   $playerinfo = $res->fields;
   $res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
   $sectorinfo = $res->fields;
?>   
<table border=1 cellspacing=0 cellpadding=0 bgcolor="#400040" width="75%" align=center>
   <tr>
      <td align="center" colspan=3>
         <font color=silver size=<? echo $basefontsize + 2; ?> face="arial">
            <? echo player_insignia_name($username);?> 
            <font color=white><b><? echo trim($playerinfo[character_name]);?></b></font>
         </font>
         <?php echo $l_abord ?>
         <font color=white><b><a href="report.php"><? echo $playerinfo[ship_name] ?></a></b></font>
      </td>
   </tr>
</table>

<?
   $result = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id='".$playerinfo[ship_id]."' AND notified='N'");
   if ($result->RecordCount() > 0)
   {
?>
   <script language="javascript" type="text/javascript">
   {
      alert('<? echo $l_youhave . $result->RecordCount() . $l_messages_wait;?>');
   }
   </script>
<?
   $db->Execute("UPDATE $dbtables[messages] SET notified='Y' WHERE recp_id='".$playerinfo[ship_id]."'");
   }
?>

<table width="75%" cellpadding=0 cellspacing=1 border=0 align=center>
<tr>
   <td>
      <font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;<? echo $l_turns_have; ?></font><font color=white><b><? echo NUMBER($playerinfo[turns]) ?></b></font>
   </td>
   <td align=center>
      <font color=silver size=<? echo $basefontsize + 2; ?> face="arial"><? echo $l_turns_used ?></font><font color=white><b><? echo NUMBER($playerinfo[turns_used]); ?></b></font>
   </td>
   <td align=right>
      <font color=silver size=<? echo $basefontsize + 2; ?> face="arial"><? echo $l_score?></font><font color=white><b><? echo NUMBER($playerinfo[score])?>&nbsp;</b></font>
   </td>
</tr>
<tr>
   <td>
      <font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;<? echo $l_sector ?>: </font><font color=white><b><? echo $playerinfo[sector]; ?></b></font>
   </td>
   <td align=center>
<?
if(!empty($sectorinfo[beacon]))
{
   echo "<font color=white size=", $basefontsize + 2," face=\"arial\"><b>", $sectorinfo[beacon], "</b></font>";
}
if($zoneinfo[zone_id] < 5)
   $zoneinfo[zone_name] = $l_zname[$zoneinfo[zone_id]];
?>
   </td>
   <td align=right>
      <a href="<? echo "zoneinfo.php?zone=$zoneinfo[zone_id]"; ?>"><b><? echo "<font size=", $basefontsize + 2," face=\"arial\">$zoneinfo[zone_name]</font>"; ?></b></a>&nbsp;
   </td>
</tr>
</table>
