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
// File: mailto.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'mail');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

echo $l_sendm_title;

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_sendm_title;
updatecookie($db);
include_once ("./header.php");

$name = '';
if (isset($_POST['name']))
{
    $name = $_POST['name'];
}
elseif (isset($_GET['name']))
{
    $name = $_GET['name'];
}

$to = '';
if (isset($_GET['to']))
{
    $to = $_GET['to'];
}
elseif (isset($_POST['to']))
{
    $to = $_POST['to'];
}

if (!isset($_POST['content']))
{
    $content = '';
}
else
{
    $content = $_POST['content'];
}

if (!isset($_GET['subject']))
{
    $subject = '';
}
else
{
    $subject = $_GET['subject'];
}

if (isset($_POST['subject']))
{
    $subject = $_POST['subject'];
}


echo "<h1>" . $title. "</h1>\n";

if (empty($content))
{
    //-- Get list of all other players except the AI players
    $res = $db->Execute("SELECT character_name FROM " .
                        "{$db->prefix}players WHERE acl != '0' ORDER BY character_name ASC");

    $res2 = $db->Execute("SELECT team_name FROM {$db->prefix}teams ORDER BY team_name ASC");
    echo '<form name="bntform" action="mailto.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
    echo "<table>";
    echo "<tr><td>$l_sendm_to:</td><td><select name=\"to\">";

    while (!$res->EOF && $res)
    {
        $row = $res->fields;
        echo "<option ";
        if ($row['character_name'] == $name)
        {
            echo "selected";
        }

        echo '>';
        echo $row['character_name'];
        echo '</option>';
        $res->MoveNext();
    }

    while (!$res2->EOF && $res)
    {
        $row2 = $res2->fields;
        echo "<option>$l_sendm_ally $row2[team_name]</option>";
        $res2->MoveNext();
    }

    echo "</select></td></tr>";
    echo "<tr><td>$l_sendm_from:</td><td>";
    echo "<input disabled type=\"text\" name=\"dummy\" size=\"40\" maxlength=\"40\" value=\"$playerinfo[character_name]\"></td></tr>";
    if (isset($subject) && (strpos($subject, "RE:") === FALSE))
    {
        $subject = "RE: " . $subject;
    }

    echo "<tr><td>$l_sendm_subj:</td><td><input type=\"text\" name=\"subject\" size=\"40\" maxlength=\"40\" value=\"$subject\"></td></tr>";
    echo "<tr><td>$l_sendm_mess:</td><td><textarea name=\"content\" rows=\"5\" cols=\"40\"></textarea></td></tr>";
    echo "<tr><td></td><td><input name=submit_button type=\"submit\" value=\"$l_sendm_send\"><input type=\"reset\" value=\"$l_reset\"></td>";
    echo "</table>";
    echo "</form>";
}
else
{
    echo "$l_sendm_sent<br><br>";
}

if ($to != '')
{

if (strpos($to, $l_sendm_ally) === false)
{
    $timestamp = date("Y-m-d H:i:s");
    $res = $db->Execute("SELECT * FROM {$db->prefix}players WHERE character_name='$to'");
    $target_info = $res->fields;
    $content = htmlspecialchars($content,ENT_QUOTES,"UTF-8");
    $subject = htmlspecialchars($subject,ENT_QUOTES,"UTF-8");
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}messages (sender_id, recp_id, sent, subject, message) VALUES " .
                                "(?,?,?,?,?)", array($playerinfo['player_id'], $target_info['player_id'], $timestamp, $subject, $content));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}
else
{
    $timestamp = date("Y-m-d H:i:s");
    $to = str_replace ($l_sendm_ally, "", $to);
    $to = trim($to);

    $res = $db->Execute("SELECT team_id FROM {$db->prefix}teams WHERE team_name='$to'");
    $row = $res->fields;
    $res2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE team='$row[team_id]'");

    // New lines to prevent SQL injection. Bad stuff.
    $content = htmlspecialchars($content,ENT_QUOTES,"UTF-8");
    $subject = htmlspecialchars($subject,ENT_QUOTES,"UTF-8");

    while (!$res2->EOF)
    {
        $row2 = $res2->fields;
        $debug_query = $db->Execute("INSERT INTO {$db->prefix}messages (sender_id, recp_id, sent, subject, message) VALUES " .
                                    "(?,?,?,?,?)", array($playerinfo['player_id'], $row2['player_id'], $timestamp, $subject, $content));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $res2->MoveNext();
    }
}

}
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
