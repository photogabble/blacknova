<?

include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_feedback_title;
include("header.php");

connectdb();

if (checklogin()) {die();}

$result = $db->Execute ("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo=$result->fields;
bigtitle();
if (empty($content))
{
  echo "<form action=feedback.php method=post>";
  echo "<table>";
  echo "<tr><td>$l_feedback_to</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=GameAdmin></td></tr>";
  echo "<tr><td>$l_feedback_from</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>";
  echo "<tr><td>$l_feedback_topi</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=$l_feedback_feedback></td></tr>";
  echo "<tr><td>$l_feedback_message</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>";
  echo "<tr><td></td><td><input type=submit value=$l_submit><input type=reset value=$l_reset></td>";
  echo "</table>";
  echo "</form>";
  echo "<br>$l_feedback_info<br>";
} else {

  $msg .= "IP address - $ip\r\nGame Name - $playerinfo[character_name]\r\n\r\n$content\n\nhttp://$SERVER_NAME$gamepath\r\n";
  $msg = ereg_replace("\r\n.\r\n","\r\n. \r\n",$msg);
  $hdrs .= "From: $playerinfo[character_name] <$playerinfo[email]>\r\n";

  $e_response=mail($admin_mail,$l_feedback_subj,$msg,$hdrs);
  if ($Enable_EmailLoggerModule AND $modules['ELM']){
    if ($e_response===TRUE)
    {
      echo "<font color=\"lime\">Message Sent</font><br>";
      AddELog($admin_mail,Feedback,'Y',$l_feedback_subj,$e_response);
    }
    else
    {
      echo "<font color=\"red\">Message failed to send!</font><br>\n";
      AddELog($admin_mail,Feedback,'N',$l_feedback_subj,$e_response);
    }
    echo "<BR>";
  }
  else
  {
    echo "<font color=\"lime\">Message Sent</font><BR><BR>";
  }
}

TEXT_GOTOMAIN();
include("footer.php");

?>
