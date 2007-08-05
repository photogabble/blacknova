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
// File: includes/showteaminfo.php

function showteaminfo($db,$whichteam,$isowner)
{
    global $thisplayer_info, $invite_info, $team, $l_team_coord, $l_team_member, $l_options, $l_team_ed, $l_team_inv, $l_team_leave, $l_team_members, $l_score, $l_team_noinvites, $l_team_pending;
    global $l_team_eject;
    global $color_line1, $color_line2;

    // Heading
    echo "<div align=\"center\">";
    echo "<h3><font color=\"white\"><strong>$team[team_name]</strong>";
    echo "<br><font style=\"font-size: 0.8em;\">\"<strong>$team[description]</strong>\"</font></font></h3>";
    echo "<a href=\"team_report.php\">Team Ships</a>&nbsp;<br>";
    if ($thisplayer_info['team'] == $team['team_id'])
    {
        echo "<font color=white>";
        if ($thisplayer_info['player_id'] == $team['creator'])
        {
            echo "$l_team_coord ";
        }
        else
        {
            echo "$l_team_member ";
        }

        echo "$l_options<br><font style=\"font-size: 0.8em;\">";
        if ($thisplayer_info['player_id'] == $team['creator'])
        {
            echo "[<a href='teams.php?teamwhat=9&amp;whichteam=$thisplayer_info[team]'>$l_team_ed</a>] - ";
        }

        echo "[<a href=\"teams.php?teamwhat=7&amp;whichteam=$thisplayer_info[team]\">$l_team_inv</a>] - [<a href=\"teams.php?teamwhat=2&amp;whichteam=$thisplayer_info[team]\">$l_team_leave</a>]</font></font>";
    }

    display_invite_info();
    echo "</div>";

    // Main table
    echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=\"#400040\" width=\"75%\" align=center>";
    echo "<tr>";
    echo "<td><font color=white>$l_team_members</font></td>";
    echo "</tr><tr bgcolor=\"$color_line2\">";
    $result  = $db->Execute("SELECT * FROM {$db->prefix}players WHERE team=?", array($whichteam));
    while (!$result->EOF)
    {
        $member = $result->fields;
        echo "<td> - $member[character_name] ($l_score $member[score])";
        if ($isowner && ($member['player_id'] != $thisplayer_info['player_id']))
        {
            echo " - <font style=\"font-size: 0.8em;\">[<a href=\"teams.php?teamwhat=5&amp;who=$member[player_id]\">$l_team_eject</a>]</font></td>";
        }
        else
        {
            if ($member['player_id'] == $team['creator'])
            {
                echo " - $l_team_coord</td>";
            }
        }

        echo "</tr><tr bgcolor=\"$color_line2\">";
        $result->MoveNext();
    }

    // Displays for members name
    $res = $db->Execute("SELECT player_id,character_name FROM {$db->prefix}players WHERE team_invite=?", array($whichteam));
    echo "<td bgcolor=\"$color_line2\"><font color=\"white\">$l_team_pending <strong>$team[team_name]</strong></font></td>";
    echo "</tr><tr>";
    if ($res->RecordCount() > 0)
    {
        echo "</tr><tr bgcolor=\"$color_line2\">";
        while (!$res->EOF)
        {
            $who = $res->fields;
            echo "<td> - $who[character_name]</td>";
            echo "</tr><tr bgcolor=\"$color_line2\">";
            $res->MoveNext();
        }

    }
    else
    {
        echo "<td>$l_team_noinvites <strong>$team[team_name]</strong>.</td>";
        echo "</tr><tr>";
    }

    echo "<td></td></tr></table>";
}
?>
