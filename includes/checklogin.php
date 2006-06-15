<?php
function checklogin($db, $email='', $password='')
{
    // Dynamic functions
    dynamic_loader ($db, "adminlog.php");

    global $server_closed, $langdir, $l_global_needlogin, $l_error_occured, $local_lang;
    global $templateset;
    global $open_time, $end_time, $attack_repeats;

    if (!isset($_SESSION['character_name']))
    {
        $_SESSION['character_name'] = '';
    }

    if (!isset($_SESSION['password']))
    {
        $_SESSION['password'] = '';
    }

    if (!isset($_SESSION['email']))
    {
        $_SESSION['email'] = '';
    }

    if ($email == '')
    {
        $email = $_SESSION['email'];
    }

    if ((!isset($password)) || ($password == ''))
    {
        $password = $_SESSION['password'];
    }

/*    $stamp = date("Y-m-d H:i:s");
    if ($server_closed && $open_time > $stamp)
    {
        $server_closed = false;
        $db->Execute("UPDATE {$db->prefix}settings SET server_closed=0");
    }

    if ($end_time > $stamp)
    {
        $server_closed = true;
        $db->Execute("UPDATE {$db->prefix}settings SET server_closed=1");
    }*/

    // Dynamic functions
    dynamic_loader ($db, "auth.php"); // handles all login checks
    $login_results = authcheck($email, $password);

    if ($login_results == 'baduser')
    {
        global $l_error_occured, $raw_prefix, $template;
        // User not recognized - we should offer a redirect to login in this case.
        $title = $l_error_occured;
        include_once("./header.php");
        echo "<h1>" . $title. "</h1>\n";
        echo "<a href=\"index.php\">" . $l_global_needlogin . "</a>";
        include_once ("./footer.php");
        // Dynamic functions
        dynamic_loader ($db, "attack_check.php");
        attack_check($db, $ip_address, $attack_repeats);
        adminlog($db, "LOG_RAW","Bad login - user from $ip_address"); // This isnt working!
        die();
    }
    elseif ($login_results == 'badpass')
    {
        global $l_login_4gotpw1, $l_login_4gotpw2, $l_login_4gotpw3, $l_login_4gotpw4, $l_login_4gotpw5, $l_clickme;
        global $db, $raw_prefix;

        $debug_query2 = $db->SelectLimit("SELECT * FROM {$raw_prefix}users WHERE email='$email'",1);
        db_op_result($db,$debug_query2,__LINE__,__FILE__);
        $accountinfo = $debug_query2->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}players WHERE account_id='$accountinfo[account_id]'",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $playerinfo = $debug_query->fields;

        // Password is incorrect
        global $l_error_occured, $title;
        $title = $l_error_occured;
        include_once("./header.php");
        echo "<h1>" . $title. "</h1>\n";
        echo $l_login_4gotpw1 ."<br><br>" . $l_login_4gotpw2 . "<a href=\"mail.php?character_name=$playerinfo[character_name]\">" . $l_login_4gotpw3;
        echo "</a><br><br> <a href=\"index.php\">" . $l_login_4gotpw4 . "</a> " . $l_login_4gotpw5 . " " . $ip_address. "...";
        adminlog($db, "LOG_RAW","Bad login - password from $ip_address");
        // Dynamic functions
        dynamic_loader ($db, "attack_check.php");
        attack_check($db, $ip_address, $attack_repeats);
        include_once ("./footer.php");
        die();
    }
    elseif ($login_results == 'banned')
    {
        global $db;
        global $l_error_occured;
        dynamic_loader ($db, "load_languages.php");

        // Load language variables
        load_languages($db, $raw_prefix, 'login2');

        $ip_address = getenv("REMOTE_ADDR"); // Get IP address for user
        $proxy_address = getenv("HTTP_X_FORWARDED_FOR"); // Get Proxy IP address for user
        $client_ip_address = getenv("HTTP_CLIENT_IP"); // Get http's IP address for user

        // IP was banned
        $debug_query = $db->SelectLimit("SELECT ban_reason FROM {$raw_prefix}ip_bans WHERE '$ip_address' LIKE ban_mask OR '$client_ip_address' " .
                                    "LIKE ban_mask OR '$proxy_address' LIKE ban_mask",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $ban = $debug_query->fields;

        $title = $l_error_occured;
        include_once("./header.php");
        echo "<h1>" . $title. "</h1>\n";
        echo "<<div style='text-align:center'><p><font color=\"red\"><strong>" . $l_login_banned . "<strong></font><p></div><br>";
        echo "<div style='text-align:center'><p><font color=\"red\"><strong>" . $l_login_banned2 . $ban['ban_reason'] . "<strong></font><p></div>";
        include_once ("./footer.php");
        adminlog($db, "LOG_RAW", "Bad login - banned user from $ip_address");
        die();
    }
    elseif ($login_results == 'notactive')
    {
        global $l_error_occured;
        $title = $l_error_occured;
        include_once("./header.php");
        echo "<h1>" . $title. "</h1>\n";
        echo "Your account is marked inactive. This means that you have not confirmed your account. Please do so following ";
        echo "the directions you received in email.<br><br>\n";
        adminlog($db, "LOG_RAW", "Bad login - inactive user from $ip_address");
        // Dynamic functions
        dynamic_loader ($db, "attack_check.php");
        attack_check($db, $ip_address, $attack_repeats);
        include_once ("./footer.php");
        die();
    }
    elseif ($server_closed && $open_time < $stamp)
    {
        global $l_error_occured, $l_login_closed_message;
        $title = $l_error_occured;
        include_once("./header.php");
        echo "<h1>" . $title. "</h1>\n";
        echo $l_login_closed_message;
//        adminlog($db, "LOG_RAW", "Bad login - server closed from $ip_address");
        include_once ("./footer.php");
        die();
    }
    elseif ($login_results == 'insecure-success')
    {
        return "insecure";
    }
    elseif ($login_results == 'success')
    {
        // Passthru!
    }
    else
    {
        global $l_error_occured;
        $title = $l_error_occured;
        include_once("./header.php");
        echo "passthru error " . $login_results;
        echo "<h1>" . $title. "</h1>\n";
        echo $l_error_occured;          
        include_once ("./footer.php");
        adminlog($db, "LOG_RAW", "Questionable login - unknown issue from $ip_address");
        die();
    }
}
?>
