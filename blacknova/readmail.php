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

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

if ($action=="delete") {
     if(isset($mid)) {
          $query = "DELETE FROM $dbtables[messages] WHERE ID='$mid[0]'";
          $mids = sizeof($mid);
          if($mids >= 2) {
               for($i=1;$i<$mids;$i++) {
                    $query .= " OR ID='$mid[$i]'";
               }
          }
          $query .= " AND recp_id='$playerinfo[ship_id]'";
     } else {
          $query = "DELETE FROM $dbtables[messages] WHERE ID='$ID' AND recp_id='$playerinfo[ship_id]'";
     }
     $db->Execute($query);
?>
<FONT COLOR="#FF0000" Size="4"><B><Center><? echo $l_readm_delete; ?></Center></B></FONT><BR>
<?
}

$res = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id='".$playerinfo[ship_id]."' ORDER BY sent DESC");
 if ($res->EOF)
 {
  echo "$l_readm_nomessage";
 }
 else
 {
$cur_D = date("Y-m-d");
$cur_T = date("H:i:s");
?>

<form action="<?= $PHP_SELF ?>" method="post">
<table>
<TR>
<TD colspan="2" valign="middle" BGCOLOR="<? echo $color_header; ?>"><center><strong><? echo $l_readm_center ?></strong><br><? echo "<font size=-1>$cur_D - $cur_T</font>" ?></center></TD>
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
   $result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id='".$msg[sender_id]."'");
   $sender = $result->fields;

if ($line_counter)
{
 $color = $color_line2;
 $line_counter = false;
}
else
{
 $color = $color_line1;
 $line_counter = true;
}
?>
<TR BGCOLOR="<?= $color ?>">
<td valign="top" width="150" nowrap>
<? echo $sender[character_name]; ?>
</td>
<td valign="top" colspan="2" width="100%">
<strong><? echo $msg[subject]; ?></strong>
</td></tr>
<tr BGCOLOR="<?= $color ?>">
<td>
<? echo $l_readm_captn ?><BR><? echo $sender[ship_name] ?><BR><BR><? echo "<font size=-1>$msg[sent]</font>" ?>
</td>
<td valign="top" colspan="2">
<? echo nl2br($msg[message]); ?>
</td>
</tr>
<tr BGCOLOR="<?= $color ?>">
<td valign="middle" align="left"><input type="checkbox" name="mid[]" value="<?= $msg[ID] ?>"> <font size=-1>Delete?</font></td>
<td colspan="2" valign="middle" align="left">
<font size="-1">[<A HREF="readmail.php?action=delete&ID=<? echo $msg[ID]; ?>"><? echo $l_readm_del ?></A>&nbsp;|&nbsp;<A HREF="mailto2.php?name=<? echo $sender[character_name]; ?>&subject=<? echo $msg[subject] ?>"><? echo $l_readm_repl ?></A>]</font>
</TD>
</TR>
<tr><td colspan="2"></td></tr>
<?
    $res->MoveNext();
  }
?>
<tr><td align="left" valign="middle" colspan="3">
<input type="hidden" name="action" value="delete">
<input type="submit" name="delete" value="Delete Marked Messages">
</TABLE>
</form>
<?
 }

TEXT_GOTOMAIN();

include("footer.php");
?>
