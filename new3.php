<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: new3.php

include_once ("./global_includes.php"); 

if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
{
    $add_slash_to_url = '/';
}

// Dynamic functions & classes
dynamic_loader ($db, "newplayer.php");
dynamic_loader ($db, "validateemailformat.php");
dynamic_loader ($db, "addelog.php");
dynamic_loader ($db, "phpmailer.php");

// Load language variables
load_languages($db, $raw_prefix, 'mail');
load_languages($db, $raw_prefix, 'new2');
load_languages($db, $raw_prefix, 'common');

if (!isset($_POST['character']))
{
    $_POST['character'] = '';
}

if (!isset($_POST['shipname']))
{
    $_POST['shipname'] = '';
}

if (!isset($_POST['email']))
{
    $_POST['email'] = '';
}

if (!isset($_POST['password']))
{
    $_POST['password'] = '';
}

$c_code = '';
$title = $l_new_title2;

echo "<h1>" . $title. "</h1>\n";

$flag = 0;
if (!isset($hdrs))
{
    $hdrs = '';
}

if ($account_creation_closed)
{
    load_languages($db, $raw_prefix, 'new');
    echo $l_new_closed_message . '<br>';
    $flag = 1;
}

if (!validateEmailFormat($_POST['email']))
{
    echo $l_invalid_email . '<br>';
    $flag = 1;
}

// Convert any html entities. Prevents html/js exploit crap.
$email = htmlspecialchars($_POST['email'],ENT_QUOTES,"UTF-8");
$shipname = htmlspecialchars($_POST['shipname'],ENT_QUOTES,"UTF-8");
$character = htmlspecialchars($_POST['character'],ENT_QUOTES,"UTF-8");
$password = htmlspecialchars($_POST['password'],ENT_QUOTES,"UTF-8");

if ($invitation_only && !$flag)
{
   global $l_not_invited;
   $debug_query = $db->Execute("SELECT email FROM {$db->prefix}memberlist WHERE email='" . strtolower($email). "'");
   db_op_result($db,$debug_query,__LINE__,__FILE__);
   if ($debug_query)
   {
       if ($debug_query->EOF)
       {
           echo $l_not_invited;
           $flag=1;
       }
       else
       {
           $flag=0;
       }
   }
}

if (($email == '' || $character == '' || $shipname == '' || $password == '') && !$flag)
{ 
    echo "$l_new_blank<br>"; 
    $flag = 1;
}

if (!$flag)
{
    $result = $db->Execute ("SELECT email FROM {$raw_prefix}users");
    $result2 = $db->Execute ("SELECT character_name FROM {$db->prefix}players");

    if ($result>0)
    {
        while (!$result->EOF)
        {
            $row = $result->fields;
            if (strtolower($row['email']) == strtolower($email)) 
            { 
                $l_new_inuse = str_replace("[username]", "\"$email\"", $l_new_inuse);
                echo "$l_new_inuse<br><br>"; 
                $flag = 1;
                $pw_flag = 1;
            }
            $result->MoveNext();
        }
    }

    if ($result2>0)
    {
        while (!$result2->EOF)
        {
            $row2 = $result2->fields;
            if (strtolower($row2['character_name']) == strtolower($character)) 
            { 
                $l_new_inusechar = str_replace("[character]", "\"$character\"", $l_new_inusechar);
                echo "$l_new_inusechar<br><br>"; 
                $flag = 1;
                $pw_flag = 1;
            }
            elseif (metaphone($row2['character_name']) == metaphone($character)) 
            { 
                $l_new_similar_inusechar = str_replace("[character]", "\"$row2[character_name]\"", $l_new_similar_inusechar);
                echo "$l_new_similar_inusechar<br><br>"; 
                $flag = 1;
                $pw_flag = 1;
            }
            $result2->MoveNext();
        }
    }
}

