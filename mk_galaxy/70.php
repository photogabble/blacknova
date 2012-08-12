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
// File: mk_galaxy/70.php

$pos = strpos($_SERVER['PHP_SELF'], "/70.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

// Dynamic functions
dynamic_loader ($db, "db_output.php");

$cumulative = 0;
$current_status = 0;
$query = '';
$total_planets = $_POST['nump'];

while ($_POST['nump'] >0)
{
    for ($i=0; (($i<2000) && ($_POST['nump'] > 0)); )
    {
        $random_sector = mt_rand(1, $_POST['sektors']);
        // Planetary sector must have an appropriate star size, the zone must allow planets, and it must be in an uncontrolled zone.
        // This can probably be rewritten to remove the select call each call, like we do in 50.php, but it scales fairly well so
        // far, so I see little reason to change it right now.

        $planetary_sector = $db->SelectLimit("SELECT {$db->prefix}universe.sector_id, {$db->prefix}universe.star_size, ".
                                             "{$db->prefix}universe.zone_id, {$db->prefix}zones.allow_planet ".
                                             "FROM {$db->prefix}universe, {$db->prefix}zones ".
                                             "WHERE {$db->prefix}universe.sector_id=? AND ".
                                             "{$db->prefix}zones.allow_planet='Y' AND {$db->prefix}universe.zone_id!='2' AND ".
                                             "{$db->prefix}universe.star_size!='0'",1,-1,array($random_sector));
        db_op_result($db,$planetary_sector,__LINE__,__FILE__);

        if (!$planetary_sector->EOF)
        {
            $debug_query = $db->Execute("SELECT * from {$db->prefix}planets where sector_id=?", array($random_sector));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $num_planets_in_sector = $debug_query->RecordCount();
            $num_ok_planets = $planetary_sector->fields['star_size'] - $num_planets_in_sector;

            if ($num_ok_planets > $_POST['nump'])
            {
                $num_ok_planets = $_POST['nump'];
            }

            if ($num_ok_planets > 0)
            {
                $random_num_planets = mt_rand(0, $num_ok_planets);

                while ($random_num_planets > 0)
                {
                    // Set the values for the fields in the record
                    $insertquery[$i] = "(";
                    $insertquery[$i] = $insertquery[$i] . "2" . ",";                     // Colonists
                    $insertquery[$i] = $insertquery[$i] . "0" . ",";                     // Owner
                    $insertquery[$i] = $insertquery[$i] . "0" . ",";                     // Team
                    $insertquery[$i] = $insertquery[$i] . "'Unnamed'" . ",";             // Name
                    $insertquery[$i] = $insertquery[$i] . $default_prod_ore . ",";       // Prod ore
                    $insertquery[$i] = $insertquery[$i] . $default_prod_organics . ",";  // Prod Organics
                    $insertquery[$i] = $insertquery[$i] . $default_prod_goods . ",";     // Prod Goods
                    $insertquery[$i] = $insertquery[$i] . $default_prod_energy . ",";    // Prod Energy
                    $insertquery[$i] = $insertquery[$i] . $default_prod_fighters . ",";  // Prod Fighters
                    $insertquery[$i] = $insertquery[$i] . $default_prod_torp . ",";      // Prod Torp
                    $insertquery[$i] = $insertquery[$i] . $random_sector . ")";          // Sector ID

                    // Pass the empty recordset and the array containing the data to insert
                    // into the GetInsertSQL function. The function will process the data and return
                    // a fully formatted insert sql statement.
                    $_POST['nump']--;
                    $i++;
                    $random_num_planets--;
                }
            }
        }
    }

    if ($ADODB_SESSION_DRIVER == 'postgres7')
    {
        // Postgres doesn't support bulk inserts (multiple inserts in a single call), so we just dump one at a time.
        $query = "INSERT into {$db->prefix}planets (colonists, owner, team, name, prod_ore, prod_organics, prod_goods" .
                 ",prod_energy, prod_fighters, prod_torp, sector_id) VALUES " . $insertquery[$i-1];
    }
    else
    {
        $query = "INSERT into {$db->prefix}planets (colonists, owner, team, name, prod_ore, prod_organics, prod_goods" .
                 ",prod_energy, prod_fighters, prod_torp, sector_id) VALUES ";

        for (; $i>0; $i--)
        {
            $query = $query . $insertquery[$i-1];
            if ($i != 1)
            {
                $query = $query . ",";
            }
        }
    }

    $debug_query = $db->Execute($query);
    $current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
    cumulative_error($cumulative, $current_status);
    $query = '';
}


$l_creating_planets = str_replace("[nump]", $total_planets, $l_creating_planets);
$l_creating_planets = $l_creating_planets . db_output($db,!$cumulative,__LINE__,__FILE__);
$template->assign("autorun", $_POST['autorun']);
$template->assign("title", $title);
$template->assign("l_creating_planets", $l_creating_planets);
$template->assign("linksper", $_POST['linksper']);
$template->assign("twoways", $_POST['twoways']);
$template->assign("encrypted_password", $_POST['encrypted_password']);
$template->assign("sektors", $_POST['sektors']);
$template->assign("l_continue", $l_continue);
$template->assign("step", ($_POST['step']+1));
$template->assign("admin_charname", $_POST['admin_charname']);
$template->assign("gamenum", $_POST['gamenum']);
$template->display("$templateset/mk_galaxy/70.tpl");

?>
