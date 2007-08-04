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
// File: global_includes.php

ini_set("include_path","."); // This seems to be a problem on a few platforms, 
                             // so we manually set it to avoid those problems.

// Benchmarking - start before anything else.
include_once ("./includes/timer.php");
$BenchmarkTimer = new c_Timer;
$BenchmarkTimer->start(); // Start benchmarking immediately

// Allows dynamic drop-in mods
include_once ("./includes/dynamic_loader.php");

//$ADODB_CACHE_DIR = "../scratch_dir"; // TODO - This should be able to be set 
                                       // dynamically, like in install

// Adodb handles database abstraction. We also use clob sessions, so that pgsql is
// supported, and cryptsessions, so the session data itself is encrypted.
// For some reason, compression + crypt + clob causes problems on pgsql.
include_once ("./backends/adodb/adodb.inc.php");
include_once ("./backends/adodb/adodb-perf.inc.php");
include_once ("./backends/adodb/session/adodb-session-clob.php");
include_once ("./backends/adodb/session/adodb-cryptsession.php");
// include_once("./backends/adodb/session/adodb-compress-gzip.php");

// Add the db_op_result function so that all files in the game can have debugging.
include_once ("./includes/db_op_result.php");

// Smarty provides templating
//include_once ("./backends/smarty/libs/Smarty.class.php");
//include_once ("./includes/smarty.php");
include_once ("./backends/smarty/src/class.template.php");

// Because these files declare variables (in the global scope), they can't
// be called from inside a function.
include_once ("./config/db_config.php");
include_once ("./includes/fix_magic_quotes.php");
include_once ("./includes/input_filter.php");
include_once ("./includes/set_langdir.php");
include_once ("./includes/connectdb.php");

include_once ("global_cleanups.php");

// Soon to be removed - db-specific functions.
include_once ("includes/" . $ADODB_SESSION_DRIVER . "-common.php");
?>
