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
// File: stats.php

$pos = (strpos($_SERVER['PHP_SELF'], "/stats.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once ("./header.php");
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

$res1 = $db->Execute("SELECT count(*) as count FROM {$db->prefix}ip_log");
$res = $db->Execute("SELECT browser, count(*) as count FROM {$db->prefix}ip_log GROUP BY browser ORDER BY count DESC");
$total_count = $res1->fields['count'];
$i = 0;

if (!$res)
{
    print $res->ErrorMsg();
}
else
{
    while (!$res->EOF && $i < 3)
    {
        $browsers[$i]['name'] = $res->fields[0];
        $browsers[$i]['count'] = $res->fields[1];
        $browsers[$i]['percent'] = round(($res->fields[1] / $total_count)*100,2);
        $res->MoveNext();
        $i++;
    }
}

$res->Close(); # optional

$template->assign('templateset', $templateset);
$template->assign('browsers', $browsers);

$template->display("$templateset/admin/stats.tpl");

?>
