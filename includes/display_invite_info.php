<?php
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
// File: inclues/display_invite_info.php

function display_invite_info()
{
    global $thisplayer_info, $invite_info, $l_team_noinvite, $l_team_ifyouwant, $l_team_tocreate, $l_clickme, $l_team_injoin, $l_team_tojoin;
    global $l_team_reject, $l_team_or;
    if (!$thisplayer_info['team_invite'] && $thisplayer_info['team'] == 0)
    {
        echo "<br><br><font color=blue style=\"font-size: 0.8em;\"><strong>$l_team_noinvite</strong></font><br>";
        echo "$l_team_ifyouwant<br>";
        echo "<a href=\"teams.php?teamwhat=6\">$l_clickme</a> $l_team_tocreate<br><br>";
    }
    elseif ($thisplayer_info['team_invite'] && $thisplayer_info['team'] == 0)
    {
        echo "<br><br><font color=blue style=\"font-size: 0.8em;\"><strong>$l_team_injoin ";
        echo "<a href=\"teams.php?teamwhat=1&amp;whichteam=$thisplayer_info[team_invite]\">$invite_info[team_name]</a>.</strong></font><br>";
        echo "<a href=\"teams.php?teamwhat=3&amp;whichteam=$thisplayer_info[team_invite]\">$l_clickme</a> $l_team_tojoin <strong>$invite_info[team_name]</strong> $l_team_or <a href=\"teams.php?teamwhat=8&amp;whichteam=$thisplayer_info[team_invite]\">$l_clickme</a> $l_team_reject<br><br>";
    }
    else
    {
        echo "<br><br>";
    }
}
?>
