<?php
function AddELog($db,$d_user,$e_type,$e_status,$e_subject,$e_response)
{
    global $ip_address, $raw_prefix;

    $res = $db->Execute("SELECT email, account_id FROM {$raw_prefix}users WHERE email='$d_user'");
    $accountinfo = $res->fields;

    $result = $db->Execute("SELECT * FROM {$db->prefix}players LEFT JOIN {$db->prefix}ships " .
                           "ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id WHERE account_id='$accountinfo[account_id]'");
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
