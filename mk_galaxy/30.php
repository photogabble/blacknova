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
// File: mk_galaxy/30.php

$pos = strpos($_SERVER['PHP_SELF'], "/30.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

$fedsecs = intval($sector_max / 200);

$template->assign("autorun", $_POST['autorun']);
$template->assign("title", $title);
$template->assign("l_planet_setup",$l_planet_setup);
$template->assign("l_suggested_value",$l_suggested_value);
$template->assign("l_safe_range",$l_safe_range);
$template->assign("l_percent_ship",$l_percent_ship);
$template->assign("l_percent_upgrade",$l_percent_upgrade);
$template->assign("l_percent_device",$l_percent_device);
$template->assign("l_percent_ore",$l_percent_ore);
$template->assign("l_percent_organics",$l_percent_organics);
$template->assign("l_percent_goods",$l_percent_goods);
$template->assign("l_percent_energy",$l_percent_energy);
$template->assign("l_initscommod",$l_initscommod);
$template->assign("l_initbcommod",$l_initbcommod);
$template->assign("l_percent_of_max",$l_percent_of_max);
$template->assign("l_sector_link_setup",$l_sector_link_setup);
$template->assign("l_num_sectors",$l_num_sectors);
$template->assign("l_num_fedsecs",$l_num_fedsecs);
$template->assign("l_avg_links_per",$l_avg_links_per);
$template->assign("l_two_way_secs",$l_two_way_secs);
$template->assign("l_unowned_secs",$l_unowned_secs);
$template->assign("sector_max", $sector_max);
$template->assign("ship_classes", $ship_classes);
$template->assign("fedsecs", $fedsecs);
$template->assign("encrypted_password", $_POST['encrypted_password']);
$template->assign("l_store_warning", $l_store_warning);
$template->assign("l_store_success", $l_store_success);
$template->assign("cumulative", $cumulative);
$template->assign("l_continue", $l_continue);
$template->assign("l_reset", $l_reset);
$template->assign("step", ($_POST['step']+1));
$template->assign("admin_charname", $_POST['admin_charname']);
$template->assign("gamenum", $_POST['gamenum']);
$template->display("$templateset/mk_galaxy/30.tpl");

?>
