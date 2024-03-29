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
// File: common.php

if (strpos($_SERVER['PHP_SELF'], 'common.php')) // Prevent direct access to this file
{
    die('Blacknova Traders error: You cannot access this file directly.');
}

if (!extension_loaded('mbstring')) // Test to ensure mbstring extension is loaded
{
    die ('Blacknova Traders Error: The PHP mbstring extension is required. Please install it.');
}

require_once './vendor/autoload.php';              // Load the auto-loader
require_once './global_defines.php';               // Defines used in many places
mb_http_output('UTF-8');                           // Our output should be served in UTF-8 no matter what.
mb_internal_encoding('UTF-8');                     // We are explicitly UTF-8, with Unicode language variables.
ini_set('include_path', '.');                      // Set include path to avoid issues on a few platforms
ini_set('session.use_only_cookies', 1);            // Ensure that sessions will only be stored in a cookie
ini_set('session.cookie_httponly', 1);             // Ensure that javascript cannot tamper with session cookies
ini_set('session.use_trans_sid', 0);               // Prevent session ID from being put in URLs
ini_set('session.entropy_file', '/dev/urandom');   // Use urandom as entropy source, to increase randomness
ini_set('session.entropy_length', '512');          // Increase the length of entropy gathered
ini_set('session.hash_function', 'sha512');        // Provides improved reduction for session collision
ini_set('session.hash_bits_per_character', 5);     // Explicitly set the number of bits per character of the hash
ini_set('url_rewriter.tags', '');                  // Do not pass Session id on the url for improved security on login
ini_set('default_charset', 'utf-8');               // Set PHP's default character set to utf-8

if (file_exists('dev'))                            // Create/touch a file named dev to activate development mode
{
    ini_set('error_reporting', -1);                // During development, output all errors, even notices
    ini_set('display_errors', 1);                  // During development, display all errors
}
else
{
    ini_set('error_reporting', 0);                 // Do not report errors
    ini_set('display_errors', 0);                  // Do not display errors
}

session_name('blacknova_session');                 // Change the default to defend better against session hijacking
date_default_timezone_set('UTC');                  // Set to your server's local time zone - Avoid a PHP notice
                                                   // Since header is now temlate driven, these weren't being passed
                                                   // along except on old crusty pages. Now everthing gets them!
header('Content-type: text/html; charset=utf-8');  // Set character set to utf-8, and using HTML as our content type
header('X-UA-Compatible: IE=Edge, chrome=1');      // IE - use the latest rendering engine (edge), and chrome shell
header('Cache-Control: public');                   // Tell browser and caches that it's ok to store in public caches
header('Connection: Keep-Alive');                  // Tell browser to keep going until it gets all data, please
header('Vary: Accept-Encoding, Accept-Language');  // Tell CDN's or proxies to keep a separate version of the page in
                                                   // various encodings - compressed or not, in english or french
                                                   // for example.
header('Keep-Alive: timeout=15, max=100');         // Ask for persistent HTTP connections (15sec), which give better
                                                   // per-client performance, but can be worse (for a server) for many
ob_start(array('Bnt\Compress', 'compress'));       // Start a buffer, and when it closes (at the end of a request),
                                                   // call the callback function 'bnt\Compress' to properly handle
                                                   // detection of compression.

$pdo_db = new Bnt\Db;
$pdo_db = $pdo_db->initDb('pdo');                  // Connect to db using pdo
$db = new Bnt\Db;
$db = $db->initDb('adodb');                        // Connect to db using adodb also - for now - to be eliminated!

$bntreg = new Bnt\Reg($pdo_db);                    // BNT Registry object -  passing config variables via classes
$bntreg->bnttimer = new Bnt\Timer;                 // Create a benchmark timer to get benchmarking data for everything
$bntreg->bnttimer->start();                        // Start benchmarking immediately
$langvars = null;                                  // Language variables in every page, set them to a null value first
$template = new \Bnt\Template();                   // Template API.
$template->setTheme($bntreg->default_template);    // Set the name of the theme, temporary until we have a theme picker

$bnt_session = new Bnt\Sessions($pdo_db);

if (!isset($index_page))
{
    $index_page = false;
    // Ensure that we do not start sessions on the index page (or pages likely to have no db),
    // until the player chooses to allow them or until the db exists.
    if (!isset($_SESSION))
    {
        session_start();
    }
}

if (isset($bntreg->default_lang))
{
    $lang = $bntreg->default_lang;
}

if (Bnt\Db::isActive($pdo_db))
{
    if (empty($_SESSION['username']))              // If the user has not logged in
    {
        if (array_key_exists('lang', $_GET))       // And the user has chosen a language on index.php
        {
            $lang = $_GET['lang'];                 // Set $lang to the language the user has chosen
        }
    }
    else // The user has logged in, so use his preference from the database
    {
        $players_gateway = new \Bnt\Players\PlayersGateway($pdo_db); // Build a player gateway object to handle the SQL calls
        $playerinfo = $players_gateway->selectPlayerInfo($_SESSION['username']);
        $lang = $playerinfo['lang'];
    }
}

// Initialize the Plugin System.
Bnt\PluginSystem::initialize($pdo_db);

// Load all Plugins.
Bnt\PluginSystem::loadPlugins();

// Ok, here we raise EVENT_TICK which is called every page load, this saves us from having to add new lines to
// support new features. This is used for ingame stuff and Plug-ins that need to be called on every page load.
// May need to change array(time()) to have extra info, but the current suits us fine for now.
Bnt\PluginSystem::raiseEvent(EVENT_TICK, array(time()));
?>
