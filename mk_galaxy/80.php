<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: mk_galaxy/80.php

$pos = strpos($_SERVER['PHP_SELF'], "/80.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die();
}

// Dynamic functions
dynamic_loader ($db, "db_output.php");

$s = 0;
$query = '';
$insertquery = '';
for ($i=1; $i<=$_POST['sektors']; $i++)
{
    $numlinks = mt_rand(0,$_POST['linksper']);
    for ($j=0; $j<$numlinks; $j++)
    {
        $destination = mt_rand(1,$_POST['sektors']);
        if ($destination != $i)
        {
            $insertquery[$s] = "(" . $i . "," . $destination . ")"; // Link Start, then destination.
            $s++;

            $link_odds = mt_rand(0,100);
            if ($link_odds < $_POST['twoways'])
            {
                $insertquery[$s] = "(" . $destination . "," . $i . ")"; // Link Destination, then start.
                $s++;
            }
        }
        else
        {
            $j--;
        }   
    }

    if ($ADODB_SESSION_DRIVER == 'postgres7')
    {
        // Postgres doesn't support bulk inserts (multiple inserts in a single call), so we just dump one at a time.
        $query = "INSERT into {$db->prefix}links (link_start, link_dest) VALUES " . $insertquery[$s-1];
    }
    else
    {
        if  (( ($i % 2000)==0 || ($i==$_POST['sektors'])) && ($s>0) && ($i>0))
        {
            $query = "INSERT into {$db->prefix}links (link_start, link_dest) VALUES ";

            $comma_added = implode(",", $insertquery);
            $query = $query . $comma_added;
            $s = 0;
        }
    }

    if ($query != '')
    {
        $debug_query = $db->Execute($query);
        $current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
        cumulative_error($cumulative, $current_status);
        $query = '';
    }
}

// Put in the sector 1, 2, and 3 warp loop, and the links from 1 and 2 to 0.
$debug_query = $db->Execute("INSERT INTO {$db->prefix}links (link_start, link_dest) VALUES (1,2);");
$current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
cumulative_error($cumulative, $current_status);

$debug_query = $db->Execute("INSERT INTO {$db->prefix}links (link_start, link_dest) VALUES (2,1);");
$current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
cumulative_error($cumulative, $current_status);

$debug_query = $db->Execute("INSERT INTO {$db->prefix}links (link_start, link_dest) VALUES (2,3);");
$current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
cumulative_error($cumulative, $current_status);

$debug_query = $db->Execute("INSERT INTO {$db->prefix}links (link_start, link_dest) VALUES (3,2);");
$current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
cumulative_error($cumulative, $current_status);

$debug_query = $db->Execute("INSERT INTO {$db->prefix}links (link_start, link_dest) VALUES (1,3);");
$current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
cumulative_error($cumulative, $current_status);

$debug_query = $db->Execute("INSERT INTO {$db->prefix}links (link_start, link_dest) VALUES (3,1);");
$current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
cumulative_error($cumulative, $current_status);

$template->assign("autorun", $_POST['autorun']);
$template->assign("title", $title);
$template->assign("gen_links_result", db_output($db,!$cumulative,__LINE__,__FILE__));
$template->assign("l_gen_links", $l_gen_links);
$template->assign("encrypted_password", $_POST['encrypted_password']);
$template->assign("l_continue", $l_continue);
$template->assign("step", ($_POST['step']+1));
$template->assign("admin_charname", $_POST['admin_charname']);
$template->assign("gamenum", $_POST['gamenum']);
$template->display("$templateset/mk_galaxy/80.tpl");
?>
