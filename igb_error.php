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
// File: igb_error.php

include_once ("./global_includes.php");

global $l_igb_igberrreport, $l_igb_back, $l_igb_logout;

$smarty->assign("title", $l_igb_igberrreport);
$smarty->assign("backlink", $backlink);
$smarty->assign("igb_errmsg", $igb_errmsg);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);
$smarty->assign("templateset", $templateset);
$smarty->display("$templateset/igb_error.tpl");

include_once ("footer.php");
die();
?>

