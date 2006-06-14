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
// File: check_defenses.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Dynamic functions
dynamic_loader ($db, "distribute_toll.php");
dynamic_loader ($db, "playerdeath.php");
dynamic_loader ($db, "message_defense_owner.php");
dynamic_loader ($db, "explode_mines.php");
dynamic_loader ($db, "log_move.php");
dynamic_loader ($db, "scan_success.php");
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'check_fighters');
load_languages($db, $raw_prefix, 'check_mines');
load_languages($db, $raw_prefix, 'common');

global $ok;
if (isset($_GET['ok']))
{
    $ok = $_POST['ok'];
}
elseif (isset($_POST['ok']))
{
    $ok = $_POST['ok'];
}
elseif (!isset($ok))
{
    $ok = 0;
}

if (!isset($sector))
{
    if (isset($_POST['sector']))
    {
        $sector = $_POST['sector'];
    }

    if (isset($_GET['sector']))
    {
        $sector = $_GET['sector'];
    }
}

if (!isset($destination))
{
    if (isset($_POST['destination']))
    {
        $destination = $_POST['destination'];
    }

    if (isset($_GET['destination']))
    {
        $destination = $_GET['destination'];
    }
}

if (!isset($_POST['response']))
{
    $_POST['response'] = '';
}

if (!isset($called_from))
{
    $called_from = '';
}

if (!isset($destination))
{
    $destination = '';
}

if (!isset($engage))
{
    $engage = '';
}

// Put the defense information into the array "defenseinfo"
$num_defenses = 0;
$total_sector_fighters = 0;
$total_sector_mines = 0;
$result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id='?' and player_id!='?' ORDER BY quantity DESC", array($destination, $playerinfo['player_id']));

if ($result3 > 0)
{
    while (!$result3->EOF)
    {
        $row = $result3->fields;
        $defenses[$num_defenses] = $row;
        if ($defenses[$num_defenses]['defense_type'] == 'F')
        {
            $total_sector_fighters += $defenses[$num_defenses]['quantity'];
        }
        elseif ($defenses[$num_defenses]['defense_type'] == 'M')
        {
            $total_sector_mines += $defenses[$num_defenses]['quantity'];
        }

        $num_defenses++;
        $result3->MoveNext();
    }
}

