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
// File: install/20.php

$safe_mode = @ini_get("safe_mode");

// Used for file integrity check
if (!function_exists("file_get_contents"))
{
    // Dynamic functions
    dynamic_loader ($db, "file_get_contents.php");
}

$reinstall = ($game_installed && $swordfish != $adminpass);

if ($reinstall)
{
    $showit = 0;
}

$output = '';
if ($showit == 1)     // Preparing values for the form
{
    $v[1]  = isset($ADODB_SESSION_DRIVER) ? $ADODB_SESSION_DRIVER : 'mysqlt';
    $v[2]  = isset($ADODB_SESSION_DB) ? $ADODB_SESSION_DB : '';
    $v[3]  = isset($ADODB_SESSION_USER) ? $ADODB_SESSION_USER : '';
    $v[4]  = isset($ADODB_SESSION_PWD) ? $ADODB_SESSION_PWD : '';
    $v[5]  = isset($ADODB_SESSION_CONNECT) ? $ADODB_SESSION_CONNECT : 'localhost';
    $v[6]  = isset($dbport) ? $dbport : '3306';
    $v[8]  = isset($raw_prefix) ? $raw_prefix : 'bnt_';
    $v[14] = isset($adminpass) ? $adminpass : '';

    if (isset($ADODB_CRYPT_KEY))
    {
        $v[17] = $ADODB_CRYPT_KEY;
    }
    else
    {
        $mykey='';
        mt_srand((double)microtime()*1000000);
        for ($i=0; $i<16; $i++)
        {
            $mykey .= chr(mt_rand(97,122));
        }
        $v[17] = $mykey;
    }

    $v[18] = isset($server_type) ? $server_type : 'http';

    foreach($dbs as $value => $name)
    {
        $output.= "<option value=$value " . ($v[1] == $value ? 'selected' : '') . ">$name</option>";
    }
}

$template->assign("title", $title);
$template->assign("output", $output);
$template->assign("v1", $v[1]);
$template->assign("v2", $v[2]);
$template->assign("v3", $v[3]);
$template->assign("v4", $v[4]);
$template->assign("v5", $v[5]);
$template->assign("v6", $v[6]);
$template->assign("v8", $v[8]);
$template->assign("v14", $v[14]);
$template->assign("v17", $v[17]);
$template->assign("v18", $v[18]);
$template->assign("showit", $showit);
$template->assign("reinstall", $reinstall);
$template->assign("safe_mode", $safe_mode);
$template->assign("l_continue", $l_continue);
$template->assign("step", ($_POST['step']+1));
$template->display("$templateset/install/20.tpl");

?>
