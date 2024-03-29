<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: sector_fighters.php

if (strpos($_SERVER['PHP_SELF'], 'sector_fighters.php')) // Prevent direct access to this file
{
    die('Blacknova Traders error: You cannot access this file directly.');
}

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('sector_fighters', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));

echo $langvars['l_sf_attacking'] . "<br>";
$targetfighters = $total_sector_fighters;
$playerbeams = Bnt\CalcLevels::beams($playerinfo['beams'], $level_factor);
if ($calledfrom == 'rsmove.php')
{
    $playerinfo['ship_energy'] += $energyscooped;
}

if ($playerbeams > $playerinfo['ship_energy'])
{
    $playerbeams = $playerinfo['ship_energy'];
}

$playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playerbeams;
$playershields = Bnt\CalcLevels::shields($playerinfo['shields'], $level_factor);

if ($playershields > $playerinfo['ship_energy'])
{
    $playershields = $playerinfo['ship_energy'];
}
$playertorpnum = round(pow($level_factor, $playerinfo['torp_launchers'])) * 2;

if ($playertorpnum > $playerinfo['torps'])
{
    $playertorpnum = $playerinfo['torps'];
}

$playertorpdmg = $torp_dmg_rate * $playertorpnum;
$playerarmor = $playerinfo['armor_pts'];
$playerfighters = $playerinfo['ship_fighters'];
if ($targetfighters > 0 && $playerbeams > 0)
{
    if ($playerbeams > round($targetfighters / 2))
    {
        $temp = round($targetfighters / 2);
        $lost = $targetfighters - $temp;
        $langvars['l_sf_destfight'] = str_replace("[lost]", $lost, $langvars['l_sf_destfight']);
        echo $langvars['l_sf_destfight'] . "<br>";
        $targetfighters = $temp;
        $playerbeams = $playerbeams - $lost;
    }
    else
    {
        $targetfighters = $targetfighters - $playerbeams;
        $langvars['l_sf_destfightb'] = str_replace("[lost]", $playerbeams, $langvars['l_sf_destfightb']);
        echo $langvars['l_sf_destfightb'] . "<br>";
        $playerbeams = 0;
    }
}

echo "<br>" . $langvars['l_sf_torphit'] . "<br>";
if ($targetfighters > 0 && $playertorpdmg > 0)
{
    if ($playertorpdmg > round($targetfighters / 2))
    {
        $temp = round($targetfighters / 2);
        $lost = $targetfighters - $temp;
        $langvars['l_sf_destfightt'] = str_replace("[lost]", $lost, $langvars['l_sf_destfightt']);
        echo $langvars['l_sf_destfightt'] . "<br>";
        $targetfighters = $temp;
        $playertorpdmg = $playertorpdmg - $lost;
    }
    else
    {
        $targetfighters = $targetfighters - $playertorpdmg;
        $langvars['l_sf_destfightt'] = str_replace("[lost]", $playertorpdmg, $langvars['l_sf_destfightt']);
        echo $langvars['l_sf_destfightt'];
        $playertorpdmg = 0;
    }
}

echo "<br>" . $langvars['l_sf_fighthit'] . "<br>";
if ($playerfighters > 0 && $targetfighters > 0)
{
    if ($playerfighters > $targetfighters)
    {
        echo $langvars['l_sf_destfightall'] . "<br>";
        $temptargfighters = 0;
    }
    else
    {
        $langvars['l_sf_destfightt2'] = str_replace("[lost]", $playerfighters, $langvars['l_sf_destfightt2']);
        echo $langvars['l_sf_destfightt2'] . "<br>";
        $temptargfighters = $targetfighters - $playerfighters;
    }

    if ($targetfighters > $playerfighters)
    {
        echo $langvars['l_sf_lostfight'] . "<br>";
        $tempplayfighters = 0;
    }
    else
    {
         $langvars['l_sf_lostfight2'] = str_replace("[lost]", $targetfighters, $langvars['l_sf_lostfight2']);
         echo $langvars['l_sf_lostfight2'] . "<br>";
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
        echo $langvars['l_sf_armorbreach'] . "<br>";
    }
    else
    {
        $playerarmor = $playerarmor - $targetfighters;
        $langvars['l_sf_armorbreach2'] = str_replace("[lost]", $targetfighters, $langvars['l_sf_armorbreach2']);
        echo $langvars['l_sf_armorbreach2'] . "<br>";
    }
}

