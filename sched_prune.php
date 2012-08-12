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
// File: sched_repair.php

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_repair.php"));
if ($pos !== false)
{
    include_once './global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once './footer.php';
    die();
}

$in_past = time() - (192 * 3600); // Time gives time in seconds,
                                  // so multiply 8 days *
                                  // 24 hours * 60 minutes * 60 seconds.
$targetday = date("Y-m-d H:i:s", $in_past);

$debug_query = $db->Execute("DELETE FROM {$db->prefix}ip_log WHERE time < '$targetday'");
db_op_result($db,$debug_query,__LINE__,__FILE__);

$debug_query = $db->Execute("DELETE FROM {$db->prefix}adodb_logsql WHERE created < '$targetday'");
db_op_result($db,$debug_query,__LINE__,__FILE__);

$session_kill = time() - $session_time_out;
$debug_query = $db->Execute("DELETE from {$db->prefix}sessions WHERE expiry > '$session_kill'");
db_op_result($db,$debug_query,__LINE__,__FILE__);

$multiplier = 0;

$template->assign("turns_results1", $turns_results1);
$template->display("$templateset/sched_prune.tpl");
?>
