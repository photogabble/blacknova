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
// File: igb_deposit.php

include_once ("./global_includes.php");

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'igb');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);
$title = $l_igb_title;
include_once ("./header.php");

if (!$allow_ibank)
{
    include_once ("./igb_error.php");
}

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND owner=$playerinfo[player_id]");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$planetinfo = $debug_query->RecordCount();

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND team=$playerinfo[team]");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$teamplanetinfo = $debug_query->RecordCount();

if ($portinfo['port_type'] != 'shipyard' && $portinfo['port_type'] != 'upgrades' && $portinfo['port_type'] != 'devices' && $planetinfo < 1 && $teamplanetinfo < 1)
{
    echo $l_noport . "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
else
{
    $no_body = 2;
}

updatecookie($db);

$result = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=$playerinfo[player_id]");
$account = $result->fields;

//echo "<body bgcolor=\"#666\" text=\"#FFFFFF\" link=\"#00FF00\" vlink=\"#00FF00\" alink=\"#FF0000\">";

$template->assign("playerinfo_credits", number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep));
$template->assign("l_igb_depositfunds", $l_igb_depositfunds);
$template->assign("l_igb_fundsavailable", $l_igb_fundsavailable);
$template->assign("l_igb_seldepositamount", $l_igb_seldepositamount);
$template->assign("l_igb_deposit", $l_igb_deposit);
$template->assign("l_igb_back", $l_igb_back);
$template->assign("l_igb_logout", $l_igb_logout);
$template->display("$templateset/igb_deposit.tpl");

include_once ("./footer.php");
?>
