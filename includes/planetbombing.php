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
// File: includes/planetbombing.php

include_once ("./global_includes.php");
dynamic_loader ($db, "direct_test.php");
direct_test(__FILE__, $_SERVER['PHP_SELF']);

function planetbombing()
{
    dynamic_loader ($db, "playerlog.php");

    // Planet log constants
    define('PLOG_GENESIS_CREATE',1);
    define('PLOG_GENESIS_DESTROY',2);
    define('PLOG_CAPTURE',3);
    define('PLOG_ATTACKED',4);
    define('PLOG_SCANNED',5);
    define('PLOG_OWNER_DEAD',6);
    define('PLOG_DEFEATED',7);
    define('PLOG_SOFA',8);
    define('PLOG_PLANET_DESTRUCT',9);

    global $playerinfo, $shipinfo;
    global $ownerinfo, $ownershipinfo;
    global $sectorinfo;
    global $planetinfo;
    global $planetbeams;
    global $planetfighters;
    global $attackerfighters;
    global $planettorps;
    global $torp_dmg_rate;
    global $l_cmb_atleastoneturn;
    global $db;
    global $l_bombsaway;
    global $l_bigfigs;
    global $l_bigbeams;
    global $l_bigtorps;
    global $l_strafesuccess;
    //$debug = true;

    if ($playerinfo['turns'] < 1)
    {
        echo "$l_cmb_atleastoneturn<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    if ($shipinfo['fighters'] < 1)
    {
        echo "$l_cmb_needfighters<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    planet_log($db, $planetinfo[planet_id],$planetinfo[owner],$playerinfo[player_id],PLOG_SOFA);
//    $res = $db->Execute("LOCK TABLES {$db->prefix}players WRITE, {$db->prefix}ships WRITE, {$db->prefix}planets WRITE, " .
//                        "{$db->prefix}logs WRITE");

    echo "$l_bombsaway<br><br>\n";

    $attackerfighterslost = 0;
    $planetfighterslost = 0;
    $attackerfightercapacity = num_fighters($shipinfo[computer]);
    $ownerfightercapacity = num_fighters($planetinfo[computer]);
    $beamsused = 0;
    $planettorps = calcplanettorps($db);
    $planetbeams = calcplanetbeams($db);
    $planetfighters = calcplanetfighters($db);
    $attackerfighters = $shipinfo[fighters];

    if ($debug)
    {
        echo "FigsCapacity $attackerfightercapacity <br>\n";
    }

    if ($debug)
    {
        echo "Figsused $attackerfighters<br>\n";
    }

    if ($ownerfightercapacity/$attackerfightercapacity<1)
    {
        echo "$l_bigfigs<br><br>\n";
    }


    if ($planetbeams <= $attackerfighters)
    {
        $attackerfighterslost = $planetbeams;
        $beamsused = $planetbeams;
    }
    else
    {
        $attackerfighterslost = $attackerfighters;
        $beamsused = $attackerfighters;
    }

    if ($attackerfighters <= $attackerfighterslost)
    {
        echo "$l_bigbeams<br>\n";
        if ($debug)
        {
            echo "Fighters destroyed by beams $attackerfighterslost<br>\n";
        }
    }
    else
    {
        if ($debug)
        {
            echo "pfigs $planetfighterslost mefigs $attackerfighters - $attackerfighterslost<br>\n";
        }

        $attackerfighterslost += $planettorps*$torp_dmg_rate;

        if ($attackerfighters <= $attackerfighterslost)
        {
            echo "$l_bigtorps<br>\n";
        }
        else
        {
            echo "$l_strafesuccess<br>\n";
            if ($ownerfightercapacity/$attackerfightercapacity>1)
            {
                $planetfighterslost = $attackerfighters-$attackerfighterslost;
                if ($debug) echo "small guyfigs go boom $planetfighterslost<br>\n";

            }
            else
            {
                $planetfighterslost=round(($attackerfighters-$attackerfighterslost)*$ownerfightercapacity/$attackerfightercapacity);
                if ($debug)
                {
                    echo "bigguy figs go boom $planetfighterslost<br>\n";
                }

                if ($debug)
                {
                    echo "which is ".$attackerfighters."-".$attackerfighterslost." times ".$ownerfightercapacity/$attackerfightercapacity." <br>\n";
                }
            }
            if ($planetfighterslost>$planetfighters)
            {
                $planetfighterslost = $planetfighters;
            }
        }
    }

    if ($debug)
    {
        echo "total figs go boom $planetfighterslost<br>\n";
    }

    echo "<br><br>\n";
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 " .
                                "WHERE player_id=?", array($playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET fighters=fighters-? WHERE ship_id=?", array($attackerfighters, $shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET energy=energy-?, fighters=fighters-?, " .
                                "torps=torps-? WHERE planet_id=?", array($beamsused, $planetfighterslost, $planettorps, $planetinfo['planet_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

//    $debug_query = $db->Execute("UNLOCK TABLES");
//    db_op_result($db,$debug_query,__LINE__,__FILE__);

    playerlog($db,$ownerinfo[player_id], "LOG_PLANET_BOMBED", "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]|$beamsused|$planettorps|$planetfighterslost");
}
?>
