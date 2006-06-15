<?php
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
