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
// File: admin/globmsg.php

$pos = (strpos($_SERVER['PHP_SELF'], "/globmsg.php"));
if ($pos !== false)
{
    include_once './global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once './header.php';
    echo $l_cannot_access;
    include_once './footer.php';
    die();
}
// Dynamic functions & classes
dynamic_loader ($db, "addelog.php");
dynamic_loader ($db, "phpmailer.php");

if (empty($content))
{
    $selfpath = basename($_SERVER['PHP_SELF']);
    echo "<div align=\"left\">\n";
    echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n";
    echo "    <tr>\n";
    echo "      <td width=\"50%\" nowrap><b>Global Email Message</b></td>\n";
    echo "    </tr>\n";
    echo "  </table>\n";
    echo "</div>\n";
    echo "<form action=admin.php method=post>";
    echo "<input type=\"radio\" name=\"messagetype\" value=\"email\" checked>Email &nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"messagetype\" value=\"message\">Internal Message<br><br>";
    echo "<table>";
    echo "  <tr>";
    echo "    <td><font size=\"2\">TO:</font></td>";
    echo "    <td><input disabled maxLength=\"40\" size=\"40\" value=\"All Players\" name=\"dummy\"></td>";
    echo "  </tr>";
    echo "  <tr>";
    echo "    <td><font size=\"2\">FROM:</font></td>";
    echo "    <td><input disabled maxLength=\"40\" size=\"40\" value=\"GameAdmin\" name=\"dummy\"></td>";
    echo "  </tr>";
    echo "  <tr>";
    echo "    <td><font size=\"2\">SUBJECT:</font></td>";
    echo "    <td><input maxLength=\"40\" size=\"40\" name=\"subject\"></td>";
    echo "  </tr>";
    echo "  <tr>";
    echo "    <td valign=\"top\"><font size=\"2\">MESSAGE:</font></td>";
    echo "    <td><textarea name=\"content\" rows=\"5\" cols=\"40\"></textarea></td>";
    echo "  </tr>";
    echo "  <tr>";
    echo "    <td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"Reset\"></td>";
    echo "  </tr>";
    echo "</table>";
    echo "<input type=hidden name=\"menu\" value=\"globmsg\">";
    echo "</form>";
}
else
{
    $res = $db->Execute("select * from {$db_prefix}players LEFT JOIN {$db_prefix}ships " .
                        "ON {$db_prefix}players.player_id = {$db_prefix}ships.player_id " .
                        "WHERE email!=? AND email NOT LIKE '%@aiplayer' ORDER BY character_name ASC", array($admin_mail));
    $row = $res->fields;

    if ($messagetype=='email')
    {
        $headers = "From: GameAdmin <$admin_mail>\r\n";
        if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
        {
            $add_slash_to_url = '/';
        }

        $content .= "\r\n\r\n" . $server_type . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url ."\r\n";
        $content = str_replace("\r\n.\r\n","\r\n. \r\n",$content);
    }
    elseif ($messagetype=='message')
    {
        $timestamp = date("Y-m-d H:i:s");
        $r2 = $db->Execute("select player_id FROM {$db_prefix}players WHERE email=?", array($admin_mail));
        $admin_id = $r2->fields["player_id"];
    }

    // New lines to prevent SQL injection. Bad stuff.
    $content = htmlspecialchars($content);
    $subject = htmlspecialchars($subject);

    while (!$res->EOF)
    {
        if ($messagetype=='email')
        {
            // Include the phpmailer backend
            include_once './backends/phpmailer/class.phpmailer.php';

            // Instantiate your new class
            $mail_msg = new bnt_mailer;

            // Now you only need to add the necessary stuff
            $mail_msg->AddAddress($res->fields['email'], $res->fields['character_name']); // Who its sent to
            $mail_msg->Subject = $subject;
            $mail_msg->Body    = $content;

            if ($mail_msg->Send())
            {
                echo "<font color=\"lime\">Global Message sent to ".$res->fields["email"]."</font> - \n";
                AddELog($res->fields['email'],5,'Y',$subject,$mail_msg->ErrorInfo);
            }
            else
            {
                echo "<font color=\"Red\">Global Message failed to send to ".$res->fields["email"]."</font> - \n";
                AddELog($res->fields['email'],5,'N',$subject,$mail_msg->ErrorInfo);
            }
        }
        elseif ($messagetype=='message')
        {
            $temp = $silent;
            $silent = 0;
            echo "Sending Global Message to <b>". $res->fields["character_name"] . "</b> ";
            $debug_query = $db->Execute("INSERT INTO {$db_prefix}messages (sender_id, recp_id, subject, sent, message) VALUES " .
                                        "(?,?,?,?,?)", array($admin_id, $res->fields['player_id'], $subject, $timestamp, $content));
            db_op_result($debug_query,__LINE__,__FILE__);
            $silent = $temp;
        }

        $res->MoveNext();
    }

    echo "<br><font color=\"lime\">Messages sent</font><br>\n";
}

?>
