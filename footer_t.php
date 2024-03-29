<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: footer_t.php

$online = (int) 0;

if (Bnt\Db::isActive($pdo_db))
{
    $stamp = date("Y-m-d H:i:s", time()); // Now (as seen by PHP)
    $since_stamp = date("Y-m-d H:i:s", time() - 5 * 60); // Five minutes ago
    $players_gateway = new \Bnt\Players\PlayersGateway($pdo_db); // Build a player gateway object to handle the SQL calls
    $online = $players_gateway->selectPlayersLoggedIn($since_stamp, $stamp); // Online is the (int) count of the numbers of players currently logged in via SQL select
}

$elapsed = (int) 999; // Default value for elapsed, overridden with an actual value if its available
if (isset ($bntreg))
{
    if (property_exists($bntreg, 'bnttimer'))
    {
        $bntreg->bnttimer->stop();
        $elapsed = $bntreg->bnttimer->elapsed();
    }
}

// Suppress the news ticker on the IGB and index pages
$news_ticker_active = (!(preg_match("/index.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF']) || preg_match("/new.php/i", $_SERVER['PHP_SELF'])));

// Suppress the news ticker if the database is not active
if (!Bnt\Db::isActive($pdo_db))
{
    $news_ticker_active = false;
}

// Update counter
$scheduler_gateway = new \Bnt\Scheduler\SchedulerGateway($pdo_db); // Build a scheduler gateway object to handle the SQL calls
$last_run = $scheduler_gateway->selectSchedulerLastRun(); // Last run is the (int) count of the numbers of players currently logged in via SQL select or false if DB is not active
if ($last_run !== false)
{
    $seconds_left = ($bntreg->sched_ticks * 60) - (time() - $last_run);
    $display_update_ticker = true;
}
else
{
    $seconds_left = (int) 0;
    $display_update_ticker = false;
}
// End update counter

if ($bntreg->footer_show_debug == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
{
    $sf_logo_type = '14';
    $sf_logo_width = "150";
    $sf_logo_height = "40";
}
else
{
    $sf_logo_type = '11';
    $sf_logo_width = "120";
    $sf_logo_height = "30";
}

if ($news_ticker_active == true)
{
    // Database driven language entries
    $langvars_temp = Bnt\Translate::load($pdo_db, $lang, array('news', 'common', 'footer', 'global_includes', 'logout'));

    // Use Array merge so that we do not clobber the langvars array, and only add to it the items needed for footer
    $langvars = array_merge($langvars, $langvars_temp);

    // Use Array unique so that we don't end up with duplicate lang array entries
    // This is resulting in an array with blank values for specific keys, so array_unique isn't entirely what we want
    // $langvars = array_unique ($langvars);

    // SQL call that selects all of the news items between the start date beginning of day, and the end of day.
    $news_gateway = new \Bnt\News\NewsGateway($pdo_db); // Build a scheduler gateway object to handle the SQL calls
    $row = $news_gateway->selectNewsByDay(date('Y-m-d'));

    $news_ticker = array();
    if (count($row) == 0)
    {
        array_push($news_ticker, array('url' => null, 'text' => $langvars['l_news_none'], 'type' => null, 'delay' => 5));
    }
    else
    {
        foreach($row as $item)
        {
            array_push($news_ticker, array('url' => "news.php", 'text' => $item['headline'], 'type' => $item['news_type'], 'delay' => 5));
        }
        array_push($news_ticker, array('url'=>null, 'text' => "End of News", 'type' => null, 'delay' => 5));
    }
    $news_ticker['container']    = "article";
    $template->addVariables("news", $news_ticker);
}
else
{
    $sf_logo_type++; // Make the SF logo darker for all pages except login. No need to change the sizes as 12 is the same size as 11 and 15 is the same size as 14.
}

if (!array_key_exists('lang', $_GET))
{
    $sf_logo_link = null;
}
else
{
    $sf_logo_link = "?lang=" . $_GET['lang'];
}

$mem_peak_usage = floor(memory_get_peak_usage() / 1024);
$public_pages = array( 'ranking.php', 'new.php', 'faq.php', 'settings.php', 'news.php', 'index.php');
$slash_position = mb_strrpos($_SERVER['PHP_SELF'], '/') + 1;
$current_page = mb_substr($_SERVER['PHP_SELF'], $slash_position);
if (in_array($current_page, $public_pages))
{
    // If it is a non-login required page, such as ranking, new, faq, settings, news, and index use the public SF logo, which increases project stats.
    $variables['suppress_logo'] = false;
}
else
{
    // Else suppress the logo, so it is as fast as possible.
    $variables['suppress_logo'] = true;
}

// Set array with all used variables in page
$variables['update_ticker'] = array("display" => $display_update_ticker, "seconds_left" => $seconds_left, "sched_ticks" => $bntreg->sched_ticks);
$variables['players_online'] = $online;
$variables['sf_logo_type'] = $sf_logo_type;
$variables['sf_logo_height'] = $sf_logo_height;
$variables['sf_logo_width'] = $sf_logo_width;
$variables['sf_logo_link'] = $sf_logo_link;
$variables['elapsed'] = $elapsed;
$variables['mem_peak_usage'] = $mem_peak_usage;
$variables['footer_show_debug'] = $bntreg->footer_show_debug;
$variables['cur_year'] = date('Y');
?>
