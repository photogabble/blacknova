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
// File: sched_spies.php

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_spies.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php"); 
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

// Dynamic functions
dynamic_loader ($db, "change_planet_ownership.php");
dynamic_loader ($db, "spyrand.php");
dynamic_loader ($db, "calc_ownership.php");
dynamic_loader ($db, "load_languages.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'common'); // For Unnamed planets in log entrie

echo "<strong>SPIES</strong><br>\n";

if ($spy_success_factor)
{
    $sabotage    = (900 / $spy_success_factor) + 1;
    $steal_intr  = (800 / $spy_success_factor) + 1;
    $birth       = (1000 / $spy_success_factor) + 1;
    $steal_money = (2000 / $spy_success_factor) + 1;
    $blowup_torp = (3000 / $spy_success_factor) + 1;
    $blowup_fits = (5000 / $spy_success_factor) + 1;
    $capture     = (20000 / $spy_success_factor) + 1;
    $kill        = (4000 / $spy_kill_factor) + 1;

    $sabotage_trigger    =  ($sabotage / 2);
    $steal_intr_trigger  =  ($steal_intr / 2);
    $birth_trigger       =  ($birth / 2);
    $steal_money_trigger =  ($steal_money / 2);
    $blowup_torp_trigger =  ($blowup_torp / 2);
    $blowup_fits_trigger =  ($blowup_fits / 2);
    $capture_trigger     =  ($capture / 2);
    $kill_trigger        =  (50 / $spy_kill_factor); // Don't write '$kill / 2' -- may cause a bug

    $lower = -$kill + 100 / $spy_kill_factor;
    $i = 0;

    for ($k = 0; $k<$multiplier; $k++)
    {
        // Getting all possibly needed information about the spy, the planet, the spy owner and his ship
        $spies = $db->Execute("SELECT {$db->prefix}planets.*, {$db->prefix}spies.*, {$db->prefix}players.character_name, {$db->prefix}ships.cloak AS spy_cloak FROM {$db->prefix}planets INNER JOIN {$db->prefix}spies ON {$db->prefix}planets.planet_id = {$db->prefix}spies.planet_id INNER JOIN {$db->prefix}players ON {$db->prefix}spies.owner_id = {$db->prefix}players.player_id INNER JOIN {$db->prefix}ships ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id WHERE {$db->prefix}spies.planet_id != '0' AND {$db->prefix}spies.active='Y' AND {$db->prefix}spies.ship_id = '0' ");

        while (!$spies->EOF)
        {
            $spy = $spies->fields;
            $flag = 1;

            if (!$spy['name'])
            {
                $spy['name'] = $l_unnamed;
            }

            for ($j=1; $j <= $i; $j++)
            {
                if ($spy['planet_id'] == $changed_planets[$j])
                {
                    $flag = 0;
                }
            }

            if ($spy['job_id'] == 0) // Not yet 'occupied' - ready to do something bad...
            {
                if ($spy['try_sabot'] == 'Y')
                {
                    $success = mt_rand(0, $sabotage);
                    if ($success == $sabotage_trigger && $flag)
                    {
                        $r1 = $db->Execute("SELECT SUM(spy_percent) as s_total FROM {$db->prefix}spies WHERE active='Y' AND planet_id=$spy[planet_id] AND job_id='1' ");
                        $total = $r1->fields['s_total'];
                        $total = (($colonist_production_rate - $total) * 30000);
                        $new_percet = spyrand(($total * 0.1), ($total * 0.3), 1.3); //10%...30%
                        $new_percet /= 30000.0;
                        if ($new_percet)
                        {
                            $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET spy_percent='$new_percet', job_id='1' WHERE spy_id=$spy[spy_id] ");
                            db_op_result($db,$debug_query,__LINE__,__FILE__);

                            $temp = number_format($new_percet*100.0, 0, $local_number_dec_point, $local_number_thousands_sep);
                            playerlog($db,$spy['owner_id'], "LOG_SPY_SABOTAGE", "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
                            $flag = 0;
                        }
                    }
                }

                if ($spy['try_inter'] == 'Y')
                {
                    $success = mt_rand(0, $steal_intr);
                    if ($success == $steal_intr_trigger && $flag)
                    {
                        $r1 = $db->Execute("SELECT SUM(spy_percent) as i_total FROM {$db->prefix}spies WHERE active='Y' AND planet_id=$spy[planet_id] AND job_id='2' ");
                        $total = $r1->fields['i_total'];
                        $total = (($interest_rate - $total - 1) * 30000);
                        $new_percet = spyrand(($total * 0.15), ($total * 0.35), 1.3); //15%...35%
                        $new_percet /= 30000.0;
                        if ($new_percet)
                        {
                            $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET spy_percent='$new_percet', job_id='2' WHERE spy_id=$spy[spy_id] ");
                            db_op_result($db,$debug_query,__LINE__,__FILE__);
                            $temp = number_format($new_percet*100.0, 5, $local_number_dec_point, $local_number_thousands_sep);
                            playerlog($db,$spy['owner_id'], "LOG_SPY_INTEREST", "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
                            $flag = 0;
                        }
                    }
                }

                if ($spy['try_birth'] == 'Y')
                {
                    $success = mt_rand(0, $birth);
                    if ($success == $birth_trigger && $flag)
                    {
                        $r1 = $db->Execute("SELECT SUM(spy_percent) as b_total FROM {$db->prefix}spies WHERE active='Y' AND planet_id=$spy[planet_id] AND job_id='3' ");
                        $total = $r1->fields['b_total'];
                        $total = (($colonist_reproduction_rate - $total) * 500000);
                        $new_percet = spyrand(($total * 0.1), ($total * 0.3), 1.3); //10%...30%
                        $new_percet /= 500000.0;
                        if ($new_percet)
                        {
                            $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET spy_percent='$new_percet', job_id='3' WHERE spy_id=$spy[spy_id] ");
                            db_op_result($db,$debug_query,__LINE__,__FILE__);
                            $temp = number_format($new_percet*100.0, 5, $local_number_dec_point, $local_number_thousands_sep);
                            playerlog($db,$spy['owner_id'], "LOG_SPY_BIRTH", "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
                            $flag = 0;
                        }
                    }
                }

                if ($spy['try_steal'] == 'Y')
                {
                    $success = mt_rand(0, $steal_money);
                    if ($success == $steal_money_trigger && $flag)
                    {
                        if ($spy['credits'] > 0)
                        {
                            $roll = spyrand(2400, 9000, 2.5); //8%...30%
                            $sum = ($spy['credits'] * $roll / 30000);
                            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits-$sum WHERE planet_id=$spy[planet_id] ");
                            db_op_result($db,$debug_query,__LINE__,__FILE__);

                            if ($allow_ibank)
                            {
                                $debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=balance+$sum WHERE player_id=$spy[owner_id]");
                                db_op_result($db,$debug_query,__LINE__,__FILE__);
                            }
                            else
                            {
                                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits+$sum WHERE player_id=$spy[owner_id]");
                                db_op_result($db,$debug_query,__LINE__,__FILE__);
                            }

                            $temp = number_format($sum, 0, $local_number_dec_point, $local_number_thousands_sep);
                            playerlog($db,$spy['owner_id'], "LOG_SPY_MONEY", "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
                            $flag = 0;
                            // don't change spy's job_id and don't inform the planet owner!
                        }
                    }
                }

                if ($spy['try_torps'] == 'Y')
                {
                    $success = mt_rand(0, $blowup_torp);
                    if ($success == $blowup_torp_trigger && $flag)
                    {
                        if ($spy['torps'] > 0)
                        {
                            $roll = spyrand(2100, 7500, 3); //7%...25%
                            $blow = ($spy['torps'] * $roll / 30000);
                            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET torps=torps-$blow WHERE planet_id=$spy[planet_id] ");
                            db_op_result($db,$debug_query,__LINE__,__FILE__);
                            $temp = number_format($blow, 0, $local_number_dec_point, $local_number_thousands_sep);
                            playerlog($db,$spy['owner_id'], "LOG_SPY_TORPS", "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
                            $flag = 0;
                            // don't change spy's job_id and don't inform the planet owner!
                        }
                    }
                }

                if ($spy['try_fits'] == 'Y')
                {
                    $success = mt_rand(0, $blowup_fits);
                    if ($success == $blowup_fits_trigger && $flag)
                    {
                        if ($spy['fighters'] > 0)
                        {
                            $roll = spyrand(2400, 9000, 4); //8%...30%
                            $blow = ($spy['fighters'] * $roll / 30000);
                            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET fighters=fighters-$blow WHERE planet_id=$spy[planet_id] ");
                            db_op_result($db,$debug_query,__LINE__,__FILE__);
                            $temp = number_format($blow, 0, $local_number_dec_point, $local_number_thousands_sep);
                            playerlog($db,$spy['owner_id'], "LOG_SPY_FITS", "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
                            $flag = 0;
                            // don't change spy's job_id and don't inform the planet owner!
                        }
                    }
                }

                if ($allow_spy_capture_planets && $spy['try_capture'] == 'Y')
                {
                    $success = mt_rand(0, $capture);
                    if ($success == $capture_trigger && $flag)
                    {
                        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET owner=$spy[owner_id] WHERE planet_id=$spy[planet_id]");
                        db_op_result($db,$debug_query,__LINE__,__FILE__);

                        change_planet_ownership($db, $spy['planet_id'], $spy['owner'], $spy['owner_id']);
                        calc_ownership($db,$spy['sector_id']);

                        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET on_planet = 'N',planet_id = '0' WHERE on_planet='Y' and planet_id = '$spy[planet_id]'");
                        db_op_result($db,$debug_query,__LINE__,__FILE__);

                        playerlog($db,$spy['owner_id'], "LOG_SPY_CPTURE", "$spy[spy_id]|$spy[name]|$spy[sector_id]");
                        playerlog($db,$spy['owner'], "LOG_SPY_CPTURE_OWNER", "$spy[name]|$spy[sector_id]|$spy[character_name]");
                        $flag = 0;
                        $i++;
                        $changed_planets[$i] = $spy['planet_id'];
                        // don't change spy's job_id!
                    }
                }
            } // Job_id==0

            $base_factor = ($spy['base'] == 'Y') ? $basedefense : 0;
            $spy['sensors'] += $base_factor;

            $res = $db->Execute("SELECT MAX(sensors) AS maxsensors FROM {$db->prefix}ships WHERE planet_id=$spy[planet_id] AND on_planet='Y'");
            if (!$res->EOF)
            {
                if ($spy['sensors'] < $res->fields['maxsensors'])
                {
                    $spy['sensors'] = $res->fields['maxsensors'];
                }
            }

            $kill2 = ($spy['spy_cloak'] - $spy['sensors']) * $kill * 0.1;
            if ($kill2 > $kill)
            {
                $kill2 = $kill;
            }

            if ($kill2 < $lower)
            {
                $kill2 = $lower;
            }

            $kill2 =  ($kill2 + $kill) + 1;
            $success = mt_rand(0, $kill2);
            if ($success == $kill_trigger && $flag)
            {
                $debug_query = $db->Execute("DELETE FROM {$db->prefix}spies WHERE spy_id=$spy[spy_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                playerlog($db,$spy['owner_id'], "LOG_SPY_KILLED_SPYOWNER", "$spy[spy_id]|$spy[name]|$spy[sector_id]");
                playerlog($db,$spy['owner'], "LOG_SPY_KILLED", "$spy[name]|$spy[sector_id]|$spy[character_name]");
            }

            $spies->MoveNext();
        } // While
        echo "Spies updated.<br><br>\n";
    }
}
else
{
    echo "Spies are disabled in this game.<br><br>";
    $multiplier = 0;
}
?>
