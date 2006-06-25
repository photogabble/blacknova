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
// File: mk_galaxy/90.php

$pos = strpos($_SERVER['PHP_SELF'], "/90.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

// Dynamic functions
dynamic_loader ($db, "db_output.php");
dynamic_loader ($db, "newplayer.php");

$stamp = date("Y-m-d H:i:s");

// Commented out by tr0n to avoid the kabal/scheduler bug
/*
echo "The AI will play every $sched_turns minutes ";
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 0, 'sched_ai.php', '', ?)", array($sched_turns, $stamp));
echo db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);
*/

// v1 Master Server Lists
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(360, 'server_list_client.php', '', ?)", array($stamp));
$template->assign("l_server_list_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_igb.php', '', ?)", array($sched_igb, $stamp));
$template->assign("l_sched_igb_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Planets will generate production every $sched_planets minutes
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_planets.php', '', ?)", array($sched_planets, $stamp));
$template->assign("l_sched_planets_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Spies will act every $sched_spies minutes
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_spies.php', '', ?)", array($sched_spies, $stamp));
$template->assign("l_sched_spies_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Ports will regenerate every $sched_ports minutes
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_ports.php', '', ?)", array($sched_ports, $stamp));
$template->assign("l_sched_ports_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Rankings will be generated every $sched_ranking minutes
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_ranking.php', '', ?)", array($sched_ranking, $stamp));
$template->assign("l_sched_ranking_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Sector Defenses will degrade every $sched_degrade minutes
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_degrade.php', '', ?)", array($sched_degrade, $stamp));
$template->assign("l_sched_degrade_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// The planetary apocalypse will occur every $sched_apocalypse minutes
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_apocalypse.php', '', ?)", array($sched_apocalypse, $stamp));
$template->assign("l_sched_apoc_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Sched repair is no longer needed - now all tables are innodb, which dont have corruption issues like myisam tables do.

// The database log prune will occur every $sched_prune minutes
$debug_query = $db->Execute("INSERT INTO {$db->prefix}scheduler (timer, sched_file, extra_info, last_run) VALUES(?, 'sched_prune.php', '', ?)", array($sched_prune, $stamp));
$template->assign("l_sched_prune_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

if (!$ship_classes)
{
    $shiptypes = 1; // Hardcoded for now.

    $shiptype_names[0] = 'Starship';
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ship_types (type_id,".
                            "name, image, description, buyable, cost_ore, ".
                            "cost_goods, cost_energy, cost_organics, turnstobuild, minhull, ".
                            "maxhull, minengines, maxengines, minpengines, maxpengines, minpower, maxpower, mincomputer, ".
                            "maxcomputer, minsensors, maxsensors, minbeams, maxbeams, ".
                            "mintorp_launchers, maxtorp_launchers, minshields, maxshields, ".
                            "minarmor, maxarmor, mincloak, maxcloak) VALUES (" .
                            "1, " .              // type_id
                            "'Pioneer', " .      // name
                            "'pioneer.png', " .  // image
                            "'The Pioneer ship class is the standard ship issued by the Federation to new Starship Captains departing Earth.'," .
                            "'Y', " .            // buyable
                            "0, " .              // cost_ore
                            "0, " .              // cost_goods
                            "0, " .              // cost_energy
                            "0, " .              // cost_organics
                            "1, " .              // turnstobuild
                            "0, " .              // minhull
                            "100, " .             // maxhull
                            "0, " .              // minengines
                            "100, " .             // maxengines
                            "0, " .              // minpengines
                            "100, " .             // maxpengines
                            "0, " .              // minpower
                            "100, " .             // maxpower
                            "0, " .              // mincomputer
                            "100, " .             // maxcomputer
                            "0, " .              // minsensors
                            "100, " .             // maxsensors
                            "0, " .              // minbeams
                            "100, " .             // maxbeams
                            "0, " .              // mintorp_launchers
                            "100, " .             // maxtorp_launchers
                            "0, " .              // minshields
                            "100, " .             // maxshields
                            "0, " .              // minarmor
                            "100, " .             // maxarmor
                            "0, " .              // mincloak
                            "100  " .             // maxcloak
                            ")");
    $shiptype_results_array[0] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);
}
else
{
    $shiptypes = 4; // Hardcoded for now.

    $shiptype_names[0] = 'Pioneer';
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ship_types (type_id,".
                            "name, image, description, buyable, cost_ore, ".
                            "cost_goods, cost_energy, cost_organics, turnstobuild, minhull, ".
                            "maxhull, minengines, maxengines, minpengines, maxpengines, minpower, maxpower, mincomputer, ".
                            "maxcomputer, minsensors, maxsensors, minbeams, maxbeams, ".
                            "mintorp_launchers, maxtorp_launchers, minshields, maxshields, ".
                            "minarmor, maxarmor, mincloak, maxcloak) VALUES (" .
                            "1, " .              // type_id
                            "'Pioneer', " .      // name
                            "'pioneer.png', " .  // image
                            "'The Pioneer ship class is the standard ship issued by the Federation to new Starship Captains departing Earth.'," .
                            "'Y', " .            // buyable
                            "0, " .              // cost_ore
                            "0, " .              // cost_goods
                            "0, " .              // cost_energy
                            "0, " .              // cost_organics
                            "1, " .              // turnstobuild
                            "0, " .              // minhull
                            "20, " .             // maxhull
                            "0, " .              // minengines
                            "20, " .             // maxengines
                            "0, " .              // minpengines
                            "20, " .             // maxpengines
                            "0, " .              // minpower
                            "20, " .             // maxpower
                            "0, " .              // mincomputer
                            "20, " .             // maxcomputer
                            "0, " .              // minsensors
                            "20, " .             // maxsensors
                            "0, " .              // minbeams
                            "20, " .             // maxbeams
                            "0, " .              // mintorp_launchers
                            "20, " .             // maxtorp_launchers
                            "0, " .              // minshields
                            "20, " .             // maxshields
                            "0, " .              // minarmor
                            "20, " .             // maxarmor
                            "0, " .              // mincloak
                            "20  " .             // maxcloak
                            ")");
    $shiptype_results_array[0] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

    $shiptype_names[1] = 'Endeavour';
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ship_types (type_id,".
                            "name, image, description, buyable, cost_ore, ".
                            "cost_goods, cost_energy, cost_organics, turnstobuild, minhull, ".
                            "maxhull, minengines, maxengines, minpengines, maxpengines, minpower, maxpower, mincomputer, ".
                            "maxcomputer, minsensors, maxsensors, minbeams, maxbeams, ".
                            "mintorp_launchers, maxtorp_launchers, minshields, maxshields, ".
                            "minarmor, maxarmor, mincloak, maxcloak) VALUES (" .
                            "2, " .                // type_id
                            "'Endeavour', " .      // name
                            "'endeavour.png', " .  // image
                            "'The Endeavour ship class is a the next evolution above the pioneer. Faster, stronger, and much more robust. It does come with a serious pricetag however.'," .
                            "'Y', " .              // buyable
                            "0, " .                // cost_ore
                            "0, " .                // cost_goods
                            "0, " .                // cost_energy
                            "0, " .                // cost_organics
                            "250, " .              // turnstobuild
                            "21, " .               // minhull
                            "40, " .               // maxhull
                            "21, " .               // minengines
                            "40, " .               // maxengines
                            "21, " .               // minpengines
                            "40, " .               // maxpengines
                            "21, " .               // minpower
                            "40, " .               // maxpower
                            "21, " .               // mincomputer
                            "40, " .               // maxcomputer
                            "21, " .               // minsensors
                            "40, " .               // maxsensors
                            "21, " .               // minbeams
                            "40, " .               // maxbeams
                            "21, " .               // mintorp_launchers
                            "40, " .               // maxtorp_launchers
                            "21, " .               // minshields
                            "40, " .               // maxshields
                            "21, " .               // minarmor
                            "40, " .               // maxarmor
                            "21, " .               // mincloak
                            "40  " .               // maxcloak
                            ")");
    $shiptype_results_array[1] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

    $shiptype_names[2] = 'Phobos';
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ship_types (type_id,".
                            "name, image, description, buyable, cost_ore, ".
                            "cost_goods, cost_energy, cost_organics, turnstobuild, minhull, ".
                            "maxhull, minengines, maxengines, minpengines, maxpengines, minpower, maxpower, mincomputer, ".
                            "maxcomputer, minsensors, maxsensors, minbeams, maxbeams, ".
                            "mintorp_launchers, maxtorp_launchers, minshields, maxshields, ".
                            "minarmor, maxarmor, mincloak, maxcloak) VALUES (" .
                            "3, " .               // type_id
                            "'Phobos', " .      // name
                            "'phobos.png', " .  // image
                            "'The Phobos ship class is highly manueverable, yet solidly expandable. While its not as formidable as other classes, it should never be underestimated.'," .
                            "'Y', " .             // buyable
                            "0, " .               // cost_ore
                            "0, " .               // cost_goods
                            "0, " .               // cost_energy
                            "0, " .               // cost_organics
                            "500, " .             // turnstobuild
                            "41, " .              // minhull
                            "60, " .              // maxhull
                            "41, " .              // minengines
                            "60, " .              // maxengines
                            "41, " .              // minpengines
                            "60, " .              // maxpengines
                            "41, " .              // minpower
                            "60, " .              // maxpower
                            "41, " .              // mincomputer
                            "60, " .              // maxcomputer
                            "41, " .              // minsensors
                            "60, " .              // maxsensors
                            "41, " .              // minbeams
                            "60, " .              // maxbeams
                            "41, " .              // mintorp_launchers
                            "60, " .              // maxtorp_launchers
                            "41, " .              // minshields
                            "60, " .              // maxshields
                            "41, " .              // minarmor
                            "60, " .              // maxarmor
                            "41, " .              // mincloak
                            "60  " .              // maxcloak
                            ")");
    $shiptype_results_array[2] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

    $shiptype_names[3] = 'Columbus';
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ship_types (type_id,".
                            "name, image, description, buyable, cost_ore, ".
                            "cost_goods, cost_energy, cost_organics, turnstobuild, minhull, ".
                            "maxhull, minengines, maxengines, minpengines, maxpengines, minpower, maxpower, mincomputer, ".
                            "maxcomputer, minsensors, maxsensors, minbeams, maxbeams, ".
                            "mintorp_launchers, maxtorp_launchers, minshields, maxshields, ".
                            "minarmor, maxarmor, mincloak, maxcloak) VALUES (" .
                            "4, " .               // type_id
                            "'Columbus', " .      // name
                            "'columbus.png', " .  // image
                            "'The Columbus ship class is impressive. A solid construction makes this ship one of the most powerful devices able to be wielded by a single entity.'," .
                            "'Y', " .             // buyable
                            "0, " .               // cost_ore
                            "0, " .               // cost_goods
                            "0, " .               // cost_energy
                            "0, " .               // cost_organics
                            "500, " .             // turnstobuild
                            "61, " .              // minhull
                            "80, " .              // maxhull
                            "61, " .              // minengines
                            "80, " .              // maxengines
                            "61, " .              // minpengines
                            "80, " .              // maxpengines
                            "61, " .              // minpower
                            "80, " .              // maxpower
                            "61, " .              // mincomputer
                            "80, " .              // maxcomputer
                            "61, " .              // minsensors
                            "80, " .              // maxsensors
                            "61, " .              // minbeams
                            "80, " .              // maxbeams
                            "61, " .              // mintorp_launchers
                            "80, " .              // maxtorp_launchers
                            "61, " .              // minshields
                            "80, " .              // maxshields
                            "61, " .              // minarmor
                            "80, " .              // maxarmor
                            "61, " .              // mincloak
                            "80  " .              // maxcloak
                            ")");
    $shiptype_results_array[3] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

    $shiptype_names[4] = 'Razorback';
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ship_types (type_id,".
                            "name, image, description, buyable, cost_ore, ".
                            "cost_goods, cost_energy, cost_organics, turnstobuild, minhull, ".
                            "maxhull, minengines, maxengines, minpengines, maxpengines, minpower, maxpower, mincomputer, ".
                            "maxcomputer, minsensors, maxsensors, minbeams, maxbeams, ".
                            "mintorp_launchers, maxtorp_launchers, minshields, maxshields, ".
                            "minarmor, maxarmor, mincloak, maxcloak) VALUES (" .
                            "5, " .                // type_id
                            "'Razorback', " .      // name
                            "'razorback.png', " .  // image
                            "'The Razorback ship class is an enourmous, terrifying combination of size, weaponry, and specialized killing devices. All who see it tremble with fear.'," .
                            "'Y', " .              // buyable
                            "0, " .                // cost_ore
                            "0, " .                // cost_goods
                            "0, " .                // cost_energy
                            "0, " .                // cost_organics
                            "1000, " .             // turnstobuild
                            "81, " .               // minhull
                            "100, " .               // maxhull
                            "81, " .               // minengines
                            "100, " .               // maxengines
                            "81, " .               // minpengines
                            "100, " .               // maxpengines
                            "81, " .               // minpower
                            "100, " .               // maxpower
                            "81, " .               // mincomputer
                            "100, " .               // maxcomputer
                            "81, " .               // minsensors
                            "100, " .               // maxsensors
                            "81, " .               // minbeams
                            "100, " .               // maxbeams
                            "81, " .               // mintorp_launchers
                            "100, " .               // maxtorp_launchers
                            "81, " .               // minshields
                            "100, " .               // maxshields
                            "81, " .               // minarmor
                            "100, " .               // maxarmor
                            "81, " .               // mincloak
                            "100  " .               // maxcloak
                            ")");
    $shiptype_results_array[4] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);
}

$stamp = date("Y-m-d H:i:s");
$debug_query = $db->Execute("INSERT INTO {$db->prefix}news (news_data, user_id, date, news_type) VALUES ('', 1, ?, 'creation')", array($stamp));
$template->assign("l_news_created_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Allowing new account creation
$debug_query = $db->Execute("UPDATE {$db->prefix}config_values SET value='0' WHERE name='account_creation_closed'");
$template->assign("l_allow_newaccounts_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Allowing player logins
$debug_query = $db->Execute("UPDATE {$db->prefix}config_values SET value='0' WHERE name='server_closed'");
$template->assign("l_allow_logins_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Get a random string and push it through the md5 function
$c_code = md5(mt_rand(0,9999));

// Creates the confirmation code.        
$c_code = substr($c_code, 8, 6);

// Add Gamemaster account and set active
newplayer($db,$admin_mail, $_POST['admin_charname'], $adminpass, $c_code, $_POST['admin_charname'] . "&#39;s ship", "255"); // Admin's acl is 255.
$debug_query = $db->Execute("UPDATE {$raw_prefix}users SET active='Y' WHERE email=?", array($admin_mail));
$template->assign("l_add_gamemaster_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

// Automatically add Gamemaster to the invited list
$debug_query = $db->Execute("INSERT INTO {$db->prefix}memberlist (email) VALUES ('$admin_mail')");
$template->assign("l_add_gamemaster_invite_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

$template->assign("title", $title);
$template->assign("l_config_scheduler", $l_config_scheduler);

$l_updates_run = str_replace("[time]", $sched_ticks, $l_updates_run);
$template->assign("l_updates_run", $l_updates_run);

$l_turns_run = str_replace("[time]", $sched_turns, $l_turns_run);
$template->assign("l_turns_run", $l_turns_run);

$l_sched_igb_run = str_replace("[time]", $sched_igb, $l_sched_igb_run);
$template->assign("l_sched_igb_run", $l_sched_igb_run);

$l_sched_planets_run = str_replace("[time]", $sched_planets, $l_sched_planets_run);
$template->assign("l_sched_planets_run", $l_sched_planets_run);

$l_sched_spies_run = str_replace("[time]", $sched_spies, $l_sched_spies_run);
$template->assign("l_sched_spies_run", $l_sched_spies_run);

$l_sched_ports_run = str_replace("[time]", $sched_ports, $l_sched_ports_run);
$template->assign("l_sched_ports_run", $l_sched_ports_run);

$l_sched_ranking_run = str_replace("[time]", $sched_ranking, $l_sched_ranking_run);
$template->assign("l_sched_ranking_run", $l_sched_ranking_run);

$l_sched_degrade_run = str_replace("[time]", $sched_degrade, $l_sched_degrade_run);
$template->assign("l_sched_degrade_run", $l_sched_degrade_run);

$l_sched_apoc_run = str_replace("[time]", $sched_apocalypse, $l_sched_apoc_run);
$template->assign("l_sched_apoc_run", $l_sched_apoc_run);

$l_sched_prune_run = str_replace("[time]", $sched_prune, $l_sched_prune_run);
$template->assign("l_sched_prune_run", $l_sched_prune_run);

for ($i=0; $i<$shiptypes; $i++)
{
    $temp = str_replace("[shiptype]", $shiptype_names[$i], $l_insert_shiptypes);
    $l_shiptype_array[$i] = $temp;
}

$db->CacheFlush();
$template->assign("l_shiptype_array", $l_shiptype_array);
$template->assign("shiptype_results_array", $shiptype_results_array);
$template->assign("l_config_shiptypes", $l_config_shiptypes);
$template->assign("l_allow_newaccounts", $l_allow_newaccounts);
$template->assign("l_allow_logins", $l_allow_logins);
$template->assign("l_add_gamemaster", $l_add_gamemaster);
$template->assign("l_add_gamemaster_invite", $l_add_gamemaster_invite);
$template->assign("l_news_created", $l_news_created);
$template->assign("l_admin_login", $l_admin_login);
$template->assign("l_email", $l_email);
$template->assign("l_universe_success", $l_universe_success);
$template->assign("adminpass", $adminpass);
$template->assign("admin_mail", $admin_mail);
$template->display("$templateset/mk_galaxy/90.tpl");
       
?>
