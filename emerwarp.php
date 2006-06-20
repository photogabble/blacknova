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
// File: emerwarp.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "log_move.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'emerwarp');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_ewd_title;
updatecookie($db);
include_once ("./header.php");

seed_mt_rand();

if ($shipinfo['dev_emerwarp'] > 0)
{
    $source_sector = $shipinfo['sector_id'];
    $dest_sector = mt_rand(0,$sector_max);
    $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET sector_id='?', " .
                                 "dev_emerwarp=dev_emerwarp-1 WHERE ship_id='?'", array($dest_sector, $shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute ("UPDATE {$db->prefix}players SET turns_used=turns_used+1, turns=turns-1 " .
                                 "WHERE player_id='?'", array($playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    log_move($db, $playerinfo['player_id'],$shipinfo['ship_id'],$source_sector,$dest_sector,$shipinfo['class'],$shipinfo['cloak']);
    $l_ewd_used=str_replace("[sector]",$dest_sector,$l_ewd_used);
    $ewd_output = $l_ewd_used;
} 
else 
{
    $ewd_output = $l_ewd_none;
}

global $l_global_mmenu;

$template->assign("title", $title);
$template->assign("ewd_output", $ewd_output);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/emerwarp.tpl");

include_once ("./footer.php");
?>
