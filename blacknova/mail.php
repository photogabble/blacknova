<?
include("config.php");
include("languages/$lang");

$title=$l_mail_title;
include("header.php");

connectdb();

bigtitle();

$result = $db->Execute ("select email, password from $dbtables[players] where email='$mail'");

if(!$result->EOF) 
{
  $playerinfo=$result->fields;
  $l_mail_message=str_replace("[pass]",$playerinfo[password],$l_mail_message);

  $msg = $l_mail_message;
  $msg .="\r\n\r\nhttp://$SERVER_NAME$gamepath\r\n";
  $msg = ereg_replace("\r\n.\r\n","\r\n. \r\n",$msg);
  $hdrs = "From: BlackNova Mailer <$admin_mail>\r\n";
  $e_response=mail($mail,$l_mail_topic,$msg,$hdrs);
  if ($Enable_EmailLoggerModule AND $modules['ELM'])
  {
    if ($e_response===TRUE)
    {
      echo "<font color=\"lime\">Password has been sent to $mail.</font> - \n";
      AddELog($mail,ReqPassword,'Y',$l_mail_topic,$e_response);
    }
    else
    {
      echo "<font color=\"red\">Password failed to send to $mail.</font> - \n";
      AddELog($mail,ReqPassword,'N',$l_mail_topic,$e_response);
    }
  }
  else
  {
    echo "<font color=\"lime\">Password has been sent to $mail.</font><br>";
  }
  echo "<br>";
  echo "<A HREF=login.php class=nav>$l_clickme</A> $l_new_login";
}
else
{
  echo "<b>$l_mail_noplayer</b><br>";
}

include("footer.php");
?>

