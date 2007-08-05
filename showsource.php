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
// File: showsource.php

include_once ("./global_includes.php");

// Load language variables
load_languages($db, $raw_prefix, 'global_includes');

$no_body = 1;
include_once ("./header.php");

if (!isset($_GET['file']))
{
    $_GET['file'] = '';
}

dynamic_loader ($db, "syntax_highlighter.php");

$pos = strpos($_GET['file'], 'config');
$pos2 = strpos($_GET['file'], '..');
if (($pos === false) && ($pos2 === false))
{
    $file_title = basename($_GET['file']);
    $title = "Show sourcecode for " . $file_title;

    $HL = new highlighter();
    $HL->set_code(file_get_contents($_GET['file']));
    $output = $HL->process();
    $output = str_replace("<br />", "<br>\n", $output);
    $output = str_replace("</span>", "</span>\n", $output);
}
else
{
    $title = "An error occured";
    $output = "Illegal target entered.";
}

$template->assign("title", $title);
$template->assign("output", $output);
$template->display("$templateset/showsource.tpl");
include_once ("./footer.php");
?>
