<?php
function player_insignia_name($db,$a_username) 
{
    global $raw_prefix, $player_insignia;
    global $langdir;
    global $l_insignia_0;
    global $l_insignia_1;
    global $l_insignia_2;
    global $l_insignia_3;
    global $l_insignia_4;
    global $l_insignia_5;
    global $l_insignia_6;
    global $l_insignia_7;
    global $l_insignia_8;
    global $l_insignia_9;
    global $l_insignia_10;
    global $l_insignia_11;
    global $l_insignia_12;
    global $l_insignia_13;

    $res = $db->Execute("SELECT account_id FROM {$raw_prefix}users WHERE email=?", array($a_username));
    db_op_result($db,$res,__LINE__,__FILE__);
    $account_id = $res->fields['account_id'];

    $res = $db->Execute("SELECT score FROM {$db->prefix}players WHERE account_id=?", array($account_id));
    db_op_result($db,$res,__LINE__,__FILE__);

    // Dynamic functions
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'global_includes');

    $player_score = $res->fields;

    // Temporary fix, still needed until the final solution for loans is in place!
    if ($player_score['score'] < 0)
    {
        $player_score['score'] = 0;
    }

    // Level 59 across the board is 16, with no planets/credits, etc. 
    // We need to redo this now that top level is 100.
    $i = round( (log($player_score['score'] / 2000)) / log(3.5) + 2);
    if ($i > 13)
    {
        $i = 13;
    }

    if ($i < 0)
    {
        $i = 0;
    }

    $player_insignia['rank_icon'] = $i;

    $insigvar = "l_insignia_" . $i;
    $player_insignia['rank_name'] = $$insigvar;

    return $player_insignia;
}
?>
