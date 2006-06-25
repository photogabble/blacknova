<?php
function spy_buy_new_ship($db,$old_ship_id)
{
    dynamic_loader ($db, "playerlog.php");

    $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}players.character_name, {$db->prefix}ships.name FROM {$db->prefix}spies INNER JOIN {$db->prefix}ships ON {$db->prefix}spies.ship_id = {$db->prefix}ships.ship_id INNER JOIN {$db->prefix}players ON {$db->prefix}ships.player_id = {$db->prefix}players.player_id WHERE {$db->prefix}spies.ship_id=? AND {$db->prefix}spies.active = 'Y' AND {$db->prefix}spies.planet_id = '0' ", array($old_ship_id));

    while (!$res->EOF)
    {
        $spy = $res->fields;
        playerlog($db,$spy['owner_id'], "LOG_SPY_NEWSHIP", "$spy[spy_id]|$spy[character_name]|$spy[name]");
        $res->MoveNext();
    }
  
    $res2 = $db->Execute("DELETE FROM {$db->prefix}spies WHERE ship_id=?", array($old_ship_id));  // Including player's own spies!
}
?>
