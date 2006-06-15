<?php
function db_kill_player($db, $player_id) // Might be able to split out of g_functions..
{
    // Dynamic functions
    dynamic_loader ($db, "calc_ownership.php");

    // Planet log constants
    define('PLOG_GENESIS_CREATE',1);
    define('PLOG_GENESIS_DESTROY',2);
    define('PLOG_CAPTURE',3);
    define('PLOG_ATTACKED',4);
    define('PLOG_SCANNED',5);
    define('PLOG_OWNER_DEAD',6);
    define('PLOG_DEFEATED',7);
    define('PLOG_SOFA',8);
    define('PLOG_PLANET_DESTRUCT',9);

    // Planet log
    global $default_prod_ore, $default_prod_organics, $default_prod_goods, $default_prod_energy;
    global $default_prod_fighters, $default_prod_torp;
    global $l_killheadline, $l_news_killed, $langdir;
    global $spy_success_factor; // Used for spies
    global $start_armor, $start_fighters, $start_energy;

    // Dynamic functions
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');
    load_languages($db, $raw_prefix, 'sched_news');

    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET destroyed='Y', class=1, hull=0, engines=0, pengines=0, power=0, sensors=0, " .
                                "computer=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=$start_armor, " .
                                "cloak=0, shields=0, sector_id=1, organics=0, ore=0, goods=0, energy=$start_energy, " .
                                "colonists=0, fighters=$start_fighters, dev_warpedit=0, dev_genesis=0, dev_emerwarp=0, " .    
                                "dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, on_planet='N', " .
                                "cleared_defenses=' ' WHERE player_id=$player_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET times_dead=times_dead+1 WHERE player_id = $player_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance='0' WHERE player_id = $player_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("DELETE FROM {$db->prefix}bounty WHERE placed_by = $player_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // If I lost my ship, all spies on it are killed and the spy owners will get log messages about it.
    if ($spy_success_factor)
    {
        dynamic_loader ($db, "spy_ship_destroyed.php");
        $debug_query = $db->Execute("SELECT ship_id FROM {$db->prefix}ships WHERE player_id = $player_id");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $ship_id = $debug_query->fields['ship_id'];
        spy_ship_destroyed($db, $ship_id, 0);

        $debug_query = $db->Execute("DELETE FROM {$db->prefix}spies WHERE owner_id = $player_id");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    $debug_query = $db->Execute("SELECT DISTINCT sector_id FROM {$db->prefix}planets WHERE owner='$player_id' AND base='Y'");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $i = 0;

    while (!$debug_query->EOF && $debug_query)
    {
        $sectors[$i] = $debug_query->fields['sector_id'];
        $i++;
        $debug_query->MoveNext();
    }

    $debug_query = $db->Execute("SELECT planet_id FROM {$db->prefix}planets WHERE owner='$player_id'");
    while (!$debug_query->EOF && $debug_query)
    {
        planet_log($db, $debug_query->fields['planet_id'], $player_id, $player_id, PLOG_OWNER_DEAD);
        $debug_query->MoveNext();
    }

    $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET owner=0, fighters=0, base='N' WHERE owner=$player_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    if (!empty($sectors))
    {
        foreach($sectors as $sector)
        {
            calc_ownership($db,$sector);
        }
    }

    $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE player_id=$player_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("SELECT zone_id FROM {$db->prefix}zones WHERE team_zone='N' AND owner=$player_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $zone = $debug_query->fields;

    $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=1 WHERE zone_id=$zone[zone_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $debug_query = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id='$player_id'");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $name = $debug_query->fields;

    $headline = $name['character_name'] . $l_killheadline;

    $newstext = str_replace("[name]",$name['character_name'],$l_news_killed);

    $stamp = date("Y-m-d H:i:s");
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}news (news_data, user_id, date, news_type) VALUES " .
                                "(?,?,?,?) ", array($name['character_name'], $player_id, $stamp, 'killed'));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}
?>
