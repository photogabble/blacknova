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
// File: logout.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'logout');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'global_includes');

$title = $l_logout;
include_once './header.php';

// Set globals (BAD!)
global $local_number_dec_point, $local_number_thousands_sep;
checklogin($db);
get_info($db);
checkdead($db);

playerlog($db,$playerinfo['player_id'], "LOG_LOGOUT", $_SESSION['ip_address']);

$_SESSION = array();
setcookie("PHPSESSID","",0,"/");

session_destroy();

$current_score = number_format(gen_score($db,$playerinfo['player_id']),0,$local_number_dec_point, $local_number_thousands_sep);

$l_logout_text = str_replace("[name]",$playerinfo['character_name'],$l_logout_text);
$l_logout_text = $l_logout_text . "<br><br><a href=\"index.php\">" . $l_global_mlogin . "</a>";

$template->assign("title", $title);
$template->assign("l_logout_score", $l_logout_score);
$template->assign("current_score", $current_score);
$template->assign("l_logout_text", $l_logout_text);
$template->display("$templateset/logout.tpl");

include_once './footer.php';
?>
