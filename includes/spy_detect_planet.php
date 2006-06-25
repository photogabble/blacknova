<?php
function spy_detect_planet($db,$shipowner_ship_id, $planet_id, $succ)
{
    // Dynamic functions
    dynamic_loader ($db, "seed_mt_rand.php");

    global $l_unnamed;

    seed_mt_rand();
    $res0 = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE ship_id=? AND active='Y'", array($shipowner_ship_id)); // AND owner_id != ship_id
    while (!$res0->EOF)
    {
        $spyowners = $res0->fields;

        $i = mt_rand(1,100);
        if ($i <= $succ)
        {
            $res = $db->Execute("SELECT * FROM {$db->prefix}detect WHERE unique_value=? AND owner_id=? AND det_type = '0'", array($planet_id, $spyowners['owner_id']));
            if (!$res->RecordCount())
            {
                $res = $db->Execute("SELECT {$db->prefix}planets.planet_id, {$db->prefix}planets.sector_id, {$db->prefix}planets.name, {$db->prefix}players.character_name FROM {$db->prefix}planets LEFT JOIN {$db->prefix}players ON {$db->prefix}planets.owner={$db->prefix}players.player_id WHERE {$db->prefix}planets.planet_id=? AND {$db->prefix}planets.owner!=?", array($planet_id, $spyowners['owner_id']));
                if ($res->RecordCount())
                {
                    $planet = $res->fields;
                    if (!$planet['name']) 
                    { 
                        $planet['name'] = $l_unnamed; 
                    }

                    $stamp = date("Y-m-d H:i:s");
                    $debug_query = $db->Execute("INSERT INTO {$db->prefix}detect (det_id, owner_id, det_type, det_time, detect_data, unique_value) " .
                                                "values ('', ?, ?, ?, ?, ?)", array($spyowners['owner_id'], 0, $stamp, "$planet['sector_id']|$planet['character_name']|$planet['name']", $planet['planet_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                }
            }
        }
    $res0->MoveNext();
    }
}
?>
