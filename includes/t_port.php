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
// File: includes/t_port.php

function t_port($db, $ptype)
{
    global $l_ore, $l_none, $l_energy, $l_organics, $langdir;
    global $l_goods, $l_upgrade_ports, $l_device_ports, $l_shipyard_title;
    global $raw_prefix;

    // Dynamic functions
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'shipyard');
    switch ($ptype)
    {
        case "none":
            $ret = $l_none;
            break;
        case "ore":
            $ret = $l_ore;
            break;
        case "energy":
            $ret = $l_energy;
            break;
        case "organics":
            $ret = $l_organics;
            break;
        case "goods":
            $ret = $l_goods;
            break;
        case "upgrades":
            $ret = $l_upgrade_ports;
            break;
        case "devices":
            $ret = $l_device_ports;
            break;
        case "shipyard":
            $ret = $l_shipyard_title;
            break;
    }
    return $ret;
}
?>
