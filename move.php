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
// File: move.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "calc_dist.php"); 
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'navcomp');
load_languages($db, $raw_prefix, 'move');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

if (!isset($_GET['move_method']))
{
    $_GET['move_method'] = '';
}

if (!isset($_POST['move_method']))
{
    $_POST['move_method'] = '';
}

if ($_POST['move_method'] == 'real' || $_GET['move_method'] == 'real' )
{
    $title = $l_rs_title;
}
elseif ($_POST['move_method'] == 'plasma' || $_GET['move_method'] == 'plasma' )
{
    $title = $l_plasma_title;
}
elseif ($_POST['move_method'] == 'warp' || $_GET['move_method'] == 'warp' )
{
    $title = $l_move_title;
}
elseif ($_POST['move_method'] == 'navcomp')
{
    $title = $l_nav_title;
}
else
{
    echo "ERROR! - Unknown movement type!!!<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

updatecookie($db);
include_once ("./header.php");

if (!isset($_GET['sector']))
{
    $_GET['sector'] = '';
}

$destination = '';
$engage = '';

if (isset($_GET['destination']))
{
    $destination = $_GET['destination'];
}

if (isset($_POST['destination']))
{
    $destination = $_POST['destination'];   
}

if (isset($_GET['engage']))
{
    $engage = $_GET['engage'];
}

if (isset($_POST['engage']))
{
    $engage = $_POST['engage'];   
}

if (isset($_POST['go']))
{
    $engage = 1;
}

// Check to see if the player has less than one turn available
// and if so return to the main menu
if ($playerinfo['turns']<1)
{
    echo "$l_move_turn<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($_GET['move_method'] == 'real' || $_POST['move_method'] == 'real')
{
    //-------------------------------------------------------------------------------------------------

    if ($destination == '' || ($destination > $sector_max) || ($destination <1))
    {
        echo "$l_rs_invalid.<br><br>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
    else
    {
        $destination = preg_replace('/[^0-9]/','',$destination);

        if ($destination <= $sector_max)
        {
            $distance = calc_dist($db, $shipinfo['sector_id'],$destination);
            $shipspeed = $shipinfo['engines']; // 101 is the max_engines size + 1.
            if ($shipspeed < 1)             
            {    
                $shipspeed = 1;
            }

            // rs_difficulty is divided by the galaxy size to ensure that the equation is roughly the same regardless
            // of galaxy size. This ensures that low engines will get you nowhere, and high engines will be needed
            // for the longest trips.
            $triptime = abs(round(($rs_difficulty/$galaxy_size) * ($distance / $shipspeed)) -8); // 8 just makes sure at high levels that it levels out better.

            if (($triptime == 0 && $destination != $shipinfo['sector_id']) || ($shipinfo['engines'] == 100)) // 100 is the new max.
            {
                $triptime = 1;
            }

            if ($shipinfo['dev_fuelscoop'] == "Y")
            {
                $energyscooped = $distance * 100;
            }
            else
            {
                $energyscooped = 0;
            }

            if ($shipinfo['dev_fuelscoop'] == "Y" && $energyscooped == 0 && $triptime == 1)
            {
                $energyscooped = 100;
            }

            $free_power = (5 * num_level(($shipinfo['power']) - $shipinfo['energy'], $level_factor, $level_magnitude));

            if ($free_power < $energyscooped)
            {
                $energyscooped = $free_power;
            }

            if (!isset($energyscooped))
            {
                $energyscooped = 0;
            }

            if ($energyscooped < 1)
            {
                $energyscooped = 0;
            }

            if ($destination == $shipinfo['sector_id'])
            {
                $triptime = 0;
                $energyscooped = 0;
            }

            $debug_query = $db->Execute("SELECT zone_id FROM {$db->prefix}universe WHERE sector_id=?", array($destination));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $dest_zone = $debug_query->fields['zone_id'];

            $debug_query = $db->Execute("SELECT max_level FROM {$db->prefix}zones WHERE zone_id=?", array($dest_zone));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $dest_max_level = $debug_query->fields['max_level'];

            $combat_levels = round(($shipinfo['computer'] + $shipinfo['torp_launchers'] + $shipinfo['beams']) / 3);
            if ($combat_levels > $dest_max_level && $dest_max_level > 0)
            {
                echo $l_max_level_move . "<br>";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            elseif ($triptime > $playerinfo['turns'])
            {
                $l_rs_movetime=str_replace("[triptime]",number_format($triptime, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_movetime);
                echo "$l_rs_movetime<br><br>";
                echo "$l_rs_noturns";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            elseif ($engage == 1)
            {
                $ok = 1;
                $sector = $destination;
                $called_from = "rsmove.php";
                include_once ("./check_defenses.php");
            }
            else
            {
                $l_rs_movetime=str_replace("[triptime]",number_format($triptime, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_movetime);
                $l_rs_energy=str_replace("[energy]",number_format($energyscooped, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_energy);
                echo "$l_rs_movetime $l_rs_energy<br><br>";
            
                $l_rs_engage_link= "<a href=move.php?move_method=real&amp;engage=1&amp;destination=$destination>" . $l_rs_engage_link . "</a>";
                $l_rs_engage=str_replace("[turns]",number_format($playerinfo['turns'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_engage);
                $l_rs_engage=str_replace("[engage]",$l_rs_engage_link,$l_rs_engage);
                echo "$l_rs_engage<br><br>";
            }
        }
    }

    //-------------------------------------------------------------------------------------------------
}
elseif ($_GET['move_method'] == 'warp' || $_POST['move_method'] == 'warp')
{
    // Retrive all the warp links out of the current sector
    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}links WHERE link_start=?", array($shipinfo['sector_id']));
    $i = 0;
    $flag = 0;

    if ($result3 > 0)
    {
        // Loop through the available warp links to make sure it's a valid move
        while (!$result3->EOF)
        {
            $row = $result3->fields;
            if ($row['link_dest'] == $destination && $row['link_start'] == $shipinfo['sector_id'])
            {
                $flag = 1;
            }
            $i++;
            $result3->MoveNext();
        }
    }
    else
    {
        // TODO: Add 'link disappeared' text, as entropy will soon make this common.
    }

    $debug_query = $db->Execute("SELECT zone_id FROM {$db->prefix}universe WHERE sector_id=?", array($destination));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $dest_zone = $debug_query->fields['zone_id'];

    $debug_query = $db->Execute("SELECT max_level FROM {$db->prefix}zones WHERE zone_id=?", array($dest_zone));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $dest_max_level = $debug_query->fields['max_level'];

    $combat_levels = round(($shipinfo['computer'] + $shipinfo['torp_launchers'] + $shipinfo['beams']) / 3);
    if ($combat_levels > $dest_max_level && $dest_max_level > 0)
    {
        echo $l_max_level_move . "<br>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
    elseif ($flag == 1)   // Check if there was a valid warp link to move to
    {
        $ok = 1;
        $called_from = "move.php";
        include_once ("./check_defenses.php");
    }
    else
    {
        // TODO: Add 'link disappeared' text, as entropy will soon make this common.
    }
}
elseif (($_GET['move_method'] == 'plasma' || $_POST['move_method'] == 'plasma') && $plasma_engines)
{
    $destination = '';
    $engage = '';

    if (isset($_GET['destination']))
    {
        $destination = $_GET['destination'];
    }

    if (isset($_POST['destination']))    
    {
        $destination = $_POST['destination'];   
    }

    if (isset($_GET['engage']))
    {
        $engage = $_GET['engage'];
    }

    if (isset($_POST['engage']))    
    {
        $engage = $_POST['engage'];   
    }

    if (isset($_POST['go']))
    {
        $engage = 1;
    }

    //-------------------------------------------------------------------------------------------------

    if ($destination == '' || ($destination > $sector_max))
    {
        echo "$l_rs_invalid.<br><br>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
    else
    {
        $destination = preg_replace('/[^0-9]/','',$destination);

        if ($destination <= $sector_max)
        {
            $distance = calc_dist($db, $shipinfo['sector_id'],$destination);
            $shipspeed = pow($level_factor, $shipinfo['pengines']);
            if ($shipspeed < 1)
            {
                $shipspeed = 1;
            }

            // This is complicated. First, we have the max carryable amount of energy - num_level(100) * 5.
            // Thats the maximum amount of 'fuel' they are carrying. So, we divide the max amount of fuel by the shipspeed,
            // which means that 'bigger' engines are more efficient. 
            // Next we multiply that by the distance the ship is going. Because universe size varies, we have to 'normalize'
            // the equation so that the engines will match the formula we used to get these numbers. Basically, thats *50,
            // entirely due to the power equation being *50. 
            // Validity checks: level 0 for distance 50,000 is in the hundred-billions. Cost for lvl 100 is < 10k.

            $plasmacost =   round((5 * num_level(100, $level_factor, $level_magnitude)) / $shipspeed) * ($distance/($galaxy_size/50000 * 1000));
            if ($plasmacost < 1)
            {
                $plasmacost = 1;
            }

            $triptime = 1;

            $free_power = (5 * num_level($shipinfo['power'], $level_factor, $level_magnitude)) - $shipinfo['energy'];

            if ($destination == $shipinfo['sector_id'])
            {
                $triptime = 0;
            }

            $debug_query = $db->Execute("SELECT zone_id FROM {$db->prefix}universe WHERE sector_id=?", array($destination));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $dest_zone = $debug_query->fields['zone_id'];

            $debug_query = $db->Execute("SELECT max_level FROM {$db->prefix}zones WHERE zone_id=?", array($dest_zone));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $dest_max_level = $debug_query->fields['max_level'];

            $combat_levels = round(($shipinfo['computer'] + $shipinfo['torp_launchers'] + $shipinfo['beams']) / 3);
            if ($combat_levels > $dest_max_level && $dest_max_level > 0)
            {
                echo $l_max_level_move . "<br>";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            elseif ($triptime > $playerinfo['turns'])
            {
                $l_rs_movetime=str_replace("[triptime]",number_format($triptime, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_movetime);
                echo "$l_rs_movetime<br><br>";
                echo "$l_rs_noturns";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            elseif ($plasmacost > $shipinfo['energy'])
            {
                $l_plasma_energy_need = str_replace("[energy]",number_format($plasmacost, 0, $local_number_dec_point, $local_number_thousands_sep),$l_plasma_energy_need);
                echo $l_plasma_energy_need."<br>";
                echo $l_plasma_not_enuf."<br>";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=?", array($shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            elseif ($engage == 1)
            {
                $ok = 1;
                $sector = $destination;
                $called_from = "plasmamove.php";
                include_once ("./check_defenses.php");
            }
            else
            {
                $l_plasma_energy_need=str_replace("[energy]",number_format($plasmacost, 0, $local_number_dec_point, $local_number_thousands_sep),$l_plasma_energy_need);
                echo $l_plasma_energy_need ."<br><br>";
            
                $l_rs_engage_link= "<a href=move.php?move_method=plasma&amp;engage=1&amp;destination=$destination>" . $l_rs_engage_link . "</a>";
                $l_plasma_engage=str_replace("[energy]",number_format($shipinfo['energy'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_plasma_engage);
                $l_plasma_engage=str_replace("[engage]",$l_rs_engage_link,$l_plasma_engage);
                echo "$l_plasma_engage<br><br>";
            }
        }
    }

    //-------------------------------------------------------------------------------------------------
}
elseif ($_POST['move_method'] == 'navcomp')
{
    include_once ("./navcomp.php");
}

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");

?>
