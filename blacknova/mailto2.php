<?
include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_sendm_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

bigtitle();

if(empty($content))
{
  $res = $db->Execute("SELECT character_name FROM $dbtables[ships] ORDER BY character_name ASC");
  $res2 = $db->Execute("SELECT team_name FROM $dbtables[teams] ORDER BY team_name ASC");
  echo "<FORM ACTION=mailto2.php METHOD=POST>";
  echo "<TABLE>";
  echo "<TR><TD>$l_sendm_to:</TD><TD><SELECT NAME=to>";
  while(!$res->EOF)
  {
    $row = $res->fields;
  ?>
    <OPTION <? if ($row[character_name]==$name) echo "selected" ?>><? echo $row[character_name] ?></OPTION>
  <?
    $res->MoveNext();
  }
  while(!$res2->EOF)
  {
    $row2 = $res2->fields;
    echo "<OPTION>$l_sendm_ally $row2[team_name]</OPTION>";
    $res2->MoveNext();
  }

  echo "</SELECT>";
  echo "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"to_allies\"> $l_sendm_myallies</td></tr>\n";
  echo "<TR><TD>$l_sendm_from:</TD><TD><INPUT DISABLED TYPE=TEXT NAME=dummy SIZE=40 MAXLENGTH=40 VALUE=\"$playerinfo[character_name]\"></TD></TR>";
  // Try to reduce the "RE: RE: RE: RE: RE: RE:" subjects
  if (isset($subject) && strpos($subject, "RE:") === false)
     $subject = "RE: ".$subject;

  echo "<TR><TD>$l_sendm_subj:</TD><TD><INPUT TYPE=TEXT NAME=subject SIZE=40 MAXLENGTH=40 VALUE=\"$subject\"></TD></TR>";
  echo "<TR><TD>$l_sendm_mess:</TD><TD><TEXTAREA NAME=content ROWS=5 COLS=40></TEXTAREA></TD></TR>";
  echo "<TR><TD></TD><TD><INPUT TYPE=SUBMIT VALUE=$l_sendm_send><INPUT TYPE=RESET VALUE=$l_reset></TD>";
  echo "</TABLE>";
  echo "</FORM>";
}
else
{
  echo "$l_sendm_sent<BR><BR>";

if (strpos($to, $l_sendm_ally)===false && !isset($to_allies))
{
  $timestamp = date("Y\-m\-d H\:i\:s");
  $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE character_name='$to'");
  $target_info = $res->fields;
  $content = htmlspecialchars($content);
  $subject = htmlspecialchars($subject);
  $db->Execute("INSERT INTO $dbtables[messages] (sender_id, recp_id, sent, subject, message) VALUES ('".$playerinfo[ship_id]."', '".$target_info[ship_id]."', '".$timestamp."', '".$subject."', '".$content."')");
}
else
{
  $timestamp = date("Y\-m\-d H\:i\:s");

     if(!isset($to_allies)) {
          $to = str_replace ($l_sendm_ally, "", $to);
          $to = trim($to);
          $subject = "$to: $subject";
          $to = addslashes($to);
          $res = $db->Execute("SELECT id FROM $dbtables[teams] WHERE team_name='$to'");
          $row = $res->fields;
     } else {
          $res = $db->Execute("SELECT team AS id FROM $dbtables[ships] WHERE ship_id='$playerinfo[ship_id]'");
          $row = $res->fields;
          $subject = "$l_sendm_ally $subject";
     }
     $res2 = $db->Execute("SELECT * FROM $dbtables[ships] where team='$row[id]'");

     while (!$res2->EOF)
     {
        $row2 = $res2->fields;
        $db->Execute("INSERT INTO $dbtables[messages] (sender_id, recp_id, sent, subject, message) VALUES ('".$playerinfo[ship_id]."', '".$row2[ship_id]."', '".$timestamp."', '".$subject."', '".$content."')");
        $res2->MoveNext();
     }

   }

}

TEXT_GOTOMAIN();

include("footer.php");

?>
