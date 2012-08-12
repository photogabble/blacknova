<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: mail.php

include_once './global_includes.php';

// Dynamic functions & classes
dynamic_loader ($db, "addelog.php");
dynamic_loader ($db, "phpmailer.php");

// Load language variables
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'mail');

if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
{
    $add_slash_to_url = '/';
}

if (!isset($_GET['character_name']))
{
    $_GET['character_name'] = '';
}

$title = $l_mail_title;

echo "<h1>" . $title. "</h1>\n";

global $game_name;
$result = $db->Execute ("SELECT c_code, email, password FROM {$raw_prefix}users WHERE character_name=?", array($_GET['character_name']));
if (!$result->EOF)
{
    $mailplayer_info = $result->fields;
    $tempurl = $server_type . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "confirm.php";
    $l_mail_message = str_replace("[c_code]",$mailplayer_info['c_code'],$l_mail_message);
    $l_mail_message = str_replace("[ip_address]",$ip_address,$l_mail_message);
    $l_mail_message = str_replace("[game_name]",$game_name,$l_mail_message);
    $l_mail_message = str_replace("[url]",$tempurl,$l_mail_message);
    $msg = $l_mail_message;

    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
    {
        $add_slash_to_url = '/';
    }

    $msg .="\r\n\r\n" . $server_type . "://$_SERVER[SERVER_NAME]" . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "\r\n";
    $msg = str_replace("\r\n.\r\n","\r\n. \r\n",$msg);

    // Include the phpmailer backend
    include_once './backends/phpmailer/class.phpmailer.php';

    // Instantiate your new class
    $mail_msg = new bnt_mailer;

    $mail_msg->Host = $mail_host;
    $mail_msg->Mailer = $mailer_type;
    $mail_msg->AddAddress($mailplayer_info['email'], ""); // Who its sent to
    $mail_msg->Subject = $l_mail_topic;
    $mail_msg->Body    = $msg;
    $mail_msg->From    = $admin_mail;
    $mail_msg->FromName = $admin_mail_name;

    if ($mail_msg->Send())
    {
        echo "<font color=\"lime\">Confirmation code has been sent to " . $_GET['character_name'] . ".</font>\n<br>";
        AddELog($db,$mailplayer_info['email'],3,'Y',$l_mail_topic,$mail_msg->ErrorInfo);
    }
    else
    {
        echo "<font color=\"red\">Confirmation code failed to send to " . $_GET['character_name'] . ".</font> - \n<br>";
        AddELog($db,$mailplayer_info['email'],3,'N',$l_mail_topic,$mail_msg->ErrorInfo);
    }

    echo "<br>";
    echo "<a href=confirm.php class=nav>$l_clickme</a> $l_new_login";
}
else
{
    echo "<strong>" . $l_mail_noplayer1 . "<a href=new.php>" . $l_mail_noplayer2 . "</a></strong><br>";
}

include_once './footer.php';
?>
