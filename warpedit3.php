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
// File: warpedit3.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'warpedit3');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_warp_title;
updatecookie($db);

if (!isset($target_sector))
{
    $target_sector = '';
}

if (!isset($flag))
{
    $flag = '';
}

if ($playerinfo['turns'] < 1)
{
    echo "$l_warp_turn<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if ($shipinfo['dev_warpedit'] < 1)
{
    echo "$l_warp_none<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$res = $db->Execute("SELECT allow_warpedit,{$db->prefix}universe.zone_id FROM {$db->prefix}zones,{$db->prefix}universe WHERE " .
                    "sector_id=$shipinfo[sector_id] AND {$db->prefix}universe.zone_id={$db->prefix}zones.zone_id");
$query97 = $res->fields;
if ($query97['allow_warpedit'] == 'N')
{
    echo "$l_warp_forbid<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$target_sector = preg_replace('/[^0-9]/','',$_POST['target_sector']);
echo "<h1>" . $title. "</h1>\n";

$res = $db->Execute("SELECT allow_warpedit,{$db->prefix}universe.zone_id FROM {$db->prefix}zones,{$db->prefix}universe WHERE " .
                    "sector_id=$target_sector AND {$db->prefix}universe.zone_id={$db->prefix}zones.zone_id");
$query97 = $res->fields;
if ($query97['allow_warpedit'] == 'N' && $bothway)
{
    $l_warp_forbidtwo = str_replace("[target_sector]", $target_sector, $l_warp_forbidtwo);
    echo "$l_warp_forbidtwo<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$result2 = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=$target_sector");
$row = $result2->fields;
if (!$row)
{
    echo "$l_warp_nosector<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$result3 = $db->Execute("SELECT * FROM {$db->prefix}links WHERE link_start=$shipinfo[sector_id]");
if ($result3 > 0)
{
    while (!$result3->EOF)
    {
        $row = $result3->fields;
        if ($target_sector == $row['link_dest'])
        {
            $flag = 1;
        }

        $result3->MoveNext();
    }

    if ($flag != 1)
    {
        $l_warp_unlinked = str_replace("[target_sector]", $target_sector, $l_warp_unlinked);
        echo "$l_warp_unlinked<br><br>";
    }
    else
    {
        $debug_query = $db->Execute("DELETE FROM {$db->prefix}links WHERE link_start=$shipinfo[sector_id] AND " .
                                    "link_dest=$target_sector");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET dev_warpedit=dev_warpedit - 1 WHERE ship_id=$shipinfo[ship_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1" .
                                    " WHERE player_id=$playerinfo[player_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        if (!$bothway)
        {
            echo "$l_warp_removed $target_sector.<br><br>";
        }
        else
        {
            $debug_query = $db->Execute("DELETE FROM {$db->prefix}links WHERE link_start=$target_sector AND " .
                                        "link_dest=$shipinfo[sector_id]");
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            echo "$l_warp_removedtwo $target_sector.<br><br>";
        }
    }
}

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
