<BR><BR><CENTER>
<?
global $db,$dbtables;
connectdb();

// Load default Playerinfo
$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

// Players Online
$res = $db->Execute("SELECT COUNT(*) as loggedin from $dbtables[ships] WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP($dbtables[ships].last_login)) / 60 <= 5 and email NOT LIKE '%@furangee'");
$row = $res->fields;
$online = $row[loggedin];

// Time Left
$res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] LIMIT 1");
$result = $res->fields;
$mySEC = ($sched_ticks * 60) - (TIME()-$result[last_run]);

// Admin News
$res = $db->Execute("SELECT * FROM $dbtables[adminnews] ORDER BY an_id DESC");
$result = $res->fields;
$adminnews = $result[an_text];
if ($adminnews == "") $adminnews = "---";

// Vote System
if(!empty($username))
	{
		if ($playerinfo['vote'] < 0) {
			$vote_text = "Inactive!";
		} else if ($playerinfo['vote'] > 0) {
			$vote_text =  "<a href='javascript:OpenVote()'>View Results.</A>";
		} else if ($playerinfo['vote'] == 0) {
			$res = $db->Execute("SELECT * FROM $dbtables[vote] WHERE vote_id = 0");
			$row = $res->fields;
			$vote_text =  "<a href='javascript:OpenVote()'>$row[vote_text]</A>";
		}
	} else {
		$vote_text =  "Login!";
	}



?>

<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 xwidth="100%">
	<TR>
		<TD nowrap xwidth="10%" align=center bgcolor='<? echo $color_header; ?>'>&nbsp;&nbsp;Time Zone&nbsp;&nbsp;</TD>
		<TD nowrap xwidth="10%" align=center bgcolor='<? echo $color_line2; ?>'>&nbsp;&nbsp;Server Date/Time&nbsp;&nbsp;</TD>
		<TD nowrap xwidth="10%" align=center bgcolor='<? echo $color_header; ?>'>&nbsp;&nbsp;Next Update in&nbsp;&nbsp;</TD>
		<TD nowrap xwidth="10%" align=center bgcolor='<? echo $color_line2; ?>'>&nbsp;&nbsp;Players Online&nbsp;&nbsp;</TD>
		<TD nowrap xwidth="35%" align=center bgcolor='<? echo $color_header; ?>'>&nbsp;&nbsp;Vote&nbsp;&nbsp;</TD>
		<TD nowrap xwidth="35%" align=center bgcolor='<? echo $color_line2; ?>'>&nbsp;&nbsp;Admin News&nbsp;&nbsp;</TD>
	</TR>
	<TR>
		<TD nowrap align=center>&nbsp;<? echo $servertimezone; ?>&nbsp;</TD>
		<TD nowrap align=center>&nbsp;<span id=MyServerTime>---</span>&nbsp;</TD>
		<TD nowrap align=center>&nbsp;<B><span id=MyTimer>---</span></B>&nbsp;</TD>
		<TD nowrap align=center>&nbsp;<B><? echo $online; ?></B>&nbsp;</TD>
		<TD nowrap align=center>&nbsp;<? echo $vote_text; ?>&nbsp;</TD>
		<TD nowrap align=center>&nbsp;<? echo $adminnews; ?>&nbsp;</TD>
	</TR>
	<TR>
		<TD colspan=6 bgcolor='<? echo $color_line1; ?>'><IMG height=1 width=1 SRC='images/spacer.gif'></TD>
	</TR>
</TABLE>

<?
if (($playerinfo[shoutbox]=='Y')&&($title!="SHOUTBOX"))
	{
	echo "<BR><BR>";
	include("shoutbox.php");
	}
	else
	{
	echo "<BR><a href='javascript:OpenSB()'>View SHOUTBOX!</A>";
	}
?>


<script language="javascript" type="text/javascript">
	var myi = <? echo $mySEC; ?> + 1;
	var myt = new Date();
	var myt_start = <? echo TIME();?> + "000";
	var myt_tmp = "---";
	myt.setTime(myt_start - 1000);

	rmyx();
	
	function rmyx()
		{
			myi--;

			myt_start= (myt_start*1) + 1000;
			myt.setTime(myt_start);
				{
					var Year = myt.getFullYear();
					var Mon = myt.getMonth() + 1;
					var Day = myt.getDate();
					var Std = myt.getHours();
					var Min = myt.getMinutes();
					var Sec = myt.getSeconds();
					
					Mon  = ((Mon < 10) ? "0" + Mon : Mon);
					Day  = ((Day < 10) ? "0" + Day : Day);
					Std  = ((Std < 10) ? "0" + Std : Std);
					Min  = ((Min < 10) ? "0" + Min : Min);
					Sec  = ((Sec < 10) ? "0" + Sec : Sec);
					
					myt_tmp = Year + "-" + Mon + "-" + Day + "   " + Std + ":" + Min + ":" + Sec;
				}

			document.getElementById("MyServerTime").innerHTML = myt_tmp;

			if (myi <= 0)
					document.getElementById("MyTimer").innerHTML = "<FONT COLOR=RED>UPDATE!</FONT>";
			else
					document.getElementById("MyTimer").innerHTML = myi;

			setTimeout("rmyx();",1000);
		}
	function OpenVote()
		{
			f1 = open("vote.php","f1","width=250,height=350");
		}
	function OpenSB()
		{
			f2 = open("shoutbox.php","f2","width=600,height=400,scrollbars=yes");
		}
</script>

</center><br>
  <table width="100%" border=0 cellspacing=0 cellpadding=0>
   <tr>
    <td><font color=silver size=-4><a href="http://www.sourceforge.net/projects/blacknova">BlackNova Traders</a></font></td>
    <td align=right><font color=silver size=-4>© 2000-2002 <a href="mailto:webmaster@blacknova.net">Ron Harwood</a></font></td>
   </tr>
   <tr>
    <td><font color=silver size=-4><a href="news.php">Local BlackNova News</a></font></td>
    <td align=right><font color=silver size=-4>© 2002 <a href="mailto:indiana@rednova.de">Indiana</a></font></td>
   </tr>
  </table>
</body>
</html>