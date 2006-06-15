<?php
function spy_planet_destroyed($db,$planet_id)
{
    dynamic_loader ($db, "playerlog.php");

    $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}planets.name, {$db->prefix}planets.sector_id FROM {$db->prefix}spies INNER JOIN {$db->prefix}planets ON {$db->prefix}spies.planet_id = {$db->prefix}planets.planet_id WHERE {$db->prefix}spies.planet_id = '$planet_id' ");
    while (!$res->EOF)
    {
        $owners = $res->fields;
        playerlog($db,$owners[owner_id], "LOG_SPY_CATACLYSM", "$owners[spy_id]|$owners[name]|$owners[sector_id]");
        $res->MoveNext();
    }
  
    $db->Execute("DELETE FROM {$db->prefix}spies WHERE planet_id = '$planet_id' ");
}
?>
