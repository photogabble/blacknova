<?
include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_shipyard_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

bigtitle();

$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $result->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;



TEXT_GOTOMAIN();

include("footer.php");

?>