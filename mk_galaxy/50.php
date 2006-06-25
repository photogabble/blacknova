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
// File: mk_galaxy/50.php

$pos = strpos($_SERVER['PHP_SELF'], "/50.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

// Dynamic functions
dynamic_loader ($db, "db_output.php");
dynamic_loader ($db, "sector_todb.php");

$cumulative = 0;
$sectors_built = 0;
$debug_query = $db->Execute("UPDATE {$db->prefix}config_values SET value=? WHERE name='sector_max'", array($_POST['sektors']));
$template->assign("set_sector_result", db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__));

$empty = $_POST['sektors']-$_POST['shp']-$_POST['spp']-$_POST['oep']-$_POST['ogp']-$_POST['gop']-$_POST['enp'];
if ($empty < 0)
{
    // This needs to be much nicer. Just a placeholder for now.
    echo "Error - number of ports needs to be less than the number of sectors";
    die();
}

// Build the zones table. Only four zones here. The rest are named after players for
// when they manage to dominate a sector.

$zone_names = array();
$zone_results_array = array();
$zone_names[0] = $l_unclaimed_space;
$debug_query = $db->Execute("INSERT INTO {$db->prefix}zones (zone_name, ".
                            "owner, team_zone, allow_attack, ".
                            "allow_planetattack, allow_warpedit, ".
                            "allow_planet, allow_trade, allow_defenses, ".
                            "max_level) VALUES(" .
                            "'Unchartered Space'," .      // zone_name
                            "'0'," .               // owner
                            "'N'," .               // team_zone
                            "'Y'," .               // allow_attack
                            "'Y'," .               // allow_planetattack
                            "'Y'," .               // allow_warpedit
                            "'Y'," .               // allow_planet
                            "'Y'," .               // allow_trade
                            "'Y'," .               // allow_defenses
                            "0" .                  // max_level
                            ")");
$zone_results_array[0] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$l_good_space = str_replace("[goodguy]", $goodguys_name, $l_good_space);
$zone_names[1] = $l_good_space;
$debug_query = $db->Execute("INSERT INTO {$db->prefix}zones (zone_name, ".
                            "owner, team_zone, allow_attack, ".
                            "allow_planetattack, allow_warpedit, ".
                            "allow_planet, allow_trade, allow_defenses, ".
                            "max_level) VALUES(" .
                            "'Federation Space'," .      // zone_name
                            "'0'," .               // owner
                            "'N'," .               // team_zone
                            "'N'," .               // allow_attack
                            "'N'," .               // allow_planetattack
                            "'N'," .               // allow_warpedit
                            "'N'," .               // allow_planet
                            "'Y'," .               // allow_trade
                            "'N'," .               // allow_defenses
                            "?" .                  // max_level
                            ")", array($max_avg_combat_tech));
$zone_results_array[1] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$l_ai_space = str_replace("[ai]", $ai_name, $l_ai_space);
$zone_names[2] = $l_ai_space;
$debug_query = $db->Execute("INSERT INTO {$db->prefix}zones (zone_name, ".
                            "owner, team_zone, allow_attack, ".
                            "allow_planetattack, allow_warpedit, ".
                            "allow_planet, allow_trade, allow_defenses, ".
                            "max_level) VALUES(" .
                            "?," .      // zone_name
                            "'0'," .               // owner
                            "'N'," .               // team_zone
                            "'Y'," .               // allow_attack
                            "'Y'," .               // allow_planetattack
                            "'Y'," .               // allow_warpedit
                            "'Y'," .               // allow_planet
                            "'Y'," .               // allow_trade
                            "'Y'," .               // allow_defenses
                            "0" .                  // max_level
                            ")", array($ai_name . 'Space'));
$zone_results_array[2] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

$zone_names[3] = $l_warzone_space;
$debug_query = $db->Execute("INSERT INTO {$db->prefix}zones (zone_name, ".
                            "owner, team_zone, allow_attack, ".
                            "allow_planetattack, allow_warpedit, ".
                            "allow_planet, allow_trade, allow_defenses, ".
                            "max_level) VALUES(" .
                            "'War Zone'," .      // zone_name
                            "'0'," .               // owner
                            "'N'," .               // team_zone
                            "'Y'," .               // allow_attack
                            "'Y'," .               // allow_planetattack
                            "'Y'," .               // allow_warpedit
                            "'Y'," .               // allow_planet
                            "'Y'," .               // allow_trade
                            "'Y'," .               // allow_defenses
                            "0" .                  // max_level
                            ")");
$zone_results_array[3] = db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

// Begin building unique sectors
$sector_names = array();
$sector_results_array = array();

if ($no_stars)
{
    $star_size = $max_star_size;
}
else
{
    $star_size = 1;
}

