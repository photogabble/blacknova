<?php
function showinfo($db,$whichteam,$isowner)
{
    global $playerinfo, $invite_info, $team, $l_team_coord, $l_team_member, $l_options, $l_team_ed, $l_team_inv, $l_team_leave;
    global $db, $l_team_eject, $main_table_heading, $l_team_members, $l_score, $l_team_noinvites, $l_team_pending;
    global $color_line1, $color_line2, $orderby, $direction, $color_header, $plasma_engines;

    // Heading
    echo"<div align=center>";
    echo "<h3><font color=white><strong>" . $team['team_name'] . "</strong>";
    echo "<br>\"<strong>" . $team['description'] . "</strong>\"</font></h3>";

    echo "</div>";

    for ($iz=0; $iz<50; $iz++)
    {
        if ($iz<10)
        {
            $colorarray[$iz] = "#FFADAD";
        }

        if ($iz>9 && $iz<20)
        {
            $colorarray[$iz] = "#FFFF00";
        }

        if ($iz>19 && $iz<30)
        {
            $colorarray[$iz] = "#0CD616";
        }

        if ($iz>29)
        {
            $colorarray[$iz] = "#ffffff";
        }
    }

    // Main table
    echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=\"" . $color_header . "\" width=\"75%\" align=center>";
    echo "<tr>";
    echo "<td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=p.character_name\">$l_team_members</a></strong></font></h3></td>";
    echo "<td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.hull&amp;direction=desc\">Hull</a></strong></font></h3></td>";
    echo "<td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.engines&amp;direction=desc\">Engines</a></strong></font></h3></td>";
    if ($plasma_engines)
    {
        echo "<td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.pengines&amp;direction=desc\">Plasma Engines</a></strong></font></h3></td>";
    }

    echo "<td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.power&amp;direction=desc\">Power</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.computer&amp;direction=desc\">Computer</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.sensors&amp;direction=desc\">Sensors</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.armor&amp;direction=desc\">armor</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.shields&amp;direction=desc\">Shields</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.beams&amp;direction=desc\">Beams</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.torp_launchers&amp;direction=desc\">Torps</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=s.cloak&amp;direction=desc\">Cloak</a></strong></font></h3></td><td align='center'><h3><font color=white><strong><a href=\"team_report.php?orderby=p.score&amp;direction=desc\">Score</a></strong></font></h3>";
    echo "</tr><tr bgcolor=\"$color_line2\">";

    $result  = $db->Execute("SELECT * FROM {$db->prefix}players as p, {$db->prefix}ships as s WHERE p.team=? and s.player_id=p.player_id AND s.ship_id=p.currentship order by ? ?", array($whichteam, $orderby, $direction));
    while (!$result->EOF)
    {
        $member = $result->fields;
        // Consider caching here.
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ship_types WHERE type_id=?",1,-1, array($member['class']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $classstuff = $debug_query->fields;

        $hull = $member['hull'];
        $engines = $member['engines'];

        if ($plasma_engines)
        {
            $pengines = $member['pengines'];
        }

        $power = $member['power'];
        $computer = $member['computer'];
        $sensors = $member['sensors'];
        $armor = $member['armor'];
        $shields = $member['shields'];
        $beams = $member['beams'];
        $torps = $member['torp_launchers'];
        $cloak = $member['cloak'];
        $shipname = $member['name'];

        echo "<td align='center'><font style=\"font-size: 0.8em;\" color=\"#0CD616\"><strong>$member[character_name]</strong></font>";
        if ($member['player_id'] == $team['creator'])
        {
            echo "<br><font style=\"font-size: 0.8em;\" color=\"$main_table_heading\"><strong>$l_team_coord</strong></font>";
        }

        echo "<br><font style=\"font-size: 0.7em;\" color=\"#9ff4f8\"><strong>$shipname</strong></font><br><font style=\"font-size: 0.7em;\" color=\"#FFD161\"><strong>$classstuff[name]</strong></font><font style=\"font-size: 0.7em;\" color=\"$main_table_heading\"><strong> - </strong></font><font style=\"font-size: 0.7em;\" color=\"#61DFFF\"><strong>Class </strong></font><font style=\"font-size: 0.7em;\" color=\"$main_table_heading\"><strong>$member[class]</strong></font></td><td align='center'><font color=\"$colorarray[$hull]\"><strong>$hull</strong></font></td><td align='center'><font color=\"$colorarray[$engines]\"><strong>$engines</strong></font></td>";
        if ($plasma_engines)
        {
            echo "<td align='center'><font color=\"$colorarray[$pengines]\"><strong>$pengines</strong></font></td>";
        }

        echo "<td align='center'><font color=\"$colorarray[$power]\"><strong>$power</strong></font></td><td align='center'><font color=\"$colorarray[$computer]\"><strong>$computer</strong></font></td><td align='center'><font color=\"$colorarray[$sensors]\"><strong>$sensors</strong></font></td><td align='center'><font color=\"$colorarray[$armor]\"><strong>$armor</strong></font></td><td align='center'><font color=\"$colorarray[$shields]\"><strong>$shields</strong></font></td><td align='center'><font color=\"$colorarray[$beams]\"><strong>$beams</strong></font></td><td align='center'><font color=\"$colorarray[$torps]\"><strong>$torps</strong></font></td><td align='center'><font color=\"$colorarray[$cloak]\"><strong>$cloak</strong></font></td><td align='center'><font style=\"font-size: 0.8em;\" color=\"#0CD616\"><strong>$l_score</strong></font> <font style=\"font-size: 0.8em;\" color=\"$main_table_heading\"><strong>$member[score]</strong></font>";
        echo "</td></tr>";
        $result->MoveNext();
    }

    echo "</table>";
}
?>