if (!$flag)
{
    $result = $db->Execute ("SELECT name FROM {$db->prefix}ships");

    if ($result>0)
    {
        while (!$result->EOF)
        {
            $row = $result->fields;
            if (strtolower($row['name']) == strtolower($shipname)) 
            { 
                $l_new_inuseship = str_replace("[shipname]", "\"$shipname\"", $l_new_inuseship);
                echo "$l_new_inuseship <br><br>"; 
                $flag = 1;
            }
            elseif (metaphone($row['name']) == metaphone($shipname)) 
            { 
                $l_new_similar_inuseship = str_replace("[shipname]", "\"$row[name]\"", $l_new_similar_inuseship);
                echo "$l_new_similar_inuseship<br><br>"; 
                $flag = 1;
                $pw_flag = 1;
            }

            $result->MoveNext();
        }
    }
}

if ($flag == 0)
{
    // insert code to add player to database
    // Now we need to generate text to put in the image
    // Get a random string and push it through the md5 function
    $c_code = md5(mt_rand(0,9999));

    // Creates the confirmation code.
    $c_code = substr($c_code, 8, 6);

    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
    {
        $add_slash_to_url = '/';
    }

    $shipid = newplayer($db,$email, $character, $password, $c_code, $shipname, "1"); // Last param is 1 for their acl level.
    $tempurl = $server_type . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "confirm.php?c_code=" . $c_code;

    $l_newmsg_whosent = str_replace("[ip_address]",$ip_address,$l_newmsg_whosent);
    $l_newmsg_whosent = str_replace("[game_name]",$game_name,$l_newmsg_whosent);
    $l_newmsg_confirm = str_replace("[url]",$tempurl,$l_newmsg_confirm);
    $l_newmsg_gamename = str_replace("[game_name]",$game_name,$l_newmsg_gamename);
    $l_newmsg_c_code = str_replace("[c_code]",$c_code,$l_newmsg_c_code);

    // Give the variables to Smarty, and then retreive the templated newplayer email
    $template->assign("l_newmsg_greetings", $l_newmsg_greetings);
    $template->assign("l_newmsg_whosent", $l_newmsg_whosent);
    $template->assign("l_newmsg_c_code", $l_newmsg_c_code);
    $template->assign("l_newmsg_confirm", $l_newmsg_confirm);
    $template->assign("l_newmsg_thanks", $l_newmsg_thanks);
    $template->assign("l_newmsg_gamename", $l_newmsg_gamename);
    $msg = $template->fetch("$templateset/newplayer_email.tpl");

    $text_msg = str_replace("<br>", "\n", $msg);
    $text_msg = str_replace("<a href=\"", "", $text_msg);
    $text_msg = str_replace("\">", "", $text_msg);
    $text_msg = str_replace("</a>", "", $text_msg);

    // Make sure that the html version has the url hyperlinked
    $msg = str_replace($tempurl, "<a href=\"" . $tempurl . "\">" . $tempurl . "</a>",$msg);

    // Include the phpmailer backend
    include_once ("./backends/phpmailer/class.phpmailer.php");

    // Instantiate your new class
    $mail_msg = new bnt_mailer;

    $mail_msg->Host = $mail_host;
    $mail_msg->Mailer = $mailer_type;
    $mail_msg->From = $admin_mail;
    $mail_msg->FromName = $admin_mail_name;
    $mail_msg->AddAddress($email, $character); // Who its sent to
    $mail_msg->Subject  = $l_new_topic;
    $mail_msg->Body     = $msg;
    $mail_msg->AltBody  = $text_msg;

    if ($mail_msg->Send())
    {
        echo "<font color=\"lime\">Message Sent</font><br>";
        AddELog($db,$email,1,'Y',$l_new_topic,$mail_msg->ErrorInfo);
    }
    else
    {
        echo "<font color=\"red\">Message failed to send!</font><br>\n";
        AddELog($db,$email,1,'N',$l_new_topic,$mail_msg->ErrorInfo);
    }

    $mail_msg->ClearAddresses();
} 

$template->assign("display_password", $display_password);
$template->assign("l_new_pwis", $l_new_pwis);
$template->assign("l_new_charis", $l_new_charis);
$template->assign("password", $password);
$template->assign("character", $character);
$template->assign("email", $email);
$template->assign("l_new_login", $l_new_login);
$template->assign("l_new_forgotpw", $l_new_forgotpw);
$template->assign("l_clickme", $l_clickme);
$template->assign("flag", $flag);
$template->assign("l_new_err", $l_new_err);
$template->display("$templateset/new3.tpl");

include_once ("./footer.php");
?>
