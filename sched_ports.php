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
// File: sched_ports.php

if (strpos($_SERVER['PHP_SELF'], 'sched_ports.php')) // Prevent direct access to this file
{
    die('Blacknova Traders error: You cannot access this file directly.');
}

// Update Ore in Ports
echo "<strong>PORTS</strong><br><br>";
echo "Adding ore to all commodities ports...";

$resa = $db->Execute("UPDATE {$db->prefix}universe SET port_ore = port_ore + ($bntreg->ore_rate * $multiplier * $bntreg->port_regenrate ) WHERE port_type='ore' AND port_ore < $bntreg->ore_limit");
$debug = Bnt\Db::logDbErrors($db, $resa, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Adding ore to all ore ports...";
$resb = $db->Execute("UPDATE {$db->prefix}universe SET port_ore = port_ore + ($bntreg->ore_rate * $multiplier * $bntreg->port_regenrate) WHERE port_type!='special' AND port_type!='none' AND port_ore < $bntreg->ore_limit");
$debug = Bnt\Db::logDbErrors($db, $resb, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Ensuring minimum ore levels are 0...";
$resc = $db->Execute("UPDATE {$db->prefix}universe SET port_ore = 0 WHERE port_ore < 0");
$debug = Bnt\Db::logDbErrors($db, $resc, __LINE__, __FILE__);
is_query_ok($db, $debug);
echo "<br>";

// Update Organics in Ports
echo "Adding organics to all commodities ports...";
$resd = $db->Execute("UPDATE {$db->prefix}universe SET port_organics = port_organics + (? * ? * ?) WHERE port_type='organics' AND port_organics < ?", array($bntreg->organics_rate, $multiplier, $bntreg->port_regenrate, $bntreg->organics_limit));
$debug = Bnt\Db::logDbErrors($db, $resd, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Adding organics to all organics ports...";
$rese = $db->Execute("UPDATE {$db->prefix}universe SET port_organics = port_organics + (? * ? * ?) WHERE port_type!='special' AND port_type!='none' AND port_organics < ?", array($bntreg->organics_rate, $multiplier, $bntreg->port_regenrate, $bntreg->organics_limit));
$debug = Bnt\Db::logDbErrors($db, $rese, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Ensuring minimum organics levels are 0...";
$resf = $db->Execute("UPDATE {$db->prefix}universe SET port_organics = 0 WHERE port_organics < 0");
$debug = Bnt\Db::logDbErrors($db, $resf, __LINE__, __FILE__);
is_query_ok($db, $debug);
echo "<br>";

// Update Goods in Ports
echo "Adding goods to all commodities ports...";
$resg = $db->Execute("UPDATE {$db->prefix}universe SET port_goods = port_goods + (? * ? * ?) WHERE port_type='goods' AND port_goods < ?", array($bntreg->goods_rate, $multiplier, $bntreg->port_regenrate, $bntreg->goods_limit));
$debug = Bnt\Db::logDbErrors($db, $resg, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Adding goods to all goods ports...";
$resh = $db->Execute("UPDATE {$db->prefix}universe SET port_goods = port_goods + (? * ? * ?) WHERE port_type!='special' AND port_type!='none' AND port_goods < ?", array($bntreg->goods_rate, $multiplier, $bntreg->port_regenrate, $bntreg->goods_limit));
$debug = Bnt\Db::logDbErrors($db, $resh, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Ensuring minimum goods levels are 0...";
$resi = $db->Execute("UPDATE {$db->prefix}universe SET port_goods = 0 WHERE port_goods < 0");
$debug = Bnt\Db::logDbErrors($db, $resi, __LINE__, __FILE__);
is_query_ok($db, $debug);
echo "<br>";

// Update Energy in Ports
echo "Adding energy to all commodities ports...";
$resj = $db->Execute("UPDATE {$db->prefix}universe SET port_energy = port_energy + (? * ? * ?) WHERE port_type='energy' AND port_energy < ?", array($bntreg->energy_rate, $multiplier, $bntreg->port_regenrate, $bntreg->energy_limit));
$debug = Bnt\Db::logDbErrors($db, $resj, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Adding energy to all energy ports...";
$resk = $db->Execute("UPDATE {$db->prefix}universe SET port_energy = port_energy + (? * ? * ?) WHERE port_type!='special' AND port_type!='none' AND port_energy < ?", array($bntreg->energy_rate, $multiplier, $bntreg->port_regenrate, $bntreg->energy_limit));
$debug = Bnt\Db::logDbErrors($db, $resk, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Ensuring minimum energy levels are 0...";
$resl = $db->Execute("UPDATE {$db->prefix}universe SET port_energy = 0 WHERE port_energy < 0");
$debug = Bnt\Db::logDbErrors($db, $resl, __LINE__, __FILE__);
is_query_ok($db, $debug);
echo "<br>";

// Now check to see if any ports are over max, if so rectify.
echo "Checking Energy Port Cap...";
$resm = $db->Execute("UPDATE {$db->prefix}universe SET port_energy = ? WHERE port_energy > ?", array($bntreg->energy_limit, $bntreg->energy_limit));
$debug = Bnt\Db::logDbErrors($db, $resm, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Checking Goods Port Cap...";
$resn = $db->Execute("UPDATE {$db->prefix}universe SET port_goods = ? WHERE port_goods > ?", array($bntreg->goods_limit, $bntreg->goods_limit));
$debug = Bnt\Db::logDbErrors($db, $resn, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Checking Organics Port Cap...";
$reso = $db->Execute("UPDATE {$db->prefix}universe SET port_organics = ? WHERE port_organics > ?", array($bntreg->organics_limit, $bntreg->organics_limit));
$debug = Bnt\Db::logDbErrors($db, $reso, __LINE__, __FILE__);
is_query_ok($db, $debug);

echo "Checking Ore Port Cap...";
$resp = $db->Execute("UPDATE {$db->prefix}universe SET port_ore = ? WHERE port_ore > ?", array($bntreg->ore_limit, $bntreg->ore_limit));
$debug = Bnt\Db::logDbErrors($db, $resp, __LINE__, __FILE__);
is_query_ok($db, $debug);
$multiplier = 0;

echo "<br>";
?>
