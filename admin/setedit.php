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
// File: setedit.php

$pos = (strpos($_SERVER['PHP_SELF'], "/setedit.php"));
if ($pos !== false)
{
    include ("global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include ("header.php");
    echo $l_cannot_access;
    include ("footer.php");
    die();
}

if(!isset($_POST['save']))
{
    // Get the config_values from the DB - silently.
    $silent = 1;
    $debug_query = $db->Execute("SELECT * FROM {$db->prefix}config_values");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $i = 0;
    while (!$debug_query->EOF && $debug_query)
    {
        $temp = $debug_query->fields;
        $db_config_name[$i] = $temp['name'];
        $db_config_category[$i] = $temp['category'];
        $db_config_value[$i] = $temp['value'];
        $db_config_description[$i] = $temp['description'];
        $i++;
        $debug_query->MoveNext();
    }

    $smarty->assign('db_config_name', $db_config_name);
    $smarty->assign('db_config_category', $db_config_category);
    $smarty->assign('db_config_value', $db_config_value);
    $smarty->assign('db_config_description', $db_config_description);
    $smarty->display("$templateset/admin/setedit.tpl");
}
else
{
    foreach($_POST as $n=>$v)
    {
        if ($n != "swordfish" && $n != "menu" && $n != "save")
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}config_values SET value=? WHERE name=?", array($v, $n));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }

    echo "<font color=lime>Game settings updated</font>";
}
?>
