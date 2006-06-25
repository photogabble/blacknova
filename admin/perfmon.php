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
// File: admin/perfmon.php

$pos = (strpos($_SERVER['PHP_SELF'], "/perfmon.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once ("./header.php");
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

//$title = 'Performance Monitor';
$perf =& NewPerfMonitor($db);

echo '<style type="text/css">';
echo '<!--  ';
echo 'TABLE            { background-color: #000;}';
echo '-->';
echo '</style>';

//echo $perf->HealthCheck(); // Not using this until adodb patches removing bgcolor=white are accepted
echo $perf->SuspiciousSQL(10);
echo $perf->ExpensiveSQL(10);
echo $perf->InvalidSQL(10);
// echo $perf->Tables(); // Not much use in this.

?>
