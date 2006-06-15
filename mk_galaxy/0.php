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
// File: mk_galaxy/0.php

$pos = strpos($_SERVER['PHP_SELF'], "/0.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

// Template Lite
$template = new Template_Lite;

$l_welcome_make = str_replace("[project]", $l_project_name, $l_welcome_make);
$template->assign("l_welcome_make",$l_welcome_make);
$template->assign("l_welcome_assist",$l_welcome_assist);
$template->assign("l_welcome_defaults",$l_welcome_defaults);
$template->assign("l_welcome_limits",$l_welcome_limits);
$template->assign("l_welcome_safe",$l_welcome_safe);
$template->assign("l_welcome_badidea",$l_welcome_badidea);
$template->assign("l_welcome_nosupport",$l_welcome_nosupport);
$template->assign("l_admin_password",$l_admin_password);
$template->assign("l_readmin_password",$l_readmin_password);
$template->assign("templateset", $templateset);
$template->assign("title", $title);
$template->assign("l_submit", $l_submit);
$template->assign("l_reset", $l_reset);
$template->assign("badpass", $badpass);
$template->display("$templateset/mk_galaxy/0.tpl");
$filezero = 1;
?>
