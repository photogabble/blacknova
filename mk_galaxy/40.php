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
// File: mk_galaxy/40.php

$pos = strpos($_SERVER['PHP_SELF'], "/40.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

if ($_POST['fedsecs'] > $_POST['sektors']) 
{
    echo "The number of Federation sectors must be smaller than the size of the universe!";
    echo "We have lowered it to half the total number of sectors.";
    $_POST['fedsecs'] = round($_POST['sektors'] / 2);
}

if ($_POST['linksper'] > $link_max)
{
    echo "The average number of links per sector must not be more than the max number of links per sector!<br>";
    echo "We have lowered it to the max number of links per sector.";
    $_POST['linksper'] = $link_max;
}

$shp = round($_POST['sektors'] * $_POST['shipyards']/100);
$upp = round($_POST['sektors'] * $_POST['devices']/100);
$spp = round($_POST['sektors'] * $_POST['upgrades']/100);
$oep = round($_POST['sektors'] * $_POST['ore']/100);
$ogp = round($_POST['sektors'] * $_POST['organics']/100);
$gop = round($_POST['sektors'] * $_POST['goods']/100);
$enp = round($_POST['sektors'] * $_POST['energy']/100);
$empty = $_POST['sektors']-$shp-$spp-$oep-$ogp-$gop-$enp;
$nump = round($_POST['sektors'] * $_POST['planets']/100);
// We now have true density, so a variable galaxy size isn't realistic anymore. We simply set the galaxy size to 1/10 the number of sectors.
$galaxy_size = round($_POST['sektors']/10);

$l_galaxy_size_change = str_replace("[size]", $galaxy_size, $l_galaxy_size_bigger);

$l_like_to_have = str_replace("[sektors]", $_POST['sektors'], $l_like_to_have);
$l_total_links = str_replace("[total_links]", ($_POST['linksper'] * $_POST['sektors']), $l_total_links);
$l_total_twoways = str_replace("[total_twoways]", round(($_POST['twoways']/100) * $_POST['sektors']), $l_total_twoways);
$l_total_oneways = str_replace("[total_oneways]", round(((100-$_POST['twoways'])/100) * $_POST['sektors']), $l_total_oneways);

$smarty->assign("autorun", $_POST['autorun']);
$smarty->assign("title", $title);
$smarty->assign("galaxy_size", $galaxy_size);
$smarty->assign("l_galaxy_size_change", $l_galaxy_size_change);
$smarty->assign("l_two_way_secs", $l_two_way_secs);
$smarty->assign("l_unowned_secs", $l_unowned_secs);
$smarty->assign("l_shipyards", $l_shipyards);
$smarty->assign("l_upgrade_ports", $l_upgrade_ports);
$smarty->assign("l_ore_ports", $l_ore_ports);
$smarty->assign("l_organics_ports", $l_organics_ports);
$smarty->assign("l_goods_ports", $l_goods_ports);
$smarty->assign("l_energy_ports", $l_energy_ports);
$smarty->assign("l_empty_sectors", $l_empty_sectors);
$smarty->assign("l_unowned_planets", $l_unowned_planets);
$smarty->assign("l_fed_sectors", $l_fed_sectors);
$smarty->assign("l_total_links", $l_total_links);
$smarty->assign("l_total_twoways", $l_total_twoways);
$smarty->assign("l_total_oneways", $l_total_oneways);
$smarty->assign("l_like_to_have", $l_like_to_have);
$smarty->assign("l_initscommod", $l_initscommod);
$smarty->assign("l_initbcommod", $l_initbcommod);
$smarty->assign("sektors", $_POST['sektors']);
$smarty->assign("initscommod", $_POST['initscommod']);
$smarty->assign("initbcommod", $_POST['initbcommod']);
$smarty->assign("empty", $empty);
$smarty->assign("fedsecs", $_POST['fedsecs']);
$smarty->assign("nump", $nump);
$smarty->assign("shp", $shp);
$smarty->assign("upp", $upp);
$smarty->assign("spp", $spp);
$smarty->assign("oep", $oep);
$smarty->assign("ogp", $ogp);
$smarty->assign("gop", $gop);
$smarty->assign("enp", $enp);
$smarty->assign("initscommod", $_POST['initscommod']);
$smarty->assign("initbcommod", $_POST['initbcommod']);
$smarty->assign("linksper", $_POST['linksper']);
$smarty->assign("twoways", $_POST['twoways']);
$smarty->assign("fedsecs", $_POST['fedsecs']);
$smarty->assign("encrypted_password", $_POST['encrypted_password']);
$smarty->assign("l_continue", $l_continue);
$smarty->assign("step", ($_POST['step']+1));
$smarty->assign("admin_charname", $_POST['admin_charname']);
$smarty->assign("gamenum", $_POST['gamenum']);
$smarty->display("$templateset/mk_galaxy/40.tpl");

?>
