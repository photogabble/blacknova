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
// File: ranking.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "player_insignia_name.php"); 
dynamic_loader ($db, "gen_score.php");

// Load language variables
load_languages($db, $raw_prefix, 'ranking');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'teams');

$title = $l_ranks_title;
include_once ("./header.php");

echo "<h1>" . $title. "</h1>\n";

//-------------------------------------------------------------------------------------------------
if ($raw_prefix != $db->prefix)
{
    if (!empty($_SESSION['email']))
    {
        global $db;
        $debug_query = $db->SelectLimit("SELECT account_id FROM {$raw_prefix}users WHERE email=?",1,-1,array($_SESSION['email']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $account_id = $debug_query->fields['account_id'];

        $debug_query = $db->SelectLimit("SELECT player_id FROM {$db->prefix}players WHERE account_id=?",1,-1,array($account_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        gen_score($db,$debug_query->fields['player_id']);
    }

    if (!isset($_GET['sort']))
    {
        $_GET['sort'] = '';
    }

    if ($_GET['sort'] == "turns")
    {
        $by = "turns_used DESC,character_name ASC";
    }
    elseif ($_GET['sort'] == "login")
    {
        $by = "last_login DESC,character_name ASC";
    }
    elseif ($_GET['sort'] == "good")
    {
        $by = "rating DESC,character_name ASC";
    }
    elseif ($_GET['sort'] == "bad")
    {
        $by = "rating ASC,character_name ASC";
    }
    elseif ($_GET['sort'] == "team")
    {
        $by = "{$db->prefix}teams.team_name DESC, character_name ASC";
    }
    elseif ($_GET['sort'] == "efficiency")
    {
        $by = "efficiency DESC";
    }
    else
    {
        $by = "score DESC,character_name ASC";
    }

    if ($hide_admin_rank == 1)
    {
        $query = " AND {$raw_prefix}users.email NOT LIKE " . $db->qstr($admin_mail);
    }
    else
    {
        $query = " ";
    }

    echo $l_header_title;
    // The only non-functional portion is sort on efficiency on postgres.
    $res = sql_ranking($db, $query, $by, $max_rank);
    $num_players = $res->RecordCount();

    if ((!isset($_GET['offset'])) || ($_GET['offset'] == ''))
    {
        $_GET['offset'] = 0;
    }

    if ($_GET['offset'] >= $num_players)
    {
        $_GET['offset'] = $num_players-$max_rank;
    }

    if ($_GET['offset'] < 1)
    {
        $_GET['offset'] = 0;
    }

    //-------------------------------------------------------------------------------------------------

    $i = 1;
    $offset_n_max = $_GET['offset'] + $max_rank;
    $offset_m_max = $_GET['offset'] - $max_rank;
    if ($offset_n_max > $num_players)
    {
        $offset_n_max = $num_players;
    }

    if ($offset_m_max < 0)
    {
        $offset_m_max = 0;
    }

    if (!$res)
    {
        echo "$l_ranks_none<br>\n";
    }
    else
    {
        echo "<br>Total number of players: " . number_format($num_players , 0, $local_number_dec_point, $local_number_thousands_sep);
        echo "<br>Displaying players " . number_format($_GET['offset']+1, 0, $local_number_dec_point, $local_number_thousands_sep) . " to " . number_format($offset_n_max, 0, $local_number_dec_point, $local_number_thousands_sep);
        echo " <a href=\"ranking.php?offset=". $offset_m_max ."\">&lt;</a> ";
        echo " <a href=\"ranking.php?offset=". $offset_n_max ."\">&gt;</a>";
        echo "<br>$l_ranks_dships";
        echo "<br><br>\n";
        echo "<table border=0 cellspacing=0 cellpadding=4>\n";
        echo " <tbody>\n";
        echo "  <tr bgcolor=\"$color_header\">\n";
        echo "    <td><strong>$l_ranks_standing</strong></td>\n";
        echo "    <td><strong><a href=\"ranking.php\">$l_score</a></strong></td>\n";
        echo "    <td><strong>$l_ranks_rank</strong></td>\n";
        echo "    <td><strong>$l_player</strong></td>\n";
        echo "    <td><strong><a href=\"ranking.php?sort=good\">$l_ranks_good</a> \ <a href=\"ranking.php?sort=bad\">$l_ranks_evil</a></strong></td>\n";
        echo "    <td><strong><a href=\"ranking.php?sort=team\">$l_team</a></strong></td>\n";
        echo "    <td><strong><a href=\"ranking.php?sort=login\">Online</a></strong></td>\n";
        echo "    <td><strong><a href=\"ranking.php?sort=efficiency\">Eff. Rating</a></strong></td>\n";
        echo "    <td><strong><a href=\"ranking.php?sort=turns\">$l_turns_used</a></strong></td>\n";
        echo "    <td><strong><a href=\"ranking.php?sort=login\">$l_ranks_lastlog</a></strong></td>\n";
        echo "  </tr>\n";
        $color = $color_line1;
        $fun = $res->Move($_GET['offset']);
        while ($i <= ($max_rank) && ($i+$_GET['offset'] <= $num_players))
        {
            $row = $res->fields;
            $rating_sign = 0;
            if ($row['rating'] > 0)
            {
                $rating_sign = 1;
            }
            elseif ($row['rating'] < 0)
            {
                $rating_sign = -1;
            }
            else
            {
                $rating_sign = 0;
            }

            $rating = $rating_sign * round(sqrt(abs($row['rating'])));

            $session_kill = time() - $session_time_out;
            $time = $row['last_login'];
            $online = "";
            if ($time > $session_kill)
            {
                $online = "Online";
            }

            $temp_turns = $row['turns_used'];
            if ($temp_turns <= 0)
            {
                $temp_turns = 1;
            }

            $lastlogin = date($local_date_full_format, $row['last_login']);

            echo "  <tr bgcolor=\"$color\">\n";
            echo "    <td>" . number_format($i+$_GET['offset'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
            echo "    <td>" . number_format($row['score'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
            $player_insignia = player_insignia_name($db,$row['email']);
            if ($player_insignia['rank_icon'] == 0)
            {                 
                $player_insignia['rank_icon'] = 1;
            }

            echo "    <td nowrap=\"nowrap\"><img src=\"templates/$templateset/images/rank/" . $player_insignia['rank_icon'] . ".png\" align=\"middle\" alt=\"" . $player_insignia['rank_name'] . "\"></td>";
            echo "    <td><strong>" . $row['character_name'] . "</strong></td>\n";
            echo "    <td>" . number_format($rating, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
            echo "    <td>" . $row['team_name'] . "</td>\n";
            echo "    <td>" . $online . "</td>\n";

            if ($row['turns_used'] >= 150)
            {
                $res2 = $db->Execute("SELECT ROUND({$db->prefix}players.score/{$db->prefix}players.turns_used) ".
                                     "AS efficiency FROM {$db->prefix}players WHERE player_id=?", array($row['player_id']));
                db_op_result($db,$res2,__LINE__,__FILE__);

                $row2 = $res2->fields;
                echo "    <td>" . $row2['efficiency'] . "</td>\n";
            }
            else
            {
                echo "    <td>" . 0 . "</td>\n";
            }

            echo "    <td>" . number_format($row['turns_used'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
            echo "    <td>" . $lastlogin . "</td>\n";
            echo "  </tr>\n";

            if ($color == $color_line1)
            {
                $color = $color_line2;
            }
            else
            {
                $color = $color_line1;
            }

            $res->MoveNext();
            $i++;
        }
        echo "</tbody></table>\n";
    }

    echo "<br>\n";

    if (empty($_SESSION['email']))
    {
        global $l_global_mlogin;
        echo "<a href=\"index.php\">" . $l_global_mlogin . "</a>";
    }
    else
    {
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    }
}

$debug_query = $db->Execute("SELECT gamenumber FROM {$raw_prefix}instances ORDER BY gamenumber ASC");
db_op_result($db,$debug_query,__LINE__,__FILE__);
while (!$debug_query->EOF)
{
    $gamenumber = $debug_query->fields['gamenumber'];
    $game_instances[$gamenumber] = $l_gamehash . $gamenumber;
    $debug_query->MoveNext();
}

$template->assign('game_instances' , $game_instances);
$template->assign("title", $title);
$template->assign("l_notsetup", $l_notsetup);
$template->assign("l_submit", $l_submit);
$template->assign("dbprefix", $db->prefix);
$template->assign("raw_prefix", $raw_prefix);
$template->display("$templateset/rankings.tpl");

include_once ("./footer.php");
?>
