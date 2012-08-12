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
// File: includes/planetcombat.php

include_once './global_includes.php';
//dynamic_loader ($db, "direct_test.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

function planetcombat()
{
    // Dynamic functions
    dynamic_loader ($db, "gen_score.php");
    dynamic_loader ($db, "calc_ownership.php");
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
    global $torpedo_price;
    global $colonist_price;
    global $ore_price;
    global $organics_price;
    global $goods_price;
    global $energy_price;
    global $sneak_by_beams;
    global $planetbeams;
    global $planetfighters;
    global $planetshields;
    global $planettorps;
    global $attackerbeams;
    global $attackerfighters;
    global $attackershields;
    global $attackertorps;
    global $attackerarmor;
    global $torp_dmg_rate;
    global $level_factor;
    global $attackertorpdamage;
    global $initial_energy;
    global $min_value_capture, $fighter_price;
    global $l_cmb_atleastoneturn;
    global $l_cmb_atleastoneturn, $l_cmb_shipenergybb, $l_cmb_shipenergyab;
    global $l_cmb_shipenergyas, $l_cmb_shiptorpsbtl, $l_cmb_shiptorpsatl;
    global $l_cmb_planettorpdamage, $l_cmb_attackertorpdamage, $l_cmb_beams;
    global $l_cmb_fighters, $l_cmb_shields, $l_cmb_torps;
    global $l_cmb_torpdamage, $l_cmb_armor, $l_cmb_you, $l_cmb_planet;
    global $l_cmb_combatflow, $l_cmb_defender, $l_cmb_attackingplanet;
    global $l_cmb_youfireyourbeams, $l_cmb_defenselost, $l_cmb_defenselost2;
    global $l_cmb_planetarybeams, $l_cmb_planetarybeams2, $l_cmb_planetarybeams;
    global $l_cmb_youdestroyedplanetshields, $l_cmb_beamsexhausted;
    global $l_cmb_breachedyourshields, $l_cmb_destroyedyourshields;
    global $l_cmb_breachedyourarmor, $l_cmb_destroyedyourarmor;
    global $l_cmb_torpedoexchangephase, $l_cmb_nofightersleft;
    global $l_cmb_youdestroyfighters, $l_cmb_planettorpsdestroy;
    global $l_cmb_planettorpsdestroy2, $l_cmb_torpsbreachedyourarmor;
    global $l_cmb_planettorpsdestroy3, $l_cmb_youdestroyedallfighters;
    global $l_cmb_youdestroyplanetfighters, $l_cmb_fightercombatphase;
    global $l_cmb_youdestroyedallfighters2, $l_cmb_youdestroyplanetfighters2;
    global $l_cmb_allyourfightersdestroyed, $l_cmb_fightertofighterlost;
    global $l_cmb_youbreachedplanetshields, $l_cmb_shieldsremainup;
    global $l_cmb_fighterswarm, $l_cmb_swarmandrepel, $l_cmb_engshiptoshipcombat;
    global $l_cmb_shipdock, $l_cmb_approachattackvector, $l_cmb_noshipsdocked;
    global $l_cmb_yourshipdestroyed, $l_cmb_escapepod;
    global $l_cmb_finalcombatstats, $l_cmb_youlostfighters;
    global $l_cmb_youlostarmorpoints, $l_cmb_energyused, $l_cmb_planetdefeated;
    global $l_cmb_citizenswanttodie, $l_cmb_youmaycapture1, $l_cmb_youmaycapture2;
    global $l_cmb_youmaycapture3, $l_cmb_planetnotdefeated, $l_cmb_planetstatistics;
    global $l_cmb_fighterloststat, $l_cmb_energyleft;
    global $db;
    global $upgrade_cost, $upgrade_factor, $debug;
    global $start_energy, $start_fighters, $start_armor;
    global $spy_success_factor, $basedefense, $plasma_engines;
    global $ship_based_combat;

//    $debug = true;

    if ($playerinfo['turns'] < 1)
    {
        echo "$l_cmb_atleastoneturn<br><br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once './footer.php';
        die();
    }

    planet_log($db, $planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_ATTACKED);
    // Planetary defense system calculation

    $planetbeams = calcplanetbeams($db);
    $planetfighters = calcplanetfighters($db);
    $planetshields = calcplanetshields($db);
    $planettorps = calcplanettorps($db);
    $planetsensors = calcplanetsensors($db);

    $startplanetfighters = $planetfighters;

    // Attacking ship calculations

    $attackerbeams = num_beams($shipinfo['beams']);
    $attackerfighters = num_fighters($shipinfo['computer']);
    $attackershields = num_shields($shipinfo['shields']);
    $attackertorps = num_torpedoes($shipinfo['torp_launchers']);
    $attackercloak = num_cloak($shipinfo['cloak']);
    $attackerarmor = $shipinfo['armor_pts'];

    // Now check to see if the sensors detected the cloaked ship attacking. If not, beams are nullified.
    if (($planetsensors < $attackercloak) && $sneak_by_beams)
    {
        $planetbeams = 0;
    }

    // Now modify player beams, shields and torpedos on available material
    $initial_energy = $shipinfo['energy'];

    // Beams
    if ($debug)
    {
        echo "$l_cmb_shipenergybb: $shipinfo[energy]<br>\n";
    }

    if ($attackerbeams > $shipinfo['energy'])
    {
        $attackerbeams   = $shipinfo['energy'];
    }

    $shipinfo['energy'] = $shipinfo['energy'] - $attackerbeams;

    if ($debug)
    {
        echo "$l_cmb_shipenergyab (before shields): $shipinfo[energy]<br>\n";
    }

    // Shields
    if ($attackershields > $shipinfo['energy'])
    {
        $attackershields = $shipinfo['energy'];
    }

    $shipinfo['energy'] = $shipinfo['energy'] - $attackershields;
    if ($debug)
    {
        echo "$l_cmb_shipenergyas: $shipinfo[energy]<br>\n";
    }

    // Torpedos
    if ($debug)
    {
        echo "$l_cmb_shiptorpsbtl: $attackertorps ($shipinfo[torps] / $shipinfo[torp_launchers])<br>\n";
    }

    if ($attackertorps > $shipinfo['torps'])
    {
        $attackertorps = $shipinfo['torps'];
    }

    $shipinfo['torps'] = $shipinfo['torps'] - $attackertorps;
    if ($debug)
    {
        echo "$l_cmb_shiptorpsatl: $attackertorps ($shipinfo[torps] / $shipinfo[torp_launchers])<br>\n";
    }

    // Setup torp damage rate for both Planet and Ship
    $planettorpdamage    = $torp_dmg_rate * $planettorps;
    $attackertorpdamage    = $torp_dmg_rate * $attackertorps;
    if ($debug)
    {
        echo "$l_cmb_planettorpdamage: $planettorpdamage<br>\n";
    }

    if ($debug)
    {
        echo "$l_cmb_attackertorpdamage: $attackertorpdamage<br>\n";
    }

    echo "
    <div style='text-align:center'>
    <hr>
    <table width='80%' border='0'>
    <tr align='center'>
    <td width='14%' height='27'></td>
    <td width='12%' height='27'><font color='white'>$l_cmb_beams</font></td>
    <td width='17%' height='27'><font color='white'>$l_cmb_fighters</font></td>
    <td width='18%' height='27'><font color='white'>$l_cmb_shields</font></td>
    <td width='11%' height='27'><font color='white'>$l_cmb_torps</font></td>
    <td width='22%' height='27'><font color='white'>$l_cmb_torpdamage</font></td>
    <td width='11%' height='27'><font color='white'>$l_cmb_armor</font></td>
    </tr>
    <tr align='center'>
    <td width='14%'> <font color='red'>$l_cmb_you</td>
    <td width='12%'><font color='red'><strong>$attackerbeams</strong></font></td>
    <td width='17%'><font color='red'><strong>$attackerfighters</strong></font></td>
    <td width='18%'><font color='red'><strong>$attackershields</strong></font></td>
    <td width='11%'><font color='red'><strong>$attackertorps</strong></font></td>
    <td width='22%'><font color='red'><strong>$attackertorpdamage</strong></font></td>
    <td width='11%'><font color='red'><strong>$attackerarmor</strong></font></td>
    </tr>
    <tr align='center'>
    <td width='14%'> <font color='#6098F8'>$l_cmb_planet</font></td>
    <td width='12%'><font color='#6098F8'><strong>$planetbeams</strong></font></td>
    <td width='17%'><font color='#6098F8'><strong>$planetfighters</strong></font></td>
    <td width='18%'><font color='#6098F8'><strong>$planetshields</strong></font></td>
    <td width='11%'><font color='#6098F8'><strong>$planettorps</strong></font></td>
    <td width='22%'><font color='#6098F8'><strong>$planettorpdamage</strong></font></td>
    <td width='11%'><font color='#6098F8'><strong>N/A</strong></font></td>
    </tr>";

    $attacker_number = 1;
    $result99a = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=? AND on_planet='Y'", array($planetinfo['planet_id']));
    while (!$result99a->EOF)
    {
        $attacker = $result99a->fields;
        echo "<tr align='center'>
        <td width='14%'> <font color='#6098F8'>$l_cmb_defender #" . $attacker_number . "</font></td>
        <td width='12%'><font color='#6098F8'><strong>" . num_beams($attacker['beams']) . "</strong></font></td>
        <td width='17%'><font color='#6098F8'><strong>" . num_fighters($attacker['computer']) . "</strong></font></td>
        <td width='18%'><font color='#6098F8'><strong>" . num_shields($attacker['shields']) . "</strong></font></td>
        <td width='11%'><font color='#6098F8'><strong>" . num_torpedoes($attacker['torp_launchers']) . "</strong></font></td>
        <td width='22%'><font color='#6098F8'><strong>" . ($torp_dmg_rate * num_torpedoes($attacker['torp_launchers'])) . "</strong></font></td>
        <td width='11%'><font color='#6098F8'><strong>" . $attacker['armor_pts'] . "</strong></font></td>
        </tr>";
        $result99a->MoveNext();
        $attacker_number++;
    }

    echo "</table>
    <hr>
    </div>
    ";

    // Begin actual combat calculations

    $planetdestroyed   = 0;
    $attackerdestroyed = 0;

    echo "<br><div style='text-align:center'><strong><font style=\"font-size: 1.5em;\">$l_cmb_combatflow</font></strong><br><br>\n";
    echo "<table width='75%' border='0'><tr align='center'><td><font color='red'>$l_cmb_you</font></td><td><font color='#6098F8'>$l_cmb_defender</font></td>\n";
    echo "<tr align='center'><td><font color='red'><strong>$l_cmb_attackingplanet $shipinfo[sector_id]</strong></font></td><td></td>";
    echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youfireyourbeams</strong></font></td><td></td>\n";
    if ($planetfighters > 0 && $attackerbeams > 0)
    {
        if ($attackerbeams >= $planetfighters)
        {
            $l_cmb_defenselost = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_defenselost);
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_defenselost</strong></font>";
            $attackerbeams = $attackerbeams - $planetfighters;
            $planetfighters = 0;
        }
        else
        {
            $l_cmb_defenselost2 = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_defenselost2);
            $planetfighters = $planetfighters - $attackerbeams;
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_defenselost2</strong></font>";
            $attackerbeams = 0;
        }
    }

    if ($attackerfighters > 0 && $planetbeams > 0)
    {
        // If there are more beams on the planet than attacker has fighters
        if ($planetbeams > round($attackerfighters / 2))
        {
            // Half the attacker fighters
            $temp = round($attackerfighters / 2);

            // Attacker loses half his fighters
            $lost = $attackerfighters - $temp;

            // Set attacker fighters to 1/2 it's original value
            $attackerfighters = $temp;

            // Subtract half the attacker fighters from available planetary beams
            $planetbeams = $planetbeams - $lost;
            $l_cmb_planetarybeams = str_replace("[cmb_temp]", $temp, $l_cmb_planetarybeams);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_planetarybeams</strong></font><td></td>";
        }
        else
        {
            $l_cmb_planetarybeams2 = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_planetarybeams2);
            $attackerfighters = $attackerfighters - $planetbeams;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_planetarybeams2</strong></font><td></td>";
            $planetbeams = 0;
        }
    }

    if ($attackerbeams > 0)
    {
        if ($attackerbeams > $planetshields)
        {
            $attackerbeams = $attackerbeams - $planetshields;
            $planetshields = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyedplanetshields</font></strong><td></td>";
        }
        else
        {
            $l_cmb_beamsexhausted = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_beamsexhausted);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_beamsexhausted</font></strong><td></td>";
            $planetshields = $planetshields - $attackerbeams;
            $attackerbeams = 0;
        }
    }

    if ($planetbeams > 0)
    {
        if ($planetbeams > $attackershields)
        {
            $planetbeams = $planetbeams - $attackershields;
            $attackershields = 0;
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_breachedyourshields</font></strong></td>";
        }
        else
        {
            $attackershields = $attackershields - $planetbeams;
            $l_cmb_destroyedyourshields = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_destroyedyourshields);
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_destroyedyourshields</font></strong></td>";
            $planetbeams = 0;
        }
    }

    if ($planetbeams > 0)
    {
        if ($planetbeams > $attackerarmor)
        {
            $attackerarmor = 0;
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_breachedyourarmor</strong></font></td>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $planetbeams;
            $l_cmb_destroyedyourarmor = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_destroyedyourarmor);
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_destroyedyourarmor</font></strong></td>";
        }
    }

    echo "<tr align='center'><td><font color='yellow'><strong>$l_cmb_torpedoexchangephase</strong></font></td><td><strong><font color='yellow'>$l_cmb_torpedoexchangephase</strong></font></td><br>";
    if ($planetfighters > 0 && $attackertorpdamage > 0)
    {
        if ($attackertorpdamage > $planetfighters)
        {
            $l_cmb_nofightersleft = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_nofightersleft);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_nofightersleft</font></strong></td><td></td>";
            $attackertorpdamage = $attackertorpdamage - $planetfighters;
            $planetfighters = 0;
        }
        else
        {
            $planetfighters = $planetfighters - $attackertorpdamage;
            $l_cmb_youdestroyfighters = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_youdestroyfighters);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyfighters</font></strong></td><td></td>";
            $attackertorpdamage = 0;
        }
    }

    if ($attackerfighters > 0 && $planettorpdamage > 0)
    {
        if ($planettorpdamage > round($attackerfighters / 2))
        {
            $temp = round($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $planettorpdamage = $planettorpdamage - $lost;
            $l_cmb_planettorpsdestroy = str_replace("[cmb_temp]", $temp, $l_cmb_planettorpsdestroy);
            echo "<tr align='center'><td></td><td><font color='red'><strong>$l_cmb_planettorpsdestroy</strong></font></td>";
        }
        else
        {
            $attackerfighters = $attackerfighters - $planettorpdamage;
            $l_cmb_planettorpsdestroy2 = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $l_cmb_planettorpsdestroy2);
            echo "<tr align='center'><td></td><td><font color='red'><strong>$l_cmb_planettorpsdestroy2</strong></font></td>";
            $planettorpdamage = 0;
        }
    }

    if ($planettorpdamage > 0)
    {
        if ($planettorpdamage > $attackerarmor)
        {
            $attackerarmor = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_torpsbreachedyourarmor</strong></font></td><td></td>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $planettorpdamage;
            $l_cmb_planettorpsdestroy3 = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $l_cmb_planettorpsdestroy3);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_planettorpsdestroy3</strong></font></td><td></td>";
        }
    }

    if ($attackertorpdamage > 0 && $planetfighters > 0)
    {
        $planetfighters = $planetfighters - $attackertorpdamage;
        if ($planetfighters < 0)
        {
            $planetfighters = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyedallfighters</strong></font></td><td></td>";
        }
        else
        {
            $l_cmb_youdestroyplanetfighters = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_youdestroyplanetfighters);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyplanetfighters</strong></font></td><td></td>";
        }
    }

    echo "<tr align='center'><td><font color='yellow'><strong>$l_cmb_fightercombatphase</strong></font></td><td><strong><font color='yellow'>$l_cmb_fightercombatphase</strong></font></td><br>";
    if ($attackerfighters > 0 && $planetfighters > 0)
    {
        if ($attackerfighters > $planetfighters)
        {
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyedallfighters2</strong></font></td><td></td>";
            $tempplanetfighters = 0;
        }
        else
        {
            $l_cmb_youdestroyplanetfighters2 = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_youdestroyplanetfighters2);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyplanetfighters2</strong></font></td><td></td>";
            $tempplanetfighters = $planetfighters - $attackerfighters;
        }
        if ($planetfighters > $attackerfighters)
        {
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_allyourfightersdestroyed</strong></font></td><td></td>";
            $tempplayfighters = 0;
        }
        else
        {
            $tempplayfighters = $attackerfighters - $planetfighters;
            $l_cmb_fightertofighterlost = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_fightertofighterlost);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_fightertofighterlost</strong></font></td><td></td>";
        }
        $attackerfighters = $tempplayfighters;
        $planetfighters = $tempplanetfighters;
    }

    if ($attackerfighters > 0 && $planetshields > 0)
    {
        if ($attackerfighters > $planetshields)
        {
            $attackerfighters = $attackerfighters - round($planetshields / 2);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youbreachedplanetshields</strong></font></td><td></td>";
            $planetshields = 0;
        }
        else
        {
            $l_cmb_shieldsremainup = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_shieldsremainup);
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_shieldsremainup</strong></font></td>";
            $planetshields = $planetshields - $attackerfighters;
            $attackerfighters = 0;
        }
    }

    if ($planetfighters > 0)
    {
        if ($planetfighters > $attackerarmor)
        {
            $planetfighters = $planetfighters - $attackerarmor;
            $attackerarmor = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_fighterswarm</strong></font></td><td></td>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $planetfighters;
            $planetfighters = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_swarmandrepel</strong></font></td><td></td>";
        }
    }

    echo "</table></div>\n";
    // Send each docked ship in sequence to attack agressor
    $result4 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=? AND on_planet='Y'", array($planetinfo['planet_id']));
    $shipsonplanet = $result4->RecordCount();
    if ($shipsonplanet > 0)
    {
        $l_cmb_shipdock = str_replace("[cmb_shipsonplanet]", $shipsonplanet, $l_cmb_shipdock);
        echo "<br><br><div style='text-align:center'>$l_cmb_shipdock<br>$l_cmb_engshiptoshipcombat</div><br><br>\n";
        while (!$result4->EOF)
        {
            $onplanet = $result4->fields;
            if ($attackerfighters < 0)
            {
                $attackerfighters = 0;
            }

            if ($attackertorps    < 0)
            {
                $attackertorps = 0;
            }

            if ($attackershields  < 0)
            {
                $attackershields = 0;
            }

            if ($attackerbeams    < 0)
            {
                $attackerbeams = 0;
            }

            if ($attackerarmor    < 1)
            {
                break;
            }

            echo "<br>-$onplanet[name] $l_cmb_approachattackvector-<br>";
            dynamic_loader ($db, "planet_shiptoship.php");
            shiptoship($onplanet['player_id'], $onplanet['ship_id']);
            $result4->MoveNext();
        }
    }
    else
    {
        echo "<br><br><div style='text-align:center'>$l_cmb_noshipsdocked</div><br><br>\n";
    }

    if ($attackerarmor < 1)
    {
        $free_ore = round($shipinfo['ore']/2);
        $free_organics = round($shipinfo['organics']/2);
        $free_goods = round($shipinfo['goods']/2);
        if ($plasma_engines)
        {
            $ship_value = $playerinfo['credits'] + $upgrade_cost*(round(pow($upgrade_factor, $shipinfo['hull']))+round(pow($upgrade_factor, $shipinfo['engines']))+round(pow($upgrade_factor, $shipinfo['pengines']))+round(pow($upgrade_factor, $shipinfo['power']))+round(pow($upgrade_factor, $shipinfo['computer']))+round(pow($upgrade_factor, $shipinfo['sensors']))+round(pow($upgrade_factor, $shipinfo['beams']))+round(pow($upgrade_factor, $shipinfo['torp_launchers']))+round(pow($upgrade_factor, $shipinfo['shields']))+round(pow($upgrade_factor, $shipinfo['armor']))+round(pow($upgrade_factor, $shipinfo['cloak'])));
        }
        else
        {
            $ship_value = $playerinfo['credits'] + $upgrade_cost*(round(pow($upgrade_factor, $shipinfo['hull']))+round(pow($upgrade_factor, $shipinfo['engines']))+round(pow($upgrade_factor, $shipinfo['power']))+round(pow($upgrade_factor, $shipinfo['computer']))+round(pow($upgrade_factor, $shipinfo['sensors']))+round(pow($upgrade_factor, $shipinfo['beams']))+round(pow($upgrade_factor, $shipinfo['torp_launchers']))+round(pow($upgrade_factor, $shipinfo['shields']))+round(pow($upgrade_factor, $shipinfo['armor']))+round(pow($upgrade_factor, $shipinfo['cloak'])));
        }

        $ship_salvage_rate = mt_rand(0,10);
        $ship_salvage = $ship_value*$ship_salvage_rate/100;
        echo "<br><div style='text-align:center'><font style=\"font-size: 1.5em;\" color='RED'><strong>$l_cmb_yourshipdestroyed</font></strong></div><br>";

//        Dynamic functions
//        dynamic_loader ($db, "playerdeath.php");
//        playerdeath($db,$playerinfo['player_id'], "LOG_SHIP_KILLED_BY_PLANET", "$planetinfo[name]|$planetinfo[sector_id]|$ownerinfo[character_name]",'','',$playerinfo['currentship']);
    }
    else
    {
        $free_ore = 0;
        $free_goods = 0;
        $free_organics = 0;
        $ship_salvage_rate = 0;
        $ship_salvage = 0;
        $planetrating = $ownershipinfo['hull'] + $ownershipinfo['engines'] + $planetinfo['computer'] + $planetinfo['beams'] + $planetinfo['torp_launchers'] + $planetinfo['shields'] + $planetinfo['armor'];
        if ($ownerinfo['rating'] != 0)
        {
            $rating_change = ($ownerinfo['rating']/abs($ownerinfo['rating']))*$planetrating*10;
        }
        else
        {
            $rating_change =- 100;
        }

        echo "<div style='text-align:center'><br><strong><font style=\"font-size: 1.5em;\">$l_cmb_finalcombatstats</font></strong><br><br>";
//        $fighters_lost = $shipinfo['fighters'] - $attackerfighters;
        $fighters_lost = num_fighters($shipinfo['computer']) - $attackerfighters;

        $l_cmb_youlostfighters = str_replace("[cmb_fighters_lost]", $fighters_lost, $l_cmb_youlostfighters);
        $l_cmb_youlostfighters = str_replace("[cmb_playerinfo_ship_fighters]", num_fighters($shipinfo['computer']), $l_cmb_youlostfighters);
        echo "$l_cmb_youlostfighters<br>";

        $armor_lost = $shipinfo['armor_pts'] - $attackerarmor;
        $l_cmb_youlostarmorpoints = str_replace("[cmb_armor_lost]", $armor_lost, $l_cmb_youlostarmorpoints);
        $l_cmb_youlostarmorpoints = str_replace("[cmb_playerinfo_armor_pts]", $shipinfo['armor_pts'], $l_cmb_youlostarmorpoints);
        $l_cmb_youlostarmorpoints = str_replace("[cmb_attackerarmor]", $attackerarmor, $l_cmb_youlostarmorpoints);
        echo "$l_cmb_youlostarmorpoints<br>";

        $energy = $shipinfo['energy'];
        $energy_lost = $initial_energy - $shipinfo['energy'];
        $l_cmb_energyused = str_replace("[cmb_energy_lost]", $energy_lost, $l_cmb_energyused);
        $l_cmb_energyused = str_replace("[cmb_playerinfo_ship_energy]", $initial_energy, $l_cmb_energyused);
        echo "$l_cmb_energyused<br></div>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET energy=?, fighters=fighters-?, torps=torps-?, armor_pts=armor_pts-? WHERE ship_id=?", array($energy, $fighters_lost, $attackertorps, $armor_lost, $shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET rating=rating-? WHERE player_id=?", array($rating_change, $playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    $result4 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=? AND on_planet='Y'", array($planetinfo['planet_id']));
    $shipsonplanet = $result4->RecordCount();

    if ($planetshields < 1 && $planetfighters < 1 && $attackerarmor > 0 && $shipsonplanet == 0)
    {
        echo "<br><br><div style='text-align:center'><font color='green'><strong>$l_cmb_planetdefeated</strong></font></div><br><br>";
        planet_log($db, $planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_DEFEATED);

        if ($min_value_capture != 0)
        {
            $playerscore = gen_score($db,$playerinfo['player_id']);
            $playerscore *= $playerscore;

            $planetscore = $planetinfo['organics'] * $organics_price + $planetinfo['ore'] * $ore_price + $planetinfo['goods'] * $goods_price + $planetinfo['energy'] * $energy_price + $planetinfo['fighters'] * $fighter_price + $planetinfo['torps'] * $torpedo_price + $planetinfo['colonists'] * $colonist_price + $planetinfo['credits'];
            $planetscore = $planetscore * $min_value_capture / 100;

            //            echo "playerscore $playerscore, planetscore $planetscore";
            if ($playerscore < $planetscore)
            {
                planet_log($db, $planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_PLANET_DESTRUCT);
                echo "<div style='text-align:center'>$l_cmb_citizenswanttodie</div><br><br>";
                ///
                if ($spy_success_factor)
                {
                   dynamic_loader ($db, "spy_planet_destroyed.php");
                   spy_planet_destroyed($db,$planetinfo['planet_id']);
                }

                $db->Execute("DELETE FROM {$db->prefix}planets WHERE planet_id=?", array($planetinfo['planet_id']));
                playerlog($db,$ownerinfo['player_id'], "LOG_PLANET_DEFEATED_D", "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
                adminlog($db, "LOG_ADMIN_PLANETDEL", "$playerinfo[character_name]|$ownerinfo[character_name]|$shipinfo[sector_id]");
                gen_score($db,$ownerinfo['player_id']);
                calc_ownership($db,$planetinfo['sector_id']); // Doesnt seem to run otherwise - per SF BUG # 588421
            }
            else
            {
                echo "<div style='text-align:center'><font color=red>$l_cmb_youmaycapture1";
                echo "<a href=planet.php?planet_id=".$planetinfo['planet_id']."&command=capture>";
                echo $l_cmb_youmaycapture2;
                echo "</a> ".$l_cmb_youmaycapture3;
                echo "</font></div><br><br>";
                playerlog($db,$ownerinfo['player_id'], "LOG_PLANET_DEFEATED", "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
                gen_score($db,$ownerinfo['player_id']);
                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET fighters=0, torps=torps-?, base='N', " .
                                            "defeated='Y' WHERE planet_id=?", array($planettorps, $planetinfo['planet_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                if ($spy_success_factor)
                {
                   dynamic_loader ($db, "change_planet_ownership.php");
                   change_planet_ownership($db, $planetinfo['planet_id'],$ownerinfo['player_id'],0);
                }
            }
        }
        else
        {
            echo "<div style='text-align:center'><font color=red>$l_cmb_youmaycapture1";
            echo "<a href=planet.php?planet_id=".$planetinfo['planet_id']."&command=capture>";
            echo $l_cmb_youmaycapture2;
            echo "</a> ".$l_cmb_youmaycapture3;
            echo "</font></div><br><br>";
            playerlog($db,$ownerinfo['player_id'], "LOG_PLANET_DEFEATED", "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
            gen_score($db,$ownerinfo['player_id']);
            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET fighters=0, torps=torps-?, base='N', " .
                                        "defeated='Y' WHERE planet_id=?", array($planettorps, $planetinfo['planet_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            if ($spy_success_factor)
            {
               dynamic_loader ($db, "change_planet_ownership.php");
               change_planet_ownership($db, $planetinfo['planet_id'],$ownerinfo['player_id'],0);
            }
        }
        calc_ownership($db,$planetinfo['sector_id']);
    }
    else
    {
        echo "<br><br><div style='text-align:center'><font color='#6098F8'><strong>$l_cmb_planetnotdefeated</strong></font></div><br><br>";
        if ($debug)
        {
            echo "<br><br>$l_cmb_planetstatistics<br><br>";
        }

        $fighters_lost = $startplanetfighters - $planetfighters;
        $l_cmb_fighterloststat = str_replace("[cmb_fighters_lost]", $fighters_lost, $l_cmb_fighterloststat);
        $l_cmb_fighterloststat = str_replace("[cmb_planetinfo_fighters]", $planetinfo['fighters'], $l_cmb_fighterloststat);
        $l_cmb_fighterloststat = str_replace("[cmb_planetfighters]", ($planetinfo['fighters'] - $fighters_lost), $l_cmb_fighterloststat);
        if ($debug)
        {
            echo "$l_cmb_fighterloststat<br>";
        }

        $energy = $planetinfo['energy'];
        if ($debug)
        {
            echo "$l_cmb_energyleft: $planetinfo[energy]<br>";
        }

        $numbered_salvage = number_format($ship_salvage, 0, $local_number_dec_point, $local_number_thousands_sep);
        playerlog($db,$ownerinfo['player_id'], "LOG_PLANET_NOT_DEFEATED", "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$numbered_salvage");
        gen_score($db,$ownerinfo['player_id']);

        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET energy=?, fighters=fighters-?, " .
                                    "torps=torps-?, ore=ore+?, goods=goods+?, " .
                                    "organics=organics+?, credits=credits+? WHERE planet_id=?", array($energy, $fighters_lost, $planettorps, $free_ore, $free_goods, $free_organics, $ship_salvage, $planetinfo['planet_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

//      This seems to be the right code to run here, but for some reason the players goods get updated before hand, I dunno where.
//      $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET ore=ore-$free_ore, goods=goods-$free_goods, organics=organics-$free_organics WHERE ship_id=$shipinfo[ship_id]");
//        db_op_result($db,$debug_query,__LINE__,__FILE__);

//        For some reason, this manages to set the ships to negative??
//        Answer: Because its removing credits from the ship based on the value of levels, goods, and so on..
//        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits-$ship_salvage " .
//                                    "WHERE player_id=$shipinfo[player_id]");
//        db_op_result($db,$debug_query,__LINE__,__FILE__);
        if ($debug)
        {
            echo "<br>Set: energy=$energy, fighters lost=$fighters_lost, torps=$planetinfo[torps], sectorid=$sectorinfo[sector_id]<br>";
        }
    }

    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 " .
                                "WHERE player_id=?", array($playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}
?>
