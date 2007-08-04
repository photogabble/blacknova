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
// File: make_galaxy.php

if (is_writable ("./config/db_config.php"))
{
    echo "You *must* set your db_config.php permissions right away!<br>";
    echo "Set it to '0404', or user = R, group = ---, other = R--<br>";
    echo "<font color=\"red\">If you do not, you risk having your server compromised!!</font><br><br>";
    die();
}

// XML Schema handler
if (!@include_once ("./backends/adodb/adodb-xmlschema.inc.php"))
{
    echo "adodb-xmlschema.inc.php ";
    echo "cannot be found, and it is required for BNT to run.";
    die();
}

// Easter egg comment - But you cant be any geek off the street. You gotta be handy with the steel if you know what I mean, earn your keep.

$default_lang = "english"; 
$default_template = "classic";
// Horrible hack. Truly. However, with the config file not chosen until 20.php, we need a language for the user. 
// Since the default lang is normally set in the config file, or in the db, and neither is available, we have to use SOMETHING.
// I dont see an elegant fix around this issue, yet.

if ((!isset($_POST['step'])) || ($_POST['step'] == 0))
{
    $_POST['step'] = 0;
}

$no_body = 1;

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "getdirfiles.php");
dynamic_loader ($db, "cumulative_error.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "ini_to_mem.php");

ini_to_mem("languages/english.ini");

// Include the sha256 backend
include_once ("./backends/sha256/shaclass.php");
include_once ("header.php");

// Set timelimit

$sf = (bool) ini_get('safe_mode');
if (!$sf)
{
    set_time_limit(0);
}

seed_mt_rand();

// Manually set step var if info isn't correct.

$badpass = ''; // Prevent variable injection.
if (!isset($_POST['encrypted_password']))
{
    $_POST['encrypted_password'] = '';
    $_POST['step'] = 0;
    $badpass = false;
}
elseif ($_POST['encrypted_password'] !== sha256::hash($adminpass))
{
    $_POST['step'] = 0;
    $badpass = true;
}
else
{
    $badpass = false;
}

$mk_galaxy_files = getDirFiles("mk_galaxy/");

if ((!isset($_POST['step'])) || ($_POST['step'] == 0))
{
    $title = $l_make_galaxy;
}
else
{
    $title = $l_make_galaxy . " - Phase ". $_POST['step'] . " of " . (count($mk_galaxy_files)-1); // Step 0 isnt counted.
}

if ($_POST['step'] > 2)
{
    // Load language variables
    load_languages($db, $raw_prefix, 'common');
    load_languages($db, $raw_prefix, 'make_galaxy');
}

// Print Title on Page.

if (isset($_POST['step']) && $_POST['step'] != '')
{
    $cleaned = preg_replace('/[^0-9]/','',stripslashes($_POST['step']));

    $filename = 'mk_galaxy/' . $mk_galaxy_files[$cleaned];
    if (file_exists($filename))
    {
        include_once ($filename);
    }
}
else
{
    include_once ("./mk_galaxy/0.php");
}

include_once ("./footer.php");
?>
