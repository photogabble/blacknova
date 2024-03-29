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
// File: classes/Score.php

namespace Bnt;

class Score
{
    public static function updateScore($db, $ship_id, $bntreg)
    {
        $upgrade_factor = $bntreg->upgrade_factor;
        $upgrade_cost = $bntreg->upgrade_cost;
        $torpedo_price = $bntreg->torpedo_price;
        $armor_price = $bntreg->armor_price;
        $fighter_price = $bntreg->fighter_price;
        $ore_price = $bntreg->ore_price;
        $organics_price = $bntreg->organics_price;
        $goods_price = $bntreg->goods_price;
        $energy_price = $bntreg->energy_price;
        $colonist_price = $bntreg->colonist_price;
        $dev_genesis_price = $bntreg->dev_genesis_price;
        $dev_beacon_price = $bntreg->dev_beacon_price;
        $dev_emerwarp_price = $bntreg->dev_emerwarp_price;
        $dev_warpedit_price = $bntreg->dev_warpedit_price;
        $dev_minedeflector_price = $bntreg->dev_minedeflector_price;
        $dev_escapepod_price = $bntreg->dev_escapepod_price;
        $dev_fuelscoop_price = $bntreg->dev_fuelscoop_price;
        $dev_lssd_price = $bntreg->dev_lssd_price;
        $base_ore = $bntreg->base_ore;
        $base_goods = $bntreg->base_goods;
        $base_organics = $bntreg->base_organics;
        $base_credits = $bntreg->base_credits;

        // These are all SQL Queries, so treat them like them.
        $calc_hull              = "ROUND(POW($upgrade_factor, hull))";
        $calc_engines           = "ROUND(POW($upgrade_factor, engines))";
        $calc_power             = "ROUND(POW($upgrade_factor, power))";
        $calc_computer          = "ROUND(POW($upgrade_factor, computer))";
        $calc_sensors           = "ROUND(POW($upgrade_factor, sensors))";
        $calc_beams             = "ROUND(POW($upgrade_factor, beams))";
        $calc_torp_launchers    = "ROUND(POW($upgrade_factor, torp_launchers))";
        $calc_shields           = "ROUND(POW($upgrade_factor, shields))";
        $calc_armor             = "ROUND(POW($upgrade_factor, armor))";
        $calc_cloak             = "ROUND(POW($upgrade_factor, cloak))";
        $calc_levels            = "($calc_hull + $calc_engines + $calc_power + $calc_computer + $calc_sensors + $calc_beams + $calc_torp_launchers + $calc_shields + $calc_armor + $calc_cloak) * $upgrade_cost";

        $calc_torps             = "{$db->prefix}ships.torps * $torpedo_price";
        $calc_armor_pts         = "armor_pts * $armor_price";
        $calc_ship_ore          = "ship_ore * $ore_price";
        $calc_ship_organics     = "ship_organics * $organics_price";
        $calc_ship_goods        = "ship_goods * $goods_price";
        $calc_ship_energy       = "ship_energy * $energy_price";
        $calc_ship_colonists    = "ship_colonists * $colonist_price";
        $calc_ship_fighters     = "ship_fighters * $fighter_price";
        $calc_equip             = "$calc_torps + $calc_armor_pts + $calc_ship_ore + $calc_ship_organics + $calc_ship_goods + $calc_ship_energy + $calc_ship_colonists + $calc_ship_fighters";

        $calc_dev_warpedit      = "dev_warpedit * $dev_warpedit_price";
        $calc_dev_genesis       = "dev_genesis * $dev_genesis_price";
        $calc_dev_beacon        = "dev_beacon * $dev_beacon_price";
        $calc_dev_emerwarp      = "dev_emerwarp * $dev_emerwarp_price";
        $calc_dev_escapepod     = "IF(dev_escapepod='Y', $dev_escapepod_price, 0)";
        $calc_dev_fuelscoop     = "IF(dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
        $calc_dev_lssd          = "IF(dev_lssd='Y', $dev_lssd_price, 0)";
        $calc_dev_minedeflector = "dev_minedeflector * $dev_minedeflector_price";
        $calc_dev               = "$calc_dev_warpedit + $calc_dev_genesis + $calc_dev_beacon + $calc_dev_emerwarp + $calc_dev_escapepod + $calc_dev_fuelscoop + $calc_dev_minedeflector + $calc_dev_lssd";

        $calc_planet_goods      = "SUM({$db->prefix}planets.organics) * $organics_price + SUM({$db->prefix}planets.ore) * $ore_price + SUM({$db->prefix}planets.goods) * $goods_price + SUM({$db->prefix}planets.energy) * $energy_price";
        $calc_planet_colonists  = "SUM({$db->prefix}planets.colonists) * $colonist_price";
        $calc_planet_defence    = "SUM({$db->prefix}planets.fighters) * $fighter_price + IF({$db->prefix}planets.base='Y', $base_credits + SUM({$db->prefix}planets.torps) * $torpedo_price, 0)";
        $calc_planet_credits    = "SUM({$db->prefix}planets.credits)";

        $pl_score_res = $db->Execute("SELECT IF(COUNT(*)>0, $calc_planet_goods + $calc_planet_colonists + $calc_planet_defence + $calc_planet_credits, 0) AS planet_score FROM {$db->prefix}planets WHERE owner=?", array($ship_id));
        Db::logDbErrors($db, $pl_score_res, __LINE__, __FILE__);
        if ($pl_score_res instanceof ADORecordSet)
        {
            $planet_score = $pl_score_res->fields['planet_score'];
        }
        else
        {
            $planet_score = null;
        }

        $ship_score_res = $db->Execute("SELECT IF(COUNT(*)>0, $calc_levels + $calc_equip + $calc_dev + {$db->prefix}ships.credits, 0) AS ship_score FROM {$db->prefix}ships LEFT JOIN {$db->prefix}planets ON {$db->prefix}planets.owner=ship_id WHERE ship_id=? AND ship_destroyed='N'", array($ship_id));
        Db::logDbErrors($db, $ship_score_res, __LINE__, __FILE__);
        if ($ship_score_res instanceof ADORecordSet)
        {
            $ship_score = $ship_score_res->fields['ship_score'];
        }
        else
        {
            $ship_score = null;
        }

        $bank_score_res = $db->Execute("SELECT (balance - loan) AS bank_score FROM {$db->prefix}ibank_accounts WHERE ship_id = ?;", array($ship_id));
        Db::logDbErrors($db, $bank_score_res, __LINE__, __FILE__);
        if ($bank_score_res instanceof ADORecordSet)
        {
            $bank_score = $bank_score_res->fields['bank_score'];
        }
        else
        {
            $bank_score = null;
        }

        $score = $ship_score + $planet_score + $bank_score;
        if ($score < 0)
        {
            $score = 0;
        }

        $score = (int) round(sqrt($score));
        $set_score_res = $db->Execute("UPDATE {$db->prefix}ships SET score=? WHERE ship_id=?", array($score, $ship_id));
        Db::logDbErrors($db, $set_score_res, __LINE__, __FILE__);

        return $score;
    }
}
?>
