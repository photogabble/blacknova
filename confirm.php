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
// File: confirm.php
$no_body = 1;
$title = $l_confirm_title;
include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "attack_check.php");
dynamic_loader ($db, "adminlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'login2');


global $db;

// If player's current OR previous ip address is in ban list, he's banned.
$ip_address = getenv("REMOTE_ADDR"); // Get IP address for user
$proxy_address = getenv("HTTP_X_FORWARDED_FOR"); // Get Proxy IP address for user
$client_ip_address = getenv("HTTP_CLIENT_IP"); // Get http's IP address for user

$debug_query = $db->SelectLimit("SELECT ban_reason FROM {$raw_prefix}ip_bans WHERE '$ip_address' LIKE ban_mask OR '$client_ip_address' " .
                                "LIKE ban_mask OR '$proxy_address' LIKE ban_mask",1);
db_op_result($db,$debug_query,__LINE__,__FILE__);

if ($debug_query && !$debug_query->EOF)
{
        // IP was banned      
        global $l_error_occured, $l_login_banned;
        $title = $l_error_occured;
        echo "<h1>" . $title. "</h1>\n";
        echo "<div style="text-align:center;"><p><font color=\"red\"><strong>" . $l_login_banned . "</strong></font><p></div>";
        include_once ("./footer.php");
        adminlog($db, "LOG_RAW","Bad login - banned user from $ip_address");
        die();
}

if (isset($_GET['c_code']))
{
    $c_code = $_GET['c_code'];
}
elseif (isset($_POST['c_code']))
{
    $c_code = $_POST['c_code'];
}
else
{
    $c_code = '';
}

if (isset($_GET['email']))
{
    $email = $_GET['email'];
}
elseif (isset($_POST['email']))
{
    $email = $_POST['email'];
}
else
{
    $email = '';
}

if (!isset($_POST['submit']))
{
    $_POST['submit'] = '';
}

$template->assign("email", $email);
$template->assign("title", $title);
$template->assign("code", $c_code);
$template->assign("submit", $_POST['submit']);
$template->display("$templateset/confirm.tpl");

// If they have clicked submit, check everything.
if ($_POST['submit'] == 'submit')
{
    $confirm = '';
    $debug_query = $db->SelectLimit("SELECT * FROM {$raw_prefix}users WHERE email='$email' and c_code='$c_code'",1);
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $account_id = $debug_query->fields['account_id'];

    if ($debug_query->EOF || $debug_query2->EOF)
    {
        echo "Error. Please try again.<br><br>"; // character name or confirmation code did not match
        attack_check($db);
        global $l_global_mlogin;
        echo "<a href=\"index.php\">" . $l_global_mlogin . "</a>";
    }
    else // Its an actual user, and he knew his c_code (or we arent using c_codes). Now check his password.
    {
        $accountinfo = $debug_query2->fields;

        if ($_POST['crypted_password'] != $accountinfo['password'])
        {
            echo "Resetting password - ";
            $debug_query = $db->Execute("UPDATE {$raw_prefix}users SET password='". $_POST['crypted_password'] ."' WHERE account_id='$account_id' and c_code='$c_code'");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            global $l_global_mlogin;
            echo "<a href=\"index.php\">" . $l_global_mlogin . "</a>";
        }
        else
        {
            echo "Thank you for activating your account. <br><br>You may now";
            echo " <a href=\"index.php\">login</a>.";

            $debug_query = $db->SelectLimit("UPDATE {$raw_prefix}users SET active='Y' WHERE account_id='$account_id'");
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
}
else
{
    global $l_global_mlogin;
    echo "<a href=\"index.php\">" . $l_global_mlogin . "</a>";
}

include_once ("./footer.php");
?>
