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
// File: mk_galaxy/60.php

$pos = strpos($_SERVER['PHP_SELF'], "/60.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

// Dynamic functions
dynamic_loader ($db, "db_output.php");
dynamic_loader ($db, "dump_ports_to_db.php");

$port_table = $db->prefix . "ports";

$empty = $_POST['sektors']-$_POST['shp']-$_POST['spp']-$_POST['oep']-$_POST['ogp']-$_POST['gop']-$_POST['enp'];
if ($empty < 0)
{
    // This needs to be much nicer. Just a placeholder for now.
    echo "Error - number of ports needs to be less than the number of sectors";
    die();
}

// Setup some need values for product amounts
$initsore = $ore_limit * $_POST['initscommod'] / 100.0;
$initbore = $ore_limit * $_POST['initbcommod'] / 100.0;
$initsgoods = $goods_limit * $_POST['initscommod'] / 100.0;
$initbgoods = $goods_limit * $_POST['initbcommod'] / 100.0;
$initsenergy = $energy_limit * $_POST['initscommod'] / 100.0;
$initbenergy = $energy_limit * $_POST['initbcommod'] / 100.0;
$initsorganics = $organics_limit * $_POST['initscommod'] / 100.0;
$initborganics = $organics_limit * $_POST['initbcommod'] / 100.0;

// This is where the ports begin
$_POST['spp']--; // Since Sol is an upgrade port, we must decrement the total count of upgrade ports to make.
$port_names[0] = $l_sector_zero_name;
$sector = array();
$sector = array('sector_id' => '1',
                'port_type' => 'upgrades');

// Adodb generates an update statement for pushing the array into the db.
$debug_query_insert  = $db->GetInsertSQL($port_table, $sector);
// db_op_result($db,$debug_query_insert,__LINE__,__FILE__);

// Now execute the generated query for insert/update
$debug_query = $db->Execute($debug_query_insert);
$special_ports_results_array[0] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

unset ($sector);

$_POST['upp']--; // Since Proxima is a devices port, we must decrement the total count of device ports to make.
$port_names[1] = $l_sector_one_name;
$sector = array();
$sector = array('sector_id' => '2',
                'port_type' => 'devices');

// Adodb generates an update statement for pushing the array into the db.
$debug_query_insert  = $db->GetInsertSQL($port_table, $sector);

// Now execute the generated query for insert/update
$debug_query = $db->Execute($debug_query_insert);
$special_ports_results_array[1] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

unset ($sector);

$_POST['enp']--; // Since Wolf-359 is an energy port, we must decrement the total count of energy ports to make.
$port_names[2] = $l_sector_two_name;
$sector = array();
$sector = array('sector_id' => '3',
                'port_type' => 'energy',
                'port_organics' => $initborganics,
                'port_ore' => $initbore,
                'port_goods' => $initbgoods,
                'port_energy' => $initsenergy);

// Adodb generates an update statement for pushing the array into the db.
$debug_query_insert  = $db->GetInsertSQL($port_table, $sector);
db_op_result($db,$debug_query_insert,__LINE__,__FILE__);

// Now execute the generated query for insert/update
$debug_query = $db->Execute($debug_query_insert);
$special_ports_results_array[2] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

unset ($sector);

$special_ports = 4; // Hardcoded for now
for ($i=0; ($i<$special_ports-1); $i++)
{
    $temp = str_replace("[name]", $port_names[$i], $l_gen_port);
    $l_special_ports_array[$i] = $temp;
}

// Here's where the remaining sectors get built

// Port generation code
$s = 0;

// This is cute - we tell it to get an array of empty sectors (they all are) other than the unique sectors, and randomize it.
$query = "SELECT sector_id FROM {$db->prefix}universe WHERE sector_id>3 ORDER BY " . $db->random;
$empty_sectors = $db->Execute($query);
db_op_result($db,$empty_sectors,__LINE__,__FILE__);

unset($insertquery); // Cleanup - this keeps memory usage low!
$insertquery = array(); // Must have a defined array, or you get warnings.

// Shipyard ports
if ($ship_classes)
{
    $cumulative = 0;

    $l_all_ports_array[0] = str_replace("[number]", ($_POST['shp']), $l_place_shipyards);
    while ($_POST['shp'] > 0) // While we still have more shipyards to make
    {
        if (!$empty_sectors->EOF) // Make sure the number of sectors hasn't run out.
        {
            $port_sector = $empty_sectors->fields;
            $_POST['shp']--;
            $insertquery[$s] = "(";                                                // Beginning of values
            $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
            $insertquery[$s] = $insertquery[$s] . "'shipyard'" . ",";              // port_type
            $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_organics
            $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_ore
            $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_goods
            $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_energy
            $insertquery[$s] = $insertquery[$s] . "'0'";                           // port_rating
            $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
            dump_ports_to_db($insertquery,$s,false);
            $empty_sectors->MoveNext();                                            // Move to the next empty sector
        }
    }

    if (isset($insertquery))
    {
        dump_ports_to_db($insertquery,$s,true);
    }

    $all_ports_results_array[0] = db_output($db,!$cumulative,__LINE__,__FILE__);
}
else
{
    $l_all_ports_array[0] = '';
    $all_ports_results_array[0] = '';
}


// Device ports
$cumulative = 0;
$l_all_ports_array[1] = str_replace("[number]", ($_POST['upp']), $l_place_devices);
while ($_POST['upp'] > 0)
{
    if (!$empty_sectors->EOF)
    {
        $port_sector = $empty_sectors->fields;
        $_POST['upp']--;
        $insertquery[$s] = "(";                                                // Beginning of values
        $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
        $insertquery[$s] = $insertquery[$s] . "'devices'" . ",";               // port_type
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_organics
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_ore
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_goods
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_energy
        $insertquery[$s] = $insertquery[$s] . "'0'";                           // port_rating
        $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
        dump_ports_to_db($insertquery,$s,false);
        $empty_sectors->MoveNext();                                            // Move to the next empty sector
    }
}

if (isset($insertquery))
{
    dump_ports_to_db($insertquery,$s,true);
}

$all_ports_results_array[1] = db_output($db,!$cumulative,__LINE__,__FILE__);

// Upgrade ports
$cumulative = 0;
$l_all_ports_array[2] = str_replace("[number]", ($_POST['spp']), $l_place_upgrades);
while ($_POST['spp'] > 0)
{
    if (!$empty_sectors->EOF)
    {
        $port_sector = $empty_sectors->fields;
        $_POST['spp']--;
        $insertquery[$s] = "(";                                                // Beginning of values
        $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
        $insertquery[$s] = $insertquery[$s] . "'upgrades'" . ",";              // port_type
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_organics
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_ore
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_goods
        $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_energy
        $insertquery[$s] = $insertquery[$s] . "'0'";                           // port_rating
        $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
        dump_ports_to_db($insertquery,$s,false);
        $empty_sectors->MoveNext();                                            // Move to the next empty sector
    }
}

if (isset($insertquery))
{
    dump_ports_to_db($insertquery,$s,true);
}

$all_ports_results_array[2] = db_output($db,!$cumulative,__LINE__,__FILE__);

// Ore ports
$cumulative = 0;
$l_all_ports_array[3] = str_replace("[number]", ($_POST['oep']), $l_place_ore);
while ($_POST['oep'] > 0)
{
    if (!$empty_sectors->EOF)
    {
        $port_sector = $empty_sectors->fields;
        $_POST['oep']--;
        $insertquery[$s] = "(";                                                // Beginning of values
        $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
        $insertquery[$s] = $insertquery[$s] . "'ore'" . ",";                   // port_type
        $insertquery[$s] = $insertquery[$s] . "'$initborganics'" . ",";        // port_organics
        $insertquery[$s] = $insertquery[$s] . "'$initsore'" . ",";             // port_ore
        $insertquery[$s] = $insertquery[$s] . "'$initbgoods'" . ",";           // port_goods
        $insertquery[$s] = $insertquery[$s] . "'$initbenergy'" . ",";          // port_energy
        $insertquery[$s] = $insertquery[$s] . "'0'";                           // port_rating
        $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
        dump_ports_to_db($insertquery,$s,false);
        $empty_sectors->MoveNext();                                            // Move to the next empty sector
    }
}

if (isset($insertquery))
{
    dump_ports_to_db($insertquery,$s,true);
}

$all_ports_results_array[3] = db_output($db,!$cumulative,__LINE__,__FILE__);

// Organics ports
$cumulative = 0;
$l_all_ports_array[4] = str_replace("[number]", ($_POST['ogp']), $l_place_organics);
while ($_POST['ogp'] > 0)
{
    if (!$empty_sectors->EOF)
    {
        $port_sector = $empty_sectors->fields;
        $_POST['ogp']--;
        $insertquery[$s] = "(";                                                // Beginning of values
        $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
        $insertquery[$s] = $insertquery[$s] . "'organics'" . ",";              // port_type
        $insertquery[$s] = $insertquery[$s] . "'$initsorganics'" . ",";        // port_organics
        $insertquery[$s] = $insertquery[$s] . "'$initbore'" . ",";             // port_ore
        $insertquery[$s] = $insertquery[$s] . "'$initbgoods'" . ",";           // port_goods
        $insertquery[$s] = $insertquery[$s] . "'$initbenergy'" . ",";          // port_energy
        $insertquery[$s] = $insertquery[$s] . "'0'";                           // port_rating
        $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
        dump_ports_to_db($insertquery,$s,false);
        $empty_sectors->MoveNext();                                            // Move to the next empty sector
    }
}

if (isset($insertquery))
{
    dump_ports_to_db($insertquery,$s,true);
}

$all_ports_results_array[4] = db_output($db,!$cumulative,__LINE__,__FILE__);

// Goods ports
$cumulative = 0;
$l_all_ports_array[5] = str_replace("[number]", ($_POST['gop']), $l_place_goods);
while ($_POST['gop'] > 0)
{
    if (!$empty_sectors->EOF)
    {
        $port_sector = $empty_sectors->fields;
        $_POST['gop']--;
        $insertquery[$s] = "(";                                                // Beginning of values
        $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
        $insertquery[$s] = $insertquery[$s] . "'goods'" . ",";                 // port_type
        $insertquery[$s] = $insertquery[$s] . "'$initborganics'" . ",";        // port_organics
        $insertquery[$s] = $insertquery[$s] . "'$initbore'" . ",";             // port_ore
        $insertquery[$s] = $insertquery[$s] . "'$initsgoods'" . ",";           // port_goods
        $insertquery[$s] = $insertquery[$s] . "'$initbenergy'". ",";           // port_energy
        $insertquery[$s] = $insertquery[$s] . "'0'";                           // port_rating
        $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
        dump_ports_to_db($insertquery,$s,false);
        $empty_sectors->MoveNext();                                            // Move to the next empty sector
    }
}

if (isset($insertquery))
{
    dump_ports_to_db($insertquery,$s,true);
}

$all_ports_results_array[5] = db_output($db,!$cumulative,__LINE__,__FILE__);

// Energy ports
$cumulative = 0;
$l_all_ports_array[6] = str_replace("[number]", ($_POST['enp']), $l_place_energy);
while ($_POST['enp'] > 0)
{
    if (!$empty_sectors->EOF)
    {
        $port_sector = $empty_sectors->fields;
        $_POST['enp']--;
        $insertquery[$s] = "(";                                                // Beginning of values
        $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
        $insertquery[$s] = $insertquery[$s] . "'energy'" . ",";                // port_type
        $insertquery[$s] = $insertquery[$s] . "'$initborganics'" . ",";        // port_organics
        $insertquery[$s] = $insertquery[$s] . "'$initbore'" . ",";             // port_ore
        $insertquery[$s] = $insertquery[$s] . "'$initbgoods'" . ",";           // port_goods
        $insertquery[$s] = $insertquery[$s] . "'$initsenergy'" . ",";          // port_energy
        $insertquery[$s] = $insertquery[$s] . "'0'";                           // port_rating
        $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
        dump_ports_to_db($insertquery,$s,false);
        $empty_sectors->MoveNext();                                            // Move to the next empty sector
    }
}

if (isset($insertquery))
{
    dump_ports_to_db($insertquery,$s,true);
}

$all_ports_results_array[6] = db_output($db,!$cumulative,__LINE__,__FILE__);

// Empty ports
$cumulative = 0;
$l_all_ports_array[7] = str_replace("[number]", ($empty), $l_place_empty);
while (!$empty_sectors->EOF)
{
    $port_sector = $empty_sectors->fields;
    $insertquery[$s] = "(";                                                // Beginning of values
    $insertquery[$s] = $insertquery[$s] . $port_sector['sector_id'] . ","; // Sector id
    $insertquery[$s] = $insertquery[$s] . "'none'" . ",";                  // port_type
    $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_organics
    $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_ore
    $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_goods
    $insertquery[$s] = $insertquery[$s] . "'0'" . ",";                      // port_energy
    $insertquery[$s] = $insertquery[$s] . "'0'";                            // port_rating
    $insertquery[$s] = $insertquery[$s] . ")";                             // End of values
    dump_ports_to_db($insertquery,$s,false);
    $empty_sectors->MoveNext();                                            // Move to the next empty sector
}

if (isset($insertquery))
{
    dump_ports_to_db($insertquery,$s,true);
}

$all_ports_results_array[7] = db_output($db,!$cumulative,__LINE__,__FILE__);

// build a form for the next stage
$smarty->assign("special_ports_results_array", $special_ports_results_array);
$smarty->assign("l_special_ports_array", $l_special_ports_array);
$smarty->assign("l_all_ports_array", $l_all_ports_array);
$smarty->assign("all_ports_results_array", $all_ports_results_array);
$smarty->assign("autorun", $_POST['autorun']);
$smarty->assign("title", $title);
$smarty->assign("l_create_sectors", $l_create_sectors);
$smarty->assign("l_repair_collisions", $l_repair_collisions);
$smarty->assign("l_set_sector_max", $l_set_sector_max);
$smarty->assign("sektors", $_POST['sektors']);
$smarty->assign("nump", $_POST['nump']);
$smarty->assign("linksper", $_POST['linksper']);
$smarty->assign("twoways", $_POST['twoways']);
$smarty->assign("encrypted_password", $_POST['encrypted_password']);
$smarty->assign("l_continue", $l_continue);
$smarty->assign("step", ($_POST['step']+1));
$smarty->assign("admin_charname", $_POST['admin_charname']);
$smarty->assign("gamenum", $_POST['gamenum']);
$smarty->display("$templateset/mk_galaxy/60.tpl");

// Dynamic functions
dynamic_loader ($db, "dump_ports_to_db.php");
?>
