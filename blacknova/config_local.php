<?php

// Path on the filesystem where the blacknova files
// will reside:
$gameroot = "/home/bnt1/www";

// The ADOdb db module is now required to run BNT. You
// can find it at http://php.weblogs.com/ADODB. Enter the
// path where it is installed here. I suggest simply putting
// every ADOdb file in a subdir of BNT.
$ADOdbpath = "/mysqldata/adodb";

// Domain & path of the game on your webserver (used to validate login cookie)
// This is the domain name part of the URL people enter to access your game.
// So if your game is at www.blah.com you would have:
// $gamedomain = "www.blah.com";
// Do not enter slashes for $gamedomain or anything that would come after a slash
// if you get weird errors with cookies then make sure the game domain has TWO dots
// i.e. if you reside your game on http://www.blacknova.net put .blacknova.net as $gamedomain. If your game is on http://www.some.site.net put .some.site.net as your game domain. Do not put port numbers in $gamedomain.
$gamedomain = "khaoskamp.ucs.umbc.edu";

// This is the trailing part of the URL, that is not part of the domain.
// If you enter www.blah.com/blacknova to access the game, you would leave the line as it is.
// If you do not need to specify blacknova, just enter a single slash eg:
// $gamepath = "/";
$gamepath = "/~bnt2/";

// Hostname and port of the database server:
// These are defaults, you normally won't have to change them
$dbhost = "localhost";

// Note : if you do not know the port, set this to "" for default. Ex, MySQL default is 3306
$dbport = "";

// Username and password to connect to the database:
$dbuname = "bnt_user";
$dbpass = "bnt_pass";

// Name of the SQL database:
$dbname = "bnt_db";

// Type of the SQL database. This can be anything supported by ADOdb. Here are a few:
// "access" for MS Access databases. You need to create an ODBC DSN.
// "ado" for ADO databases
// "ibase" for Interbase 6 or earlier
// "borland_ibase" for Borland Interbase 6.5 or up
// "mssql" for Microsoft SQL
// "mysql" for MySQL
// "oci8" for Oracle8/9
// "odbc" for a generic ODBC database
// "postgres" for PostgreSQL ver < 7
// "postgres7" for PostgreSQL ver 7 and up
// "sybase" for a SyBase database
// NOTE: only mysql work as of right now, due to SQL compat code
$db_type = "mysql";

// Set this to 1 to use db persistent connections, 0 otherwise
$db_persistent = 1;

/* Table prefix for the database. If you want to run more than
one game of BNT on the same database, or if the current table
names conflict with tables you already have in your db, you will
need to change this */
$db_prefix = "bnt_";

// Administrator's name, password and email:
// Be sure to change these. Don't leave them as is.
$adminpass = "pika";
$adminname = "Adam";
$admin_mail = "i_m_sane@despammed.com";

// Address the forum link, link's to:
$link_forums = "http://forums.blacknova.net";
$bnt_ls = true;
$bnt_ls_key = "myKey"; // enter an authentification key here (always use the sam e key for the same server/game)
$bnt_ls_url = "http://www.rednova.de/";
$max_team_members = 6; // Max Members per Team (if limit reached, the INVITE Lin k is disabled.
$servertimezone = "CET [GMT+1]";
?>
