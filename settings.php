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
// File: settings.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "truefalse.php");
dynamic_loader ($db, "time_since_reset.php");

// Load language variables
load_languages($db, $raw_prefix, 'settings');
load_languages($db, $raw_prefix, 'global_includes');

$title = $l_s_gamesettings;
include_once './header.php';

if ($db->prefix != $raw_prefix)
{
    // Game status
    $gamestatus = array(array());
    $gamestatus[0]['item'] = $l_s_release_version;
    $gamestatus[0]['value'] = $release_version;
    $gamestatus[1]['item'] = $l_s_time_since_reset;
    $gamestatus[1]['value'] = time_since_reset($db);
    $gamestatus[2]['item'] = $l_s_allowpl;
    $gamestatus[2]['value'] = truefalse($server_closed,False,$l_s_yes,"<font color=red>$l_s_no</font>");
    $gamestatus[3]['item'] = $l_s_allownewpl;
    $gamestatus[3]['value'] = truefalse($account_creation_closed,False,$l_s_yes,"<font color=red>$l_s_no</font>");

    // Game Options
    $gameoptions = array(array());
    $gameoptions[0]['item'] = $l_s_allowteamplcreds;
    $gameoptions[0]['value'] = truefalse($team_planet_transfers,True,$l_s_yes,"<font color=red>$l_s_no</font>");
    $gameoptions[1]['item'] = $l_s_allowfullscan;
    $gameoptions[1]['value'] = truefalse($allow_fullscan,True,$l_s_yes,"<font color=red>$l_s_no</font>");
    $gameoptions[2]['item'] = $l_s_sofa;
    $gameoptions[2]['value'] = truefalse($sofa_on,True,$l_s_yes,"<font color=red>$l_s_no</font>");
    $gameoptions[3]['item'] = $l_s_showpassword;
    $gameoptions[3]['value'] = truefalse($display_password,True,$l_s_yes,"<font color=red>$l_s_no</font>");
    $gameoptions[4]['item'] = $l_s_genesisdestroy;
    $gameoptions[4]['value'] = truefalse($allow_genesis_destroy,True,$l_s_yes,"<font color=red>$l_s_no</font>");
    $gameoptions[5]['item'] = $l_s_ksm;
    $gameoptions[5]['value'] = truefalse($ksm_allowed,True,$l_s_enabled,"<font color=red>$l_s_disabled</font>");
    $gameoptions[6]['item'] = $l_s_navcomp;
    $gameoptions[6]['value'] = truefalse($allow_navcomp,True,$l_s_enabled,"<font color=red>$l_s_disabled</font>");
    $gameoptions[7]['item'] = $l_s_newbienice;
    $gameoptions[7]['value'] = truefalse($newbie_nice,True,$l_s_enabled,"<font color=red>$l_s_disabled</font>");

    // Game Settings
    $gamesettings = array(array());
    $gamesettings[0]['item'] = $l_s_gameversion;
    $gamesettings[0]['value'] =     $game_name;
    $gamesettings[1]['item'] = $l_s_minhullmines;
    $gamesettings[1]['value'] = $mine_hullsize;
    $gamesettings[2]['item'] = $l_s_averagetechewd;
    $gamesettings[2]['value'] = $ewd_maxavgtechlevel;
    $gamesettings[3]['item'] = $l_s_numsectors;
    $gamesettings[3]['value'] = number_format($sector_max, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[4]['item'] = $l_s_maxwarpspersector;
    $gamesettings[4]['value'] = $link_max;
    $gamesettings[5]['item'] = $l_s_averagecombattech;
    $gamesettings[5]['value'] = $max_avg_combat_tech;
    $gamesettings[6]['item'] = $l_s_techupgradebase;
    $gamesettings[6]['value'] = $basedefense;
    $gamesettings[7]['item'] = $l_s_collimit;
    $gamesettings[7]['value'] = number_format($colonist_limit, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[8]['item'] = $l_s_maxturns;
    $gamesettings[8]['value'] = number_format($max_turns, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[9]['item'] = $l_s_maxplanetssector;
    $gamesettings[9]['value'] = $max_star_size;
    $gamesettings[10]['item'] = $l_s_maxtraderoutes;
    $gamesettings[10]['value'] = $max_traderoutes_player;
    $gamesettings[11]['item'] = $l_s_colreprodrate;
    $gamesettings[11]['value'] = $colonist_reproduction_rate. "%";
    $gamesettings[12]['item'] = $l_s_energyperfighter;
    $gamesettings[12]['value'] = $energy_per_fighter;
    $gamesettings[13]['item'] = $l_s_secfighterdegrade;
    $gamesettings[13]['value'] = ($defense_degrade_rate * 100) . "%";
    $gamesettings[14]['item'] = $l_s_planetinterest;
    $gamesettings[14]['value'] = number_format(($interest_rate - 1) * 100 , 0, $local_number_dec_point, $local_number_thousands_sep) . "%";
    $gamesettings[15]['item'] = $l_s_colsperfighter;
    $gamesettings[15]['value'] = number_format((1 / $colonist_production_rate)/$fighter_prate, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[16]['item'] = $l_s_colspertorp;
    $gamesettings[16]['value'] = number_format((1 / $colonist_production_rate)/$torpedo_prate, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[17]['item'] = $l_s_colsperore;
    $gamesettings[17]['value'] = number_format((1 / $colonist_production_rate)/$ore_prate, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[18]['item'] = $l_s_colsperorganics;
    $gamesettings[18]['value'] = number_format((1 / $colonist_production_rate)/$organics_prate, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[19]['item'] = $l_s_colspergoods;
    $gamesettings[19]['value'] = number_format((1 / $colonist_production_rate)/$goods_prate, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[20]['item'] = $l_s_colsperenergy;
    $gamesettings[20]['value'] = number_format((1 / $colonist_production_rate)/$energy_prate, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[21]['item'] = $l_s_colspercreds;
    $gamesettings[21]['value'] = number_format((1 / $colonist_production_rate)/$credits_prate, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[22]['item'] = $l_s_max_members_team;
    $gamesettings[22]['value'] = number_format($max_team_members, 0, $local_number_dec_point, $local_number_thousands_sep);
    $gamesettings[23]['item'] = $l_s_startcredits;
    $gamesettings[23]['value'] = number_format($start_credits, 0, $local_number_dec_point, $local_number_thousands_sep);

    // Scheduler Settings
    $schedsettings = array(array());
    $schedsettings[0]['item'] = $l_s_schedtype;
    $schedsettings[0]['value'] = truefalse($sched_type,0,$l_s_cronbased,$l_s_playertriggered);
    $schedsettings[1]['item'] = $l_s_ticksupdate;
    $schedsettings[1]['value'] = $sched_ticks . $l_s_minutes;
    $schedsettings[2]['item'] = $l_s_turnsupdate;
    $schedsettings[2]['value'] = $sched_turns . $l_s_minutes;
    $schedsettings[3]['item'] = $l_s_aiupdate;
    $schedsettings[3]['value'] = $sched_turns . $l_s_minutes;
    $schedsettings[4]['item'] = $l_s_planetupdate;
    $schedsettings[4]['value'] = $sched_planets . $l_s_minutes;
    $schedsettings[5]['item'] = $l_s_portsupdate;
    $schedsettings[5]['value'] = $sched_ports . $l_s_minutes;
    $schedsettings[6]['item'] = $l_s_towupdate;
    $schedsettings[6]['value'] = $sched_turns . $l_s_minutes;
    $schedsettings[7]['item'] = $l_s_scoreupdate;
    $schedsettings[7]['value'] = $sched_ranking . $l_s_minutes;
    $schedsettings[8]['item'] = $l_s_secdefdegrupdate;
    $schedsettings[8]['value'] = $sched_degrade . $l_s_minutes;
    $schedsettings[9]['item'] = $l_s_apocalypseupdate;
    $schedsettings[9]['value'] = $sched_apocalypse . $l_s_minutes;
    $schedsettings[10]['item'] = $l_s_serverlistupdate;
    $schedsettings[10]['value'] = $sched_serverlist . $l_s_minutes;
    $schedsettings[11]['item'] = $l_s_igbturnsupdate;
    $schedsettings[11]['value'] = $sched_igb . $l_s_minutes;
    if ($spy_success_factor > 0)
    {
        $schedsettings[12]['item'] = $l_s_spyupdate;
        $schedsettings[12]['value'] = $sched_spies . $l_s_minutes;
    }

    // Spy settings
    $temp = ($spy_success_factor) ? "YES": "NO";
    $spysettings = array(array());
    $spysettings[0]['item'] = $l_s_spies;
    $spysettings[0]['value'] = truefalse($temp,"YES",$l_s_enabled,"<font color=red>$l_s_disabled</font>");
    $spysettings[1]['item'] = $l_s_spiesperplanet;
    $spysettings[1]['value'] = $max_spies_per_planet;
    $spysettings[2]['item'] = $l_s_spysuccessfactor;
    $spysettings[2]['value'] = number_format($spy_success_factor, 0, $local_number_dec_point, $local_number_thousands_sep);
    $spysettings[3]['item'] = $l_s_spykillfactor;
    $spysettings[3]['value'] = number_format($spy_kill_factor, 0, $local_number_dec_point, $local_number_thousands_sep);

    $temp = ($allow_spy_capture_planets) ? "YES": "NO";
    $spysettings[5]['item'] = $l_s_spycapture;
    $spysettings[5]['value'] = truefalse($temp,"YES",$l_s_yes,"<font color=red>$l_s_no</font>");

    // IBank settings
    $ibanksettings[0]['item'] = $l_s_igb;
    $ibanksettings[0]['value'] = truefalse($allow_ibank,True,$l_s_enabled,"<font color=red>$l_s_disabled</font>");
    $ibanksettings[1]['item'] = $l_s_igbirateperupdate;
    $ibanksettings[1]['value'] = ($ibank_interest * 100) . "%";
    $ibanksettings[2]['item'] = $l_s_igblrateperupdate;
    $ibanksettings[2]['value'] = ($ibank_loaninterest * 100) . "%";

    // Smarty assignments
    $template->assign("l_global_mlogin", $l_global_mlogin);
    $template->assign("l_global_mmenu", $l_global_mmenu);
    $template->assign("l_s_gameoptions", $l_s_gameoptions);
    $template->assign("l_s_gamesettings", $l_s_gamesettings);
    $template->assign("l_s_gameschedsettings", $l_s_gameschedsettings);
    $template->assign("l_s_spysettings", $l_s_spysettings);
    $template->assign("l_s_ibanksettings", $l_s_ibanksettings);
    $template->assign("spy_success_factor", $spy_success_factor);
    $template->assign("allow_ibank", $allow_ibank);
    $template->assign("gamestatus", $gamestatus);
    $template->assign("gameoptions", $gameoptions);
    $template->assign("gamesettings", $gamesettings);
    $template->assign("schedsettings", $schedsettings);
    $template->assign("spysettings", $spysettings);
    $template->assign("ibanksettings", $ibanksettings);
    $template->assign("color_line1", $color_line1);
    $template->assign("color_line2", $color_line2);
    $template->assign("templateset", $templateset);
    $template->assign("session_email", empty($_SESSION['email']));
}

$i = 0;
$debug_query = $db->Execute("SELECT gamenumber FROM {$raw_prefix}instances ORDER BY gamenumber ASC");
db_op_result($db,$debug_query,__LINE__,__FILE__);
while (!$debug_query->EOF)
{
    $gamenumber = $debug_query->fields['gamenumber'];
    $game_instances[$gamenumber] = $l_gamehash . $gamenumber;
    $i++;
    $debug_query->MoveNext();
}

$template->assign("title", $title);
$template->assign('game_instances' , $game_instances);
$template->assign("l_submit", $l_submit);
$template->assign("dbprefix", $db->prefix);
$template->assign("raw_prefix", $raw_prefix);
$template->display("$templateset/settings.tpl");

include_once './footer.php';
?>
