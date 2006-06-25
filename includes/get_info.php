<?php
function get_info($db)
{
    // Dynamic functions
    dynamic_loader ($db, "adminlog.php");

    global $raw_prefix, $playerinfo, $portinfo, $shipinfo, $zoneinfo, $sectorinfo, $classinfo, $igbinfo, $accountinfo; 
    global $templateset;

    if (isset($_SESSION['email']))
    {
        $debug_query = $db->SelectLimit("SELECT * FROM {$raw_prefix}users WHERE email=?",1,-1,array($_SESSION['email']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $accountinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}players WHERE account_id=?",1,-1,array($accountinfo['account_id'));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $playerinfo = $debug_query->fields;

        if ($playerinfo['credits'] < 0)
        {
            adminlog($db, "LOG_RAW", "Negative value for player credits - possible cheat from - " . $_SESSION['ip_address']);
        }

        $templateset = $playerinfo['template'];
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ships WHERE player_id=? AND ship_id=?",1,-1,array($playerinfo['player_id'], $playerinfo['currentship']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $shipinfo = $debug_query->fields;

        $debug_query = $db->CacheSelectLimit("SELECT * FROM {$db->prefix}ship_types WHERE type_id=?",1,-1,array($shipinfo['class']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $classinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}universe WHERE sector_id=?",1,-1,array($shipinfo['sector_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $sectorinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ports WHERE sector_id=?",1,-1,array($shipinfo['sector_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $portinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}zones WHERE zone_id=?",1,-1,array($sectorinfo['zone_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $zoneinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=?",1,-1,array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $igbinfo = $debug_query->fields;

        return TRUE;
    }
    else
    {
        return FALSE;
    }
}
?>
