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
// File: planet2.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "transfer_to_planet.php"); 
dynamic_loader ($db, "transfer_to_ship.php"); 
dynamic_loader ($db, "spy_detect_planet.php"); 
dynamic_loader ($db, "spy_sneak_to_ship.php"); 
dynamic_loader ($db, "spy_sneak_to_planet.php");
dynamic_loader ($db, "col_count_news.php");
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'spy');
load_languages($db, $raw_prefix, 'traderoute');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_planet2_title;
updatecookie($db);

if (isset($_GET['planet_id']))
{
    $planet_id = $_GET['planet_id'];
}
elseif (isset($_POST['planet_id']))
{
    $planet_id = $_POST['planet_id'];
}
else
{
    $planet_id = '';
}

if (!isset($planetinfo))
{
    $planetinfo = '';
}

seed_mt_rand();

//-------------------------------------------------------------------------------------------------

$result2 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($planet_id));
if ($result2)
{
    $planetinfo = $result2->fields;
}

echo "<h1>" . $title. "</h1>\n";

if ($planetinfo['sector_id'] != $shipinfo['sector_id'])
{
    echo "$l_planet2_sector<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($playerinfo['turns'] < 1)
{
    echo "$l_planet2_noturn<br><br>";
}
else
{
    if ($spy_success_factor)
    {
        spy_detect_planet($db,$shipinfo['ship_id'], $planetinfo['planet_id'], $planet_detect_success1);
    }

    $free_holds = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
    $fighter_max = num_level($shipinfo['computer'], $level_factor, $level_magnitude) - $shipinfo['fighters'];
    $torpedo_max = num_level($shipinfo['torp_launchers'], $level_factor, $level_magnitude) - $shipinfo['torps'];
    $free_power = 5 * num_level($shipinfo['power'], $level_factor, $level_magnitude) - $shipinfo['energy'];
} // Is this close-bracket supposed to be here????

if ((!isset($_GET['planetupgrade'])) || ($_GET['planetupgrade'] == ''))
{
    $planetupgrade = '';
}
else
{
    $planetupgrade = $_GET['planetupgrade'];
}

if ((!isset($_POST['tpore'])) || ($_POST['tpore'] == ''))
{
    $tpore = 0;
}
else
{
    $tpore = $_POST['tpore'];
}

if ((!isset($_POST['tporganics'])) || ($_POST['tporganics'] == ''))
{
    $tporganics = 0;
}
else
{
    $tporganics = $_POST['tporganics'];
}

if ((!isset($_POST['tpgoods'])) || ($_POST['tpgoods'] == ''))
{
    $tpgoods = 0;
}
else
{
    $tpgoods = $_POST['tpgoods'];
}

if ((!isset($_POST['tpenergy'])) || ($_POST['tpenergy'] == ''))
{
    $tpenergy = 0;
}
else
{
    $tpenergy = $_POST['tpenergy'];
}

if ((!isset($_POST['tpcolonists'])) || ($_POST['tpcolonists'] == ''))
{
    $tpcolonists = 0;
}
else
{
    $tpcolonists = $_POST['tpcolonists'];
}

if ((!isset($_POST['tpcredits'])) || ($_POST['tpcredits'] == ''))
{
    $tpcredits = 0;
}
else
{
    $tpcredits = $_POST['tpcredits'];
}

if ((!isset($_POST['tptorps'])) || ($_POST['tptorps'] == ''))
{
    $tptorps = 0;
}
else
{
    $tptorps = $_POST['tptorps'];
}

if ((!isset($_POST['tpfighters'])) || ($_POST['tpfighters'] == ''))
{
    $tpfighters = 0;
}
else
{
    $tpfighters = $_POST['tpfighters'];
}

if ((!isset($_POST['tpspies'])) || ($_POST['tpspies'] == ''))
{
    $tpspies = 0;
}
else
{
    $tpspies = $_POST['tpspies'];
}

if ((!isset($_POST['transfer_ore'])) || ($_POST['transfer_ore'] == ''))
{
    $transfer_ore = 0;
}
else
{
    $transfer_ore = $_POST['transfer_ore'];
}

if ((!isset($_POST['transfer_organics'])) || ($_POST['transfer_organics'] == ''))
{
    $transfer_organics = 0;
}
else
{
    $transfer_organics = $_POST['transfer_organics'];
}

if ((!isset($_POST['transfer_goods'])) || ($_POST['transfer_goods'] == ''))
{
    $transfer_goods = 0;
}
else
{
    $transfer_goods = $_POST['transfer_goods'];
}


if ((!isset($_POST['transfer_energy'])) || ($_POST['transfer_energy'] == ''))
{
    $transfer_energy = 0;
}
else
{
    $transfer_energy = $_POST['transfer_energy'];
}

if ((!isset($_POST['transfer_colonists'])) || ($_POST['transfer_colonists'] == ''))
{
    $transfer_colonists = 0;
}
else
{
    $transfer_colonists = $_POST['transfer_colonists'];
}


if ((!isset($_POST['transfer_credits'])) || ($_POST['transfer_credits'] == ''))
{
    $transfer_credits = 0;
}
else
{
    $transfer_credits = $_POST['transfer_credits'];
}

if ((!isset($_POST['transfer_torps'])) || ($_POST['transfer_torps'] == ''))
{
    $transfer_torps = 0;
}
else
{
    $transfer_torps = $_POST['transfer_torps'];
}

if ((!isset($_POST['transfer_fighters'])) || ($_POST['transfer_fighters'] == ''))
{
    $transfer_fighters = 0;
}
else
{
    $transfer_fighters = $_POST['transfer_fighters'];
}

if ((!isset($_POST['transfer_spies'])) || ($_POST['transfer_spies'] == ''))
{
    $transfer_spies = 0;
}
else
{
    $transfer_spies = $_POST['transfer_spies'];
}

if ((!isset($_POST['armor_upgrade'])) || ($_POST['armor_upgrade'] == ''))
{
    $armor_upgrade = 0;
}
else
{
    $armor_upgrade = $_POST['armor_upgrade'];
}

if ((!isset($_POST['allore'])) || ($_POST['allore'] == ''))
{
    $allore = 0;
}
else
{
    $allore = $_POST['allore'];
}

if ((!isset($_POST['allorganics'])) || ($_POST['allorganics'] == ''))
{
    $allorganics = 0;
}
else
{
    $allorganics = $_POST['allorganics'];
}

if ((!isset($_POST['allgoods'])) || ($_POST['allgoods'] == ''))
{
    $allgoods = 0;
}
else
{
    $allgoods = $_POST['allgoods'];
}

if ((!isset($_POST['allenergy'])) || ($_POST['allenergy'] == ''))
{
    $allenergy = 0;
}
else
{
    $allenergy = $_POST['allenergy'];
}


if ((!isset($_POST['allcolonists'])) || ($_POST['allcolonists'] == ''))
{
    $allcolonists = 0;
}
else
{
    $allcolonists = $_POST['allcolonists'];
}

if ((!isset($_POST['allcredits'])) || ($_POST['allcredits'] == ''))
{
    $allcredits = 0;
}
else
{
    $allcredits = $_POST['allcredits'];
}

if ((!isset($_POST['alltorps'])) || ($_POST['alltorps'] == ''))
{
    $alltorps = 0;
}
else
{
    $alltorps = $_POST['alltorps'];
}

if ((!isset($_POST['allfighters'])) || ($_POST['allfighters'] == ''))
{
    $allfighters = 0;
}
else
{
    $allfighters = $_POST['allfighters'];
}


if ((!isset($_POST['allspies'])) || ($_POST['allspies'] == ''))
{
    $allspies = 0;
}
else
{
    $allspies = $_POST['allspies'];
}

// first setup the tp flags
if ($tpore != -1)
{
    $tpore = 1;
}

if ($tporganics != -1)
{
    $tporganics  = 1;
}

if ($tpgoods != -1)
{
    $tpgoods = 1;
}

if ($tpenergy != -1)
{
    $tpenergy = 1;
}

if ($tpcolonists != -1)
{
    $tpcolonists = 1;
}

if ($tpcredits != -1)
{
    $tpcredits = 1;
}

if ($tptorps != -1)
{
    $tptorps = 1;
}

if ($tpfighters != -1)
{
    $tpfighters = 1;
}

if ($tpspies != -1)
{
    $tpspies = 1;
}

    
    // Now remove anything but numbers from the submitted form. Yes, even negative numbers. :)
    $transfer_ore = floor(preg_replace('/[^0-9]/','',$transfer_ore));
    $transfer_organics = floor(preg_replace('/[^0-9]/','',$transfer_organics));
    $transfer_goods = floor(preg_replace('/[^0-9]/','',$transfer_goods));
    $transfer_energy = floor(preg_replace('/[^0-9]/','',$transfer_energy));
    $transfer_colonists = floor(preg_replace('/[^0-9]/','',$transfer_colonists));
    $transfer_credits = floor(preg_replace('/[^0-9]/','',$transfer_credits));
    $transfer_torps = floor(preg_replace('/[^0-9]/','',$transfer_torps));
    $transfer_fighters = floor(preg_replace('/[^0-9]/','',$transfer_fighters));
    $transfer_spies = floor(preg_replace('/[^0-9]/','',$transfer_spies));

    if ($allore == -1)
    {
        if ($tpore == -1)
        {
            $transfer_ore = $shipinfo['ore'];
        }
        else
        {
            $transfer_ore = $planetinfo['ore'];
        }
    }

    if ($allorganics == -1)
    {
        if ($tporganics == -1)
        {
            $transfer_organics = $shipinfo['organics'];
        }
       else
       {
           $transfer_organics = $planetinfo['organics'];
       }
    }

    if ($allgoods == -1)
    {
        if ($tpgoods == -1)
        {
            $transfer_goods = $shipinfo['goods'];
        }
        else
        {
            $transfer_goods = $planetinfo['goods'];
        }
    }

    if ($allenergy == -1)
    {
        if ($tpenergy == -1)
        {
            $transfer_energy = $shipinfo['energy'];
        }
        else
        {
            $transfer_energy = $planetinfo['energy'];
        }
    }

    if ($allcolonists == -1)
    {
        if ($tpcolonists==-1)
        {
            $transfer_colonists = $shipinfo['colonists'];
        }
        else
        {
            $transfer_colonists = $planetinfo['colonists'];
        }
    }

    if ($allcredits == -1)
    {
        if ($tpcredits == -1)
        {
            $transfer_credits = floor($playerinfo['credits']);
        }
        else
        {
            $transfer_credits = floor($planetinfo['credits']);
        }
    }

    if ($alltorps == -1)
    {
        if ($tptorps == -1)
        {
            $transfer_torps = $shipinfo['torps'];
        }
        else
        {
            $transfer_torps = $planetinfo['torps'];
        }
    }

    if ($allfighters == -1)
    {
        if ($tpfighters == -1)
        {
            $transfer_fighters = $shipinfo['fighters'];
        }
        else
        {
            $transfer_fighters = $planetinfo['fighters'];
        }
    }

    if ($allspies == -1)
    {
        if ($tpspies == -1)
        {
            $res = $db->execute("SELECT * FROM {$db->prefix}spies WHERE ship_id=? AND owner_id=?", array($shipinfo['ship_id'], $playerinfo['player_id']));
            $transfer_spies = $res->RecordCount();
        }
        else
        {
            $res = $db->execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=? AND owner_id=?", array($planet_id, $playerinfo['player_id']));
            $transfer_spies = $res->RecordCount();
        }
    }

    // ok now get rid of all negative amounts so that all operations are expressed in terms of positive units
    if ($transfer_ore < 0)
    {
        $transfer_ore = -1 * $transfer_ore;
        $tpore = -1 * $tpore;
    }

    if ($transfer_organics < 0)
    {
        $transfer_organics = -1 * $transfer_organics;
        $tporganics = -1 * $tporganics;
    }

    if ($transfer_goods < 0)
    {
        $transfer_goods = -1 * $transfer_goods;
        $tpgoods = -1 * $tpgoods;
    }

    if ($transfer_energy < 0)
    {
        $transfer_energy = -1 * $transfer_energy;
        $tpenergy = -1 * $tpenergy;
    }

    if ($transfer_colonists < 0)
    {
        $transfer_colonists = -1 * $transfer_colonistst;
        $tpcolonists = -1 * $tpcolonists;
    }

    if ($transfer_credits < 0)
    {
        $transfer_credits = -1 * $transfer_credits;
        $tpcredits = -1 * $tpcredits;
    }

    if ($transfer_torps < 0)
    {
        $transfer_torps = -1 * $transfer_torps;
        $tptorps = -1 * $tptorps;
    }

    if ($transfer_fighters < 0)
    {
        $transfer_fighters = -1 * $transfer_fighters;
        $tpfighters = -1 * $tpfighters;
    }

    if ($transfer_spies < 0)
    {
        $transfer_spies = -1 * $transfer_spies;
        $tpspies = -1 * $tpspies;
    }
  
    if ($spy_success_factor)
    {
        spy_sneak_to_planet($db,$planetinfo['planet_id'], $shipinfo['ship_id']);
        spy_sneak_to_ship($db,$planetinfo['planet_id'], $shipinfo['ship_id']);

        if ($transfer_spies)
        {
            if ($tpspies<0)
            {
                $temp = transfer_to_planet($db,$playerinfo['player_id'], $planetinfo['planet_id'], $transfer_spies);
            }
            else
            {
                $temp = transfer_to_ship($db,$playerinfo['player_id'], $planetinfo['planet_id'], $transfer_spies);
            }

            echo "$temp $l_spy_transferred.<br>";
        }
    }

    // now make sure that the source for each commodity transfer has sufficient numbers to fill the transfer
    if (($tpore == -1) && ($transfer_ore > $shipinfo['ore']))
    {
        $transfer_ore = $shipinfo[ore];
        echo "$l_planet2_noten $l_ore. $l_planet2_settr $transfer_ore $l_units $l_ore.<br>\n";
    }
    elseif (($tpore == 1) && ($transfer_ore > $planetinfo['ore']))
    {
        $transfer_ore = $planetinfo['ore'];
        echo "$l_planet2_losup $transfer_ore $l_units $l_ore.<br>\n";
    }

    if (($tporganics == -1) && ($transfer_organics > $shipinfo['organics']))
    {
        $transfer_organics = $shipinfo['organics'];
        echo "$l_planet2_noten $l_organics. $l_planet2_settr $transfer_organics $l_units.<br>\n";
    }
    elseif (($tporganics == 1) && ($transfer_organics > $planetinfo['organics']))
    {
        $transfer_organics = $planetinfo['organics'];
        echo "$l_planet2_losup $transfer_organics $l_units $l_organics.<br>\n";
    }

    if (($tpgoods == -1) && ($transfer_goods > $shipinfo['goods']))
    {
        $transfer_goods = $shipinfo['goods'];
        echo "$l_planet2_noten $l_goods. $l_planet2_settr $transfer_goods $l_units.<br>\n";
    }
    elseif (($tpgoods == 1) && ($transfer_goods > $planetinfo['goods']))
    {
        $transfer_goods = $planetinfo['goods'];
        echo "$l_planet2_losup $transfer_goods $l_units $l_goods.<br>\n";
    }

    if (($tpenergy == -1) && ($transfer_energy > $shipinfo['energy']))
    {
        $transfer_energy = $shipinfo['energy'];
        echo "$l_planet2_noten $l_energy. $l_planet2_settr $transfer_energy $l_units.<br>\n";
    }
    elseif (($tpenergy == 1) && ($transfer_energy > $planetinfo['energy']))
    {
        $transfer_energy = $planetinfo['energy'];
        echo "$l_planet2_losup $transfer_energy $l_units $l_energy.<br>\n";
    }

    if (($tpcolonists == -1) && ($transfer_colonists > $shipinfo['colonists']))
    {
        $transfer_colonists = $shipinfo['colonists'];
        echo "$l_planet2_noten $l_colonists. $l_planet2_settr $transfer_colonists $l_colonists.<br>\n";
    }
    elseif (($tpcolonists == -1) && ($transfer_colonists + $planetinfo['colonists'] > $colonist_limit))
    {
        $transfer_colonists = $colonist_limit - $planetinfo['colonists'];
        echo "$l_tdr_planetisovercrowded.<br>";
    }
    elseif (($tpcolonists == 1) && ($transfer_colonists > $planetinfo['colonists']))
    {
        $transfer_colonists = $planetinfo['colonists'];
        echo "$l_planet2_losup $transfer_colonists $l_colonists.<br>\n";
    }

    if (($tpcredits == -1) && ($transfer_credits > $playerinfo['credits']))
    {
        $transfer_credits = $playerinfo['credits'];
        echo "$l_planet2_noten $l_credits. $l_planet2_settr $transfer_credits $l_credits.<br>\n";
    }
    elseif (($tpcredits == 1) && ($transfer_credits > $planetinfo['credits']))
    {
        $transfer_credits = $planetinfo['credits'];
        echo "$l_planet2_losup $transfer_credits $l_credits.<br>\n";
    }

    if (($tpcredits == -1) && $planetinfo['base'] == 'N' && ($transfer_credits + $planetinfo['credits'] > $max_credits_without_base))
    {
        $transfer_credits = MAX($max_credits_without_base - $planetinfo['credits'],0);
        echo "$l_planet2_baseexceeded $l_planet2_settr $transfer_credits $l_credits.<br>\n";
    }

    if (($tptorps == -1) && ($transfer_torps > $shipinfo['torps']))
    {
        $transfer_torps = $shipinfo['torps'];
        echo "$l_planet2_noten $l_torps. $l_planet2_settr $transfer_torps $l_torps.<br>\n";
    }
    elseif (($tptorps == 1) && ($transfer_torps > $planetinfo['torps']))
    {
        $transfer_torps = $planetinfo['torps'];
        echo "$l_planet2_losup $transfer_torps $l_torps.<br>\n";
    }

    if (($tpfighters == -1) && ($transfer_fighters > $shipinfo['fighters']))
    {
        $transfer_fighters = $shipinfo['fighters'];
        echo "$l_planet2_noten $l_fighters. $l_planet2_settr $transfer_fighters $l_fighters.<br>\n";
    }
    elseif (($tpfighters == 1) && ($transfer_fighters > $planetinfo['fighters']))
    {
        $transfer_fighters = $planetinfo['fighters'];
        echo "$l_planet2_losup $transfer_fighters $l_fighters.<br>\n";
    }

    // Now we have to set negative values for any tpflag = -1's. We cant multiply by their tpflag, because
    // it may be larger than a float - which would give inaccurate numbers, and thus cause neg values.
    if ($tpore < 0)
    {
        $transfer_ore = "-" . $transfer_ore;
    }

    if ($tporganics < 0)
    {
        $transfer_organics = "-" . $transfer_organics;
    }

    if ($tpgoods < 0)
    {
        $transfer_goods = "-" . $transfer_goods;
    }

    if ($tpenergy < 0)
    {
        $transfer_energy = "-" . $transfer_energy;
    }

    if ($tpcolonists < 0)
    {
        $transfer_colonists = "-" . $transfer_colonists;
    }

    if ($tpcredits < 0)
    {
        $transfer_credits = "-" . $transfer_credits;
    }

    if ($tptorps < 0)
    {
        $transfer_torps = "-" . $transfer_torps;
    }

    if ($tpfighters < 0)
    {
        $transfer_fighters = "-" . $transfer_fighters;
    }

    $total_holds_needed = $transfer_ore + $transfer_organics + $transfer_goods + $transfer_colonists;

    if ($playerinfo['player_id'] != $planetinfo['owner'] && $transfer_credits != 0 && $team_planet_transfers != 1)
    {
        echo "$l_planet2_noteamtransfer<p>";
        echo "<a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu<br><br>";
    }
    elseif ($total_holds_needed > $free_holds)
    {
        echo "$l_planet2_noten $l_holds $l_planet2_fortr<br><br>";
        echo "<a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu<br><br>";
    }
    else
    {
        if ((!empty($planetinfo)) && (empty($planetupgrade)))
        {
            if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] != 0))
            {
                if ($transfer_ore < 0 && $shipinfo['ore'] < floor(abs($transfer_ore)))
                {
                    echo "$l_planet2_noten $l_ore $l_planet2_fortr<br>";
                    $transfer_ore = 0;
                }
                elseif ($transfer_ore > 0 && $planetinfo['ore'] < $transfer_ore)
                {
                    echo "$l_planet2_noten $l_ore $l_planet2_fortr<br>";
                    $transfer_ore = 0;
                }

                if ($transfer_organics < 0 && $shipinfo['organics'] <  floor(abs($transfer_organics)))
                {
                    echo "$l_planet2_noten $l_organics $l_planet2_fortr<br>";
                    $transfer_organics = 0;
                }
                elseif ($transfer_organics > 0 && $planetinfo['organics'] < $transfer_organics)
                {
                    echo "$l_planet2_noten $l_organics $l_planet2_fortr<br>";
                    $transfer_organics = 0;
                }

                if ($transfer_goods < 0 && $shipinfo['goods'] < floor(abs($transfer_goods)))
                {
                    echo "$l_planet2_noten $l_goods $l_planet2_fortr<br>";
                    $transfer_goods = 0;
                }
                elseif ($transfer_goods > 0 && $planetinfo['goods'] < $transfer_goods)
                {
                    echo "$l_planet2_noten $l_goods $l_planet2_fortr<br>";
                    $transfer_goods = 0;
                }

                if ($transfer_energy < 0 && $shipinfo['energy'] <  floor(abs($transfer_energy)))
                {
                    echo "$l_planet2_noten $l_energy $l_planet2_fortr<br>";
                    $transfer_energy = 0;
                }
                elseif ($transfer_energy > 0 && $planetinfo['energy'] < $transfer_energy)
                {
                    echo "$l_planet2_noten $l_energy $l_planet2_fortr<br>";
                    $transfer_energy = 0;
                }
                elseif ($transfer_energy > 0 && $transfer_energy > $free_power)
                {
                    echo "$l_planet2_noten $l_planet2_power $l_planet2_fortr<br>";
                    $transfer_energy = 0;
                }

                if ($transfer_colonists < 0 && $shipinfo['colonists'] <  floor(abs($transfer_colonists)))
                {
                    echo "$l_planet2_noten $l_colonists $l_planet2_fortr<br>";
                    $transfer_colonists = 0;
                }
                elseif ($transfer_colonists > 0 && $planetinfo['colonists'] < $transfer_colonists)
                {
                    echo "$l_planet2_noten $l_colonists $l_planet2_fortr<br>";
                    $transfer_colonists = 0;
                }

                if ($transfer_fighters < 0 && $shipinfo['fighters'] <  floor(abs($transfer_fighters)))
                {
                    echo "$l_planet2_noten $l_fighters $l_planet2_fortr<br>";
                    $transfer_fighters = 0;
                }
                elseif ($transfer_fighters > 0 && $planetinfo['fighters'] < $transfer_fighters)
                {
                    echo "$l_planet2_noten $l_fighters $l_planet2_fortr<br>";
                    $transfer_fighters = 0;
                }
                elseif ($transfer_fighters > 0 && $transfer_fighters > $fighter_max)
                {
                    echo "$l_planet2_noten $l_planet2_comp $l_planet2_fortr<br>";
                    $transfer_fighters = 0;
                }

                if ($transfer_torps < 0 && $shipinfo['torps'] <  floor(abs($transfer_torps)))
                {
                    echo "$l_planet2_noten $l_torpedoes $l_planet2_fortr<br>";
                    $transfer_torps = 0;
                }
                elseif ($transfer_torps > 0 && $planetinfo['torps'] < $transfer_torps)
                {
                    echo "$l_planet2_noten $l_torpedoes $l_planet2_fortr<br>";
                    $transfer_torps = 0;
                }
                elseif ($transfer_torps > 0 && $transfer_torps > $torpedo_max)
                {
                    echo "$l_planet2_noten $l_planet2_laun $l_planet2_fortr<br>";
                    $transfer_torps = 0;
                }

                if ($transfer_credits < 0 && $playerinfo['credits'] <  floor(abs($transfer_credits)))
                {
                    echo "First failure: transfer_credits=$transfer_credits, playerinfo[credits]=$playerinfo[credits]: ";
                    echo "$l_planet2_noten $l_credits $l_planet2_fortr<br>";
                    $transfer_credits = 0;
                }
                elseif ($transfer_credits > 0 && $planetinfo['credits'] < $transfer_credits)
                {
                    echo "Second failure: transfer_credits=$transfer_credits, playerinfo[credits]=$playerinfo[credits]: ";
                    echo "$l_planet2_noten $l_credits $l_planet2_fortr<br>";
                    $transfer_credits = 0;
                }

                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET ore=ore+?, organics=organics+?, goods=goods+?, energy=energy+?, colonists=colonists+?, torps=torps+?, fighters=fighters+? WHERE ship_id=?", array($transfer_ore, $transfer_organics, $transfer_goods, $transfer_energy, $transfer_colonists, $transfer_torps, $transfer_fighters, $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits+?, turns=turns-1, turns_used=turns_used+1 WHERE player_id=?", array($transfer_credits, $playerinfo['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET ore=ore-?, organics=organics-?, goods=goods-?, energy=energy-?, colonists=colonists-?, torps=torps-?, fighters=fighters-?, credits=credits-? WHERE planet_id=?", array($transfer_ore, $transfer_organics, $transfer_goods, $transfer_energy, $transfer_colonists, $transfer_torps, $transfer_fighters, $transfer_credits, $planet_id));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "$l_planet2_compl<br><a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu<br><br>";
                col_count_news($db, $playerinfo['player_id']);
            }
            else
            {
                echo "$l_planet2_notowner<br><br>";
            }
        }
        elseif ((!empty($planetinfo)) && (!empty($planetupgrade)))
        {
            $cloak_upgrade = $_POST['cloak_upgrade'];
            $shields_upgrade = $_POST['shields_upgrade'];
            $torp_launchers_upgrade = $_POST['torp_launchers_upgrade'];
            $beams_upgrade = $_POST['beams_upgrade'];
            $sensors_upgrade = $_POST['sensors_upgrade'];
            $computer_upgrade = $_POST['computer_upgrade'];

            if ($planetinfo['base'] == "N")
            {
                echo "You must have a base to perform an upgrade.";
                break;
            }

            $color_red     = "red";
            $color_green   = "#00FF00"; // Light green
            $trade_deficit = "$l_cost : ";
            $trade_benefit = "$l_profit : ";

            // Dynamic functions
            dynamic_loader ($db, "buildonecol.php");
            dynamic_loader ($db, "buildtwocol.php");
            dynamic_loader ($db, "phpchangedelta.php");

            // Computer
            $computer_upgrade_cost = 0;
            if ($computer_upgrade > 54)
            {
                $computer_upgrade = 54;
            }

            if ($computer_upgrade < 0)
            {
                $computer_upgrade = 0;
            }

            if ($computer_upgrade > $planetinfo['computer'])
            {
                $computer_upgrade_cost = phpChangeDelta($computer_upgrade, $planetinfo['computer'],$upgrade_cost,$upgrade_factor);
            }

            // Sensors
            $sensors_upgrade_cost = 0;
            if ($sensors_upgrade > 54)
            {
                $sensors_upgrade = 54;
            }

            if ($sensors_upgrade < 0)
            {
                $sensors_upgrade = 0;
            }

            if ($sensors_upgrade > $planetinfo['sensors'])
            {
                $sensors_upgrade_cost = phpChangeDelta($sensors_upgrade, $planetinfo['sensors'],$upgrade_cost,$upgrade_factor);
            }

            // Beams
            $beams_upgrade_cost = 0;
            if ($beams_upgrade > 54)
            {
                $beams_upgrade = 54;
            }

            if ($beams_upgrade < 0)
            {
                $beams_upgrade = 0;
            }

            if ($beams_upgrade > $planetinfo['beams'])
            {
                $beams_upgrade_cost = phpChangeDelta($beams_upgrade, $planetinfo['beams'],$upgrade_cost,$upgrade_factor);
            }

            // armor
            $armor_upgrade_cost = 0;
            if ($armor_upgrade > 54)
            {
                $armor_upgrade = 54;
            }

            if ($armor_upgrade < 0)
            {
                $armor_upgrade = 0;
            }

            if ($armor_upgrade > $planetinfo['armor'])
            {
                $armor_upgrade_cost = phpChangeDelta($armor_upgrade, $planetinfo['armor'],$upgrade_cost,$upgrade_factor);
            }

            // Cloak
            $cloak_upgrade_cost = 0;
            if ($cloak_upgrade > 54)
            {
                $cloak_upgrade = 54;
            }

            if ($cloak_upgrade < 0)
            {
                $cloak_upgrade = 0;
            }

            if ($cloak_upgrade > $planetinfo['cloak'])
            {
                $cloak_upgrade_cost = phpChangeDelta($cloak_upgrade, $planetinfo['cloak'],$upgrade_cost,$upgrade_factor);
            }

            // Torp_launchers
            $torp_launchers_upgrade_cost = 0;
            if ($torp_launchers_upgrade > 54)
            {
                $torp_launchers_upgrade = 54;
            }

            if ($torp_launchers_upgrade < 0)
            {
                $torp_launchers_upgrade = 0;
            }

            if ($torp_launchers_upgrade > $planetinfo['torp_launchers'])
            {
                $torp_launchers_upgrade_cost = phpChangeDelta($torp_launchers_upgrade, $planetinfo['torp_launchers'],$upgrade_cost,$upgrade_factor);
            }

            // Shields
            $shields_upgrade_cost = 0;
            if ($shields_upgrade > 54)
            {
                $shields_upgrade = 54;
            }

            if ($shields_upgrade < 0)
            {
                $shields_upgrade = 0;
            }

            if ($shields_upgrade > $planetinfo['shields'])
            {
                $shields_upgrade_cost = phpChangeDelta($shields_upgrade, $planetinfo['shields'],$upgrade_cost,$upgrade_factor);
            }

            $total_cost = $computer_upgrade_cost +
            $sensors_upgrade_cost + $beams_upgrade_cost + $cloak_upgrade_cost +
            $torp_launchers_upgrade_cost + $shields_upgrade_cost;

            if ($total_cost > $playerinfo['credits'])
            {
                echo "You do not have enough credits for this transaction.  The total cost is " . number_format($total_cost, 0, $local_number_dec_point, $local_number_thousands_sep) . " credits and you only have " . number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " credits.";
            }
            else
            {
                $trade_credits = number_format($total_cost, 0, $local_number_dec_point, $local_number_thousands_sep);
                echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=\"" . $color_line2 . "\" width=600 align=center><tr>";
                echo "<td colspan=99 align=center bgcolor=\"" . $color_line1 . "\"><font color=white><strong>$l_trade_result</strong></font></td>";
                echo "</tr><tr>";
                echo "<td colspan=99 align=center><strong><font color=red>$l_cost : " . $trade_credits . " $l_credits</font></strong></td>";
                echo "</tr>";

                //  Total cost is " . number_format(abs($total_cost), 0, $local_number_dec_point, $local_number_thousands_sep) . " credits.<br><br>";
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits-?, turns=turns-1, turns_used=turns_used+1 WHERE player_id=?", array($total_cost, $playerinfo['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                // DB NOT CLEANED!
                $query = "UPDATE {$db->prefix}planets SET planet_id=$planet_id ";

                // Computer
                if ($computer_upgrade > $planetinfo['computer'])
                {
                    $tempvar = 0;
                    $tempvar = $computer_upgrade - $planetinfo['computer'];
                    $query = $query . ", computer=computer+$tempvar";
                    BuildOneCol("$l_computer $l_trade_upgraded $computer_upgrade");
                }

                // Sensors
                if ($sensors_upgrade > $planetinfo['sensors'])
                {
                    $tempvar = 0;
                    $tempvar = $sensors_upgrade - $planetinfo['sensors'];
                    $query = $query . ", sensors=sensors+$tempvar";
                    BuildOneCol("$l_sensors $l_trade_upgraded $sensors_upgrade");
                }

                // Beam Weapons
                if ($beams_upgrade > $planetinfo['beams'])
                {
                    $tempvar = 0;
                    $tempvar = $beams_upgrade - $planetinfo['beams'];
                    $query = $query . ", beams=beams+$tempvar";
                    BuildOneCol("$l_beams $l_trade_upgraded $beams_upgrade");
                }

                // Torpedo Launchers
                if ($torp_launchers_upgrade > $planetinfo['torp_launchers'])
                {
                    $tempvar = 0;
                    $tempvar = $torp_launchers_upgrade - $planetinfo['torp_launchers'];
                    $query = $query . ", torp_launchers=torp_launchers+$tempvar";
                    BuildOneCol("$l_torp_launch $l_trade_upgraded $torp_launchers_upgrade");
                }

                // Shields
                if ($shields_upgrade > $planetinfo['shields'])
                {
                    $tempvar = 0;
                    $tempvar = $shields_upgrade - $planetinfo['shields'];
                    $query = $query . ", shields=shields+$tempvar";
                    BuildOneCol("$l_shields $l_trade_upgraded $shields_upgrade");
                }

                // Cloak
                if ($cloak_upgrade > $planetinfo['cloak'])
                {
                    $tempvar = 0;
                    $tempvar = $cloak_upgrade - $planetinfo['cloak'];
                    $query = $query . ", cloak=cloak+$tempvar";
                    BuildOneCol("$l_cloak $l_trade_upgraded $cloak_upgrade");
                }

                $query = $query . " WHERE planet_id=$planet_id";
                $debug_query = $db->Execute("$query");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "</table>";
                echo "<br><br>";
                echo "<a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu<br><br>";
            }
        }
    }
}

//    elseif
//    {
//      echo "$l_planet_none<br><br>";
//    }
// This used to be part of the code, but does not appear neccesary any longer.
//

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
