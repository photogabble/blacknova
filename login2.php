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
// File: login2.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "spy_ship_destroyed.php"); 
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'login2');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

include_once ("./header.php");
global $playerinfo, $accountinfo, $ip_address;

// Here we set to defaults all unset variables. Later we will also clean them and typecast them.
$temppass = '';

if (!isset($_POST['email']))
{
    $_POST['email'] = '';
}

if (!isset($_POST['password']))
{
    $_POST['password'] = '';
}

if ((!isset($_POST['encrypted_password'])) || ($_POST['encrypted_password'] == ''))
{
    $_POST['encrypted_password'] = '';
    $temppass = $_POST['password'];
}
else
{
    $temppass = $_POST['encrypted_password'];
}

$insecure = checklogin($db, $_POST['email'], $temppass);
$title = $l_login_title2;

global $l_login_insecure1;
echo "<h1>" . $title. "</h1>\n";
if ($insecure != '')
{
    echo $l_login_insecure1 . "<br>" . $l_login_insecure2 . "<br><br><strong>" . $l_login_insecure3 . "<br>" . $l_login_insecure4 . "</strong><br><br>";
}

// Ensure langdir is set
$found = 0;

// Now that the user is logged in, we will regenerate their session id for additional security.
// This helps reduce the chance of a session replay attack.
adodb_session_regenerate_id();

// TODO: Newlang needs to be cleaned. BADLY.
if (isset($_POST['newlang'])) 
{ 
    $_SESSION['langdir'] = $_POST['newlang']; 
}

if ((!isset($_SESSION['langdir'])) || ($_SESSION['langdir'] == ''))
{
    $_SESSION['langdir'] = $default_lang;
    
    $h_a_l = '';
    if (isset($_ENV['HTTP_ACCEPT_LANGUAGE'])) 
    {
        $h_a_l = $_ENV['HTTP_ACCEPT_LANGUAGE'];
    }
    elseif (isset($HTTP_ACCEPT_LANGUAGE))
    {
        $h_a_l = $HTTP_ACCEPT_LANGUAGE;
    }
    else
    {
        $h_a_l = 0; // Cannot find either
    }
    
    if ($h_a_l) 
    {
        $plng = split(',', $h_a_l);
        if (count($plng) > 0) 
        {
            $found=0;
//            while (list($k,$v) = each($plng)) 
            foreach($plng as $key=>$val);
            {
                $k = split(';', $v, 1);
                $k = split('-', $k[0]);
                
                switch ($k[0])
                {
                    case 'en': 
                        $_SESSION['langdir'] = 'english';  
                        $found = 1; 
                        break;
                    case 'et': 
                        $_SESSION['langdir'] = 'estonian'; 
                        $found = 1; 
                        break;
                    case 'es': 
                        $_SESSION['langdir'] = 'spanish';  
                        $found = 1; 
                        break;
                    case 'fr': 
                        $_SESSION['langdir'] = 'french';   
                        $found = 1; 
                        break;
                    case 'ru': 
                        $_SESSION['langdir'] = 'russian';   
                        $found = 1; 
                        break;
                    default: 
                        if (file_exists(str_replace(basename(__FILE__),"","login2.php") . 'languages/' . $k[0] ))
                        {
                           $_SESSION['langdir'] = $k[0];
                           $found = 1;
                        }
                }
                if ($found) break;
            }
        }
    }
}

// Set session variables for user
$_SESSION['email'] = $accountinfo['email'];
$_SESSION['character_name'] = $playerinfo['character_name'];
$_SESSION['password'] = $temppass;
$_SESSION['ip_address'] = $ip_address;
$_SESSION['game'] = $raw_prefix . $_POST['gamenum']. "_";
$_SESSION['sessionid'] = session_id();

// lets get the shipinfo for them.
$debug_query = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE player_id=? AND ship_id=?", array($playerinfo['player_id'], $playerinfo['currentship']));
global $db;
db_op_result($db,$debug_query,__LINE__,__FILE__);
$shipinfo = $debug_query->fields;

