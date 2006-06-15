<?php
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
