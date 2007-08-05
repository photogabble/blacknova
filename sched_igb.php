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
// File: sched_igb.php

$igb_results = '';

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_igb.php"));
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

$exponinter = pow($ibank_interest + 1, $multiplier);
$expoloan = pow($ibank_loaninterest + 1, $multiplier);

$debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=balance * $exponinter, " .
                            "loan=loan * $expoloan");
db_op_result($db,$debug_query,__LINE__,__FILE__);

while (!$debug_query->EOF && $debug_query !='')
{
    $igb_results = $debug_query;
}

$template->assign("multiplier", $multiplier);
$template->assign("igb_results", $igb_results);
$template->display("$templateset/sched_igb.tpl");
$multiplier = 0;
?>
