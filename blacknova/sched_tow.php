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
    $res = $db->Execute("SELECT ship_id, $dbtables[players].player_id,character_name,hull,$dbtables[ships].sector_id,$dbtables[universe].zone_id,max_hull FROM $dbtables[ships], $dbtables[universe],$dbtables[zones] LEFT JOIN $dbtables[players] ON $dbtables[ships].player_id=$dbtables[players].player_id WHERE $dbtables[ships].sector_id=$dbtables[universe].sector_id AND $dbtables[universe].zone_id=$dbtables[zones].zone_id AND max_hull<>0 AND (($dbtables[ships].hull + $dbtables[ships].engines + $dbtables[ships].computer + $dbtables[ships].beams + $dbtables[ships].torp_launchers + $dbtables[ships].shields + $dbtables[ships].armour)/7) >max_hull AND destroyed='N'");
    echo $db->ErrorMsg() . "<br>";
    if($res)
    {
      $num_to_tow = $res->RecordCount();
      echo "<BR>$num_to_tow players to tow:<BR>";
      while(!$res->EOF)
      {
        $row = $res->fields;
        echo "...towing $row[character_name] out of $row[sector_id] ...";
        $newsector = rand(0, $sector_max);
        echo " to sector $newsector.<BR>";
        $query = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$newsector,cleared_defences=' ' where ship_id=$row[ship_id]");
        playerlog($row[player_id], LOG_TOW, "$row[sector_id]|$newsector|$row[max_hull]");
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