// Build Sector 0, Sol
$sector = array();
$sector_names[0] = $l_sector_zero_name;
$sector = array('sector_id' => '1',
                'sector_name' => $l_sector_zero_name,
                'zone_id' => '2',
                'star_size' => $star_size,
                'beacon' => 'Sol: Hub of the Universe',
                'x' => '0',
                'y' => '0',
                'z' => '0');
$sector_results_array[0] = db_output($db,sector_todb($db, $sector,"Insert",1),__LINE__,__FILE__);
$sectors_built++;

if ($no_stars)
{
    $star_size = $max_star_size;
}
else
{
    $star_size = 1;
}

$sector_names[1] = $l_sector_one_name;
// Build Sector 2, Proxima Centauri
$sector = array('sector_id' => '2',
                'sector_name' => $l_sector_one_name,
                'zone_id' => '2',
                'star_size' => $star_size,
                'beacon' => 'Proxima Centari: Gateway to the Galaxy',
                'x' => '0',
                'y' => '0',
                'z' => '1');
$sector_results_array[1] = db_output($db,sector_todb($db, $sector,"Insert",2),__LINE__,__FILE__);
$sectors_built++;

if ($no_stars)
{
    $star_size = $max_star_size;
}
else
{
    $star_size = 1;
}

$sector_names[2] = $l_sector_two_name;
// Build Sector 3, Wolf-359
$sector = array('sector_id' => '3',
                'sector_name' => 'Wolf-359',
                'zone_id' => '2',
                'star_size' => $star_size,
                'beacon' => 'Wolf 359: A thriving hub of interstellar commerce',
                'x' => '0',
                'y' => '0',
                'z' => '2');
$sector_results_array[2] = db_output($db,sector_todb($db, $sector,"Insert",3),__LINE__,__FILE__);
$sectors_built++;
unset ($sector);

$query = ''; // Query stores the actual SQL for dumping sectors to the db.
unset($sector); // Sector of course is the sector template used above.
$insertquery = array(); // Insertquery is the array holding the 1,000 sector dump we will send to the db.
$s=0; // S is the counter that loops us through the 1,000 sectors at a time.


// This loop creates the raw sectors themselves - NOT the ports - that happens later in an update.
for ($i=4; $i<=$_POST['sektors']; $i++)
{
    $s++;
    $insertquery[$s] = "(" . $i . ",";                         // sector_id

    if ($no_stars)
    {
        $insertquery[$s] = $insertquery[$s] . $max_star_size . ",";  // star_size
    }
    else
    {
        $insertquery[$s] = $insertquery[$s] . mt_rand(0,$max_star_size) . ",";  // star_size
    }

    $radius = mt_rand(100,($galaxy_size/2)*100)/100;

    $temp_a = deg2rad(mt_rand(0,36000)/100-180);
    $temp_b = deg2rad(mt_rand(0,18000)/100-90);
    $temp_c = $radius*sin($temp_b);

    $xx = ($galaxy_size /2) + round(cos($temp_a)*$temp_c);
    $yy = ($galaxy_size /2) + round(sin($temp_a)*$temp_c);
    $zz = ($galaxy_size /2) + round($radius*cos($temp_a));

    $insertquery[$s] = $insertquery[$s] . "'" . $xx . "',";  // x-coordinate
    $insertquery[$s] = $insertquery[$s] . "'" . $yy . "',";  // y-coordinate
    $insertquery[$s] = $insertquery[$s] . "'" . $zz . "',";  // z-coordinate

    // The Federation owns the first series of sectors. Logical because they
    // probably numbered them as they were found.
    if ($i<$_POST['fedsecs'])
    {
        $insertquery[$s] = $insertquery[$s] . '2' . ")";                         // zone_id for Fed space is 2.
    }
    else
    {
        $insertquery[$s] = $insertquery[$s] . '1' . ")";                         // zone_id for Uncharted space is 1.
    }

    if ($ADODB_SESSION_DRIVER == 'postgres7')
    {
        // Postgres doesn't support bulk inserts (multiple inserts in a single call), so we just dump one at a time.
        $query = "INSERT into {$db->prefix}universe (sector_id, star_size, x, y, z, zone_id) VALUES " . $insertquery[$s];
    }
    else
    {
        if  (( ($i % 1000)==0 || ($i==$_POST['sektors'])) && ($s>0) && ($i>0)) // If its a clean divisible by 2,000, OR, if its the end of the sector count AND loop(s) is higher than 0 AND sector number ($i) is greater than 1, THEN create a query.
        {
            $query = "INSERT into {$db->prefix}universe (sector_id, star_size, x, y, z, zone_id) VALUES ";

            $comma_added = implode(",", $insertquery);
            $query = $query . $comma_added;
            $s = 0;
        }
    }

    if ($query != '')
    {
        $debug_query = $db->Execute($query);
//        echo db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__); // can be used to debug
        $current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
        cumulative_error($cumulative, $current_status); // Delay "Success/Fail" notice
        unset ($query); // Cleanup afterwards
        $query = ""; // Need it to have a value - null errors out.
        unset ($debug_query); // Cleanup atferwards
    }
}

