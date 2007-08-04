<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_ports.php

$pos = (strpos($_SERVER['PHP_SELF'], "/sched_ports.php"));
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

// Dynamic functions
dynamic_loader ($db, "db_output.php");

// Easter egg comment - "Yes, but as a KNOWN scientist it was a bit surprising that the girl blinded ME with science"
$port_add_results = db_output($db,sql_port_grow(),__LINE__,__FILE__);

$multiplier = 0;

$smarty->assign("l_sched_ports_add", $l_sched_ports_add);
$smarty->assign("l_sched_ports_title", $l_sched_ports_title);
$smarty->assign("port_add_results", $port_add_results);
$smarty->display("$templateset/sched_ports.tpl");
?>
