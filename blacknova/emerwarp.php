<?
	include("config.php");
	updatecookie();

  include("languages/$lang");
	$title=$l_ewd_title;
	include("header.php");

	connectdb();

	if (checklogin()) {die();}

	$result = $db->Execute ("SELECT * FROM $dbtables[players] WHERE email='$username'");
	$playerinfo=$result->fields;

  $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
  $shipinfo = $res->fields;

	srand((double)microtime()*1000000);
        bigtitle();
	if ($shipinfo[dev_emerwarp]>0)
	{
		$dest_sector=rand(0,$sector_max);
		$result_warp = $db->Execute ("UPDATE $dbtables[ships] SET sector_id=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$shipinfo[ship_id]");
    log_move($shipinfo[ship_id],$dest_sector);
		$l_ewd_used=str_replace("[sector]",$dest_sector,$l_ewd_used);
		echo "$l_ewd_used<BR><BR>";
	} else {
		echo "$l_ewd_none<BR><BR>";
	}

    TEXT_GOTOMAIN();

	include("footer.php");

?>
