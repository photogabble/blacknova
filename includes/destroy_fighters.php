<?php
function destroy_fighters($db,$sector, $num_fighters) // Might not belong in g_functions
{
    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? and defense_type ='F' order by quantity ASC", array($sector));
    db_op_result($db,$result3,__LINE__,__FILE__);

    // Put the defense information into the array "defenseinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF && $num_fighters > 0)
        {
            $row=$result3->fields;
            if ($row['quantity'] > $num_fighters)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense set quantity=quantity-? WHERE defense_id=?", array($$num_fighters, row['defense_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $num_fighters = 0;
            }
            else
            {
                $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE defense_id=?", array($row['defense_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $num_fighters -= $row['quantity'];
            }

            $result3->MoveNext();
        }
    }
}
?>
