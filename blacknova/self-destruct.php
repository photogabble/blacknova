<?
// This self destruct code will only work with version 4.1 code

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
$pass = md5($_POST['pass']);

$result = $db->Execute("SELECT ship_id,character_name,password FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

if ($sure == -1) {
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface?id=" . $playerinfo[ship_id] . "\">";
}


if(!isset($sure)) {
echo "
<TABLE width=75% align=center>
<TR>
	<TD colspan=2>
		<FONT COLOR=RED><B>$l_die_rusure</B></FONT>
	</TD>
</TR>
<TR>
	<TD align=center>
		<FORM action=\"$PHP_SELF\" method=post>
		<INPUT type=hidden name=sure value=-1>
		<INPUT type=submit value=\"$l_die_nonono\">
		</FORM>
	</TD>
	<TD align=center>
		$l_die_what
	</TD>
</TR>
<TR>
	<TD align=center>
		<FORM action=\"$PHP_SELF\" method=post>
		<INPUT type=hidden name=sure value=1>
		<INPUT type=submit value=\"$l_yes\">
		</FORM>
	</TD>
	<TD align=center>
		$l_die_goodbye
	</TD>
</TR>
</TABLE>
";
} elseif(($sure == 1) || (($sure == 2) && ($pass != $playerinfo['password']))) {	
echo "
<TABLE width=75% align=center>
<TR>
	<TD colspan=2>
	<FONT COLOR=RED><B>$l_die_rusure</B></FONT>
	</TD>
</TR>
<TR>
	<TD colspan=2 align=center>
	You must enter your password to destroy your ship.
";
if ($sure == 2) { echo "<BR>Password was incorrect."; }
echo "
	<FORM action=\"$PHP_SELF\" method=post>
	<INPUT type=hidden name=sure value=2>
	Password: <INPUT type=password name=pass>
	</TD>
</TR>
<TR>
	<TD align=center>
		<INPUT type=submit value=$l_yes>&nbsp;&nbsp;$l_die_goodbye
		</FORM>
	</TD>
	<TD align=center>
	<FORM action=\"$PHP_SELF\" method=post>
	<INPUT type=hidden name=sure value=-1>
	$l_die_what&nbsp;&nbsp;<INPUT type=submit value=$l_die_nonono>
	</FORM>
	</TD>
</TR>
</TABLE>
";
} elseif(($sure == 2) && ($pass == $playerinfo['password'])) { 
	echo "$l_die_count<BR>";
	echo "$l_die_vapor<BR><BR>";
	echo "$l_die_please.<BR>";
	db_kill_player($playerinfo['ship_id']);
	cancel_bounty($playerinfo['ship_id']);
	adminlog(LOG_ADMIN_HARAKIRI, "$playerinfo[character_name]|$ip");
	playerlog($playerinfo[ship_id], LOG_HARAKIRI, "$ip");
} elseif ($sure != -1) {
	echo "$l_die_exploit<BR><BR>";
}

if($sure != 2)
{
	TEXT_GOTOMAIN();
}

include("footer.php");
?>
