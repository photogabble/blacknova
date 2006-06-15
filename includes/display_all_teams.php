<?php
function display_all_teams($db)
{
    global $local_number_dec_point, $local_number_thousands_sep;
    dynamic_loader ($db, "sign.php");

    global $color_line1, $color_header, $order, $type, $l_team_galax, $l_team_member, $l_team_members;
    global $l_team_coord, $l_score, $l_name;

    echo "<br><br>$l_team_galax<br>";
    echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
    echo "<tr bgcolor=\"$color_header\">";

    if ($type == "d")
    {
        $type = "a";
        $by = "ASC";
    }
    else
    {
        $type = "d";
        $by = "DESC";
    }

    echo "<td><strong><a href=\"teams.php?order=team_name&amp;type=$type\">$l_name</a></strong></td>";
    echo "<td><strong><a href=\"teams.php?order=number_of_members&amp;type=$type\">$l_team_members</a></strong></td>";
    echo "<td><strong><a href=\"teams.php?order=character_name&amp;type=$type\">$l_team_coord</a></strong></td>";
    echo "<td><strong><a href=\"teams.php?order=total_score&amp;type=$type\">$l_score</a></strong></td>";
    echo "</tr>";
    $sql_query = "SELECT {$db->prefix}players.character_name, COUNT(*) as number_of_members, " .
                 "SUM({$db->prefix}players.score * ABS({$db->prefix}players.score) ) as total_score, " .
                 "{$db->prefix}teams.team_id, {$db->prefix}teams.team_name, {$db->prefix}teams.creator " .
                 "FROM {$db->prefix}players LEFT JOIN {$db->prefix}teams ON " .
                 "{$db->prefix}players.team = {$db->prefix}teams.team_id WHERE {$db->prefix}players.team = {$db->prefix}teams.team_id " .
                 "GROUP BY {$db->prefix}teams.team_name";

    // Setting if the order is Ascending or descending, if any. Default is ordered by teams.team_name.
    if ($order)
    {
        $sql_query = $sql_query ." ORDER BY " . $order . " $by";
    }
    $res = $db->Execute($sql_query);
//    echo $sql_query;
    while (!$res->EOF) 
    {
        $row = $res->fields;
        echo "<tr bgcolor=\"$color_line1\">";
        echo "<td><a href=\"teams.php?teamwhat=1&amp;whichteam=".$row['team_id']."\">".$row['team_name']."</a></td>";
        echo "<td>".$row['number_of_members']."</td>";
        $sql_query_2 = "SELECT character_name FROM {$db->prefix}players WHERE player_id = $row[creator]";
        $res2 = $db->Execute($sql_query_2);
        while (!$res2->EOF)
        {
            $row2 = $res2->fields;
            $res2->MoveNext();
        }

        echo "<td><a href=\"mailto.php?name=".$row2['character_name']."\">".$row2['character_name']."</a></td>";
        echo "<td>" . number_format(sign($row['total_score'], 0, $local_number_dec_point, $local_number_thousands_sep) * round(SQRT(abs($row['total_score'])))) . "</td>";
        echo "</tr>";
        $res->MoveNext();
    }

    echo "</table><br>";
}
?>
