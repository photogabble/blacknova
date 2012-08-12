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
// File: feedback.php

include_once './global_includes.php';
include_once './backends/phpmailer/class.phpmailer.php';

// Dynamic functions & classes
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "addelog.php");
dynamic_loader ($db, "phpmailer.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'feedback');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_feedback_title;
include_once './header.php';
updatecookie($db);

if (!isset($msg))
{
    $msg = '';
}

if (!isset($hdrs))
{
    $hdrs = '';
}

if (!isset($subject))
{
    $subject = '';
}

$empty_content = FALSE;
$mail_result = FALSE;

if (empty($_POST['content']))
{
    $empty_content = TRUE;
}
else
{
    $content = stripslashes($_POST['content']);
    $subject = stripslashes($_POST['subject']);

    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
    {
        $add_slash_to_url = '/';
    }

    $msg .= "IP address - " . $_SESSION['ip_address'] . "\r\nGame Name - $playerinfo[character_name]\r\n\r\n$content\n\n" . $server_type . "://$_SERVER[SERVER_NAME]" . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "\r\n";
    $msg = str_replace("./\r\n.\r\n","\r\n. \r\n",$msg);
    $hdrs .= "From: $playerinfo[character_name] <$accountinfo[email]>\r\n";

    // Instantiate your new class
    $mail_msg = new bnt_mailer;

    $mail_msg->Host = $mail_host;
    $mail_msg->Mailer = $mailer_type;
    $mail_msg->AddAddress($admin_mail, $admin_mail_name); // Who its sent to
    $mail_msg->Subject = $l_feedback_subj;
    $mail_msg->Body     = $msg;
    $mail_msg->From     = $accountinfo['email'];
    $mail_msg->FromName = $playerinfo['character_name'];

    if ($mail_msg->Send())
    {
        $mail_result = TRUE;
        AddELog($db,$admin_mail,2,'Y',$l_feedback_subj,$mail_msg->ErrorInfo);
    }
    else
    {
        $mail_result = FALSE;
        AddELog($db,$admin_mail,2,'N',$l_feedback_subj,$mail_msg->ErrorInfo);
    }
}

global $l_global_mmenu;

$template->assign("l_feedback_mno_sent", $l_feedback_mno_sent);
$template->assign("l_feedback_msent", $l_feedback_msent);
$template->assign("l_feedback_info", $l_feedback_info);
$template->assign("l_reset", $l_reset);
$template->assign("l_feedback_feedback", $l_feedback_feedback);
$template->assign("accountinfo_email", $accountinfo['email']);
$template->assign("playerinfo_character_name", $playerinfo['character_name']);
$template->assign("l_submit", $l_submit);
$template->assign("l_feedback_message", $l_feedback_message);
$template->assign("l_feedback_topi", $l_feedback_topi);
$template->assign("l_feedback_from", $l_feedback_from);
$template->assign("l_feedback_to", $l_feedback_to);
$template->assign("empty_content", $empty_content);
$template->assign("mail_result", $mail_result);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->assign("title", $title);
$template->display("$templateset/feedback.tpl");

include_once './footer.php';

?>
