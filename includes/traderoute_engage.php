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
// File: traderoute_engage.php

include_once ("./global_includes.php");
dynamic_loader ($db, "direct_test.php");
direct_test(__FILE__, $_SERVER['PHP_SELF']);

function traderoute_engage($j)
{
    // Dynamic functions
    dynamic_loader ($db, "traderoute_die.php");
    dynamic_loader ($db, "get_info.php");
    dynamic_loader ($db, "number.php");

    global $i;
    global $playerinfo, $shipinfo;
    global $traderoutes;
    global $fighter_price;
    global $torpedo_price;
    global $colonist_price;
    global $colonist_limit;
    global $inventory_factor;
    global $ore_price;
    global $ore_delta;
    global $ore_limit;
    global $organics_price;
    global $organics_delta;
    global $organics_limit;
    global $goods_price;
    global $goods_delta;
    global $goods_limit;
    global $energy_price;
    global $energy_delta;
    global $energy_limit;
    global $l_tdr_turnsused, $l_tdr_turnsleft, $l_tdr_credits, $l_tdr_profit;
    global $l_tdr_cost, $l_tdr_totalprofit, $l_tdr_totalcost;
    global $l_tdr_planetisovercrowded, $l_tdr_engageagain, $l_tdr_engageagain2;
    global $l_tdr_engageagain5, $l_tdr_engageagain10, $l_tdr_engageagain50;
    global $l_tdr_onlyonewaytdr, $l_tdr_engagenonexist, $l_tdr_notowntdr;
    global $l_tdr_invalidspoint, $l_tdr_inittdr, $l_tdr_invalidsrc, $l_tdr_inittdrsector;
    global $l_tdr_organics, $l_tdr_energy, $l_tdr_loaded;
    global $l_tdr_nothingtoload, $l_tdr_scooped, $l_tdr_dumped, $l_tdr_portisempty;
    global $l_tdr_portisfull, $l_tdr_ore, $l_tdr_sold;
    global $l_tdr_goods, $l_tdr_notyourplanet, $l_tdr_invalidssector;
    global $l_tdr_invaliddport, $l_tdr_invaliddplanet;
    global $l_tdr_invaliddsector, $l_tdr_nowlink1, $l_tdr_nowlink2;
    global $l_tdr_moreturnsneeded, $l_tdr_tdrhostdef;
    global $l_tdr_globalsetbuynothing, $l_tdr_nosrcporttrade;
    global $l_tdr_tradesrcportoutsider, $l_tdr_tdrres, $l_tdr_torps;
    global $l_tdr_nodestporttrade, $l_tdr_tradedestportoutsider, $l_tdr_portin;
    global $l_tdr_planet, $l_tdr_bought, $l_tdr_colonists;
    global $l_tdr_fighters, $l_tdr_nothingtotrade, $l_submit, $l_tdr_nothingtodump;
    global $l_tdr_timestorep, $l_max_level_move;
    global $spy_success_factor, $planet_detect_success1;
    global $db;

    $dist['scooped'] = 0;
    $dist['scooped1'] = 0;
    $dist['scooped2'] = 0;
    $colonists_buy = 0;
    $fighters_buy = 0;
    $torps_buy = 0;

    $setcol=0;
    //10 pages of sanity checks! yeah!

    foreach($traderoutes as $testroute)
    {
        if ($testroute['traderoute_id'] == $_GET['engage'])
        {
            $traderoute = $testroute;
        }
    }

    if (!isset($traderoute))
    {
        traderoute_die($l_tdr_engagenonexist);
    }

    if ($traderoute['owner'] != $playerinfo['player_id'])
    {
        traderoute_die($l_tdr_notowntdr);
    }

    // Source Check
    if ($traderoute['source_type'] == 'P')
    {
        // Retrieve port info here, we'll need it later anyway
        $result = $db->Execute("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($traderoute['source_id']));
        if (!$result || $result->EOF)
        {
            traderoute_die($l_tdr_invalidspoint);
        }

        $source = $result->fields;

        if ($traderoute['source_id'] != $shipinfo['sector_id'])
        {
            $l_tdr_inittdr = str_replace("[tdr_source_id]", $traderoute['source_id'], $l_tdr_inittdr);
            traderoute_die($l_tdr_inittdr);
        }
    }
    elseif ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')  // get data from planet table
    {
        $result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($traderoute['source_id']));
        if (!$result || $result->EOF)
        {
            traderoute_die($l_tdr_invalidsrc);
        }

        $source = $result->fields;

        if ($source['sector_id'] != $shipinfo['sector_id'])
        {
            $l_tdr_inittdrsector = str_replace("[tdr_source_sector_id]", $source[sector_id], $l_tdr_inittdrsector);
            traderoute_die($l_tdr_inittdrsector);
        }

        if ($traderoute['source_type'] == 'L')
        {
            if ($source['owner'] != $playerinfo['player_id'])
            {
                $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $source[name], $l_tdr_notyourplanet);
                $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $source[sector_id], $l_tdr_notyourplanet);
                traderoute_die($l_tdr_notyourplanet);
            }
        }
        elseif ($traderoute[source_type] == 'C')   // check to make sure player and planet are in the same team.
        {
            if ($source[team] != $playerinfo[team])
            {
                $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $source[name], $l_tdr_notyourplanet);
                $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $source[sector_id], $l_tdr_notyourplanet);
                $not_team_planet = "$source[name] in $source[sector_id] not a Copporate Planet";
                traderoute_die($not_team_planet);
            }
        }

        // Store starting port info, we'll need it later
        $result = $db->Execute("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($source['sector_id']));
        if (!$result || $result->EOF)
        {
            traderoute_die($l_tdr_invalidssector);
        }
        
        $sourceport = $result->fields;
    }

    // Destination Check for combat levels
    if (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
    {
        $debug_query = $db->Execute("SELECT sector_id FROM {$db->prefix}planets WHERE planet_id=?", array($traderoute['dest_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $planet_sector = $debug_query->fields['sector_id'];

        $debug_query = $db->Execute("SELECT zone_id FROM {$db->prefix}universe WHERE sector_id=?", array($planet_sector));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $dest_zone = $debug_query->fields['zone_id'];
    }
    else
    {
        $debug_query = $db->Execute("SELECT zone_id FROM {$db->prefix}universe WHERE sector_id=?", array($traderoute['dest_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $dest_zone = $debug_query->fields['zone_id'];
    }

    $debug_query = $db->Execute("SELECT max_level FROM {$db->prefix}zones WHERE zone_id=?", array($dest_zone));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $dest_max_level = $debug_query->fields['max_level'];

    $combat_levels = round(($shipinfo['computer'] + $shipinfo['torp_launchers'] + $shipinfo['beams']) / 3);
    if ($combat_levels > $dest_max_level && $dest_max_level > 0)
    {
        traderoute_die($l_max_level_move);
    }

    // Destination Check
    if ($traderoute['dest_type'] == 'P')
    {
        $result = $db->Execute("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($traderoute['dest_id']));
        if (!$result || $result->EOF)
        {
            traderoute_die($l_tdr_invaliddport);
        }

        $dest = $result->fields;
    }
    elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))  // get data from planet table
    {
        $result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=?", array($traderoute['dest_id']));
        if (!$result || $result->EOF)
        {
            traderoute_die($l_tdr_invaliddplanet);
        }

        $dest = $result->fields;
    
        if ($traderoute['dest_type'] == 'L')
        {
            if ($dest['owner'] != $playerinfo['player_id'])
            {
                $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $dest['name'], $l_tdr_notyourplanet);
                $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $dest['sector_id'], $l_tdr_notyourplanet);
                traderoute_die($l_tdr_notyourplanet);
            }
        }
        elseif ($traderoute['dest_type'] == 'C')   // check to make sure player and planet are in the same team.
        {
            if ($dest['team'] != $playerinfo['team'])
            {
                $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $dest['name'], $l_tdr_notyourplanet);
                $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $dest['sector_id'], $l_tdr_notyourplanet);
                $not_team_planet = "$dest[name] in $dest[sector_id] not a Copporate Planet";
                traderoute_die($not_team_planet);
            }
        }

        $result = $db->Execute("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($dest['sector_id']));
        if (!$result || $result->EOF)
        {
            traderoute_die($l_tdr_invaliddsector);
        }

        $destport = $result->fields;
    }

    if (!isset($sourceport))
    {
        $sourceport=$source;
    }

    if (!isset($destport))
    {
        $destport=$dest;
    }

    // Warp or RealSpace and generate distance
    if ($traderoute['move_type'] == 'W')
    {
        $query = $db->Execute("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array($source['sector_id'], $dest['sector_id']));
        if ($query->EOF)
        {
            $l_tdr_nowlink1 = str_replace("[tdr_src_sector_id]", $source['sector_id'], $l_tdr_nowlink1);
            $l_tdr_nowlink1 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink1);
            traderoute_die($l_tdr_nowlink1);
        }

        if ($traderoute['circuit'] == '2')
        {
            $query = $db->Execute("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array($dest['sector_id'], $source['sector_id']));
            if ($query->EOF)
            {
                $l_tdr_nowlink2 = str_replace("[tdr_src_sector_id]", $source['sector_id'], $l_tdr_nowlink2);
                $l_tdr_nowlink2 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink2);
                traderoute_die($l_tdr_nowlink2);
            }

            $dist['triptime'] = 4;
        }
        else
        {
            $dist['triptime'] = 2;
        }

        $dist['scooped'] = 0;
    }
    else
    {
        $dist = traderoute_distance('P', 'P', $sourceport['sector_id'], $destport['sector_id'], $traderoute['circuit'], $playerinfo['trade_energy']);
    }

    // Check if player has enough turns
    if ($playerinfo['turns'] < $dist['triptime'])
    {
        $l_tdr_moreturnsneeded = str_replace("[tdr_dist_triptime]", $dist['triptime'], $l_tdr_moreturnsneeded);
        $l_tdr_moreturnsneeded = str_replace("[tdr_playerinfo_turns]", $playerinfo['turns'], $l_tdr_moreturnsneeded);
        traderoute_die($l_tdr_moreturnsneeded);
    }

    // Sector Defense Check
    $hostile = 0;

    $result99 = $db->Execute("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? AND player_id!=?", array($source['sector_id'], $playerinfo['player_id']));
    if (!$result99->EOF)
    {
        $fighters_owner = $result99->fields;
        $nsresult = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=$fighters_owner[player_id]");
        $nsfighters = $nsresult->fields;
        if ($nsfighters[team] != $playerinfo[team] || $playerinfo[team]==0)
        {
            $hostile = 1;
        }
    }

    $result98 = $db->Execute("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? AND player_id!=?", array($dest['sector_id'], $playerinfo['player_id']));
    if (!$result98->EOF)
    {
        $fighters_owner = $result98->fields;
        $nsresult = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=?", array($fighters_owner['player_id']));
        $nsfighters = $nsresult->fields;
        if ($nsfighters[team] != $playerinfo[team] || $playerinfo[team]==0)
        {
            $hostile = 1;
        }
    }

    if ($hostile > 0 && $shipinfo['hull'] > $mine_hullsize)
    {
        traderoute_die($l_tdr_tdrhostdef);
    }

    // Upgrades Port Nothing to do
    if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'upgrades' && $playerinfo['trade_colonists'] == 'N' && $playerinfo['trade_fighters'] == 'N' && $playerinfo['trade_torps'] == 'N')
    {
        traderoute_die($l_tdr_globalsetbuynothing);
    }

    // Check if zone allows trading  SRC
    if ($traderoute['source_type'] == 'P')
    {
        // foo - messy query here
        $res = $db->Execute("SELECT * FROM {$db->prefix}zones,{$db->prefix}universe WHERE {$db->prefix}universe.sector_id=? AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id", array($traderoute['source_id']));
        $query97 = $res->fields;
        if ($query97['allow_trade'] == 'N')
        {
            traderoute_die($l_tdr_nosrcporttrade);
        }
        elseif ($query97['allow_trade'] == 'L')
        {
            if ($query97[team_zone] == 'N')
            {
                $res = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($query97['owner']));
                $ownerinfo = $res->fields;

                if ($playerinfo[player_id] != $query97[owner] && $playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
                {
                    traderoute_die($l_tdr_tradesrcportoutsider);
                }
            }
            else
            {
                if ($playerinfo[team] != $query97[owner])
                {
                    traderoute_die($l_tdr_tradesrcportoutsider);
                }
            }
        }
    }

    // Check if zone allows trading  DEST
    if ($traderoute['dest_type'] == 'P')
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}zones, {$db->prefix}universe WHERE {$db->prefix}universe.sector_id=? AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id", array($traderoute['dest_id']));
        $query97 = $res->fields;
        if ($query97['allow_trade'] == 'N')
        {
            traderoute_die($l_tdr_nodestporttrade);
        }
        elseif ($query97['allow_trade'] == 'L')
        {
            if ($query97[team_zone] == 'N')
            {
                $res = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($query97['owner']));
                $ownerinfo = $res->fields;

                if ($playerinfo[player_id] != $query97[owner] && $playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
                {
                    traderoute_die($l_tdr_tradedestportoutsider);
                }
            }
            else
            {
                if ($playerinfo[team] != $query97[owner])
                {
                    traderoute_die($l_tdr_tradedestportoutsider);
                }
            }
        }
    }

    // Check if player has a loan pending
    if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'upgrades' && isLoanPending($playerinfo['player_id']))
    {
        global $l_port_loannotrade, $l_igb_term;
        traderoute_die("$l_port_loannotrade<p><a href=igb.php>$l_igb_term</a><p>");
    }

    // Check if player has a fedbounty

    if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'upgrades')
    {
        global $l_port_bounty, $l_port_bounty2, $l_by_placebounty;
        $res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM {$db->prefix}bounty WHERE placed_by=0 AND bounty_on=?", array($playerinfo['player_id']));
        if ($res2)
        {
            $bty = $res2->fields;
            if ($bty['total_bounty'] > 0)
            {
                $l_port_bounty2 = str_replace("[amount]",number_format($bty['total_bounty'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_port_bounty2);
                traderoute_die("$l_port_bounty $l_port_bounty2 <br> <a href=\"bounty.php\">$l_by_placebounty</a><br><br>");
            }
        }
    }

    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //---------  We're done with checks! All that's left is to make it happen --------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    echo "
    <table border=1 cellspacing=1 cellpadding=2 width=\"65%\" align=center>
    <tr bgcolor=\"#400040\"><td align=\"center\" colspan=7><strong>$l_tdr_tdrres</strong></td></tr>
    <tr align=center bgcolor=\"#400040\">
    <td width=\"50%\"><strong>
    ";


    // ------------ Determine if Source is Planet or Port
    if ($traderoute['source_type'] == 'P')
    {
        echo "$l_tdr_portin $source[sector_id]";
    }
    elseif (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
    {
        echo "$l_tdr_planet $source[name] in $sourceport[sector_id]";
    }

    echo '
    </strong></td>
    <td width="50%"><strong>
    ';

    // ------------ Determine if Destination is Planet or Port
    if ($traderoute['dest_type'] == 'P')
    {
        echo "$l_tdr_portin $dest[sector_id]";
    }
    elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
    {
        echo "$l_tdr_planet $dest[name] in $destport[sector_id]";
    }

    echo '
    </strong></td>
    </tr><tr bgcolor="#300030">
    <td align=center>
    ';

    $sourcecost=0;

    //-------- Source is Port ------------
    if ($traderoute['source_type'] == 'P')
    {
        //-------- Upgrade Port Section (begin) ------
        if ($source['port_type'] == 'upgrades')
        {
            $total_credits = $playerinfo['credits'];

            if ($playerinfo['trade_colonists'] == 'Y')
            {
                $free_holds = num_holds($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
                $colonists_buy = $free_holds;

                if ($playerinfo['credits'] < $colonist_price * $colonists_buy)
                {
                    $colonists_buy = $playerinfo['credits'] / $colonist_price;
                }

                if ($colonists_buy != 0)
                {
                    echo "$l_tdr_bought " . number_format($colonists_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_colonists<br>";
                }

                $sourcecost-=$colonists_buy * $colonist_price;
                $total_credits-=$colonists_buy * $colonist_price;
            }
            else
            {
                $colonists_buy = 0;
            }

            if ($playerinfo['trade_fighters'] == 'Y')
            {
                $free_fighters = num_fighters($shipinfo['computer']) - $shipinfo['fighters'];
                $fighters_buy = $free_fighters;

                if ($total_credits < $fighters_buy * $fighter_price)
                {
                    $fighters_buy = $total_credits / $fighter_price;
                }

                if ($fighters_buy != 0)
                {
                    echo "$l_tdr_bought " . number_format($fighters_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_fighters<br>";
                }

                $sourcecost-=$fighters_buy * $fighter_price;
                $total_credits-=$fighters_buy * $fighter_price;
            }
            else
            {
                $fighters_buy = 0;
            }

            if ($playerinfo['trade_torps'] == 'Y')
            {
                $free_torps = num_fighters($shipinfo['torp_launchers']) - $shipinfo['torps'];
                $torps_buy = $free_torps;

                if ($total_credits < $torps_buy * $torpedo_price)
                {
                    $torps_buy = $total_credits / $torpedo_price;
                }

                if ($torps_buy != 0)
                {
                    echo "$l_tdr_bought " . number_format($torps_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_torps<br>";
                }

                $sourcecost-=$torps_buy * $torpedo_price;
            }
            else
            {
                $torps_buy = 0;
            }

            if ($torps_buy == 0 && $colonists_buy == 0 && $fighters_buy == 0)
            {
                echo "$l_tdr_nothingtotrade<br>";
            }

            if ($traderoute['circuit'] == '1')
            {
                // Check that the ship doesnt get more energy than it is allowed to have
                if ($shipinfo['energy'] + $dist['scooped1'] > (5 * num_level($shipinfo['power'])) // Energy is level * 5
                {
                    $dist['scooped1'] = (num_level($shipinfo['power']) *5) - $shipinfo['energy'];
                }

                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET colonists=colonists+?, " . 
                                            "fighters=fighters+?, torps=torps+?, " .
                                            "energy=energy+? WHERE ship_id=?", array($colonists_buy, $fighters_buy, $torps_buy, $dist['scooped1'], $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }
        //-------- Upgrade Port Section (end) ------
        //-------- Normal Port Section (begin) ------
        else
        {
            // Sells commodities
            // initialize variables to 0, prevents sql error.
            $ore_buy = 0;
            $goods_buy = 0;
            $organics_buy = 0;
            $energy_buy = 0;

            $portfull = 0;
            if ($source['port_type'] != 'ore')
            {
                $ore_price1 = $ore_price + $ore_delta * $source['port_ore'] / $ore_limit * $inventory_factor;
                if ($source['port_ore'] - $shipinfo['ore'] < 0)
                {
                    $ore_buy = $source['port_ore'];
                    $portfull = 1;
                }
                else
                {
                    $ore_buy = $shipinfo['ore'];
                }

                $sourcecost += $ore_buy * $ore_price1;
                if ($ore_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore<br>";
                    }
                }

                $shipinfo['ore'] -= $ore_buy;
            }

            $portfull = 0;
            if ($source['port_type'] != 'goods')
            {
                $goods_price1 = $goods_price + $goods_delta * $source['port_goods'] / $goods_limit * $inventory_factor;
                if ($source['port_goods'] - $shipinfo['goods'] < 0)
                {
                    $goods_buy = $source['port_goods'];
                    $portfull = 1;
                }
                else
                {
                    $goods_buy = $shipinfo['goods'];
                }

                $sourcecost += $goods_buy * $goods_price1;
                if ($goods_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods<br>";
                    }
                }
                $shipinfo['goods'] -= $goods_buy;
            }

            $portfull = 0;
            if ($source['port_type'] != 'organics')
            {
                $organics_price1 = $organics_price + $organics_delta * $source['port_organics'] / $organics_limit * $inventory_factor;
                if ($source['port_organics'] - $shipinfo['organics'] < 0)
                {
                    $organics_buy = $source['port_organics'];
                    $portfull = 1;
                }
                else
                {
                    $organics_buy = $shipinfo['organics'];
                }

                $sourcecost += $organics_buy * $organics_price1;
                if ($organics_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics<br>";
                    }
                }
                $shipinfo['organics'] -= $organics_buy;
            }

            $portfull = 0;
            if ($source['port_type'] != 'energy' && $playerinfo['trade_energy'] == 'Y')
            {
                $energy_price1 = $energy_price + $energy_delta * $source['port_energy'] / $energy_limit * $inventory_factor;
                if ($source['port_energy'] - $shipinfo['energy'] < 0)
                {
                    $energy_buy = $source['port_energy'];
                    $portfull = 1;
                }
                else
                {
                    $energy_buy = $shipinfo['energy'];
                }

                $sourcecost += $energy_buy * $energy_price1;
                if ($energy_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy<br>";
                    }
                }
                $shipinfo['energy'] -= $energy_buy;
            }
            else
            {
                $energy_buy = 0;
            }

            $free_holds = num_holds($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];

            // Time to buy
            if ($source['port_type'] == 'ore')
            {
                $ore_price1 = $ore_price - $ore_delta * $source['port_ore'] / $ore_limit * $inventory_factor;
                $ore_buy = $free_holds;
                if ($playerinfo['credits'] + $sourcecost < $ore_buy * $ore_price1)
                {
                    $ore_buy = ($playerinfo['credits'] + $sourcecost) / $ore_price1;
                }

                if ($source['port_ore'] < $ore_buy)
                {
                    $ore_buy = $source['port_ore'];
                    if ($source[port_ore] == 0)
                    {
                        echo "$l_tdr_bought " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore ($l_tdr_portisempty)<br>";
                    }
                }

                if ($ore_buy != 0)
                {
                    echo "$l_tdr_bought " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore<br>";
                }

                $shipinfo['ore'] += $ore_buy;
                $sourcecost -= $ore_buy * $ore_price1;
                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($source['port_type'] == 'goods')
            {
                $goods_price1 = $goods_price - $goods_delta * $source['port_goods'] / $goods_limit * $inventory_factor;
                $goods_buy = $free_holds;
                if ($playerinfo['credits'] + $sourcecost < $goods_buy * $goods_price1)
                {
                    $goods_buy = ($playerinfo['credits'] + $sourcecost) / $goods_price1;
                }

                if ($source['port_goods'] < $goods_buy)
                {
                    $goods_buy = $source['port_goods'];
                    if ($source['port_goods'] == 0)
                    {
                        echo "$l_tdr_bought " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods ($l_tdr_portisempty)<br>";
                    }
                }

                if ($goods_buy != 0)
                {
                    echo "$l_tdr_bought " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods<br>";
                }

                $shipinfo['goods'] += $goods_buy;
                $sourcecost -= $goods_buy * $goods_price1;
                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($source['port_type'] == 'organics')
            {
                $organics_price1 = $organics_price - $organics_delta * $source['port_organics'] / $organics_limit * $inventory_factor;
                $organics_buy = $free_holds;
                if ($playerinfo['credits'] + $sourcecost < $organics_buy * $organics_price1)
                {
                    $organics_buy = ($playerinfo['credits'] + $sourcecost) / $organics_price1;
                }

                if ($source['port_organics'] < $organics_buy)
                {
                    $organics_buy = $source['port_organics'];
                    if ($source['port_organics'] == 0)
                    {
                        echo "$l_tdr_bought " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics ($l_tdr_portisempty)<br>";
                    }
                }

                if ($organics_buy != 0)
                {
                    echo "$l_tdr_bought " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics<br>";
                }

                $shipinfo['organics'] += $organics_buy;
                $sourcecost -= $organics_buy * $organics_price1;
                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($source['port_type'] == 'energy')
            {
                $energy_price1 = $energy_price - $energy_delta * $source['port_energy'] / $energy_limit * $inventory_factor;
                $energy_buy = (5 * num_level($shipinfo['power'])) - $shipinfo['energy'] - $dist['scooped1'];
                if ($playerinfo['credits'] + $sourcecost < $energy_buy * $energy_price1)
                {
                    $energy_buy = ($playerinfo['credits'] + $sourcecost) / $energy_price1;
                }

                if ($source['port_energy'] < $energy_buy)
                {
                    $energy_buy = $source['port_energy'];
                    if ($source['port_energy'] == 0)
                    {
                        echo "$l_tdr_bought " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy ($l_tdr_portisempty)<br>";
                    }
                }

                if ($energy_buy != 0)
                {
                    echo "$l_tdr_bought " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy<br>";
                }

                $shipinfo['energy'] += $energy_buy;
                $sourcecost -= $energy_buy * $energy_price1;
                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-$ore_buy, port_energy=port_energy-$energy_buy, port_goods=port_goods-$goods_buy, port_organics=port_organics-$organics_buy WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($dist['scooped1'] > 0)
            {
                $shipinfo['energy']+= $dist['scooped1'];
                if ($shipinfo['energy'] > (5 * num_level($shipinfo['power'])))
                {
                    $shipinfo['energy'] = (5 * num_level($shipinfo['power']));
                }
            }

            if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
            {
                echo "$l_tdr_nothingtotrade<br>";
            }

            if ($traderoute['circuit'] == '1')
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET ore=?, goods=?, organics=?, energy=? WHERE ship_id=?", array($shipinfo['ore'], $shipinfo['goods'], $shipinfo['organics'], $shipinfo['energy'], $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }
    }
    //------------- Source is port (end) ---------
    //------------- Source is planet (begin) -----
    elseif (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
    {
        $free_holds = num_holds($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];

        if ($traderoute['dest_type'] == 'P')
        {
            // Pick stuff up to sell at port
            if (($playerinfo['player_id'] == $source['owner']) || ($playerinfo['team'] == $source['team']))
            {
                if ($source['goods'] > 0 && $free_holds > 0 && $dest['port_type'] != 'goods')
                {
                    if ($source['goods'] > $free_holds)
                    {
                        $goods_buy = $free_holds;
                    }
                    else
                    {
                        $goods_buy = $source['goods'];
                    }

                    $free_holds -= $goods_buy;
                    $shipinfo['goods'] += $goods_buy;
                    echo "$l_tdr_loaded " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods<br>";
                }
                else
                {
                    $goods_buy = 0;
                }

                if ($source['ore'] > 0 && $free_holds > 0 && $dest['port_type'] != 'ore')
                {
                    if ($source['ore'] > $free_holds)
                    {
                        $ore_buy = $free_holds;
                    }
                    else
                    {
                        $ore_buy = $source['ore'];
                    }

                    $free_holds -= $ore_buy;
                    $shipinfo['ore'] += $ore_buy;
                    echo "$l_tdr_loaded " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore<br>";
                }
                else
                {
                    $ore_buy = 0;
                }

                if ($source['organics'] > 0 && $free_holds > 0 && $dest['port_type'] != 'organics')
                {
                    if ($source['organics'] > $free_holds)
                    {
                        $organics_buy = $free_holds;
                    }
                    else
                    {
                        $organics_buy = $source['organics'];
                    }

                    $free_holds -= $organics_buy;
                    $shipinfo['organics'] += $organics_buy;
                    echo "$l_tdr_loaded " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics<br>";
                }
                else
                {
                    $organics_buy = 0;
                }

                if ($ore_buy == 0 && $goods_buy == 0 && $organics_buy == 0)
                {
                    echo "$l_tdr_nothingtoload<br>";
                }

                if ($traderoute['circuit'] == '1')
                {
                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET ore=?, goods=?, organics=? WHERE ship_id=?", array($shipinfo['ore'], $shipinfo['goods'],$shipinfo['organics'], $shipinfo['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                }
            }
            else  // Buy from planet - not implemented yet
            {
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET ore=ore-?, goods=goods-?, organics=organics-? WHERE planet_id=?", array($ore_buy, $goods_buy, $organics_buy, $source['planet_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            ///
            if ($spy_success_factor)
            {
                // echo "Start : $source[planet_id], $shipinfo[ship_id], $planet_detect_success1<br>";
                spy_sneak_to_planet($db,$source['planet_id'], $shipinfo['ship_id']);
                spy_sneak_to_ship($db,$source['planet_id'], $shipinfo['ship_id']);
                spy_detect_planet($db,$shipinfo['ship_id'], $source['planet_id'], $planet_detect_success1);
            }
        }
        // ---------- destination is a planet, so load cols and weapons
        elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
        {
            if ($source['colonists'] > 0 && $free_holds > 0 && $playerinfo['trade_colonists'] == 'Y')
            {
                if ($source['colonists'] > $free_holds)
                {
                    $colonists_buy = $free_holds;
                }
                else
                {
                    $colonists_buy = $source['colonists'];
                }

                $free_holds -= $colonists_buy;
                $shipinfo['colonists'] += $colonists_buy;
                echo "$l_tdr_loaded " . number_format($colonists_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_colonists<br>";
            }
            else
            {
                $colonists_buy = 0;
            }

            $free_torps = num_torpedoes($shipinfo['torp_launchers']) - $shipinfo['torps'];
            if ($source['torps'] > 0 && $free_torps > 0 && $playerinfo['trade_torps'] == 'Y')
            {
                if ($source['torps'] > $free_torps)
                {
                    $torps_buy = $free_torps;
                }
                else
                {
                    $torps_buy = $source['torps'];
                }

                $free_torps -= $torps_buy;
                $shipinfo[torps] += $torps_buy;
                echo "$l_tdr_loaded " . number_format($torps_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_torps<br>";
            }
            else
            {
                $torps_buy = 0;
            }

            $free_fighters = num_fighters($shipinfo['computer']) - $shipinfo['fighters'];
            if ($source['fighters'] > 0 && $free_fighters > 0 && $playerinfo['trade_fighters'] == 'Y')
            {
                if ($source['fighters'] > $free_fighters)
                {
                    $fighters_buy = $free_fighters;
                }
                else
                {
                    $fighters_buy = $source['fighters'];
                }

                $free_fighters -= $fighters_buy;
                $shipinfo['fighters'] += $fighters_buy;
                echo "$l_tdr_loaded " . number_format($fighters_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_fighters<br>";
            }
            else
            {
                $fighters_buy = 0;
            }

            if ($fighters_buy == 0 && $torps_buy == 0 && $colonists_buy == 0)
            {
                echo "$l_tdr_nothingtoload<br>";
            }

            if ($traderoute['circuit'] == '1')
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET torps=?, fighters=?, colonists=? WHERE ship_id=?", array($shipinfo['torps'], $shipinfo['fighters'], $shipinfo['colonists'], $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET colonists=colonists-?, torps=torps-?, fighters=fighters-? WHERE planet_id=?", array($colonists_buy, $torps_buy, $fighters_buy, $source['planet_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            col_count_news($db, $source['owner_id']);
            ///
            if ($spy_success_factor)
            {
                // echo "Start pl : $source[planet_id], $shipinfo[ship_id], $planet_detect_success1<br>";
                spy_sneak_to_planet($db,$source['planet_id'], $shipinfo['ship_id']);
                spy_sneak_to_ship($db,$source['planet_id'], $shipinfo['ship_id']);
                spy_detect_planet($db,$shipinfo['ship_id'], $source['planet_id'], $planet_detect_success1);
            }
        }
    }

    if ($dist['scooped1'] != 0)
    {
        echo "$l_tdr_scooped " . number_format($dist['scooped1'], 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy<br>";
    }

    echo '
    </td>
    <td align=center>
    ';

    if ($traderoute['circuit'] == '2')
    {
        $playerinfo['credits'] += $sourcecost;
        $destcost = 0;
        if ($traderoute['dest_type'] == 'P')
        {
            $ore_buy = 0;
            $goods_buy = 0;
            $organics_buy = 0;
            $energy_buy = 0;
            // Sells commodities
            $portfull = 0;
            if ($dest['port_type'] != 'ore')
            {
                $ore_price1 = $ore_price + $ore_delta * $dest['port_ore'] / $ore_limit * $inventory_factor;
                if ($dest['port_ore'] - $shipinfo['ore'] < 0)
                {
                    $ore_buy = $dest['port_ore'];
                    $portfull = 1;
                }
                else
                {
                    $ore_buy = $shipinfo['ore'];
                }

                $destcost += $ore_buy * $ore_price1;
                if ($ore_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore<br>";
                    }
                }

                $shipinfo['ore'] -= $ore_buy;
            }

            $portfull = 0;
            if ($dest['port_type'] != 'goods')
            {
                $goods_price1 = $goods_price + $goods_delta * $dest['port_goods'] / $goods_limit * $inventory_factor;
                if ($dest['port_goods'] - $shipinfo['goods'] < 0)
                {
                    $goods_buy = $dest['port_goods'];
                    $portfull = 1;
                }
                else
                {
                    $goods_buy = $shipinfo['goods'];
                }

                $destcost += $goods_buy * $goods_price1;
                if ($goods_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods<br>";
                    }
                }

                $shipinfo['goods'] -= $goods_buy;
            }

            $portfull = 0;
            if ($dest['port_type'] != 'organics')
            {
                $organics_price1 = $organics_price + $organics_delta * $dest['port_organics'] / $organics_limit * $inventory_factor;
                if ($dest['port_organics'] - $shipinfo['organics'] < 0)
                {
                    $organics_buy = $dest['port_organics'];
                    $portfull = 1;
                }
                else
                {
                    $organics_buy = $shipinfo['organics'];
                }

                $destcost += $organics_buy * $organics_price1;
                if ($organics_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics<br>";
                    }
                }
                $shipinfo['organics'] -= $organics_buy;
            }

            $portfull = 0;
            if ($dest['port_type'] != 'energy' && $playerinfo['trade_energy'] == 'Y')
            {
                $energy_price1 = $energy_price + $energy_delta * $dest['port_energy'] / $energy_limit * $inventory_factor;
                if ($dest['port_energy'] - $shipinfo['energy'] < 0)
                {
                    $energy_buy = $dest['port_energy'];
                    $portfull = 1;
                }
                else
                {
                    $energy_buy = $shipinfo['energy'];
                }

                $destcost += $energy_buy * $energy_price1;
                if ($energy_buy != 0)
                {
                    if ($portfull == 1)
                    {
                        echo "$l_tdr_sold " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy<br>";
                    }
                }
                $shipinfo['energy'] -= $energy_buy;
            }
            else
            {
                $energy_buy = 0;
            }

            $free_holds = num_holds($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];

            // Time to buy
            if ($dest['port_type'] == 'ore')
            {
                $ore_price1 = $ore_price - $ore_delta * $dest['port_ore'] / $ore_limit * $inventory_factor;
                if ($traderoute['source_type'] == 'L')
                {
                    $ore_buy = 0;
                }
                else
                {
                    $ore_buy = $free_holds;
                    if ($playerinfo['credits'] + $destcost < $ore_buy * $ore_price1)
                    {
                        $ore_buy = ($playerinfo['credits'] + $destcost) / $ore_price1;
                    }

                    if ($dest['port_ore'] < $ore_buy)
                    {
                        $ore_buy = $dest['port_ore'];
                        if ($dest['port_ore'] == 0)
                        {
                            echo "$l_tdr_bought " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($ore_buy != 0)
                    {
                        echo "$l_tdr_bought " . number_format($ore_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_ore<br>";
                    }

                    $shipinfo['ore'] += $ore_buy;
                    $destcost -= $ore_buy * $ore_price1;
                }

                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($dest['port_type'] == 'goods')
            {
                $goods_price1 = $goods_price - $goods_delta * $dest['port_goods'] / $goods_limit * $inventory_factor;
                if ($traderoute['source_type'] == 'L')
                {
                    $goods_buy = 0;
                }
                else
                {
                    $goods_buy = $free_holds;
                    if ($playerinfo['credits'] + $destcost < $goods_buy * $goods_price1)
                    {
                        $goods_buy = ($playerinfo['credits'] + $destcost) / $goods_price1;
                    }

                    if ($dest['port_goods'] < $goods_buy)
                    {
                        $goods_buy = $dest['port_goods'];
                        if ($dest['port_goods'] == 0)
                        {
                            echo "$l_tdr_bought " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($goods_buy != 0)
                    {
                        echo "$l_tdr_bought " . number_format($goods_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_goods<br>";
                    }

                    $shipinfo['goods'] += $goods_buy;
                    $destcost -= $goods_buy * $goods_price1;
                }

                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($dest['port_type'] == 'organics')
            {
                $organics_price1 = $organics_price - $organics_delta * $dest['port_organics'] / $organics_limit * $inventory_factor;
                if ($traderoute['source_type'] == 'L')
                {
                    $organics_buy = 0;
                }
                else
                {
                    $organics_buy = $free_holds;
                    if ($playerinfo['credits'] + $destcost < $organics_buy * $organics_price1)
                    {
                        $organics_buy = ($playerinfo['credits'] + $destcost) / $organics_price1;
                    }

                    if ($dest['port_organics'] < $organics_buy)
                    {
                        $organics_buy = $dest['port_organics'];
                        if ($dest['port_organics'] == 0)
                        {
                            echo "$l_tdr_bought " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($organics_buy != 0)
                    {
                        echo "$l_tdr_bought " . number_format($organics_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_organics<br>";
                    }

                    $shipinfo['organics'] += $organics_buy;
                    $destcost -= $organics_buy * $organics_price1;
                }

                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($dest['port_type'] == 'energy')
            {
                $energy_price1 = $energy_price - $energy_delta * $dest['port_energy'] / $energy_limit * $inventory_factor;
                if ($traderoute['source_type'] == 'L')
                {
                    $energy_buy = 0;
                }
                else
                {
                    $energy_buy = (5 * num_level($shipinfo['power'])) - $shipinfo['energy'] - $dist['scooped1'];
                    if ($playerinfo['credits'] + $destcost < $energy_buy * $energy_price1)
                    {
                        $energy_buy = ($playerinfo['credits'] + $destcost) / $energy_price1;
                    }

                    if ($dest['port_energy'] < $energy_buy)
                    {
                        $energy_buy = $dest['port_energy'];
                        if ($dest['port_energy'] == 0)
                        {
                            echo "$l_tdr_bought " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($energy_buy != 0)
                    {
                        echo "$l_tdr_bought " . number_format($energy_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy<br>";
                    }

                    $shipinfo['energy'] += $energy_buy;
                    $destcost -= $energy_buy * $energy_price1;
                }

                if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
                {
                    echo "$l_tdr_nothingtotrade<br>";
                }

                $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            if ($dist['scooped2'] > 0)
            {
                $shipinfo['energy']+= $dist['scooped2'];
                if ($shipinfo['energy'] > (5 * num_level($shipinfo['power'])))
                {
                    $shipinfo['energy'] = (5 * num_level($shipinfo['power']));
                }
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET ore=?, goods=?, organics=?, energy=? WHERE ship_id=?", array($shipinfo['ore'], $shipinfo['goods'], $shipinfo['organics'], $shipinfo['energy'], $shipinfo['ship_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
        else // Dest is planet
        {
            if ($traderoute['source_type'] == 'L'  || $traderoute['source_type'] == 'C')
            {
                $colonists_buy=0;
                $fighters_buy=0;
                $torps_buy=0;
            }

            if ($playerinfo['trade_colonists'] == 'Y')
            {
                $colonists_buy += $shipinfo['colonists'];
                $col_dump = $shipinfo['colonists'];
                if ($dest['colonists'] + $colonists_buy >= $colonist_limit)
                {
                    $exceeding = $dest['colonists'] + $colonists_buy - $colonist_limit;
                    $col_dump = $exceeding;
                    $setcol = 1;
                    $colonists_buy-=$exceeding;
                    if ($colonists_buy < 0)
                    {
                        $colonists_buy = 0;
                    }
                }
            }
            else
            {
                $col_dump = 0;
            }

            if ($colonists_buy != 0)
            {
                if ($setcol ==1)
                {
                    echo "$l_tdr_dumped " . number_format($colonists_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_colonists ($l_tdr_planetisovercrowded)<br>";
                }
                else
                {
                    echo "$l_tdr_dumped " . number_format($colonists_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_colonists<br>";
                }
            }

            if ($playerinfo['trade_fighters'] == 'Y')
            {
                $fighters_buy += $shipinfo['fighters'];
                $fight_dump = $shipinfo['fighters'];
            }
            else
            {
                $fight_dump = 0;
            }

            if ($fighters_buy != 0)
            {
                echo "$l_tdr_dumped " . number_format($fighters_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_fighters<br>";
            }

            if ($playerinfo['trade_torps'] == 'Y')
            {
                $torps_buy += $shipinfo['torps'];
                $torps_dump = $shipinfo['torps'];
            }
            else
            {
                $torps_dump = 0;
            }

            if ($torps_buy != 0)
            {
                echo "$l_tdr_dumped " . number_format($torps_buy, 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_torps<br>";
            }

            if ($torps_buy == 0 && $fighters_buy == 0 && $colonists_buy == 0 && $organics_buy == 0)
            {
                echo "$l_tdr_nothingtodump<br>";
            }

            if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
            {
                if ($playerinfo['trade_colonists'] == 'Y')
                {
                    if ($setcol != 1)
                    {
                        $col_dump = 0;
                    }
                }
                else
                {
                    $col_dump = $shipinfo['colonists'];
                }

                if ($playerinfo['trade_fighters'] == 'Y')
                {
                    $fight_dump = 0;
                }
                else
                {
                    $fight_dump = $shipinfo['fighters'];
                }

                if ($playerinfo['trade_torps'] == 'Y')
                {
                    $torps_dump = 0;
                }
                else
                {
                    $torps_dump = $shipinfo['torps'];
                }
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET colonists=colonists+?, fighters=fighters+?, torps=torps+? WHERE planet_id=", array($colonists_buy, $fighters_buy, $torps_buy, $traderoute['dest_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            col_count_news($db, $playerinfo['player_id']);
            ///
            if ($spy_success_factor)
            {
                // echo "Finish : $traderoute[dest_id], $shipinfo[ship_id], $planet_detect_success1<br>";
                spy_sneak_to_planet($db,$traderoute['dest_id'], $shipinfo['ship_id']);
                spy_sneak_to_ship($db,$traderoute['dest_id'], $shipinfo['ship_id']);
                spy_detect_planet($db,$shipinfo['ship_id'], $traderoute['dest_id'], $planet_detect_success1);
            }

            // Check that the ship doesnt get more energy than it is allowed to have
            if ($shipinfo['energy'] + $dist['scooped'] > (5 * num_level($shipinfo['power'])))
            {
                $dist['scooped'] = (5 * num_level($shipinfo['power'])) - $shipinfo['energy'];
            }

            if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET colonists=?, fighters=?, torps=?, energy=energy+? WHERE ship_id=?", array($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $shipinfo['ship_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            else
            {
                if ($setcol == 1)
                {
                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET colonists=?, fighters=fighters-?, torps=torps-?, energy=energy+? WHERE ship_id=?", array($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $shipinfo['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                }
                else
                {
                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET colonists=colonists-?, fighters=fighters-?, torps=torps-?, energy=energy+? WHERE ship_id=?", array($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $shipinfo['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                }
            }
        }

        if ($dist['scooped2'] != 0)
        {
            echo "$l_tdr_scooped " . number_format($dist['scooped1'], 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_tdr_energy<br>";
        }
    }
    else
    {
        echo $l_tdr_onlyonewaytdr;
        $destcost = 0;
    }

    echo "</td></tr><tr bgcolor=\"#400040\"><td align=center>";

    if ($sourcecost > 0)
    {
        echo "$l_tdr_profit : " . number_format($sourcecost, 0, $local_number_dec_point, $local_number_thousands_sep);
    }
    else
    {
        echo "$l_tdr_cost : " . number_format($sourcecost, 0, $local_number_dec_point, $local_number_thousands_sep);
    }

    echo "</td><td align=center>";

    if ($destcost > 0)
    {
        echo "$l_tdr_profit : " . number_format($destcost, 0, $local_number_dec_point, $local_number_thousands_sep);
    }
    else
    {
        echo "$l_tdr_cost : " . number_format($destcost, 0, $local_number_dec_point, $local_number_thousands_sep);
    }

    echo '
    </td></tr>
    </table>
    <div style="text-align:center">
    <strong>
    ';

    $total_profit = $sourcecost + $destcost;
    if ($total_profit > 0)
    {
        echo "$l_tdr_totalprofit : " . number_format($total_profit, 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong><br>";
    }
    ///- end with <p> instead of <br> above.
    else
    {
        echo "$l_tdr_totalcost : " . number_format($total_profit, 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong><br>";
    }

    if ($traderoute['circuit'] == '1')
    {
        $newsec = $destport['sector_id'];
    }
    else
    {
        $newsec = $sourceport['sector_id'];
    }

    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-?, credits=credits+?, turns_used=turns_used+? WHERE player_id=?", array($dist['triptime'], $total_profit, $dist['triptime'], $playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id=? WHERE ship_id=?", array($newsec, $shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    get_info($db);
//  $playerinfo['credits']+=$total_profit - $sourcecost;
//  $playerinfo['turns']-=$dist['triptime'];

    echo "<strong>$l_tdr_turnsused : $dist[triptime]</strong><br>";
    echo "<strong>$l_tdr_turnsleft : $playerinfo[turns]</strong><br>";
    ///- add a <p> before ending.

    echo "<strong>$l_tdr_credits : " . number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong><br></div>";

    // stupid user limiter.
    if ( ($playerinfo['turns'] <= 0) || ($playerinfo['credits'] < 0) || (($traderoute['source_type'] != 'L') && ($traderoute['source_type'] != 'C') && ($traderoute['dest_type'] != 'L') && ($traderoute['dest_type'] != 'C') && (($total_profit <= 0) || (($ore_buy == 0) && ($goods_buy == 0) && ($organics_buy == 0)))) )
    {
        traderoute_die("");
    }

    if ($i == 1)
    {
        $l_tdr_engageagain2 = str_replace("[five]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=5\">" . $l_tdr_engageagain5 . "</a>", $l_tdr_engageagain2);
        $l_tdr_engageagain2 = str_replace("[ten]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=10\">" . $l_tdr_engageagain10 . "</a>", $l_tdr_engageagain2);
        $l_tdr_engageagain2 = str_replace("[fifty]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=50\">" . $l_tdr_engageagain50 . "</a>", $l_tdr_engageagain2);
        $l_tdr_engageagain2 = str_replace("[tdr_engage]", $_GET['engage'], $l_tdr_engageagain2);
        echo "<a href=\"traderoute.php?engage=" . $_GET['engage'] . "\">" . $l_tdr_engageagain . "</a>" . $l_tdr_engageagain2;
        echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean_forms.js\"></script><noscript></noscript>";
        echo "<form action=\"traderoute.php?engage=$_GET[engage]\" method=post>" .
             "<br>$l_tdr_timestorep <input type=text name=tr_repeat value=1 size=5>" .
//             " <input type=submit value=$l_submit>";
             " <input type=submit value=$l_submit onclick=\"clean_forms()\">";
        echo "</form>";
        traderoute_die("");
    }
}
?>
