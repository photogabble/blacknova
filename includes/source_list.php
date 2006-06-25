<?php
function source_list($destination, $debug_query, $db, $db->prefix)
{
    $temp = array();
    $i = 0;
    $debug_query = $db->Execute("SELECT link_start FROM {$db->prefix}links WHERE link_dest=? order by link_start", array($destination));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    while (!$debug_query->EOF)
    {
        $res = $debug_query->fields;
        $temp[$i] = $res['link_start'];
        $debug_query->MoveNext();
        $i++;
    }
    return $temp;
}
?>
