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
// File: admin/an.php

$pos = (strpos($_SERVER['PHP_SELF'], "/an.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once ("./header.php");
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

if(!empty($_POST['command']))
{
    $command = $_POST['command'];
}

if ($command == "del")
{
    $debug_query = $db->Execute("SELECT MAX(an_id) as x FROM {$db->prefix}alerts");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $row = $debug_query->fields;

    $debug_query = $db->Execute("DELETE FROM {$db->prefix}alerts WHERE an_id=?", array($row['x']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}

if ($command == "add")
{
    $xsql = $db->Prepare("INSERT INTO {$db->prefix}alerts (an_text) VALUES (?)");
    $debug_query = $db->Execute($xsql,array($_POST['an_text']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}

$debug_query = $db->Execute("select * FROM {$db->prefix}alerts ORDER BY an_id DESC");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$row = $debug_query->fields;

$template->assign("title", $title);
$template->assign("an_text", $row['an_text']);
$template->display("$templateset/admin/an.tpl");
?>
