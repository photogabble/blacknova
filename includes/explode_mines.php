<?php
function explode_mines($db,$sector, $num_mines)
{
    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id='$sector' and defense_type ='M' order by quantity ASC");
    db_op_result($db,$result3,__LINE__,__FILE__);

    // Put the defense information into the array "defenseinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF && $num_mines > 0)
        {
            $row = $result3->fields;
            if ($row['quantity'] > $num_mines)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense set quantity=quantity - $num_mines WHERE defense_id = $row[defense_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $num_mines = 0;
            }
            else
            {
                $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE defense_id = $row[defense_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $num_mines -= $row['quantity'];
            }

            $result3->MoveNext();
        }
    }
}
?>
