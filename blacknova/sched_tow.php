<?

  if (preg_match("/sched_tow.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  echo "<B>ZONES</B><BR><BR>";
  echo "Towing bigger players out of restricted zones...";
  $num_to_tow = 0;
  do
  {
    $res = $db->Execute("SELECT ship_id,character_name,hull,sector,$dbtables[universe].zone_id,max_hull FROM $dbtables[players],$dbtables[universe],$dbtables[zones] WHERE sector=sector_id AND $dbtables[universe].zone_id=$dbtables[zones].zone_id AND max_hull<>0 AND (($dbtables[players].hull + $dbtables[players].engines + $dbtables[players].computer + $dbtables[players].beams + $dbtables[players].torp_launchers + $dbtables[players].shields + $dbtables[players].armour)/7) >max_hull AND ship_destroyed='N'");
    if($res)
    {
      $num_to_tow = $res->RecordCount();
      echo "<BR>$num_to_tow players to tow:<BR>";
      while(!$res->EOF)
      {
        $row = $res->fields;
        echo "...towing $row[character_name] out of $row[sector] ...";
        $newsector = rand(0, $sector_max);
        echo " to sector $newsector.<BR>";
        $query = $db->Execute("UPDATE $dbtables[players] SET sector=$newsector,cleared_defences=' ' where ship_id=$row[ship_id]");
        playerlog($row[ship_id], LOG_TOW, "$row[sector]|$newsector|$row[max_hull]");
        log_move($row[ship_id],$newsector);
        $res->MoveNext();
      }
    }
    else
    {
      echo "<BR>No players to tow.<BR>";
    }
  } while($num_to_tow);
  echo "<BR>";
  
  $multiplier = 0; //no use to run this again
?>
