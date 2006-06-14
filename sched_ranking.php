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
// File: sched_ranking.php

// Dynamic functions
dynamic_loader ($db, "gen_score.php");

$cleanup_results = '';

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_ranking.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php"); 
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

$last_date = date_in_past($sched_ranking);

// Add "And last login > $sched_ranking minutes ago, so that active players who did their own updates don't have to be updated too.
$debug_query = $db->Execute("SELECT {$db->prefix}players.player_id FROM {$db->prefix}players LEFT JOIN " .
                            "{$db->prefix}ships ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id ".
                            "WHERE destroyed='N' AND acl != '0' AND last_login < '$last_date'"); // ACL = 0 means its an AI player.

while (!$debug_query->EOF && $debug_query)
{
    gen_score($db,$debug_query->fields['player_id']);
    $debug_query->MoveNext();
}

while (!$debug_query->EOF && $debug_query !='')
{
    $cleanup_results = $debug_query;
}

$multiplier = 0; // No use to run this again
$template->assign("cleanup_results", $cleanup_results);
$template->display("$templateset/sched_ranking.tpl");
?>
