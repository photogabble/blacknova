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
// File: dump.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'dump');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_dump_title;
updatecookie($db);

echo "<h1>" . $title. "</h1>\n";

if ($playerinfo['turns'] < 1)
{
    $template->assign("l_dump_turn", $l_dump_turn);
    $template->display("$templateset/dump.tpl");
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

if ($shipinfo['colonists'] == 0)
{
    $dump_echo = $l_dump_nocol;
}
elseif ($portinfo['port_type'] == "upgrades" || $portinfo['port_type'] == "devices")
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET colonists=0 WHERE ship_id=?", array($shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 WHERE player_id=?", array($playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $dump_echo = $l_dump_dumped;
}
else
{
    $dump_echo = $l_dump_nono;
}

$template->assign("dump_echo", $dump_echo);
$template->display("$templateset/dump.tpl");

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once './footer.php';

?>
