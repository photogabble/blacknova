<?

  if (preg_match("/sched_ranking.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  echo "<B>RANKING</B><BR><BR>";
  $res = $db->Execute("SELECT player_id FROM $dbtables[players] LEFT JOIN $dbtables[ships] USING(player_id) WHERE destroyed='N'");
  while(!$res->EOF)
  {
    gen_score($res->fields[player_id]);
    $res->MoveNext();
  }
  echo "<BR>";
  $multiplier = 0;

?>
