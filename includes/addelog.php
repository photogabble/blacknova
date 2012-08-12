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
// File: includes/addelog.php

<?php
function AddELog($db,$d_user,$e_type,$e_status,$e_subject,$e_response)
{
    global $ip_address, $raw_prefix;

    $res = $db->Execute("SELECT email, account_id FROM {$raw_prefix}users WHERE email=?", array($d_user));
    $accountinfo = $res->fields;

    $result = $db->Execute("SELECT * FROM {$db->prefix}players LEFT JOIN {$db->prefix}ships " .
                           "ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id WHERE account_id=?", array($accountinfo['account_id']));
    $playerinfo = $result->fields;

    if ($e_type == 0) // For Normal Email, For Future Use.
    {
        $sp_id = $playerinfo['ship_id'];
        $sp_name = $playerinfo['character_name'];
        $sp_IP = $_SESSION['ip_address'];
        $dp_id = $playertinfo['ship_id'];
        $dp_name = $playerinfo['character_name'];
    }
    elseif ($e_type == 1) // For when users Register.
    {
        $sp_id = -1;
        $sp_name = "Not Logged In";
        $sp_IP = $ip_address;
        $dp_id = $playerinfo['ship_id'];
        $dp_name = $accountinfo['email'];
    }
    elseif ($e_type == 2) // For when users Send Feedback.
    {
        $sp_id = $playerinfo['ship_id'];
        $sp_name = $playerinfo['character_name'];
        $sp_IP = $_SESSION['ip_address'];
        $dp_id = $playerinfo['ship_id'];
        $dp_name = $playerinfo['character_name'];
    }
    elseif ($e_type == 3) // For when users Request Password.
    {
        $sp_id = -1;
        $sp_name = "Not Logged In";
        $sp_IP = $ip_address;
        $dp_id = $playerinfo['ship_id'];
        $dp_name = $accountinfo['email'];
    }
    elseif ($e_type == 4) // For when Debugging (Not Used yet).
    {
        $sp_id = -1;
        $sp_name = "GameAdmin";
        $sp_IP = $ip_address;
        $dp_id = $playerinfo['ship_id'];
        $dp_name = $d_user;
    }
    elseif ($e_type == 5) // For sending Global Email to all registered players
    {
        $sp_id = -1;
        $sp_name = "GameAdmin";
        $sp_IP = $_SESSION['ip_address'];
        $dp_id = $playerinfo['ship_id'];
        $dp_name = $d_user;
    }

    if ($e_response == '1')
    {
        $e_response = "Sent OK";
    }

    $e_stamp = date("Y-m-d H:i:s");
    $dp_name = htmlspecialchars($dp_name,ENT_QUOTES,"UTF-8");
    $sp_name = htmlspecialchars($sp_name,ENT_QUOTES,"UTF-8");
    $e_subject = htmlspecialchars($e_subject,ENT_QUOTES,"UTF-8");

    $debug_query = $db->Execute("INSERT INTO {$db->prefix}email_log (log_id, sp_name, sp_IP, dp_name, e_subject, e_status, e_type, e_stamp, e_response) " .
                                "VALUES(?,?,?,?,?,?,?,?,?)", array('', $sp_name, $sp_IP, $dp_name, $e_subject, $e_status, $e_type, $e_stamp, $e_response));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}
?>
