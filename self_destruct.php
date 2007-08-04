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
// File: self_destruct.php

include_once ("./global_includes.php"); 

// dynamic includes
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "playerdeath.php"); 
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'self_destruct');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_die_title;
updatecookie($db);
include_once ("./header.php");

if (!isset($_GET['sure']))
{
    $sure ='';
}
else
{
    $sure = $_GET['sure'];
}

if ($sure == 2)
{
    playerdeath($db,$playerinfo['player_id'], "LOG_HARAKIRI");
}

$smarty->assign("title", $title);
$smarty->assign("sure", $sure);
$smarty->assign("l_yes", $l_yes);
$smarty->assign("l_die_rusure", $l_die_rusure);
$smarty->assign("l_die_check", $l_die_check);
$smarty->assign("l_die_what", $l_die_what);
$smarty->assign("l_die_nonono", $l_die_nonono);
$smarty->assign("l_die_goodbye", $l_die_goodbye);
$smarty->assign("l_die_exploit", $l_die_exploit);
$smarty->assign("l_die_count", $l_die_count);
$smarty->assign("l_die_vapor", $l_die_vapor);
$smarty->assign("l_global_mlogin", $l_global_mlogin);
$smarty->assign("l_global_mmenu", $l_global_mmenu);
$smarty->display("$templateset/self_destruct.tpl");

include_once ("./footer.php");
?>
