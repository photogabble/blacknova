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
// File: bigbang/80.php

$pos = strpos ($_SERVER['PHP_SELF'], "/80.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die ();
}

// Determine current step, next step, and number of steps
$bigbang_info = BntBigBang::findStep (__FILE__);

// Set variables
$variables['templateset']            = $bntreg->get ("default_template");
$variables['body_class']             = 'bigbang';
$variables['steps']                  = $bigbang_info['steps'];
$variables['current_step']           = $bigbang_info['current_step'];
$variables['next_step']              = $bigbang_info['next_step'];
$variables['sector_max']             = (int) filter_input (INPUT_POST, 'sektors', FILTER_SANITIZE_NUMBER_INT); // Sanitize the input and typecast it to an int
$variables['spp']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'special', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['oep']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'ore', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['ogp']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'organics', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['gop']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'goods', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['enp']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'energy', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['nump']                   = round ($variables['sector_max'] * filter_input (INPUT_POST, 'planets', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['empty']                  = $variables['sector_max'] - $variables['spp'] - $variables['oep'] - $variables['ogp'] - $variables['gop'] - $variables['enp'];
$variables['initscommod']            = filter_input (INPUT_POST, 'initscommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['initbcommod']            = filter_input (INPUT_POST, 'initbcommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['fedsecs']                = filter_input (INPUT_POST, 'fedsecs', FILTER_SANITIZE_NUMBER_INT);
$variables['loops']                  = filter_input (INPUT_POST, 'loops', FILTER_SANITIZE_NUMBER_INT);
$variables['swordfish']              = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);
$variables['autorun']                = filter_input (INPUT_POST, 'autorun', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe'));

$variables['update_ticks_results']['sched'] = $sched_ticks;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_turns, 'sched_turns.php', ?)", array (time ()));
$variables['update_turns_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_turns_results']['sched'] = $sched_turns;

// This is causing errors at the moment, disabling until we get clean solutions for it.
//$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_turns, 'sched_xenobe.php', ?)", array (time ()));
//$variables['update_xenobe_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_xenobe_results']['result'] = "DISABLED!";
$variables['update_xenobe_results']['sched'] = $sched_turns;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_igb, 'sched_igb.php', ?)", array (time ()));
$variables['update_igb_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_igb_results']['sched'] = $sched_igb;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_news, 'sched_news.php', ?)", array (time ()));
$variables['update_news_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_news_results']['sched'] = $sched_news;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_planets, 'sched_planets.php', ?)", array (time ()));
$variables['update_planets_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_planets_results']['sched'] = $sched_planets;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_ports, 'sched_ports.php', ?)", array (time ()));
$variables['update_ports_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_ports_results']['sched'] = $sched_ports;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_turns, 'sched_tow.php', ?)", array (time ()));
$variables['update_tow_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_tow_results']['sched'] = $sched_turns; // Towing occurs at the same time as turns

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_ranking, 'sched_ranking.php', ?)", array (time ()));
$variables['update_ranking_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_ranking_results']['sched'] = $sched_ranking;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_degrade, 'sched_degrade.php', ?)", array (time ()));
$variables['update_degrade_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_degrade_results']['sched'] = $sched_degrade;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_apocalypse, 'sched_apocalypse.php', ?)", array (time ()));
$variables['update_apoc_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_apoc_results']['sched'] = $sched_apocalypse;

$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', $sched_thegovernor, 'sched_thegovernor.php', ?)", array (time ()));
$variables['update_gov_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_gov_results']['sched'] = $sched_thegovernor;

// This adds a news item into the newly created news table
$resxx = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, date, news_type) " .
              "VALUES ('Big Bang!','Scientists have just discovered the Universe exists!',NOW(), 'col25')");
$variables['first_news_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);

if ($bnt_ls === true)
{
// $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('Y', 60, 'bnt_ls_client.php', ?)", array (time ()));
// FIX table_row ($db, "The public list updater will occur every 60 minutes", $langvars['l_cu_failed'], $langvars['l_cu_inserted']);
    $creating = 1;
// include_once './bnt_ls_client.php';
}
// FIX table_footer ($langvars['l_cu_completed']);
// FIX table_header ($langvars['l_cu_account_info'] ." " . $admin_name, "h1");

$update = $db->Execute ("INSERT INTO {$db->prefix}ibank_accounts (ship_id,balance,loan) VALUES (1,0,0)");
$variables['ibank_results']['result'] = DbOp::dbResult ($db, $update, __LINE__, __FILE__);
$stamp = date ("Y-m-d H:i:s");

// Hash the password.  $hashed_pass will be a 60-character string.
$hasher = new PasswordHash (10, false); // The first number is the hash strength, or number of iterations of bcrypt to run.
$hashed_pass = $hasher->HashPassword (ADMIN_PW);

$adm_ship = $db->qstr ($admin_ship_name);
$adm_name = $db->qstr ($admin_name);
$adm_ship_sql = "INSERT INTO {$db->prefix}ships " .
                "(ship_name, ship_destroyed, character_name, password, " .
                "email, turns, armor_pts, credits, sector, ship_energy, " .
                "ship_fighters, last_login, " .
                "ip_address, lang) VALUES " .
                "($adm_ship, 'N', $adm_name, '$hashed_pass', " .
                "'$admin_mail', $start_turns, $start_armor, $start_credits, 1, $start_energy, " .
                "$start_fighters, '$stamp', " .
                "'1.1.1.1', '$default_lang')";
$resxx = $db->Execute ($adm_ship_sql);
$variables['admin_account_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['admin_name'] = $admin_name;

$adm_terri = $db->qstr ($admin_zone_name);
$resxx = $db->Execute ("INSERT INTO {$db->prefix}zones (zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ($adm_terri, 1, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
$variables['admin_zone_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/bigbang/80.tpl");
?>
