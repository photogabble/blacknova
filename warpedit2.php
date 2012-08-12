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
// File: warpedit2.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'warpedit2');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_warp_title;
updatecookie($db);

if (!isset($flag))
{
    $flag = '';
}

if (!isset($_POST['target_sector']))
{
    $_POST['target_sector'] = '';
}

if (!isset($flag))
{
    $flag = '';
}

if (!isset($oneway))
{
    $oneway = '';
}

if (!isset($flag2))
{
    $flag2 = '';
}

$res = $db->Execute("SELECT allow_warpedit,{$db->prefix}universe.zone_id FROM {$db->prefix}zones,{$db->prefix}universe WHERE " .
                    "sector_id=? AND {$db->prefix}universe.zone_id={$db->prefix}zones.zone_id", array($shipinfo['sector_id']));
$query97 = $res->fields;

if ($playerinfo['turns'] < 1)
{
    echo "$l_warp_turn<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

if ($shipinfo['dev_warpedit'] < 1)
{
    echo "$l_warp_none<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

if ($query97['allow_warpedit'] == 'N')
{
    echo "$l_warp_forbid<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

$_POST['target_sector'] = preg_replace('/[^0-9]/','',$_POST['target_sector']);

echo "<h1>" . $title. "</h1>\n";

$result2 = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array($_POST['target_sector']));
$row = $result2->fields;
if (!$row)
{
    echo "$l_warp_nosector<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

$res = $db->Execute("SELECT allow_warpedit, {$db->prefix}universe.zone_id FROM {$db->prefix}zones, {$db->prefix}universe WHERE " .
                    "sector_id=? AND {$db->prefix}universe.zone_id={$db->prefix}zones.zone_id", array($_POST['target_sector']));
$query97 = $res->fields;
if ($query97['allow_warpedit'] == 'N' && !$oneway)
{
    $l_warp_twoerror = str_replace("[target_sector]", $_POST['target_sector'], $l_warp_twoerror);
    echo "$l_warp_twoerror<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}links WHERE link_start=?", array($shipinfo['sector_id']));
$numlink_start = $debug_query->RecordCount();

if ($numlink_start >= $link_max )
{
    $l_warp_sectex = str_replace("[link_max]", $link_max, $l_warp_sectex);
    echo "$l_warp_sectex<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

$result3 = $db->Execute("SELECT * FROM {$db->prefix}links WHERE link_start=?", array($shipinfo['sector_id']));
if ($result3 > 0)
{
    while (!$result3->EOF)
    {
        $row = $result3->fields;
        if ($_POST['target_sector'] == $row['link_dest'])
        {
            $flag = 1;
        }
        $result3->MoveNext();
    }

    if ($flag == 1)
    {
        $l_warp_linked = str_replace("[target_sector]", $_POST['target_sector'], $l_warp_linked);
        echo "$l_warp_linked<br><br>";
    }
    elseif ($shipinfo['sector_id'] == $_POST['target_sector'])
    {
        echo $l_warp_cantsame;
    }
    else
    {
        $debug_query = $db->Execute ("INSERT INTO {$db->prefix}links (link_start, link_dest) " .
                                     "VALUES (?,?)", array ($shipinfo['sector_id'], $_POST['target_sector']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET dev_warpedit=dev_warpedit - 1 WHERE ship_id=?", array($shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute ("UPDATE {$db->prefix}players SET turns=turns-1, " .
                                     "turns_used=turns_used+1 WHERE player_id=?", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        if ($oneway)
        {
            echo "$l_warp_coneway $_POST[target_sector].<br><br>";
        }
        else
        {
            $result4 = $db->Execute ("SELECT * FROM {$db->prefix}links WHERE link_start=?", array($_POST['target_sector']));
            if ($result4)
            {
                while (!$result4->EOF)
                {
                    $row = $result4->fields;
                    if ($shipinfo['sector_id'] == $row['link_dest'])
                    {
                        $flag2 = 1;
                    }

                    $result4->MoveNext();
                }
            }

            if ($flag2 != 1)
            {
                $debug_query = $db->Execute ("INSERT INTO {$db->prefix}links (link_start, link_dest) VALUES " .
                                             "(?,?)", array($_POST['target_sector'], $shipinfo['sector_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            echo "$l_warp_ctwoway $_POST[target_sector].<br><br>";
        }
    }
}

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once './footer.php';

?>
