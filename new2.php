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
// File: new.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'new');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_terms;
updatecookie($db);

$debug_query = $db->Execute("SELECT value FROM {$db->prefix}config_values WHERE name='new_rules'");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$additional_rules = $debug_query->fields['value'];

global $l_global_mmenu;
$template->assign("title", $title);
$template->assign("l_tos", $l_tos);
$template->assign("additional_rules", $additional_rules);
$template->assign("email", $_POST['email']);
$template->assign("shipname", $_POST['shipname']);
$template->assign("character", $_POST['character']);
$template->assign("password", $_POST['password']);
$template->assign("l_agree", $l_agree);
$template->assign("l_reset", $l_reset);
$template->assign("l_reset", $l_global_mmenu);
$template->display("$templateset/new2.tpl");

include_once ("./footer.php");
?> 
