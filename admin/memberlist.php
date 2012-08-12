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
// File: admin/memberlist.php

$pos = (strpos($_SERVER['PHP_SELF'], "/memberlist.php"));
if ($pos !== false)
{
    include_once './global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once './header.php';
    echo $l_cannot_access;
    include_once './footer.php';
    die();
}

if ($_POST['delete'] == 'delete')
{
    if ($_POST['email'] == '')
    {
        echo "No email address selected - Try again!";
    }
    else
    {
        echo "Deleting $_POST[email] from the Invitation list ";
        $silent=0;
        $debug_query = $db->Execute("DELETE FROM {$db->prefix}memberlist where email=?", array($_POST['email']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    echo "<form action='admin.php' method='post'>";
    echo "<input type=hidden name=menu value='memberlist'>";
    echo "<br><br><input type=submit value='Return to memberlist'>";
}
elseif ($_POST['action'] == 'save')
{
    echo "Updating Member list ";
    if ($_POST['member_id'] == '')
    {
        $silent=0;
        $debug_query = $db->Execute("INSERT INTO {$db->prefix}memberlist (email) VALUES (?)",array($_POST['email']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
    else
    {
        $silent=0;
        $debug_query = $db->Execute("UPDATE {$db->prefix}memberlist SET email=? where member_id=?", array($_POST['email'], $_POST['member_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    echo "<form action='admin.php' method='post'>";
    echo "<input type=hidden name=menu value='memberlist'>";
    echo "<br><br><input type=submit value='Return to memberlist'>";
}
elseif (($_POST['edit'] == 'edit') || ($_POST['add'] == 'add'))
{
    $email = '';
    $member_id = '';
    if ($_POST['email'] != '' && $_POST['edit'] == 'edit')
    {
        $debug_query = $db->SelectLimit("SELECT member_id from {$db->prefix}memberlist where email=?",1,-1,array($_POST['email']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $member_id = $debug_query->fields['member_id'];
        $email = $_POST['email'];
    }

    echo "<h3>Invitation List Editor</h3>";
    echo "<form action='admin.php' method='post'>";
    echo "<input type=text size=60 name=email value='$email'>";
    echo "<input type=hidden name=menu value='memberlist'>";
    echo "<input type=hidden name=member_id value='$member_id'>";
    echo "<input type=hidden name=action value='save'>";
    echo "&nbsp;<input type=submit value='Save'>";
    echo "</form>";
}
else
{
    echo "<h3>Invitation List Editor</h3>";
    echo "<form action=admin.php method=post>";
    $debug_query = $db->Execute("SELECT email from {$db->prefix}memberlist");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    echo "<select size=15 name=email>";
    while (!$debug_query->EOF)
    {
        $row = $debug_query->fields;

        echo "<option value=\"$row[email]\"> $row[email] </option>\n";
        $debug_query->MoveNext();
    }

    echo "</select>";
    echo "&nbsp;<input type=submit value=edit name='edit'>";
    echo "&nbsp;<input type=submit value=delete name='delete'>";
    echo "&nbsp;<input type=submit value=add name='add'>";
    echo "<input type=hidden name=menu value=memberlist>";
    echo "</form>";
}

?>