$fighterslost = $total_sector_fighters - $targetfighters;
Bnt\Fighters::destroy($db, $sector, $fighterslost);

$langvars['l_sf_sendlog'] = str_replace("[player]", $playerinfo['character_name'], $langvars['l_sf_sendlog']);
$langvars['l_sf_sendlog'] = str_replace("[lost]", $fighterslost, $langvars['l_sf_sendlog']);
$langvars['l_sf_sendlog'] = str_replace("[sector]", $sector, $langvars['l_sf_sendlog']);

Bnt\SectorDefense::messageDefenseOwner($db, $sector, $langvars['l_sf_sendlog']);
Bnt\PlayerLog::writeLog($db, $playerinfo['ship_id'], LOG_DEFS_DESTROYED_F, "$fighterslost|$sector");
$armor_lost = $playerinfo['armor_pts'] - $playerarmor;
$fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
$energy = $playerinfo['ship_energy'];
$update4b = $db->Execute("UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, armor_pts = armor_pts - ?, torps = torps - ? WHERE ship_id = ?;", array($energy, $fighters_lost, $armor_lost, $playertorpnum, $playerinfo['ship_id']));
Bnt\Db::logDbErrors($db, $update4b, __LINE__, __FILE__);
$langvars['l_sf_lreport'] = str_replace("[armor]", $armor_lost, $langvars['l_sf_lreport']);
$langvars['l_sf_lreport'] = str_replace("[fighters]", $fighters_lost, $langvars['l_sf_lreport']);
$langvars['l_sf_lreport'] = str_replace("[torps]", $playertorpnum, $langvars['l_sf_lreport']);
echo $langvars['l_sf_lreport'] . "<br><br>";
if ($playerarmor < 1)
{
    echo $langvars['l_sf_shipdestroyed'] . "<br><br>";
    Bnt\PlayerLog::writeLog($db, $playerinfo['ship_id'], LOG_DEFS_KABOOM, "$sector|$playerinfo[dev_escapepod]");
    $langvars['l_sf_sendlog2'] = str_replace("[player]", $playerinfo['character_name'], $langvars['l_sf_sendlog2']);
    $langvars['l_sf_sendlog2'] = str_replace("[sector]", $sector, $langvars['l_sf_sendlog2']);
    Bnt\SectorDefense::messageDefenseOwner($db, $sector, $langvars['l_sf_sendlog2']);
    if ($playerinfo['dev_escapepod'] == 'Y')
    {
        $rating = round($playerinfo['rating'] / 2);
        echo $langvars['l_sf_escape'] . "<br><br>";
        $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull = 0, engines = 0, power = 0, sensors = 0, computer = 0, beams = 0, torp_launchers = 0, torps = 0, armor = 0, armor_pts = 100, cloak = 0, shields = 0, sector = 0, ship_organics = 0, ship_ore = 0, ship_goods = 0, ship_energy = ?, ship_colonists = 0, ship_fighters = 100, dev_warpedit = 0, dev_genesis = 0, dev_beacon = 0, dev_emerwarp = 0, dev_escapepod = 'N', dev_fuelscoop = 'N', dev_minedeflector = 0, on_planet = 'N', rating = ?, cleared_defences=' ', dev_lssd = 'N' WHERE ship_id = ?;", array($bntreg->start_energy, $rating, $playerinfo['ship_id']));
        Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        Bnt\Bounty::cancel($db, $playerinfo['ship_id']);
        $ok = 0;
        Bnt\Text::gotoMain($db, $lang, $langvars);
        die ();
    }
    else
    {
        Bnt\Bounty::cancel($db, $playerinfo['ship_id']);
        Bnt\Character::kill($db, $playerinfo['ship_id'], $langvars, $bntreg, false);
        $ok = 0;
        Bnt\Text::gotoMain($db, $lang, $langvars);
        die();
    }
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
