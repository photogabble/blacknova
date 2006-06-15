<?php
function message_defense_owner($db, $sector, $message)
{
    dynamic_loader ($db, "playerlog.php");

    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id='$sector' ");
    db_op_result($db,$result3,__LINE__,__FILE__);

    // Put the defense information into the array "defenseinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF)
        {
            playerlog($db,$result3->fields['player_id'], "LOG_RAW", $message);
            $result3->MoveNext();
        }
    }
}
?>
