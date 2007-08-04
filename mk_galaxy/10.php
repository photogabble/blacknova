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
// File: mk_galaxy/10.php

$pos = strpos($_SERVER['PHP_SELF'], "/10.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

$i=0;
$configsets = array(array());
$configset_files = getDirFiles("config/");
foreach ($configset_files as $configset_filename)
{
    $configset_name = substr($configset_filename,0,-4); // Strip off the .php
    $perm = substr($configset_filename,0,10); // Configset- is 10 characters
    if ($perm == 'configset-') // files starting with "configset-" are configsets
    {
        $configsets[$i]['name'] = substr($configset_name,10);
        if ($configsets[$i]['name'] == 'BNT') // Our default selection is BNT.
        {
            $configsets[$i]['selected'] = TRUE;
        }
    $i++;
    }
}

$game_drop_down='';

$debug_query = $db->Execute("SELECT MAX(gamenumber) AS gamenumber FROM {$raw_prefix}instances");
db_op_result($db, $debug_query,__LINE__,__FILE__);
if ($debug_query && !$debug_query->EOF)
{
    $instances = $debug_query->fields['gamenumber'];
}
else
{
    $instances = 1;
}

for ($i=1; $i<9; $i++) // Nine is the maximum number of game instances for now.. maybe more later if we need it (doubtful)
{
    $game_instances[$i] = $l_gamehash . $i;
}

if ($instances < 9)
{
    $newgame = $instances+1;
}
else
{
    $newgame = 1;
}

$smarty->assign('game_instances',$game_instances);
$smarty->assign('newgame',$newgame);
$smarty->assign("game_drop_down", $game_drop_down);
$smarty->assign("l_mk_adminname", $l_mk_adminname);
$smarty->assign("l_autorun", $l_autorun);
$smarty->assign("configsets", $configsets);
$smarty->assign("l_configset_which", $l_configset_which);
$smarty->assign("l_welcome_warning",$l_welcome_warning);
$smarty->assign("autorun", TRUE);
$smarty->assign("title", $title);
$smarty->assign("encrypted_password", $_POST['encrypted_password']);
$smarty->assign("cumulative", $cumulative);
$smarty->assign("l_continue", $l_continue);
$smarty->assign("l_reset", $l_reset);
$smarty->assign("l_persist", $l_persist);
$smarty->assign("step", ($_POST['step']+1));
$smarty->display("$templateset/mk_galaxy/10.tpl");

?>
