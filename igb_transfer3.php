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
// File: igb_transfer3.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "playerlog.php");

// Load language variables
load_languages($db, $raw_prefix, 'igb');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);
$title = $l_igb_title;
include_once ("./header.php");

if (!$allow_ibank)
{
    include_once ("./igb_error.php");
}

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND owner=$playerinfo[player_id]");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$planetinfo = $debug_query->RecordCount();

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND team=$playerinfo[team]");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$teamplanetinfo = $debug_query->RecordCount();

if ($portinfo['port_type'] != 'shipyard' && $portinfo['port_type'] != 'upgrades' && $portinfo['port_type'] != 'devices' && $planetinfo < 1 && $teamplanetinfo < 1)
{
    echo $l_noport . "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
else
{
    $no_body = 2;
}

updatecookie($db);

$result = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=$playerinfo[player_id]");
$account = $result->fields;

//echo "<body bgcolor=\"#666\" text=\"#FFFFFF\" link=\"#00FF00\" vlink=\"#00FF00\" alink=\"#FF0000\">";

echo "<style type=\"text/css\">";
echo "    input.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}";
echo "    select.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}";
echo "</style>";

echo "\n<div style=\"text-align:center;\">";
echo "\n<img alt=\"\" src=\"templates/$templateset/images/div1.png\">";
//echo "\n<table width=\"600\" height=\"350\" border=\"0\">";
//echo "\n<tr><td align=\"center\" background=\"templates/$templateset/images/igbscreen.png\">";

global $playerinfo;
global $account;
global $player_id;
global $splanet_id;
global $dplanet_id;
global $igb_min_turns;
global $igb_svalue;
global $ibank_paymentfee;
global $amount;
global $igb_trate;
global $l_igb_errsendyourself, $l_igb_unknowntargetship, $l_igb_min_turns3, $l_igb_min_turns4, $l_igb_mustwait2;
global $l_igb_invalidtransferinput, $l_igb_nozeroamount, $l_igb_notenoughcredits, $l_igb_notenoughcredits2, $l_igb_in, $l_igb_to;
global $l_igb_amounttoogreat, $l_igb_transfersuccessful, $l_igb_creditsto, $l_igb_transferamount, $l_igb_amounttransferred;
global $l_igb_transferfee, $l_igb_igbaccount, $l_igb_back, $l_igb_logout, $l_igb_errplanetsrcanddest, $l_igb_errnotyourplanet;
global $l_igb_errunknownplanet, $l_igb_unnamed, $l_igb_ctransferred, $l_igb_srcplanet, $l_igb_destplanet, $l_igb_ctransferredfrom;
global $db;

$amount = preg_replace('/[^0-9]/','',$_POST['amount']);

if ($amount < 0)
{
    $amount = 0;
}

if (isset($_POST['player_id'])) // Ship transfer
{
    // Need to check again to prevent cheating by manual posts
    $res = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=$_POST[player_id]");

    if ($playerinfo['player_id'] == $_POST[player_id])
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errsendyourself;
        include_once ("./igb_error.php");
    }

    if (!$res || $res->EOF)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_unknowntargetship;
        include_once ("./igb_error.php");
    }

    $target = $res->fields;

    if ($target['turns_used'] < $igb_min_turns)
    {
        $l_igb_min_turns3 = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns3);
        $l_igb_min_turns3 = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_min_turns3);
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_min_turns3;
        include_once ("./igb_error.php");
    }

    if ($playerinfo['turns_used'] < $igb_min_turns)
    {
        $l_igb_min_turns4 = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns4);
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_min_turns4;
        include_once ("./igb_error.php");
    }

    if ($igb_trate > 0)
    {
        $curtime = time();
        $curtime -= $igb_trate * 60;
        $res = $db->Execute("SELECT UNIX_TIMESTAMP(transfer_time) as time FROM {$db->prefix}ibank_transfers WHERE " .
                            "UNIX_TIMESTAMP(transfer_time) > $curtime AND source_id=$playerinfo[player_id] AND " .
                            "dest_id=$target[player_id]");
        if (!$res->EOF)
        {
            $time = $res->fields;
            $difftime = ($time['time'] - $curtime) / 60;
            $l_igb_mustwait2 = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_mustwait2);
            $l_igb_mustwait2 = str_replace("[igb_trate]", number_format($igb_trate, 0, $local_number_dec_point, $local_number_thousands_sep), $l_igb_mustwait2);
            $l_igb_mustwait2 = str_replace("[igb_difftime]", number_format($difftime, 0, $local_number_dec_point, $local_number_thousands_sep), $l_igb_mustwait2);
            $backlink = "igb_transfer.php";
            $igb_errmsg = $l_igb_mustwait2;
            include_once ("./igb_error.php");
        }
    }

    if (($amount * 1) != $amount)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_invalidtransferinput;
        include_once ("./igb_error.php");
    }

    if ($amount == 0)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_nozeroamount;
        include_once ("./igb_error.php");
    }

    if ($amount > $account['balance'])
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_notenoughcredit;
        include_once ("./igb_error.php");
    }

    if ($igb_svalue != 0)
    {
        $percent = $igb_svalue * 100;
        $score = gen_score($db,$playerinfo['player_id']);
        $maxtrans = $score * $score * $igb_svalue;

        if ($amount > $maxtrans)
        {
            $backlink = "igb_transfer.php";
            $igb_errmsg = $l_igb_amounttoogreat;
            include_once ("./igb_error.php");
        }
    }

    $account['balance'] -= $amount;
    $amount2 = $amount * $ibank_paymentfee;
    $transfer = $amount - $amount2;

    $smarty->assign("formatted_trans_to", number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("formatted_trans_amt", number_format($amount, 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("formatted_trans_fee", number_format($amount2, 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("formatted_amt_transd", number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("formatted_acc_balance", number_format($account['balance'], 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("l_igb_transfersuccessful", $l_igb_transfersuccessful);
    $smarty->assign("l_igb_creditsto", $l_igb_creditsto);
    $smarty->assign("target_character_name", $target['character_name']);
    $smarty->assign("l_igb_transferamount", $l_igb_transferamount);
    $smarty->assign("l_igb_transferfee", $l_igb_transferfee);
    $smarty->assign("l_igb_amounttransferred", $l_igb_amounttransferred);
    $smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
    $smarty->assign("l_igb_back", $l_igb_back);
    $smarty->assign("l_igb_logout", $l_igb_logout);
    $smarty->assign("templateset", $templateset);
    $smarty->display("$templateset/igb_transfer3_ship.tpl");

    $debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=balance-$amount WHERE " .
                                "player_id=$playerinfo[player_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=balance+$transfer WHERE " .
                                "player_id=$target[player_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $stamp = date("Y-m-d H:i:s");
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}ibank_transfers (transfer_id, amount, source_id, dest_id, transfer_time) VALUES " .
                                "(?,?,?,?,?)", array ('', $transfer, $playerinfo['player_id'], $target['player_id'], $stamp));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $temp = number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep);
    playerlog($db,$target['player_id'], "LOG_IGB_TRANSFER1", "$playerinfo[character_name]|$temp");
    playerlog($db,$playerinfo['player_id'], "LOG_IGB_TRANSFER2", "$target[character_name]|$temp");
}
else
{
    if ($_POST['splanet_id'] == $_POST['dplanet_id'])
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errplanetsrcanddest;
        include_once ("./igb_error.php");
    }

    $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id=$_POST[splanet_id]");
    if (!$res || $res->EOF)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errunknownplanet;
        include_once ("./igb_error.php");
    }

    $source = $res->fields;

    if (empty($source['name']))
    {
        $source[name] = $l_igb_unnamed;
    }

    $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id=$_POST[dplanet_id]");
    if (!$res || $res->EOF)
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errunknownplanet;
        include_once ("./igb_error.php");
    }

    $dest = $res->fields;

    if (empty($dest['name']))
    {
        $dest[name] = $l_igb_unnamed;
    }

    if ($source['owner'] != $playerinfo['player_id'] || $dest['owner'] != $playerinfo['player_id'])
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_errnotyourplanet;
        include_once ("./igb_error.php");
    }

    if ($amount > $source['credits'])
    {
        $backlink = "igb_transfer.php";
        $igb_errmsg = $l_igb_notenoughcredits2;
        include_once ("./igb_error.php");
    }

    $percent = $ibank_paymentfee * 100;
    $source['credits'] -= $amount;
    $amount2 = $amount * $ibank_paymentfee;
    $transfer = $amount - $amount2;
    $dest['credits'] += $transfer;

    $smarty->assign("templateset",templateset);
    $smarty->assign("l_igb_transfersuccessful",$l_igb_transfersuccessful);
    $smarty->assign("l_igb_ctransferredfrom",$l_igb_ctransferredfrom);
    $smarty->assign("source_name",$source['name']);
    $smarty->assign("l_igb_to",$l_igb_to);
    $smarty->assign("dest_name",$dest['name']);
    $smarty->assign("formatted_trans_to", number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("l_igb_transferamount", $l_igb_transferamount);
    $smarty->assign("formatted_trans_amt", number_format($amount, 0, $local_number_dec_point, $local_number_thousands_sep)); 
    $smarty->assign("l_igb_transferfee", $l_igb_transferfee);
    $smarty->assign("formatted_trans_fee", number_format($amount2, 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("l_igb_amounttransferred", $l_igb_amounttransferred);
    $smarty->assign("formatted_amt_transd", number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("l_igb_srcplanet", $l_igb_srcplanet);
    $smarty->assign("source_name", $source['name']);
    $smarty->assign("source_sector_id", $source['sector_id']);
    $smarty->assign("formatted_src_creds", number_format($source['credits'], 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("l_igb_destplanet", $l_igb_destplanet);
    $smarty->assign("dest_name", $dest['name']);
    $smarty->assign("l_igb_in", $l_igb_in);
    $smarty->assign("dest_sector_id", $dest['sector_id']);
    $smarty->assign("formatted_dest_creds", number_format($dest['credits'], 0, $local_number_dec_point, $local_number_thousands_sep));
    $smarty->assign("l_igb_back", $l_igb_back);
    $smarty->assign("l_igb_logout", $l_igb_logout);
    $smarty->assign("templateset", $templateset);
    $smarty->display("$templateset/igb_transfer3_notship.tpl");

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits-$amount WHERE planet_id=$splanet_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits+$transfer WHERE planet_id=$dplanet_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}

echo "<img alt=\"\" src=\"templates/$templateset/images/div2.png\">";
echo "</div>";

include_once ("./footer.php");
?>
