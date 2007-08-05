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
// File: sector_fighters.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Dynamic functions
dynamic_loader ($db, "playerdeath.php");
dynamic_loader ($db, "message_defense_owner.php");
dynamic_loader ($db, "destroy_fighters.php");
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'sector_fighters');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

echo $l_sf_attacking . "<br>";
$targetfighters = $total_sector_fighters;
$playerbeams = num_level($shipinfo['beams'], $level_factor, $level_magnitude);

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

if (!isset($destination)) // If its still not set, it means we are in the same sector.
{
    $destination = $sector;
}

if (!isset($called_from))
{
    $called_from = '';
}

if ($called_from == 'rsmove.php')
{
    $shipinfo['energy'] += $energyscooped;
}

if ($playerbeams > $shipinfo['energy'])
{
    $playerbeams = $shipinfo['energy'];
}

$shipinfo['energy'] = $shipinfo['energy'] - $playerbeams;
$playershields = num_level($shipinfo['shields'], $level_factor, $level_magnitude);

if ($playershields > $shipinfo['energy'])
{  
    $playershields = $shipinfo['energy'];
}

//  $shipinfo['energy']=$shipinfo['energy']-$playershields;

$playertorpnum = round(pow($level_factor,$shipinfo['torp_launchers']))*2;

if ($playertorpnum > $shipinfo['torps'])
{ 
    $playertorpnum = $shipinfo['torps'];
}

$playertorpdmg = $torp_dmg_rate*$playertorpnum;
$playerarmor = $shipinfo['armor_pts'];
$playerfighters = $shipinfo['fighters'];

if ($targetfighters > 0 && $playerbeams > 0)
{
    if ($playerbeams > round($targetfighters / 2))
    {
        $temp = round($targetfighters/2);
        $lost = $targetfighters - $temp;
        $l_sf_destfight = str_replace("[lost]", $lost, $l_sf_destfight);
        echo $l_sf_destfight . "<br>";
        $targetfighters = $temp;
        $playerbeams = $playerbeams-$lost;
    }
    else
    {
        $targetfighters = $targetfighters - $playerbeams;
        $l_sf_destfightb = str_replace("[lost]", $playerbeams, $l_sf_destfightb);
        echo $l_sf_destfightb . "<br>";
        $playerbeams = 0;
    }   
}

echo "<br>$l_sf_torphit<br>";

if ($targetfighters > 0 && $playertorpdmg > 0)
{
    if ($playertorpdmg > round($targetfighters / 2))
    {
        $temp = round($targetfighters/2);
        $lost = $targetfighters - $temp;
        $l_sf_destfightt = str_replace("[lost]", $lost, $l_sf_destfightt);
        echo $l_sf_destfightt . "<br>";
        $targetfighters = $temp;
        $playertorpdmg = $playertorpdmg - $lost;
    }
    else
    {
        $targetfighters = $targetfighters - $playertorpdmg;
        $l_sf_destfightt = str_replace("[lost]", $playertorpdmg, $l_sf_destfightt);
        echo $l_sf_destfightt . "<br>";
        $playertorpdmg = 0;
    }
}

echo "<br>$l_sf_fighthit<br>";

if ($playerfighters > 0 && $targetfighters > 0)
{
    if ($playerfighters > $targetfighters)
    {
        echo $l_sf_destfightall . "<br>";
        $temptargfighters = 0;
    }
    else
    {
        $l_sf_destfightt2 = str_replace("[lost]", $playerfighters, $l_sf_destfightt2);
        echo $l_sf_destfightt2 . "<br>";
        $temptargfighters = $targetfighters - $playerfighters;
    }

    if ($targetfighters > $playerfighters)
    {
        echo $l_sf_lostfight . "<br>";
        $tempplayfighters = 0;
    }
    else
    {
        $l_sf_lostfight2 = str_replace("[lost]", $targetfighters, $l_sf_lostfight2);
        echo $l_sf_lostfight2 . "<br>";
        $tempplayfighters = $playerfighters - $targetfighters;
    }     

    $playerfighters = $tempplayfighters;
    $targetfighters = $temptargfighters;
}

if ($targetfighters > 0)
{
    if ($targetfighters > $playerarmor)
    {
        $playerarmor = 0;
        echo $l_sf_armorbreach . "<br>";
    }
    else
    {
        $playerarmor = $playerarmor - $targetfighters;
        $l_sf_armorbreach2 = str_replace("[lost]", $targetfighters, $l_sf_armorbreach2);
        echo $l_sf_armorbreach2 . "<br>";
    } 
}

$fighterslost = $total_sector_fighters - $targetfighters;

$l_sf_sendlog = str_replace("[player]", $playerinfo['character_name'], $l_sf_sendlog);
$l_sf_sendlog = str_replace("[lost]", $fighterslost, $l_sf_sendlog);
$l_sf_sendlog = str_replace("[sector]", $destination, $l_sf_sendlog);
                 
message_defense_owner($db, $destination,$l_sf_sendlog);
destroy_fighters($db,$destination,$fighterslost);
playerlog($db,$playerinfo['player_id'], "LOG_DEFS_DESTROYED_F", "$fighterslost|$destination");
$armor_lost = $shipinfo['armor_pts'] - $playerarmor;
$fighters_lost = $shipinfo['fighters'] - $playerfighters;
$energy = $shipinfo['energy'];

$debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET energy=?, fighters=fighters-?, armor_pts=armor_pts-?, torps=torps-? WHERE ship_id=?", array($energy, $fighters_lost, $armor_lost, $playertorpnum, $shipinfo['ship_id']));
db_op_result($db,$debug_query,__LINE__,__FILE__);

$l_sf_lreport = str_replace("[armor]", $armor_lost, $l_sf_lreport);
$l_sf_lreport = str_replace("[fighters]", $fighters_lost, $l_sf_lreport);
$l_sf_lreport = str_replace("[torps]", $playertorpnum, $l_sf_lreport);
echo $l_sf_lreport . "<br><br>";

if ($playerarmor < 1)
{
    echo $l_sf_shipdestroyed . "<br><br>";
    $l_sf_sendlog2 = str_replace("[player]", $playerinfo['character_name'], $l_sf_sendlog2);
    $l_sf_sendlog2 = str_replace("[sector]", $destination, $l_sf_sendlog2);
    message_defense_owner($db, $destination,$l_sf_sendlog2);

    playerdeath($db,$playerinfo['player_id'], "LOG_DEFS_KABOOM", "$destination|$shipinfo[dev_escapepod]",0,0,$shipinfo['ship_id']);

    $ok = 0;
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($targetfighters > 0)
{
    $ok = 0;
}
else
{
    $ok = 2;
}

?>
