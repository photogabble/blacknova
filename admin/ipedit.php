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
// File: ipedit.php

$pos = (strpos($_SERVER['PHP_SELF'], "/ipedit.php"));
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

echo "<b>IP Bans editor</b><p>";
$bans=array();
if (empty($_POST['command']))
{
    echo "<form action=admin.php method=post>";
    echo "<input type=hidden name=command value=showips>";
    echo "<input type=hidden name=menu value=ipedit>";
    echo "<input type=submit value=\"Show player's ips\">";
    echo "</form>";

    $res = $db->Execute("select * FROM {$db->prefix}ip_bans");
    while (!$res->EOF)
    {
        $bans[]=$res->fields;
        $res->MoveNext();
    }

    if (empty($bans))
    {
        echo "<b>No IP bans are currently active.</b>";
    }
    else
    {
        echo "<table border=1 cellspacing=1 cellpadding=2 width=100% align=center>" .
             "<tr bgcolor=$color_line2><td align=center colspan=7><b><font color=white>" .
             "Active IP Bans" .
             "</font></b>" .
             "</td></tr>" .
             "<tr align=center bgcolor=$color_line2>" .
             "<td><font size=2 color=white><b>Ban Mask</b></font></td>" .
             "<td><font size=2 color=white><b>Affected Players</b></font></td>" .
             "<td><font size=2 color=white><b>E-mail</b></font></td>" .
             "<td><font size=2 color=white><b>Reason</b></font></td>" .
             "<td><font size=2 color=white><b>Operations</b></font></td>" .
             "</tr>";

        $curcolor = $color_line1;

        foreach ($bans as $ban)
        {
            echo "<tr bgcolor=$curcolor>";
            if ($curcolor == $color_line1)
            {
                $curcolor = $color_line2;
            }
            else
            {
                $curcolor = $color_line1;
            }

            $printban = str_replace("%", "*", $ban['ban_mask']);
            echo "<td align=center><font size=2 color=white>$printban</td>" .
                 "<td align=center><font size=2 color=white>";

            $res2 = $db->Execute("select character_name, player_id, email FROM {$db->prefix}players WHERE " .
                                "ip_address LIKE ?", array($ban['ban_mask']));
            unset($players);
            while (!$res2->EOF)
            {
                $players[] = $res2->fields;
                $res2->MoveNext();
            }

            if (empty($players))
            {
                echo "None";
            }
            else
            {
                foreach ($players as $player)
                {
                    echo "<b>$player[character_name]</b><br>";
                }
            }

            echo "<td align=center><font size=2 color=white>";
            if (empty($players))
            {
                echo "N/A";
            }
            else
            {
                foreach ($players as $player)
                {
                    echo "$player[email]<br>";
                }
            }

            echo "<td align=center><font size=2 color=white>" . $ban['ban_reason'] ."</td>";
            echo "<td align=center nowrap valign=center><font size=2 color=white>" .
                 "<form action=admin.php method=post>" .
                 "<input type=hidden name=command value=unbanip>" .
                 "<input type=hidden name=menu value=ipedit>" .
                 "<input type=hidden name=ban value=$ban>" .
                 "<input type=submit value=Remove>" .
                 "</form>";
        }
    echo "</table><p>";
    }
}
elseif ($_POST['command'] == 'showips')
{
    $res = $db->Execute("select ip_address FROM {$db->prefix}players group by ip_address");
    while (!$res->EOF)
    {
        $ips[]=$res->fields['ip_address'];
        $res->MoveNext();
    }

    echo "<table border=1 cellspacing=1 cellpadding=2 width=100% align=center>" .
         "<tr bgcolor=$color_line2><td align=center colspan=7><b><font color=white>" .
         "Players sorted by IP address" .
         "</font></b>" .
         "</td></tr>" .
         "<tr align=center bgcolor=$color_line2>" .
         "<td><font size=2 color=white><b>IP address</b></font></td>" .
         "<td><font size=2 color=white><b>Players</b></font></td>" .
         "<td><font size=2 color=white><b>E-mail</b></font></td>" .
         "<td><font size=2 color=white><b>Operations</b></font></td>" .
         "</tr>";

    $curcolor = $color_line1;

    foreach ($ips as $ip)
    {
        echo "<tr bgcolor=$curcolor>";
        if ($curcolor == $color_line1)
        {
            $curcolor = $color_line2;
        }
        else
        {
            $curcolor = $color_line1;
        }

        echo "<td align=center><font size=2 color=white><a href=http://www.geektools.com/cgi-bin/proxy.cgi?query=$ip&targetnic=auto target=_blank class=mnu>$ip</a></td>" .
             "<td align=center><font size=2 color=white>";

        $res = $db->Execute("select {$db->prefix}players.character_name, {$db->prefix}players.player_id, {$raw_prefix}users.email FROM {$raw_prefix}users LEFT JOIN {$db->prefix}players ON {$raw_prefix}users.account_id={$db->prefix}players.player_id WHERE {$db->prefix}players.ip_address=?", array($ip));

        unset($players);
        while (!$res->EOF)
        {
            $players[] = $res->fields;
            $res->MoveNext();
        }

        foreach ($players as $player)
        {
            echo "<b>$player[character_name]</b><br>";

        }

        echo "<td align=center><font size=2 color=white>";

        foreach($players as $player)
        {
            echo "$player[email]<br>";
        }

        echo "<td align=center nowrap valign=center><font size=2 color=white>" .
             "<form action=admin.php method=post>" .
             "<input type=hidden name=command value=banip>" .
             "<input type=hidden name=menu value=ipedit>" .
             "<input type=hidden name=ip value=$ip>" .
             "<input type=submit value=Ban>" .
             "</form>" .
             "<form action=admin.php method=post>" .
             "<input type=hidden name=command value=unbanip>" .
             "<input type=hidden name=menu value=ipedit>" .
             "<input type=hidden name=ip value=$ip>" .
             "<input type=submit value=Unban>" .
             "</form>";
    }

    echo "</table><p>" .
         "<form action=admin.php method=post>" .
         "<input type=hidden name=menu value=ipedit>" .
         "<input type=submit value=\"Return to IP bans menu\">" .
         "</form>";
}
elseif ($_POST['command'] == 'banip')
{
    $ip = $_POST['ip'];
    echo "<b>Banning ip : $ip<p>";
    echo "<font size=2 color=white>Please select ban type :<p>";
    $ipparts = explode(".", $ip);

    echo "<table border=0>" .
         "<tr><td align=right>" .
         "<form action=admin.php method=post>" .
         "<input type=hidden name=menu value=ipedit>" .
         "<input type=hidden name=command value=banip2>" .
         "<input type=hidden name=ip value=$ip>" .
         "<input type=radio name=class value=I checked>" .
         "<td><font size=2 color=white>IP only : $ip</td>" .
         "<tr><td>" .
         "<input type=radio name=class value=A>" .
         "<td><font size=2 color=white>Class A : $ipparts[0].$ipparts[1].$ipparts[2].*</td>" .
         "<tr><td>" .
         "<input type=radio name=class value=B>" .
         "<td><font size=2 color=white>Class B : $ipparts[0].$ipparts[1].*</td>" .
         "<tr><td><td>" .
         "<input type=text name=reason value=\"Because you were an idiot\">" .
         "<br><input type=submit value=Ban>" .
         "</table>" .
         "</form>";

    echo "<form action=admin.php method=post>" .
         "<input type=hidden name=menu value=ipedit>" .
         "<input type=submit value=\"Return to IP bans menu\">" .
         "</form>";
}
elseif ($_POST['command'] == 'banip2')
{
    $ip = $_POST['ip'];
    $ipparts = explode(".", $ip);

    if ($class == 'A')
    {
        $banmask = "$ipparts[0].$ipparts[1].$ipparts[2].%";
    }
    elseif ($class == 'B')
    {
        $banmask = "$ipparts[0].$ipparts[1].%";
    }
    else
    {
        $banmask = $ip;
    }

    $printban = str_replace("%", "*", $banmask);
    echo "<font size=2 color=white><b>Successfully banned $printban</b>.<p>";

    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ip_bans (ban_id, banmask, reason) VALUES ".
                                "(?,?,?)", array('', $banmask, $reason));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $res = $db->Execute("select DISTINCT character_name FROM {$db->prefix}players, {$db->prefix}ip_bans " .
                        "WHERE ip_address LIKE ban_mask");
    echo "Affected players :<p>";
    while (!$res->EOF)
    {
        echo " - " . $res->fields['character_name'] . "<br>";
        $res->MoveNext();
    }

    echo "<form action=admin.php method=post>" .
         "<input type=hidden name=menu value=ipedit>" .
         "<input type=submit value=\"Return to IP bans menu\">" .
         "</form>";
}
elseif ($_POST['command'] == 'unbanip')
{
    $ip = $_POST['ip'];

    if (!empty($ban))
    {
        $res = $db->Execute("select * FROM {$db->prefix}ip_bans WHERE ban_mask=?", array($ban));
    }
    else
    {
        $res = $db->Execute("select * FROM {$db->prefix}ip_bans WHERE ? LIKE ban_mask", array($ip));
    }

    $nbbans = $res->RecordCount();
    while (!$res->EOF)
    {
        $res->fields['print_mask'] = str_replace("%", "*", $res->fields['ban_mask']);
        $bans[]=$res->fields;
        $res->MoveNext();
    }

    if (!empty($ban))
    {
        $db->Execute("DELETE FROM {$db->prefix}ip_bans WHERE ban_mask=?", array($ban));
    }
    else
    {
        $db->Execute("DELETE FROM {$db->prefix}ip_bans WHERE ? LIKE ban_mask", array($ip));
    }

    $query_string = "ip_address LIKE '" . $bans[0]['ban_mask'] ."'";
    for ($i = 1; $i < $nbbans ; $i++)
    {
        $query_string = $query_string . " OR ip_address LIKE '" . $bans[$i]['ban_mask'] . "'";
    }

    // DB NOT CLEANED
    $res = $db->Execute("select DISTINCT character_name FROM {$db->prefix}players WHERE $query_string");
    $nbplayers = $res->RecordCount();

    while (!$res->EOF)
    {
        $players[]=$res->fields['character_name'];
        $res->MoveNext();
    }

    echo "<font size=2 color=white><b>Successfully removed $nbbans bans</b> :<p>";
    foreach ($bans as $ban)
    {
        echo " - $ban[print_mask]<br>";
    }

    echo "<p><b>Affected players :</b><p>";
    if (empty($players))
    {
        echo " - None<br>";
    }
    else
    {
        foreach($players as $player)
        {
            echo " - $player<br>";
        }
    }

    echo "<form action=admin.php method=post>" .
         "<input type=hidden name=menu value=ipedit>" .
         "<input type=submit value=\"Return to IP bans menu\">" .
         "</form>";
}
?>
