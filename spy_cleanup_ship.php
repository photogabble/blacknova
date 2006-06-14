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
// File: spy_cleanup_ship.php

include_once ("./global_includes.php");

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "calc_ship_cleanup_cost.php");

// Load language variables
load_languages($db, $raw_prefix, 'spy');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'main');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_spy_title;
echo "<h1>" . $title. "</h1>\n";
updatecookie($db);

seed_mt_rand();

if (!$spy_success_factor)
{
    echo "<strong>$l_spy_disabled</strong><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if (!isset($_POST['doit']))
{
    $_POST['doit'] = '';
}
else
{
    $doit = $_POST['doit'];
}

if (!isset($_GET['doit']))
{
    $_GET['doit'] = '';
}
else
{
    $doit = $_GET['doit'];
}

if (!isset($_GET['command']))
{
    $_GET['command'] = '';
}
else
{
    $command = $_GET['command'];
}

if (!isset($_POST['command']))
{
    $_POST['command'] = '';
}
else
{
    $command = $_POST['command'];
}

if (!isset($by))
{
    $by = '';
}

if (!isset($by1))
{
    $by1 = '';
}

if (!isset($by2))
{
    $by2 = '';
}

if (!isset($by3))
{
    $by3 = '';
}

if (!isset($_POST['planet_id']))
{
    $_POST['planet_id'] = '';
}
else
{
    $planet_id = $_POST['planet_id'];
}

if (!isset($_GET['planet_id']))
{
    $_GET['planet_id'] = '';
}
else
{
    $planet_id = $_GET['planet_id'];
}

if (!isset($planet_id))
{
    $planet_id = '-1';
}

if (!isset($spy_id))
{
    $spy_id = '-1';
}

if (!isset($dismiss))
{
    $dismiss = '';
}

$line_color = $color_line2;

// Trying to find enemy spies on my ship

// This is a really cheesy solution, but hey, it works for now, and gets rid of global_variables!
$spy_cleanup_ship_turns[1] = $spy_cleanup_ship_turns1;
$spy_cleanup_ship_turns[2] = $spy_cleanup_ship_turns2;
$spy_cleanup_ship_turns[3] = $spy_cleanup_ship_turns3;

    echo "<strong>$l_spy_cleanupshiptitle</strong><br>";
    if ($portinfo['port_type']!="devices")
    {
        echo "<br>$l_spy_notinspecial<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");    
        die();
    }

    if ($plasma_engines)
    {
        $level_avg = $shipinfo['hull'] + $shipinfo['engines'] + $shipinfo['pengines'] + $shipinfo['computer'] + $shipinfo['beams'] + $shipinfo['torp_launchers'] + $shipinfo['shields'] + $shipinfo['armor'];
        $level_avg /=8;
    }
    else
    {
        $level_avg = $shipinfo['hull'] + $shipinfo['engines'] + $shipinfo['computer'] + $shipinfo['beams'] + $shipinfo['torp_launchers'] + $shipinfo['shields'] + $shipinfo['armor'];
        $level_avg /=7;
    }

    for ($a=1; $a<=3; $a++)
    {
        $spy_cleanup_ship_credits[$a] = calc_ship_cleanup_cost($level_avg,$a);
        $spycleantext = "l_spy_cleanuptext_" . $a;
        global $$spycleantext;
        $new_spy_clean = $$spycleantext;

        $l_spy_cleanuptext[$a] = str_replace("[creds]", number_format($spy_cleanup_ship_credits[$a], 0, $local_number_dec_point, $local_number_thousands_sep), $new_spy_clean);
        $l_spy_cleanuptext[$a] = str_replace("[turns]", number_format($spy_cleanup_ship_turns[$a], 0, $local_number_dec_point, $local_number_thousands_sep), $new_spy_clean);
    }
  
    if ($playerinfo['credits'] < $spy_cleanup_ship_credits[1] || $playerinfo['turns'] < $spy_cleanup_ship_turns[1])
    {
        $set[1] = "DISABLED";
    }
    else
    {
        $set[1] = "checked";
    }
 
    if ($playerinfo['credits'] < $spy_cleanup_ship_credits[2] || $playerinfo['turns'] < $spy_cleanup_ship_turns[2])
    {
        $set[2] = "DISABLED";
    }
    elseif ($set[1] == "checked")
    {
        $set[2] = "";
    }
    else
    {
        $set[2] = "checked";
    }

    if ($playerinfo['credits'] < $spy_cleanup_ship_credits[3] || $playerinfo['turns'] < $spy_cleanup_ship_turns[3])
    {
        $set[3] = "DISABLED";
    }
    elseif ($set[1] == "checked" || $set[2] == "checked")
    {
        $set[3] = "";
    }
    else
    {
        $set[3] = "checked";
    }
  
    if (empty($doit))
    { 
        echo '<form name="bntform" action="spy_cleanup_ship.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
        echo "<input type=hidden name=doit value=1>";
        echo "<input type=radio name=type value=1 $set[1]> $l_spy_cleanuptext_1<br>";
        echo "<input type=radio name=type value=2 $set[2]> $l_spy_cleanuptext_2<br>";
        echo "<input type=radio name=type value=3 $set[3]> $l_spy_cleanuptext_3<br><br>";
    
        if ($set[1] == "DISABLED" && $set[2] == "DISABLED" && $set[3] == "DISABLED")
        {
            $l_spy_cannotcleanupship = str_replace("[credits]" , number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep), $l_spy_cannotcleanupship);
            $l_spy_cannotcleanupship = str_replace("[turns]" , number_format($playerinfo['turns'], 0, $local_number_dec_point, $local_number_thousands_sep), $l_spy_cannotcleanupship);
            echo $l_spy_cannotcleanupship;
        }
        else
        {
            echo "<input type=submit name=submit_button value=\"$l_spy_cleanupbutton2\">";
        }
    
        echo "</form>";
    }
    else
    {
        echo "<br>$l_spy_cleanupshiptitle2<br><br>";
        if ($type != 1 && $type != 2 && $type != 3)
        {
            $type = 1;
        }
      
        if ($set[$type] != "DISABLED") 
        {  
            $found = 0;
            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns_used=turns_used+$spy_cleanup_ship_turns[$type], turns=turns-$spy_cleanup_ship_turns[$type], credits=credits-$spy_cleanup_ship_credits[$type] WHERE player_id=$playerinfo[player_id] ");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}ships.cloak, {$db->prefix}players.character_name FROM {$db->prefix}ships INNER JOIN {$db->prefix}players ON {$db->prefix}ships.player_id = {$db->prefix}players.player_id INNER JOIN {$db->prefix}spies ON {$db->prefix}players.player_id={$db->prefix}spies.owner_id WHERE {$db->prefix}spies.ship_id=$shipinfo[ship_id] AND {$db->prefix}spies.active='Y' AND {$db->prefix}spies.planet_id='0'");
            while (!$res->EOF)
            {
                $info = $res->fields;
                if ($type == 1)
                {
                    $success = (5 + $shipinfo['sensors'] - $info['cloak']) * 5;
                    if ($success < 10)
                    {
                        $success = 10;
                    }

                    if ($success > 60)
                    {
                        $success = 60;
                    }
                }
                elseif ($type == 2)
                {
                    $success = (11 + $shipinfo['sensors'] - $info['cloak']) * 5;
                    if ($success < 25)
                    {
                        $success = 25;
                    }

                    if ($success > 77)
                    {
                        $success = 77;
                    }
                }
                else
                {
                    $success = (14 + 1.1 * $shipinfo['sensors'] - $info['cloak']) * 5;
                    if ($success < 40)
                    {
                        $success = 40;
                    }

                    if ($success > 95)
                    {
                        $success = 95;
                    }
                }

                $roll = mt_rand(1,100);
                if ($roll < $success)
                {
                    $found = 1;
                    $l_spy_spyfoundonship2 = str_replace("[player]", "<strong>$info[character_name]</strong>", $l_spy_spyfoundonship);
                    $l_spy_spyfoundonship2 = str_replace("[spyid]", "<strong>$info[spy_id]</strong>", $l_spy_spyfoundonship2);
                    echo "$l_spy_spyfoundonship2<br>";
                    $res2 = $db->Execute("DELETE FROM {$db->prefix}spies WHERE spy_id=$info[spy_id]");
                    playerlog($db,$info['owner_id'], "LOG_SHIPSPY_KILLED", "$info[spy_id]|$playerinfo[character_name]|$shipinfo[name]");
                }
                $res->MoveNext();
            }
   
            if (!$found)
            {
                echo "$l_spy_spynotfoundonship<br>";
            }
        }  
        else
        {
            echo "<br>$l_spy_notenough<br>"; 
        }
    }
global $l_global_mmenu;
$template->assign("title", $title);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/spy.tpl");

include_once ("./footer.php");
?>
