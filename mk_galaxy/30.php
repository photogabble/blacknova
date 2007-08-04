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

$smarty->assign("autorun", $_POST['autorun']);
$smarty->assign("title", $title);
$smarty->assign("l_planet_setup",$l_planet_setup);
$smarty->assign("l_suggested_value",$l_suggested_value);
$smarty->assign("l_safe_range",$l_safe_range);
$smarty->assign("l_percent_ship",$l_percent_ship);
$smarty->assign("l_percent_upgrade",$l_percent_upgrade);
$smarty->assign("l_percent_device",$l_percent_device);
$smarty->assign("l_percent_ore",$l_percent_ore);
$smarty->assign("l_percent_organics",$l_percent_organics);
$smarty->assign("l_percent_goods",$l_percent_goods);
$smarty->assign("l_percent_energy",$l_percent_energy);
$smarty->assign("l_initscommod",$l_initscommod);
$smarty->assign("l_initbcommod",$l_initbcommod);
$smarty->assign("l_percent_of_max",$l_percent_of_max);
$smarty->assign("l_sector_link_setup",$l_sector_link_setup);
$smarty->assign("l_num_sectors",$l_num_sectors);
$smarty->assign("l_num_fedsecs",$l_num_fedsecs);
$smarty->assign("l_avg_links_per",$l_avg_links_per);
$smarty->assign("l_two_way_secs",$l_two_way_secs);
$smarty->assign("l_unowned_secs",$l_unowned_secs);
$smarty->assign("sector_max", $sector_max);
$smarty->assign("ship_classes", $ship_classes);
$smarty->assign("fedsecs", $fedsecs);
$smarty->assign("encrypted_password", $_POST['encrypted_password']);
$smarty->assign("l_store_warning", $l_store_warning);
$smarty->assign("l_store_success", $l_store_success);
$smarty->assign("cumulative", $cumulative);
$smarty->assign("l_continue", $l_continue);
$smarty->assign("l_reset", $l_reset);
$smarty->assign("step", ($_POST['step']+1));
$smarty->assign("admin_charname", $_POST['admin_charname']);
$smarty->assign("gamenum", $_POST['gamenum']);
$smarty->display("$templateset/mk_galaxy/30.tpl");

?>
