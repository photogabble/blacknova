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
// File: footer.php

$included_footer = true;

// Dynamic functions
dynamic_loader ($db, "return_print_r.php");

$pos = (strpos($_SERVER['PHP_SELF'], "/footer.php"));
if ($pos !== false)
{
    include_once ("global_includes.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once ("footer.php");
    die();
}

global $l_footer_one_player_on, $l_footer_no_players_on, $l_footer_players_on_1, $l_footer_players_on_2, $l_news_none;
global $db ,$sched_ticks, $langdir, $create_universe, $sched_type, $l_main_noscript;
global $templateset, $raw_prefix;

if (isset($langdir))
{
    if (!isset($_POST['step']) || $_POST['step'] >2) // Prevent loading languages before db loads.
    {
        // Load language variables
        load_languages($db, $raw_prefix, 'footer');
        load_languages($db, $raw_prefix, 'common');
        load_languages($db, $raw_prefix, 'news');
        load_languages($db, $raw_prefix, 'main');
    }
}

$online = 0;
$timeleft = TIME();
$mySEC = 10000;

$adminnews = '';
$news_array = '';
$url_array = '';

if (is_object($db) && ($raw_prefix != $db->prefix))
{
    $newspath = "news.php";
    $startdate = date("Y-m-d"); // Do not change it if you don't know what you are doing!

    // Players online
    global $session_time_out;
    $session_kill = time() - $session_time_out;
    $session_stamp = date("Y-m-d H:i:s", time()+$session_time_out);
    $debug_query = $db->Execute("SELECT character_name from {$db->prefix}players WHERE last_login < ?", array($session_stamp));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    if ($debug_query) // Returns false if db isnt setup yet.
    {
        $online = $debug_query->RecordCount();
    }

    // Admin News
//    $debug_query = $db->CacheExecute("SELECT * FROM {$db->prefix}alerts ORDER BY an_id DESC"); -- causes crazy mad errors with caching..
    $debug_query = $db->Execute("SELECT * FROM {$db->prefix}alerts ORDER BY an_id DESC");
    if ($debug_query && !$debug_query->EOF) // Returns false if db isnt setup yet.
    {
        $row = $debug_query->fields;
        $adminnews = $row['an_text'];
    }

    if ((!isset($online)) || ($online == ''))
    {
        $online = 0;
    }

    if ($online == 1)
    {
        $players_online = "  " . $l_footer_one_player_on . " ";
    }
    elseif ($online == 0)
    {
        $players_online = "  " . $l_footer_no_players_on . " ";
    }
    else
    {
        $players_online = "  " . $l_footer_players_on_1 . " " . $online . " " . $l_footer_players_on_2;
    }

    $res = $db->Execute("SELECT * FROM {$db->prefix}news WHERE date LIKE ? ORDER BY news_id", array($startdate . '%')); // Matches based on y-m-d.
    if ($res)
    {
        if ($adminnews != '')
        {
            $url_array = $url_array . "\"" . $newspath . "\","; // add one manually for the link for admin notices.
        }

        $url_array = $url_array . "\"" . $newspath . "\","; // add one manually for the link for Users online.
        if ($res->EOF)
        {
            $url_array = $url_array . "\"". $newspath . "\"";
        }
        else
        {
            while (!$res->EOF)
            {
                $row = $res->fields;
                $url_array = $url_array . "\"$newspath\",";
                $res->MoveNext();
            }

            $url_array = $url_array . "\"$newspath\"";
        }

        // Here is the php function to populate the javascript array.

        if ($adminnews != '')
        {
            $news_array = $news_array . "\"" . "ALERT: " . $adminnews . "\",";
        }

        if ($players_online != '')
        {
            $news_array = $news_array . "\"" . "Info:" . $players_online . "\"";
        }

        if ($res->EOF)
        {
            global $l_news_none;
            $news_array = $news_array . "," . "\"" . $l_news_none . "\"";
        }
        else
        {
            while (!$res->EOF)
            {
                $row = $res->fields;
                // Dynamic functions
                dynamic_loader ($db, "translate_news.php");
                $newsdata = translate_news($row);
                $news_array = $news_array . "," . "\"". "News: " . $newsdata['headline'] ."\"";
                $res->MoveNext();
            }
        }

        $template->assign("url_array", $url_array);
        $template->assign("news_array", $news_array);
        $template->assign("l_main_noscript", $l_main_noscript);
    }
}

// Functions end

if (is_object($db) && ($raw_prefix != $db->prefix))
{
    // Time left til next update
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    $debug_query = $db->Execute("SELECT last_run FROM {$db->prefix}scheduler"); // perfmon shows this to be faster than selectlimit.
    if ($debug_query && !$debug_query->EOF) // Returns false if db isnt setup yet. 
    {
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $row = $debug_query->fields;
        $debug_query2 = $db->UnixTimeStamp($row['last_run']);
        db_op_result($db,$debug_query2,__LINE__,__FILE__);
        $timeleft = $debug_query2;
    }

    $mySEC = ($sched_ticks * 60) - (TIME()-$timeleft);
    if (($mySEC <= 0) || ($mySEC == ($sched_ticks * 60)))
    {
        $mySEC = 10000;
    }
}

if (is_object($db))
{
    if (!isset($sectorinfo))
    {
        $sectorinfo['sector_id'] = '';
    }

    if (!isset($playerinfo))
    {
        $playerinfo['player_id'] = '';
    }

    $swordfish = '';
    if ((!isset($_POST['swordfish'])) ||  $_POST['swordfish'] == '')
    {
        $_POST['swordfish'] = '';
    }
    else
    {
        $swordfish = $_POST['swordfish'];
    }

    if ((!isset($_GET['swordfish'])) ||  $_GET['swordfish'] == '')
    {
        $_GET['swordfish'] = '';
    }
    else
    {
        $swordfish = $_GET['swordfish'];
    }

    if (!isset($_SERVER['HTTP_REFERER']))
    {
        $_SERVER['HTTP_REFERER'] = '';
    }

    if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) === false)
    {
        $referer = $_SERVER['HTTP_REFERER']; // Record where they came from if it didnt come from this server address.
    }
    elseif (strpos($_SERVER['HTTP_REFERER'], dirname($_SERVER['PHP_SELF'])))
    {
        $referer =  substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], '/')+1)); // Just record the page name if it was in the same folder.
    }
    else
    {
        $referer =  substr($_SERVER['HTTP_REFERER'], (strpos($_SERVER['HTTP_REFERER'], '/',8))); // Otherwise, just remove the domain - since its the same.
    }

    $stamp = date("Y-m-d H:i:s");

    if (!isset($_SERVER['REQUEST_URI']))
    {
        $_SERVER['REQUEST_URI'] = '';
    }

    if (basename($_SERVER['PHP_SELF']) == 'scheduler.php'  && $swordfish == $adminpass)
    {
    }
    elseif ($_SERVER['REQUEST_URI'] !='')
    {
        $truncated_url = substr($_SERVER['REQUEST_URI'], (strrpos($_SERVER['REQUEST_URI'], '/')+1));
        $postvars = '';
        if (!empty($_POST))
        {
            $postvars = return_print_r($_POST);
        }

        $getvars = '';
        if (!empty($_GET))
        {
            $getvars = return_print_r($_GET);
        }

        // We don't use these checks, so just set them blank.

        // include the phpsniff backend
        include_once ("./backends/phpsniff/phpSniff.class.php");

        $sniffer_settings = array('check_cookies'=>'',
                                  'default_language'=>'',
                                  'allow_masquerading'=>'');
        $browser_sniff =& new phpSniff('',$sniffer_settings); // We don't use the UA check, so just set it blank.
        $browser['os'] = $browser_sniff->property('os');
        $browser['platform'] = $browser_sniff->property('platform');
        $browser['useragent'] = $browser_sniff->property('ua');
        $browser['browser'] = $browser_sniff->property('long_name');
        $browser['version'] = $browser_sniff->property('version');

        $ip_address = getenv("REMOTE_ADDR"); // Get IP address for user
        $proxy_address = getenv("HTTP_X_FORWARDED_FOR"); // Get Proxy IP address for user
        $client_ip_address = getenv("HTTP_CLIENT_IP"); // Get http's IP address for user

        $debug_query = $db->Execute("INSERT INTO {$db->prefix}ip_log (player_id, ip_address, proxy_address, client_ip_address, referer, getvars, " . 
                                    "postvars, sector_id, url, time, os, platform, useragent, browser, bversion) " . 
                                    "VALUES " .
                                    "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array ($playerinfo['player_id'], $ip_address, $proxy_address, $client_ip_address, $referer, $getvars, $postvars, $sectorinfo['sector_id'], $truncated_url, $stamp, $browser['os'], $browser['platform'], $browser['useragent'], $browser['browser'], $browser['version']));
        if ($debug_query && !$debug_query->EOF) // Returns false if db isnt setup yet. 
        {
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
    else
    {
    }
}

$template->assign("seconds_until_update", $mySEC);

if (isset($langdir))
{
    global $l_footer_until_update, $l_footer_bad_updates, $l_footer_view_source;
    $template->assign("l_footer_until_update", $l_footer_until_update);
    $template->assign("l_footer_bad_updates", $l_footer_bad_updates);
    $template->assign("l_footer_view_source", $l_footer_view_source);
}

// Take the buffer contents, md5 it, and that is our etag - which lets caches check against 'has file changed'.
// This has to happen BEFORE the output of players online, and seconds until update, otherwise it would never match.
if (ob_get_length())
{
    $etag = md5(ob_get_contents());
    header("ETag: " . $etag);
}

$template->assign("scheduler_ticks", $sched_ticks);
$template->assign("sched_type", $sched_type);
$template->assign("sourcefile", basename($_SERVER['PHP_SELF']));
if (!isset($view_source))
{
    $view_source = FALSE;
}

$template->assign("view_source", $view_source);

global $BenchmarkTimer;
if (is_object($BenchmarkTimer))
{
    $stoptime = $BenchmarkTimer->stop();
    $elapsed = $BenchmarkTimer->elapsed();
    $elapsed = substr($elapsed,0,5);
}
else
{
    $elapsed = 999;
}
$template->assign("gen_time", $elapsed);

if (basename($_SERVER['PHP_SELF']) == 'make_galaxy.php')
{
    if (isset($_POST['step']) && $_POST['step'] >2) // Prevent loading languages before db loads.
    {
        if (!isset($_SESSION['total_elapsed']))
        {
            $_SESSION['total_elapsed'] = 0;
        }
        else
        {
            $_SESSION['total_elapsed'] = $_SESSION['total_elapsed'] + $elapsed;
        }

        $template->assign("total_elapsed", $_SESSION['total_elapsed']);
    }
}

if ($db=='')
{
    $template->assign("dbprefix", '');
}
else
{
    $template->assign("dbprefix", $db->prefix);
}

$template->assign("raw_prefix", $raw_prefix);
$template->display("$templateset/footer.tpl");

$size='';
if (is_object($db) && ($raw_prefix != $db->prefix))
{
    if (isset($perf_logging) && $perf_logging)
    {
        $db->LogSQL(false);
    }

    $db->close();
}

$send_body = TRUE;

// ob_get_level will be higher than 1 if output_buffering or ob_gzhandler is enabled in php.ini, which would result in an incorrect buffer length.

$output = '';

$zl = ini_get( 'zlib.output_compression' );
$minLevel = empty($zl) ? 0 : 1;

while (ob_get_level() > $minLevel)
{
    $output .= ob_get_contents();
    ob_end_clean();
}

$outlength = strlen($output);
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && !$zl)
{
    if (eregi("gzip",$_SERVER['HTTP_ACCEPT_ENCODING']))
    {
        header("Content-Encoding: gzip");
        $output = gzencode($output,9);
    }
}

if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
{
    $inm = explode(',', $_SERVER['HTTP_IF_NONE_MATCH']);
    foreach ($inm as $i)
    {
        if (trim($i) == $etag)
        {
            $send_body = FALSE;
            break;
        }
    }
}

if (!$send_body)
{
    header ("HTTP/1.0 304 Not Modified");
}
else
{
    // Set the last modified date to now, since we just created it.
    $last_modified = gmdate("D, d M Y H:i:s");

    // Headers to send on all pages
    // P3P Compliance - coming soon.
    //header("P3P: policyref=\"" . $url . "w3c/p3p.xml\"");
//    header("Last-Modified: $last_modified");

    // This content type header overrides apache if it is pre-setting the value. There is a serious xss vulnerability
    // that apache is preventing by doing so.
    header("Content-type: text/html; charset=utf-8");
    header("Content-Length: $outlength");
    header("Pragma: public");
    header("Cache-Control: public"); // Tell the client (and any caches) that this information can be stored in public caches.
    header("Connection: Keep-Alive"); // Tell the client to keep going until it gets all data, please.
    header("Keep-Alive: timeout=15, max=100");
    // This header still causes issues on IE6, and I dont understand why. We need this header, so we need to figure out why its causing the problem, 
    // and fix it.
    // header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT"); // Tell the client that this page expires "now". This overrides incorrect apache info.
    echo $output;
}

exit; // To prevent pop-up windows ;)
?>
