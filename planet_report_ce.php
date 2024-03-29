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
// File: planet_report_ce.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

$title = $langvars['l_pr_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title);

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('planet_report', 'rsmove', 'common', 'global_includes', 'global_funcs', 'footer', 'news', 'regional'));
echo '<h1>' . $title . '</h1>';

echo '<br>';
echo str_replace('[here]', "<a href='planet_report.php'>" . $langvars['l_here'] . '</a>', $langvars['l_pr_click_return']);
echo '<br>';

// Detect if this variable exists, and filter it. Returns false if anything wasn't right.
$tpcreds = null;
$tpcreds = $_POST['tpcreds']; // TODO: tp creds is an array. Filtering will be tricky.
if (mb_strlen(trim($tpcreds)) === 0)
{
    $tpcreds = false;
}

if ($tpcreds !== null && $tpcreds !== false)
{
    Bad\PlanetReportCE::collectCredits($db, $langvars, $tpcreds, $sector_max);
}
elseif (isset($buildp) && isset($builds))
{
    Bad\PlanetReportCE::buildBase($db, $langvars, $buildp, $builds);
}
else
{
    Bad\PlanetReportCE::changePlanetProduction($db, $langvars, $_POST);
}

echo '<br><br>';
Bnt\Text::gotoMain($db, $lang, $langvars);
Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