if ($num_defenses > 0 && $total_sector_fighters > 0)
{
    // Are the fighter owner and player are on the same team?
    // All sector defenses must be owned by members of the same team.
    $fm_owner = $defenses[0]['player_id'];
    $result2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id='?'", array($fm_owner));
    $fighters_owner = $result2->fields;

    if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
    {
        switch ($_POST['response']) 
        {
            case "fight":
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id='?'", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "<h1>" . $title. "</h1>\n";
                echo $destination;
                include_once ("./sector_fighters.php");
                break;

            case "retreat":
                if ($called_from == 'rsmove.php')
                {
                    $shipspeed = $shipinfo['engines'];
                    $triptime = abs(round(($rs_difficulty/$galaxy_size) * ($distance / $shipspeed)) -8); // 8 just makes sure at high levels that it levels out better.

                    $turns_back = $triptime * 2;
                    if ($turns_back == 0)
                    {
                        $turns_back = 2;
                    }
                }
                elseif ($called_from == 'move.php')
                {
                    $turns_back = 2; // Warp
                }
                elseif ($called_from == 'plasmamove.php' && $plasma_engines)
                {
                    $turns_back = 2; // Plasma
                }

                // Todo: If we don't have enough turns for BOTH moves (forth+back) then we echo a "you cant retreat", and force it forward.

                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses = ' ' WHERE ship_id ='?'", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-'?', turns_used=turns_used+'?' WHERE player_id='?'", array($turns_back, $turns_back, $playerinfo['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id='?' WHERE ship_id='?'", array($shipinfo['sector_id'], $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "<h1>" . $title. "</h1>\n";
                echo "$l_chf_youretreatback<br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                include_once ("./footer.php");
                die();
                break;

            case "pay":
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses = ' ' WHERE ship_id ='?'", $shipinfo['ship_id']);
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
                if ($playerinfo['credits'] < $fighterstoll)
                {
                    if ($called_from == 'rsmove.php')
                    {
                        $shipspeed = $shipinfo['engines'];
                        $triptime = abs(round(($rs_difficulty/$galaxy_size) * ($distance / $shipspeed)) -8); // 8 just makes sure at high levels that it levels out better.
                        $turns_back = 2 * $triptime;
                        if ($turns_back == 0)
                        {
                            $turns_back = 2;
                        }
                    }
                    else
                    {
                        $turns_back = 2; // Warp
                    }

                    echo "$l_chf_notenoughcreditstoll<br>";
                    echo "$l_chf_movefailed<br>";
                    // undo the move
                    // Todo: what happens if we don't have enough turns for BOTH moves (forth+back)?? Destroy the ship? Order him to wait turns?
   
                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses = ' ' WHERE ship_id ='?'", array($shipinfo['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-'?', turns_used=turns_used+'?' WHERE player_id='?'", array($turns_back, $turns_back, $playerinfo['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id='?' WHERE ship_id='?'", array($shipinfo['sector_id'], $shipinfo['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                    $ok = 0;
                }
                else
                {
                    $tollstring = number_format($fighterstoll, 0, $local_number_dec_point, $local_number_thousands_sep);
                    $l_chf_youpaidsometoll = str_replace("[chf_tollstring]", $tollstring, $l_chf_youpaidsometoll);
                    echo "$l_chf_youpaidsometoll<br>";
                    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits-'?' WHERE player_id='?'", array($fighterstoll, $playerinfo['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    distribute_toll($db, $destination,$fighterstoll,$total_sector_fighters);
                    playerlog($db,$playerinfo[player_id], "LOG_TOLL_PAID", "$tollstring|$destination");
                    $ok = 1;
                 }
                 break;

            case "sneak":
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses = ' ' WHERE ship_id='?'", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $res = $db->Execute("SELECT sensors FROM {$db->prefix}ships WHERE player_id='?'", array($fm_owner));
                $sensors = $res->fields['sensors'];

                $success = scan_success($sensors, $shipinfo['cloak']);
                if ($success < 5)
                {
                    $success = 5;
                }

                if ($success > 95)
                {
                    $success = 95;
                }

                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    // sector defenses detect incoming ship
                    echo "<h1>" . $title. "</h1>\n";
                    echo "$l_chf_thefightersdetectyou<br>";
                    include_once ("./sector_fighters.php");
                    break;
                }
                else
                {
                    // sector defenses don't detect incoming ship
                    $ok = 1;
                }
                break;

            default:
                if ($called_from == "rsmove.php")
                {
                    $move_method = "real";
                }
                elseif ($called_from == "move.php")
                {
                    $move_method = "warp";
                }
                elseif ($called_from == "plasmamove.php" && $plasma_engines)
                {
                    $move_method = "plasma";
                }

                $face_string = 'move.php?sector='.$sector.'&destination='.$destination.'&engage='.$engage.'&move_method='.$move_method;
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses = '?' WHERE ship_id ='?'", array($face_string, $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
                echo "<h1>" . $title. "</h1>\n";
                echo '<form name="bntform" action="move.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
                $l_chf_therearetotalfightersindest = str_replace("[chf_total_sector_fighters]", $total_sector_fighters, $l_chf_therearetotalfightersindest);
                echo "$l_chf_therearetotalfightersindest<br>";
                 
                if ($defenses[0]['fm_setting'] == "toll")
                {
                    $l_chf_creditsdemanded = str_replace("[chf_number_fighterstoll]", number_format($fighterstoll, 0, $local_number_dec_point, $local_number_thousands_sep), $l_chf_creditsdemanded);
                    echo "$l_chf_creditsdemanded<br>";
                }

                global $l_chf_yocanretreat2;
                echo "<input type=radio name=response value=retreat><strong>" . $l_chf_youcanretreat1 . "</strong>" . $l_chf_yocanretreat2 . "<br></input>";
                if ($defenses[0]['fm_setting'] == "toll")
                {
                    echo "<input type=radio name=response checked=\"checked\" value=pay><strong>" . $l_chf_inputpay1 . "</strong>" . $l_chf_inputpay2 . "<br></input>";
                }
                 
                echo "<input type=radio name=response checked=\"checked\" value=fight><strong>" . $l_chf_inputfight1 . "</strong>" . $l_chf_inputfight2 . "<br></input>";
                echo "<input type=radio name=response checked=\"checked\" value=sneak><strong>" . $l_chf_inputcloak1 . "</strong>" . $l_chf_inputcloak2 . "<br></input>";
                echo "<br>";
                echo "<input type=submit name=submit_button value=$l_chf_go><br><br>";
                if ($_GET['move_method'] == 'real' || $_POST['move_method'] == 'real')
                {
                    echo "<input type=hidden name=move_method value=real>";
                }
                elseif ($_GET['move_method'] == 'warp' || $_POST['move_method'] == 'warp')
                {
                    echo "<input type=hidden name=move_method value=warp>";
                }
                elseif (($_GET['move_method'] == 'plasma' || $_POST['move_method'] == 'plasma') && $plasma_engines)
                {
                    echo "<input type=hidden name=move_method value=plasma>";
                }
//                echo "<input type=hidden name=sector value=$sector>";
                echo "<input type=hidden name=engage value=1>";
                echo "<input type=hidden name=destination value=$destination>";
                echo "</form>";
                include_once ("./footer.php");
                die();
                break;
        }

        // clean up any sectors that have used up all mines or fighters
        $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE quantity <= 0 ");
    }
}

if ($ok > 0)
{
    $source_sector = $shipinfo['sector_id'];
    if ($called_from == "move.php")
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 WHERE player_id='?'", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id='?' WHERE ship_id='?'", array($destination, $shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
    elseif ($called_from == "rsmove.php")
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-'?', turns_used=turns_used+'?' WHERE player_id='?'", array($triptime, $triptime, $playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id='?', energy=energy+'?' WHERE ship_id='?'", array($destination, $energyscooped, $shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $l_rs_ready = str_replace("[sector]",$destination,$l_rs_ready);
        $l_rs_ready = str_replace("[triptime]", number_format($triptime, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_ready);
        $l_rs_ready = str_replace("[energy]", number_format($energyscooped, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_ready);
        echo "$l_rs_ready<br><br>";
    }
    elseif ($called_from == "plasmamove.php" && $plasma_engines)
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-'?',turns_used=turns_used+'?' WHERE player_id='?'", array($triptime, $triptime, $playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id='?',energy=energy-'?' WHERE ship_id='?'", array($destination, $plasmacost, $shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $l_plasma_ready = str_replace("[sector]",$destination,$l_plasma_ready);
        $l_plasma_ready = str_replace("[triptime]", number_format($triptime, 0, $local_number_dec_point, $local_number_thousands_sep),$l_plasma_ready);
        $l_plasma_ready = str_replace("[energy]", number_format($plasmacost, 0, $local_number_dec_point, $local_number_thousands_sep),$l_plasma_ready);
        echo "$l_plasma_ready<br><br>";
    }

    log_move($db, $playerinfo['player_id'],$shipinfo['ship_id'],$source_sector,$destination,$shipinfo['class'],$shipinfo['cloak']);
}

// Easter egg comment - She looks like one of those rap guys girlfriends.. I mean her butt, its just so .. BIG!

if ($num_defenses > 0 && $total_sector_mines > 0 && ($shipinfo['hull'] > $mine_hullsize))
{
    $fm_owner = $defenses[0]['player_id'];
    $result2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id='?'", array($fm_owner));
    $mine_owner = $result2->fields;

    if ($mine_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0) // Are the mine owner and player are on the same team?
    {
        // Lets blow up some mines!
        echo "<h1>" . $title. "</h1>\n";
        $ok = 0;
        $totalmines = $total_sector_mines;
        if ($totalmines > 1)
        {
            $roll = mt_rand(0,100); // Get a random percentage of the mines to attack
        }
        else
        {
            $roll = 1;
        }

        $roll = round(($roll / 100) * $totalmines);
        $l_chm_youhitsomemines = str_replace("[chm_roll]", $roll, $l_chm_youhitsomemines);
        echo "$l_chm_youhitsomemines<br>";
        playerlog($db,$playerinfo['player_id'], "LOG_HIT_MINES", "$roll|$destination");
 
        $l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", $playerinfo['character_name'], $l_chm_hehitminesinsector);
        $l_chm_hehitminesinsector = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector);
        $l_chm_hehitminesinsector = str_replace("[chm_sector]", $destination, $l_chm_hehitminesinsector);
        message_defense_owner($db, $destination,"$l_chm_hehitminesinsector");
 
        if ($shipinfo['dev_minedeflector'] >= $roll)
        {
            $l_chm_youlostminedeflectors = str_replace("[chm_roll]", $roll, $l_chm_youlostminedeflectors);
            echo "$l_chm_youlostminedeflectors<br>";
            $debug_query = $db->Execute("UPDATE {$db->prefix}ships set dev_minedeflector=dev_minedeflector-'?' WHERE ship_id='?'", array($roll, $shipinfo['ship_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
        else
        {
            if ($shipinfo['dev_minedeflector'] > 0)
            {
                echo "$l_chm_youlostallminedeflectors<br>";
            }
            else
            {
                echo "$l_chm_youhadnominedeflectors<br>";
            }
 
            $mines_left = $roll - $shipinfo['dev_minedeflector'];
            $playershields = num_level($shipinfo['shields'], $level_factor, $level_magnitude);
               
            if ($playershields > $shipinfo['energy'])
            {
                $playershields = $shipinfo['energy'];
            }
 
            if ($playershields >= $mines_left)
            {
                $l_chm_yourshieldshitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourshieldshitforminesdmg);
                echo "$l_chm_yourshieldshitforminesdmg<br>";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships set energy=energy-'?', dev_minedeflector=0 WHERE ship_id='?'", array($mines_left, $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                if ($playershields == $mines_left) 
                {
                    echo "$l_chm_yourshieldsaredown<br>";
                }
            }
            else
            {
                echo "$l_chm_youlostallyourshields<br>";
                $mines_left = $mines_left - $playershields;
                if ($shipinfo['armor_pts'] >= $mines_left)
                {
                    $l_chm_yourarmorhitforminesdmg = str_replace("[chm_mines_left]", $mines_left,$l_chm_yourarmorhitforminesdmg);
                    echo "$l_chm_yourarmorhitforminesdmg<br>";
                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET armor_pts=armor_pts-'?', energy=0, dev_minedeflector=0 WHERE ship_id='?'", array($mines_left, $shipinfo['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                    if ($shipinfo['armor_pts'] == $mines_left) 
                    {
                        echo "$l_chm_yourhullisbreached<br>";
                    }
                }
                else
                {
                    $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]",$playerinfo['character_name'], $l_chm_hewasdestroyedbyyourmines);
                    $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", "<a href=move.php?move_method=real&engage=1&destination=$destination>$destination</a>",$l_chm_hewasdestroyedbyyourmines);
                    message_defense_owner($db, $destination,"$l_chm_hewasdestroyedbyyourmines");
                    echo "$l_chm_yourshiphasbeendestroyed<br><br>";

                    playerdeath($db,$playerinfo['player_id'], "LOG_SHIP_DESTROYED_MINES", "$destination|$shipinfo[dev_escapepod]",0,0,$shipinfo['ship_id']);
                }
            }
        }
        explode_mines($db,$destination,$roll);
    }
}

if ($ok == 1)
{
    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
    {
        $add_slash_to_url = '/';
    }

    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>.";

    $server_port = '';
    if ($_SERVER['SERVER_PORT'] != '80')
    {
        $server_port = ':' . $_SERVER['SERVER_PORT'];
    }

    // No click/refresh - seems smoother.
    header("Location: " . $server_type . "://" . $_SERVER['SERVER_NAME'] . $server_port . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "main.php");
    die();
}
else
{
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
}

?>
