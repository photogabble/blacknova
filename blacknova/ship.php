<?
include("config.php");
updatecookie();

include("languages/$lang");

$title=$l_ship_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

$res = $db->Execute("SELECT sector FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

$res2 = $db->Execute("SELECT name, character_name, sector_id FROM $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE ship_id=$ship_id");
$otherplayer = $res2->fields;

bigtitle();

if($othership[sector_id] != $shipinfo[sector_id])
{
  echo "$l_ship_the <font color=white>", $othership[name],"</font> $l_ship_nolonger ", $shipinfo[sector_id], "<BR>";
}
else
{
	echo "$l_ship_youc <font color=white>", $otherplayer[name], "</font>, $l_ship_owned <font color=white>", $otherplayer[character_name],"</font>.<br><br>";
	echo "$l_ship_perform<BR><BR>";
	echo "<a href=scan.php?player_id=$player_id&ship_id=$ship_id>$l_planet_scn_link</a><br>";
	echo "<a href=attack.php?player_id=$player_id&ship_id=$ship_id>$l_planet_att_link</a><br>";
	echo "<a href=mailto.php?to=$player_id>$l_send_msg</a><br>";
}

echo "<BR>";
TEXT_GOTOMAIN();

include("footer.php");

?>
