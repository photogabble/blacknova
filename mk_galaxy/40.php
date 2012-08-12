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

$template->assign("autorun", $_POST['autorun']);
$template->assign("title", $title);
$template->assign("galaxy_size", $galaxy_size);
$template->assign("l_galaxy_size_change", $l_galaxy_size_change);
$template->assign("l_two_way_secs", $l_two_way_secs);
$template->assign("l_unowned_secs", $l_unowned_secs);
$template->assign("l_shipyards", $l_shipyards);
$template->assign("l_upgrade_ports", $l_upgrade_ports);
$template->assign("l_ore_ports", $l_ore_ports);
$template->assign("l_organics_ports", $l_organics_ports);
$template->assign("l_goods_ports", $l_goods_ports);
$template->assign("l_energy_ports", $l_energy_ports);
$template->assign("l_empty_sectors", $l_empty_sectors);
$template->assign("l_unowned_planets", $l_unowned_planets);
$template->assign("l_fed_sectors", $l_fed_sectors);
$template->assign("l_total_links", $l_total_links);
$template->assign("l_total_twoways", $l_total_twoways);
$template->assign("l_total_oneways", $l_total_oneways);
$template->assign("l_like_to_have", $l_like_to_have);
$template->assign("l_initscommod", $l_initscommod);
$template->assign("l_initbcommod", $l_initbcommod);
$template->assign("sektors", $_POST['sektors']);
$template->assign("initscommod", $_POST['initscommod']);
$template->assign("initbcommod", $_POST['initbcommod']);
$template->assign("empty", $empty);
$template->assign("fedsecs", $_POST['fedsecs']);
$template->assign("nump", $nump);
$template->assign("shp", $shp);
$template->assign("upp", $upp);
$template->assign("spp", $spp);
$template->assign("oep", $oep);
$template->assign("ogp", $ogp);
$template->assign("gop", $gop);
$template->assign("enp", $enp);
$template->assign("initscommod", $_POST['initscommod']);
$template->assign("initbcommod", $_POST['initbcommod']);
$template->assign("linksper", $_POST['linksper']);
$template->assign("twoways", $_POST['twoways']);
$template->assign("fedsecs", $_POST['fedsecs']);
$template->assign("encrypted_password", $_POST['encrypted_password']);
$template->assign("l_continue", $l_continue);
$template->assign("step", ($_POST['step']+1));
$template->assign("admin_charname", $_POST['admin_charname']);
$template->assign("gamenum", $_POST['gamenum']);
$template->display("$templateset/mk_galaxy/40.tpl");

?>
