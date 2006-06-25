<?php
function updatecookie($db)
{
    // Refresh the db with timestamp, and give the player however many turns he deserves.
    global $playerinfo, $sched_turns, $max_turns;

    $stamp = date("Y-m-d H:i:s");

    // Update the last login.
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET last_login=?, sessionid=? WHERE player_id=?", array($stamp, $_SESSION['sessionid'], $playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Get the player's last update time.
    $debug_query = $db->SelectLimit("SELECT last_update, turns from {$db->prefix}players WHERE player_id =?",1,-1,array($playerinfo['player_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Dynamic functions
    dynamic_loader ($db, "dbtm2unixtime.php");
    $last_update = dbtm2unixtime( $debug_query->fields['last_update'] );
    $turns = $debug_query->fields['turns'];
    
    if ($turns < $max_turns)
    {
        // Find the number of minutes since last update - use floor so that its not based on half-minutes!
        $timesince = floor((time() - $last_update) /60);
        $newturns = $debug_query->fields['turns'] + ($sched_turns*$timesince);

        if ($newturns > $max_turns)
        {
            $newturns = $max_turns;
        }

        if ($timesince > 0) // If the number of minutes since last update is more than 0, update him.
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET last_update=?, turns=? WHERE player_id=?", array($stamp, $newturns, $playerinfo['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
}
?>
