<?php
function buy_them($db,$player_id, $how_many = 1)
{
    global $shipinfo;

    for ($i=1; $i<=$how_many; $i++)
    {
        $debug_query = $db->Execute("INSERT INTO {$db->prefix}spies (spy_id, active, owner_id, planet_id, ship_id, job_id, spy_percent, move_type) " .
                                    "values (?,?,?,?,?,?,?,?)", array('','N',$player_id,'0',$shipinfo['ship_id'],'0','0.0','toship'));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }  
}
?>
