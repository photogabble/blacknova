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
// File: teams.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "defense_vs_defense.php");
dynamic_loader ($db, "kick_off_planet.php");
dynamic_loader ($db, "showteaminfo.php");
dynamic_loader ($db, "display_all_teams.php");
dynamic_loader ($db, "display_invite_info.php");
dynamic_loader ($db, "db_output.php");
dynamic_loader ($db, "calc_ownership.php");
dynamic_loader ($db, "adminlog.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'teams');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_team_title;
updatecookie($db);
include_once ("./header.php");

echo "<h1>" . $title. "</h1>\n";

if (!isset($swordfish))
{
    $swordfish = '';
}

if (!isset($_POST['teamwhat']))
{
    $_POST['teamwhat'] = '';
}
else
{
    $teamwhat = $_POST['teamwhat'];
}

if (!isset($_GET['teamwhat']))
{
    $_GET['teamwhat'] = '';
}
else
{
    $teamwhat = $_GET['teamwhat'];
}

if (!isset($teamwhat))
{
    $teamwhat = '';
}

if (!isset($_GET['whichteam']))
{
    $_GET['whichteam'] = '';
}
else
{
    $whichteam = $_GET['whichteam'];
}

if (!isset($_POST['whichteam']))
{
    $_POST['whichteam'] = '';
}
else
{
    $whichteam = $_POST['whichteam'];
}

if (!isset($whichteam))
{
    $whichteam = '';
}

if (!isset($_GET['confirmleave']))
{
    $_GET['confirmleave'] = '';
}
else
{
    $confirmleave = $_GET['confirmleave'];
}

if (!isset($_POST['confirmleave']))
{
    $_POST['confirmleave'] = '';
}
else
{
    $confirmleave = $_POST['confirmleave'];
}

if (!isset($confirmleave))
{
    $confirmleave = '';
}

// Get user info
$debug_query = $db->Execute("SELECT {$db->prefix}players.*, {$db->prefix}teams.team_name, {$db->prefix}teams.description, " .
                            "{$db->prefix}teams.creator, {$db->prefix}teams.team_id FROM {$db->prefix}players " .
                            "LEFT JOIN {$db->prefix}teams ON {$db->prefix}players.team = {$db->prefix}teams.team_id " .
                            "LEFT JOIN {$raw_prefix}users ON {$raw_prefix}users.account_id = {$db->prefix}players.account_id ".
                            "WHERE {$raw_prefix}users.email=? ", array($_SESSION['email']));
db_op_result($db,$debug_query,__LINE__,__FILE__);
$thisplayer_info = $debug_query->fields;

// We do not want to query the database if it is not necessary.
if ($thisplayer_info['team_invite'] != "")
{
    // Get invite info
    $debug_query = $db->Execute("SELECT {$db->prefix}players.player_id, {$db->prefix}players.team_invite, " .
                                "{$db->prefix}teams.team_name, {$db->prefix}teams.team_id FROM {$db->prefix}players " .
                                "LEFT JOIN {$db->prefix}teams ON {$db->prefix}players.team_invite = {$db->prefix}teams.team_id " .
                                "LEFT JOIN {$raw_prefix}users ON {$raw_prefix}users.account_id = {$db->prefix}players.account_id ".
                                "WHERE {$raw_prefix}users.email=? ", array($_SESSION['email']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $invite_info = $debug_query->fields;
}

// Get Team Info
$whichteam = preg_replace('/[^0-9]/','',$whichteam);
if ($whichteam)
{
    $result_team = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=?", array($whichteam));
    $team = $result_team->fields;
}
else
{
    $result_team = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=?", array($thisplayer_info['team']));
    $team = $result_team->fields;
}

switch ($teamwhat)
{
    case 1:    // INFO on sigle team
        showteaminfo($db,$whichteam, 0);
        echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
        break;
    case 2:    // LEAVE
        if (!isset($confirmleave) || ($confirmleave == '') || (!$confirmleave))
        {
            echo "$l_team_confirmleave <strong>$team[team_name]</strong> ? <a href=\"teams.php?teamwhat=$teamwhat&amp;confirmleave=1&amp;whichteam=$whichteam\">$l_yes</a> - <a href=\"teams.php\">$l_no</a><br><br>";
        }
        elseif ($confirmleave == 1)
        {
            if ($team['number_of_members'] == 1)
            {
                $debug_query = $db->Execute("DELETE FROM {$db->prefix}teams WHERE team_id=?", array($whichteam));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team='0' WHERE " .
                                            "player_id=?", array($thisplayer_info['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team_invite=0 WHERE " .
                                            "team_invite=?", array($whichteam));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $res = $db->Execute("SELECT DISTINCT sector_id FROM {$db->prefix}planets WHERE owner=? AND base='Y'", array($thisplayer_info['player_id']));
                $i = 0;
                if ($res)
                {
                    while (!$res->EOF)
                    {
                        $row = $res->fields;
                        $sectors[$i] = $row['sector_id'];
                        $i++;
                        $res->MoveNext();
                    }
                }

                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team=0 WHERE owner=?", array($thisplayer_info['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                if (!empty($sectors))
                {
                    foreach ($sectors as $sector)
                    {
                        calc_ownership($db,$sector);
                    }
                }

                defense_vs_defense($db, $thisplayer_info['player_id']);
                kick_off_planet($db, $thisplayer_info['player_id'],$whichteam);

                $l_team_onlymember = str_replace("[team_name]", "<strong>$team[team_name]</strong>", $l_team_onlymember);
                echo "$l_team_onlymember<br><br>";
                playerlog($db,$thisplayer_info['player_id'], "LOG_TEAM_LEAVE", "$team[team_name]");
            }
            else
            {
                if ($team['creator'] == $thisplayer_info['player_id'])
                {
                    echo "$l_team_youarecoord <strong>$team[team_name]</strong>. $l_team_relinq<br><br>";
                    echo "<form action='teams.php' method=post>";
                    echo "<table><input type=hidden name=teamwhat value=$teamwhat><input type=hidden name=confirmleave value=2><input type=hidden name=whichteam value=$whichteam>";
                    echo "<tr><td>$l_team_newc</td><td><select name=newcreator>";
                    $res = $db->Execute("SELECT character_name,player_id FROM {$db->prefix}players WHERE team=? ORDER BY character_name ASC", array($whichteam));
                    while (!$res->EOF)
                    {
                        $row = $res->fields;
                        if ($row['player_id'] != $team['creator'])
                        {
                            echo "<OPTION value=$row[player_id]>$row[character_name]";
                        }

                        $res->MoveNext();
                    }
                    echo "</select></td></tr>";
                    echo "<tr><td><input type=submit type=$l_submit></td></tr>";
                    echo "</table>";
                    echo "</form>";
                }
                else
                {
                    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team='0' WHERE " .
                                                "player_id=?", array($thisplayer_info['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute("UPDATE {$db->prefix}teams SET number_of_members=number_of_members-1 WHERE " .
                                                "team_id=?", array($whichteam));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $res = $db->Execute("SELECT DISTINCT sector_id FROM {$db->prefix}planets WHERE " .
                                        "owner=? AND base='Y' AND team!=0", array($thisplayer_info['player_id']));
                    $i = 0;
                    while (!$res->EOF)
                    {
                        $sectors[$i] = $res->fields[sector_id];
                        $i++;
                        $res->MoveNext();
                    }

                    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team=0 WHERE owner=?", array($thisplayer_info['player_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    if (!empty($sectors))
                    {
                        foreach ($sectors as $sector)
                        {
                            calc_ownership($db,$sector);
                        }
                    }

                    echo "$l_team_youveleft <strong>$team[team_name]</strong>.<br><br>";
                    defense_vs_defense($db, $thisplayer_info['player_id']);
                    kick_off_planet($db, $thisplayer_info['player_id'],$whichteam);
                    playerlog($db,$thisplayer_info['player_id'], "LOG_TEAM_LEAVE", "$team[team_name]");
                    playerlog($db,$team['creator'], "LOG_TEAM_NOT_LEAVE", "$thisplayer_info[character_name]");
                }
            }
        }
        elseif ($confirmleave == 2)
        { // owner of a team is leaving and set a new owner
            $res = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=?", array($newcreator));
            $newcreatorname = $res->fields;
            echo "$l_team_youveleft <strong>$team[team_name]</strong> $l_team_relto $newcreatorname[character_name].<br><br>";
            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team='0' WHERE " .
                                        "player_id=?", array($thisplayer_info['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

// TODO: What is that $creator and do we need this query?
//            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team=? WHERE " .
//                                        "team=?", array($newcreator, $creator));
//            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $debug_query = $db->Execute("UPDATE {$db->prefix}teams SET number_of_members=number_of_members-1,creator=? " .
                                        "WHERE team_id=?", array($newcreator, $whichteam));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $res = $db->Execute("SELECT DISTINCT sector_id FROM {$db->prefix}planets WHERE owner=? AND base='Y' AND team!=0", array($thisplayer_info['player_id']));
            $i = 0;
            while (!$res->EOF)
            {
                $sectors[$i] = $res->fields[sector_id];
                $i++;
                $res->MoveNext();
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team=0 WHERE owner=?", array($thisplayer_info['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            if (!empty($sectors))
            {
                foreach($sectors as $sector)
                {
                    calc_ownership($db,$sector);
                }
            }

            playerlog($db,$thisplayer_info['player_id'], "LOG_TEAM_NEWLEAD", "$team[team_name]|$newcreatorname[character_name]");
            playerlog($db,$newcreator, "LOG_TEAM_LEAD","$team[team_name]");
        }

        echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
        break;
    case 3: // JOIN
        if ($thisplayer_info['team'] != 0)
        {
            echo $l_team_leavefirst . "<br>";
        }                 
        else
        {
            if ($thisplayer_info['team_invite'] == $whichteam)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team=?, team_invite=0 " .
                                            "WHERE player_id=?", array($whichteam, $thisplayer_info['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}teams SET number_of_members=number_of_members+1 WHERE " .
                                            "team_id=?", array($whichteam));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "$l_team_welcome <strong>$team[team_name]</strong>.<br><br>";
                playerlog($db,$thisplayer_info['player_id'], "LOG_TEAM_JOIN", "$team[team_name]");
                playerlog($db,$team['creator'], "LOG_TEAM_NEWMEMBER", "$team[team_name]|$thisplayer_info[character_name]");
            }
            else
            {
                echo "$l_team_noinviteto<br>";
            }
        }

        echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
        break;
    case 4:
        echo "Not implemented yet. Please file a Feature Request at sourceforge.<br><br>";
        echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
        break;
    case 5: // Eject member
        if ($thisplayer_info['team'] == $team['team_id'])
        {
            $who = preg_replace('/[^0-9]/','',$who);
            $result = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=?", array($who));
            $whotoexpel = $result->fields;
            if (!isset($confirmed))
            {
                echo "$l_team_ejectsure $whotoexpel[character_name]? <a href=\"teams.php?teamwhat=$teamwhat&amp;confirmed=1&amp;who=$who\">$l_yes</a> - <a href=\"teams.php\">$l_no</a><br>";
            }
            else
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET team='0' WHERE owner=?", array($who));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team='0' WHERE player_id=?", array($who));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                playerlog($db,$who, "LOG_TEAM_KICK", "$team[team_name]");
                echo "$whotoexpel[character_name] $l_team_ejected<br>";
            }

            echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
            break;
        }
        else
        {
             adminlog($db, "LOG_CHEAT_TEAM", "$thisplayer_info[character_name]|$ip_address");
             echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
             break;
        } 
    case 6: // Create Team

            if (!isset($_POST['teamname']) || ($_POST['teamname'] == ''))
            {
                if ($thisplayer_info['team'] == 0)
                {
                    echo "<form action=\"teams.php\" method=post>";
                    echo "$l_team_entername: ";
                    echo "<input type=hidden name=teamwhat value=$teamwhat>";
                    echo "<input type=text name=teamname size=20 maxlength=20><br>";
                    echo "$l_team_enterdesc: ";
                    echo "<input type=text name=teamdesc size=20 maxlength=20><br>";
                    echo "<input type=submit value=$l_submit><input type=reset value=$l_reset>";
                    echo "</form>";
                    echo "<br><br>";
                }
                break;
            }
            else
            {
                $zonename = htmlspecialchars($_POST['teamname'] . "'s Empire", ENT_QUOTES,"UTF-8");
                $teamname = htmlspecialchars($_POST['teamname'],ENT_QUOTES,"UTF-8");
                $teamdesc = htmlspecialchars($_POST['teamdesc'],ENT_QUOTES,"UTF-8");
                $zonename = $db->qstr($zonename,get_magic_quotes_gpc());
                $teamname = $db->qstr($teamname,get_magic_quotes_gpc());
                $teamdesc = $db->qstr($teamdesc,get_magic_quotes_gpc());

                $debug_query = $db->Execute("INSERT INTO {$db->prefix}teams (team_id, creator, team_name, number_of_members, description) " .
                                            "VALUES (?,?,?,?,?)", array($thisplayer_info['player_id'], $thisplayer_info['player_id'], $teamname, '1', $teamdesc));
                echo db_output($db,db_op_result($db,$debug_query,__LINE__,__FILE__),__LINE__,__FILE__);

                $debug_query = $db->Execute("INSERT INTO {$db->prefix}zones (zone_id, zone_name, owner, team_zone, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_level) " .
                                            "VALUES (?,?,?,?,?,?,?,?,?,?,?)", array('', $zonename, $thisplayer_info['player_id'], 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team=? WHERE player_id=?", array($thisplayer_info['player_id'], $thisplayer_info['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("SELECT team_name FROM {$db->prefix}teams WHERE team_id=?", array($thisplayer_info['player_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $teamname = $debug_query->fields['team_name'];

                echo "$l_team <strong>$teamname</strong> $l_team_hcreated.<br><br>";
                playerlog($db,$thisplayer_info['player_id'], "LOG_TEAM_CREATE", "$teamname");

                echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
                break;
            }
// ALL cleaned except for this perverse section!
    case 7: // INVITE player
        if (!isset($invited) || ($invited == '') || !($invited))
        {
            echo "\n<form action='teams.php' method=post>";
            echo "\n<input type=hidden name=teamwhat value=$teamwhat><input type=hidden name=invited value=1><input type=hidden name=whichteam value=$whichteam>";
            echo "\n<table><tr><td>$l_team_selectp:</td><td><select name=who>";
            $res = $db->Execute("SELECT character_name,player_id FROM {$db->prefix}players WHERE team!=? " .
                                "ORDER BY character_name ASC", array($whichteam));
            while (!$res->EOF)
            {
                $row = $res->fields;
                if ($row['player_id'] != $team['creator'])
                {
                    echo "<OPTION value=$row[player_id]>$row[character_name]";
                }

                $res->MoveNext();
            }

            echo "</select></td></tr>";
            echo "<tr><td><input type=submit value=$l_submit></td></tr>";
            echo "</table>";
            echo "</form>";
            echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
            break;
        }
        else
        {
            if ($thisplayer_info['team'] == $whichteam)
            {
                $res = $db->Execute("SELECT character_name,team_invite FROM {$db->prefix}players WHERE player_id=?", array($who));
                $newpl = $res->fields;
                if ($newpl['team_invite']) 
                {
                    $l_team_isorry = str_replace("[name]", $newpl[character_name], $l_team_isorry);
                    echo "$l_team_isorry<br><br>";
                }
                else 
                {
                    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team_invite=? " .
                                                "WHERE player_id=?", array($whichteam, $who));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    echo $l_team_plinvted1 . "<br>" . $l_team_plinvted2;
                    playerlog($db,$who, "LOG_TEAM_INVITE", "$team[team_name]");
                }
            }
            else
            {
                echo "$l_team_notyours<br>";
            }

            echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
            break;
        }

        echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu<br><br>";
    case 8: // REFUSE invitation
        echo "$l_team_refuse <strong>$invite_info[team_name]</strong>.<br><br>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET team_invite=0 WHERE " .
                                    "player_id=?", array($thisplayer_info['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        playerlog($db,$team[creator], "LOG_TEAM_REJECT", "$thisplayer_info[character_name]|$invite_info[team_name]");
        echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
        break;
    case 9: // Edit Team
/*        if ($testing)
        {
            if ($swordfish != $adminpass)
            {
                echo "<form action=\"teams.php\" method=post>";
                echo "$l_team_testing<br><br>";
                echo "$l_team_pw: <input type=password name=swordfish size=20 maxlength=20><br><br>";
                echo "<input type=hidden name=teamwhat value=$teamwhat>";
                echo "<input type=submit value=$l_submit><input type=reset value=$l_reset>";
                echo "</form>";
                echo "<br><br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                include_once ("./footer.php");
                die();
            }
        }*/

        if ($thisplayer_info['team'] == $whichteam)
        {
            if (!isset($update) || ($update == ''))
            {
                echo "<form action=\"teams.php\" method=post>";
                echo "$l_team_edname: <br>";
                echo "<input type=hidden name=swordfish value='$swordfish'>";
                echo "<input type=hidden name=teamwhat value=$teamwhat>";
                echo "<input type=hidden name=whichteam value=$whichteam>";
                echo "<input type=hidden name=update value=true>";
                echo "<input type=text name=teamname size=20 maxlength=20 value=\"".$team['team_name']."\"><br>";
                echo "$l_team_eddesc: <br>";
                echo "<input type=text name=teamdesc size=20 maxlength=20 value=\"".$team['description']."\"><br>";
                echo "<input type=submit value=$l_submit><input type=reset value=$l_reset>";
                echo "</form>";
                echo "<br><br>";
            }
            else
            {
                $teamname = htmlspecialchars($teamname,ENT_QUOTES,"UTF-8");
                $teamdesc = htmlspecialchars($teamdesc,ENT_QUOTES,"UTF-8");
                $debug_query = $db->Execute("UPDATE {$db->prefix}teams SET team_name=?, description=? " . 
                                            "WHERE team_id=?", array($teamname, $teamdesc, $whichteam));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                echo "$l_team <strong>$teamname</strong> $l_team_hasbeenr<br><br>";

                // Adding a log entry to all members of the renamed team
                $result_team_name = $db->Execute("SELECT player_id FROM {$db->prefix}players WHERE team=? AND player_id!=?", array($whichteam, $thisplayer_info['player_id']));
                playerlog($db,$thisplayer_info['player_id'], "LOG_TEAM_RENAME", "$teamname");
                while (!$result_team_name->EOF)
                {
                    $teamname_array = $result_team_name->fields;
                    playerlog($db,$teamname_array[player_id], "LOG_TEAM_M_RENAME", "$teamname");
                    $result_team_name->MoveNext();
                }
            }
            echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
            break;
        }
        else
        {
            echo "<strong><font color=\"red\">" . $l_error_occured . "</font></strong>" . $l_team_error;
            echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
            break;
        }
    default:
        if (!$thisplayer_info['team'])
        {
            echo "$l_team_notmember";
            display_invite_info();
        }
        else
        {
            if ($thisplayer_info['team'] < 0)
            {
                $thisplayer_info[team] = -$thisplayer_info[team];
                $result = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=?", array($thisplayer_info['team']));
                $whichteam = $result->fields;
                echo "<strong>" . $l_team_urejected1 . "</strong>" . $l_team_urejected2 . "<strong>" . $whichteam[team_name] . "</strong><br><br>";

                echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
                break;
            }
            $result = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=?", array($thisplayer_info['team']));
            $whichteam = $result->fields;
            if ($thisplayer_info['team_invite'])
            {
                $result = $db->Execute("SELECT * FROM {$db->prefix}teams WHERE team_id=?", array($thisplayer_info['team_invite']));
                $whichinvitingteam = $result->fields;
            }

            $isowner = ($thisplayer_info['player_id'] == $whichteam['creator']);
            showteaminfo($db,$thisplayer_info['team'],$isowner);
        }

        $res = $db->Execute("SELECT * FROM {$db->prefix}teams");
        $teams_count = $res->RecordCount();
        if ($teams_count > 0) 
        {
            display_all_teams($db);
        } 
        else 
        {
            echo "$l_team_noteams<br><br>";
        }

        break;
} // switch ($teamwhat)

echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");
?>
