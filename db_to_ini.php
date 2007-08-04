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
// File: includes/db_to_ini.php

include_once ("./global_includes.php");
include_once ("./header.php");

$debug_query = $db->Execute("SELECT DISTINCT category FROM {$raw_prefix}languages order by category asc");

$j = 1;
while (!$debug_query->EOF)
{
    $categories[$j] = $debug_query->fields['category'];
    $j++;
    $debug_query->MoveNext();
}

$inifile = fopen("languages/english.ini","w+");
for ($i=1; $i<count($categories); $i++)
{
    $line =  "[" . $categories[$i] . "]\n";
    $inires = fwrite($inifile,$line); //write the line to the file
    $debug_query = $db->Execute("SELECT * FROM {$raw_prefix}languages where category=? order by name asc", array($categories[$i]));
    while (!$debug_query->EOF)
    {
        $line = $debug_query->fields['name'] . " = \"" . addslashes($debug_query->fields['value']) . "\";\n";
        $inires = fwrite($inifile,$line); //write the line to the file
        $debug_query->MoveNext();
    }
}

fclose($inifile);
include_once ("./footer.php");
?>
