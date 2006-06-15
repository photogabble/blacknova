<?php
function transfer_to_planet($db,$player_id, $planet_id, $how_many = 1)
{
    global $max_spies_per_planet;
    global $shipinfo;
    $res = $db->Execute("SELECT COUNT(spy_id) AS n FROM {$db->prefix}spies WHERE owner_id = $player_id AND ship_id = '0' AND planet_id = $planet_id");
    $on_planet = $res->fields['n'];
    $can_transfer = min(($max_spies_per_planet - $on_planet), $how_many);
    if ($can_transfer < 0)
    {
        $can_transfer = 0;
    }

    $res = $db->SelectLimit("SELECT spy_id FROM {$db->prefix}spies WHERE owner_id = $player_id AND ship_id = $shipinfo[ship_id]",$can_transfer);
    $how_many2 = $res->RecordCount();
  
    if (!$how_many2)
    {
        return 0;
    }
    else  
    {
        while (!$res->EOF)
        {
            $spy = $res->fields['spy_id'];
            $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET planet_id = '$planet_id', ship_id = '0', active = 'N', job_id = '0', spy_percent = '0.0' WHERE spy_id = $spy");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $res->MoveNext();
        }
    return $how_many2;
    }   
}
?>
