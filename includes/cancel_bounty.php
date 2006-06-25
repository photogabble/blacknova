<?php
function cancel_bounty($db, $bounty_on)
{
    dynamic_loader ($db, "playerlog.php");

    $res = $db->Execute("SELECT * FROM {$db->prefix}bounty, {$db->prefix}players WHERE bounty_on=? AND bounty_on = player_id", array($bounty_on));
    db_op_result($db,$res,__LINE__,__FILE__);
    if ($res)
    {
        while (!$res->EOF)
        {
            $bountydetails = $res->fields;
            if ($bountydetails['placed_by'] != 0)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits = credits + ? WHERE player_id=?", array($bountydetails['character_name'], $bountydetails['placed_by']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                playerlog($db,$bountydetails['placed_by'], "LOG_BOUNTY_CANCELLED","$bountydetails[amount]|$bountydetails[character_name]");
            }

            $debug_query = $db->Execute("DELETE FROM {$db->prefix}bounty WHERE bounty_id=?", array($bountydetails['bounty_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $res->MoveNext();
        }
    }
}
?>
