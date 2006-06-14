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
// File: spy_send.php
//
// Sending spy to enemy planet

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "seed_mt_rand.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'spy');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'planets');
load_languages($db, $raw_prefix, 'main');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_spy_title;
echo "<h1>" . $title. "</h1>\n";
updatecookie($db);

seed_mt_rand();

if (!$spy_success_factor)
{
    echo "<strong>$l_spy_disabled</strong><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

if (!isset($_POST['doit']))
{
    $_POST['doit'] = '';
}
else
{
    $doit = $_POST['doit'];
}

if (!isset($_GET['doit']))
{
    $_GET['doit'] = '';
}
else
{
    $doit = $_GET['doit'];
}

if (!isset($_GET['command']))
{
    $_GET['command'] = '';
}
else
{
    $command = $_GET['command'];
}

if (!isset($_POST['command']))
{
    $_POST['command'] = '';
}
else
{
    $command = $_POST['command'];
}

if (!isset($by))
{
    $by = '';
}

if (!isset($by1))
{
    $by1 = '';
}

if (!isset($by2))
{
    $by2 = '';
}

if (!isset($by3))
{
    $by3 = '';
}

if (!isset($_POST['planet_id']))
{
    $_POST['planet_id'] = '';
}
else
{
    $planet_id = $_POST['planet_id'];
}

if (!isset($_GET['planet_id']))
{
    $_GET['planet_id'] = '';
}
else
{
    $planet_id = $_GET['planet_id'];
}

if (!isset($planet_id))
{
    $planet_id = '-1';
}

if (!isset($spy_id))
{
    $spy_id = '-1';
}

if (!isset($dismiss))
{
    $dismiss = '';
}

$line_color = $color_line2;


if ($playerinfo['turns'] < 1)
{
    echo "$l_spy_noturn<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
  
$res2 = $db->SelectLimit("SELECT spy_id FROM {$db->prefix}spies WHERE owner_id = $playerinfo[player_id] AND ship_id = $shipinfo[ship_id]",1);// AND active = 'N'
$result = $res2->RecordCount();
if (!$result)
{
    echo "$l_spy_notonboard<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
else
{
    $spyinfo = $res2->fields['spy_id'];
}

$res3 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=$planet_id");
$planetinfo = $res3->fields;
$base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
$planetinfo['sensors'] += $base_factor;

$res = $db->Execute("SELECT MAX(sensors) as maxsensors FROM {$db->prefix}ships WHERE planet_id=$planet_id AND on_planet='Y'");
if ($planetinfo['sensors'] < $res->fields['maxsensors'])
{
    $planetinfo['sensors'] = $res->fields['maxsensors'];
}

if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
{
    echo "$l_planet_none<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");    
    die();
}

if ($planetinfo['owner'] == $playerinfo['player_id'])
{
    echo "$l_spy_ownplanet<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");    
    die();
}
elseif ($planetinfo['owner'] == 0)
{
    echo "$l_spy_unownedplanet<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");    
    die();
}

$res5 = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=$planet_id AND owner_id=$playerinfo[player_id]");
$num_spies = $res5->RecordCount();
if ($num_spies >= $max_spies_per_planet)
{
    $l_spy_planetfull = str_replace("[max]", $max_spies_per_planet, $l_spy_planetfull);
    echo "$l_spy_planetfull<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");    
    die();
}

if (empty($doit))
{
    $l_spy_sendtitle = str_replace("[spyid]", "$spyinfo", $l_spy_sendtitle);
    echo "<strong>" . $l_spy_sendtitle . "<br>" . $l_spy_sendtitle2 . "</strong><br>";
    echo '<form name="bntform" action="spy.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
    echo "<input type=hidden name=command value=send>";
    echo "<input type=hidden name=doit value=1>";
    echo "<input type=hidden name=planet_id value=$planet_id>";
    echo "<input type=radio name=mode value=none>$l_spy_type1<br>";
    echo "<input type=radio name=mode value=toship checked>$l_spy_type2<br>";
    echo "<input type=radio name=mode value=toplanet>$l_spy_type3<br><br>";

    echo $l_spy_trytitle . ":<br>";
    echo "<input type=checkbox name=try_sabot checked> $l_spy_try_sabot<br>";
    echo "<input type=checkbox name=try_inter checked> $l_spy_try_inter<br>";
    echo "<input type=checkbox name=try_birth checked> $l_spy_try_birth<br>";
    echo "<input type=checkbox name=try_steal checked> $l_spy_try_steal<br>";
    echo "<input type=checkbox name=try_torps checked> $l_spy_try_torps<br>";
    echo "<input type=checkbox name=try_fits checked> $l_spy_try_fits<br>";
    if ($allow_spy_capture_planets)
    {
        echo "<input type=checkbox name=try_capture checked> $l_spy_try_capture<br><br>";
    }

    echo "<input name=submit_button type=submit value=\"$l_spy_sendbutton\">";
    echo "</form>";
}
else
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id] ");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $success = $shipinfo['cloak'] - $planetinfo['sensors'];
    if ($success > 0)
    {
        $success = $success * 5;
    }

    // Here we subtract 4% for every spy the planet owner has on the planet from the success score.
    $res66 = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=$planet_id AND owner_id=$planetinfo[owner]");
    $num_spies = $res66->RecordCount();
    $success = $success - ($num_spies * 4);

    // Here we add 4% for every spy the spy owner has on the planet to the success score.
    $res77 = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE planet_id=$planet_id AND owner_id=$playerinfo[player_id]");
    $num_own_spies = $res77->RecordCount();
    $success = $success + ($num_own_spies * 4);

    if ($success<5)
    {
        $success=5;
    }

    if ($success>99)
    {
        $success=99;
    }

    $roll = mt_rand(1,100);

    if ($roll<$success)
    {
        $try_sabot   = isset($_POST['try_sabot'])   ? "Y" : "N";
        $try_inter   = isset($_POST['try_inter'])   ? "Y" : "N";
        $try_birth   = isset($_POST['try_birth'])   ? "Y" : "N";
        $try_steal   = isset($_POST['try_steal'])   ? "Y" : "N";
        $try_torps   = isset($_POST['try_torps'])   ? "Y" : "N";
        $try_fits    = isset($_POST['try_fits'])    ? "Y" : "N";
        $try_capture = isset($_POST['try_capture']) ? "Y" : "N";

        if (empty($mode) || ($mode!="toship" && $mode!="toplanet" && $mode!="none"))
        {
            $mode = "toship";
        }

        $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET active='Y', planet_id='$planet_id', ship_id='0', spy_percent='0.0', job_id='0', move_type='$mode', try_sabot='$try_sabot', try_inter='$try_inter', try_birth='$try_birth', try_steal='$try_steal', try_torps='$try_torps', try_fits='$try_fits', try_capture='$try_capture' WHERE spy_id='$spyinfo' ");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        echo "$l_spy_sendsuccessful<br>";
    }
    else
    {
        $debug_query = $db->Execute("DELETE FROM {$db->prefix}spies WHERE spy_id=$spyinfo ");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        echo "$l_spy_sendfailed<br>";
        if (!$planetinfo['name']) 
        {
            $planetinfo['name'] = $l_unnamed;
        }

        playerlog($db,$planetinfo['owner'], "LOG_SPY_SEND_FAIL", "$planetinfo[name]|$planetinfo[sector_id]|$playerinfo[character_name]");
    }
}   

echo "<a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu";
global $l_global_mmenu;
$template->assign("title", $title);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->display("$templateset/spy.tpl");

include_once ("./footer.php");
?>
