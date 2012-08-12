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
// File: includes/mysqlt-common.php

$pos = (strpos($_SERVER['PHP_SELF'], "/mysqlt-common.php"));
if ($pos !== false)
{
    include_once '../global_includes.php';
    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once '../footer.php';
    die();
}

function isLoanPending($player_id)
{
    global $db, $igb_lrate;

    $debug_query = $db->Execute("SELECT loan, loantime from {$db->prefix}ibank_accounts WHERE player_id=? and (loan != 0) and (((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(loantime)) / 60) > ?)", array($player_id, $igb_lrate));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    if (!$debug_query->EOF)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function sql_log_starvation()
{
    global $db, $starvation_death_rate, $expoprod;
    global $line, $colonist_limit, $colonist_production_rate, $organics_prate;
    global $organics_consumption;

    // LOGGING Starvation
    $debug_query = $db->Execute("SELECT owner, sector_id, ROUND(colonists * ? * ?) AS st_value FROM ".
                 "{$db->prefix}planets WHERE planet_id NOT IN (?) AND (organics + (LEAST(colonists, ?) " .
                 "* ? * ? * prod_organics / 100.0 * ?) - " .
                 "(LEAST(colonists, ?) * ? * ? * ?) < 0)", array($starvation_death_rate, $expoprod, $line, $colonist_limit, $colonist_production_rate, $organics_prate, $expoprod, $colonist_limit, $colonist_production_rte, $organics_consumption, $expoprod));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_update_starvation()
{
    global $db, $starvation_death_rate, $expoprod, $expocreds;
    global $line, $colonist_limit, $colonist_production_rate, $organics_prate;
    global $colonist_reproduction_rate, $organics_consumption;
    global $ore_prate, $goods_prate, $energy_prate, $credits_prate, $line;

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET organics=0, " .
                    "colonists = LEAST((colonists - (colonists * ? * ?) + " .
                    "(colonists * ? * ?)), ?), " .
                    "ore=ore + (LEAST(colonists, ?) * ?) * " .
                    "? * prod_ore / 100.0 * ?, " .
                    "goods=goods + (LEAST(colonists, ?) * ?) * " .
                    "? * prod_goods / 100.0 * ?, " .
                    "energy=energy + (LEAST(colonists, ?) * ?) * " .
                    "? * prod_energy / 100.0 * ?, " .
                    "credits=credits * ? + (LEAST(colonists, ?) * ?) " .
                    "* ? * (100.0 - prod_organics - prod_ore - prod_goods - prod_energy - prod_fighters - prod_torp) " .
                    "/ 100.0 * ? WHERE planet_id NOT IN (?) AND (organics + (LEAST(colonists, ?) * " .
                    "? * ? * prod_organics / 100.0 * ?) - " .
                    "(LEAST(colonists, ?) * ? * ? * ?) < 0)", array($starvation_death_rate, $expoprod, $colonist_reproduction_rate, $expoprod, $colonist_limit, $colonist_limit, $colonist_production_rate, $ore_prate, $expoprod, $colonist_limit, $colonist_production_rate, $goods_prate, $expoprod, $colonist_limit, $colonist_production_rate, $energy_prate, $expoprod, $expocreds, $colonist_limit, $colonist_production_rate, $credits_prate, $expoprod, $line, $colonist_limit, $colonist_production_rate, $organics_prate, $expoprod, $colonist_limit, $colonist_production_rate, $organics_consumption, $expoprod));

    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_production_update()
{
    global $db, $starvation_death_rate, $expoprod, $expocreds;
    global $line, $colonist_limit, $colonist_production_rate, $organics_prate;
    global $colonist_reproduction_rate, $organics_consumption;
    global $ore_prate, $goods_prate, $energy_prate, $credits_prate, $line;

    // If organics plus org production minus org consumption is greater then or equal to zero
    // Then all colonists are fed and life is happy
    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET organics=GREATEST(organics + (LEAST(colonists, ?) * " .
                    "? * ? * prod_organics / 100.0 * ?) - " .
                    "(LEAST(colonists, ?) * ? * ? * ?), 0), " .
                    "ore=ore + (LEAST(colonists, ?) * ?) * ? * prod_ore / 100.0 " .
                    "* ?, goods=goods + (LEAST(colonists, ?) * ?) * " .
                    "? * prod_goods / 100.0 * ?, energy=energy + (LEAST(colonists, ?) * " .
                    "?) * ? * prod_energy / 100.0 * ?, " .
                    "colonists= LEAST((colonists + (colonists * ? * ?)), ?), " .
                    "credits=credits * ? + (LEAST(colonists, ?) * ?) * " .
                    "? * (100.0 - prod_organics - prod_ore - prod_goods - prod_energy - prod_fighters - prod_torp) " .
                    "/ 100.0 * ? WHERE planet_id NOT IN (?) AND " .
                    "(organics + (LEAST(colonists, ?) * ? * ? * " .
                    "prod_organics / 100.0 * ?) - (LEAST(colonists, ?) * ? * " .
                    "? * ?) >= 0)", array($colonist_limit, $colonist_production_rate, $organics_prate, $expoprod, $colonist_limit, $colonist_production_rate, $organics_consumption, $expoprod, $colonist_limit, $colonist_production_rate, $ore_prate, $expoprod, $colonist_limit, $colonist_production_rate, $goods_prate, $expoprod, $colonist_limit, $colonist_production_rate, $energy_prate, $expoprod, $colonist_reproduction_rate, $expoprod, $colonist_limit, $expocreds, $colonist_limit, $colonist_production_rate, $credits_prate, $expoprod, $line, $colonist_limit, $colonist_production_rate, $organics_prate, $expoprod, $colonist_limit, $colonist_production_rate, $organics_consumption, $expoprod));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_defense_update()
{
    global $db, $expoprod;
    global $colonist_limit, $colonist_production_rate;
    global $fighter_prate, $torpedo_prate, $line;

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET fighters=fighters + " .
                    "(LEAST(colonists, ?) * ?) * ? * prod_fighters / 100.0 * " .
                    "?, torps=torps + (LEAST(colonists, ?) * ?) * ? * " .
                    "prod_torp / 100.0 * ? WHERE planet_id NOT IN (?) AND owner!=0", array($colonist_limit, $colonist_production_rate, $fighter_prate, $expoprod,  $colonist_limit, $colonist_production_rate, $torpedo_prate, $expoprod, $line));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_sched_degrade_defenses($defense_id)
{
    global $db, $defense_degrade_rate;

    $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense set quantity = quantity - " .
                                "GREATEST(ROUND(quantity * ?),1) where " .
                                "defense_id=? and quantity > 0", array($defense_degrade_rate, $defense_id));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_sched_degrade_energy($planet_id)
{
    global $db, $defense_degrade_rate, $energy_required, $energy_available;

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets set energy = energy - " .
                                "GREATEST(ROUND(? * (energy / ?)),1) where planet_id=?", array($energy_required, $energy_available, $planet_id));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_ranking($db, $query, $by, $max_rank)
{
    global $raw_prefix;
    $debug_query = $db->SelectLimit("SELECT {$raw_prefix}users.email, {$db->prefix}players.score, " .
                        "{$db->prefix}players.player_id, " .
                        "{$db->prefix}players.character_name, " .
                        "{$db->prefix}players.turns_used, UNIX_TIMESTAMP({$db->prefix}players.last_login) as last_login, " .
                        "{$db->prefix}players.rating, " .
                        "{$db->prefix}teams.team_name, " .
                        "IF({$db->prefix}players.turns_used<150,0,ROUND({$db->prefix}players.score/{$db->prefix}players.turns_used)) " .
                        "AS efficiency FROM {$db->prefix}players LEFT JOIN {$db->prefix}teams ON {$db->prefix}players.team " .
                        "= {$db->prefix}teams.team_id LEFT JOIN {$db->prefix}ships ON " .
                        "{$db->prefix}players.player_id={$db->prefix}ships.player_id LEFT JOIN {$raw_prefix}users ON {$raw_prefix}users.account_id ={$db->prefix}players.account_id WHERE destroyed!='Y' " .
                        "and acl != '0'".$query." ORDER BY ?",$max_rank,-1,array($by));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_spy_select()
{
    global $db, $by11, $playerinfo;
    $debug_query = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}players.character_name, {$db->prefix}ships.name AS " .
                                "ship_name, {$db->prefix}ships.sector_id, {$db->prefix}ship_types.name AS c_name, " .
                                "UNIX_TIMESTAMP({$db->prefix}players.last_login) AS online FROM {$db->prefix}spies " .
                                "INNER JOIN {$db->prefix}ships ON {$db->prefix}spies.ship_id={$db->prefix}ships.ship_id INNER JOIN " .
                                "{$db->prefix}ship_types ON {$db->prefix}ships.class={$db->prefix}ship_types.type_id INNER JOIN " .
                                "{$db->prefix}players ON {$db->prefix}players.player_id={$db->prefix}ships.player_id WHERE " .
                                "{$db->prefix}spies.active='Y' AND {$db->prefix}spies.owner_id=? ORDER BY $by11", array($playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_planet_zero()
{
    global $db;
    // Make sure planets never go below 0.
    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET credits=GREATEST(credits,0), organics=GREATEST(organics,0), ore=GREATEST(ore,0), ".
                    "goods=GREATEST(goods,0), energy=GREATEST(energy,0), colonists=GREATEST(colonists,0), torps=GREATEST(torps,0), " .
                    "armor_pts=GREATEST(armor_pts,0), fighters=GREATEST(fighters,0)");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}

function sql_port_grow()
{
    // I did try optimizing this further by splitting it into two seperate sql calls - one to increase and one to cap to limit, but
    // two calls ended up being far slower than this one complex call.
    global $db, $ore_rate, $multiplier, $ore_limit, $organics_rate, $organics_limit, $goods_rate, $goods_limit, $energy_rate;
    global $energy_limit;
    $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=LEAST(port_ore+(?*?), ?),".
                                "port_organics=LEAST(port_organics+(?*?), ?),".
                                "port_goods=LEAST(port_goods+(?*?), ?),".
                                "port_energy=LEAST(port_energy+(?*?), ?) ".
                                "WHERE port_type != 'upgrades' AND port_type != 'devices' AND port_type != 'shipyard' AND port_type != 'none'", array($ore_rate, $multiplier, $ore_limit, $organics_rate, $multiplier, $organics_limit, $goods_rate, $multiplier, $goods_limit, $energy_rate, $multiplier, $energy_limit));
    return db_op_result($db,$debug_query,__LINE__,__FILE__);
}
?>
