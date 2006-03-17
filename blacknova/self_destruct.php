<?
include("config.php");
updatecookie();

include("languages/$lang");

$title=$l_die_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

bigtitle();

$sure = $_POST['sure'];
$pass = $_POST['pass'];

$result = $db->Execute("SELECT player_id,character_name,password FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $result->fields;

if ($sure == -1) {
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface?id=" . $playerinfo[player_id] . "\">";
}


if(!isset($sure)) {
echo "
<TABLE width=75% align=center>
<TR><TD colspan=2><FONT COLOR=RED><B>$l_die_rusure</B></FONT></TD></TR>
<TR><TD align=center>
<FORM action=$PHP_SELF method=post><INPUT type=hidden name=sure value=-1>
	<INPUT type=submit value=$l_die_nonono!>
</TD><TD align=center>
	$l_die_what
</FORM>
</TD></TR><TR><TD align=center>
<FORM action=$PHP_SELF method=post><INPUT type=hidden name=sure value=1>
	<INPUT type=submit value=$l_yes!>
</TD><TD align=center>
	$l_die_goodbye
</FORM>
</TD></TR></TABLE>
";
} elseif(($sure == 1) || (($sure == 2) && ($pass != $playerinfo['password']))) {	
echo "
<TABLE width=75% align=center>
<TR><TD colspan=2><FONT COLOR=RED><B>$l_die_rusure</B></FONT></TD></TR>
<TR><TD colspan=2 align=center>
	You must enter your password to destroy your ship.
";
if ($sure == 2) {
	echo "<BR>Password was incorrect.";
}
echo "
	<FORM action=$PHP_SELF method=post>Password: <INPUT type=password name=pass>
</TR><TR><TD align=center>
	<INPUT type=hidden name=sure value=2><INPUT type=submit value=$l_yes!>$l_die_goodbye</FORM>
</TD><TD align=center>
	<FORM action=$PHP_SELF method=post><INPUT type=hidden name=sure value=-1>&nbsp;&nbsp;$l_die_what&nbsp;&nbsp;<INPUT type=submit value=$l_die_nonono!></FORM>
</TD></TR></TABLE>
";
} elseif(($sure == 2) && ($pass == $playerinfo['password'])) { 
	echo "$l_die_count<BR>";
	echo "$l_die_vapor<BR><BR>";
	echo "$l_die_please.<BR>";
	db_kill_player($playerinfo['player_id']);
	cancel_bounty($playerinfo['player_id']);
	adminlog(LOG_ADMIN_HARAKIRI, "$playerinfo[character_name]|$ip");
	playerlog($playerinfo[player_id], LOG_HARAKIRI, "$ip");
} elseif ($sure != -1) {
	echo "$l_die_exploit<BR><BR>";
}

if($sure != 2)
{
	TEXT_GOTOMAIN();
}

include("footer.php");
?>
