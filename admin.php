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
// File: admin.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "yesno.php");
dynamic_loader ($db, "checked.php");
dynamic_loader ($db, "attack_check.php");
dynamic_loader ($db, "adminlog.php");
dynamic_loader ($db, "get_info.php");

// Load language variables
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'login');
load_languages($db, $raw_prefix, 'login2');
load_languages($db, $raw_prefix, 'admin');

// Later this may change to accomidate perfmon.
$style_sheet_file = "templates/$templateset/styles/style.css";
$no_body = 1;
$title = $l_admin_title;
include_once ("./header.php");

global $db;

$ip_address = getenv("REMOTE_ADDR"); // Get IP address for user
$proxy_address = getenv("HTTP_X_FORWARDED_FOR"); // Get Proxy IP address for user
$client_ip_address = getenv("HTTP_CLIENT_IP"); // Get http's IP address for user

if (!get_info($db) || $playerinfo['acl'] < 128) // Check player's ACL to ensure they have access to the admin panel - currently at 128.
{
    global $l_error_occured, $l_acc_denied;
    $title = $l_error_occured;
    attack_check($db);
//    adminlog($db, "LOG_RAW","Bad login - banned ADMIN LOGIN ATTEMPT from $ip_address");

    $template->assign("title", $title);
    $template->assign("l_acc_denied", $l_acc_denied);
    $template->display("$templateset/admin-denied.tpl");

    include_once ("./footer.php");
    die();
}

$debug_query = $db->SelectLimit("SELECT ban_reason FROM {$raw_prefix}ip_bans WHERE '$ip_address' LIKE ban_mask OR '$client_ip_address' " .
                                "LIKE ban_mask OR '$proxy_address' LIKE ban_mask",1);
db_op_result($db,$debug_query,__LINE__,__FILE__);

if ($debug_query && !$debug_query->EOF)
{
        // IP was banned
        global $l_error_occured, $l_login_banned;
        $title = $l_error_occured;
        adminlog($db, "LOG_RAW","Bad login - banned ADMIN LOGIN ATTEMPT from $ip_address");

        $template->assign("title", $title);
        $template->assign("l_login_banned", $l_login_banned);
        $template->display("$templateset/admin-banned.tpl");

        include_once ("./footer.php");
        die();
}

if ((!isset($_GET['menu'])) || ($_GET['menu'] == ''))
{
    $_GET['menu'] = '';
}
else
{
    $admin_menu = $_GET['menu'];
}

if ((!isset($_POST['menu'])) || ($_POST['menu'] == ''))
{
    $_POST['menu'] = '';
}
else
{
    $admin_menu = $_POST['menu'];
}

if (!isset($admin_menu))
{
    $admin_menu = '';
}

if (!isset($_sells))
{
    $_sells = '';
}

if (!isset($command))
{
    $command = '';
}

if (!isset($cmd))
{
    $cmd = '';
}

$login_ip = getenv("REMOTE_ADDR");

if (!isset($_POST['delete']))
{
    $_POST['delete'] = '';
}

if (!isset($_POST['action']))
{
    $_POST['action'] = '';
}

if (!isset($_POST['edit']))
{
    $_POST['edit'] = '';
}

if (!isset($_POST['add']))
{
    $_POST['add'] = '';
}

if (!isset($_POST['email']))
{
    $_POST['email'] = '';
}

if (isset($_GET['hidem']))
{
    $admin_menu = 'perfmon'; // If hidem is set, its part of adodb's perf monitor.
}

if ($admin_menu == '')
{
    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
    {
        $add_slash_to_url = '/';
    }

    $adminurl = $server_type . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "admin.php";
}

$button_main = true;

if ($playerinfo['acl'] >= 128 && $admin_menu == "logview")
{
    include_once ("./admin/logview.php");
} 
elseif ($playerinfo['acl'] >= 128 && $admin_menu == "an")
{
    include_once ("./admin/an.php");
}
elseif ($playerinfo['acl'] >= 128 && $admin_menu == "stats")
{
    include_once ("./admin/stats.php");
}
elseif ($playerinfo['acl'] >= 128 && $admin_menu == "emailview")
{
    include_once ("./admin/emailview.php");
}
elseif ($playerinfo['acl'] >= 128 && $admin_menu == "iplog")
{
    include_once ("./admin/iplog.php");
}
elseif ($playerinfo['acl'] >= 128 && $admin_menu == "perfmon")
{
    include_once ("./admin/perfmon.php");
}
elseif ($playerinfo['acl'] >= 128 && $admin_menu == "ai_instruct")
{
    include_once ("./admin/ai_instruct.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "globmsg")
{ 
    include_once ("./admin/globmsg.php");
} 
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "useredit")
{
    include_once ("./admin/useredit.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "planedit")
{
    include_once ("./admin/planedit.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "ipedit")
{
    include_once ("./admin/ipedit.php");
}    
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "setedit")
{
    include_once ("./admin/setedit.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "galedit")
{
    include_once ("./admin/galedit.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "sectedit")
{
    include_once ("./admin/sectedit.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "zoneedit")
{
    include_once ("./admin/zoneedit.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "memberlist")
{
    include_once ("./admin/memberlist.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "kinstruct")
{
    include_once ("./admin/kinstruct.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "drop_ai")
{
    include_once ("./admin/drop_ai.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "new_ai")
{
    include_once ("./admin/new_ai.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "clear_ai_log")
{
    include_once ("./admin/clear_ai_log.php");
}
elseif ($playerinfo['acl'] >= 255 && $admin_menu == "ai_edit")
{
    include_once ("./admin/ai_edit.php");
}
elseif ($admin_menu != '')
{
    global $l_admin_unknown;

    $template->assign("l_admin_unknown", $l_admin_unknown);
    $template->display("$templateset/admin-unknown.tpl");
}

$template->assign("playerinfo_acl", $playerinfo['acl']);
$template->assign("admin_menu", $admin_menu);
$template->assign("adminurl", $adminurl);
$template->assign("button_main", $button_main);
$template->display("$templateset/admin.tpl");

include_once ("./footer.php");
?> 
