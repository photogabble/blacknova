<?php
function planetcount_news($db, $player_id)
{
    // Dynamic functions
    dynamic_loader ($db, "get_player.php");

    $debug_query = $db->Execute("SELECT count(owner) as amount FROM {$db->prefix}planets WHERE owner=? order by amount ASC", array($player_id));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $row = $debug_query->fields;
    $planet_count = $row['amount'];

    if ($planet_count < 50)
    {
        $rounded = intval(substr($planet_count, 0, 1)) * pow(10, strlen($planet_count)-1);
        if ($planet_count >= $rounded)
        {
            $news_type = 'planet' . $rounded;
            $debug_query = $db->Execute("SELECT * FROM {$db->prefix}news WHERE user_id=? and news_type=?", array($player_id, $news_type));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            if ($debug_query->EOF)
            {
                $name = get_player($db, $player_id);
                $stamp = date("Y-m-d H:i:s");
                $debug_query = $db->Execute("INSERT INTO {$db->prefix}news (news_data, user_id, date, news_type) VALUES " .
                                            "(?,?,?,?)", array($name, $player_id, $stamp, $news_type));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }
    }
    elseif ($planet_count < 500)
    {
        $rounded = intval(substr($planet_count, 0, 1)) * pow(100, strlen($planet_count)-2);
        if ($planet_count >= $rounded)
        {
            $news_type = 'planet' . $rounded;
            $debug_query = $db->Execute("SELECT * FROM {$db->prefix}news WHERE user_id=? and news_type=?", array($player_id, $news_type));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            if ($debug_query->EOF)
            {
                $name = get_player($db, $player_id);
                $stamp = date("Y-m-d H:i:s");
                $debug_query = $db->Execute("INSERT INTO {$db->prefix}news (news_data, user_id, date, news_type) VALUES " .
                                            "(?,?,?,?)", array($name, $player_id, $stamp, $news_type));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }
    }
    else
    {
        $rounded = intval(substr($planet_count, 0, 1)) * pow(1000, strlen($planet_count)-3);
        if ($planet_count >= $rounded)
        {
            $news_type = 'planet' . $rounded;
            $debug_query = $db->Execute("SELECT * FROM {$db->prefix}news WHERE user_id=? and news_type=?", array($player_id, $news_type));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            if ($debug_query->EOF)
            {
                $name = get_player($db, $player_id);
                $stamp = date("Y-m-d H:i:s");
                $debug_query = $db->Execute("INSERT INTO {$db->prefix}news (news_data, user_id, date, news_type) VALUES " .
                                            "(?,?,?,?)", array($name, $player_id, $stamp, $news_type));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }
    }
}
?>
