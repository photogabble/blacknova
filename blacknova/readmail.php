<?
include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_readm_title;
include("header.php");

bigtitle();

connectdb();

if(checklogin())
{
  die();
}

$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $res->fields;

if ($action=="delete")
{
 $db->Execute("DELETE FROM $dbtables[messages] WHERE ID='".$ID."' AND recp_id='".$playerinfo[player_id]."'");
?>
<FONT COLOR="#FF0000" Size="7"><B><Blink><Center><? echo $l_readm_delete; ?></Center></Blink></B></FONT><BR>
<?
}

$res = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id='".$playerinfo[player_id]."' ORDER BY sent DESC");
 if ($res->EOF)
 {
  echo "$l_readm_nomessage";
 }
 else
 {
$cur_D = date("Y-m-d");
$cur_T = date("H:i:s");
?>

<Table >
<TR>
<TD colspan="2" BGCOLOR="<? echo $color_header; ?>"><? echo $l_readm_center ?></TD>
<TD rowspan="2" width=75><? echo "<font size=-1>$cur_D<BR>$cur_T</font>" ?></TD>
</TR>
<TR BGCOLOR="<? echo $color_line1; ?>">
<TD>
<? echo $l_readm_sender; ?>
</TD>
<TD>
<? echo $l_sendm_mess ?>
</TD>
</TR>
<?
  $line_counter = true;
  while(!$res->EOF)
  {
   $msg = $res->fields;
   $result = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id='".$msg[sender_id]."'");
   $sender = $result->fields;
?>
<TR BGCOLOR="<?
if ($line_counter)
{
 echo $color_line2;
 $line_counter = false;
}
else
{
 echo $color_line1;
 $line_counter = true;
}
?>">
<TD VALIGN=TOP width=150>
<? echo $sender[character_name]; ?><HR><? echo $l_readm_captn ?><BR><? echo $sender[ship_name] ?><BR><BR><? echo "<font size=-1>$msg[sent]</font>" ?>
</TD>
<TD VALIGN=TOP>
<B><? echo $msg[subject]; ?></B><HR><? echo nl2br($msg[message]); ?>
</TD>
<TD>
<A HREF="readmail.php?action=delete&ID=<? echo $msg[ID]; ?>"><? echo $l_readm_del ?></A><BR>
<A HREF="mailto2.php?name=<? echo $sender[character_name]; ?>&subject=<? echo $msg[subject] ?>"><? echo $l_readm_repl ?></A><BR>
</TD>
</TR>
<?
    $res->MoveNext();
  }
?>
</TABLE>
<?
 }

TEXT_GOTOMAIN();

include("footer.php");
?>
