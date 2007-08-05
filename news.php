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
// File: news.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "translate_news.php");

// Load language variables
load_languages($db, $raw_prefix, 'news');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

$title = $l_news_title;
include_once ("./header.php");

global $local_date_short_format;
// Check to see if the date was passed in the query string

if ((!isset($_GET['startdate'])) || ($_GET['startdate'] == ''))
{
    // The date wasn't supplied so use today's date
    $_GET['startdate'] = date("Y-m-d");
}

// Convert the formatted date into a timestamp, and subtract one day in seconds
$previousday = strtotime($_GET['startdate']) - 86400;
// Return the final amount formatted as YYYY-MM-DD
$previousday = date("Y-m-d",$previousday);

// Convert the formatted date into a timestamp, and add one day in seconds
$nextday = strtotime($_GET['startdate']) + 86400;
// Return the final amount formatted as YYYY-MM-DD
$nextday = date("Y-m-d",$nextday);

$month = substr($_GET['startdate'], 5, 2);
$day = substr($_GET['startdate'], 8, 2);
$year = substr($_GET['startdate'], 0, 4);
$today = adodb_mktime (0,0,0,$month,$day,$year);
$today = date($local_date_short_format, $today);

$news_array = array();
// Select news for date range
$res = $db->Execute("SELECT * FROM {$db->prefix}news WHERE date like ? order by news_id", array($_GET['startdate'].'%'));

// Check to see if there was any news to be shown
if ($res->EOF && $res)
{
    // No news
    $news_array = "";
}
else
{
    while (!$res->EOF && $res)
    {
        $row = $res->fields;
        $newsdata = translate_news($row);
        array_push($news_array, $newsdata);
        $res->MoveNext();
    }
}

$template->assign("l_news_prev", $l_news_prev);
$template->assign("l_news_next", $l_news_next);
$template->assign("nextday", $nextday);
$template->assign("previousday", $previousday);
$template->assign("today", $today);
$template->assign("l_news_for", $l_news_for);
$template->assign("l_news_info1", $l_news_info1);
$template->assign("l_news_info2", $l_news_info2);
$template->assign("l_news_info3", $l_news_info3);
$template->assign("l_news_info4", $l_news_info4);
$template->assign("l_news_info5", $l_news_info5);
$template->assign("templateset", $templateset);
$template->assign("l_news_none", $l_news_none);
$template->assign("l_news_flash", $l_news_flash);
$template->assign("news_array", $news_array);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->assign("l_global_mlogin", $l_global_mlogin);
$template->assign("session_email", empty($_SESSION['email']));
$template->display("$templateset/news.tpl");

include_once ("./footer.php");
?>