if ($shipinfo['destroyed'] == "N")
{
    // player's ship has not been destroyed
    playerlog($db,$playerinfo['player_id'], "LOG_LOGIN", $_SESSION['ip_address']);
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET ip_address=? WHERE player_id=?", array($_SESSION['ip_address'],$playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
    {
        $add_slash_to_url = '/';
    }

    if ($insecure == '')
    {
        $server_port = '';
        if ($_SERVER['SERVER_PORT'] != '80')
        {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }

        if ($_SERVER['SERVER_PORT'] == '443')
        {
            $server_type = 'https';
        }
        else
        {
            $server_type = 'http';
        }

        // No click/refresh - seems smoother.

        header("Location: " . $server_type . "://" . $_SERVER['SERVER_NAME'] . $server_port . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "main.php");
    }
}
else
{
    // player's ship has been destroyed
    if ($shipinfo['dev_escapepod'] == "Y")
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET class=1, hull=0, engines=0, pengines=0, power=0, computer=0, " .
                                    "sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=?, " .
                                    "cloak=0, shields=0, sector_id=1, ore=0, organics=0, " . 
                                    "energy=?, colonists=0, goods=0, fighters=?, on_planet='N', " .
                                    "dev_warpedit=0, dev_genesis=0, dev_emerwarp=0, dev_escapepod=?, " .
                                    "dev_fuelscoop=?, dev_minedeflector=0, destroyed='N' WHERE " .
                                    "player_id=? AND ship_id=?", array($start_armor, $start_energy, $start_fighters, $start_pod, $start_scoop, $playerinfo['player_id'], $playerinfo['currentship']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET times_dead=times_dead+1 " .
                                    "WHERE player_id=?", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        if ($spy_success_factor)
        {
            spy_ship_destroyed($db,$playerinfo['currentship'],0);
        }

        echo $l_login_died1 . "<a href=\"main.php\">" . $l_login_died2 . "</a>";
    }
    else
    {
        echo "<br><br>" . $l_global_died1 . "<br><br><a href=\"log.php\">" . $l_global_died2 . "</a><br>";

        // Check if $newbie_nice is set, if so, verify ship limits
        if ($newbie_nice == "YES" || $always_reincarnate || $playerinfo['acl'] >= 255)
        {
            $debug_query = $db->Execute("SELECT hull, engines, power, computer, sensors, armor, shields, beams, torp_launchers, " .
                                        "cloak FROM {$db->prefix}ships WHERE player_id=? AND " .
                                        "ship_id=? AND hull<=? AND " .
                                        "engines<=? AND power<=? AND " .
                                        "computer<=? AND sensors<=? AND " .
                                        "armor<=? AND shields<=? AND " .
                                        "beams<=? AND torp_launchers<=? AND " .
                                        "cloak<=?", array($playerinfo['player_id'], $playerinfo['currentship'], $newbie_hull, $newbie_engines, $newbie_power, $newbie_computer, $newbie_sensors, $newbie_armor, $newbie_shields, $newbie_beams, $newbie_torp_launchers, $newbie_cloak));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $num_rows = $debug_query->RecordCount();

            if ($num_rows || $always_reincarnate)
            {
                echo "<br><br>$l_login_newbie<br><br>";              
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET hull=0,engines=0,pengines=0,power=0,computer=0,sensors=0,beams=0, torp_launchers=0,torps=0,armor=0,armor_pts=$start_armor, cloak=0,shields=0,sector_id=1,ore=0,organics=0,energy=$start_energy, colonists=0,goods=0,fighters=$start_fighters, on_planet='N',dev_warpedit=0,dev_genesis=0,dev_emerwarp=0,dev_escapepod='$start_pod', dev_fuelscoop='$start_scoop', dev_minedeflector=0,destroyed='N' WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]", array($start_armor, $start_energy, $start_fighters, $start_pod, $start_scoop, $playerinfo['player_id'], $playerinfo['currentship']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=1000 WHERE player_id=?", array($playerinfo['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                if ($spy_success_factor)
                {
                    spy_ship_destroyed($db,$playerinfo['currentship'],0);
                }

                echo "<a href=\"main.php\">" . $l_login_newlife . "</a>";
            }
            else
            {
                echo "<br>" . $l_login_looser . "<br><br>";
            }

        } // End if $newbie_nice
        else
        {
            echo "<br>" . $l_login_looser . "<br><br>";
        }
    }
}

include_once ("./footer.php");
?>
