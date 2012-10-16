<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: sched_apocalypse.php

if (strpos ($_SERVER['PHP_SELF'], 'sched_apocalypse.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

echo "<strong>PLANETARY APOCALYPSE</strong><br><br>";
echo "The four horsemen of the apocalypse set forth...<br>";
$doomsday = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE colonists > ?;", array ($doomsday_value));
db_op_result ($db, $doomsday, __LINE__, __FILE__);
$chance = 9;
$reccount = $doomsday->RecordCount();
if ($reccount > 200)
{
    $chance = 7; // Increase the chance it will happen if we have lots of planets meeting the criteria
}

$affliction = mt_rand (1, $chance); // The chance something bad will happen
if ($doomsday && $affliction < 3 && $reccount > 0)
{
    $i = 1;
    $targetnum = mt_rand (1, $reccount);
    while (!$doomsday->EOF)
    {
        if ($i == $targetnum)
        {
            $targetinfo = $doomsday->fields;
            break;
        }
        $i++;
        $doomsday->MoveNext();
    }
    if ($affliction == 1) // Space Plague
    {
        echo "The horsmen release the Space Plague!<br>.";
        $resx = $db->Execute ("UPDATE {$db->prefix}planets SET colonists = ROUND (colonists - colonists * ?) WHERE planet_id = ?;", array ($space_plague_kills, $targetinfo['planet_id']));
        db_op_result ($db, $resx, __LINE__, __FILE__);
        $logpercent = ROUND ($space_plague_kills * 100);
        player_log ($db, $targetinfo['owner'], LOG_SPACE_PLAGUE, "$targetinfo[name]|$targetinfo[sector_id]|$logpercent");
    }
    else
    {
        echo "The horsemen release a Plasma Storm!<br>.";
        $resy = $db->Execute ("UPDATE {$db->prefix}planets SET energy = 0 WHERE planet_id = ?;", array ($targetinfo['planet_id']));
        db_op_result ($db, $resy, __LINE__, __FILE__);
        player_log ($db, $targetinfo['owner'], LOG_PLASMA_STORM, "$targetinfo[name]|$targetinfo[sector_id]");
    }
}
echo "<br>";
?>
