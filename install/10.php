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
// File: install/10.php

// Used for file integrity check
if (!function_exists("file_get_contents"))
{
    // Dynamic functions
    dynamic_loader ($db, "file_get_contents.php");
}

$md5list = file_get_contents("md5sum_list", $use_include_path = 0);

$listpart = explode("\n", $md5list);
$k = 1;
for ($i = 0; $i < count($listpart) ; $i++)
{
    $part = $listpart[$i];
    $md5word = explode("  ", $listpart[$i]);
    for ($j = 0; $j < count($md5word) ; $j++)
    {
        $kimble[$k] = $md5word[$j];
        $k++;
    }
}

$j = 0;
for ($temp = 1; $temp< $k-1; $temp=$temp+2)
{
    if (md5_file($kimble[$temp+1]) == $kimble[$temp])
    {
        $j++;
        $badlist[$j] = $kimble[$temp+1];
    }
}

$template->assign("title", $title);
$template->assign("badlist", $badlist);
$template->assign("testedcount",count($listpart));
$template->assign("errorchk",$j);
$template->assign("l_continue", $l_continue);
$template->assign("step", ($_POST['step']+1));
$template->display("templates/$templateset/install/10.tpl");
?>
