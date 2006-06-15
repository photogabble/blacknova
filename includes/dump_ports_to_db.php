<?php
function dump_ports_to_db($insertquery,$s,$flush)
{
    global $s, $db, $port_table, $cumulative, $ADODB_SESSION_DRIVER;
    $s++;
    $query = '';
    if ($flush)
    {
        $s--;
    }

    if ($ADODB_SESSION_DRIVER == 'postgres7')
    {
        // Postgres doesn't support bulk inserts (multiple inserts in a single call), so we just dump one at a time.
        $query = "INSERT into $port_table (sector_id, port_type, port_organics, port_ore, port_goods, port_energy, port_rating) VALUES " . $insertquery[$s-1];
    }
    else
    {
        if  ($s >= 1000 || $flush) // Just picked a number that seemed to work good
        {
            $query = "INSERT into $port_table (sector_id, port_type, port_organics, port_ore, port_goods, port_energy, port_rating) VALUES ";

            for (; $s>0; $s--)
            {
                $query = $query . $insertquery[$s-1]; // minus one, because s was incremented at the beginning of the function
                if ($s != 1 && $s !=1001) // The first and last iteration cannot have commas between the parenthesis
                {
                    $query = $query . ",";
                }
            }
        }
    }

    if ($query != '')
    {
        $debug_query = $db->Execute($query);
        $current_status = db_op_result($db,$debug_query,__LINE__,__FILE__);
        cumulative_error($cumulative, $current_status);
        unset($GLOBALS['insertquery']);
        unset($insertquery);
        unset($query);
    }
}
?>
