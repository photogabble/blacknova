<?php
function traderoute_delete()
{
    // Dynamic functions
    dynamic_loader ($db, "traderoute_die.php");
    global $playerinfo;
    global $confirm;
    global $num_traderoutes;
    global $traderoute_id;
    global $traderoutes;
    global $l_tdr_returnmenu, $l_tdr_tdrdoesntexist, $l_tdr_notowntdr, $l_tdr_tdrdeleted;
    global $db;

    $query = $db->Execute("SELECT * FROM {$db->prefix}traderoutes WHERE traderoute_id=?", array($_GET['traderoute_id']));
    if (!$query || $query->EOF)
    {
        traderoute_die($l_tdr_tdrdoesntexist);
    }

    $delroute = $query->fields;

    if ($delroute['owner'] != $playerinfo['player_id'])
    {
        traderoute_die($l_tdr_notowntdr);
    }

    if (empty($_GET['confirm']))
    {
        $num_traderoutes = 1;
        $traderoutes[0] = $delroute;
        // here it continues to the main file area to print the route
    }
    else
    {
        $query = $db->Execute("DELETE FROM {$db->prefix}traderoutes WHERE traderoute_id=?", $_GET['traderoute_id']));
        echo "$l_tdr_tdrdeleted <a href=traderoute.php>$l_tdr_returnmenu</a>";
        traderoute_die("");
    }
}
?>
