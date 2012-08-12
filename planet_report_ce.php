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
// File: planet_report_ce.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "go_build_base.php");
dynamic_loader ($db, "collect_credits.php");
dynamic_loader ($db, "change_planet_production.php");
dynamic_loader ($db, "take_credits.php");
dynamic_loader ($db, "real_space_move.php");

// Load language variables
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'move');
load_languages($db, $raw_prefix, 'planet_report');
load_languages($db, $raw_prefix, 'planets');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_pr_title;
updatecookie($db);

if (!isset($player_id))
{
    $player_id = '';
}

if (!isset($team_id))
{
    $team_id = '';
}

// Ensures that all parameters sent are numbers only - no negs,letters,punctuation,etc
foreach($_POST as $element=>$value)
{
    $_POST[$element] = preg_replace('/[^0-9]/','',$value);
}

$template->assign("title", $title);
$template->assign("l_pr_menulink", $l_pr_menulink);
$template->display("$templateset/planet_report_ce.tpl");

if (isset($_POST['TPCreds']))
{
    collect_credits($_POST['TPCreds']);
}
elseif (isset($buildp) AND isset($builds))
{
    go_build_base($buildp, $builds);
}
else
{
    change_planet_production($_POST);
}

include_once './footer.php';
?>
