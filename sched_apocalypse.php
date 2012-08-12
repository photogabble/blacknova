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
// File: sched_apocalypse.php

dynamic_loader ($db, "playerlog.php");

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_apocalypse.php"));
if ($pos !== false)
{
    include_once './global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once './footer.php';
    die();
}

echo "<strong>Planetary apocalypse</strong><br><br>";

$debug_query = $db->Execute("SELECT * from {$db->prefix}planets WHERE colonists > $doomsday_value");
db_op_result($db,$debug_query,__LINE__,__FILE__);

$chance = 9;
$reccount = $debug_query->RecordCount();

if ($reccount > 200)
{
    $chance = 7; // increase chance it will happen if we have lots of planets meeting the criteria
}

for loop here 1 to multiplier
for ($j = 0; $j<$multiplier; $j++)
{
    $affliction = mt_rand(1,$chance); // the chance something bad will happen

    if ($debug_query && $affliction < 3 && $reccount > 0)
    {
        $i = 1;
        $targetnum = mt_rand(1,$reccount);
        while (!$debug_query->EOF)
        {
            $debug_query->Move($targetnum);
            $targetinfo = $debug_query->fields;
        }

        if ($affliction == 1) // Space Plague
        {
            echo "Space Plague triggered.<br>.";
            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET colonists = ROUND(colonists-colonists*$space_plague_kills) " .
                                        "WHERE planet_id = $targetinfo[planet_id]");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $logpercent = ROUND($space_plague_kills * 100);
            playerlog($db,$targetinfo['owner'], "LOG_SPACE_PLAGUE","$targetinfo[name]|$targetinfo[sector_id]|$logpercent");
        }
        else
        {
            echo "Plasma Storm triggered.<br>.";
            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET energy = 0 WHERE planet_id = $targetinfo[planet_id]");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            playerlog($db,$targetinfo['owner'], "LOG_PLASMA_STORM","$targetinfo[name]|$targetinfo[sector_id]");
        }
    }
}

echo "<br>";
?>
