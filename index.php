<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
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
// File: index.php

$index_page = true;
include "config.php";

if (!isset($_GET['indexlang']))
{
    $_GET['indexlang'] = null;
}

if ($_GET['indexlang'] == 'french')
{
    $lang = 'french.inc';
}
elseif ($_GET['indexlang'] == 'german')
{
    $lang = 'german.inc';
}
elseif ($_GET['indexlang'] == 'spanish')
{
    $lang = 'spanish.inc';
}
elseif ($_GET['indexlang'] == 'british')
{
    $lang = 'english.inc';
}
elseif ($_GET['indexlang'] == 'english')
{
    $lang = 'english.inc';
}
else
{
    $lang = $default_lang . ".inc";
}
if (empty($lang))
{
    $lang = $default_lang;
}

include "languages/$lang";
$title = $l_welcome_bnt;
$body_class = 'index';
include "header.php";
?>

<div class="index-header"><img class="index" src="images/header1.png" alt="Blacknova Traders"></div>
<div class="index-flags">
<a href="index.php?indexlang=french"><img src="images/flags/France.png" alt="French"></a>
<a href="index.php?indexlang=german"><img src="images/flags/Germany.png" alt="German"></a>
<a href="index.php?indexlang=spanish"><img src="images/flags/Mexico.png" alt="Spanish"></a>
<a href="index.php?indexlang=british"><img src="images/flags/United_Kingdom.png" alt="British English"></a>
<a href="index.php?indexlang=english"><img src="images/flags/United_States_of_America.png" alt="American English"></a></div>
<div class="index-header-text">Blacknova Traders</div>
<br>
<a href="login.php"><button class="button blue"><span class="shine"></span><?php echo $l_login_title; ?></button></a>
<a href="new.php"><button class="button green"><span class="shine"></span><?php echo $l_new_player; ?></button></a>
<a href="mailto:<?php echo $admin_mail; ?>"><button class="button gray"><span class="shine"></span><?php echo utf8_encode($l_login_emailus); ?></button></a>
<a href="ranking.php"><button class="button purple"><span class="shine"></span><?php echo $l_rankings; ?></button></a>
<a href="docs/faq.html"><button class="button brown"><span class="shine"></span><?php echo $l_faq; ?></button></a>
<a href="settings.php"><button class="button red"><span class="shine"></span><?php echo $l_settings; ?></button></a>
<?php
if (!empty($link_forums))
{
    echo "<a href='$link_forums' target='_blank'><button class='button orange'><span class='shine'></span>$l_forums</button></a>";
}
?>
<br><br>
<div><p></p></div>
<div class="index-welcome"><p>
<h1 class="index-h1"><?php echo utf8_encode($l_welcome_bnt); ?></h1><br>
<?php echo utf8_encode($l_bnt_description); ?><br></p>
<br>
<p class="cookie-warning"><?php echo utf8_encode($l_cookie_warning); ?></p></div>
<br>
<?php include "footer.php"; ?>
