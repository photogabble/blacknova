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
// File: install.php

$no_body = 1;
include_once ("./global_includes.php");

// Dynamic functions
dynamic_loader ($db, "ini_to_mem.php");
dynamic_loader ($db, "getdirfiles.php");

// Load language variables
ini_to_mem("languages/english.ini");

$install_files = getDirFiles("install/");
if ((!isset($_POST['step'])) || ($_POST['step'] ==''))
{
    $_POST['step'] = "0";
    $title = $l_install_title;
}
else
{
    $title = $l_install_title . " - Phase ". $_POST['step'] . " of " . (count($install_files)-1); // Step 0 isnt counted.
}

include_once ("./header.php");

if ((!isset($_POST['swordfish'])) || ($_POST['swordfish'] ==''))
{
    if (isset($_POST['_adminpass']))
    {
        $swordfish = $_POST['_adminpass'];
    }
    else
    {
        $swordfish = '';
    }
}
else
{
    $swordfish = $_POST['swordfish'];
}

if ((!isset($_POST['data'])) || ($_POST['data'] ==''))
{
    $_POST['data'] = "";
}

// DATABASE TYPES - None of these are available yet other than mysql. XMLSchema still has issues with anything else - bug filed, waiting for fix.
// $dbs = array('access' => 'Microsoft Access', 'ado' => 'ADO', ado_mssql => 'Microsoft SQL ADO', 'ibase' => 'Interbase 6 or earlier', 'mssql' => 'Microsoft SQL', 'mysqlt' => 'MySQL', 'oci8' => 'Oracle8/9', 'odbc' => 'generic ODBC database', odbc_mssql => 'Microsoft SQL ODBC, 'postgres' => 'PostgreSQL ver &lt; 7', 'postgres7' => 'PostgreSQL ver 7 and up', 'sybase' => 'SyBase');
$dbs = array('mysqlt' => 'MySQL');

$showit = 1;  // Show the config form?
$adminpass = '';
$game_installed = isset($raw_prefix);

if (isset($_POST['step']) && $_POST['step'] != '')
{
    $cleaned = preg_replace('/[^0-9]/','',stripslashes($_POST['step']));

    $filename = 'install/' . $install_files[$cleaned];
    if (file_exists($filename))
    {
        include_once ($filename);
    }
}
else
{
    include_once ("./install/0.php");
}

if ($_POST['step'] != '3')
{
    include_once ("./footer.php");
}

/*
if (($game_installed && $swordfish != $adminpass && $_POST['step'] !='1'))
{
    echo "<strong>Hacking attempt</strong>";
    include_once ("./footer.php");
    die();
}
*/
?>
