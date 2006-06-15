<?php
function time_since_reset($db)
{
    $debug_query = $db->SelectLimit("SELECT date FROM {$db->prefix}news",1);
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $throwaway = $debug_query->fields['date'];
    $creation_date = $db->UnixTimeStamp($throwaway);

    // Select creation news (should always be the first event)
    $time_since = time() - $creation_date;
    $timestring = '';

    $weeks = floor($time_since/604800);
    $days = floor(($time_since%604800)/86400);
    $hours = floor((($time_since%604800)%86400)/3600);
    $minutes = floor(((($time_since%604800)%86400)%3600)/60);

    if ($weeks)
    {
        $timestring=$weeks." weeks ";
    }

    if ($days)
    {
        $timestring.=$days." days ";
    }

    if ($hours)
    {
        $timestring.=$hours." hours ";
    }

    if ($minutes)
    {
        $timestring.=$minutes." minutes";
    }

    return $timestring;
}
?>
