<?php
function adminlog($db, $log_type, $data = '')
{
/*
    $debug_query = $db->Execute("SHOW TABLES LIKE '{$db->prefix}logs'");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $row = $debug_query->fields;
    if ($row !== false)
*/
    $throw_away = $db->SelectLimit("SELECT * FROM {$db->prefix}logs",1);
    if ($throw_away)
    {
        // write log_entry to the admin log
        if (!empty($log_type))
        {
            $stamp = date("Y-m-d H:i:s");
            $debug_query = $db->Execute("INSERT INTO {$db->prefix}logs (player_id, type, log_time, log_data) " .
                                        "VALUES (?,?,?,?)", array (0, $log_type, $stamp, $data));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
}
?>
