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
// File: sched_turns.php

$turns_results1 = '';

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_turns.php"));
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

if (!isset($swordfish) || $swordfish != $adminpass)
{
    die("Script has not been called properly");
}

//$debug_query = $db->Execute("UPDATE {$db_prefix}players SET turns=LEAST(turns+round($sched_turns*$multiplier), $max_turns)");
//$debug_query = $db->Execute("UPDATE {$db_prefix}players SET turns=MIN(turns+round($sched_turns*$multiplier), $max_turns)");
sql_turns_update(); // See includes/dbtype-common.php

db_op_result($debug_query,__LINE__,__FILE__);

while (!$debug_query->EOF && $debug_query !='')
{
        $turns_results1 = $debug_query;
}

//$session_kill = time() - $session_time_out;
//$debug_query = $db->Execute("DELETE from {$db_prefix}sessions WHERE expiry > '$session_kill'");
//db_op_result($debug_query,__LINE__,__FILE__);

$smarty->assign("max_turns", $max_turns);
$smarty->assign("multiplier", $multiplier);
$smarty->assign("turns_results1", $turns_results1);
$smarty->display("$templateset/sched_turns.tpl");
$multiplier = 0;
?>
