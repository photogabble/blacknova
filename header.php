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
// File: header.php

$pos = (strpos($_SERVER['PHP_SELF'], "/header.php"));
if ($pos !== false)
{
    include_once ("global_includes.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once ("footer.php");
    die();
}

// Dynamic functions
dynamic_loader ($db, "callback.php");

ob_start('callback');

if (isset($langdir))
{
    if (!isset($_POST['step']) || $_POST['step'] >2) // Prevent loading languages before db loads.
    {
        // Load language variables
        load_languages($db, $raw_prefix, 'common');
        global $l_header_title;
    }
}

global $templateset;

$indexpage = (strpos($_SERVER['PHP_SELF'], "/index.php"));

// Defaults for unset variables to avoid warnings
if (!isset($no_body))
{
    $no_body = '';
}

if (!isset($style_sheet_file))
{
    $style_sheet_file = "templates/$templateset/styles/style.css";
}

if (!isset($refreshurl))
{
    $refreshurl = '';
}

if (!isset($indexpage))
{
    $indexpage = '';
}

if (!isset($l_header_title))
{
    $l_header_title = '';
}

if (!isset($title))
{
    $title = '';
}

if (strlen($title) >= 1)
{
    $l_header_title = $title . " : " . $l_header_title;
}

if (!isset($local_lang))
{
    $local_lang = "en"; // On index.php, before install, this has to be set manually.
}

$smarty->assign("title", $l_header_title);
$smarty->assign("local_lang", $local_lang);
$smarty->assign("templateset", $templateset);
$smarty->assign("refreshurl", $refreshurl);
$smarty->assign("indexpage", $indexpage);
$smarty->assign("header_bg_color", "#000");
$smarty->assign("header_text_color", "#CCC");
$smarty->assign("header_link_color", "#00FF00");
$smarty->assign("header_alink_color", "#F00");
$smarty->assign("header_vlink_color", "#00FF00");
$smarty->assign("no_body", $no_body);
$smarty->assign("style_sheet_file", $style_sheet_file);
$smarty->display("$templateset/header.tpl");
?>
