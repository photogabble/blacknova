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
// File: mk_galaxy/20.php

$pos = strpos($_SERVER['PHP_SELF'], "/20.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

// Dynamic functions
dynamic_loader ($db, "db_output.php");
dynamic_loader ($db, "ini_to_db.php");

// Drop all tables.

global $raw_prefix;
if (isset($_POST['gamenum']))
{
    $db->prefix = $raw_prefix . $_POST['gamenum']. "_";
}
elseif (isset($_SESSION['game']))
{
    $db->prefix = $_SESSION['game'];
}

global $db;
global $silent;
global $served_page;

$table_names = array();
$i = 0;
$cumulative = 0; // Clears the db error counter
if ((!isset($_POST['autorun'])) || ($_POST['autorun'] == ''))
{
    $_POST['autorun'] = '';
}

if ($_POST['autorun'] == "on")
{
    $_POST['autorun'] = TRUE;
}

// Delete all tables in the database

$drop_table_results = array();
$db->LogSQL(false); // You must turn back on the logging. :)

if (!isset($_POST['persist']))
{
    $_POST['persist'] = '';
}

$schema_files = getDirFiles("schema/");
foreach ($schema_files as $schema_filename)
{
    $persist_accounts = FALSE;
    $i++;
    $tablename = substr($schema_filename,0,-4);
    $perm = substr($schema_filename,0,4);
    if ($perm == 'perm' || $perm == 'root' || $perm == 'prot')
    {
        $tablename = substr($tablename,5);
    }

    // Drop tables - only non-perm/prot, and accounts IF persist accounts is not true
    if ($tablename == 'accounts')
    {
        if ($_POST['persist'] != 'on')
        {
            $drop_table_names[$i] = str_replace("[table]", $tablename, $l_drop_table);
            $debug_query = $db->Execute("DROP TABLE {$raw_prefix}$tablename");
            $results = '';
            $results = db_op_result($db, $debug_query,__LINE__,__FILE__);
            cumulative_error($cumulative, $results);
            $drop_table_results[$i] = db_output($db,$results,__LINE__,__FILE__);
        }
    }
    elseif ($perm != 'perm' && $perm != 'prot' || $ADODB_SESSION_DRIVER != "mysqlt") // files starting with "perm" and "prot" are NOT deleted across resets - allowing things like hall of fame.
    {
        $drop_table_names[$i] = str_replace("[table]", $tablename, $l_drop_table);
        if ($perm != 'root' && $perm != 'prot')
        {
            $debug_query = $db->Execute("DROP TABLE {$db->prefix}$tablename");
        }
        else
        {
            $debug_query = $db->Execute("DROP TABLE {$raw_prefix}$tablename");
        }

        $results = '';
        $results = db_op_result($db, $debug_query,__LINE__,__FILE__);
        cumulative_error($cumulative, $results);
        $drop_table_results[$i] = db_output($db,$results,__LINE__,__FILE__);
    }

    // Create the table
    $schema = new adoSchema($db);
    if ($perm == 'root' || $perm == 'prot')
    {
        $schema->setPrefix($raw_prefix); // Root and prot tables are cross-game, allowing communication between games.
    }
    else
    {
        $schema->setPrefix($db->prefix);
    }

    $db->debug=0;
    $create_table_names[$i] = str_replace("[table]", $tablename, $l_creating_table);
    $parsed_xml = $schema->ParseSchema("schema/" . $schema_filename);
    $result = $schema->ExecuteSchema($parsed_xml,TRUE); // AXMLS sets 2 for success, 0 if failed, and 1 for errors. Grr.

    if ($result == 0 || $result == 1)
    {
        $create_table_results[$i] = "Schema parse error or failure code (" . $result . ") in: " . $schema_filename;
    }
    else
    {
        $create_table_results[$i] = db_output($db,true,__LINE__,__FILE__);
    }

    if ($ADODB_SESSION_DRIVER == "mysqlt")
    {
        // From AATrade - need check for innodb support. how?
        // This is for detecting support for innodb - make it work
        // $result= $db->Execute("SHOW VARIABLES LIKE 'have_innodb'");
        // $stuff = $result->fields;
        // var_dump($stuff);

        if ($perm == 'root' || $perm == 'prot' || $perm == 'perm')
        {
            $alter = substr($schema_filename, strpos($schema_filename,"-")+1);
            $alter = substr($alter, 0, -4);
            $results = $db->Execute("ALTER TABLE {$raw_prefix}" . $alter . " TYPE=INNODB");
        }
        else
        {
            $alter = $schema_filename;
            $alter = substr($alter, 0, -4);
            $results = $db->Execute("ALTER TABLE {$db->prefix}" . $alter . " TYPE=INNODB");
        }
    }
}

$template->assign("final_drop_result", $cumulative);

// Even though this is already done in global_cleanups, you have to do it again here
adodb_perf::table("{$db->prefix}adodb_logsql");
$db->LogSQL(false); // You must turn back on the logging. :)

$debug_query = '';
$cumulative = 0; // Clears the db error counter

// End table droppage.

// Create the new schema.

// If you add/remove a table, don't forget to update the
// table name variables in the global_func file.

// Create database schema

$cumulative = 0; // Clears the db error counter

$i = 0;

// Special case for planets table - XML can't set variables (xml is non-dynamic) for the default production values, so we
// do it manually after the fact.
global $default_prod_ore;
global $default_prod_organics;
global $default_prod_goods;
global $default_prod_energy;
global $default_prod_fighters;
global $default_prod_torp;

$set_ore_prodrate = str_replace("[type]", $l_ore, $l_set_prodrate);
$debug_query = $db->Execute("ALTER TABLE {$db->prefix}planets ALTER COLUMN prod_ore SET DEFAULT '$default_prod_ore'");
$ore_prodrate_results = db_output($db, db_op_result($db, $debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$set_organics_prodrate = str_replace("[type]", $l_organics, $l_set_prodrate);
$debug_query = $db->Execute("ALTER TABLE {$db->prefix}planets ALTER COLUMN prod_organics SET DEFAULT '$default_prod_organics'");
$organics_prodrate_results = db_output($db, db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$set_goods_prodrate = str_replace("[type]", $l_goods, $l_set_prodrate);
$debug_query = $db->Execute("ALTER TABLE {$db->prefix}planets ALTER COLUMN prod_goods SET DEFAULT '$default_prod_goods'");
$goods_prodrate_results = db_output($db, db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$set_energy_prodrate = str_replace("[type]", $l_energy, $l_set_prodrate);
$debug_query = $db->Execute("ALTER TABLE {$db->prefix}planets ALTER COLUMN prod_energy SET DEFAULT '$default_prod_energy'");
$energy_prodrate_results = db_output($db, db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$set_fighters_prodrate = str_replace("[type]", $l_fighters, $l_set_prodrate);
$debug_query = $db->Execute("ALTER TABLE {$db->prefix}planets ALTER COLUMN prod_fighters SET DEFAULT '$default_prod_fighters'");
$fighters_prodrate_results = db_output($db, db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$set_torps_prodrate = str_replace("[type]", $l_torps, $l_set_prodrate);
$debug_query = $db->Execute("ALTER TABLE {$db->prefix}planets ALTER COLUMN prod_torp SET DEFAULT '$default_prod_torp'");
$torps_prodrate_results = db_output($db, db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

// Finished
$template->assign("tablecreate_result", $cumulative);

$debug_query = '';

$config_result = ini_to_db($db, "config/serverconfig.php", $raw_prefix . 'serverconfig', $l_store_configs); // Prot server configuration items
$instlang_result = ini_to_db($db, "config/languages.php", $raw_prefix . 'inst_languages', $l_store_languages); // Languages has to be in the raw_prefix context

// This needs to be recoded to support multiple languages.
$lang_result = ini_to_db($db, "languages/english.ini", $raw_prefix . 'languages', "testing"); // Languages has to be in the raw_prefix context

// This has to come after we've imported the languages above
$config_result = ini_to_db($db, "config/configset-" . $_POST['mode'] . ".php", $db->prefix . 'config_values', $l_store_configs);

$debug_query = $db->Replace("{$raw_prefix}instances", 
                            array('instance_id'=>"$_POST[gamenum]", 'gamenumber'=>"$_POST[gamenum]"),"instance_id", false);
//db_output($db,$debug_query,__LINE__,__FILE__);

$db->CacheFlush();

dynamic_loader ($db, "load_languages.php");

// Load language variables
load_languages($db, $raw_prefix, 'make_galaxy');

$template->assign("ore_prodrate_results", $ore_prodrate_results);
$template->assign("organics_prodrate_results", $organics_prodrate_results);
$template->assign("goods_prodrate_results", $goods_prodrate_results);
$template->assign("energy_prodrate_results", $energy_prodrate_results);
$template->assign("fighters_prodrate_results", $fighters_prodrate_results);
$template->assign("torps_prodrate_results", $torps_prodrate_results);
$template->assign("set_ore_prodrate", $set_ore_prodrate);
$template->assign("set_organics_prodrate", $set_organics_prodrate);
$template->assign("set_goods_prodrate", $set_goods_prodrate);
$template->assign("set_energy_prodrate", $set_energy_prodrate);
$template->assign("set_fighters_prodrate", $set_fighters_prodrate);
$template->assign("set_torps_prodrate", $set_torps_prodrate);
$template->assign("create_table_names", $create_table_names);
$template->assign("create_table_results", $create_table_results);
$template->assign("l_tablecreate", $l_tablecreate);
$template->assign("l_tablecreate_failure", $l_tablecreate_failure);
$template->assign("l_tablecreate_success", $l_tablecreate_success);
$template->assign("instlang_result", $instlang_result);
$template->assign("config_result", $config_result);
$template->assign("l_tabledrop_failure", $l_tabledrop_failure);
$template->assign("l_tabledrop_success", $l_tabledrop_success);
$template->assign("l_drop_all_tables", $l_drop_all_tables);
$template->assign("drop_table_names", $drop_table_names);
$template->assign("drop_table_results", $drop_table_results);
$template->assign("autorun", $_POST['autorun']);
$template->assign("title", $title);
$template->assign("l_store_languages", $l_store_languages);
$template->assign("l_store_configs", $l_store_configs);
$template->assign("l_store_values", $l_store_values);
$template->assign("l_store_complete", $l_store_complete);
$template->assign("encrypted_password", $_POST['encrypted_password']);
$template->assign("cumulative", $cumulative);
$template->assign("l_continue", $l_continue);
$template->assign("l_reset", $l_reset);
$template->assign("step", ($_POST['step']+1));
$template->assign("admin_charname", $_POST['admin_charname']);
$template->assign("gamenum", $_POST['gamenum']);
$template->display("$templateset/mk_galaxy/20.tpl");
?>