$template->assign("sector_build_result", db_output($db,!$cumulative,__LINE__,__FILE__));

$cumulative = 0;
$total_collisions = 0;
$collisions_repaired = false;

while (!$collisions_repaired)
{
    // Step 1:  Retrieve coordinates of collisions
    $debug_query = $db->GetAll("SELECT x, y, z, count(*) as num_collisions FROM {$db->prefix}universe GROUP BY x, y, z HAVING num_collisions > 1");

    $coll = $debug_query;
    $num_collisions = sizeof($coll);

    for ($xx=0; $xx < $num_collisions; $xx++)
    {
        if ($coll[$xx]['num_collisions'] <= 1)
        {
            $collisions_repaired = true;
        }
        else
        {
            $debug_query2 = $db->Execute("SELECT sector_id from {$db->prefix}universe where x=? and y=? and z=?", array($coll[$xx]['x'], $coll[$xx]['y'], $coll[$xx]['z']));
//            var_dump($debug_query);
            while (!$debug_query2->EOF)
            {
                $redo_sector = $debug_query2->fields;

                $radius = mt_rand(100,($galaxy_size/2)*100)/100;

                $temp_a = deg2rad(mt_rand(0,36000)/100-180);
                $temp_b = deg2rad(mt_rand(0,18000)/100-90);
                $temp_c = $radius*sin($temp_b);

                $xx = ($galaxy_size /2) + round(cos($temp_a)*$temp_c);
                $yy = ($galaxy_size /2) + round(sin($temp_a)*$temp_c);
                $zz = ($galaxy_size /2) + round($radius*cos($temp_a));

                $debug_query3 = $db->Execute("UPDATE {$db->prefix}universe SET x=?, y=?, z=? where sector_id=?", array($xx, $yy, $zz, $redo_sector['sector_id']));
                $current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
                cumulative_error($cumulative, $current_status); // Delay "Success/Fail" notice
                $total_collisions++;
                $debug_query2->MoveNext();
            }
        } 
    }
    $collisions_repaired = true;
}

$template->assign("collision_repair_result", db_output($db,!$cumulative,__LINE__,__FILE__));

$l_set_sector_max = str_replace("[sektors]", $_POST['sektors'], $l_set_sector_max);
$l_create_sectors = str_replace("[number]", ($_POST['sektors']-$sectors_built), $l_create_sectors);
$l_repair_collisions = str_replace("[number]", $total_collisions, $l_repair_collisions);
$num_zones = 4; // Hardcoded for now.
$num_special_sectors = 3; // Hardcoded for now.

for ($i=0; $i<$num_zones; $i++)
{
    $temp = str_replace("[number]", ($i+1), $l_set_zone);
    $temp = str_replace("[name]", $zone_names[$i], $temp);
    $l_zone_array[$i] = $temp;
}

$i=0;
for ($i=0; $i<$num_special_sectors; $i++)
{
    $temp = str_replace("[number]", ($i+1), $l_build_sector);
    $temp = str_replace("[name]", $sector_names[$i], $temp);
    $l_sector_array[$i] = $temp;
}

$template->assign("sector_results_array", $sector_results_array);
$template->assign("zone_results_array", $zone_results_array);
$template->assign("autorun", $_POST['autorun']);
$template->assign("title", $title);
$template->assign("l_sector_array", $l_sector_array);
$template->assign("l_zone_array", $l_zone_array);
$template->assign("l_create_sectors", $l_create_sectors);
$template->assign("l_repair_collisions", $l_repair_collisions);
$template->assign("l_set_sector_max", $l_set_sector_max);
$template->assign("sektors", $_POST['sektors']);
$template->assign("initscommod", $_POST['initscommod']);
$template->assign("initbcommod", $_POST['initbcommod']);
$template->assign("empty", $empty);
$template->assign("nump", $_POST['nump']);
$template->assign("shp", $_POST['shp']);
$template->assign("upp", $_POST['upp']);
$template->assign("spp", $_POST['spp']);
$template->assign("oep", $_POST['oep']);
$template->assign("ogp", $_POST['ogp']);
$template->assign("gop", $_POST['gop']);
$template->assign("enp", $_POST['enp']);
$template->assign("initscommod", $_POST['initscommod']);
$template->assign("initbcommod", $_POST['initbcommod']);
$template->assign("linksper", $_POST['linksper']);
$template->assign("twoways", $_POST['twoways']);
$template->assign("encrypted_password", $_POST['encrypted_password']);
$template->assign("l_continue", $l_continue);
$template->assign("step", ($_POST['step']+1));
$template->assign("admin_charname", $_POST['admin_charname']);
$template->assign("gamenum", $_POST['gamenum']);
$template->display("$templateset/mk_galaxy/50.tpl");

// dynamic function
dynamic_loader ($db, "sector_todb.php");
?>
