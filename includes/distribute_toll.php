<?php
function distribute_toll($db, $destination, $toll, $total_fighters)
{
    dynamic_loader ($db, "playerlog.php");

    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id='$destination' AND defense_type ='F' ");
    db_op_result($db,$result3,__LINE__,__FILE__);

    // Put the defense information into the array "defenseinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF)
        {
            $row = $result3->fields;
            $toll_amount = ROUND(($row['quantity'] / $total_fighters) * $toll);
            $debug_query = $db->Execute("UPDATE {$db->prefix}players set credits=credits + $toll_amount WHERE player_id = $row[player_id]");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            playerlog($db,$row[player_id], "LOG_TOLL_RECV", "$toll_amount|$destination");
            $result3->MoveNext();
        }
    }
}
?>
