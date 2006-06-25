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
// File: planet_shiptoship.php

include_once ("./global_includes.php");
dynamic_loader ($db, "direct_test.php");
direct_test(__FILE__, $_SERVER['PHP_SELF']);

function shiptoship($player_id, $ship_id)
{
    // Dynamic functions
    dynamic_loader ($db, "playerlog.php");

    global $plasma_engines;
    global $attackerbeams;
    global $attackerfighters;
    global $attackershields;
    global $attackertorps;
    global $attackerarmor;
    global $attackertorpdamage;
    global $start_energy, $start_fighters, $start_armor;
    global $playerinfo, $shipinfo;
    global $l_cmb_statattackershields, $l_cmb_statattackertorps;
    global $l_cmb_statattackerarmor, $l_cmb_statattackertorpdamage;
    global $l_cmb_startingstats, $l_cmb_statattackerbeams, $l_cmb_statattackerfighters;
    global $l_cmb_statattackershields, $l_cmb_statattackertorps;
    global $l_cmb_statattackerarmor, $l_cmb_statattackertorpdamage;
    global $l_cmb_isattackingyou, $l_cmb_beamexchange, $l_cmb_beamsdestroy;
    global $l_cmb_beamsdestroy2, $l_cmb_nobeamsareleft, $l_cmb_beamshavenotarget;
    global $l_cmb_fighterdestroyedbybeams, $l_cmb_beamsdestroystillhave;
    global $l_cmb_fighterunhindered, $l_cmb_youhavenofightersleft;
    global $l_cmb_breachedsomeshields, $l_cmb_shieldsarehitbybeams;
    global $l_cmb_nobeamslefttoattack;
    global $l_cmb_yourshieldsbreachedby, $l_cmb_yourshieldsarehit;
    global $l_cmb_hehasnobeamslefttoattack, $l_cmb_yourbeamsbreachedhim;
    global $l_cmb_yourbeamshavedonedamage, $l_cmb_nobeamstoattackarmor;
    global $l_cmb_yourarmorbreachedbybeams, $l_cmb_yourarmorhitdamaged;
    global $l_cmb_torpedoexchange, $l_cmb_hehasnobeamslefttoattackyou;
    global $l_cmb_yourtorpsdestroy, $l_cmb_yourtorpsdestroy2;
    global $l_cmb_youhavenotorpsleft, $l_cmb_hehasnofighterleft;
    global $l_cmb_torpsdestroyyou, $l_cmb_someonedestroyedfighters;
    global $l_cmb_hehasnotorpsleftforyou;
    global $l_cmb_youhavenofightersanymore, $l_cmb_youbreachedwithtorps;
    global $l_cmb_hisarmorishitbytorps, $l_cmb_notorpslefttoattackarmor;
    global $l_cmb_yourarmorbreachedbytorps, $l_cmb_yourarmorhitdmgtorps;
    global $l_cmb_hehasnotorpsforyourarmor, $l_cmb_fightersattackexchange;
    global $l_cmb_enemylostallfighters, $l_cmb_helostsomefighters;
    global $l_cmb_youlostallfighters, $l_cmb_youalsolostsomefighters;
    global $l_cmb_hehasnofightersleftattack;
    global $l_cmb_younofightersattackleft, $l_cmb_youbreachedarmorwithfighters;
    global $l_cmb_youhitarmordmgfighters, $l_cmb_youhavenofighterstoarmor;
    global $l_cmb_hasbreachedarmorfighters, $l_cmb_yourarmorishitfordmgby;
    global $l_cmb_nofightersleftheforyourarmor, $l_cmb_hehasbeendestroyed;
    global $l_cmb_escapepodlaunched, $l_cmb_yousalvaged1, $l_cmb_yousalvaged2;
    global $l_cmb_youdidntdestroyhim, $l_cmb_shiptoshipcombatstats;
    global $l_cmb_planettorpdamage, $l_cmb_attackertorpdamage, $l_cmb_beams;
    global $l_cmb_fighters, $l_cmb_shields, $l_cmb_torps;
    global $l_cmb_torpdamage, $l_cmb_armor, $l_cmb_you, $l_cmb_planet;
    global $l_cmb_combatflow, $l_cmb_defender, $l_cmb_attackingplanet;
    global $db;
    global $level_factor, $torp_dmg_rate, $rating_combat_factor, $upgrade_factor;
    global $upgrade_cost;
    global $spy_success_factor;

    $result2 = $db->Execute ("SELECT {$db->prefix}ships.*, {$db->prefix}players.currentship, {$db->prefix}players.character_name, " .
                             "{$db->prefix}players.rating, " . 
                             "{$db->prefix}players.player_id FROM {$db->prefix}ships LEFT JOIN {$db->prefix}players " .
                             "ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id WHERE ship_id=?", array($ship_id));
    $targetinfo=$result2->fields;

    $targetbeams = num_beams($targetinfo['beams']);
    if ($targetbeams > $targetinfo['energy'])
    {
        $targetbeams = $targetinfo['energy'];
    }

    $targetinfo['energy'] = $targetinfo['energy'] - $targetbeams;
    $targetshields = num_shields($targetinfo['shields']);
    if ($targetshields > $targetinfo['energy'])
    {
        $targetshields=$targetinfo['energy'];
    }

    $targetinfo['energy'] = $targetinfo['energy'] - $targetshields;

    $targettorpnum = num_torpedoes($targetinfo['torp_launchers']);
    if ($targettorpnum > $targetinfo['torps'])
    {
        $targettorpnum = $targetinfo['torps'];
    }

    $targettorpdmg = $torp_dmg_rate * $targettorpnum;
    $targetarmor = $targetinfo['armor_pts'];

    $targetcomps = num_fighters($targetinfo['computer']);
    $targetfighters = $targetinfo['fighters'];
    if ($targetfighters > $targetcomps)
    {
        $targetfighters = $targetcomps;
    }

    $targetdestroyed = 0;
    $playerdestroyed = 0;
    
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
    </tr>";

    echo "<tr align='center'>
    <td width='14%'> <font color='#6098F8'>$l_cmb_defender </font></td>
    <td width='12%'><font color='#6098F8'><strong>" . $targetbeams . "</strong></font></td>
    <td width='17%'><font color='#6098F8'><strong>" . $targetfighters . "</strong></font></td>
    <td width='18%'><font color='#6098F8'><strong>" . $targetshields . "</strong></font></td>
    <td width='11%'><font color='#6098F8'><strong>" . $targettorpnum . "</strong></font></td>
    <td width='22%'><font color='#6098F8'><strong>" . $targettorpdmg . "</strong></font></td>
    <td width='11%'><font color='#6098F8'><strong>" . $targetarmor . "</strong></font></td>
    </tr>";

    echo "</table>
    <hr>
    </div>
    ";

    echo "<br>";
    echo "-->$targetinfo[name] $l_cmb_isattackingyou<br><br>";
    echo "$l_cmb_beamexchange<br>";
    if ($targetfighters > 0 && $attackerbeams > 0)
    {
        if ($attackerbeams > round($targetfighters / 2))
        {
            $temp = round($targetfighters/2);
            $lost = $targetfighters-$temp;
            $targetfighters = $temp;
            $attackerbeams = $attackerbeams-$lost;
            $l_cmb_beamsdestroy = str_replace("[cmb_lost]", $lost, $l_cmb_beamsdestroy);
            echo "<-- $l_cmb_beamsdestroy<br>";
        }
        else
        {
            $targetfighters = $targetfighters-$attackerbeams;
            $l_cmb_beamsdestroy2 = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_beamsdestroy2);
            echo "--> $l_cmb_beamsdestroy2<br>";
            $attackerbeams = 0;
        }
    }
    elseif ($targetfighters > 0 && $attackerbeams < 1)
    {
        echo "$l_cmb_nobeamsareleft<br>";
    }
    else
    {
        echo "$l_cmb_beamshavenotarget<br>";
    }

    if ($attackerfighters > 0 && $targetbeams > 0)
    {
        if ($targetbeams > round($attackerfighters / 2))
        {
            $temp = round($attackerfighters/2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $targetbeams = $targetbeams - $lost;
            $l_cmb_fighterdestroyedbybeams = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_fighterdestroyedbybeams);
            $l_cmb_fighterdestroyedbybeams = str_replace("[cmb_lost]", $lost, $l_cmb_fighterdestroyedbybeams);
            echo "--> " . $l_cmb_fighterdestroyedbybeams . "<br>";
        }
        else
        {
            $attackerfighters = $attackerfighters - $targetbeams;
            $l_cmb_beamsdestroystillhave = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_beamsdestroystillhave);
            $l_cmb_beamsdestroystillhave = str_replace("[cmb_targetbeams]", $targetbeams, $l_cmb_beamsdestroystillhave);
            $l_cmb_beamsdestroystillhave = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_beamsdestroystillhave);
            echo "<-- $l_cmb_beamsdestroystillhave<br>";
            $targetbeams=0;
        }
    }
    elseif ($attackerfighters > 0 && $targetbeams < 1)
    {
        echo "$l_cmb_fighterunhindered<br>";
    }
    else
    {
        echo "$l_cmb_youhavenofightersleft<br>";
    }

    if ($attackerbeams > 0)
    {
        if ($attackerbeams > $targetshields)
        {
            $attackerbeams = $attackerbeams - $targetshields;
            $targetshields = 0;
            $l_cmb_breachedsomeshields = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_breachedsomeshields);
            echo "<-- $l_cmb_breachedsomeshields<br>";
        }
        else
        {
            $l_cmb_shieldsarehitbybeams = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_shieldsarehitbybeams);
            $l_cmb_shieldsarehitbybeams = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_shieldsarehitbybeams);
            echo "$l_cmb_shieldsarehitbybeams<br>";
            $targetshields = $targetshields - $attackerbeams;
            $attackerbeams = 0;
        }
    }
    else
    {
        $l_cmb_nobeamslefttoattack = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_nobeamslefttoattack);
        echo "$l_cmb_nobeamslefttoattack<br>";
    }

    if ($targetbeams > 0)
    {
        if ($targetbeams > $attackershields)
        {
            $targetbeams = $targetbeams - $attackershields;
            $attackershields = 0;
            $l_cmb_yourshieldsbreachedby = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourshieldsbreachedby);
            echo "--> $l_cmb_yourshieldsbreachedby<br>";
        }
        else
        {
            $l_cmb_yourshieldsarehit = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourshieldsarehit);
            $l_cmb_yourshieldsarehit = str_replace("[cmb_targetbeams]", $targetbeams, $l_cmb_yourshieldsarehit);
            echo "<-- $l_cmb_yourshieldsarehit<br>";
            $attackershields = $attackershields - $targetbeams;
            $targetbeams = 0;
        }
    }
    else
    {
        $l_cmb_hehasnobeamslefttoattack = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hehasnobeamslefttoattack);
        echo "$l_cmb_hehasnobeamslefttoattack<br>";
    }

    if ($attackerbeams > 0)
    {
        if ($attackerbeams > $targetarmor)
        {
            $targetarmor = 0;
            $l_cmb_yourbeamsbreachedhim = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourbeamsbreachedhim);
            echo "--> $l_cmb_yourbeamsbreachedhim<br>";
        }
        else
        {
            $targetarmor = $targetarmor - $attackerbeams;
            $l_cmb_yourbeamshavedonedamage = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_yourbeamshavedonedamage);
            $l_cmb_yourbeamshavedonedamage = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourbeamshavedonedamage);
            echo "$l_cmb_yourbeamshavedonedamage<br>";
        }
    }
    else
    {
        $l_cmb_nobeamstoattackarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_nobeamstoattackarmor);
        echo "$l_cmb_nobeamstoattackarmor<br>";
    }

    if ($targetbeams > 0)
    {
        if ($targetbeams > $attackerarmor)
        {
            $attackerarmor = 0;
            $l_cmb_yourarmorbreachedbybeams = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourarmorbreachedbybeams);
            echo "--> $l_cmb_yourarmorbreachedbybeams<br>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $targetbeams;
            $l_cmb_yourarmorhitdamaged = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourarmorhitdamaged);
            $l_cmb_yourarmorhitdamaged = str_replace("[cmb_targetbeams]", $targetbeams, $l_cmb_yourarmorhitdamaged);
            echo "<-- $l_cmb_yourarmorhitdamaged<br>";
        }
    }
    else
    {
        $l_cmb_hehasnobeamslefttoattackyou = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hehasnobeamslefttoattackyou);
        echo "$l_cmb_hehasnobeamslefttoattackyou<br>";
    }

    echo "<br>$l_cmb_torpedoexchange<br>";
    if ($targetfighters > 0 && $attackertorpdamage > 0)
    {
        if ($attackertorpdamage > round($targetfighters / 2))
        {
            $temp = round($targetfighters/2);
            $lost = $targetfighters - $temp;
            $targetfighters = $temp;
            $attackertorpdamage = $attackertorpdamage - $lost;
            $l_cmb_yourtorpsdestroy = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourtorpsdestroy);
            $l_cmb_yourtorpsdestroy = str_replace("[cmb_lost]", $lost, $l_cmb_yourtorpsdestroy);
            echo "--> $l_cmb_yourtorpsdestroy<br>";
        }
        else
        {
            $targetfighters = $targetfighters - $attackertorpdamage;
            $l_cmb_yourtorpsdestroy2 = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourtorpsdestroy2);
            $l_cmb_yourtorpsdestroy2 = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_yourtorpsdestroy2);
            echo "<-- $l_cmb_yourtorpsdestroy2<br>";
            $attackertorpdamage = 0;
        }
    }
    elseif ($targetfighters > 0 && $attackertorpdamage < 1)
    {
        $l_cmb_youhavenotorpsleft = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_youhavenotorpsleft);
        echo "$l_cmb_youhavenotorpsleft<br>";
    }
    else
    {
        $l_cmb_hehasnofighterleft = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hehasnofighterleft);
        echo "$l_cmb_hehasnofighterleft<br>";
    }

    if ($attackerfighters > 0 && $targettorpdmg > 0)
    {
        if ($targettorpdmg > round($attackerfighters / 2))
        {
            $temp = round($attackerfighters/2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $targettorpdmg = $targettorpdmg - $lost;
            $l_cmb_torpsdestroyyou = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_torpsdestroyyou);
            $l_cmb_torpsdestroyyou = str_replace("[cmb_lost]", $lost, $l_cmb_torpsdestroyyou);
            echo "--> $l_cmb_torpsdestroyyou<br>";
        }
        else
        {
            $attackerfighters = $attackerfighters - $targettorpdmg;
            $l_cmb_someonedestroyedfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_someonedestroyedfighters);
            $l_cmb_someonedestroyedfighters = str_replace("[cmb_targettorpdmg]", $targettorpdmg, $l_cmb_someonedestroyedfighters);
            echo "<-- $l_cmb_someonedestroyedfighters<br>";
            $targettorpdmg = 0;
        }
    }
    elseif ($attackerfighters > 0 && $targettorpdmg < 1)
    {
        $l_cmb_hehasnotorpsleftforyou = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hehasnotorpsleftforyou);
        echo "$l_cmb_hehasnotorpsleftforyou<br>";
    }
    else
    {
        $l_cmb_youhavenofightersanymore = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_youhavenofightersanymore);
        echo "$l_cmb_youhavenofightersanymore<br>";
    }

    if ($attackertorpdamage > 0)
    {
        if ($attackertorpdamage > $targetarmor)
        {
            $targetarmor = 0;
            $l_cmb_youbreachedwithtorps = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_youbreachedwithtorps);
            echo "--> $l_cmb_youbreachedwithtorps<br>";
        }
        else
        {
            $targetarmor = $targetarmor - $attackertorpdamage;
            $l_cmb_hisarmorishitbytorps = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hisarmorishitbytorps);
            $l_cmb_hisarmorishitbytorps = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_hisarmorishitbytorps);
            echo "<-- $l_cmb_hisarmorishitbytorps<br>";
        }
    }
    else
    {
        $l_cmb_notorpslefttoattackarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_notorpslefttoattackarmor);
        echo "$l_cmb_notorpslefttoattackarmor<br>";
    }

    if ($targettorpdmg > 0)
    {
        if ($targettorpdmg > $attackerarmor)
        {
            $attackerarmor = 0;
            $l_cmb_yourarmorbreachedbytorps = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourarmorbreachedbytorps);
            echo "<-- $l_cmb_yourarmorbreachedbytorps<br>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $targettorpdmg;
            $l_cmb_yourarmorhitdmgtorps = str_replace("[cmb_targettorpdmg]", $targettorpdmg, $l_cmb_yourarmorhitdmgtorps);
            $l_cmb_yourarmorhitdmgtorps = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourarmorhitdmgtorps);
            echo "<-- $l_cmb_yourarmorhitdmgtorps<br>";
        }
    }
    else
    {
        $l_cmb_hehasnotorpsforyourarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hehasnotorpsforyourarmor);
        echo "$l_cmb_hehasnotorpsforyourarmor<br>";
    }

    echo "<br>$l_cmb_fightersattackexchange<br>";
    if ($attackerfighters > 0 && $targetfighters > 0)
    {
        if ($attackerfighters > $targetfighters)
        {
            $l_cmb_enemylostallfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_enemylostallfighters);
            echo "--> $l_cmb_enemylostallfighters<br>";
            $temptargfighters = 0;
        }
        else
        {
            $l_cmb_helostsomefighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_helostsomefighters);
            $l_cmb_helostsomefighters = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_helostsomefighters);
            echo "$l_cmb_helostsomefighters<br>";
            $temptargfighters = $targetfighters - $attackerfighters;
        }
        if ($targetfighters > $attackerfighters)
        {
            echo "<-- $l_cmb_youlostallfighters<br>";
            $tempplayfighters = 0;
        }
        else
        {
            $l_cmb_youalsolostsomefighters = str_replace("[cmb_targetfighters]", $targetfighters, $l_cmb_youalsolostsomefighters);
            echo "<-- $l_cmb_youalsolostsomefighters<br>";
            $tempplayfighters = $attackerfighters - $targetfighters;
        }
        $attackerfighters = $tempplayfighters;
        $targetfighters = $temptargfighters;
    }
    elseif ($attackerfighters > 0 && $targetfighters < 1)
    {
        $l_cmb_hehasnofightersleftattack = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hehasnofightersleftattack);
        echo "$l_cmb_hehasnofightersleftattack<br>";
    }
    else
    {
        $l_cmb_younofightersattackleft = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_younofightersattackleft);
        echo "$l_cmb_younofightersattackleft<br>";
    }

    if ($attackerfighters > 0)
    {
        if ($attackerfighters > $targetarmor)
        {
            $targetarmor = 0;
            $l_cmb_youbreachedarmorwithfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_youbreachedarmorwithfighters);
            echo "--> $l_cmb_youbreachedarmorwithfighters<br>";
        }
        else
        {
            $targetarmor = $targetarmor - $attackerfighters;
            $l_cmb_youhitarmordmgfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_youhitarmordmgfighters);
            $l_cmb_youhitarmordmgfighters = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_youhitarmordmgfighters);
            echo "<-- $l_cmb_youhitarmordmgfighters<br>";
        }
    }
    else
    {
        $l_cmb_youhavenofighterstoarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_youhavenofighterstoarmor);
        echo "$l_cmb_youhavenofighterstoarmor<br>";
    }

    if ($targetfighters > 0)
    {
        if ($targetfighters > $attackerarmor)
        {
            $attackerarmor = 0;
            $l_cmb_hasbreachedarmorfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hasbreachedarmorfighters);
            echo "<-- $l_cmb_hasbreachedarmorfighters<br>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $targetfighters;
            $l_cmb_yourarmorishitfordmgby = str_replace("[cmb_targetfighters]", $targetfighters, $l_cmb_yourarmorishitfordmgby);
            $l_cmb_yourarmorishitfordmgby = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_yourarmorishitfordmgby);
            echo "--> $l_cmb_yourarmorishitfordmgby<br>";
        }
    }
    else
    {
        $l_cmb_nofightersleftheforyourarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_nofightersleftheforyourarmor);
        echo "$l_cmb_nofightersleftheforyourarmor<br>";
    }

    if ($targetarmor < 1)
    {
        $l_cmb_hehasbeendestroyed = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_hehasbeendestroyed);
        echo "<br>$l_cmb_hehasbeendestroyed<br>";

        // Dynamic functions
        dynamic_loader ($db, "playerdeath.php");
        playerdeath($db,$targetinfo['player_id'], "LOG_ATTACK_LOSE", "$playerinfo[character_name]|Y", 1, $playerinfo['player_id'] ,$targetinfo['currentship']);

        if ($attackerarmor > 0)
        {
            $rating_change = round($targetinfo['rating']*$rating_combat_factor);
            $free_ore = round($targetinfo['ore']/2);
            $free_organics = round($targetinfo['organics']/2);
            $free_goods = round($targetinfo['goods']/2);
            $free_holds = num_holds($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
            if ($free_holds > $free_goods)
            {
                $salv_goods = $free_goods;
                $free_holds = $free_holds - $free_goods;
            }
            elseif ($free_holds > 0)
            {
                $salv_goods = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_goods = 0;
            }

            if ($free_holds > $free_ore)
            {
                $salv_ore = $free_ore;
                $free_holds = $free_holds - $free_ore;
            }
            elseif ($free_holds > 0)
            {
                $salv_ore = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_ore = 0;
            }

            if ($free_holds > $free_organics)
            {
                $salv_organics = $free_organics;
                $free_holds = $free_holds - $free_organics;
            }
            elseif ($free_holds > 0)
            {
                $salv_organics = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_organics = 0;
            }

            if ($plasma_engines)
            {
                $ship_value = $upgrade_cost*(round(pow($upgrade_factor, $targetinfo['hull']))+round(pow($upgrade_factor, $targetinfo['engines']))+round(pow($upgrade_factor, $targetinfo['pengines']))+round(pow($upgrade_factor, $targetinfo['power']))+round(pow($upgrade_factor, $targetinfo['computer']))+round(pow($upgrade_factor, $targetinfo['sensors']))+round(pow($upgrade_factor, $targetinfo['beams']))+round(pow($upgrade_factor, $targetinfo['torp_launchers']))+round(pow($upgrade_factor, $targetinfo['shields']))+round(pow($upgrade_factor, $targetinfo['armor']))+round(pow($upgrade_factor, $targetinfo['cloak'])));
            }
            else
            {
                $ship_value = $upgrade_cost*(round(pow($upgrade_factor, $targetinfo['hull']))+round(pow($upgrade_factor, $targetinfo['engines']))+round(pow($upgrade_factor, $targetinfo['power']))+round(pow($upgrade_factor, $targetinfo['computer']))+round(pow($upgrade_factor, $targetinfo['sensors']))+round(pow($upgrade_factor, $targetinfo['beams']))+round(pow($upgrade_factor, $targetinfo['torp_launchers']))+round(pow($upgrade_factor, $targetinfo['shields']))+round(pow($upgrade_factor, $targetinfo['armor']))+round(pow($upgrade_factor, $targetinfo['cloak'])));
            }

            $ship_salvage_rate = mt_rand(10,20);
            $ship_salvage = $ship_value * $ship_salvage_rate/100;
            $l_cmb_yousalvaged1 = str_replace("[cmb_salv_ore]", $salv_ore, $l_cmb_yousalvaged1);
            $l_cmb_yousalvaged1 = str_replace("[cmb_salv_organics]", $salv_organics, $l_cmb_yousalvaged1);
            $l_cmb_yousalvaged1 = str_replace("[cmb_salv_goods]", $salv_goods, $l_cmb_yousalvaged1);
            $l_cmb_yousalvaged1 = str_replace("[cmb_salvage_rate]", $ship_salvage_rate, $l_cmb_yousalvaged1);
            $l_cmb_yousalvaged1 = str_replace("[cmb_salvage]", $ship_salvage, $l_cmb_yousalvaged1);
            $l_cmb_yousalvaged2 = str_replace("[cmb_number_rating_change]", number_format(abs($rating_change), 0, $local_number_dec_point, $local_number_thousands_sep), $l_cmb_yousalvaged2);

            echo $l_cmb_yousalvaged1 . "<br>" . $l_cmb_yousalvaged2;
            $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET ore=ore+?, organics=organics+?, " .
                                         "goods=goods+? WHERE ship_id=?", array($salv_ore, $salv_organics, $salv_goods, $shipinfo['ship_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $debug_query = $db->Execute ("UPDATE {$db->prefix}players SET credits=credits+? " .
                                         "WHERE player_id=?", array($ship_salvage, $playerinfo['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
    else
    {
        $l_cmb_youdidntdestroyhim = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['name'], $l_cmb_youdidntdestroyhim);
        echo "$l_cmb_youdidntdestroyhim<br>";
    
        $target_rating_change = round($targetinfo['rating']*.1);
        $target_armor_lost = $targetinfo['armor_pts']-$targetarmor;
        $target_fighters_lost = $targetinfo['fighters']-$targetfighters;
        $target_energy = $targetinfo['energy'];
        playerlog($db,$targetinfo['player_id'], "LOG_ATTACKED_WIN", "$playerinfo[character_name]|$target_armor_lost|$target_fighters_lost");
    
        $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET energy=?, " .
                                     "fighters=fighters-?, armor_pts=armor_pts-?, " .
                                     "torps=torps-? WHERE ship_id=?", array($target_energy, $target_fighters_lost, $target_armor_lost, $targettorpnum, $targetinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
}
?>
