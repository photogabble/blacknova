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
// File: includes/auth.php
//
// This file handles the authentication for TKI. It expects two
// variables: the email account, and the password. It checks them against
// the db, and then checks the ip address of the user logging in against
// a list of banned ip's.
//
// It then returns with "success", "insecure-success", "baduser", "badpass", "loginclosed", "notactive" or "banned".
//
// This file depends upon having the db functions from TKI,
// which will also soon be part of JOMPT as well.
//
// It expects three tables - $db->prefix.players, $raw_prefix.users, $db->prefix.ip_log and $db->prefix.ip_bans.
//
// Here we set to defaults all non-defined variables. Later we will also clean them and typecast them.

function authcheck($charname='', $password='')
{
    // Include the sha256 backend
    include_once './backends/sha256/shaclass.php';

    global $db, $raw_prefix, $playerinfo, $accountinfo;
    global $server_closed;
    if ((!isset($_SESSION['email'])) || ($_SESSION['email'] == ''))
    {
        $_SESSION['email'] = '';
    }

    if ((!isset($_SESSION['password'])) || ($_SESSION['password'] == ''))
    {
        $_SESSION['password'] = '';
    }

/*
    if ((!isset($charname)) || ($charname == ''))
    {
        $charname = $_SESSION['email'];
    }
*/
    if ((!isset($password)) || ($password == ''))
    {
        $password = $_SESSION['password'];
    }

    if ((!isset($charname)) || ($charname == ''))
    {
        return 'baduser';
    }

    if ((!isset($password)) || ($password == ''))
    {
        return 'badpass';
    }

    $playerinfo = '';

    // Cleans character name before we run the select below. Otherwise, someone can use
    // semi-colons in the char name at login and sql inject.
    // TODO! : Mark up the new user page to mention it..
    // Allows A-Z, a-z, 0-9, whitespace, minus/dash, equals, backslash, explanation point, ampersand, asterix, and underscore.
//    $charname = preg_replace ('/[^A-Za-z0-9\s\-\=\\\'\!\&\*\_\@\.]/','',$charname);

    $debug_query2 = $db->SelectLimit("SELECT * FROM {$raw_prefix}users WHERE email=?",1,-1,array($charname));
    db_op_result($db, $debug_query2,__LINE__,__FILE__);
    $accountinfo = $debug_query2->fields;

    $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}players WHERE account_id=?",1,-1, array($accountinfo['account_id']));
    db_op_result($db, $debug_query,__LINE__,__FILE__);
    echo $db->ErrorMsg();
    $playerinfo = $debug_query->fields;

    if ($debug_query && !$debug_query->EOF)
    {
        $playerinfo = $debug_query->fields;
    }
    else
    {
        return 'baduser'; // User does not exist!
    }

    if ($accountinfo['active'] != 'Y')
    {
        return 'notactive'; // Account has not been activated!
    }

    if ($server_closed)
    {
        return 'loginclosed'; // The server is closed to logins for now.
    }

    $ip_address = getenv("REMOTE_ADDR"); // Get IP address for user
    $proxy_address = getenv("HTTP_X_FORWARDED_FOR"); // Get Proxy IP address for user
    $client_ip_address = getenv("HTTP_CLIENT_IP"); // Get http's IP address for user

    // If player's current OR previous ip address is in ban list, he's banned.
    $debug_query = $db->SelectLimit("SELECT ban_reason FROM {$db->prefix}ip_bans WHERE ? LIKE ban_mask OR ? " .
                                    "LIKE ban_mask OR ? LIKE ban_mask",1,-1,array($ip_address, $client_ip_address, $proxy_address));
    db_op_result($db, $debug_query,__LINE__,__FILE__);

    if ($debug_query && !$debug_query->EOF)
    {
        return 'banned'; // User's current or previous ip address is in ban list - He's been banned!
    }

    $ret = 'success';

    if (strlen($password) < 40)
    {
        if (sha256::hash($password) != $accountinfo['password'])
        {
            return 'badpass';
        }
        else
        {
            $ret = 'insecure-success';
        }
    }
    elseif ($password != $accountinfo['password'])
    {
        return 'badpass';
    }

    // If they've made it this far, they have correct credentials!
    return $ret;
}
?>
