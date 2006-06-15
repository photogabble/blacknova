<?php
function playerlog($db,$player_id, $log_type, $data = '')
{
    // write log_entry to the player's log - identified by player's id.
    if (!empty($log_type))
    {
        $stamp = date("Y-m-d H:i:s");
        $debug_query = $db->Execute("INSERT INTO {$db->prefix}logs (player_id, type, log_time, log_data) VALUES " .
                                    "(?,?,?,?)", array($player_id, $log_type, $stamp, $data));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
}
?>
