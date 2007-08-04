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
// File: includes/translate_news.php
// All functions assume $day is a valid formatted time
function translate_news($entry)
{
    global $l_news_p_headline, $l_news_p_text_small, $l_news_p_text_big, $l_news_p_text_huge; // planet stuff.
    global $l_news_c_headline, $l_news_c_text_small, $l_news_c_text_big, $l_news_c_text_huge; // colonist stuff.

    global $l_killheadline, $l_news_killed; // Player killed
    global $l_made_galaxy, $l_made_galaxy_full; // Galaxy created

    if (strpos($entry['news_type'], "planet") !== false)
    {
        $planet_count = substr($entry['news_type'], 6); // 6 is the number of letters in the word "Planet" - the news type
        $l_news_p_headline2 = str_replace("[player]", $entry['news_data'], $l_news_p_headline);
        $l_news_p_headline2 = str_replace("[number]", $planet_count, $l_news_p_headline2);
        $retvalue['headline']  = $l_news_p_headline2;

        if ($planet_count < 50)
        {
            $l_news_p_text_small2 = str_replace("[name]", $entry['news_data'], $l_news_p_text_small);
            $retvalue['newstext'] = str_replace("[number]", $planet_count, $l_news_p_text_small2);
        }
        elseif ($planet_count < 500)
        {
            $l_news_p_text_big2 = str_replace("[name]", $entry['news_data'], $l_news_p_text_big);
            $retvalue['newstext'] = str_replace("[number]", $planet_count, $l_news_p_text_big2);
        }
        else
        {
            $l_news_p_text_huge2 = str_replace("[name]", $entry['news_data'], $l_news_p_text_huge);
            $retvalue['newstext'] = str_replace("[number]", $planet_count, $l_news_p_text_huge2);
        }
    }

    if (strpos($entry['news_type'], "col") !== false)
    {
        $colonist_count = substr($entry['news_type'], 3); // 3 is the number of letters in the word "COLonists" - the news type
        $l_news_c_headline2 = str_replace("[player]", $entry['news_data'], $l_news_c_headline);
        $l_news_c_headline2 = str_replace("[number]", $colonist_count, $l_news_c_headline2);
        $retvalue['headline']  = $l_news_c_headline2;
        if ($colonist_count < 50)
        {
            $l_news_c_text_small2 = str_replace("[name]", $entry['news_data'], $l_news_c_text_small);
            $retvalue['newstext'] = str_replace("[number]", $colonist_count, $l_news_c_text_small2);
        }
        elseif ($colonist_count < 500)
        {
            $l_news_c_text_big2 = str_replace("[name]", $entry['news_data'], $l_news_c_text_big);
            $retvalue['newstext'] = str_replace("[number]", ($colonist_count /1000), $l_news_c_text_big2);
        }
        else
        {
            $l_news_c_headline3 = str_replace("[player]", $entry['news_data'], $l_news_c_headline_huge);
            $l_news_c_headline3 = str_replace("[number]", ($colonist_count /1000), $l_news_c_headline3);
            $retvalue['headline'] = $l_news_c_headline3;
            $l_news_c_text_huge2 = str_replace("[name]", $entry['news_data'], $l_news_c_text_huge);
            $retvalue['newstext'] = str_replace("[number]", ($colonist_count /1000), $l_news_c_text_huge2);
        }
    }

    switch ($entry['news_type'])
    {
        case "creation":
            $retvalue['headline']  = $l_made_galaxy;
            $retvalue['newstext'] = $l_made_galaxy_full;
        break;

        case "killed":
            $retvalue['headline']  = $entry['news_data'] . $l_killheadline;
            $retvalue['newstext'] = str_replace("[name]", $entry['news_data'], $l_news_killed);
        break;
    }

    return $retvalue;
}
?>
