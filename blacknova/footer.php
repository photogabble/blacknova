<?
global $db,$dbtables;
connectdb();
$res = $db->Execute("SELECT COUNT(*) as loggedin from $dbtables[ships] WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP($dbtables[ships].last_login)) / 60 <= 5 and email NOT LIKE '%@furangee'");
$row = $res->fields;
$online = $row[loggedin];
?>
<BR><CENTER>
<?
// Update counter

$res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] LIMIT 1");
$result = $res->fields;
$mySEC = ($sched_ticks * 60) - (TIME()-$result[last_run]);

?>
<SCRIPT Language="JavaScript">
var myi = <?=$mySEC?>;
document.write('<B ID=myx><?=$mySEC?></B>&nbsp;<? echo $l_footer_until_update; ?><BR>');
setTimeout("rmyx();",1000);

function rmyx()
{
myi = myi - 1;
if (myi <= 0)
{
myi = <? echo ($sched_ticks * 60); ?>
}
document.all.myx.innerHTML = myi;
setTimeout("rmyx();",1000);
}
</SCRIPT>
<?
// End update counter

if($online == 1)
{
   echo $l_footer_one_player_on;
}
else
{
echo $l_footer_players_on_1;
echo " ";
echo $online;
echo " ";
echo $l_footer_players_on_2;
}
?>
</CENTER>
<BR>
<TABLE WIDTH="100%" BORDER=0 CELLSPACING=0 CELLPADDING=0>
<TR>
<TD><FONT COLOR=SILVER SIZE=-4><A HREF="http://www.sourceforge.net/projects/blacknova">BlackNova Traders</A></FONT></TD>
<TD ALIGN=RIGHT><FONT COLOR=SILVER SIZE=-4>© 2000-2002 <a href="mailto:webmaster@blacknova.net">Ron Harwood</a></FONT></TD>
</TR>
<TR><TD><FONT COLOR=SILVER SIZE=-4><A HREF="news.php">Local BlackNova News</A></FONT></TD>
</TR>
</TABLE>
</BODY>
</HTML>
